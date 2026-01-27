<?php

namespace Drupal\pdf_embed_seo\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\TimeInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service for tracking PDF view analytics.
 */
class PdfAnalyticsTracker {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The time service.
   *
   * @var \Drupal\Core\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The analytics table name.
   *
   * @var string
   */
  protected $tableName = 'pdf_embed_seo_analytics';

  /**
   * Constructs a PdfAnalyticsTracker.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(
    Connection $database,
    AccountProxyInterface $current_user,
    RequestStack $request_stack,
    TimeInterface $time
  ) {
    $this->database = $database;
    $this->currentUser = $current_user;
    $this->requestStack = $request_stack;
    $this->time = $time;
  }

  /**
   * Track a PDF view.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   */
  public function trackView(PdfDocumentInterface $pdf_document) {
    $request = $this->requestStack->getCurrentRequest();

    try {
      $this->database->insert($this->tableName)
        ->fields([
          'pdf_document_id' => $pdf_document->id(),
          'user_id' => $this->currentUser->id(),
          'ip_address' => $request ? $request->getClientIp() : '',
          'user_agent' => $request ? substr($request->headers->get('User-Agent', ''), 0, 255) : '',
          'referer' => $request ? substr($request->headers->get('Referer', ''), 0, 255) : '',
          'timestamp' => $this->time->getRequestTime(),
        ])
        ->execute();
    }
    catch (\Exception $e) {
      // Silently fail - analytics should not break page viewing.
    }
  }

  /**
   * Get view statistics for a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array
   *   Array of statistics.
   */
  public function getDocumentStats(PdfDocumentInterface $pdf_document) {
    $total = $this->database->select($this->tableName, 'a')
      ->condition('pdf_document_id', $pdf_document->id())
      ->countQuery()
      ->execute()
      ->fetchField();

    $unique = $this->database->select($this->tableName, 'a')
      ->condition('pdf_document_id', $pdf_document->id())
      ->fields('a', ['ip_address'])
      ->distinct()
      ->countQuery()
      ->execute()
      ->fetchField();

    $today = $this->database->select($this->tableName, 'a')
      ->condition('pdf_document_id', $pdf_document->id())
      ->condition('timestamp', strtotime('today'), '>=')
      ->countQuery()
      ->execute()
      ->fetchField();

    return [
      'total_views' => (int) $total,
      'unique_visitors' => (int) $unique,
      'views_today' => (int) $today,
    ];
  }

  /**
   * Get popular documents.
   *
   * @param int $limit
   *   Number of documents to return.
   * @param int $days
   *   Number of days to consider.
   *
   * @return array
   *   Array of document IDs with view counts.
   */
  public function getPopularDocuments($limit = 10, $days = 30) {
    $since = $this->time->getRequestTime() - ($days * 86400);

    $results = $this->database->select($this->tableName, 'a')
      ->fields('a', ['pdf_document_id'])
      ->condition('timestamp', $since, '>=')
      ->groupBy('pdf_document_id')
      ->orderBy('count', 'DESC')
      ->range(0, $limit);

    $results->addExpression('COUNT(*)', 'count');

    return $results->execute()->fetchAllKeyed();
  }

  /**
   * Get recent views.
   *
   * @param int $limit
   *   Number of views to return.
   *
   * @return array
   *   Array of recent view records.
   */
  public function getRecentViews($limit = 50) {
    return $this->database->select($this->tableName, 'a')
      ->fields('a')
      ->orderBy('timestamp', 'DESC')
      ->range(0, $limit)
      ->execute()
      ->fetchAll();
  }

  /**
   * Get total view count across all documents.
   *
   * @return int
   *   Total view count.
   */
  public function getTotalViews() {
    return (int) $this->database->select($this->tableName, 'a')
      ->countQuery()
      ->execute()
      ->fetchField();
  }

  /**
   * Clear analytics data older than a certain number of days.
   *
   * @param int $days
   *   Number of days to keep.
   *
   * @return int
   *   Number of rows deleted.
   */
  public function clearOldData($days = 365) {
    $cutoff = $this->time->getRequestTime() - ($days * 86400);

    return $this->database->delete($this->tableName)
      ->condition('timestamp', $cutoff, '<')
      ->execute();
  }

}

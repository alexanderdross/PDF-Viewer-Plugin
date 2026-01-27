<?php

namespace Drupal\pdf_embed_seo_premium\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\TimeInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service for tracking PDF reading progress (Premium).
 */
class PdfProgressTracker {

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
   * The progress table name.
   *
   * @var string
   */
  protected $tableName = 'pdf_embed_seo_progress';

  /**
   * Constructs a PdfProgressTracker.
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
   * Get reading progress for a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array
   *   The progress data.
   */
  public function getProgress(PdfDocumentInterface $pdf_document) {
    $user_id = $this->currentUser->id();
    $session_id = $this->getSessionId();

    $query = $this->database->select($this->tableName, 'p')
      ->fields('p')
      ->condition('pdf_document_id', $pdf_document->id());

    if ($user_id > 0) {
      $query->condition('user_id', $user_id);
    }
    else {
      $query->condition('session_id', $session_id);
    }

    $record = $query->execute()->fetchObject();

    if ($record) {
      return [
        'page' => (int) $record->current_page,
        'scroll' => (float) $record->scroll_position,
        'zoom' => (float) $record->zoom_level,
        'last_read' => date('c', $record->updated),
      ];
    }

    // Default progress.
    return [
      'page' => 1,
      'scroll' => 0,
      'zoom' => 1,
      'last_read' => NULL,
    ];
  }

  /**
   * Save reading progress for a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   * @param array $data
   *   The progress data (page, scroll, zoom).
   *
   * @return array
   *   The saved progress data.
   */
  public function saveProgress(PdfDocumentInterface $pdf_document, array $data) {
    $user_id = $this->currentUser->id();
    $session_id = $this->getSessionId();
    $timestamp = $this->time->getRequestTime();

    $progress = [
      'page' => (int) ($data['page'] ?? 1),
      'scroll' => (float) ($data['scroll'] ?? 0),
      'zoom' => (float) ($data['zoom'] ?? 1),
    ];

    try {
      // Check if record exists.
      $query = $this->database->select($this->tableName, 'p')
        ->fields('p', ['id'])
        ->condition('pdf_document_id', $pdf_document->id());

      if ($user_id > 0) {
        $query->condition('user_id', $user_id);
      }
      else {
        $query->condition('session_id', $session_id);
      }

      $existing = $query->execute()->fetchField();

      if ($existing) {
        // Update existing record.
        $this->database->update($this->tableName)
          ->fields([
            'current_page' => $progress['page'],
            'scroll_position' => $progress['scroll'],
            'zoom_level' => $progress['zoom'],
            'updated' => $timestamp,
          ])
          ->condition('id', $existing)
          ->execute();
      }
      else {
        // Insert new record.
        $this->database->insert($this->tableName)
          ->fields([
            'pdf_document_id' => $pdf_document->id(),
            'user_id' => $user_id,
            'session_id' => $user_id > 0 ? '' : $session_id,
            'current_page' => $progress['page'],
            'scroll_position' => $progress['scroll'],
            'zoom_level' => $progress['zoom'],
            'updated' => $timestamp,
          ])
          ->execute();
      }

      $progress['last_read'] = date('c', $timestamp);
    }
    catch (\Exception $e) {
      \Drupal::logger('pdf_embed_seo_premium')->warning('Failed to save progress: @message', [
        '@message' => $e->getMessage(),
      ]);
    }

    return $progress;
  }

  /**
   * Delete progress for a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return bool
   *   TRUE if deleted.
   */
  public function deleteProgress(PdfDocumentInterface $pdf_document) {
    $user_id = $this->currentUser->id();
    $session_id = $this->getSessionId();

    $query = $this->database->delete($this->tableName)
      ->condition('pdf_document_id', $pdf_document->id());

    if ($user_id > 0) {
      $query->condition('user_id', $user_id);
    }
    else {
      $query->condition('session_id', $session_id);
    }

    return (bool) $query->execute();
  }

  /**
   * Get the current session ID.
   *
   * @return string
   *   The session ID.
   */
  protected function getSessionId() {
    $request = $this->requestStack->getCurrentRequest();
    if ($request && $request->hasSession()) {
      return $request->getSession()->getId();
    }
    return '';
  }

}

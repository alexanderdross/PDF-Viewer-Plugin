<?php

namespace Drupal\pdf_embed_seo_premium\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller for Premium REST API endpoints.
 */
class PdfPremiumApiController extends ControllerBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a PdfPremiumApiController object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Get analytics overview.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with analytics.
   */
  public function getAnalytics(Request $request) {
    $analytics = \Drupal::service('pdf_embed_seo.analytics_tracker');
    $storage = $this->entityTypeManager->getStorage('pdf_document');

    $period = $request->query->get('period', '30days');
    $days = $this->getPeriodDays($period);

    // Get statistics.
    $total_views = $analytics->getTotalViews();
    $total_documents = $storage->getQuery()
      ->accessCheck(TRUE)
      ->count()
      ->execute();

    // Get popular documents.
    $popular_ids = $analytics->getPopularDocuments(10, $days);
    $top_documents = [];
    if (!empty($popular_ids)) {
      $documents = $storage->loadMultiple(array_keys($popular_ids));
      foreach ($documents as $id => $document) {
        $top_documents[] = [
          'id' => (int) $id,
          'title' => $document->label(),
          'url' => $document->toUrl('canonical', ['absolute' => TRUE])->toString(),
          'views' => $popular_ids[$id],
        ];
      }
    }

    return new JsonResponse([
      'period' => $period,
      'total_views' => $total_views,
      'total_documents' => (int) $total_documents,
      'total_downloads' => $analytics->getTotalDownloads(),
      'top_documents' => $top_documents,
    ]);
  }

  /**
   * Get per-document analytics.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with per-document analytics.
   */
  public function getDocumentAnalytics(Request $request) {
    $analytics = \Drupal::service('pdf_embed_seo.analytics_tracker');
    $storage = $this->entityTypeManager->getStorage('pdf_document');

    $period = $request->query->get('period', '30days');
    $days = $this->getPeriodDays($period);
    $page = (int) $request->query->get('page', 1);
    $per_page = min((int) $request->query->get('per_page', 20), 100);
    $orderby = $request->query->get('orderby', 'views');
    $order = $request->query->get('order', 'desc');

    // Get all documents with analytics.
    $query = $storage->getQuery()
      ->accessCheck(TRUE);

    // Count total.
    $total = (clone $query)->count()->execute();

    // Apply pagination.
    $offset = ($page - 1) * $per_page;
    $query->range($offset, $per_page);

    $ids = $query->execute();
    $documents = $storage->loadMultiple($ids);

    $data = [];
    foreach ($documents as $document) {
      $doc_analytics = $analytics->getDocumentAnalytics($document->id(), $days);
      $data[] = [
        'id' => (int) $document->id(),
        'title' => $document->label(),
        'url' => $document->toUrl('canonical', ['absolute' => TRUE])->toString(),
        'views' => $doc_analytics['views'] ?? 0,
        'downloads' => $doc_analytics['downloads'] ?? 0,
        'unique_visitors' => $doc_analytics['unique_visitors'] ?? 0,
        'avg_time_spent' => $doc_analytics['avg_time_spent'] ?? 0,
        'last_viewed' => $doc_analytics['last_viewed'] ?? NULL,
      ];
    }

    // Sort results.
    usort($data, function ($a, $b) use ($orderby, $order) {
      $val_a = $a[$orderby] ?? 0;
      $val_b = $b[$orderby] ?? 0;
      return $order === 'desc' ? $val_b - $val_a : $val_a - $val_b;
    });

    return new JsonResponse([
      'period' => $period,
      'page' => $page,
      'per_page' => $per_page,
      'total' => (int) $total,
      'total_pages' => (int) ceil($total / $per_page),
      'documents' => $data,
    ]);
  }

  /**
   * Export analytics data.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Symfony\Component\HttpFoundation\JsonResponse
   *   Streamed CSV or JSON response.
   */
  public function exportAnalytics(Request $request) {
    $analytics = \Drupal::service('pdf_embed_seo.analytics_tracker');
    $storage = $this->entityTypeManager->getStorage('pdf_document');

    $format = $request->query->get('format', 'csv');
    $period = $request->query->get('period', '30days');
    $days = $this->getPeriodDays($period);

    // Get all documents with analytics.
    $ids = $storage->getQuery()
      ->accessCheck(TRUE)
      ->execute();
    $documents = $storage->loadMultiple($ids);

    $data = [];
    foreach ($documents as $document) {
      $doc_analytics = $analytics->getDocumentAnalytics($document->id(), $days);
      $data[] = [
        'id' => (int) $document->id(),
        'title' => $document->label(),
        'url' => $document->toUrl('canonical', ['absolute' => TRUE])->toString(),
        'views' => $doc_analytics['views'] ?? 0,
        'downloads' => $doc_analytics['downloads'] ?? 0,
        'unique_visitors' => $doc_analytics['unique_visitors'] ?? 0,
        'avg_time_spent' => $doc_analytics['avg_time_spent'] ?? 0,
      ];
    }

    if ($format === 'json') {
      return new JsonResponse([
        'period' => $period,
        'exported_at' => date('c'),
        'data' => $data,
      ]);
    }

    // CSV export.
    $response = new StreamedResponse(function () use ($data) {
      $handle = fopen('php://output', 'w');

      // Header row.
      fputcsv($handle, ['ID', 'Title', 'URL', 'Views', 'Downloads', 'Unique Visitors', 'Avg Time Spent']);

      // Data rows.
      foreach ($data as $row) {
        fputcsv($handle, [
          $row['id'],
          $row['title'],
          $row['url'],
          $row['views'],
          $row['downloads'],
          $row['unique_visitors'],
          $row['avg_time_spent'],
        ]);
      }

      fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="pdf-analytics-' . date('Y-m-d') . '.csv"');

    return $response;
  }

  /**
   * Get PDF categories.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with categories.
   */
  public function getCategories(Request $request) {
    $storage = $this->entityTypeManager->getStorage('taxonomy_term');

    // Check if pdf_category vocabulary exists.
    try {
      $terms = $storage->loadByProperties(['vid' => 'pdf_category']);
    }
    catch (\Exception $e) {
      return new JsonResponse([
        'categories' => [],
        'message' => 'PDF categories taxonomy not configured.',
      ]);
    }

    $categories = [];
    foreach ($terms as $term) {
      $categories[] = [
        'id' => (int) $term->id(),
        'name' => $term->label(),
        'slug' => $term->get('path')->alias ?? '/taxonomy/term/' . $term->id(),
        'description' => $term->getDescription(),
        'count' => $this->getTermDocumentCount($term->id()),
      ];
    }

    return new JsonResponse([
      'categories' => $categories,
    ]);
  }

  /**
   * Get PDF tags.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with tags.
   */
  public function getTags(Request $request) {
    $storage = $this->entityTypeManager->getStorage('taxonomy_term');

    // Check if pdf_tag vocabulary exists.
    try {
      $terms = $storage->loadByProperties(['vid' => 'pdf_tag']);
    }
    catch (\Exception $e) {
      return new JsonResponse([
        'tags' => [],
        'message' => 'PDF tags taxonomy not configured.',
      ]);
    }

    $tags = [];
    foreach ($terms as $term) {
      $tags[] = [
        'id' => (int) $term->id(),
        'name' => $term->label(),
        'slug' => $term->get('path')->alias ?? '/taxonomy/term/' . $term->id(),
        'count' => $this->getTermDocumentCount($term->id()),
      ];
    }

    return new JsonResponse([
      'tags' => $tags,
    ]);
  }

  /**
   * Start bulk import.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with import status.
   */
  public function startBulkImport(Request $request) {
    // Check permissions.
    if (!$this->currentUser()->hasPermission('administer pdf embed seo')) {
      return new JsonResponse([
        'success' => FALSE,
        'message' => 'Permission denied.',
      ], 403);
    }

    $content = json_decode($request->getContent(), TRUE);

    // Validate input.
    if (empty($content['source'])) {
      return new JsonResponse([
        'success' => FALSE,
        'message' => 'Import source is required.',
      ], 400);
    }

    // Generate batch ID.
    $batch_id = uniqid('pdf_import_', TRUE);

    // Store import job (would use queue/batch API in production).
    \Drupal::state()->set('pdf_import_' . $batch_id, [
      'status' => 'pending',
      'source' => $content['source'],
      'options' => $content['options'] ?? [],
      'created' => time(),
      'processed' => 0,
      'total' => 0,
      'success' => 0,
      'failed' => 0,
    ]);

    return new JsonResponse([
      'success' => TRUE,
      'batch_id' => $batch_id,
      'message' => 'Import job queued.',
    ]);
  }

  /**
   * Get bulk import status.
   *
   * @param string $batch_id
   *   The batch ID.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with import status.
   */
  public function getBulkImportStatus($batch_id, Request $request) {
    $job = \Drupal::state()->get('pdf_import_' . $batch_id);

    if (!$job) {
      return new JsonResponse([
        'success' => FALSE,
        'message' => 'Import job not found.',
      ], 404);
    }

    return new JsonResponse([
      'batch_id' => $batch_id,
      'status' => $job['status'],
      'processed' => $job['processed'],
      'total' => $job['total'],
      'success' => $job['success'],
      'failed' => $job['failed'],
      'created' => date('c', $job['created']),
    ]);
  }

  /**
   * Track PDF download.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response.
   */
  public function trackDownload(PdfDocumentInterface $pdf_document, Request $request) {
    $analytics = \Drupal::service('pdf_embed_seo.analytics_tracker');

    $analytics->trackDownload($pdf_document->id(), [
      'ip' => $request->getClientIp(),
      'user_agent' => $request->headers->get('User-Agent'),
      'referrer' => $request->headers->get('Referer'),
    ]);

    return new JsonResponse([
      'success' => TRUE,
      'document_id' => (int) $pdf_document->id(),
    ]);
  }

  /**
   * Generate expiring access link.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with access link.
   */
  public function generateExpiringLink(PdfDocumentInterface $pdf_document, Request $request) {
    // Check permissions.
    if (!$this->currentUser()->hasPermission('administer pdf embed seo')) {
      return new JsonResponse([
        'success' => FALSE,
        'message' => 'Permission denied.',
      ], 403);
    }

    $content = json_decode($request->getContent(), TRUE);
    $expires_in = (int) ($content['expires_in'] ?? 3600); // Default 1 hour.
    $max_uses = (int) ($content['max_uses'] ?? 0); // 0 = unlimited.

    // Generate token.
    $token = bin2hex(random_bytes(32));
    $expires_at = time() + $expires_in;

    // Store token.
    $token_data = [
      'document_id' => $pdf_document->id(),
      'expires_at' => $expires_at,
      'max_uses' => $max_uses,
      'uses' => 0,
      'created_by' => $this->currentUser()->id(),
      'created_at' => time(),
    ];
    \Drupal::state()->set('pdf_access_token_' . $token, $token_data);

    // Generate URL.
    $access_url = Url::fromRoute('pdf_embed_seo_premium.expiring_access', [
      'pdf_document' => $pdf_document->id(),
      'token' => $token,
    ], ['absolute' => TRUE])->toString();

    return new JsonResponse([
      'success' => TRUE,
      'document_id' => (int) $pdf_document->id(),
      'access_url' => $access_url,
      'token' => $token,
      'expires_at' => date('c', $expires_at),
      'expires_in' => $expires_in,
      'max_uses' => $max_uses,
    ]);
  }

  /**
   * Validate expiring access link.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document.
   * @param string $token
   *   The access token.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with validation result.
   */
  public function validateExpiringLink(PdfDocumentInterface $pdf_document, $token, Request $request) {
    $token_data = \Drupal::state()->get('pdf_access_token_' . $token);

    if (!$token_data) {
      return new JsonResponse([
        'valid' => FALSE,
        'message' => 'Invalid or expired access link.',
      ], 403);
    }

    // Check document ID.
    if ($token_data['document_id'] != $pdf_document->id()) {
      return new JsonResponse([
        'valid' => FALSE,
        'message' => 'Invalid access link for this document.',
      ], 403);
    }

    // Check expiration.
    if (time() > $token_data['expires_at']) {
      \Drupal::state()->delete('pdf_access_token_' . $token);
      return new JsonResponse([
        'valid' => FALSE,
        'message' => 'Access link has expired.',
      ], 403);
    }

    // Check max uses.
    if ($token_data['max_uses'] > 0 && $token_data['uses'] >= $token_data['max_uses']) {
      \Drupal::state()->delete('pdf_access_token_' . $token);
      return new JsonResponse([
        'valid' => FALSE,
        'message' => 'Access link has reached maximum uses.',
      ], 403);
    }

    // Increment uses.
    $token_data['uses']++;
    \Drupal::state()->set('pdf_access_token_' . $token, $token_data);

    return new JsonResponse([
      'valid' => TRUE,
      'document_id' => (int) $pdf_document->id(),
      'uses_remaining' => $token_data['max_uses'] > 0 ? $token_data['max_uses'] - $token_data['uses'] : NULL,
      'expires_at' => date('c', $token_data['expires_at']),
    ]);
  }

  /**
   * Get reading progress.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with progress.
   */
  public function getProgress(PdfDocumentInterface $pdf_document, Request $request) {
    $progress_tracker = \Drupal::service('pdf_embed_seo.progress_tracker');
    $progress = $progress_tracker->getProgress($pdf_document);

    return new JsonResponse([
      'document_id' => (int) $pdf_document->id(),
      'progress' => $progress,
    ]);
  }

  /**
   * Save reading progress.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response.
   */
  public function saveProgress(PdfDocumentInterface $pdf_document, Request $request) {
    $content = json_decode($request->getContent(), TRUE);

    $progress_tracker = \Drupal::service('pdf_embed_seo.progress_tracker');
    $progress = $progress_tracker->saveProgress($pdf_document, [
      'page' => (int) ($content['page'] ?? 1),
      'scroll' => (float) ($content['scroll'] ?? 0),
      'zoom' => (float) ($content['zoom'] ?? 1),
    ]);

    return new JsonResponse([
      'success' => TRUE,
      'document_id' => (int) $pdf_document->id(),
      'progress' => $progress,
    ]);
  }

  /**
   * Verify PDF password.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response.
   */
  public function verifyPassword(PdfDocumentInterface $pdf_document, Request $request) {
    $content = json_decode($request->getContent(), TRUE);
    $password = $content['password'] ?? '';

    // Check if document has password field and is protected.
    if (!$pdf_document->hasField('password') || $pdf_document->get('password')->isEmpty()) {
      return new JsonResponse([
        'success' => TRUE,
        'protected' => FALSE,
        'message' => $this->t('This document is not password protected.'),
      ]);
    }

    $stored_password = $pdf_document->get('password')->value;

    // Verify password.
    $password_service = \Drupal::service('password');
    $is_valid = $password_service->check($password, $stored_password);

    // Allow other modules to alter verification.
    \Drupal::moduleHandler()->alter('pdf_embed_seo_verify_password', $is_valid, $pdf_document, $password);

    if ($is_valid) {
      // Generate access token.
      $token = \Drupal::csrfToken()->get('pdf_access_' . $pdf_document->id());

      // Store in session.
      $session = $request->getSession();
      $session->set('pdf_access_' . $pdf_document->id(), TRUE);

      return new JsonResponse([
        'success' => TRUE,
        'access_token' => $token,
        'expires_in' => 3600,
      ]);
    }

    return new JsonResponse([
      'success' => FALSE,
      'message' => $this->t('Incorrect password.'),
    ], 403);
  }

  /**
   * Get number of days from period string.
   *
   * @param string $period
   *   The period string.
   *
   * @return int
   *   Number of days.
   */
  protected function getPeriodDays($period) {
    switch ($period) {
      case '7days':
        return 7;

      case '30days':
        return 30;

      case '90days':
        return 90;

      case '12months':
        return 365;

      case 'all':
        return 9999;

      default:
        return 30;
    }
  }

  /**
   * Get document count for a taxonomy term.
   *
   * @param int $term_id
   *   The term ID.
   *
   * @return int
   *   Number of documents with this term.
   */
  protected function getTermDocumentCount($term_id) {
    // This would query for documents with this term.
    // Implementation depends on field configuration.
    return 0;
  }

}

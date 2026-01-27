<?php

namespace Drupal\pdf_embed_seo_premium\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
      'top_documents' => $top_documents,
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

    // Check if document is password protected.
    $is_protected = (bool) $pdf_document->get('password_protected')->value;
    $stored_password = $pdf_document->get('password')->value;

    if (!$is_protected) {
      return new JsonResponse([
        'success' => TRUE,
        'protected' => FALSE,
        'message' => $this->t('This document is not password protected.'),
      ]);
    }

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

}

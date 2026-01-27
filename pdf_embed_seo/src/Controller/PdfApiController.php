<?php

namespace Drupal\pdf_embed_seo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for REST API endpoints.
 */
class PdfApiController extends ControllerBase {

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
   * Constructs a PdfApiController object.
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
   * Get public plugin settings.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with settings.
   */
  public function getSettings(Request $request) {
    $config = $this->configFactory->get('pdf_embed_seo.settings');
    $module_info = \Drupal::service('extension.list.module')->getExtensionInfo('pdf_embed_seo');

    $settings = [
      'viewer_theme' => $config->get('viewer_theme') ?? 'light',
      'archive_url' => Url::fromRoute('pdf_embed_seo.archive', [], ['absolute' => TRUE])->toString(),
      'is_premium' => (bool) $config->get('premium_enabled'),
      'version' => $module_info['version'] ?? '1.1.5',
      'api_version' => '1.0',
      'endpoints' => [
        'documents' => Url::fromUri('base:/api/pdf-embed-seo/v1/documents', ['absolute' => TRUE])->toString(),
        'settings' => Url::fromRoute('pdf_embed_seo.api.settings', [], ['absolute' => TRUE])->toString(),
      ],
    ];

    // Allow other modules to alter settings.
    \Drupal::moduleHandler()->alter('pdf_embed_seo_api_settings', $settings);

    return new JsonResponse($settings);
  }

  /**
   * Track a PDF view.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response.
   */
  public function trackView(PdfDocumentInterface $pdf_document, Request $request) {
    // Increment view count.
    $current_count = (int) $pdf_document->get('view_count')->value;
    $new_count = $current_count + 1;
    $pdf_document->set('view_count', $new_count);
    $pdf_document->save();

    // Track in analytics table if available.
    if (\Drupal::database()->schema()->tableExists('pdf_embed_seo_analytics')) {
      try {
        \Drupal::database()->insert('pdf_embed_seo_analytics')
          ->fields([
            'pdf_id' => $pdf_document->id(),
            'ip_address' => $request->getClientIp(),
            'user_agent' => $request->headers->get('User-Agent'),
            'referrer' => $request->headers->get('Referer'),
            'created' => \Drupal::time()->getRequestTime(),
          ])
          ->execute();
      }
      catch (\Exception $e) {
        // Log but don't fail.
        \Drupal::logger('pdf_embed_seo')->warning('Failed to track analytics: @message', [
          '@message' => $e->getMessage(),
        ]);
      }
    }

    // Invoke hook for other modules.
    \Drupal::moduleHandler()->invokeAll('pdf_embed_seo_view_tracked', [$pdf_document, $new_count]);

    return new JsonResponse([
      'success' => TRUE,
      'views' => $new_count,
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

}

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
    // Track view in analytics table without entity save (performance optimization).
    // Entity saves invalidate cache and cause performance issues under load.
    $view_count = 0;

    if (\Drupal::database()->schema()->tableExists('pdf_embed_seo_analytics')) {
      try {
        \Drupal::database()->insert('pdf_embed_seo_analytics')
          ->fields([
            'pdf_id' => $pdf_document->id(),
            'ip_address' => _pdf_embed_seo_anonymize_ip($request->getClientIp()),
            'user_agent' => substr($request->headers->get('User-Agent', ''), 0, 255),
            'referrer' => substr($request->headers->get('Referer', ''), 0, 255),
            'created' => \Drupal::time()->getRequestTime(),
          ])
          ->execute();

        // Get view count from analytics table.
        $view_count = (int) \Drupal::database()->select('pdf_embed_seo_analytics', 'a')
          ->condition('pdf_id', $pdf_document->id())
          ->countQuery()
          ->execute()
          ->fetchField();
      }
      catch (\Exception $e) {
        // Log but don't fail.
        \Drupal::logger('pdf_embed_seo')->warning('Failed to track analytics: @message', [
          '@message' => $e->getMessage(),
        ]);
      }
    }

    // Invoke hook for other modules.
    \Drupal::moduleHandler()->invokeAll('pdf_embed_seo_view_tracked', [$pdf_document, $view_count]);

    return new JsonResponse([
      'success' => TRUE,
      'views' => $view_count,
    ]);
  }

}

<?php

namespace Drupal\pdf_embed_seo\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a REST resource for PDF reading progress (Premium).
 *
 * @RestResource(
 *   id = "pdf_progress_resource",
 *   label = @Translation("PDF Progress Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/pdf-embed-seo/v1/documents/{id}/progress"
 *   }
 * )
 */
class PdfProgressResource extends ResourceBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a PdfProgressResource object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    EntityTypeManagerInterface $entity_type_manager,
    AccountProxyInterface $current_user
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('pdf_embed_seo'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to GET requests for reading progress.
   *
   * @param int $id
   *   The PDF document ID.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing reading progress.
   */
  public function get($id) {
    $document = $this->loadDocument($id);
    $user_id = $this->getUserIdentifier();
    $progress_key = 'pdf_progress_' . $id . '_' . $user_id;

    $progress = NULL;

    // Check user data for logged in users.
    if ($this->currentUser->isAuthenticated()) {
      $user_data = \Drupal::service('user.data');
      $progress = $user_data->get('pdf_embed_seo', $this->currentUser->id(), $progress_key);
    }
    else {
      // Use tempstore for anonymous users.
      $tempstore = \Drupal::service('tempstore.private')->get('pdf_embed_seo');
      $progress = $tempstore->get($progress_key);
    }

    if (!$progress) {
      $progress = [
        'page' => 1,
        'scroll' => 0,
        'zoom' => 1,
      ];
    }

    $response = new ResourceResponse([
      'document_id' => (int) $id,
      'progress' => $progress,
      'last_read' => $progress['timestamp'] ?? NULL,
    ]);

    // Don't cache progress data.
    $response->addCacheableDependency((new \Drupal\Core\Cache\CacheableMetadata())->setCacheMaxAge(0));
    return $response;
  }

  /**
   * Responds to POST requests to save reading progress.
   *
   * @param int $id
   *   The PDF document ID.
   * @param array $data
   *   The progress data.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The response.
   */
  public function post($id, array $data) {
    $document = $this->loadDocument($id);
    $user_id = $this->getUserIdentifier();
    $progress_key = 'pdf_progress_' . $id . '_' . $user_id;

    $progress = [
      'page' => (int) ($data['page'] ?? 1),
      'scroll' => (float) ($data['scroll'] ?? 0),
      'zoom' => (float) ($data['zoom'] ?? 1),
      'timestamp' => date('c'),
    ];

    // Save for logged in users.
    if ($this->currentUser->isAuthenticated()) {
      $user_data = \Drupal::service('user.data');
      $user_data->set('pdf_embed_seo', $this->currentUser->id(), $progress_key, $progress);
    }
    else {
      // Use tempstore for anonymous users.
      $tempstore = \Drupal::service('tempstore.private')->get('pdf_embed_seo');
      $tempstore->set($progress_key, $progress);
    }

    return new ModifiedResourceResponse([
      'success' => TRUE,
      'document_id' => (int) $id,
      'progress' => $progress,
    ], 200);
  }

  /**
   * Load a PDF document.
   *
   * @param int $id
   *   The document ID.
   *
   * @return \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface
   *   The PDF document.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  protected function loadDocument($id) {
    $storage = $this->entityTypeManager->getStorage('pdf_document');
    $document = $storage->load($id);

    if (!$document) {
      throw new NotFoundHttpException('PDF document not found.');
    }

    return $document;
  }

  /**
   * Get a unique user identifier.
   *
   * @return string
   *   The user identifier.
   */
  protected function getUserIdentifier() {
    if ($this->currentUser->isAuthenticated()) {
      return 'user_' . $this->currentUser->id();
    }

    // For anonymous users, use session ID.
    $session = \Drupal::request()->getSession();
    return 'anon_' . $session->getId();
  }

}

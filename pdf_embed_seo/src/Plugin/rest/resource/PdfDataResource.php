<?php

namespace Drupal\pdf_embed_seo\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a REST resource for PDF document data (secure file access).
 *
 * @RestResource(
 *   id = "pdf_data_resource",
 *   label = @Translation("PDF Data Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/pdf-embed-seo/v1/documents/{id}/data"
 *   }
 * )
 */
class PdfDataResource extends ResourceBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a PdfDataResource object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->entityTypeManager = $entity_type_manager;
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
      $container->get('entity_type.manager')
    );
  }

  /**
   * Responds to GET requests for PDF document data.
   *
   * @param int $id
   *   The PDF document ID.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the PDF file data.
   */
  public function get($id) {
    $storage = $this->entityTypeManager->getStorage('pdf_document');
    $document = $storage->load($id);

    if (!$document) {
      throw new NotFoundHttpException('PDF document not found.');
    }

    if (!$document->isPublished()) {
      throw new AccessDeniedHttpException('PDF document is not published.');
    }

    // Get the PDF file.
    $pdf_file = NULL;
    if ($document->hasField('pdf_file') && !$document->get('pdf_file')->isEmpty()) {
      $pdf_file = $document->get('pdf_file')->entity;
    }

    if (!$pdf_file) {
      throw new NotFoundHttpException('No PDF file attached to this document.');
    }

    $file_url_generator = \Drupal::service('file_url_generator');

    $data = [
      'id' => (int) $document->id(),
      'pdf_url' => $file_url_generator->generateAbsoluteString($pdf_file->getFileUri()),
      'allow_download' => (bool) $document->get('allow_download')->value,
      'allow_print' => (bool) $document->get('allow_print')->value,
      'filename' => $pdf_file->getFilename(),
      'filesize' => $pdf_file->getSize(),
      'mime_type' => $pdf_file->getMimeType(),
    ];

    // Allow other modules to alter the data.
    \Drupal::moduleHandler()->alter('pdf_embed_seo_document_data', $data, $document);

    $response = new ResourceResponse($data);
    $response->addCacheableDependency($document);
    return $response;
  }

}

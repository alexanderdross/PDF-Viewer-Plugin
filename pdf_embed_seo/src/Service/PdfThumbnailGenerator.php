<?php

namespace Drupal\pdf_embed_seo\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;

/**
 * Service for generating PDF thumbnails.
 */
class PdfThumbnailGenerator {

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a PdfThumbnailGenerator.
   *
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(
    FileSystemInterface $file_system,
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger_factory,
    ConfigFactoryInterface $config_factory
  ) {
    $this->fileSystem = $file_system;
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger_factory->get('pdf_embed_seo');
    $this->configFactory = $config_factory;
  }

  /**
   * Generate a thumbnail for a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return bool
   *   TRUE if thumbnail was generated successfully, FALSE otherwise.
   *
   * @throws \Exception
   *   If thumbnail generation fails.
   */
  public function generateThumbnail(PdfDocumentInterface $pdf_document) {
    $pdf_file = $pdf_document->getPdfFile();
    if (!$pdf_file) {
      throw new \Exception('No PDF file attached to document.');
    }

    $pdf_path = $this->fileSystem->realpath($pdf_file->getFileUri());
    if (!$pdf_path || !file_exists($pdf_path)) {
      throw new \Exception('PDF file not accessible.');
    }

    $config = $this->configFactory->get('pdf_embed_seo.settings');
    $width = $config->get('thumbnail_width') ?? 300;
    $height = $config->get('thumbnail_height') ?? 400;

    // Check for ImageMagick.
    if ($this->hasImageMagick()) {
      return $this->generateWithImageMagick($pdf_document, $pdf_path, $width, $height);
    }

    // Check for Ghostscript.
    if ($this->hasGhostscript()) {
      return $this->generateWithGhostscript($pdf_document, $pdf_path, $width, $height);
    }

    throw new \Exception('Neither ImageMagick nor Ghostscript is available.');
  }

  /**
   * Check if ImageMagick is available.
   *
   * @return bool
   *   TRUE if available, FALSE otherwise.
   */
  protected function hasImageMagick() {
    exec('which convert 2>/dev/null', $output, $return_var);
    return $return_var === 0;
  }

  /**
   * Check if Ghostscript is available.
   *
   * @return bool
   *   TRUE if available, FALSE otherwise.
   */
  protected function hasGhostscript() {
    exec('which gs 2>/dev/null', $output, $return_var);
    return $return_var === 0;
  }

  /**
   * Generate thumbnail using ImageMagick.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document.
   * @param string $pdf_path
   *   Path to the PDF file.
   * @param int $width
   *   Thumbnail width.
   * @param int $height
   *   Thumbnail height.
   *
   * @return bool
   *   TRUE if successful.
   */
  protected function generateWithImageMagick(PdfDocumentInterface $pdf_document, $pdf_path, $width, $height) {
    $temp_file = $this->fileSystem->tempnam('temporary://', 'pdf_thumb_');
    $output_path = $temp_file . '.png';

    // Convert first page of PDF to PNG.
    $command = sprintf(
      'convert -density 150 %s[0] -resize %dx%d -background white -flatten %s 2>&1',
      escapeshellarg($pdf_path),
      $width,
      $height,
      escapeshellarg($output_path)
    );

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
      $this->logger->error('ImageMagick conversion failed: @output', [
        '@output' => implode("\n", $output),
      ]);
      throw new \Exception('ImageMagick conversion failed.');
    }

    return $this->saveThumbnail($pdf_document, $output_path);
  }

  /**
   * Generate thumbnail using Ghostscript.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document.
   * @param string $pdf_path
   *   Path to the PDF file.
   * @param int $width
   *   Thumbnail width.
   * @param int $height
   *   Thumbnail height.
   *
   * @return bool
   *   TRUE if successful.
   */
  protected function generateWithGhostscript(PdfDocumentInterface $pdf_document, $pdf_path, $width, $height) {
    $temp_file = $this->fileSystem->tempnam('temporary://', 'pdf_thumb_');
    $output_path = $temp_file . '.png';

    // Convert first page of PDF to PNG using Ghostscript.
    $command = sprintf(
      'gs -dSAFER -dBATCH -dNOPAUSE -dFirstPage=1 -dLastPage=1 -sDEVICE=pngalpha -r150 -dPDFFitPage -g%dx%d -sOutputFile=%s %s 2>&1',
      $width * 2,
      $height * 2,
      escapeshellarg($output_path),
      escapeshellarg($pdf_path)
    );

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
      $this->logger->error('Ghostscript conversion failed: @output', [
        '@output' => implode("\n", $output),
      ]);
      throw new \Exception('Ghostscript conversion failed.');
    }

    return $this->saveThumbnail($pdf_document, $output_path);
  }

  /**
   * Save the generated thumbnail to the PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document.
   * @param string $temp_path
   *   Path to the temporary thumbnail file.
   *
   * @return bool
   *   TRUE if successful.
   */
  protected function saveThumbnail(PdfDocumentInterface $pdf_document, $temp_path) {
    if (!file_exists($temp_path)) {
      throw new \Exception('Thumbnail file not created.');
    }

    // Prepare destination.
    $destination = 'public://pdf_thumbnails/';
    $this->fileSystem->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY);

    $filename = 'pdf_thumb_' . $pdf_document->id() . '_' . time() . '.png';
    $destination_uri = $destination . $filename;

    // Copy file to permanent location.
    $uri = $this->fileSystem->copy($temp_path, $destination_uri, FileSystemInterface::EXISTS_REPLACE);

    if (!$uri) {
      throw new \Exception('Failed to save thumbnail file.');
    }

    // Create file entity.
    $file_storage = $this->entityTypeManager->getStorage('file');
    $file = $file_storage->create([
      'uri' => $uri,
      'status' => 1,
    ]);
    $file->save();

    // Attach to PDF document.
    $pdf_document->set('thumbnail', $file->id());
    $pdf_document->save();

    // Clean up temp file.
    @unlink($temp_path);

    return TRUE;
  }

}

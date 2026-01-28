<?php

namespace Drupal\pdf_embed_seo_premium\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\file\FileRepositoryInterface;
use Drupal\pdf_embed_seo\Entity\PdfDocument;

/**
 * Service for bulk PDF operations.
 */
class PdfBulkOperations {

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
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The file repository service.
   *
   * @var \Drupal\file\FileRepositoryInterface|null
   */
  protected $fileRepository;

  /**
   * Constructs a PdfBulkOperations object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Drupal\file\FileRepositoryInterface|null $file_repository
   *   The file repository service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    AccountProxyInterface $current_user,
    FileSystemInterface $file_system,
    ?FileRepositoryInterface $file_repository = NULL
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
    $this->fileSystem = $file_system;
    $this->fileRepository = $file_repository;
  }

  /**
   * Import PDFs from a CSV file.
   *
   * @param string $csv_path
   *   Path to the CSV file.
   * @param array $options
   *   Import options.
   *
   * @return array
   *   Import results with 'success', 'failed', and 'messages'.
   */
  public function importFromCsv(string $csv_path, array $options = []): array {
    $results = [
      'success' => 0,
      'failed' => 0,
      'messages' => [],
    ];

    if (!file_exists($csv_path)) {
      $results['messages'][] = t('CSV file not found.');
      return $results;
    }

    $handle = fopen($csv_path, 'r');
    if ($handle === FALSE) {
      $results['messages'][] = t('Could not open CSV file.');
      return $results;
    }

    // Read header row.
    $headers = fgetcsv($handle);
    if (!$headers) {
      $results['messages'][] = t('CSV file is empty or invalid.');
      fclose($handle);
      return $results;
    }

    // Normalize headers.
    $headers = array_map('strtolower', array_map('trim', $headers));

    // Process each row.
    while (($row = fgetcsv($handle)) !== FALSE) {
      $data = array_combine($headers, $row);
      if ($data === FALSE) {
        $results['failed']++;
        continue;
      }

      try {
        $this->createDocumentFromRow($data, $options);
        $results['success']++;
      }
      catch (\Exception $e) {
        $results['failed']++;
        $results['messages'][] = t('Error importing row: @error', ['@error' => $e->getMessage()]);
      }
    }

    fclose($handle);
    return $results;
  }

  /**
   * Create a PDF document from CSV row data.
   *
   * @param array $data
   *   Row data with column values.
   * @param array $options
   *   Import options.
   *
   * @return \Drupal\pdf_embed_seo\Entity\PdfDocument
   *   The created PDF document.
   *
   * @throws \Exception
   *   If document creation fails.
   */
  protected function createDocumentFromRow(array $data, array $options): PdfDocument {
    // Required: title.
    if (empty($data['title'])) {
      throw new \Exception('Title is required.');
    }

    $values = [
      'title' => $data['title'],
      'description' => $data['description'] ?? '',
      'status' => isset($data['status']) ? (bool) $data['status'] : TRUE,
      'uid' => $this->currentUser->id(),
    ];

    // Handle file path or URL.
    if (!empty($data['file']) || !empty($data['file_path']) || !empty($data['pdf_url'])) {
      $file_source = $data['file'] ?? $data['file_path'] ?? $data['pdf_url'];
      // File handling would be implemented here.
      // For now, skip file import in basic implementation.
    }

    // Optional fields.
    if (isset($data['allow_download'])) {
      $values['allow_download'] = (bool) $data['allow_download'];
    }
    if (isset($data['allow_print'])) {
      $values['allow_print'] = (bool) $data['allow_print'];
    }

    $document = PdfDocument::create($values);
    $document->save();

    return $document;
  }

  /**
   * Bulk update PDF documents.
   *
   * @param array $document_ids
   *   Array of document IDs to update.
   * @param array $values
   *   Values to set on all documents.
   *
   * @return array
   *   Results with 'success' and 'failed' counts.
   */
  public function bulkUpdate(array $document_ids, array $values): array {
    $results = [
      'success' => 0,
      'failed' => 0,
    ];

    $storage = $this->entityTypeManager->getStorage('pdf_document');
    $documents = $storage->loadMultiple($document_ids);

    foreach ($documents as $document) {
      try {
        foreach ($values as $field => $value) {
          if ($document->hasField($field)) {
            $document->set($field, $value);
          }
        }
        $document->save();
        $results['success']++;
      }
      catch (\Exception $e) {
        $results['failed']++;
      }
    }

    return $results;
  }

  /**
   * Bulk enable download for documents.
   *
   * @param array $document_ids
   *   Array of document IDs.
   *
   * @return array
   *   Results array.
   */
  public function bulkEnableDownload(array $document_ids): array {
    return $this->bulkUpdate($document_ids, ['allow_download' => TRUE]);
  }

  /**
   * Bulk disable download for documents.
   *
   * @param array $document_ids
   *   Array of document IDs.
   *
   * @return array
   *   Results array.
   */
  public function bulkDisableDownload(array $document_ids): array {
    return $this->bulkUpdate($document_ids, ['allow_download' => FALSE]);
  }

  /**
   * Bulk enable print for documents.
   *
   * @param array $document_ids
   *   Array of document IDs.
   *
   * @return array
   *   Results array.
   */
  public function bulkEnablePrint(array $document_ids): array {
    return $this->bulkUpdate($document_ids, ['allow_print' => TRUE]);
  }

  /**
   * Bulk disable print for documents.
   *
   * @param array $document_ids
   *   Array of document IDs.
   *
   * @return array
   *   Results array.
   */
  public function bulkDisablePrint(array $document_ids): array {
    return $this->bulkUpdate($document_ids, ['allow_print' => FALSE]);
  }

  /**
   * Get import status for a batch job.
   *
   * @param string $batch_id
   *   The batch ID.
   *
   * @return array|null
   *   Status array or NULL if not found.
   */
  public function getImportStatus(string $batch_id): ?array {
    // Implementation would use batch API or queue system.
    // For now, return basic structure.
    return [
      'batch_id' => $batch_id,
      'status' => 'completed',
      'processed' => 0,
      'total' => 0,
      'success' => 0,
      'failed' => 0,
    ];
  }

}

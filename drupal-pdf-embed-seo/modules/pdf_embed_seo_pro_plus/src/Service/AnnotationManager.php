<?php

namespace Drupal\pdf_embed_seo_pro_plus\Service;

use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * PDF annotation management service for Pro+ Enterprise.
 */
class AnnotationManager {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * UUID generator.
   *
   * @var \Drupal\Component\Uuid\UuidInterface
   */
  protected $uuid;

  /**
   * Annotation types.
   */
  const TYPE_HIGHLIGHT = 'highlight';
  const TYPE_UNDERLINE = 'underline';
  const TYPE_STRIKETHROUGH = 'strikethrough';
  const TYPE_TEXT = 'text';
  const TYPE_COMMENT = 'comment';
  const TYPE_DRAWING = 'drawing';
  const TYPE_SIGNATURE = 'signature';
  const TYPE_STAMP = 'stamp';

  /**
   * Constructs an AnnotationManager object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid
   *   The UUID generator.
   */
  public function __construct(
    Connection $database,
    AccountProxyInterface $current_user,
    UuidInterface $uuid
  ) {
    $this->database = $database;
    $this->currentUser = $current_user;
    $this->uuid = $uuid;
  }

  /**
   * Create a new annotation.
   *
   * @param int $document_id
   *   The PDF document entity ID.
   * @param int $page_number
   *   The page number.
   * @param string $type
   *   The annotation type.
   * @param array $data
   *   Annotation data including position, content, etc.
   *
   * @return string|false
   *   The annotation UUID or FALSE on failure.
   */
  public function create(int $document_id, int $page_number, string $type, array $data) {
    if (!$this->isValidType($type)) {
      return FALSE;
    }

    $annotation_uuid = $this->uuid->generate();

    try {
      $this->database->insert('pdf_annotations')
        ->fields([
          'uuid' => $annotation_uuid,
          'document_id' => $document_id,
          'user_id' => $this->currentUser->id(),
          'page_number' => $page_number,
          'annotation_type' => $type,
          'content' => $data['content'] ?? '',
          'position_x' => $data['position_x'] ?? 0,
          'position_y' => $data['position_y'] ?? 0,
          'width' => $data['width'] ?? 0,
          'height' => $data['height'] ?? 0,
          'color' => $data['color'] ?? '#ffff00',
          'metadata' => json_encode($data['metadata'] ?? []),
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ])
        ->execute();

      return $annotation_uuid;
    }
    catch (\Exception $e) {
      \Drupal::logger('pdf_embed_seo_pro_plus')->error('Failed to create annotation: @message', [
        '@message' => $e->getMessage(),
      ]);
      return FALSE;
    }
  }

  /**
   * Get an annotation by UUID.
   *
   * @param string $uuid
   *   The annotation UUID.
   *
   * @return array|null
   *   The annotation record or NULL.
   */
  public function get(string $uuid): ?array {
    try {
      $query = $this->database->select('pdf_annotations', 'a')
        ->fields('a')
        ->condition('uuid', $uuid);

      $result = $query->execute()->fetchAssoc();

      if ($result && !empty($result['metadata'])) {
        $result['metadata'] = json_decode($result['metadata'], TRUE);
      }

      return $result ?: NULL;
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Get all annotations for a document.
   *
   * @param int $document_id
   *   The PDF document entity ID.
   * @param int|null $page_number
   *   Optional page number filter.
   * @param int|null $user_id
   *   Optional user ID filter.
   *
   * @return array
   *   Array of annotation records.
   */
  public function getByDocument(int $document_id, ?int $page_number = NULL, ?int $user_id = NULL): array {
    try {
      $query = $this->database->select('pdf_annotations', 'a')
        ->fields('a')
        ->condition('document_id', $document_id)
        ->orderBy('page_number', 'ASC')
        ->orderBy('position_y', 'ASC');

      if ($page_number !== NULL) {
        $query->condition('page_number', $page_number);
      }

      if ($user_id !== NULL) {
        $query->condition('user_id', $user_id);
      }

      $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      // Decode metadata
      foreach ($results as &$result) {
        if (!empty($result['metadata'])) {
          $result['metadata'] = json_decode($result['metadata'], TRUE);
        }
      }

      return $results;
    }
    catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Update an annotation.
   *
   * @param string $uuid
   *   The annotation UUID.
   * @param array $data
   *   Updated annotation data.
   *
   * @return bool
   *   TRUE on success, FALSE on failure.
   */
  public function update(string $uuid, array $data): bool {
    $annotation = $this->get($uuid);
    if (!$annotation) {
      return FALSE;
    }

    // Only owner or admin can update
    if ($annotation['user_id'] != $this->currentUser->id() && !$this->currentUser->hasPermission('administer pdf_embed_seo')) {
      return FALSE;
    }

    $fields = ['updated_at' => date('Y-m-d H:i:s')];

    $allowed_fields = ['content', 'position_x', 'position_y', 'width', 'height', 'color'];
    foreach ($allowed_fields as $field) {
      if (isset($data[$field])) {
        $fields[$field] = $data[$field];
      }
    }

    if (isset($data['metadata'])) {
      $fields['metadata'] = json_encode($data['metadata']);
    }

    try {
      $this->database->update('pdf_annotations')
        ->fields($fields)
        ->condition('uuid', $uuid)
        ->execute();

      return TRUE;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Delete an annotation.
   *
   * @param string $uuid
   *   The annotation UUID.
   *
   * @return bool
   *   TRUE on success, FALSE on failure.
   */
  public function delete(string $uuid): bool {
    $annotation = $this->get($uuid);
    if (!$annotation) {
      return FALSE;
    }

    // Only owner or admin can delete
    if ($annotation['user_id'] != $this->currentUser->id() && !$this->currentUser->hasPermission('administer pdf_embed_seo')) {
      return FALSE;
    }

    try {
      $this->database->delete('pdf_annotations')
        ->condition('uuid', $uuid)
        ->execute();

      return TRUE;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Get annotation count for a document.
   *
   * @param int $document_id
   *   The PDF document entity ID.
   *
   * @return int
   *   The number of annotations.
   */
  public function getCount(int $document_id): int {
    try {
      $query = $this->database->select('pdf_annotations', 'a')
        ->condition('document_id', $document_id);
      $query->addExpression('COUNT(*)', 'count');

      return (int) $query->execute()->fetchField();
    }
    catch (\Exception $e) {
      return 0;
    }
  }

  /**
   * Check if document has annotations.
   *
   * @param int $document_id
   *   The PDF document entity ID.
   *
   * @return bool
   *   TRUE if document has annotations.
   */
  public function hasAnnotations(int $document_id): bool {
    return $this->getCount($document_id) > 0;
  }

  /**
   * Get valid annotation types.
   *
   * @return array
   *   Array of valid types.
   */
  public function getTypes(): array {
    return [
      self::TYPE_HIGHLIGHT,
      self::TYPE_UNDERLINE,
      self::TYPE_STRIKETHROUGH,
      self::TYPE_TEXT,
      self::TYPE_COMMENT,
      self::TYPE_DRAWING,
      self::TYPE_SIGNATURE,
      self::TYPE_STAMP,
    ];
  }

  /**
   * Check if annotation type is valid.
   *
   * @param string $type
   *   The annotation type.
   *
   * @return bool
   *   TRUE if valid.
   */
  public function isValidType(string $type): bool {
    return in_array($type, $this->getTypes(), TRUE);
  }

  /**
   * Export annotations for a document.
   *
   * @param int $document_id
   *   The PDF document entity ID.
   * @param string $format
   *   Export format (json, xfdf).
   *
   * @return string
   *   Exported data.
   */
  public function export(int $document_id, string $format = 'json'): string {
    $annotations = $this->getByDocument($document_id);

    if ($format === 'xfdf') {
      return $this->toXfdf($annotations);
    }

    return json_encode($annotations, JSON_PRETTY_PRINT);
  }

  /**
   * Convert annotations to XFDF format.
   *
   * @param array $annotations
   *   Array of annotations.
   *
   * @return string
   *   XFDF XML string.
   */
  protected function toXfdf(array $annotations): string {
    $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><xfdf xmlns="http://ns.adobe.com/xfdf/"></xfdf>');
    $annots = $xml->addChild('annots');

    foreach ($annotations as $annotation) {
      $type = $annotation['annotation_type'];
      $annot = $annots->addChild($type);
      $annot->addAttribute('page', $annotation['page_number'] - 1);
      $annot->addAttribute('color', $annotation['color']);
      $annot->addAttribute('rect', sprintf(
        '%.2f,%.2f,%.2f,%.2f',
        $annotation['position_x'],
        $annotation['position_y'],
        $annotation['position_x'] + $annotation['width'],
        $annotation['position_y'] + $annotation['height']
      ));

      if (!empty($annotation['content'])) {
        $annot->addChild('contents', htmlspecialchars($annotation['content']));
      }
    }

    return $xml->asXML();
  }

  /**
   * Delete all annotations for a document.
   *
   * @param int $document_id
   *   The PDF document entity ID.
   *
   * @return int
   *   Number of deleted annotations.
   */
  public function deleteByDocument(int $document_id): int {
    try {
      return $this->database->delete('pdf_annotations')
        ->condition('document_id', $document_id)
        ->execute();
    }
    catch (\Exception $e) {
      return 0;
    }
  }

}

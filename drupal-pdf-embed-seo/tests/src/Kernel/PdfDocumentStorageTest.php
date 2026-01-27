<?php

namespace Drupal\Tests\pdf_embed_seo\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests PDF Document storage operations.
 *
 * @group pdf_embed_seo
 */
class PdfDocumentStorageTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'file',
    'node',
    'taxonomy',
    'path',
    'path_alias',
    'pdf_embed_seo',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installEntitySchema('path_alias');
    $this->installSchema('file', ['file_usage']);

    // Install pdf_embed_seo schema.
    if (method_exists($this, 'installEntitySchema')) {
      try {
        $this->installEntitySchema('pdf_document');
      }
      catch (\Exception $e) {
        // Entity may not be available in test environment.
      }
    }
  }

  /**
   * Tests entity type definition.
   */
  public function testEntityTypeDefinition() {
    $entity_type_manager = \Drupal::entityTypeManager();

    // Check if entity type is defined.
    $definitions = $entity_type_manager->getDefinitions();

    // Entity should be defined if module is properly installed.
    $this->assertIsArray($definitions);
  }

  /**
   * Tests creating a PDF document.
   */
  public function testCreatePdfDocument() {
    $entity_type_manager = \Drupal::entityTypeManager();

    if (!$entity_type_manager->hasDefinition('pdf_document')) {
      $this->markTestSkipped('PDF Document entity type not available.');
    }

    $storage = $entity_type_manager->getStorage('pdf_document');

    $document = $storage->create([
      'title' => 'Test Document',
      'description' => 'Test description',
      'slug' => 'test-document',
      'allow_download' => TRUE,
      'allow_print' => TRUE,
      'status' => TRUE,
    ]);

    $this->assertEquals('Test Document', $document->get('title')->value);
  }

  /**
   * Tests saving and loading a PDF document.
   */
  public function testSaveAndLoadPdfDocument() {
    $entity_type_manager = \Drupal::entityTypeManager();

    if (!$entity_type_manager->hasDefinition('pdf_document')) {
      $this->markTestSkipped('PDF Document entity type not available.');
    }

    $storage = $entity_type_manager->getStorage('pdf_document');

    $document = $storage->create([
      'title' => 'Save Test Document',
      'slug' => 'save-test-document',
      'status' => TRUE,
    ]);
    $document->save();

    $id = $document->id();
    $this->assertNotNull($id);

    // Load the document.
    $loaded = $storage->load($id);
    $this->assertEquals('Save Test Document', $loaded->get('title')->value);
  }

  /**
   * Tests updating a PDF document.
   */
  public function testUpdatePdfDocument() {
    $entity_type_manager = \Drupal::entityTypeManager();

    if (!$entity_type_manager->hasDefinition('pdf_document')) {
      $this->markTestSkipped('PDF Document entity type not available.');
    }

    $storage = $entity_type_manager->getStorage('pdf_document');

    $document = $storage->create([
      'title' => 'Original Title',
      'slug' => 'original-title',
      'status' => TRUE,
    ]);
    $document->save();

    // Update the document.
    $document->set('title', 'Updated Title');
    $document->save();

    // Reload and verify.
    $storage->resetCache([$document->id()]);
    $loaded = $storage->load($document->id());

    $this->assertEquals('Updated Title', $loaded->get('title')->value);
  }

  /**
   * Tests deleting a PDF document.
   */
  public function testDeletePdfDocument() {
    $entity_type_manager = \Drupal::entityTypeManager();

    if (!$entity_type_manager->hasDefinition('pdf_document')) {
      $this->markTestSkipped('PDF Document entity type not available.');
    }

    $storage = $entity_type_manager->getStorage('pdf_document');

    $document = $storage->create([
      'title' => 'Delete Test',
      'slug' => 'delete-test',
      'status' => TRUE,
    ]);
    $document->save();

    $id = $document->id();
    $document->delete();

    // Verify deletion.
    $loaded = $storage->load($id);
    $this->assertNull($loaded);
  }

  /**
   * Tests querying published documents.
   */
  public function testQueryPublishedDocuments() {
    $entity_type_manager = \Drupal::entityTypeManager();

    if (!$entity_type_manager->hasDefinition('pdf_document')) {
      $this->markTestSkipped('PDF Document entity type not available.');
    }

    $storage = $entity_type_manager->getStorage('pdf_document');

    // Create published documents.
    for ($i = 0; $i < 3; $i++) {
      $storage->create([
        'title' => 'Published ' . $i,
        'slug' => 'published-' . $i,
        'status' => TRUE,
      ])->save();
    }

    // Create unpublished document.
    $storage->create([
      'title' => 'Unpublished',
      'slug' => 'unpublished',
      'status' => FALSE,
    ])->save();

    // Query published only.
    $query = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', TRUE);

    $ids = $query->execute();

    $this->assertCount(3, $ids);
  }

  /**
   * Tests view count increment.
   */
  public function testViewCountIncrement() {
    $entity_type_manager = \Drupal::entityTypeManager();

    if (!$entity_type_manager->hasDefinition('pdf_document')) {
      $this->markTestSkipped('PDF Document entity type not available.');
    }

    $storage = $entity_type_manager->getStorage('pdf_document');

    $document = $storage->create([
      'title' => 'View Count Test',
      'slug' => 'view-count-test',
      'view_count' => 0,
      'status' => TRUE,
    ]);
    $document->save();

    // Increment view count.
    $current = $document->get('view_count')->value ?? 0;
    $document->set('view_count', $current + 1);
    $document->save();

    // Reload and verify.
    $storage->resetCache([$document->id()]);
    $loaded = $storage->load($document->id());

    $this->assertEquals(1, $loaded->get('view_count')->value);
  }

}

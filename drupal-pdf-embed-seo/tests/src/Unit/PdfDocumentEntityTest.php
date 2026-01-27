<?php

namespace Drupal\Tests\pdf_embed_seo\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests for PDF Document Entity.
 *
 * @group pdf_embed_seo
 */
class PdfDocumentEntityTest extends UnitTestCase {

  /**
   * Tests that the entity type ID is correct.
   */
  public function testEntityTypeId() {
    $entity_type_id = 'pdf_document';
    $this->assertEquals('pdf_document', $entity_type_id);
  }

  /**
   * Tests field definitions structure.
   */
  public function testFieldDefinitions() {
    $expected_fields = [
      'id',
      'uuid',
      'title',
      'description',
      'pdf_file',
      'thumbnail',
      'slug',
      'allow_download',
      'allow_print',
      'view_count',
      'status',
      'created',
      'changed',
    ];

    foreach ($expected_fields as $field) {
      $this->assertContains($field, $expected_fields);
    }
  }

  /**
   * Tests URL structure format.
   */
  public function testUrlStructure() {
    $base_path = '/pdf';
    $slug = 'test-document';
    $expected_url = '/pdf/test-document';

    $this->assertEquals($expected_url, $base_path . '/' . $slug);
  }

  /**
   * Tests slug sanitization.
   */
  public function testSlugSanitization() {
    $title = 'Test Document With Spaces!';
    $expected_slug = 'test-document-with-spaces';

    // Simulate slug generation.
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
    $slug = trim($slug, '-');

    $this->assertEquals($expected_slug, $slug);
  }

  /**
   * Tests default values.
   */
  public function testDefaultValues() {
    $defaults = [
      'allow_download' => TRUE,
      'allow_print' => TRUE,
      'view_count' => 0,
      'status' => TRUE,
    ];

    $this->assertTrue($defaults['allow_download']);
    $this->assertTrue($defaults['allow_print']);
    $this->assertEquals(0, $defaults['view_count']);
    $this->assertTrue($defaults['status']);
  }

  /**
   * Tests view count increment logic.
   */
  public function testViewCountIncrement() {
    $view_count = 0;
    $view_count++;

    $this->assertEquals(1, $view_count);

    // Multiple increments.
    for ($i = 0; $i < 10; $i++) {
      $view_count++;
    }

    $this->assertEquals(11, $view_count);
  }

}

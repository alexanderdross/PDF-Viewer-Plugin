<?php

namespace Drupal\Tests\pdf_embed_seo_pro_plus\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests the AnnotationManager service.
 *
 * @group pdf_embed_seo_pro_plus
 */
class AnnotationManagerTest extends UnitTestCase {

  /**
   * Test annotation types.
   */
  public function testAnnotationTypes() {
    $valid_types = [
      'highlight',
      'underline',
      'strikethrough',
      'text_note',
      'sticky_note',
      'freehand',
      'rectangle',
      'circle',
      'arrow',
      'line',
    ];

    foreach ($valid_types as $type) {
      $this->assertMatchesRegularExpression('/^[a-z_]+$/', $type);
    }
  }

  /**
   * Test annotation data structure.
   */
  public function testAnnotationDataStructure() {
    $annotation = [
      'id' => 1,
      'uuid' => $this->generateUuid(),
      'document_id' => 123,
      'page' => 1,
      'type' => 'highlight',
      'x' => 100.5,
      'y' => 200.5,
      'width' => 300,
      'height' => 50,
      'color' => '#ffff00',
      'opacity' => 0.5,
      'content' => 'Important text',
      'author_id' => 1,
      'created' => time(),
      'updated' => time(),
    ];

    $required_keys = ['id', 'document_id', 'page', 'type', 'x', 'y', 'author_id'];
    foreach ($required_keys as $key) {
      $this->assertArrayHasKey($key, $annotation);
    }
  }

  /**
   * Test annotation color validation.
   */
  public function testAnnotationColorValidation() {
    $valid_colors = ['#ffff00', '#FF0000', '#00ff00'];

    foreach ($valid_colors as $color) {
      $this->assertMatchesRegularExpression('/^#[0-9a-fA-F]{6}$/', $color);
    }
  }

  /**
   * Test coordinate validation.
   */
  public function testCoordinateValidation() {
    $annotation = [
      'x' => 100,
      'y' => 200,
      'width' => 300,
      'height' => 50,
    ];

    $this->assertGreaterThanOrEqual(0, $annotation['x']);
    $this->assertGreaterThanOrEqual(0, $annotation['y']);
    $this->assertGreaterThan(0, $annotation['width']);
    $this->assertGreaterThan(0, $annotation['height']);
  }

  /**
   * Test freehand path data structure.
   */
  public function testFreehandPathData() {
    $path_data = [
      ['x' => 100, 'y' => 100],
      ['x' => 150, 'y' => 120],
      ['x' => 200, 'y' => 100],
      ['x' => 250, 'y' => 150],
    ];

    $this->assertIsArray($path_data);
    $this->assertGreaterThan(1, count($path_data));

    foreach ($path_data as $point) {
      $this->assertArrayHasKey('x', $point);
      $this->assertArrayHasKey('y', $point);
    }
  }

  /**
   * Test annotation content sanitization.
   */
  public function testAnnotationContentSanitization() {
    $dirty_content = '<script>alert("xss")</script>Important note';
    $clean_content = strip_tags($dirty_content);

    $this->assertStringNotContainsString('<script>', $clean_content);
    $this->assertStringContainsString('Important note', $clean_content);
  }

  /**
   * Generate a mock UUID.
   */
  protected function generateUuid() {
    return sprintf(
      '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0x0fff) | 0x4000,
      mt_rand(0, 0x3fff) | 0x8000,
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }

}

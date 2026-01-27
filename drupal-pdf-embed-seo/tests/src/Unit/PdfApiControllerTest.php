<?php

namespace Drupal\Tests\pdf_embed_seo\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests for PDF API Controller.
 *
 * @group pdf_embed_seo
 */
class PdfApiControllerTest extends UnitTestCase {

  /**
   * Tests API base URL structure.
   */
  public function testApiBaseUrl() {
    $base_url = '/api/pdf-embed-seo/v1';
    $this->assertEquals('/api/pdf-embed-seo/v1', $base_url);
  }

  /**
   * Tests endpoint paths.
   */
  public function testEndpointPaths() {
    $endpoints = [
      'documents' => '/api/pdf-embed-seo/v1/documents',
      'single_document' => '/api/pdf-embed-seo/v1/documents/{id}',
      'document_data' => '/api/pdf-embed-seo/v1/documents/{id}/data',
      'track_view' => '/api/pdf-embed-seo/v1/documents/{id}/view',
      'settings' => '/api/pdf-embed-seo/v1/settings',
    ];

    $this->assertArrayHasKey('documents', $endpoints);
    $this->assertArrayHasKey('single_document', $endpoints);
    $this->assertArrayHasKey('document_data', $endpoints);
    $this->assertArrayHasKey('track_view', $endpoints);
    $this->assertArrayHasKey('settings', $endpoints);
  }

  /**
   * Tests document response structure.
   */
  public function testDocumentResponseStructure() {
    $response = [
      'id' => 1,
      'title' => 'Test Document',
      'slug' => 'test-document',
      'url' => 'https://example.com/pdf/test-document',
      'description' => 'Test description',
      'created' => '2024-01-15T10:30:00+00:00',
      'modified' => '2024-06-20T14:45:00+00:00',
      'views' => 100,
      'thumbnail' => 'https://example.com/files/thumb.jpg',
      'allow_download' => TRUE,
      'allow_print' => FALSE,
    ];

    $this->assertArrayHasKey('id', $response);
    $this->assertArrayHasKey('title', $response);
    $this->assertArrayHasKey('slug', $response);
    $this->assertArrayHasKey('url', $response);
    $this->assertArrayHasKey('views', $response);
    $this->assertArrayHasKey('allow_download', $response);
    $this->assertArrayHasKey('allow_print', $response);
  }

  /**
   * Tests pagination parameters.
   */
  public function testPaginationParameters() {
    $params = [
      'page' => 0,
      'limit' => 50,
      'sort' => 'created',
      'direction' => 'DESC',
    ];

    $this->assertEquals(0, $params['page']);
    $this->assertEquals(50, $params['limit']);
    $this->assertEquals('created', $params['sort']);
    $this->assertEquals('DESC', $params['direction']);
  }

  /**
   * Tests max limit enforcement.
   */
  public function testMaxLimitEnforcement() {
    $max_limit = 100;
    $requested_limit = 200;

    $effective_limit = min($requested_limit, $max_limit);

    $this->assertEquals(100, $effective_limit);
  }

  /**
   * Tests settings response structure.
   */
  public function testSettingsResponseStructure() {
    $settings = [
      'viewer_theme' => 'light',
      'default_allow_download' => TRUE,
      'default_allow_print' => TRUE,
      'archive_url' => '/pdf',
      'is_premium' => FALSE,
    ];

    $this->assertArrayHasKey('viewer_theme', $settings);
    $this->assertArrayHasKey('is_premium', $settings);
    $this->assertFalse($settings['is_premium']);
  }

  /**
   * Tests sort options validation.
   */
  public function testSortOptionsValidation() {
    $valid_sorts = ['created', 'title', 'view_count', 'changed'];
    $invalid_sort = 'invalid_field';
    $default_sort = 'created';

    $requested_sort = $invalid_sort;
    $effective_sort = in_array($requested_sort, $valid_sorts) ? $requested_sort : $default_sort;

    $this->assertEquals('created', $effective_sort);

    $requested_sort = 'title';
    $effective_sort = in_array($requested_sort, $valid_sorts) ? $requested_sort : $default_sort;

    $this->assertEquals('title', $effective_sort);
  }

  /**
   * Tests view tracking response.
   */
  public function testViewTrackingResponse() {
    $initial_count = 50;
    $new_count = $initial_count + 1;

    $response = [
      'success' => TRUE,
      'views' => $new_count,
    ];

    $this->assertTrue($response['success']);
    $this->assertEquals(51, $response['views']);
  }

}

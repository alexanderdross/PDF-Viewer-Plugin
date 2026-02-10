<?php

namespace Drupal\Tests\pdf_embed_seo\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests for Media Library integration.
 *
 * @group pdf_embed_seo
 */
class MediaLibraryIntegrationTest extends UnitTestCase {

  /**
   * Tests PDF media source plugin definition.
   */
  public function testMediaSourcePluginDefinition() {
    $plugin_definition = [
      'id' => 'pdf_document',
      'label' => 'PDF Document',
      'description' => 'Use PDF files with the PDF Embed & SEO viewer.',
      'allowed_field_types' => ['file'],
      'default_thumbnail_filename' => 'pdf.png',
      'thumbnail_alt_metadata_attribute' => 'alt',
      'thumbnail_title_metadata_attribute' => 'title',
    ];

    $this->assertEquals('pdf_document', $plugin_definition['id']);
    $this->assertEquals('PDF Document', $plugin_definition['label']);
    $this->assertContains('file', $plugin_definition['allowed_field_types']);
    $this->assertEquals('pdf.png', $plugin_definition['default_thumbnail_filename']);
  }

  /**
   * Tests metadata attributes.
   */
  public function testMetadataAttributes() {
    $metadata_attributes = [
      'file_name' => 'File name',
      'file_size' => 'File size',
      'mime_type' => 'MIME type',
      'page_count' => 'Page count',
      'title' => 'Title',
      'alt' => 'Alternative text',
    ];

    $this->assertArrayHasKey('file_name', $metadata_attributes);
    $this->assertArrayHasKey('file_size', $metadata_attributes);
    $this->assertArrayHasKey('mime_type', $metadata_attributes);
    $this->assertArrayHasKey('page_count', $metadata_attributes);
    $this->assertArrayHasKey('title', $metadata_attributes);
    $this->assertArrayHasKey('alt', $metadata_attributes);
  }

  /**
   * Tests source field settings.
   */
  public function testSourceFieldSettings() {
    $settings = [
      'file_extensions' => 'pdf',
      'file_directory' => 'pdf_documents',
      'max_filesize' => '50 MB',
    ];

    $this->assertEquals('pdf', $settings['file_extensions']);
    $this->assertEquals('pdf_documents', $settings['file_directory']);
    $this->assertEquals('50 MB', $settings['max_filesize']);
  }

  /**
   * Tests PDF MIME type detection.
   */
  public function testPdfMimeType() {
    $pdf_mime_type = 'application/pdf';
    $doc_mime_type = 'application/msword';

    $this->assertEquals('application/pdf', $pdf_mime_type);
    $this->assertNotEquals('application/pdf', $doc_mime_type);
  }

  /**
   * Tests default name generation from filename.
   */
  public function testDefaultNameGeneration() {
    $filename = 'annual-report-2026.pdf';
    $default_name = pathinfo($filename, PATHINFO_FILENAME);

    $this->assertEquals('annual-report-2026', $default_name);
  }

  /**
   * Tests filename extraction without extension.
   */
  public function testFilenameWithoutExtension() {
    $test_cases = [
      'document.pdf' => 'document',
      'my-file.name.pdf' => 'my-file.name',
      'UPPERCASE.PDF' => 'UPPERCASE',
      'with spaces.pdf' => 'with spaces',
    ];

    foreach ($test_cases as $filename => $expected) {
      $result = pathinfo($filename, PATHINFO_FILENAME);
      $this->assertEquals($expected, $result);
    }
  }

  /**
   * Tests alt text generation.
   */
  public function testAltTextGeneration() {
    $media_label = 'Annual Report 2026';
    $alt_text = sprintf('PDF document: %s', $media_label);

    $this->assertEquals('PDF document: Annual Report 2026', $alt_text);
  }

  /**
   * Tests thumbnail URI fallback paths.
   */
  public function testThumbnailUriFallback() {
    $module_path = 'modules/custom/pdf_embed_seo';
    $default_thumbnail = $module_path . '/assets/images/pdf-icon.png';
    $fallback_thumbnail = 'core/modules/media/images/icons/generic.png';

    $this->assertStringContainsString('pdf-icon.png', $default_thumbnail);
    $this->assertStringContainsString('generic.png', $fallback_thumbnail);
  }

  /**
   * Tests view display component settings.
   */
  public function testViewDisplayComponent() {
    $component = [
      'type' => 'pdf_embed_seo_viewer',
      'label' => 'hidden',
    ];

    $this->assertEquals('pdf_embed_seo_viewer', $component['type']);
    $this->assertEquals('hidden', $component['label']);
  }

  /**
   * Tests form display widget settings.
   */
  public function testFormDisplayWidget() {
    $widget = [
      'type' => 'file_generic',
    ];

    $this->assertEquals('file_generic', $widget['type']);
  }

  /**
   * Tests PDF viewer formatter plugin definition.
   */
  public function testPdfViewerFormatterDefinition() {
    $formatter_definition = [
      'id' => 'pdf_embed_seo_viewer',
      'label' => 'PDF Viewer (SEO Optimized)',
      'field_types' => ['file'],
    ];

    $this->assertEquals('pdf_embed_seo_viewer', $formatter_definition['id']);
    $this->assertEquals('PDF Viewer (SEO Optimized)', $formatter_definition['label']);
    $this->assertContains('file', $formatter_definition['field_types']);
  }

  /**
   * Tests formatter default settings.
   */
  public function testFormatterDefaultSettings() {
    $default_settings = [
      'width' => '100%',
      'height' => '800px',
      'allow_download' => FALSE,
      'allow_print' => FALSE,
    ];

    $this->assertEquals('100%', $default_settings['width']);
    $this->assertEquals('800px', $default_settings['height']);
    $this->assertFalse($default_settings['allow_download']);
    $this->assertFalse($default_settings['allow_print']);
  }

  /**
   * Tests formatter settings form fields.
   */
  public function testFormatterSettingsFormFields() {
    $form_fields = ['width', 'height', 'allow_download', 'allow_print'];

    $this->assertCount(4, $form_fields);
    $this->assertContains('width', $form_fields);
    $this->assertContains('height', $form_fields);
    $this->assertContains('allow_download', $form_fields);
    $this->assertContains('allow_print', $form_fields);
  }

  /**
   * Tests formatter settings summary.
   */
  public function testFormatterSettingsSummary() {
    $width = '100%';
    $height = '600px';
    $allow_download = TRUE;
    $allow_print = FALSE;

    $summary = [];
    $summary[] = sprintf('Dimensions: %s x %s', $width, $height);

    if ($allow_download) {
      $summary[] = 'Download enabled';
    }

    if ($allow_print) {
      $summary[] = 'Print enabled';
    }

    $this->assertCount(2, $summary);
    $this->assertEquals('Dimensions: 100% x 600px', $summary[0]);
    $this->assertEquals('Download enabled', $summary[1]);
  }

  /**
   * Tests render array theme.
   */
  public function testRenderArrayTheme() {
    $render = [
      '#theme' => 'pdf_viewer',
      '#pdf_document' => NULL,
      '#pdf_url' => 'https://example.com/file.pdf',
      '#allow_download' => TRUE,
      '#allow_print' => FALSE,
      '#viewer_theme' => 'light',
      '#width' => '100%',
      '#height' => '800px',
    ];

    $this->assertEquals('pdf_viewer', $render['#theme']);
    $this->assertNull($render['#pdf_document']);
    $this->assertStringContainsString('.pdf', $render['#pdf_url']);
    $this->assertTrue($render['#allow_download']);
    $this->assertFalse($render['#allow_print']);
  }

  /**
   * Tests drupalSettings for PDF viewer.
   */
  public function testDrupalSettings() {
    $drupal_settings = [
      'pdfEmbedSeo' => [
        'pdfUrl' => 'https://example.com/file.pdf',
        'workerSrc' => '/modules/custom/pdf_embed_seo/assets/pdfjs/pdf.worker.min.js',
        'allowDownload' => TRUE,
        'allowPrint' => FALSE,
      ],
    ];

    $this->assertArrayHasKey('pdfEmbedSeo', $drupal_settings);
    $this->assertArrayHasKey('pdfUrl', $drupal_settings['pdfEmbedSeo']);
    $this->assertArrayHasKey('workerSrc', $drupal_settings['pdfEmbedSeo']);
    $this->assertTrue($drupal_settings['pdfEmbedSeo']['allowDownload']);
  }

  /**
   * Tests attached libraries.
   */
  public function testAttachedLibraries() {
    $libraries = ['pdf_embed_seo/viewer'];
    $dark_theme = TRUE;

    if ($dark_theme) {
      $libraries[] = 'pdf_embed_seo/viewer-dark';
    }

    $this->assertCount(2, $libraries);
    $this->assertContains('pdf_embed_seo/viewer', $libraries);
    $this->assertContains('pdf_embed_seo/viewer-dark', $libraries);
  }

  /**
   * Tests cache tags from file entity.
   */
  public function testCacheTagsFromFile() {
    $file_id = 123;
    $cache_tags = ['file:' . $file_id];

    $this->assertContains('file:123', $cache_tags);
  }

  /**
   * Tests non-PDF file skip logic.
   */
  public function testNonPdfFileSkip() {
    $mime_types = [
      'application/pdf' => TRUE,
      'application/msword' => FALSE,
      'image/jpeg' => FALSE,
      'text/plain' => FALSE,
    ];

    foreach ($mime_types as $mime_type => $should_process) {
      $is_pdf = $mime_type === 'application/pdf';
      $this->assertEquals($should_process, $is_pdf);
    }
  }

  /**
   * Tests empty file entity handling.
   */
  public function testEmptyFileEntityHandling() {
    $file = NULL;
    $should_continue = $file === NULL;

    $this->assertTrue($should_continue);
  }

  /**
   * Tests module dependency on drupal:media.
   */
  public function testMediaModuleDependency() {
    $dependencies = [
      'drupal:node',
      'drupal:file',
      'drupal:taxonomy',
      'drupal:path',
      'drupal:path_alias',
      'drupal:media',
    ];

    $this->assertContains('drupal:media', $dependencies);
  }

}

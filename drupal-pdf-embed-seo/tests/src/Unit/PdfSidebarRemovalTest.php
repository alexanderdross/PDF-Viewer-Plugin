<?php

namespace Drupal\Tests\pdf_embed_seo\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests for PDF sidebar removal hooks (v1.2.7).
 *
 * @group pdf_embed_seo
 * @coversDefaultClass \Drupal\pdf_embed_seo\pdf_embed_seo
 */
class PdfSidebarRemovalTest extends UnitTestCase {

  /**
   * Test module file contains hook_theme_suggestions_page_alter.
   */
  public function testHookThemeSuggestionsPageAlterExists() {
    $module_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/pdf_embed_seo.module';
    $this->assertFileExists($module_path);

    $content = file_get_contents($module_path);

    // Check hook function exists.
    $this->assertStringContainsString(
      'function pdf_embed_seo_theme_suggestions_page_alter',
      $content,
      'Module should implement hook_theme_suggestions_page_alter'
    );

    // Check it adds page__pdf suggestion.
    $this->assertStringContainsString(
      "page__pdf",
      $content,
      'Hook should add page__pdf suggestion'
    );
  }

  /**
   * Test module file contains hook_preprocess_page.
   */
  public function testHookPreprocessPageExists() {
    $module_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/pdf_embed_seo.module';
    $content = file_get_contents($module_path);

    // Check hook function exists.
    $this->assertStringContainsString(
      'function pdf_embed_seo_preprocess_page',
      $content,
      'Module should implement hook_preprocess_page'
    );

    // Check it clears sidebar regions.
    $this->assertStringContainsString(
      'sidebar_first',
      $content,
      'Hook should handle sidebar_first region'
    );

    $this->assertStringContainsString(
      'sidebar_second',
      $content,
      'Hook should handle sidebar_second region'
    );
  }

  /**
   * Test module file contains hook_preprocess_html.
   */
  public function testHookPreprocessHtmlExists() {
    $module_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/pdf_embed_seo.module';
    $content = file_get_contents($module_path);

    // Check hook function exists.
    $this->assertStringContainsString(
      'function pdf_embed_seo_preprocess_html',
      $content,
      'Module should implement hook_preprocess_html'
    );

    // Check it adds body classes.
    $this->assertStringContainsString(
      'page-pdf',
      $content,
      'Hook should add page-pdf body class'
    );

    $this->assertStringContainsString(
      'page-pdf-no-sidebar',
      $content,
      'Hook should add page-pdf-no-sidebar body class'
    );
  }

  /**
   * Test CSS file contains sidebar hiding rules.
   */
  public function testCssSidebarHidingRules() {
    $css_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/assets/css/pdf-archive.css';
    $this->assertFileExists($css_path);

    $content = file_get_contents($css_path);

    // Check for page-pdf selectors.
    $this->assertStringContainsString(
      '.page-pdf .layout-sidebar-first',
      $content,
      'CSS should hide .layout-sidebar-first on PDF pages'
    );

    $this->assertStringContainsString(
      '.page-pdf .layout-sidebar-second',
      $content,
      'CSS should hide .layout-sidebar-second on PDF pages'
    );

    $this->assertStringContainsString(
      '.page-pdf .region-sidebar-first',
      $content,
      'CSS should hide .region-sidebar-first on PDF pages'
    );

    $this->assertStringContainsString(
      '.page-pdf .region-sidebar-second',
      $content,
      'CSS should hide .region-sidebar-second on PDF pages'
    );

    // Check for display: none rule.
    $this->assertStringContainsString(
      'display: none !important',
      $content,
      'CSS should use display: none for sidebar hiding'
    );
  }

  /**
   * Test CSS file contains full-width content rules.
   */
  public function testCssFullWidthContentRules() {
    $css_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/assets/css/pdf-archive.css';
    $content = file_get_contents($css_path);

    // Check for full-width selectors.
    $this->assertStringContainsString(
      '.page-pdf .layout-content',
      $content,
      'CSS should target .layout-content on PDF pages'
    );

    $this->assertStringContainsString(
      '.page-pdf .region-content',
      $content,
      'CSS should target .region-content on PDF pages'
    );

    // Check for width rules.
    $this->assertStringContainsString(
      'width: 100% !important',
      $content,
      'CSS should force 100% width on PDF content'
    );

    $this->assertStringContainsString(
      'flex: 0 0 100% !important',
      $content,
      'CSS should force flex full width on PDF content'
    );
  }

  /**
   * Test module hooks target correct PDF routes.
   */
  public function testPdfRoutesTargeted() {
    $module_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/pdf_embed_seo.module';
    $content = file_get_contents($module_path);

    // Check for PDF route names in hooks.
    $this->assertStringContainsString(
      'entity.pdf_document.canonical',
      $content,
      'Hooks should target PDF document canonical route'
    );

    $this->assertStringContainsString(
      'pdf_embed_seo.archive',
      $content,
      'Hooks should target PDF archive route'
    );
  }

}

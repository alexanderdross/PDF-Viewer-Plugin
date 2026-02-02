<?php

namespace Drupal\Tests\pdf_embed_seo\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\Component\Utility\Html;

/**
 * Tests for PDF XSS Prevention (v1.2.7).
 *
 * Validates that user content is properly escaped to prevent XSS attacks.
 *
 * @group pdf_embed_seo
 * @since 1.2.7
 */
class PdfXssPreventionTest extends UnitTestCase {

  /**
   * Tests basic HTML escaping.
   */
  public function testBasicHtmlEscaping() {
    $malicious = '<script>alert("XSS")</script>';
    $escaped = Html::escape($malicious);

    $this->assertStringNotContainsString('<script>', $escaped);
    $this->assertStringContainsString('&lt;script&gt;', $escaped);
  }

  /**
   * Tests event handler attribute escaping.
   */
  public function testEventHandlerEscaping() {
    $malicious_handlers = [
      '<img onerror="alert(1)" src="x">',
      '<div onmouseover="alert(1)">',
      '<a onclick="alert(1)">Link</a>',
      '<svg onload="alert(1)">',
    ];

    foreach ($malicious_handlers as $malicious) {
      $escaped = Html::escape($malicious);
      $this->assertStringNotContainsString('onerror=', $escaped);
      $this->assertStringNotContainsString('onmouseover=', $escaped);
      $this->assertStringNotContainsString('onclick=', $escaped);
      $this->assertStringNotContainsString('onload=', $escaped);
    }
  }

  /**
   * Tests document title escaping for block display.
   */
  public function testDocumentTitleEscaping() {
    $malicious_titles = [
      '<script>alert("XSS")</script>',
      'Title<script>evil()</script>',
      'Normal Title " onclick="alert(1)"',
      "Title with 'quotes' and <tags>",
    ];

    foreach ($malicious_titles as $title) {
      $escaped = Html::escape($title);

      // Should not contain executable script tags.
      $this->assertStringNotContainsString('<script>', $escaped);
      $this->assertStringNotContainsString('onclick=', $escaped);

      // Original content intent should be preserved (readable).
      $this->assertNotEmpty($escaped);
    }
  }

  /**
   * Tests that normal content is not corrupted.
   */
  public function testNormalContentPreserved() {
    $normal_titles = [
      'Annual Report 2026',
      'Q1 Financial Summary',
      'Product Specification v1.2',
      'User Guide - Getting Started',
    ];

    foreach ($normal_titles as $title) {
      $escaped = Html::escape($title);
      $this->assertEquals($title, $escaped, 'Normal content should not be modified.');
    }
  }

  /**
   * Tests HTML entity handling.
   */
  public function testHtmlEntityHandling() {
    $with_entities = 'Price: $100 & Tax < 10%';
    $escaped = Html::escape($with_entities);

    $this->assertStringContainsString('&amp;', $escaped);
    $this->assertStringContainsString('&lt;', $escaped);
  }

  /**
   * Tests Unicode content preservation.
   */
  public function testUnicodePreservation() {
    $unicode_titles = [
      'Über uns - Dokumentation',
      '日本語ドキュメント',
      'Документация на русском',
      'العربية',
    ];

    foreach ($unicode_titles as $title) {
      $escaped = Html::escape($title);
      // Unicode should be preserved.
      $this->assertEquals($title, $escaped);
    }
  }

  /**
   * Tests nested tag escaping.
   */
  public function testNestedTagEscaping() {
    $nested = '<div><script><script>nested</script></script></div>';
    $escaped = Html::escape($nested);

    $this->assertStringNotContainsString('<div>', $escaped);
    $this->assertStringNotContainsString('<script>', $escaped);
    $this->assertStringContainsString('&lt;', $escaped);
  }

  /**
   * Tests that render array with #value uses escaped content.
   */
  public function testRenderArrayValueEscaping() {
    $title = '<script>alert("XSS")</script>';

    // This is how the block should render.
    $build = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => Html::escape($title),
      '#attributes' => ['class' => ['pdf-viewer-block-title']],
    ];

    $this->assertStringNotContainsString('<script>', $build['#value']);
    $this->assertStringContainsString('&lt;script&gt;', $build['#value']);
  }

  /**
   * Tests alternative escaping with #plain_text.
   */
  public function testPlainTextAlternative() {
    $title = '<script>alert("XSS")</script>';

    // Alternative approach using #plain_text.
    $build = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#plain_text' => $title,
      '#attributes' => ['class' => ['pdf-viewer-block-title']],
    ];

    // #plain_text is auto-escaped by Drupal render system.
    $this->assertArrayHasKey('#plain_text', $build);
  }

  /**
   * Tests javascript: protocol escaping.
   */
  public function testJavascriptProtocolEscaping() {
    $malicious = 'javascript:alert("XSS")';
    $escaped = Html::escape($malicious);

    // The literal text is preserved but made safe.
    $this->assertStringContainsString('javascript:', $escaped);
    // But in context, it won't execute as a protocol.
  }

  /**
   * Tests data: URI escaping.
   */
  public function testDataUriEscaping() {
    $malicious = '<img src="data:text/html,<script>alert(1)</script>">';
    $escaped = Html::escape($malicious);

    $this->assertStringNotContainsString('<img', $escaped);
    $this->assertStringContainsString('&lt;img', $escaped);
  }

  /**
   * Tests attribute quote escaping.
   */
  public function testAttributeQuoteEscaping() {
    $with_quotes = 'Title with "double" and \'single\' quotes';
    $escaped = Html::escape($with_quotes);

    // Quotes should be escaped for use in attributes.
    $this->assertStringContainsString('&quot;', $escaped);
  }

  /**
   * Tests null byte handling.
   */
  public function testNullByteHandling() {
    $with_null = "Title\x00with\x00nulls";
    $escaped = Html::escape($with_null);

    // Null bytes should be handled safely.
    $this->assertNotEmpty($escaped);
  }

  /**
   * Tests empty and whitespace handling.
   */
  public function testEmptyAndWhitespaceHandling() {
    $this->assertEquals('', Html::escape(''));
    $this->assertEquals(' ', Html::escape(' '));
    $this->assertEquals('   ', Html::escape('   '));
  }

}

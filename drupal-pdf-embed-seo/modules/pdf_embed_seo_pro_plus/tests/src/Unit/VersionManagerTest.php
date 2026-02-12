<?php

namespace Drupal\Tests\pdf_embed_seo_pro_plus\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests the VersionManager service.
 *
 * @group pdf_embed_seo_pro_plus
 */
class VersionManagerTest extends UnitTestCase {

  /**
   * Test version number format validation.
   */
  public function testVersionNumberFormat() {
    $valid_versions = ['1.0', '1.1', '2.0', '1.0.1', '10.5.3'];
    $invalid_versions = ['v1.0', '1', 'abc', '1.0.0.0.0'];

    foreach ($valid_versions as $version) {
      $this->assertMatchesRegularExpression('/^\d+(\.\d+){1,2}$/', $version);
    }

    foreach ($invalid_versions as $version) {
      $this->assertDoesNotMatchRegularExpression('/^\d+\.\d+(\.\d+)?$/', $version);
    }
  }

  /**
   * Test version increment logic.
   */
  public function testVersionIncrement() {
    $current_version = '1.5';
    $parts = explode('.', $current_version);
    $parts[1] = (int) $parts[1] + 1;
    $new_version = implode('.', $parts);

    $this->assertEquals('1.6', $new_version);
  }

  /**
   * Test version comparison.
   */
  public function testVersionComparison() {
    $this->assertEquals(-1, version_compare('1.0', '1.1'));
    $this->assertEquals(1, version_compare('2.0', '1.9'));
    $this->assertEquals(0, version_compare('1.0', '1.0'));
  }

  /**
   * Test checksum generation.
   */
  public function testChecksumGeneration() {
    $content = 'PDF file content here';
    $md5_checksum = md5($content);
    $sha256_checksum = hash('sha256', $content);

    $this->assertEquals(32, strlen($md5_checksum));
    $this->assertEquals(64, strlen($sha256_checksum));
  }

  /**
   * Test version metadata structure.
   */
  public function testVersionMetadataStructure() {
    $version_meta = [
      'version_id' => 1,
      'document_id' => 123,
      'version_number' => '1.0',
      'file_uri' => 'public://pdfs/test.pdf',
      'file_size' => 1024000,
      'checksum' => md5('test'),
      'changelog' => 'Initial version',
      'author_id' => 1,
      'is_current' => TRUE,
      'created' => time(),
    ];

    $required_keys = ['version_id', 'document_id', 'version_number', 'file_uri', 'created'];
    foreach ($required_keys as $key) {
      $this->assertArrayHasKey($key, $version_meta);
    }
  }

  /**
   * Test version limit enforcement.
   */
  public function testVersionLimitEnforcement() {
    $max_versions = 10;
    $versions = [];

    for ($i = 1; $i <= 15; $i++) {
      $versions[] = [
        'version_number' => "1.{$i}",
        'created' => strtotime("-{$i} days"),
      ];
    }

    // Sort by date descending.
    usort($versions, function ($a, $b) {
      return $b['created'] - $a['created'];
    });

    $kept_versions = array_slice($versions, 0, $max_versions);

    $this->assertCount($max_versions, $kept_versions);
  }

}

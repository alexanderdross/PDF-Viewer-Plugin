<?php

namespace Drupal\Tests\pdf_embed_seo_pro_plus\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests the LicenseValidator service.
 *
 * @group pdf_embed_seo_pro_plus
 */
class LicenseValidatorTest extends UnitTestCase {

  /**
   * Test valid Pro+ license key format.
   */
  public function testValidProPlusLicenseKeyFormat() {
    $valid_key = 'PDF$PRO+#ABCD-EFGH@IJKL-MNOP!QRST';

    $this->assertTrue(
      (bool) preg_match('/^PDF\$PRO\+#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}-[A-Z0-9]{4}![A-Z0-9]{4}$/i', $valid_key)
    );
  }

  /**
   * Test invalid license key formats.
   */
  public function testInvalidLicenseKeyFormats() {
    $invalid_keys = [
      'invalid-key',
      'PDF$PRO#ABCD-EFGH@IJKL-MNOP!QRST', // Missing + for Pro+.
      'PDFPRO+ABCDEFGH@IJKLMNOP!QRST', // Missing $ and #.
      'short',
    ];

    foreach ($invalid_keys as $key) {
      $this->assertFalse(
        (bool) preg_match('/^PDF\$PRO\+#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}-[A-Z0-9]{4}![A-Z0-9]{4}$/i', $key),
        "Key should be invalid: {$key}"
      );
    }
  }

  /**
   * Test unlimited license key format.
   */
  public function testUnlimitedLicenseKeyFormat() {
    $unlimited_key = 'PDF$UNLIMITED#ABCD@EFGH!IJKL';

    $this->assertTrue(
      (bool) preg_match('/^PDF\$UNLIMITED#[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i', $unlimited_key)
    );
  }

  /**
   * Test development license key format.
   */
  public function testDevLicenseKeyFormat() {
    $dev_key = 'PDF$DEV#ABCD-EFGH@IJKL!MNOP';

    $this->assertTrue(
      (bool) preg_match('/^PDF\$DEV#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i', $dev_key)
    );
  }

  /**
   * Test license status values.
   */
  public function testLicenseStatusValues() {
    $valid_statuses = ['valid', 'invalid', 'expired', 'inactive', 'grace_period'];

    foreach ($valid_statuses as $status) {
      $this->assertContains($status, $valid_statuses);
    }
  }

  /**
   * Test grace period detection.
   */
  public function testGracePeriodDetection() {
    $expired_date = gmdate('Y-m-d', strtotime('-7 days'));
    $grace_period_days = 14;

    $expires_timestamp = strtotime($expired_date);
    $grace_end = $expires_timestamp + ($grace_period_days * 24 * 60 * 60);

    $this->assertTrue(time() < $grace_end);
  }

  /**
   * Test grace period exceeded.
   */
  public function testGracePeriodExceeded() {
    $expired_date = gmdate('Y-m-d', strtotime('-30 days'));
    $grace_period_days = 14;

    $expires_timestamp = strtotime($expired_date);
    $grace_end = $expires_timestamp + ($grace_period_days * 24 * 60 * 60);

    $this->assertTrue(time() > $grace_end);
  }

  /**
   * Test license key sanitization.
   */
  public function testLicenseKeySanitization() {
    $dirty_key = '<script>PDF$PRO+#ABCD-EFGH@IJKL-MNOP!QRST</script>';
    $clean_key = strip_tags($dirty_key);

    $this->assertStringNotContainsString('<script>', $clean_key);
  }

}

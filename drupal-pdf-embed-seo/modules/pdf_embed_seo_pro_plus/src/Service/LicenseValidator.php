<?php

namespace Drupal\pdf_embed_seo_pro_plus\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;

/**
 * License validation service for Pro+ Enterprise.
 */
class LicenseValidator {

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * State service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Valid license statuses.
   */
  const STATUS_VALID = 'valid';
  const STATUS_INVALID = 'invalid';
  const STATUS_EXPIRED = 'expired';
  const STATUS_GRACE_PERIOD = 'grace_period';
  const STATUS_INACTIVE = 'inactive';

  /**
   * Constructs a LicenseValidator object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, StateInterface $state) {
    $this->configFactory = $config_factory;
    $this->state = $state;
  }

  /**
   * Validate the Pro+ license key.
   *
   * @param string|null $license_key
   *   Optional license key to validate. Uses stored key if not provided.
   *
   * @return string
   *   The license status.
   */
  public function validate(?string $license_key = NULL): string {
    if ($license_key === NULL) {
      $config = $this->configFactory->get('pdf_embed_seo_pro_plus.settings');
      $license_key = $config->get('license_key') ?? '';
    }

    if (empty($license_key)) {
      return self::STATUS_INACTIVE;
    }

    // Validate Pro+ license format: PDF$PRO+#XXXX-XXXX@XXXX-XXXX!XXXX
    if (preg_match('/^PDF\$PRO\+#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}-[A-Z0-9]{4}![A-Z0-9]{4}$/i', $license_key)) {
      return $this->checkExpiration();
    }

    // Unlimited/test license: PDF$UNLIMITED#XXXX@XXXX!XXXX
    if (preg_match('/^PDF\$UNLIMITED#[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i', $license_key)) {
      return self::STATUS_VALID;
    }

    // Development license: PDF$DEV#XXXX-XXXX@XXXX!XXXX
    if (preg_match('/^PDF\$DEV#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i', $license_key)) {
      return self::STATUS_VALID;
    }

    return self::STATUS_INVALID;
  }

  /**
   * Check license expiration.
   *
   * @return string
   *   The license status based on expiration.
   */
  protected function checkExpiration(): string {
    $config = $this->configFactory->get('pdf_embed_seo_pro_plus.settings');
    $expires = $config->get('license_expires');

    if (empty($expires)) {
      return self::STATUS_VALID;
    }

    $expiry_time = strtotime($expires);
    $now = time();

    if ($expiry_time > $now) {
      return self::STATUS_VALID;
    }

    // 14-day grace period
    $grace_end = strtotime($expires . ' +14 days');
    if ($grace_end > $now) {
      return self::STATUS_GRACE_PERIOD;
    }

    return self::STATUS_EXPIRED;
  }

  /**
   * Check if the license is valid.
   *
   * @return bool
   *   TRUE if license is valid or in grace period.
   */
  public function isValid(): bool {
    $status = $this->validate();
    return in_array($status, [self::STATUS_VALID, self::STATUS_GRACE_PERIOD], TRUE);
  }

  /**
   * Get the current license status.
   *
   * @return string
   *   The license status.
   */
  public function getStatus(): string {
    return $this->validate();
  }

  /**
   * Activate a license key.
   *
   * @param string $license_key
   *   The license key to activate.
   *
   * @return array
   *   Result with 'success' and 'message' keys.
   */
  public function activate(string $license_key): array {
    $status = $this->validate($license_key);

    if ($status === self::STATUS_INVALID) {
      return [
        'success' => FALSE,
        'message' => 'Invalid license key format.',
      ];
    }

    // Store the license key
    $config = $this->configFactory->getEditable('pdf_embed_seo_pro_plus.settings');
    $config->set('license_key', $license_key);
    $config->set('license_status', $status);
    $config->set('license_activated', date('Y-m-d H:i:s'));
    $config->save();

    // Update state for quick checks
    $this->state->set('pdf_embed_seo_pro_plus.license_valid', TRUE);

    return [
      'success' => TRUE,
      'message' => 'License activated successfully.',
      'status' => $status,
    ];
  }

  /**
   * Deactivate the current license.
   *
   * @return array
   *   Result with 'success' and 'message' keys.
   */
  public function deactivate(): array {
    $config = $this->configFactory->getEditable('pdf_embed_seo_pro_plus.settings');
    $config->set('license_key', '');
    $config->set('license_status', self::STATUS_INACTIVE);
    $config->set('license_deactivated', date('Y-m-d H:i:s'));
    $config->save();

    $this->state->set('pdf_embed_seo_pro_plus.license_valid', FALSE);

    return [
      'success' => TRUE,
      'message' => 'License deactivated successfully.',
    ];
  }

  /**
   * Get license information.
   *
   * @return array
   *   License information array.
   */
  public function getInfo(): array {
    $config = $this->configFactory->get('pdf_embed_seo_pro_plus.settings');

    return [
      'key' => $this->maskLicenseKey($config->get('license_key') ?? ''),
      'status' => $this->validate(),
      'activated' => $config->get('license_activated'),
      'expires' => $config->get('license_expires'),
    ];
  }

  /**
   * Mask a license key for display.
   *
   * @param string $key
   *   The license key.
   *
   * @return string
   *   The masked key.
   */
  protected function maskLicenseKey(string $key): string {
    if (strlen($key) <= 10) {
      return str_repeat('*', strlen($key));
    }

    return substr($key, 0, 6) . str_repeat('*', strlen($key) - 10) . substr($key, -4);
  }

}

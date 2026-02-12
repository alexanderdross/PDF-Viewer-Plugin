<?php

namespace Drupal\pdf_embed_seo_pro_plus\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * GDPR/HIPAA compliance management service for Pro+ Enterprise.
 */
class ComplianceManager {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Compliance modes.
   */
  const MODE_GDPR = 'gdpr';
  const MODE_HIPAA = 'hipaa';
  const MODE_CCPA = 'ccpa';

  /**
   * Consent types.
   */
  const CONSENT_ANALYTICS = 'analytics';
  const CONSENT_FUNCTIONAL = 'functional';
  const CONSENT_MARKETING = 'marketing';

  /**
   * Constructs a ComplianceManager object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(
    Connection $database,
    ConfigFactoryInterface $config_factory,
    AccountProxyInterface $current_user
  ) {
    $this->database = $database;
    $this->configFactory = $config_factory;
    $this->currentUser = $current_user;
  }

  /**
   * Check if a compliance mode is enabled.
   *
   * @param string $mode
   *   The compliance mode.
   *
   * @return bool
   *   TRUE if enabled.
   */
  public function isModeEnabled(string $mode): bool {
    $config = $this->configFactory->get('pdf_embed_seo_pro_plus.settings');
    return (bool) $config->get($mode . '_mode');
  }

  /**
   * Record user consent.
   *
   * @param string $consent_type
   *   The type of consent.
   * @param bool $consented
   *   Whether consent was given.
   * @param string|null $session_id
   *   Optional session ID for anonymous users.
   * @param string $consent_text
   *   The consent text shown to the user.
   *
   * @return int|false
   *   The consent record ID or FALSE.
   */
  public function recordConsent(
    string $consent_type,
    bool $consented,
    ?string $session_id = NULL,
    string $consent_text = ''
  ) {
    $request = \Drupal::request();
    $ip_address = $request ? $request->getClientIp() : '';
    $user_agent = $request ? substr($request->headers->get('User-Agent', ''), 0, 500) : '';

    // Anonymize IP if GDPR mode is enabled
    if ($this->isModeEnabled(self::MODE_GDPR)) {
      $ip_address = $this->anonymizeIp($ip_address);
    }

    try {
      $id = $this->database->insert('pdf_consents')
        ->fields([
          'user_id' => $this->currentUser->id() ?: NULL,
          'session_id' => $session_id,
          'consent_type' => $consent_type,
          'consented' => $consented ? 1 : 0,
          'ip_address' => $ip_address,
          'user_agent' => $user_agent,
          'consent_text' => $consent_text,
          'created_at' => date('Y-m-d H:i:s'),
        ])
        ->execute();

      return $id;
    }
    catch (\Exception $e) {
      \Drupal::logger('pdf_embed_seo_pro_plus')->error('Failed to record consent: @message', [
        '@message' => $e->getMessage(),
      ]);
      return FALSE;
    }
  }

  /**
   * Check if user has given consent.
   *
   * @param string $consent_type
   *   The type of consent.
   * @param int|null $user_id
   *   Optional user ID (uses current user if not provided).
   * @param string|null $session_id
   *   Optional session ID for anonymous users.
   *
   * @return bool
   *   TRUE if consent was given.
   */
  public function hasConsent(string $consent_type, ?int $user_id = NULL, ?string $session_id = NULL): bool {
    $user_id = $user_id ?? $this->currentUser->id();

    try {
      $query = $this->database->select('pdf_consents', 'c')
        ->fields('c', ['consented'])
        ->condition('consent_type', $consent_type)
        ->isNull('withdrawn_at')
        ->orderBy('created_at', 'DESC')
        ->range(0, 1);

      if ($user_id) {
        $query->condition('user_id', $user_id);
      }
      elseif ($session_id) {
        $query->condition('session_id', $session_id);
      }
      else {
        return FALSE;
      }

      $result = $query->execute()->fetchField();
      return (bool) $result;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Withdraw consent.
   *
   * @param string $consent_type
   *   The type of consent.
   * @param int|null $user_id
   *   Optional user ID.
   * @param string|null $session_id
   *   Optional session ID.
   *
   * @return bool
   *   TRUE on success.
   */
  public function withdrawConsent(string $consent_type, ?int $user_id = NULL, ?string $session_id = NULL): bool {
    $user_id = $user_id ?? $this->currentUser->id();

    try {
      $query = $this->database->update('pdf_consents')
        ->fields(['withdrawn_at' => date('Y-m-d H:i:s')])
        ->condition('consent_type', $consent_type)
        ->isNull('withdrawn_at');

      if ($user_id) {
        $query->condition('user_id', $user_id);
      }
      elseif ($session_id) {
        $query->condition('session_id', $session_id);
      }
      else {
        return FALSE;
      }

      $query->execute();
      return TRUE;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Get all consent records for a user.
   *
   * @param int|null $user_id
   *   The user ID.
   *
   * @return array
   *   Array of consent records.
   */
  public function getConsentHistory(?int $user_id = NULL): array {
    $user_id = $user_id ?? $this->currentUser->id();

    if (!$user_id) {
      return [];
    }

    try {
      $query = $this->database->select('pdf_consents', 'c')
        ->fields('c')
        ->condition('user_id', $user_id)
        ->orderBy('created_at', 'DESC');

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
    catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Export user data (GDPR data portability).
   *
   * @param int $user_id
   *   The user ID.
   *
   * @return array
   *   All user data.
   */
  public function exportUserData(int $user_id): array {
    $data = [
      'user_id' => $user_id,
      'exported_at' => date('c'),
      'consents' => [],
      'analytics' => [],
      'annotations' => [],
      'audit_log' => [],
    ];

    try {
      // Consents
      $query = $this->database->select('pdf_consents', 'c')
        ->fields('c')
        ->condition('user_id', $user_id);
      $data['consents'] = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      // Analytics (if exists)
      if ($this->database->schema()->tableExists('pdf_embed_seo_analytics')) {
        $query = $this->database->select('pdf_embed_seo_analytics', 'a')
          ->fields('a')
          ->condition('user_id', $user_id);
        $data['analytics'] = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
      }

      // Annotations
      if ($this->database->schema()->tableExists('pdf_annotations')) {
        $query = $this->database->select('pdf_annotations', 'an')
          ->fields('an')
          ->condition('user_id', $user_id);
        $data['annotations'] = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
      }

      // Audit log
      if ($this->database->schema()->tableExists('pdf_audit_log')) {
        $query = $this->database->select('pdf_audit_log', 'al')
          ->fields('al')
          ->condition('user_id', $user_id);
        $data['audit_log'] = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('pdf_embed_seo_pro_plus')->error('Failed to export user data: @message', [
        '@message' => $e->getMessage(),
      ]);
    }

    return $data;
  }

  /**
   * Delete user data (GDPR right to erasure).
   *
   * @param int $user_id
   *   The user ID.
   *
   * @return array
   *   Deletion results.
   */
  public function deleteUserData(int $user_id): array {
    $results = [
      'user_id' => $user_id,
      'deleted_at' => date('c'),
      'consents_deleted' => 0,
      'analytics_deleted' => 0,
      'annotations_deleted' => 0,
      'audit_log_deleted' => 0,
    ];

    try {
      // Delete consents
      $results['consents_deleted'] = $this->database->delete('pdf_consents')
        ->condition('user_id', $user_id)
        ->execute();

      // Delete analytics
      if ($this->database->schema()->tableExists('pdf_embed_seo_analytics')) {
        $results['analytics_deleted'] = $this->database->delete('pdf_embed_seo_analytics')
          ->condition('user_id', $user_id)
          ->execute();
      }

      // Delete annotations
      if ($this->database->schema()->tableExists('pdf_annotations')) {
        $results['annotations_deleted'] = $this->database->delete('pdf_annotations')
          ->condition('user_id', $user_id)
          ->execute();
      }

      // Anonymize audit log (keep for compliance but remove PII)
      if ($this->database->schema()->tableExists('pdf_audit_log')) {
        $results['audit_log_deleted'] = $this->database->update('pdf_audit_log')
          ->fields([
            'user_id' => NULL,
            'ip_address' => 'DELETED',
            'user_agent' => 'DELETED',
          ])
          ->condition('user_id', $user_id)
          ->execute();
      }

      // Log the deletion
      if (\Drupal::hasService('pdf_embed_seo_pro_plus.audit_logger')) {
        \Drupal::service('pdf_embed_seo_pro_plus.audit_logger')->log('delete_data', NULL, [
          'target_user_id' => $user_id,
          'results' => $results,
        ]);
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('pdf_embed_seo_pro_plus')->error('Failed to delete user data: @message', [
        '@message' => $e->getMessage(),
      ]);
    }

    return $results;
  }

  /**
   * Anonymize an IP address.
   *
   * @param string $ip
   *   The IP address.
   *
   * @return string
   *   Anonymized IP.
   */
  public function anonymizeIp(string $ip): string {
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
      // Zero out last octet
      return preg_replace('/\.\d+$/', '.0', $ip);
    }

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
      // Zero out last segment
      return preg_replace('/:[^:]+$/', ':0000', $ip);
    }

    return $ip;
  }

  /**
   * Get data retention policy.
   *
   * @return array
   *   Data retention settings.
   */
  public function getRetentionPolicy(): array {
    $config = $this->configFactory->get('pdf_embed_seo_pro_plus.settings');

    return [
      'analytics_days' => $config->get('data_retention_days') ?? 365,
      'audit_log_days' => $config->get('audit_log_retention') ?? 730,
      'consents_days' => $config->get('consent_retention') ?? 1825, // 5 years
      'heatmaps_days' => $config->get('heatmap_retention') ?? 90,
    ];
  }

  /**
   * Apply data retention policy.
   *
   * @return array
   *   Cleanup results.
   */
  public function applyRetentionPolicy(): array {
    $policy = $this->getRetentionPolicy();
    $results = [];

    // Cleanup analytics
    if ($this->database->schema()->tableExists('pdf_embed_seo_analytics')) {
      $cutoff = date('Y-m-d H:i:s', strtotime("-{$policy['analytics_days']} days"));
      $results['analytics'] = $this->database->delete('pdf_embed_seo_analytics')
        ->condition('created_at', $cutoff, '<')
        ->execute();
    }

    // Cleanup heatmaps
    if ($this->database->schema()->tableExists('pdf_heatmaps')) {
      $cutoff = date('Y-m-d H:i:s', strtotime("-{$policy['heatmaps_days']} days"));
      $results['heatmaps'] = $this->database->delete('pdf_heatmaps')
        ->condition('created_at', $cutoff, '<')
        ->execute();
    }

    // Cleanup audit log
    if ($this->database->schema()->tableExists('pdf_audit_log')) {
      $cutoff = date('Y-m-d H:i:s', strtotime("-{$policy['audit_log_days']} days"));
      $results['audit_log'] = $this->database->delete('pdf_audit_log')
        ->condition('created_at', $cutoff, '<')
        ->execute();
    }

    return $results;
  }

  /**
   * Get HIPAA compliance status.
   *
   * @return array
   *   Compliance checklist.
   */
  public function getHipaaStatus(): array {
    $config = $this->configFactory->get('pdf_embed_seo_pro_plus.settings');

    return [
      'access_controls' => [
        'status' => (bool) $config->get('two_factor_enabled'),
        'description' => 'Two-factor authentication for document access',
      ],
      'audit_logging' => [
        'status' => (bool) $config->get('enable_audit_log'),
        'description' => 'Complete audit trail of document access',
      ],
      'encryption' => [
        'status' => TRUE, // Assuming HTTPS
        'description' => 'Data encrypted in transit',
      ],
      'access_termination' => [
        'status' => (bool) $config->get('ip_whitelist'),
        'description' => 'IP whitelisting for access control',
      ],
      'data_integrity' => [
        'status' => (bool) $config->get('enable_versioning'),
        'description' => 'Document versioning for integrity',
      ],
    ];
  }

  /**
   * Generate compliance report.
   *
   * @param string $mode
   *   Compliance mode (gdpr, hipaa, ccpa).
   *
   * @return array
   *   Compliance report data.
   */
  public function generateReport(string $mode): array {
    $report = [
      'mode' => $mode,
      'generated_at' => date('c'),
      'settings' => [],
      'statistics' => [],
    ];

    $config = $this->configFactory->get('pdf_embed_seo_pro_plus.settings');

    if ($mode === self::MODE_GDPR) {
      $report['settings'] = [
        'ip_anonymization' => (bool) $config->get('gdpr_mode'),
        'consent_required' => (bool) $config->get('require_consent'),
        'data_retention' => $config->get('data_retention_days'),
      ];

      // Get consent statistics
      try {
        $query = $this->database->select('pdf_consents', 'c');
        $query->addField('c', 'consent_type');
        $query->addExpression('SUM(CASE WHEN consented = 1 THEN 1 ELSE 0 END)', 'accepted');
        $query->addExpression('SUM(CASE WHEN consented = 0 THEN 1 ELSE 0 END)', 'declined');
        $query->addExpression('SUM(CASE WHEN withdrawn_at IS NOT NULL THEN 1 ELSE 0 END)', 'withdrawn');
        $query->groupBy('consent_type');
        $report['statistics']['consents'] = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
      }
      catch (\Exception $e) {
        $report['statistics']['consents'] = [];
      }
    }
    elseif ($mode === self::MODE_HIPAA) {
      $report['settings'] = $this->getHipaaStatus();
    }

    return $report;
  }

}

<?php

namespace Drupal\pdf_embed_seo_pro_plus\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Audit logging service for Pro+ Enterprise.
 */
class AuditLogger {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Audit actions.
   */
  const ACTION_VIEW = 'view';
  const ACTION_DOWNLOAD = 'download';
  const ACTION_PRINT = 'print';
  const ACTION_PASSWORD_ATTEMPT = 'password_attempt';
  const ACTION_PASSWORD_SUCCESS = 'password_success';
  const ACTION_PASSWORD_FAILED = 'password_failed';
  const ACTION_ANNOTATION_CREATE = 'annotation_create';
  const ACTION_ANNOTATION_UPDATE = 'annotation_update';
  const ACTION_ANNOTATION_DELETE = 'annotation_delete';
  const ACTION_VERSION_CREATE = 'version_create';
  const ACTION_VERSION_RESTORE = 'version_restore';
  const ACTION_VERSION_DELETE = 'version_delete';
  const ACTION_SETTINGS_CHANGE = 'settings_change';
  const ACTION_LOGIN_2FA = 'login_2fa';
  const ACTION_EXPORT_DATA = 'export_data';
  const ACTION_DELETE_DATA = 'delete_data';

  /**
   * Constructs an AuditLogger object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(
    Connection $database,
    AccountProxyInterface $current_user,
    RequestStack $request_stack
  ) {
    $this->database = $database;
    $this->currentUser = $current_user;
    $this->requestStack = $request_stack;
  }

  /**
   * Log an audit event.
   *
   * @param string $action
   *   The action being logged.
   * @param int|null $document_id
   *   Optional document ID.
   * @param array $details
   *   Additional details to log.
   *
   * @return int|false
   *   The log entry ID or FALSE on failure.
   */
  public function log(string $action, ?int $document_id = NULL, array $details = []) {
    $request = $this->requestStack->getCurrentRequest();

    $ip_address = $request ? $request->getClientIp() : '';
    $user_agent = $request ? substr($request->headers->get('User-Agent', ''), 0, 500) : '';

    // Anonymize IP if GDPR mode is enabled
    if ($this->isGdprEnabled()) {
      $ip_address = $this->anonymizeIp($ip_address);
    }

    try {
      $id = $this->database->insert('pdf_audit_log')
        ->fields([
          'document_id' => $document_id,
          'user_id' => $this->currentUser->id() ?: NULL,
          'action' => $action,
          'details' => json_encode($details),
          'ip_address' => $ip_address,
          'user_agent' => $user_agent,
          'created_at' => date('Y-m-d H:i:s'),
        ])
        ->execute();

      return $id;
    }
    catch (\Exception $e) {
      \Drupal::logger('pdf_embed_seo_pro_plus')->error('Failed to log audit event: @message', [
        '@message' => $e->getMessage(),
      ]);
      return FALSE;
    }
  }

  /**
   * Get audit log entries.
   *
   * @param array $filters
   *   Filter options.
   * @param int $limit
   *   Maximum entries to return.
   * @param int $offset
   *   Offset for pagination.
   *
   * @return array
   *   Array of log entries.
   */
  public function getEntries(array $filters = [], int $limit = 100, int $offset = 0): array {
    try {
      $query = $this->database->select('pdf_audit_log', 'a')
        ->fields('a')
        ->orderBy('created_at', 'DESC')
        ->range($offset, $limit);

      if (!empty($filters['document_id'])) {
        $query->condition('document_id', $filters['document_id']);
      }

      if (!empty($filters['user_id'])) {
        $query->condition('user_id', $filters['user_id']);
      }

      if (!empty($filters['action'])) {
        $query->condition('action', $filters['action']);
      }

      if (!empty($filters['actions']) && is_array($filters['actions'])) {
        $query->condition('action', $filters['actions'], 'IN');
      }

      if (!empty($filters['date_from'])) {
        $query->condition('created_at', $filters['date_from'], '>=');
      }

      if (!empty($filters['date_to'])) {
        $query->condition('created_at', $filters['date_to'], '<=');
      }

      $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      // Decode details
      foreach ($results as &$result) {
        if (!empty($result['details'])) {
          $result['details'] = json_decode($result['details'], TRUE);
        }
      }

      return $results;
    }
    catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Get audit log entry count.
   *
   * @param array $filters
   *   Filter options.
   *
   * @return int
   *   Total count.
   */
  public function getCount(array $filters = []): int {
    try {
      $query = $this->database->select('pdf_audit_log', 'a');
      $query->addExpression('COUNT(*)', 'count');

      if (!empty($filters['document_id'])) {
        $query->condition('document_id', $filters['document_id']);
      }

      if (!empty($filters['user_id'])) {
        $query->condition('user_id', $filters['user_id']);
      }

      if (!empty($filters['action'])) {
        $query->condition('action', $filters['action']);
      }

      return (int) $query->execute()->fetchField();
    }
    catch (\Exception $e) {
      return 0;
    }
  }

  /**
   * Get log entry by ID.
   *
   * @param int $id
   *   The log entry ID.
   *
   * @return array|null
   *   The log entry or NULL.
   */
  public function getEntry(int $id): ?array {
    try {
      $query = $this->database->select('pdf_audit_log', 'a')
        ->fields('a')
        ->condition('id', $id);

      $result = $query->execute()->fetchAssoc();

      if ($result && !empty($result['details'])) {
        $result['details'] = json_decode($result['details'], TRUE);
      }

      return $result ?: NULL;
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Delete old log entries.
   *
   * @param int $days
   *   Delete entries older than this many days.
   *
   * @return int
   *   Number of deleted entries.
   */
  public function cleanup(int $days = 365): int {
    $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

    try {
      return $this->database->delete('pdf_audit_log')
        ->condition('created_at', $cutoff, '<')
        ->execute();
    }
    catch (\Exception $e) {
      return 0;
    }
  }

  /**
   * Export audit log.
   *
   * @param array $filters
   *   Filter options.
   * @param string $format
   *   Export format (csv, json).
   *
   * @return string
   *   Exported data.
   */
  public function export(array $filters = [], string $format = 'json'): string {
    $entries = $this->getEntries($filters, 10000);

    if ($format === 'csv') {
      return $this->toCsv($entries);
    }

    return json_encode($entries, JSON_PRETTY_PRINT);
  }

  /**
   * Convert entries to CSV.
   *
   * @param array $entries
   *   Log entries.
   *
   * @return string
   *   CSV string.
   */
  protected function toCsv(array $entries): string {
    if (empty($entries)) {
      return '';
    }

    $output = fopen('php://temp', 'r+');

    // Header
    fputcsv($output, ['ID', 'Document ID', 'User ID', 'Action', 'Details', 'IP Address', 'User Agent', 'Created At']);

    foreach ($entries as $entry) {
      fputcsv($output, [
        $entry['id'],
        $entry['document_id'],
        $entry['user_id'],
        $entry['action'],
        is_array($entry['details']) ? json_encode($entry['details']) : $entry['details'],
        $entry['ip_address'],
        $entry['user_agent'],
        $entry['created_at'],
      ]);
    }

    rewind($output);
    $csv = stream_get_contents($output);
    fclose($output);

    return $csv;
  }

  /**
   * Get all valid actions.
   *
   * @return array
   *   Array of valid actions.
   */
  public function getActions(): array {
    return [
      self::ACTION_VIEW,
      self::ACTION_DOWNLOAD,
      self::ACTION_PRINT,
      self::ACTION_PASSWORD_ATTEMPT,
      self::ACTION_PASSWORD_SUCCESS,
      self::ACTION_PASSWORD_FAILED,
      self::ACTION_ANNOTATION_CREATE,
      self::ACTION_ANNOTATION_UPDATE,
      self::ACTION_ANNOTATION_DELETE,
      self::ACTION_VERSION_CREATE,
      self::ACTION_VERSION_RESTORE,
      self::ACTION_VERSION_DELETE,
      self::ACTION_SETTINGS_CHANGE,
      self::ACTION_LOGIN_2FA,
      self::ACTION_EXPORT_DATA,
      self::ACTION_DELETE_DATA,
    ];
  }

  /**
   * Check if GDPR mode is enabled.
   *
   * @return bool
   *   TRUE if GDPR mode is enabled.
   */
  protected function isGdprEnabled(): bool {
    $config = \Drupal::config('pdf_embed_seo_pro_plus.settings');
    return (bool) $config->get('gdpr_mode');
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
  protected function anonymizeIp(string $ip): string {
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
      return preg_replace('/\.\d+$/', '.0', $ip);
    }

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
      return preg_replace('/:[^:]+$/', ':0000', $ip);
    }

    return $ip;
  }

  /**
   * Delete all log entries for a user (GDPR request).
   *
   * @param int $user_id
   *   The user ID.
   *
   * @return int
   *   Number of deleted entries.
   */
  public function deleteUserData(int $user_id): int {
    try {
      return $this->database->delete('pdf_audit_log')
        ->condition('user_id', $user_id)
        ->execute();
    }
    catch (\Exception $e) {
      return 0;
    }
  }

}

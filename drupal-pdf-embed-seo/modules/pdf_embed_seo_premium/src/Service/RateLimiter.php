<?php

namespace Drupal\pdf_embed_seo_premium\Service;

use Drupal\Core\Database\Connection;
use Drupal\Component\Datetime\TimeInterface;
use Psr\Log\LoggerInterface;

/**
 * Rate limiting service for brute force protection.
 */
class RateLimiter {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Default rate limit configuration.
   *
   * @var array
   */
  protected $config = [
    'password_verify' => [
      'max_attempts' => 5,
      'window_seconds' => 300, // 5 minutes
      'block_seconds' => 900, // 15 minutes
    ],
    'default' => [
      'max_attempts' => 10,
      'window_seconds' => 60,
      'block_seconds' => 300,
    ],
  ];

  /**
   * Constructs a RateLimiter object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   */
  public function __construct(Connection $database, TimeInterface $time, LoggerInterface $logger) {
    $this->database = $database;
    $this->time = $time;
    $this->logger = $logger;
  }

  /**
   * Check if an action is rate limited.
   *
   * @param string $action
   *   The action being performed (e.g., 'password_verify').
   * @param string $ip_address
   *   The client IP address.
   * @param int $target_id
   *   The target entity ID (e.g., PDF document ID).
   *
   * @return array
   *   Array with 'allowed' (bool) and 'retry_after' (int seconds) keys.
   */
  public function checkLimit(string $action, string $ip_address, int $target_id = 0): array {
    if (!$this->database->schema()->tableExists('pdf_embed_seo_rate_limit')) {
      return ['allowed' => TRUE, 'retry_after' => 0];
    }

    $identifier = $this->createIdentifier($action, $ip_address, $target_id);
    $config = $this->config[$action] ?? $this->config['default'];
    $now = $this->time->getRequestTime();

    // Check for existing rate limit record.
    $record = $this->database->select('pdf_embed_seo_rate_limit', 'rl')
      ->fields('rl')
      ->condition('identifier', $identifier)
      ->execute()
      ->fetchAssoc();

    if (!$record) {
      return ['allowed' => TRUE, 'retry_after' => 0];
    }

    // Check if currently blocked.
    if ($record['blocked_until'] > $now) {
      $retry_after = $record['blocked_until'] - $now;
      return [
        'allowed' => FALSE,
        'retry_after' => $retry_after,
        'message' => "Too many attempts. Please try again in {$retry_after} seconds.",
      ];
    }

    // Check if window has expired (reset attempts).
    if ($record['window_start'] + $config['window_seconds'] < $now) {
      // Window expired, will be reset on next recordAttempt.
      return ['allowed' => TRUE, 'retry_after' => 0];
    }

    // Check if max attempts reached.
    if ($record['attempts'] >= $config['max_attempts']) {
      // Block the identifier.
      $block_until = $now + $config['block_seconds'];
      $this->database->update('pdf_embed_seo_rate_limit')
        ->fields(['blocked_until' => $block_until])
        ->condition('identifier', $identifier)
        ->execute();

      $this->logger->warning('Rate limit exceeded for @action from @ip on target @target', [
        '@action' => $action,
        '@ip' => $ip_address,
        '@target' => $target_id,
      ]);

      return [
        'allowed' => FALSE,
        'retry_after' => $config['block_seconds'],
        'message' => "Too many attempts. Please try again in {$config['block_seconds']} seconds.",
      ];
    }

    return ['allowed' => TRUE, 'retry_after' => 0];
  }

  /**
   * Record an attempt for rate limiting.
   *
   * @param string $action
   *   The action being performed.
   * @param string $ip_address
   *   The client IP address.
   * @param int $target_id
   *   The target entity ID.
   * @param bool $success
   *   Whether the attempt was successful (resets counter if TRUE).
   */
  public function recordAttempt(string $action, string $ip_address, int $target_id = 0, bool $success = FALSE): void {
    if (!$this->database->schema()->tableExists('pdf_embed_seo_rate_limit')) {
      return;
    }

    $identifier = $this->createIdentifier($action, $ip_address, $target_id);
    $config = $this->config[$action] ?? $this->config['default'];
    $now = $this->time->getRequestTime();

    // If successful, clear the rate limit record.
    if ($success) {
      $this->database->delete('pdf_embed_seo_rate_limit')
        ->condition('identifier', $identifier)
        ->execute();
      return;
    }

    // Check for existing record.
    $record = $this->database->select('pdf_embed_seo_rate_limit', 'rl')
      ->fields('rl')
      ->condition('identifier', $identifier)
      ->execute()
      ->fetchAssoc();

    if (!$record) {
      // Create new record.
      $this->database->insert('pdf_embed_seo_rate_limit')
        ->fields([
          'identifier' => $identifier,
          'action' => $action,
          'target_id' => $target_id,
          'ip_address' => $ip_address,
          'attempts' => 1,
          'window_start' => $now,
          'blocked_until' => 0,
        ])
        ->execute();
      return;
    }

    // Check if window has expired.
    if ($record['window_start'] + $config['window_seconds'] < $now) {
      // Reset the window.
      $this->database->update('pdf_embed_seo_rate_limit')
        ->fields([
          'attempts' => 1,
          'window_start' => $now,
          'blocked_until' => 0,
        ])
        ->condition('identifier', $identifier)
        ->execute();
      return;
    }

    // Increment attempts.
    $this->database->update('pdf_embed_seo_rate_limit')
      ->expression('attempts', 'attempts + 1')
      ->condition('identifier', $identifier)
      ->execute();
  }

  /**
   * Clean up expired rate limit records.
   *
   * @param int $older_than
   *   Remove records older than this many seconds.
   *
   * @return int
   *   Number of records deleted.
   */
  public function cleanup(int $older_than = 86400): int {
    if (!$this->database->schema()->tableExists('pdf_embed_seo_rate_limit')) {
      return 0;
    }

    $threshold = $this->time->getRequestTime() - $older_than;

    return $this->database->delete('pdf_embed_seo_rate_limit')
      ->condition('window_start', $threshold, '<')
      ->condition('blocked_until', $this->time->getRequestTime(), '<')
      ->execute();
  }

  /**
   * Create a unique identifier for rate limiting.
   *
   * @param string $action
   *   The action.
   * @param string $ip_address
   *   The IP address.
   * @param int $target_id
   *   The target ID.
   *
   * @return string
   *   A unique identifier.
   */
  protected function createIdentifier(string $action, string $ip_address, int $target_id): string {
    return hash('sha256', "{$action}:{$ip_address}:{$target_id}");
  }

}

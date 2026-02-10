<?php

namespace Drupal\pdf_embed_seo_premium\Service;

use Drupal\Core\Database\Connection;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Psr\Log\LoggerInterface;

/**
 * Service for managing PDF access tokens.
 *
 * This replaces the State API approach for better scalability
 * and automatic cleanup of expired tokens.
 */
class AccessTokenStorage {

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
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs an AccessTokenStorage object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   */
  public function __construct(Connection $database, TimeInterface $time, AccountProxyInterface $current_user, LoggerInterface $logger) {
    $this->database = $database;
    $this->time = $time;
    $this->currentUser = $current_user;
    $this->logger = $logger;
  }

  /**
   * Create a new access token.
   *
   * @param int $pdf_document_id
   *   The PDF document ID.
   * @param int $expires_in
   *   Number of seconds until expiration.
   * @param int $max_uses
   *   Maximum number of uses (0 = unlimited).
   *
   * @return array|null
   *   Token data array or NULL on failure.
   */
  public function createToken(int $pdf_document_id, int $expires_in = 86400, int $max_uses = 0): ?array {
    if (!$this->database->schema()->tableExists('pdf_embed_seo_access_tokens')) {
      $this->logger->error('Access tokens table does not exist.');
      return NULL;
    }

    $token = bin2hex(random_bytes(32));
    $now = $this->time->getRequestTime();
    $expires = $now + $expires_in;

    try {
      $this->database->insert('pdf_embed_seo_access_tokens')
        ->fields([
          'token' => $token,
          'pdf_document_id' => $pdf_document_id,
          'created_by' => $this->currentUser->id(),
          'expires' => $expires,
          'max_uses' => $max_uses,
          'use_count' => 0,
          'created' => $now,
        ])
        ->execute();

      return [
        'token' => $token,
        'pdf_id' => $pdf_document_id,
        'expires' => $expires,
        'expires_in' => $expires_in,
        'max_uses' => $max_uses,
        'use_count' => 0,
        'created' => $now,
        'created_by' => $this->currentUser->id(),
      ];
    }
    catch (\Exception $e) {
      $this->logger->error('Failed to create access token: @message', [
        '@message' => $e->getMessage(),
      ]);
      return NULL;
    }
  }

  /**
   * Validate and use a token.
   *
   * @param string $token
   *   The access token.
   * @param int $pdf_document_id
   *   The expected PDF document ID.
   *
   * @return array
   *   Array with 'valid' (bool), 'message' (string), and optionally 'data' (array).
   */
  public function validateToken(string $token, int $pdf_document_id): array {
    if (!$this->database->schema()->tableExists('pdf_embed_seo_access_tokens')) {
      return [
        'valid' => FALSE,
        'message' => 'Token storage not available.',
      ];
    }

    $now = $this->time->getRequestTime();

    // Fetch the token.
    $record = $this->database->select('pdf_embed_seo_access_tokens', 't')
      ->fields('t')
      ->condition('token', $token)
      ->execute()
      ->fetchAssoc();

    if (!$record) {
      return [
        'valid' => FALSE,
        'message' => 'Invalid or expired token.',
      ];
    }

    // Check if token matches the document.
    if ((int) $record['pdf_document_id'] !== $pdf_document_id) {
      return [
        'valid' => FALSE,
        'message' => 'Token does not match this document.',
      ];
    }

    // Check expiration.
    if ((int) $record['expires'] < $now) {
      // Delete expired token.
      $this->deleteToken($token);
      return [
        'valid' => FALSE,
        'message' => 'Token has expired.',
      ];
    }

    // Check max uses.
    if ((int) $record['max_uses'] > 0 && (int) $record['use_count'] >= (int) $record['max_uses']) {
      // Delete exhausted token.
      $this->deleteToken($token);
      return [
        'valid' => FALSE,
        'message' => 'Token has reached maximum uses.',
      ];
    }

    // Increment use count.
    $this->database->update('pdf_embed_seo_access_tokens')
      ->expression('use_count', 'use_count + 1')
      ->condition('token', $token)
      ->execute();

    return [
      'valid' => TRUE,
      'message' => 'Token is valid.',
      'data' => [
        'pdf_id' => (int) $record['pdf_document_id'],
        'expires' => (int) $record['expires'],
        'remaining_uses' => $record['max_uses'] > 0 ? (int) $record['max_uses'] - (int) $record['use_count'] - 1 : NULL,
      ],
    ];
  }

  /**
   * Get token information without using it.
   *
   * @param string $token
   *   The access token.
   *
   * @return array|null
   *   Token data or NULL if not found.
   */
  public function getToken(string $token): ?array {
    if (!$this->database->schema()->tableExists('pdf_embed_seo_access_tokens')) {
      return NULL;
    }

    $record = $this->database->select('pdf_embed_seo_access_tokens', 't')
      ->fields('t')
      ->condition('token', $token)
      ->execute()
      ->fetchAssoc();

    if (!$record) {
      return NULL;
    }

    return [
      'token' => $record['token'],
      'pdf_id' => (int) $record['pdf_document_id'],
      'expires' => (int) $record['expires'],
      'max_uses' => (int) $record['max_uses'],
      'use_count' => (int) $record['use_count'],
      'created' => (int) $record['created'],
      'created_by' => (int) $record['created_by'],
    ];
  }

  /**
   * Delete a token.
   *
   * @param string $token
   *   The access token.
   *
   * @return bool
   *   TRUE if deleted, FALSE otherwise.
   */
  public function deleteToken(string $token): bool {
    if (!$this->database->schema()->tableExists('pdf_embed_seo_access_tokens')) {
      return FALSE;
    }

    $deleted = $this->database->delete('pdf_embed_seo_access_tokens')
      ->condition('token', $token)
      ->execute();

    return $deleted > 0;
  }

  /**
   * Clean up expired tokens.
   *
   * @return int
   *   Number of tokens deleted.
   */
  public function cleanupExpired(): int {
    if (!$this->database->schema()->tableExists('pdf_embed_seo_access_tokens')) {
      return 0;
    }

    $now = $this->time->getRequestTime();

    // Delete expired tokens.
    $expired = $this->database->delete('pdf_embed_seo_access_tokens')
      ->condition('expires', $now, '<')
      ->execute();

    // Delete exhausted tokens (max_uses > 0 AND use_count >= max_uses).
    $exhausted = $this->database->delete('pdf_embed_seo_access_tokens')
      ->condition('max_uses', 0, '>')
      ->where('use_count >= max_uses')
      ->execute();

    $total = $expired + $exhausted;

    if ($total > 0) {
      $this->logger->info('Cleaned up @count expired/exhausted access tokens.', [
        '@count' => $total,
      ]);
    }

    return $total;
  }

  /**
   * Get all tokens for a document.
   *
   * @param int $pdf_document_id
   *   The PDF document ID.
   *
   * @return array
   *   Array of token data.
   */
  public function getTokensForDocument(int $pdf_document_id): array {
    if (!$this->database->schema()->tableExists('pdf_embed_seo_access_tokens')) {
      return [];
    }

    $result = $this->database->select('pdf_embed_seo_access_tokens', 't')
      ->fields('t')
      ->condition('pdf_document_id', $pdf_document_id)
      ->orderBy('created', 'DESC')
      ->execute();

    $tokens = [];
    foreach ($result as $record) {
      $tokens[] = [
        'token' => $record->token,
        'expires' => (int) $record->expires,
        'max_uses' => (int) $record->max_uses,
        'use_count' => (int) $record->use_count,
        'created' => (int) $record->created,
        'created_by' => (int) $record->created_by,
      ];
    }

    return $tokens;
  }

}

<?php

namespace Drupal\pdf_embed_seo_pro_plus\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Password\PasswordInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Two-factor authentication service for Pro+ Enterprise.
 */
class TwoFactorAuth {

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
   * Password service.
   *
   * @var \Drupal\Core\Password\PasswordInterface
   */
  protected $password;

  /**
   * 2FA methods.
   */
  const METHOD_EMAIL = 'email';
  const METHOD_SMS = 'sms';
  const METHOD_TOTP = 'totp';

  /**
   * Token expiration in seconds.
   */
  const TOKEN_EXPIRATION = 600; // 10 minutes

  /**
   * Constructs a TwoFactorAuth object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Password\PasswordInterface $password
   *   The password service.
   */
  public function __construct(
    Connection $database,
    AccountProxyInterface $current_user,
    PasswordInterface $password
  ) {
    $this->database = $database;
    $this->currentUser = $current_user;
    $this->password = $password;
  }

  /**
   * Generate a 2FA token for a user.
   *
   * @param int $user_id
   *   The user ID.
   * @param string $method
   *   The 2FA method.
   *
   * @return string|false
   *   The generated token or FALSE on failure.
   */
  public function generateToken(int $user_id, string $method = self::METHOD_EMAIL) {
    // Generate a 6-digit token
    $token = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // Hash the token for storage
    $hashed_token = $this->password->hash($token);

    try {
      // Invalidate any existing tokens
      $this->database->update('pdf_2fa_tokens')
        ->fields(['used_at' => date('Y-m-d H:i:s')])
        ->condition('user_id', $user_id)
        ->isNull('used_at')
        ->execute();

      // Insert new token
      $this->database->insert('pdf_2fa_tokens')
        ->fields([
          'user_id' => $user_id,
          'token' => $hashed_token,
          'method' => $method,
          'expires_at' => date('Y-m-d H:i:s', time() + self::TOKEN_EXPIRATION),
          'created_at' => date('Y-m-d H:i:s'),
        ])
        ->execute();

      return $token;
    }
    catch (\Exception $e) {
      \Drupal::logger('pdf_embed_seo_pro_plus')->error('Failed to generate 2FA token: @message', [
        '@message' => $e->getMessage(),
      ]);
      return FALSE;
    }
  }

  /**
   * Verify a 2FA token.
   *
   * @param int $user_id
   *   The user ID.
   * @param string $token
   *   The token to verify.
   *
   * @return bool
   *   TRUE if valid.
   */
  public function verifyToken(int $user_id, string $token): bool {
    try {
      // Get the latest unused token
      $query = $this->database->select('pdf_2fa_tokens', 't')
        ->fields('t', ['id', 'token', 'expires_at'])
        ->condition('user_id', $user_id)
        ->isNull('used_at')
        ->condition('expires_at', date('Y-m-d H:i:s'), '>')
        ->orderBy('created_at', 'DESC')
        ->range(0, 1);

      $result = $query->execute()->fetchAssoc();

      if (!$result) {
        return FALSE;
      }

      // Verify the token
      if ($this->password->check($token, $result['token'])) {
        // Mark as used
        $this->database->update('pdf_2fa_tokens')
          ->fields(['used_at' => date('Y-m-d H:i:s')])
          ->condition('id', $result['id'])
          ->execute();

        // Log successful 2FA
        if (\Drupal::hasService('pdf_embed_seo_pro_plus.audit_logger')) {
          \Drupal::service('pdf_embed_seo_pro_plus.audit_logger')->log('login_2fa', NULL, [
            'user_id' => $user_id,
            'success' => TRUE,
          ]);
        }

        return TRUE;
      }

      return FALSE;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Send 2FA token via email.
   *
   * @param int $user_id
   *   The user ID.
   *
   * @return bool
   *   TRUE if sent successfully.
   */
  public function sendEmailToken(int $user_id): bool {
    $token = $this->generateToken($user_id, self::METHOD_EMAIL);
    if (!$token) {
      return FALSE;
    }

    try {
      $user = \Drupal::entityTypeManager()->getStorage('user')->load($user_id);
      if (!$user) {
        return FALSE;
      }

      $mail_manager = \Drupal::service('plugin.manager.mail');
      $result = $mail_manager->mail(
        'pdf_embed_seo_pro_plus',
        '2fa_token',
        $user->getEmail(),
        $user->getPreferredLangcode(),
        [
          'token' => $token,
          'user' => $user,
          'expires_in' => self::TOKEN_EXPIRATION / 60,
        ]
      );

      return (bool) $result['result'];
    }
    catch (\Exception $e) {
      \Drupal::logger('pdf_embed_seo_pro_plus')->error('Failed to send 2FA email: @message', [
        '@message' => $e->getMessage(),
      ]);
      return FALSE;
    }
  }

  /**
   * Check if 2FA is required for a document.
   *
   * @param int $document_id
   *   The document ID.
   *
   * @return bool
   *   TRUE if 2FA is required.
   */
  public function isRequiredForDocument(int $document_id): bool {
    $config = \Drupal::config('pdf_embed_seo_pro_plus.settings');

    // Check global setting
    if (!$config->get('two_factor_enabled')) {
      return FALSE;
    }

    // Check document-specific setting
    try {
      $document = \Drupal::entityTypeManager()->getStorage('pdf_document')->load($document_id);
      if ($document && $document->hasField('require_2fa')) {
        return (bool) $document->get('require_2fa')->value;
      }
    }
    catch (\Exception $e) {
      // Ignore and use global setting
    }

    return (bool) $config->get('two_factor_required_all');
  }

  /**
   * Check if user has passed 2FA for a document.
   *
   * @param int $user_id
   *   The user ID.
   * @param int $document_id
   *   The document ID.
   *
   * @return bool
   *   TRUE if passed.
   */
  public function hasPassedForDocument(int $user_id, int $document_id): bool {
    $session_key = "pdf_2fa_passed_{$document_id}";

    // Check session
    $session = \Drupal::request()->getSession();
    if ($session && $session->has($session_key)) {
      $passed_at = $session->get($session_key);
      // Token valid for 24 hours
      if (time() - $passed_at < 86400) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Mark 2FA as passed for a document.
   *
   * @param int $user_id
   *   The user ID.
   * @param int $document_id
   *   The document ID.
   */
  public function markPassedForDocument(int $user_id, int $document_id): void {
    $session_key = "pdf_2fa_passed_{$document_id}";
    $session = \Drupal::request()->getSession();
    if ($session) {
      $session->set($session_key, time());
    }
  }

  /**
   * Cleanup expired tokens.
   *
   * @return int
   *   Number of deleted tokens.
   */
  public function cleanup(): int {
    try {
      return $this->database->delete('pdf_2fa_tokens')
        ->condition('expires_at', date('Y-m-d H:i:s'), '<')
        ->execute();
    }
    catch (\Exception $e) {
      return 0;
    }
  }

  /**
   * Generate TOTP secret for a user.
   *
   * @param int $user_id
   *   The user ID.
   *
   * @return string
   *   Base32 encoded secret.
   */
  public function generateTotpSecret(int $user_id): string {
    // Generate random bytes and encode as base32
    $secret = '';
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    for ($i = 0; $i < 16; $i++) {
      $secret .= $chars[random_int(0, 31)];
    }

    // Store the secret (encrypted in production)
    try {
      $this->database->merge('pdf_2fa_secrets')
        ->key(['user_id' => $user_id])
        ->fields([
          'secret' => $secret,
          'updated_at' => date('Y-m-d H:i:s'),
        ])
        ->execute();
    }
    catch (\Exception $e) {
      // Table might not exist, ignore
    }

    return $secret;
  }

  /**
   * Verify TOTP code.
   *
   * @param int $user_id
   *   The user ID.
   * @param string $code
   *   The TOTP code.
   *
   * @return bool
   *   TRUE if valid.
   */
  public function verifyTotp(int $user_id, string $code): bool {
    try {
      $query = $this->database->select('pdf_2fa_secrets', 's')
        ->fields('s', ['secret'])
        ->condition('user_id', $user_id);

      $secret = $query->execute()->fetchField();
      if (!$secret) {
        return FALSE;
      }

      // Verify TOTP (allow 1 time step drift)
      for ($drift = -1; $drift <= 1; $drift++) {
        $expected = $this->generateTotp($secret, $drift);
        if (hash_equals($expected, $code)) {
          return TRUE;
        }
      }

      return FALSE;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Generate TOTP code.
   *
   * @param string $secret
   *   Base32 encoded secret.
   * @param int $drift
   *   Time step drift.
   *
   * @return string
   *   6-digit TOTP code.
   */
  protected function generateTotp(string $secret, int $drift = 0): string {
    $timestamp = floor(time() / 30) + $drift;
    $binary_secret = $this->base32Decode($secret);

    $time = pack('N*', 0) . pack('N*', $timestamp);
    $hash = hash_hmac('sha1', $time, $binary_secret, TRUE);
    $offset = ord(substr($hash, -1)) & 0x0F;
    $code = (
      ((ord($hash[$offset]) & 0x7F) << 24) |
      ((ord($hash[$offset + 1]) & 0xFF) << 16) |
      ((ord($hash[$offset + 2]) & 0xFF) << 8) |
      (ord($hash[$offset + 3]) & 0xFF)
    ) % 1000000;

    return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
  }

  /**
   * Decode base32 string.
   *
   * @param string $input
   *   Base32 encoded string.
   *
   * @return string
   *   Decoded binary string.
   */
  protected function base32Decode(string $input): string {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $input = strtoupper($input);
    $binary = '';

    foreach (str_split($input) as $char) {
      $binary .= str_pad(decbin(strpos($chars, $char)), 5, '0', STR_PAD_LEFT);
    }

    $output = '';
    foreach (str_split($binary, 8) as $byte) {
      if (strlen($byte) === 8) {
        $output .= chr(bindec($byte));
      }
    }

    return $output;
  }

}

<?php

namespace Drupal\pdf_embed_seo_pro_plus\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Webhook dispatcher service for Pro+ Enterprise.
 */
class WebhookDispatcher {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Webhook events.
   */
  const EVENT_DOCUMENT_CREATED = 'document.created';
  const EVENT_DOCUMENT_UPDATED = 'document.updated';
  const EVENT_DOCUMENT_DELETED = 'document.deleted';
  const EVENT_DOCUMENT_VIEWED = 'document.viewed';
  const EVENT_DOCUMENT_DOWNLOADED = 'document.downloaded';
  const EVENT_PASSWORD_ATTEMPT = 'password.attempt';
  const EVENT_ANNOTATION_CREATED = 'annotation.created';
  const EVENT_VERSION_CREATED = 'version.created';
  const EVENT_CONSENT_GIVEN = 'consent.given';
  const EVENT_DATA_EXPORTED = 'data.exported';

  /**
   * Constructs a WebhookDispatcher object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   */
  public function __construct(
    Connection $database,
    ClientInterface $http_client,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->database = $database;
    $this->httpClient = $http_client;
    $this->logger = $logger_factory->get('pdf_embed_seo_pro_plus');
  }

  /**
   * Create a new webhook.
   *
   * @param string $name
   *   Webhook name.
   * @param string $url
   *   Webhook URL.
   * @param array $events
   *   Events to subscribe to.
   * @param string|null $secret
   *   Optional secret for signing.
   *
   * @return int|false
   *   The webhook ID or FALSE on failure.
   */
  public function create(string $name, string $url, array $events, ?string $secret = NULL) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
      return FALSE;
    }

    // Generate secret if not provided
    if (empty($secret)) {
      $secret = bin2hex(random_bytes(32));
    }

    try {
      $id = $this->database->insert('pdf_webhooks')
        ->fields([
          'name' => $name,
          'url' => $url,
          'secret' => $secret,
          'events' => json_encode($events),
          'is_active' => 1,
          'failure_count' => 0,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ])
        ->execute();

      return $id;
    }
    catch (\Exception $e) {
      $this->logger->error('Failed to create webhook: @message', [
        '@message' => $e->getMessage(),
      ]);
      return FALSE;
    }
  }

  /**
   * Get a webhook by ID.
   *
   * @param int $id
   *   The webhook ID.
   *
   * @return array|null
   *   The webhook record or NULL.
   */
  public function get(int $id): ?array {
    try {
      $query = $this->database->select('pdf_webhooks', 'w')
        ->fields('w')
        ->condition('id', $id);

      $result = $query->execute()->fetchAssoc();

      if ($result) {
        $result['events'] = json_decode($result['events'], TRUE);
      }

      return $result ?: NULL;
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Get all webhooks.
   *
   * @param bool $active_only
   *   Whether to return only active webhooks.
   *
   * @return array
   *   Array of webhook records.
   */
  public function getAll(bool $active_only = FALSE): array {
    try {
      $query = $this->database->select('pdf_webhooks', 'w')
        ->fields('w')
        ->orderBy('name', 'ASC');

      if ($active_only) {
        $query->condition('is_active', 1);
      }

      $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      foreach ($results as &$result) {
        $result['events'] = json_decode($result['events'], TRUE);
      }

      return $results;
    }
    catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Update a webhook.
   *
   * @param int $id
   *   The webhook ID.
   * @param array $data
   *   Updated data.
   *
   * @return bool
   *   TRUE on success, FALSE on failure.
   */
  public function update(int $id, array $data): bool {
    $fields = ['updated_at' => date('Y-m-d H:i:s')];

    if (isset($data['name'])) {
      $fields['name'] = $data['name'];
    }

    if (isset($data['url'])) {
      if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
        return FALSE;
      }
      $fields['url'] = $data['url'];
    }

    if (isset($data['events'])) {
      $fields['events'] = json_encode($data['events']);
    }

    if (isset($data['secret'])) {
      $fields['secret'] = $data['secret'];
    }

    if (isset($data['is_active'])) {
      $fields['is_active'] = $data['is_active'] ? 1 : 0;
    }

    try {
      $this->database->update('pdf_webhooks')
        ->fields($fields)
        ->condition('id', $id)
        ->execute();

      return TRUE;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Delete a webhook.
   *
   * @param int $id
   *   The webhook ID.
   *
   * @return bool
   *   TRUE on success, FALSE on failure.
   */
  public function delete(int $id): bool {
    try {
      $this->database->delete('pdf_webhooks')
        ->condition('id', $id)
        ->execute();

      // Also delete deliveries
      $this->database->delete('pdf_webhook_deliveries')
        ->condition('webhook_id', $id)
        ->execute();

      return TRUE;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Dispatch an event to all subscribed webhooks.
   *
   * @param string $event
   *   The event type.
   * @param array $payload
   *   The event payload.
   */
  public function dispatch(string $event, array $payload): void {
    $webhooks = $this->getSubscribedWebhooks($event);

    foreach ($webhooks as $webhook) {
      $this->send($webhook, $event, $payload);
    }
  }

  /**
   * Get webhooks subscribed to an event.
   *
   * @param string $event
   *   The event type.
   *
   * @return array
   *   Array of webhooks.
   */
  protected function getSubscribedWebhooks(string $event): array {
    $all_webhooks = $this->getAll(TRUE);

    return array_filter($all_webhooks, function ($webhook) use ($event) {
      return in_array($event, $webhook['events'], TRUE) || in_array('*', $webhook['events'], TRUE);
    });
  }

  /**
   * Send a webhook request.
   *
   * @param array $webhook
   *   The webhook configuration.
   * @param string $event
   *   The event type.
   * @param array $payload
   *   The event payload.
   *
   * @return bool
   *   TRUE on success, FALSE on failure.
   */
  protected function send(array $webhook, string $event, array $payload): bool {
    $full_payload = [
      'event' => $event,
      'timestamp' => date('c'),
      'data' => $payload,
    ];

    $json_payload = json_encode($full_payload);
    $signature = $this->generateSignature($json_payload, $webhook['secret']);

    $start_time = microtime(TRUE);
    $delivery_id = $this->createDelivery($webhook['id'], $event, $json_payload);

    try {
      $response = $this->httpClient->request('POST', $webhook['url'], [
        'headers' => [
          'Content-Type' => 'application/json',
          'X-Webhook-Event' => $event,
          'X-Webhook-Signature' => $signature,
          'X-Webhook-Timestamp' => time(),
        ],
        'body' => $json_payload,
        'timeout' => 30,
      ]);

      $duration_ms = (int) ((microtime(TRUE) - $start_time) * 1000);
      $status_code = $response->getStatusCode();

      $this->updateDelivery($delivery_id, [
        'response_code' => $status_code,
        'response_body' => substr((string) $response->getBody(), 0, 1000),
        'duration_ms' => $duration_ms,
        'status' => ($status_code >= 200 && $status_code < 300) ? 'success' : 'failed',
      ]);

      if ($status_code >= 200 && $status_code < 300) {
        $this->resetFailureCount($webhook['id']);
        return TRUE;
      }
      else {
        $this->incrementFailureCount($webhook['id']);
        return FALSE;
      }
    }
    catch (GuzzleException $e) {
      $duration_ms = (int) ((microtime(TRUE) - $start_time) * 1000);

      $this->updateDelivery($delivery_id, [
        'response_code' => 0,
        'response_body' => $e->getMessage(),
        'duration_ms' => $duration_ms,
        'status' => 'failed',
      ]);

      $this->incrementFailureCount($webhook['id']);
      $this->logger->warning('Webhook delivery failed: @message', [
        '@message' => $e->getMessage(),
      ]);

      return FALSE;
    }
  }

  /**
   * Generate webhook signature.
   *
   * @param string $payload
   *   The JSON payload.
   * @param string $secret
   *   The webhook secret.
   *
   * @return string
   *   The signature.
   */
  public function generateSignature(string $payload, string $secret): string {
    return 'sha256=' . hash_hmac('sha256', $payload, $secret);
  }

  /**
   * Verify webhook signature.
   *
   * @param string $payload
   *   The JSON payload.
   * @param string $signature
   *   The received signature.
   * @param string $secret
   *   The webhook secret.
   *
   * @return bool
   *   TRUE if valid.
   */
  public function verifySignature(string $payload, string $signature, string $secret): bool {
    $expected = $this->generateSignature($payload, $secret);
    return hash_equals($expected, $signature);
  }

  /**
   * Create a delivery record.
   *
   * @param int $webhook_id
   *   The webhook ID.
   * @param string $event
   *   The event type.
   * @param string $payload
   *   The JSON payload.
   *
   * @return int|false
   *   The delivery ID or FALSE.
   */
  protected function createDelivery(int $webhook_id, string $event, string $payload) {
    try {
      return $this->database->insert('pdf_webhook_deliveries')
        ->fields([
          'webhook_id' => $webhook_id,
          'event' => $event,
          'payload' => $payload,
          'status' => 'pending',
          'created_at' => date('Y-m-d H:i:s'),
        ])
        ->execute();
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Update a delivery record.
   *
   * @param int $delivery_id
   *   The delivery ID.
   * @param array $data
   *   Updated data.
   */
  protected function updateDelivery(int $delivery_id, array $data): void {
    try {
      $this->database->update('pdf_webhook_deliveries')
        ->fields($data)
        ->condition('id', $delivery_id)
        ->execute();

      // Also update webhook last triggered
      $this->database->update('pdf_webhooks')
        ->fields([
          'last_triggered' => date('Y-m-d H:i:s'),
          'last_status' => $data['status'],
        ])
        ->condition('id', $this->getDeliveryWebhookId($delivery_id))
        ->execute();
    }
    catch (\Exception $e) {
      // Ignore update errors
    }
  }

  /**
   * Get webhook ID from delivery.
   *
   * @param int $delivery_id
   *   The delivery ID.
   *
   * @return int|null
   *   The webhook ID or NULL.
   */
  protected function getDeliveryWebhookId(int $delivery_id): ?int {
    try {
      $query = $this->database->select('pdf_webhook_deliveries', 'd')
        ->fields('d', ['webhook_id'])
        ->condition('id', $delivery_id);

      return (int) $query->execute()->fetchField();
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Reset failure count for a webhook.
   *
   * @param int $webhook_id
   *   The webhook ID.
   */
  protected function resetFailureCount(int $webhook_id): void {
    try {
      $this->database->update('pdf_webhooks')
        ->fields(['failure_count' => 0])
        ->condition('id', $webhook_id)
        ->execute();
    }
    catch (\Exception $e) {
      // Ignore errors
    }
  }

  /**
   * Increment failure count for a webhook.
   *
   * @param int $webhook_id
   *   The webhook ID.
   */
  protected function incrementFailureCount(int $webhook_id): void {
    try {
      $this->database->update('pdf_webhooks')
        ->expression('failure_count', 'failure_count + 1')
        ->condition('id', $webhook_id)
        ->execute();

      // Disable webhook after 10 consecutive failures
      $webhook = $this->get($webhook_id);
      if ($webhook && $webhook['failure_count'] >= 10) {
        $this->update($webhook_id, ['is_active' => FALSE]);
        $this->logger->warning('Webhook @name disabled after 10 consecutive failures.', [
          '@name' => $webhook['name'],
        ]);
      }
    }
    catch (\Exception $e) {
      // Ignore errors
    }
  }

  /**
   * Get deliveries for a webhook.
   *
   * @param int $webhook_id
   *   The webhook ID.
   * @param int $limit
   *   Maximum entries.
   *
   * @return array
   *   Array of deliveries.
   */
  public function getDeliveries(int $webhook_id, int $limit = 50): array {
    try {
      $query = $this->database->select('pdf_webhook_deliveries', 'd')
        ->fields('d')
        ->condition('webhook_id', $webhook_id)
        ->orderBy('created_at', 'DESC')
        ->range(0, $limit);

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
    catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Get all valid events.
   *
   * @return array
   *   Array of valid events.
   */
  public function getEvents(): array {
    return [
      self::EVENT_DOCUMENT_CREATED,
      self::EVENT_DOCUMENT_UPDATED,
      self::EVENT_DOCUMENT_DELETED,
      self::EVENT_DOCUMENT_VIEWED,
      self::EVENT_DOCUMENT_DOWNLOADED,
      self::EVENT_PASSWORD_ATTEMPT,
      self::EVENT_ANNOTATION_CREATED,
      self::EVENT_VERSION_CREATED,
      self::EVENT_CONSENT_GIVEN,
      self::EVENT_DATA_EXPORTED,
    ];
  }

  /**
   * Test a webhook.
   *
   * @param int $webhook_id
   *   The webhook ID.
   *
   * @return array
   *   Test result.
   */
  public function test(int $webhook_id): array {
    $webhook = $this->get($webhook_id);
    if (!$webhook) {
      return ['success' => FALSE, 'message' => 'Webhook not found.'];
    }

    $payload = [
      'test' => TRUE,
      'message' => 'This is a test webhook from PDF Embed SEO Pro+.',
    ];

    $success = $this->send($webhook, 'test', $payload);

    return [
      'success' => $success,
      'message' => $success ? 'Test webhook sent successfully.' : 'Test webhook failed.',
    ];
  }

}

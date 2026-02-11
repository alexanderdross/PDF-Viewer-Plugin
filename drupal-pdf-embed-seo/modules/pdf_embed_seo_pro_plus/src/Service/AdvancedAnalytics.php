<?php

namespace Drupal\pdf_embed_seo_pro_plus\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;

/**
 * Advanced analytics service for Pro+ Enterprise.
 */
class AdvancedAnalytics {

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
   * Constructs an AdvancedAnalytics object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(Connection $database, ConfigFactoryInterface $config_factory) {
    $this->database = $database;
    $this->configFactory = $config_factory;
  }

  /**
   * Track a heatmap interaction.
   *
   * @param int $document_id
   *   The document ID.
   * @param int $page_number
   *   The page number.
   * @param float $x
   *   X position (0-1).
   * @param float $y
   *   Y position (0-1).
   * @param string $type
   *   Interaction type (view, click, scroll).
   * @param int $duration_ms
   *   Duration in milliseconds.
   * @param string|null $session_id
   *   Optional session ID.
   */
  public function trackHeatmap(
    int $document_id,
    int $page_number,
    float $x,
    float $y,
    string $type = 'view',
    int $duration_ms = 0,
    ?string $session_id = NULL
  ): void {
    try {
      $this->database->insert('pdf_heatmaps')
        ->fields([
          'document_id' => $document_id,
          'page_number' => $page_number,
          'x_position' => $x,
          'y_position' => $y,
          'interaction_type' => $type,
          'duration_ms' => $duration_ms,
          'session_id' => $session_id,
          'created_at' => date('Y-m-d H:i:s'),
        ])
        ->execute();
    }
    catch (\Exception $e) {
      \Drupal::logger('pdf_embed_seo_pro_plus')->error('Failed to track heatmap: @message', [
        '@message' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Get heatmap data for a document.
   *
   * @param int $document_id
   *   The document ID.
   * @param int|null $page_number
   *   Optional page number filter.
   * @param int $days
   *   Number of days to include.
   *
   * @return array
   *   Heatmap data grouped by page.
   */
  public function getHeatmapData(int $document_id, ?int $page_number = NULL, int $days = 30): array {
    $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

    try {
      $query = $this->database->select('pdf_heatmaps', 'h')
        ->fields('h', ['page_number', 'x_position', 'y_position', 'interaction_type', 'duration_ms'])
        ->condition('document_id', $document_id)
        ->condition('created_at', $cutoff, '>=');

      if ($page_number !== NULL) {
        $query->condition('page_number', $page_number);
      }

      $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      // Group by page
      $grouped = [];
      foreach ($results as $row) {
        $page = $row['page_number'];
        if (!isset($grouped[$page])) {
          $grouped[$page] = [];
        }
        $grouped[$page][] = [
          'x' => (float) $row['x_position'],
          'y' => (float) $row['y_position'],
          'type' => $row['interaction_type'],
          'duration' => (int) $row['duration_ms'],
        ];
      }

      return $grouped;
    }
    catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Get aggregated heatmap data for rendering.
   *
   * @param int $document_id
   *   The document ID.
   * @param int $page_number
   *   The page number.
   * @param int $grid_size
   *   Grid size for aggregation.
   *
   * @return array
   *   Aggregated heatmap grid.
   */
  public function getAggregatedHeatmap(int $document_id, int $page_number, int $grid_size = 20): array {
    $raw_data = $this->getHeatmapData($document_id, $page_number);

    if (empty($raw_data[$page_number])) {
      return [];
    }

    // Create grid
    $grid = [];
    for ($y = 0; $y < $grid_size; $y++) {
      for ($x = 0; $x < $grid_size; $x++) {
        $grid["{$x}-{$y}"] = 0;
      }
    }

    // Aggregate points
    foreach ($raw_data[$page_number] as $point) {
      $grid_x = min(floor($point['x'] * $grid_size), $grid_size - 1);
      $grid_y = min(floor($point['y'] * $grid_size), $grid_size - 1);
      $key = "{$grid_x}-{$grid_y}";
      $grid[$key] += 1 + ($point['duration'] / 1000);
    }

    // Normalize
    $max = max($grid) ?: 1;
    foreach ($grid as $key => $value) {
      $grid[$key] = $value / $max;
    }

    return $grid;
  }

  /**
   * Calculate engagement score for a document.
   *
   * @param int $document_id
   *   The document ID.
   *
   * @return float
   *   Engagement score (0-100).
   */
  public function getEngagementScore(int $document_id): float {
    $config = $this->configFactory->get('pdf_embed_seo_pro_plus.settings');
    $weights = [
      'views' => 1,
      'downloads' => 5,
      'time_spent' => 0.01,
      'pages_viewed' => 2,
      'return_visits' => 3,
    ];

    $metrics = $this->getDocumentMetrics($document_id);

    $score = 0;
    $score += ($metrics['views'] ?? 0) * $weights['views'];
    $score += ($metrics['downloads'] ?? 0) * $weights['downloads'];
    $score += ($metrics['avg_time_spent'] ?? 0) * $weights['time_spent'];
    $score += ($metrics['avg_pages_viewed'] ?? 0) * $weights['pages_viewed'];
    $score += ($metrics['return_visits'] ?? 0) * $weights['return_visits'];

    // Normalize to 0-100
    return min(100, max(0, $score));
  }

  /**
   * Get document metrics.
   *
   * @param int $document_id
   *   The document ID.
   * @param int $days
   *   Number of days to include.
   *
   * @return array
   *   Document metrics.
   */
  public function getDocumentMetrics(int $document_id, int $days = 30): array {
    $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

    try {
      // Get view count
      $views_query = $this->database->select('pdf_embed_seo_analytics', 'a');
      $views_query->addExpression('COUNT(*)', 'count');
      $views_query->condition('document_id', $document_id);
      $views_query->condition('event_type', 'view');
      $views_query->condition('created_at', $cutoff, '>=');
      $views = (int) $views_query->execute()->fetchField();

      // Get download count
      $downloads_query = $this->database->select('pdf_embed_seo_analytics', 'a');
      $downloads_query->addExpression('COUNT(*)', 'count');
      $downloads_query->condition('document_id', $document_id);
      $downloads_query->condition('event_type', 'download');
      $downloads_query->condition('created_at', $cutoff, '>=');
      $downloads = (int) $downloads_query->execute()->fetchField();

      // Get average time spent
      $time_query = $this->database->select('pdf_embed_seo_analytics', 'a');
      $time_query->addExpression('AVG(time_spent)', 'avg_time');
      $time_query->condition('document_id', $document_id);
      $time_query->condition('created_at', $cutoff, '>=');
      $time_query->isNotNull('time_spent');
      $avg_time = (float) $time_query->execute()->fetchField();

      // Get unique visitors
      $visitors_query = $this->database->select('pdf_embed_seo_analytics', 'a');
      $visitors_query->addExpression('COUNT(DISTINCT session_id)', 'count');
      $visitors_query->condition('document_id', $document_id);
      $visitors_query->condition('created_at', $cutoff, '>=');
      $unique_visitors = (int) $visitors_query->execute()->fetchField();

      // Get return visits
      $return_query = $this->database->select('pdf_embed_seo_analytics', 'a');
      $return_query->addField('a', 'session_id');
      $return_query->addExpression('COUNT(*)', 'visit_count');
      $return_query->condition('document_id', $document_id);
      $return_query->condition('created_at', $cutoff, '>=');
      $return_query->groupBy('session_id');
      $return_query->having('COUNT(*) > 1');
      $return_visits = $return_query->execute()->rowCount();

      return [
        'views' => $views,
        'downloads' => $downloads,
        'avg_time_spent' => round($avg_time, 2),
        'unique_visitors' => $unique_visitors,
        'return_visits' => $return_visits,
        'period_days' => $days,
      ];
    }
    catch (\Exception $e) {
      return [
        'views' => 0,
        'downloads' => 0,
        'avg_time_spent' => 0,
        'unique_visitors' => 0,
        'return_visits' => 0,
        'period_days' => $days,
      ];
    }
  }

  /**
   * Get top documents by engagement.
   *
   * @param int $limit
   *   Number of documents to return.
   * @param int $days
   *   Period in days.
   *
   * @return array
   *   Top documents with scores.
   */
  public function getTopDocuments(int $limit = 10, int $days = 30): array {
    $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

    try {
      $query = $this->database->select('pdf_embed_seo_analytics', 'a');
      $query->addField('a', 'document_id');
      $query->addExpression('COUNT(*)', 'total_events');
      $query->addExpression('SUM(CASE WHEN event_type = \'view\' THEN 1 ELSE 0 END)', 'views');
      $query->addExpression('SUM(CASE WHEN event_type = \'download\' THEN 1 ELSE 0 END)', 'downloads');
      $query->condition('created_at', $cutoff, '>=');
      $query->groupBy('document_id');
      $query->orderBy('total_events', 'DESC');
      $query->range(0, $limit);

      $results = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      foreach ($results as &$result) {
        $result['engagement_score'] = $this->getEngagementScore((int) $result['document_id']);
      }

      return $results;
    }
    catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Get geographic analytics.
   *
   * @param int|null $document_id
   *   Optional document filter.
   * @param int $days
   *   Period in days.
   *
   * @return array
   *   Geographic data.
   */
  public function getGeographicData(?int $document_id = NULL, int $days = 30): array {
    $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

    try {
      $query = $this->database->select('pdf_embed_seo_analytics', 'a');
      $query->addField('a', 'country');
      $query->addExpression('COUNT(*)', 'count');
      $query->condition('created_at', $cutoff, '>=');
      $query->isNotNull('country');
      $query->groupBy('country');
      $query->orderBy('count', 'DESC');

      if ($document_id !== NULL) {
        $query->condition('document_id', $document_id);
      }

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
    catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Get device analytics.
   *
   * @param int|null $document_id
   *   Optional document filter.
   * @param int $days
   *   Period in days.
   *
   * @return array
   *   Device data.
   */
  public function getDeviceData(?int $document_id = NULL, int $days = 30): array {
    $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

    try {
      $query = $this->database->select('pdf_embed_seo_analytics', 'a');
      $query->addField('a', 'device_type');
      $query->addExpression('COUNT(*)', 'count');
      $query->condition('created_at', $cutoff, '>=');
      $query->isNotNull('device_type');
      $query->groupBy('device_type');
      $query->orderBy('count', 'DESC');

      if ($document_id !== NULL) {
        $query->condition('document_id', $document_id);
      }

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
    catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Get time series data.
   *
   * @param int|null $document_id
   *   Optional document filter.
   * @param int $days
   *   Period in days.
   * @param string $granularity
   *   Data granularity (day, week, month).
   *
   * @return array
   *   Time series data.
   */
  public function getTimeSeries(?int $document_id = NULL, int $days = 30, string $granularity = 'day'): array {
    $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

    $date_format = match ($granularity) {
      'week' => '%Y-%u',
      'month' => '%Y-%m',
      default => '%Y-%m-%d',
    };

    try {
      $query = $this->database->select('pdf_embed_seo_analytics', 'a');
      $query->addExpression("DATE_FORMAT(created_at, '{$date_format}')", 'period');
      $query->addExpression('COUNT(*)', 'count');
      $query->addExpression('SUM(CASE WHEN event_type = \'view\' THEN 1 ELSE 0 END)', 'views');
      $query->addExpression('SUM(CASE WHEN event_type = \'download\' THEN 1 ELSE 0 END)', 'downloads');
      $query->condition('created_at', $cutoff, '>=');
      $query->groupBy('period');
      $query->orderBy('period', 'ASC');

      if ($document_id !== NULL) {
        $query->condition('document_id', $document_id);
      }

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
    catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Clean up old heatmap data.
   *
   * @param int $days
   *   Delete data older than this many days.
   *
   * @return int
   *   Number of deleted records.
   */
  public function cleanupHeatmaps(int $days = 90): int {
    $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

    try {
      return $this->database->delete('pdf_heatmaps')
        ->condition('created_at', $cutoff, '<')
        ->execute();
    }
    catch (\Exception $e) {
      return 0;
    }
  }

}

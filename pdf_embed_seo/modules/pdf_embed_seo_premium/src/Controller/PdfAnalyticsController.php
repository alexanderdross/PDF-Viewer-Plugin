<?php

namespace Drupal\pdf_embed_seo_premium\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for PDF analytics dashboard (premium feature).
 */
class PdfAnalyticsController extends ControllerBase {

  /**
   * Display the analytics dashboard.
   *
   * @return array
   *   A render array.
   */
  public function dashboard() {
    $analytics = \Drupal::service('pdf_embed_seo.analytics_tracker');
    $storage = $this->entityTypeManager()->getStorage('pdf_document');

    // Get period from query string.
    $period = \Drupal::request()->query->get('period', '30days');

    // Get statistics.
    $total_views = $analytics->getTotalViews();
    $total_documents = $storage->getQuery()
      ->accessCheck(TRUE)
      ->count()
      ->execute();

    // Get popular documents.
    $days = $this->getPeriodDays($period);
    $popular_ids = $analytics->getPopularDocuments(10, $days);
    $popular_documents = [];
    if (!empty($popular_ids)) {
      $documents = $storage->loadMultiple(array_keys($popular_ids));
      foreach ($documents as $id => $document) {
        $popular_documents[] = [
          'document' => $document,
          'views' => $popular_ids[$id],
        ];
      }
    }

    // Get recent views.
    $recent_views = $analytics->getRecentViews(50);
    $recent_data = [];
    foreach ($recent_views as $view) {
      $document = $storage->load($view->pdf_document_id);
      if ($document) {
        $user = NULL;
        if ($view->user_id > 0) {
          $user = $this->entityTypeManager()
            ->getStorage('user')
            ->load($view->user_id);
        }

        $recent_data[] = [
          'document' => $document,
          'user' => $user,
          'timestamp' => $view->timestamp,
          'ip_address' => $view->ip_address,
        ];
      }
    }

    return [
      '#theme' => 'pdf_analytics_dashboard',
      '#total_views' => $total_views,
      '#total_documents' => $total_documents,
      '#popular_documents' => $popular_documents,
      '#recent_views' => $recent_data,
      '#period' => $period,
      '#attached' => [
        'library' => ['pdf_embed_seo/admin'],
      ],
    ];
  }

  /**
   * Export analytics data as CSV.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   A streamed CSV response.
   */
  public function export() {
    $analytics = \Drupal::service('pdf_embed_seo.analytics_tracker');
    $storage = $this->entityTypeManager()->getStorage('pdf_document');

    $response = new StreamedResponse(function () use ($analytics, $storage) {
      $handle = fopen('php://output', 'w');

      // Headers.
      fputcsv($handle, [
        'Document ID',
        'Document Title',
        'User ID',
        'Username',
        'IP Address',
        'User Agent',
        'Referer',
        'Date/Time',
      ]);

      // Get all views.
      $views = $analytics->getRecentViews(10000);

      foreach ($views as $view) {
        $document = $storage->load($view->pdf_document_id);
        $user = NULL;
        if ($view->user_id > 0) {
          $user = $this->entityTypeManager()
            ->getStorage('user')
            ->load($view->user_id);
        }

        fputcsv($handle, [
          $view->pdf_document_id,
          $document ? $document->label() : 'Deleted',
          $view->user_id,
          $user ? $user->getAccountName() : 'Anonymous',
          $view->ip_address,
          $view->user_agent,
          $view->referer,
          date('Y-m-d H:i:s', $view->timestamp),
        ]);
      }

      fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="pdf-analytics-' . date('Y-m-d') . '.csv"');

    return $response;
  }

  /**
   * Get number of days from period string.
   *
   * @param string $period
   *   The period string.
   *
   * @return int
   *   Number of days.
   */
  protected function getPeriodDays($period) {
    switch ($period) {
      case '7days':
        return 7;

      case '30days':
        return 30;

      case '90days':
        return 90;

      case '12months':
        return 365;

      case 'all':
        return 9999;

      default:
        return 30;
    }
  }

}

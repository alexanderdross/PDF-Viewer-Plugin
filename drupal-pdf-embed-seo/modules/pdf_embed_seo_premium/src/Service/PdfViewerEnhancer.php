<?php

namespace Drupal\pdf_embed_seo_premium\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;

/**
 * Service for enhancing PDF viewer with premium features.
 */
class PdfViewerEnhancer {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The progress tracker service.
   *
   * @var \Drupal\pdf_embed_seo_premium\Service\PdfProgressTracker
   */
  protected $progressTracker;

  /**
   * Constructs a PdfViewerEnhancer object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\pdf_embed_seo_premium\Service\PdfProgressTracker $progress_tracker
   *   The progress tracker service.
   */
  public function __construct(
    AccountProxyInterface $current_user,
    ConfigFactoryInterface $config_factory,
    PdfProgressTracker $progress_tracker
  ) {
    $this->currentUser = $current_user;
    $this->configFactory = $config_factory;
    $this->progressTracker = $progress_tracker;
  }

  /**
   * Get enhanced viewer options for a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array
   *   Array of viewer options.
   */
  public function getViewerOptions(PdfDocumentInterface $pdf_document): array {
    $config = $this->configFactory->get('pdf_embed_seo_premium.settings');

    $options = [
      'enableTextSearch' => (bool) $config->get('enable_text_search'),
      'enableBookmarks' => (bool) $config->get('enable_bookmarks'),
      'enableReadingProgress' => (bool) $config->get('enable_reading_progress'),
      'enableResumePrompt' => (bool) $config->get('enable_resume_prompt'),
    ];

    // Get saved progress if available.
    if ($options['enableReadingProgress']) {
      $progress = $this->progressTracker->getProgress($pdf_document->id());
      if ($progress) {
        $options['savedProgress'] = $progress;
      }
    }

    return $options;
  }

  /**
   * Get JavaScript settings for enhanced viewer.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array
   *   JavaScript settings array.
   */
  public function getJsSettings(PdfDocumentInterface $pdf_document): array {
    $options = $this->getViewerOptions($pdf_document);

    return [
      'pdfEmbedSeoPremium' => [
        'documentId' => $pdf_document->id(),
        'options' => $options,
        'apiEndpoints' => [
          'saveProgress' => '/api/pdf-embed-seo/v1/documents/' . $pdf_document->id() . '/progress',
          'getProgress' => '/api/pdf-embed-seo/v1/documents/' . $pdf_document->id() . '/progress',
        ],
        'translations' => [
          'search' => t('Search'),
          'searchPlaceholder' => t('Find in document...'),
          'noResults' => t('No results found'),
          'bookmarks' => t('Bookmarks'),
          'noBookmarks' => t('No bookmarks in this document'),
          'resumeReading' => t('Resume reading'),
          'resumePrompt' => t('Would you like to continue where you left off?'),
          'resumeYes' => t('Yes, resume'),
          'resumeNo' => t('No, start over'),
          'progressSaved' => t('Progress saved'),
        ],
      ],
    ];
  }

  /**
   * Check if text search is enabled.
   *
   * @return bool
   *   TRUE if text search is enabled.
   */
  public function isTextSearchEnabled(): bool {
    return (bool) $this->configFactory->get('pdf_embed_seo_premium.settings')->get('enable_text_search');
  }

  /**
   * Check if bookmarks panel is enabled.
   *
   * @return bool
   *   TRUE if bookmarks panel is enabled.
   */
  public function isBookmarksEnabled(): bool {
    return (bool) $this->configFactory->get('pdf_embed_seo_premium.settings')->get('enable_bookmarks');
  }

  /**
   * Check if reading progress tracking is enabled.
   *
   * @return bool
   *   TRUE if reading progress is enabled.
   */
  public function isReadingProgressEnabled(): bool {
    return (bool) $this->configFactory->get('pdf_embed_seo_premium.settings')->get('enable_reading_progress');
  }

}

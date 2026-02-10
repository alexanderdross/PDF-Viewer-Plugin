<?php

namespace Drupal\pdf_embed_seo\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'pdf_embed_seo_viewer' formatter.
 *
 * This formatter displays PDF files using the PDF.js viewer with
 * all the SEO optimization features from the PDF Embed & SEO module.
 *
 * @FieldFormatter(
 *   id = "pdf_embed_seo_viewer",
 *   label = @Translation("PDF Viewer (SEO Optimized)"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class PdfViewerFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'width' => '100%',
      'height' => '800px',
      'allow_download' => FALSE,
      'allow_print' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#description' => $this->t('The width of the PDF viewer (e.g., 100%, 800px).'),
      '#default_value' => $this->getSetting('width'),
      '#size' => 20,
    ];

    $elements['height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#description' => $this->t('The height of the PDF viewer (e.g., 800px, 100vh).'),
      '#default_value' => $this->getSetting('height'),
      '#size' => 20,
    ];

    $elements['allow_download'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow download'),
      '#description' => $this->t('Show download button in the viewer.'),
      '#default_value' => $this->getSetting('allow_download'),
    ];

    $elements['allow_print'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow print'),
      '#description' => $this->t('Show print button in the viewer.'),
      '#default_value' => $this->getSetting('allow_print'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Dimensions: @width x @height', [
      '@width' => $this->getSetting('width'),
      '@height' => $this->getSetting('height'),
    ]);

    if ($this->getSetting('allow_download')) {
      $summary[] = $this->t('Download enabled');
    }

    if ($this->getSetting('allow_print')) {
      $summary[] = $this->t('Print enabled');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $config = \Drupal::config('pdf_embed_seo.settings');

    foreach ($items as $delta => $item) {
      $file = $item->entity;
      if (!$file) {
        continue;
      }

      // Only process PDF files.
      if ($file->getMimeType() !== 'application/pdf') {
        continue;
      }

      $file_url = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
      $viewer_theme = $config->get('viewer_theme') ?? 'light';

      $elements[$delta] = [
        '#theme' => 'pdf_viewer',
        '#pdf_document' => NULL,
        '#pdf_url' => $file_url,
        '#allow_download' => $this->getSetting('allow_download'),
        '#allow_print' => $this->getSetting('allow_print'),
        '#viewer_theme' => $viewer_theme,
        '#width' => $this->getSetting('width'),
        '#height' => $this->getSetting('height'),
        '#attached' => [
          'library' => [
            'pdf_embed_seo/viewer',
          ],
          'drupalSettings' => [
            'pdfEmbedSeo' => [
              'pdfUrl' => $file_url,
              'workerSrc' => '/' . \Drupal::service('extension.list.module')->getPath('pdf_embed_seo') . '/assets/pdfjs/pdf.worker.min.js',
              'allowDownload' => (bool) $this->getSetting('allow_download'),
              'allowPrint' => (bool) $this->getSetting('allow_print'),
            ],
          ],
        ],
        '#cache' => [
          'tags' => $file->getCacheTags(),
        ],
      ];

      // Add dark theme library if configured.
      if ($viewer_theme === 'dark') {
        $elements[$delta]['#attached']['library'][] = 'pdf_embed_seo/viewer-dark';
      }
    }

    return $elements;
  }

}

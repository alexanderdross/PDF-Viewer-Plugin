<?php

namespace Drupal\pdf_embed_seo\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure PDF Embed & SEO settings.
 */
class PdfEmbedSeoSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pdf_embed_seo_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['pdf_embed_seo.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('pdf_embed_seo.settings');

    // General settings.
    $form['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General Settings'),
      '#open' => TRUE,
    ];

    $form['general']['default_allow_download'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow downloads by default'),
      '#description' => $this->t('Default setting for new PDF documents. Can be overridden per document.'),
      '#default_value' => $config->get('default_allow_download') ?? FALSE,
    ];

    $form['general']['default_allow_print'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow printing by default'),
      '#description' => $this->t('Default setting for new PDF documents. Can be overridden per document.'),
      '#default_value' => $config->get('default_allow_print') ?? FALSE,
    ];

    // Viewer settings.
    $form['viewer'] = [
      '#type' => 'details',
      '#title' => $this->t('Viewer Settings'),
      '#open' => TRUE,
    ];

    $form['viewer']['viewer_theme'] = [
      '#type' => 'select',
      '#title' => $this->t('Viewer Theme'),
      '#description' => $this->t('Choose the color theme for the PDF viewer.'),
      '#options' => [
        'light' => $this->t('Light'),
        'dark' => $this->t('Dark'),
      ],
      '#default_value' => $config->get('viewer_theme') ?? 'light',
    ];

    $form['viewer']['viewer_height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default Viewer Height'),
      '#description' => $this->t('Default height for the PDF viewer (e.g., 800px, 100vh).'),
      '#default_value' => $config->get('viewer_height') ?? '800px',
      '#size' => 20,
    ];

    // Thumbnail settings.
    $form['thumbnails'] = [
      '#type' => 'details',
      '#title' => $this->t('Thumbnail Settings'),
      '#open' => TRUE,
    ];

    $form['thumbnails']['auto_generate_thumbnails'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Auto-generate thumbnails'),
      '#description' => $this->t('Automatically generate thumbnails from PDF first pages. Requires ImageMagick or Ghostscript.'),
      '#default_value' => $config->get('auto_generate_thumbnails') ?? TRUE,
    ];

    $form['thumbnails']['thumbnail_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Thumbnail Width'),
      '#description' => $this->t('Width in pixels for auto-generated thumbnails.'),
      '#default_value' => $config->get('thumbnail_width') ?? 300,
      '#min' => 100,
      '#max' => 1000,
    ];

    $form['thumbnails']['thumbnail_height'] = [
      '#type' => 'number',
      '#title' => $this->t('Thumbnail Height'),
      '#description' => $this->t('Height in pixels for auto-generated thumbnails.'),
      '#default_value' => $config->get('thumbnail_height') ?? 400,
      '#min' => 100,
      '#max' => 1000,
    ];

    // Archive settings.
    $form['archive'] = [
      '#type' => 'details',
      '#title' => $this->t('Archive Page Settings'),
      '#open' => TRUE,
    ];

    $form['archive']['archive_posts_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Documents per page'),
      '#description' => $this->t('Number of PDF documents to display per page on the archive.'),
      '#default_value' => $config->get('archive_posts_per_page') ?? 12,
      '#min' => 1,
      '#max' => 100,
    ];

    $form['archive']['archive_display'] = [
      '#type' => 'select',
      '#title' => $this->t('Archive Display Style'),
      '#description' => $this->t('Choose how PDF documents are displayed on the archive page.'),
      '#options' => [
        'grid' => $this->t('Grid View (Thumbnail cards)'),
        'list' => $this->t('List View (Simple bullet-style list)'),
      ],
      '#default_value' => $config->get('archive_display') ?? 'grid',
    ];

    $form['archive']['archive_show_description'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show descriptions'),
      '#description' => $this->t('Display PDF descriptions/excerpts on the archive page.'),
      '#default_value' => $config->get('archive_show_description') ?? TRUE,
    ];

    $form['archive']['archive_show_view_count'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show view counts'),
      '#description' => $this->t('Display view counts on the archive page.'),
      '#default_value' => $config->get('archive_show_view_count') ?? TRUE,
    ];

    // SEO settings.
    $form['seo'] = [
      '#type' => 'details',
      '#title' => $this->t('SEO Settings'),
      '#open' => FALSE,
    ];

    $form['seo']['enable_schema'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Schema.org markup'),
      '#description' => $this->t('Add DigitalDocument and CollectionPage schema to PDF pages.'),
      '#default_value' => $config->get('enable_schema') ?? TRUE,
    ];

    $form['seo']['archive_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Archive Page Title'),
      '#description' => $this->t('The title for the PDF archive page.'),
      '#default_value' => $config->get('archive_title') ?? $this->t('PDF Documents'),
    ];

    $form['seo']['archive_description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Archive Page Meta Description'),
      '#description' => $this->t('Meta description for the PDF archive page.'),
      '#default_value' => $config->get('archive_description') ?? '',
      '#rows' => 3,
    ];

    // Premium settings (only show if premium is active).
    if (\Drupal::moduleHandler()->moduleExists('pdf_embed_seo_premium')) {
      $form['premium'] = [
        '#type' => 'details',
        '#title' => $this->t('Premium Settings'),
        '#open' => FALSE,
      ];

      $form['premium']['enable_analytics'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable Analytics'),
        '#description' => $this->t('Track detailed PDF viewing analytics.'),
        '#default_value' => $config->get('enable_analytics') ?? TRUE,
      ];

      $form['premium']['enable_search'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable PDF Search'),
        '#description' => $this->t('Allow users to search within PDF documents.'),
        '#default_value' => $config->get('enable_search') ?? TRUE,
      ];

      $form['premium']['enable_bookmarks'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable Bookmarks'),
        '#description' => $this->t('Allow users to bookmark pages within PDFs.'),
        '#default_value' => $config->get('enable_bookmarks') ?? TRUE,
      ];

      $form['premium']['enable_progress'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable Reading Progress'),
        '#description' => $this->t('Remember and restore user reading progress.'),
        '#default_value' => $config->get('enable_progress') ?? TRUE,
      ];
    }

    // Credit link.
    $form['credit'] = [
      '#type' => 'markup',
      '#markup' => '<p class="pdf-embed-seo-credit" style="text-align: center; margin-top: 30px; color: #666; font-size: 13px;">' .
        $this->t('made with <span style="color: #e25555;" aria-hidden="true">♥</span><span class="visually-hidden">love</span> by <a href="@url" target="_blank" rel="noopener noreferrer" aria-label="@aria_label" title="@title">Dross:Media</a>', [
          '@url' => 'https://dross.net/media/',
          '@aria_label' => $this->t('Visit Dross:Media website (opens in new tab)'),
          '@title' => $this->t('Visit Dross:Media website'),
        ]) .
        '</p>',
      '#weight' => 999,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('pdf_embed_seo.settings');

    // General settings.
    $config->set('default_allow_download', $form_state->getValue('default_allow_download'));
    $config->set('default_allow_print', $form_state->getValue('default_allow_print'));

    // Viewer settings.
    $config->set('viewer_theme', $form_state->getValue('viewer_theme'));
    $config->set('viewer_height', $form_state->getValue('viewer_height'));

    // Thumbnail settings.
    $config->set('auto_generate_thumbnails', $form_state->getValue('auto_generate_thumbnails'));
    $config->set('thumbnail_width', $form_state->getValue('thumbnail_width'));
    $config->set('thumbnail_height', $form_state->getValue('thumbnail_height'));

    // Archive settings.
    $config->set('archive_posts_per_page', $form_state->getValue('archive_posts_per_page'));
    $config->set('archive_display', $form_state->getValue('archive_display'));
    $config->set('archive_show_description', $form_state->getValue('archive_show_description'));
    $config->set('archive_show_view_count', $form_state->getValue('archive_show_view_count'));

    // SEO settings.
    $config->set('enable_schema', $form_state->getValue('enable_schema'));
    $config->set('archive_title', $form_state->getValue('archive_title'));
    $config->set('archive_description', $form_state->getValue('archive_description'));

    // Premium settings.
    if (\Drupal::moduleHandler()->moduleExists('pdf_embed_seo_premium')) {
      $config->set('enable_analytics', $form_state->getValue('enable_analytics'));
      $config->set('enable_search', $form_state->getValue('enable_search'));
      $config->set('enable_bookmarks', $form_state->getValue('enable_bookmarks'));
      $config->set('enable_progress', $form_state->getValue('enable_progress'));
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }

}

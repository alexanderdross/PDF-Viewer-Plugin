<?php

namespace Drupal\pdf_embed_seo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for the PDF archive page.
 */
class PdfArchiveController extends ControllerBase {

  /**
   * Display the PDF archive listing.
   *
   * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
   *   A render array or redirect response.
   */
  public function listing() {
    // Check for premium redirect first.
    $redirect = $this->checkArchiveRedirect();
    if ($redirect) {
      return $redirect;
    }

    $config = $this->config('pdf_embed_seo.settings');
    $posts_per_page = $config->get('archive_posts_per_page') ?? 12;
    $display = $config->get('archive_display') ?? 'grid';
    $show_description = $config->get('archive_show_description') ?? TRUE;
    $show_view_count = $config->get('archive_show_view_count') ?? TRUE;
    $show_breadcrumbs = $config->get('show_breadcrumbs') ?? TRUE;

    // Load published PDF documents.
    $storage = $this->entityTypeManager()->getStorage('pdf_document');

    // Get the current page from the request.
    $page = \Drupal::request()->query->get('page', 0);

    // Build query.
    $query = $storage->getQuery()
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->accessCheck(TRUE)
      ->pager($posts_per_page);

    // Apply category filter if set (premium feature).
    $category = \Drupal::request()->query->get('pdf_category');
    if ($category && $this->moduleHandler()->moduleExists('pdf_embed_seo_premium')) {
      $query->condition('pdf_category', $category);
    }

    // Apply tag filter if set (premium feature).
    $tag = \Drupal::request()->query->get('pdf_tag');
    if ($tag && $this->moduleHandler()->moduleExists('pdf_embed_seo_premium')) {
      $query->condition('pdf_tags', $tag);
    }

    $ids = $query->execute();
    $documents = $storage->loadMultiple($ids);

    // Build document items.
    $items = [];
    foreach ($documents as $document) {
      $thumbnail = NULL;
      if ($document->getThumbnail()) {
        $thumbnail = [
          '#theme' => 'image_style',
          '#style_name' => 'medium',
          '#uri' => $document->getThumbnail()->getFileUri(),
          '#alt' => $document->label(),
        ];
      }

      $items[] = [
        '#theme' => 'pdf_archive_item',
        '#document' => $document,
        '#thumbnail' => $thumbnail,
        '#url' => $document->toUrl(),
        '#show_description' => $show_description,
        '#show_view_count' => $show_view_count,
      ];
    }

    // Build the pager.
    $pager = [
      '#type' => 'pager',
    ];

    // Build breadcrumb data.
    $site_name = $this->config('system.site')->get('name');
    $site_url = Url::fromRoute('<front>')->setAbsolute()->toString();
    $archive_url = Url::fromRoute('pdf_embed_seo.archive')->setAbsolute()->toString();
    $archive_title = $config->get('archive_title') ?? $this->t('PDF Documents');
    $archive_description = $config->get('archive_description') ?? $this->t('Browse all available PDF documents.');

    $build = [
      '#theme' => 'pdf_archive',
      '#documents' => $items,
      '#pager' => $pager,
      '#display_style' => $display,
      '#show_description' => $show_description,
      '#show_view_count' => $show_view_count,
      '#archive_title' => $archive_title,
      '#archive_description' => $archive_description,
      '#site_name' => $site_name,
      '#site_url' => $site_url,
      '#archive_url' => $archive_url,
      '#show_breadcrumbs' => $show_breadcrumbs,
      '#attached' => [
        'library' => ['pdf_embed_seo/archive'],
      ],
      '#cache' => [
        'tags' => ['pdf_document_list', 'config:pdf_embed_seo.settings'],
        'contexts' => ['url.query_args:page', 'url.query_args:pdf_category', 'url.query_args:pdf_tag'],
      ],
    ];

    // Add display class.
    $build['#attributes']['class'][] = 'pdf-archive';
    $build['#attributes']['class'][] = 'pdf-archive--' . $display;

    // Add category/tag filters if premium is active.
    if ($this->moduleHandler()->moduleExists('pdf_embed_seo_premium')) {
      $build['#filters'] = $this->buildFilters();
    }

    return $build;
  }

  /**
   * Build taxonomy filter form (premium feature).
   *
   * @return array
   *   A render array for filters.
   */
  protected function buildFilters() {
    $filters = [];

    // Category filter.
    $categories = $this->entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => 'pdf_category']);

    if (!empty($categories)) {
      $options = ['' => $this->t('All Categories')];
      foreach ($categories as $term) {
        $options[$term->id()] = $term->label();
      }

      $current_category = \Drupal::request()->query->get('pdf_category', '');

      $filters['category'] = [
        '#type' => 'select',
        '#title' => $this->t('Category'),
        '#options' => $options,
        '#default_value' => $current_category,
        '#attributes' => [
          'onchange' => 'this.form.submit()',
        ],
      ];
    }

    // Tag filter.
    $tags = $this->entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => 'pdf_tags']);

    if (!empty($tags)) {
      $options = ['' => $this->t('All Tags')];
      foreach ($tags as $term) {
        $options[$term->id()] = $term->label();
      }

      $current_tag = \Drupal::request()->query->get('pdf_tag', '');

      $filters['tag'] = [
        '#type' => 'select',
        '#title' => $this->t('Tag'),
        '#options' => $options,
        '#default_value' => $current_tag,
        '#attributes' => [
          'onchange' => 'this.form.submit()',
        ],
      ];
    }

    if (!empty($filters)) {
      return [
        '#type' => 'container',
        '#attributes' => ['class' => ['pdf-archive-filters']],
        'form' => [
          '#type' => 'form',
          '#method' => 'get',
          '#action' => Url::fromRoute('pdf_embed_seo.archive')->toString(),
        ] + $filters + [
          'submit' => [
            '#type' => 'submit',
            '#value' => $this->t('Filter'),
            '#attributes' => ['class' => ['visually-hidden']],
          ],
        ],
      ];
    }

    return [];
  }

  /**
   * Check if archive redirect is enabled (premium feature).
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
   *   A redirect response or NULL if no redirect is needed.
   */
  protected function checkArchiveRedirect() {
    // Check if premium module is active and license is valid.
    if (!$this->moduleHandler()->moduleExists('pdf_embed_seo_premium')) {
      return NULL;
    }

    // Check license validity.
    if (!function_exists('pdf_embed_seo_premium_is_license_valid') || !pdf_embed_seo_premium_is_license_valid()) {
      return NULL;
    }

    // Check if redirect is enabled.
    $premium_config = $this->config('pdf_embed_seo_premium.settings');
    $redirect_enabled = $premium_config->get('archive_redirect_enabled') ?? FALSE;

    if (!$redirect_enabled) {
      return NULL;
    }

    $redirect_url = $premium_config->get('archive_redirect_url');
    if (empty($redirect_url)) {
      return NULL;
    }

    // Get redirect type (301 or 302).
    $redirect_type = $premium_config->get('archive_redirect_type') ?? '301';
    $status_code = ($redirect_type === '301') ? 301 : 302;

    return new RedirectResponse($redirect_url, $status_code);
  }

}

<?php

namespace Drupal\pdf_embed_seo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Controller for the PDF archive page.
 */
class PdfArchiveController extends ControllerBase {

  /**
   * Display the PDF archive listing.
   *
   * @return array
   *   A render array.
   */
  public function listing() {
    $config = $this->config('pdf_embed_seo.settings');
    $posts_per_page = $config->get('archive_posts_per_page') ?? 12;
    $display = $config->get('archive_display') ?? 'grid';

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
      ];
    }

    // Build the pager.
    $pager = [
      '#type' => 'pager',
    ];

    $build = [
      '#theme' => 'pdf_archive',
      '#documents' => $items,
      '#pager' => $pager,
      '#attached' => [
        'library' => ['pdf_embed_seo/archive'],
      ],
      '#cache' => [
        'tags' => ['pdf_document_list'],
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

}

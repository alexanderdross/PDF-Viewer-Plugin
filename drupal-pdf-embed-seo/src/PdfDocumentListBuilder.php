<?php

namespace Drupal\pdf_embed_seo;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a list builder for PDF Document entities.
 */
class PdfDocumentListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [
      'title' => $this->t('Title'),
      'status' => $this->t('Status'),
      'view_count' => $this->t('Views'),
      'author' => $this->t('Author'),
      'created' => $this->t('Created'),
    ];
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $entity */
    $row = [
      'title' => Link::createFromRoute(
        $entity->getTitle(),
        'entity.pdf_document.canonical',
        ['pdf_document' => $entity->id()]
      ),
      'status' => $entity->isPublished() ? $this->t('Published') : $this->t('Unpublished'),
      'view_count' => $entity->getViewCount(),
      'author' => $entity->getOwner() ? $entity->getOwner()->getDisplayName() : $this->t('Anonymous'),
      'created' => \Drupal::service('date.formatter')->format($entity->getCreatedTime(), 'short'),
    ];
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#empty'] = $this->t('No PDF documents available. <a href=":url">Add a PDF document</a>.', [
      ':url' => Url::fromRoute('entity.pdf_document.add_form')->toString(),
    ]);
    return $build;
  }

}

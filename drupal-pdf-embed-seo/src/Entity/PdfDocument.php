<?php

namespace Drupal\pdf_embed_seo\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the PDF Document entity.
 *
 * @ContentEntityType(
 *   id = "pdf_document",
 *   label = @Translation("PDF Document"),
 *   label_collection = @Translation("PDF Documents"),
 *   label_singular = @Translation("PDF document"),
 *   label_plural = @Translation("PDF documents"),
 *   label_count = @PluralTranslation(
 *     singular = "@count PDF document",
 *     plural = "@count PDF documents"
 *   ),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\pdf_embed_seo\PdfDocumentListBuilder",
 *     "form" = {
 *       "add" = "Drupal\pdf_embed_seo\Form\PdfDocumentForm",
 *       "edit" = "Drupal\pdf_embed_seo\Form\PdfDocumentForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\pdf_embed_seo\PdfDocumentAccessControlHandler",
 *   },
 *   base_table = "pdf_document",
 *   data_table = "pdf_document_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer pdf embed seo",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/pdf/{pdf_document}",
 *     "add-form" = "/admin/content/pdf-documents/add",
 *     "edit-form" = "/admin/content/pdf-documents/{pdf_document}/edit",
 *     "delete-form" = "/admin/content/pdf-documents/{pdf_document}/delete",
 *     "collection" = "/admin/content/pdf-documents",
 *   },
 *   field_ui_base_route = "pdf_embed_seo.settings",
 * )
 */
class PdfDocument extends ContentEntityBase implements PdfDocumentInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    // If no owner has been set, use the current user.
    if (!$this->getOwner()) {
      $this->setOwnerId(\Drupal::currentUser()->id());
    }

    // Generate path alias from title if not set.
    if ($this->isNew() && !$this->get('path')->alias) {
      // Use Pathauto's alias cleaner if available, otherwise use basic sanitization.
      if (\Drupal::hasService('pathauto.alias_cleaner')) {
        $clean_title = \Drupal::service('pathauto.alias_cleaner')->cleanString($this->label());
      }
      else {
        // Fallback: basic URL-safe string generation.
        $clean_title = preg_replace('/[^a-z0-9\-]/', '-', strtolower($this->label()));
        $clean_title = preg_replace('/-+/', '-', $clean_title);
        $clean_title = trim($clean_title, '-');
      }
      $alias = '/pdf/' . $clean_title;
      $this->get('path')->alias = $alias;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the PDF document.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDescription(t('A description of the PDF document.'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => -5,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'text_default',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['pdf_file'] = BaseFieldDefinition::create('file')
      ->setLabel(t('PDF File'))
      ->setDescription(t('Upload the PDF file.'))
      ->setRequired(TRUE)
      ->setSetting('file_extensions', 'pdf')
      ->setSetting('file_directory', 'pdf_documents')
      ->setSetting('max_filesize', '50 MB')
      ->setDisplayOptions('form', [
        'type' => 'file_generic',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['thumbnail'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Thumbnail'))
      ->setDescription(t('Thumbnail image for the PDF. Auto-generated if ImageMagick is available.'))
      ->setSetting('file_directory', 'pdf_thumbnails')
      ->setSetting('alt_field', FALSE)
      ->setSetting('title_field', FALSE)
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 5,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'image',
        'weight' => 0,
        'settings' => [
          'image_style' => 'medium',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['allow_download'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Allow Download'))
      ->setDescription(t('Allow users to download this PDF.'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 10,
        'settings' => [
          'display_label' => TRUE,
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['allow_print'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Allow Print'))
      ->setDescription(t('Allow users to print this PDF.'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 11,
        'settings' => [
          'display_label' => TRUE,
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    // View count is now a computed field that reads from the analytics table.
    // This avoids entity saves on each page view (performance optimization).
    $fields['view_count'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('View Count'))
      ->setDescription(t('Number of times this PDF has been viewed (computed from analytics).'))
      ->setDefaultValue(0)
      ->setComputed(TRUE)
      ->setClass('\Drupal\pdf_embed_seo\Field\ComputedViewCount')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'number_integer',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Premium fields.
    $fields['password'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Password'))
      ->setDescription(t('Optional password to protect this PDF (Premium feature).'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Published'))
      ->setDescription(t('Whether the PDF document is published.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 100,
        'settings' => [
          'display_label' => TRUE,
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['uid']
      ->setLabel(t('Author'))
      ->setDescription(t('The user who created the PDF document.'))
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 50,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time when the PDF document was created.'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'timestamp',
        'weight' => 30,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time when the PDF document was last edited.'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'timestamp',
        'weight' => 31,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['path'] = BaseFieldDefinition::create('path')
      ->setLabel(t('URL alias'))
      ->setDescription(t('The PDF document URL alias.'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'path',
        'weight' => 60,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setComputed(TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('description')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getPdfFile() {
    return $this->get('pdf_file')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getThumbnail() {
    return $this->get('thumbnail')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function allowsDownload() {
    return (bool) $this->get('allow_download')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function allowsPrint() {
    return (bool) $this->get('allow_print')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getViewCount() {
    return (int) $this->get('view_count')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function incrementViewCount() {
    $this->set('view_count', $this->getViewCount() + 1);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published = TRUE) {
    $this->set('status', $published);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPassword() {
    return $this->get('password')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function hasPassword() {
    return !empty($this->get('password')->value);
  }

}

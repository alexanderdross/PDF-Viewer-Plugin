<?php

namespace Drupal\pdf_embed_seo\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a PDF Viewer block.
 *
 * @Block(
 *   id = "pdf_viewer_block",
 *   admin_label = @Translation("PDF Viewer"),
 *   category = @Translation("Content")
 * )
 */
class PdfViewerBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a PdfViewerBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'pdf_document_id' => NULL,
      'height' => '600px',
      'show_title' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Get available PDF documents.
    $storage = $this->entityTypeManager->getStorage('pdf_document');
    $query = $storage->getQuery()
      ->condition('status', 1)
      ->sort('title')
      ->accessCheck(TRUE);
    $ids = $query->execute();

    $options = [];
    if (!empty($ids)) {
      $documents = $storage->loadMultiple($ids);
      foreach ($documents as $document) {
        $options[$document->id()] = $document->label();
      }
    }

    $form['pdf_document_id'] = [
      '#type' => 'select',
      '#title' => $this->t('PDF Document'),
      '#description' => $this->t('Select the PDF document to display.'),
      '#options' => $options,
      '#default_value' => $this->configuration['pdf_document_id'],
      '#required' => TRUE,
      '#empty_option' => $this->t('- Select a PDF -'),
    ];

    $form['height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Viewer Height'),
      '#description' => $this->t('Height of the PDF viewer (e.g., 600px, 80vh).'),
      '#default_value' => $this->configuration['height'],
      '#size' => 20,
    ];

    $form['show_title'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show document title'),
      '#default_value' => $this->configuration['show_title'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['pdf_document_id'] = $form_state->getValue('pdf_document_id');
    $this->configuration['height'] = $form_state->getValue('height');
    $this->configuration['show_title'] = $form_state->getValue('show_title');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $pdf_document_id = $this->configuration['pdf_document_id'];

    if (!$pdf_document_id) {
      return [
        '#markup' => $this->t('No PDF document selected.'),
      ];
    }

    $pdf_document = $this->entityTypeManager
      ->getStorage('pdf_document')
      ->load($pdf_document_id);

    if (!$pdf_document || !$pdf_document->isPublished()) {
      return [
        '#markup' => $this->t('PDF document not found or not published.'),
      ];
    }

    $config = \Drupal::config('pdf_embed_seo.settings');
    $height = $this->configuration['height'] ?: '600px';

    $build = [];

    if ($this->configuration['show_title']) {
      $build['title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        // Use #value with Html::escape() to properly escape user content and prevent XSS.
        '#value' => \Drupal\Component\Utility\Html::escape($pdf_document->label()),
        '#attributes' => ['class' => ['pdf-viewer-block-title']],
      ];
    }

    $build['viewer'] = [
      '#theme' => 'pdf_viewer',
      '#pdf_document' => $pdf_document,
      '#pdf_url' => Url::fromRoute('pdf_embed_seo.pdf_data', [
        'pdf_document' => $pdf_document->id(),
      ])->toString(),
      '#allow_download' => $pdf_document->allowsDownload(),
      '#allow_print' => $pdf_document->allowsPrint(),
      '#viewer_theme' => $config->get('viewer_theme') ?? 'light',
      '#height' => $height,
      '#attached' => [
        'library' => ['pdf_embed_seo/viewer'],
        'drupalSettings' => [
          'pdfEmbedSeo' => [
            'pdfUrl' => Url::fromRoute('pdf_embed_seo.pdf_data', [
              'pdf_document' => $pdf_document->id(),
            ])->toString(),
            'allowDownload' => $pdf_document->allowsDownload(),
            'allowPrint' => $pdf_document->allowsPrint(),
            'documentId' => $pdf_document->id(),
            'documentTitle' => $pdf_document->label(),
          ],
        ],
      ],
    ];

    return $build;
  }

}

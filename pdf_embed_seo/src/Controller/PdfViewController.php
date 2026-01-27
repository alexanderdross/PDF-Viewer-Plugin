<?php

namespace Drupal\pdf_embed_seo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Controller for viewing individual PDF documents.
 */
class PdfViewController extends ControllerBase {

  /**
   * View a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array
   *   A render array.
   */
  public function view(PdfDocumentInterface $pdf_document) {
    // Check if document is published.
    if (!$pdf_document->isPublished() && !$this->currentUser()->hasPermission('administer pdf embed seo')) {
      throw new NotFoundHttpException();
    }

    // Check for password protection (premium feature).
    if ($pdf_document->hasPassword() && !$this->isPasswordUnlocked($pdf_document)) {
      return $this->passwordForm($pdf_document);
    }

    $config = $this->config('pdf_embed_seo.settings');

    // Get viewer settings.
    $allow_download = $pdf_document->allowsDownload();
    $allow_print = $pdf_document->allowsPrint();
    $viewer_theme = $config->get('viewer_theme') ?? 'light';
    $viewer_height = $config->get('viewer_height') ?? '800px';

    // Build the render array.
    $build = [
      '#theme' => 'pdf_viewer',
      '#pdf_document' => $pdf_document,
      '#pdf_url' => $this->getPdfDataUrl($pdf_document),
      '#allow_download' => $allow_download,
      '#allow_print' => $allow_print,
      '#viewer_theme' => $viewer_theme,
      '#height' => $viewer_height,
      '#attached' => [
        'library' => [
          'pdf_embed_seo/viewer',
        ],
        'drupalSettings' => [
          'pdfEmbedSeo' => [
            'pdfUrl' => $this->getPdfDataUrl($pdf_document),
            'allowDownload' => $allow_download,
            'allowPrint' => $allow_print,
            'documentId' => $pdf_document->id(),
            'documentTitle' => $pdf_document->label(),
          ],
        ],
      ],
      '#cache' => [
        'tags' => $pdf_document->getCacheTags(),
        'contexts' => ['user.permissions'],
      ],
    ];

    // Add dark theme library if configured.
    if ($viewer_theme === 'dark') {
      $build['#attached']['library'][] = 'pdf_embed_seo/viewer-dark';
    }

    // Add premium features if available.
    if ($this->moduleHandler()->moduleExists('pdf_embed_seo_premium')) {
      if ($config->get('enable_search')) {
        $build['#attached']['library'][] = 'pdf_embed_seo/premium-search';
      }
      if ($config->get('enable_bookmarks')) {
        $build['#attached']['library'][] = 'pdf_embed_seo/premium-bookmarks';
      }
      if ($config->get('enable_progress')) {
        $build['#attached']['library'][] = 'pdf_embed_seo/premium-progress';
      }
      if ($config->get('enable_analytics')) {
        $build['#attached']['library'][] = 'pdf_embed_seo/premium-analytics';
      }
    }

    return $build;
  }

  /**
   * Get the title for a PDF document.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return string
   *   The title.
   */
  public function title(PdfDocumentInterface $pdf_document) {
    return $pdf_document->label();
  }

  /**
   * Display password form for protected PDF.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return array
   *   A render array.
   */
  protected function passwordForm(PdfDocumentInterface $pdf_document) {
    $form = $this->formBuilder()->getForm('Drupal\pdf_embed_seo\Form\PdfPasswordForm', $pdf_document);

    return [
      '#theme' => 'pdf_password_form',
      '#pdf_document' => $pdf_document,
      '#form' => $form,
      '#attached' => [
        'library' => ['pdf_embed_seo/premium-password'],
      ],
    ];
  }

  /**
   * Check if the PDF password has been unlocked for current session.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return bool
   *   TRUE if unlocked, FALSE otherwise.
   */
  protected function isPasswordUnlocked(PdfDocumentInterface $pdf_document) {
    // Admins can bypass password.
    if ($this->currentUser()->hasPermission('bypass pdf password')) {
      return TRUE;
    }

    $session = \Drupal::request()->getSession();
    $unlocked = $session->get('pdf_unlocked', []);

    return in_array($pdf_document->id(), $unlocked, TRUE);
  }

  /**
   * Get the PDF data URL for AJAX loading.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return string
   *   The URL to the PDF data endpoint.
   */
  protected function getPdfDataUrl(PdfDocumentInterface $pdf_document) {
    return \Drupal\Core\Url::fromRoute('pdf_embed_seo.pdf_data', [
      'pdf_document' => $pdf_document->id(),
    ])->toString();
  }

}

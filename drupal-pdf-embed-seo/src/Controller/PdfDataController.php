<?php

namespace Drupal\pdf_embed_seo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Controller for serving PDF data via AJAX.
 *
 * This controller hides the direct PDF URL from the frontend,
 * making it harder for users to discover direct download links.
 */
class PdfDataController extends ControllerBase {

  /**
   * Serve PDF data for the viewer.
   *
   * @param \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface $pdf_document
   *   The PDF document entity.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The PDF file response.
   */
  public function getData(PdfDocumentInterface $pdf_document) {
    // Check if document is published.
    if (!$pdf_document->isPublished() && !$this->currentUser()->hasPermission('administer pdf embed seo')) {
      throw new NotFoundHttpException();
    }

    // Check for password protection.
    if ($pdf_document->hasPassword() && !$this->isPasswordUnlocked($pdf_document)) {
      throw new AccessDeniedHttpException('Password required.');
    }

    // Get the PDF file.
    $file = $pdf_document->getPdfFile();
    if (!$file) {
      throw new NotFoundHttpException('PDF file not found.');
    }

    // Get the file path.
    $file_uri = $file->getFileUri();
    $file_path = \Drupal::service('file_system')->realpath($file_uri);

    if (!$file_path || !file_exists($file_path)) {
      throw new NotFoundHttpException('PDF file not accessible.');
    }

    // Increment view count.
    $pdf_document->incrementViewCount();
    $pdf_document->save();

    // Track analytics if premium is enabled.
    if ($this->moduleHandler()->moduleExists('pdf_embed_seo_premium')) {
      $config = $this->config('pdf_embed_seo.settings');
      if ($config->get('enable_analytics')) {
        \Drupal::service('pdf_embed_seo.analytics_tracker')->trackView($pdf_document);
      }
    }

    // Create response with appropriate headers.
    $response = new BinaryFileResponse($file_path);
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'inline; filename="' . $file->getFilename() . '"');

    // Set cache headers.
    $response->headers->set('Cache-Control', 'private, max-age=3600');

    // Prevent direct URL discovery.
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

    return $response;
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

}

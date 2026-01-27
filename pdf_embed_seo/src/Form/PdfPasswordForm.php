<?php

namespace Drupal\pdf_embed_seo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\pdf_embed_seo\Entity\PdfDocumentInterface;

/**
 * Form for entering PDF password.
 */
class PdfPasswordForm extends FormBase {

  /**
   * The PDF document.
   *
   * @var \Drupal\pdf_embed_seo\Entity\PdfDocumentInterface
   */
  protected $pdfDocument;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pdf_embed_seo_password_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, PdfDocumentInterface $pdf_document = NULL) {
    $this->pdfDocument = $pdf_document;

    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => $this->t('Enter password'),
        'autofocus' => 'autofocus',
      ],
    ];

    $form['pdf_document_id'] = [
      '#type' => 'hidden',
      '#value' => $pdf_document ? $pdf_document->id() : '',
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Unlock'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $pdf_document_id = $form_state->getValue('pdf_document_id');
    $password = $form_state->getValue('password');

    // Load the PDF document.
    $pdf_document = \Drupal::entityTypeManager()
      ->getStorage('pdf_document')
      ->load($pdf_document_id);

    if (!$pdf_document) {
      $form_state->setErrorByName('password', $this->t('PDF document not found.'));
      return;
    }

    // Check password.
    $stored_password = $pdf_document->getPassword();
    if ($password !== $stored_password) {
      $form_state->setErrorByName('password', $this->t('Incorrect password.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $pdf_document_id = (int) $form_state->getValue('pdf_document_id');

    // Store unlocked status in session.
    $session = \Drupal::request()->getSession();
    $unlocked = $session->get('pdf_unlocked', []);
    $unlocked[] = $pdf_document_id;
    $session->set('pdf_unlocked', array_unique($unlocked));

    // Redirect to the PDF document.
    $form_state->setRedirect('entity.pdf_document.canonical', [
      'pdf_document' => $pdf_document_id,
    ]);

    $this->messenger()->addStatus($this->t('PDF unlocked successfully.'));
  }

}

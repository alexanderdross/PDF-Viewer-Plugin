<?php

namespace Drupal\pdf_embed_seo_premium\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure PDF Embed & SEO Premium settings.
 */
class PdfPremiumSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pdf_embed_seo_premium_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['pdf_embed_seo_premium.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('pdf_embed_seo_premium.settings');
    $license_status = pdf_embed_seo_premium_get_license_status();
    $days_remaining = pdf_embed_seo_premium_get_days_remaining();

    // License section.
    $form['license'] = [
      '#type' => 'details',
      '#title' => $this->t('License'),
      '#open' => TRUE,
      '#weight' => -100,
    ];

    // Show license status message.
    $status_messages = [
      'valid' => $this->t('Your license is active.'),
      'expired' => $this->t('Your license has expired. Premium features are disabled. The plugin is running in free mode.'),
      'invalid' => $this->t('Your license key is invalid. Please check your license key.'),
      'inactive' => $this->t('Please enter your license key to activate premium features.'),
      'grace_period' => $this->t('Your license has expired! You are in the grace period. Please renew to continue using premium features.'),
    ];

    $status_class = in_array($license_status, ['valid']) ? 'messages--status' : 'messages--warning';
    if ($license_status === 'expired' || $license_status === 'invalid') {
      $status_class = 'messages--error';
    }

    $form['license']['status_message'] = [
      '#markup' => '<div class="messages ' . $status_class . '">' . ($status_messages[$license_status] ?? $status_messages['inactive']) . '</div>',
    ];

    // Show days remaining if applicable.
    if ($license_status === 'valid' && $days_remaining !== NULL && $days_remaining <= 30) {
      $form['license']['expiry_warning'] = [
        '#markup' => '<div class="messages messages--warning">' . $this->t('Your license will expire in @days days.', ['@days' => $days_remaining]) . '</div>',
      ];
    }

    $form['license']['license_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('License Key'),
      '#description' => $this->t('Enter your premium license key. Get a license at <a href="@url" target="_blank">pdfviewer.drossmedia.de</a>', ['@url' => 'https://pdfviewer.drossmedia.de']),
      '#default_value' => $config->get('license_key') ?? '',
      '#maxlength' => 64,
    ];

    $form['license']['activate_license'] = [
      '#type' => 'submit',
      '#value' => $this->t('Activate License'),
      '#submit' => ['::activateLicense'],
      '#limit_validation_errors' => [['license_key']],
    ];

    if ($license_status === 'valid' || $license_status === 'grace_period') {
      $form['license']['deactivate_license'] = [
        '#type' => 'submit',
        '#value' => $this->t('Deactivate License'),
        '#submit' => ['::deactivateLicense'],
        '#limit_validation_errors' => [],
      ];
    }

    $form['features'] = [
      '#type' => 'details',
      '#title' => $this->t('Premium Features'),
      '#open' => TRUE,
    ];

    $form['features']['enable_analytics'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Analytics Tracking'),
      '#description' => $this->t('Track detailed view statistics including user agents, referers, and timestamps.'),
      '#default_value' => $config->get('enable_analytics') ?? TRUE,
    ];

    $form['features']['enable_password_protection'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Password Protection'),
      '#description' => $this->t('Allow individual PDFs to be password protected.'),
      '#default_value' => $config->get('enable_password_protection') ?? TRUE,
    ];

    $form['features']['enable_reading_progress'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Reading Progress'),
      '#description' => $this->t('Remember and restore user reading position in PDFs.'),
      '#default_value' => $config->get('enable_reading_progress') ?? TRUE,
    ];

    $form['analytics'] = [
      '#type' => 'details',
      '#title' => $this->t('Analytics Settings'),
      '#open' => TRUE,
    ];

    $form['analytics']['analytics_retention_days'] = [
      '#type' => 'number',
      '#title' => $this->t('Data Retention Period (days)'),
      '#description' => $this->t('Number of days to keep detailed analytics data. Set to 0 for unlimited retention.'),
      '#default_value' => $config->get('analytics_retention_days') ?? 365,
      '#min' => 0,
      '#max' => 3650,
    ];

    $form['analytics']['track_anonymous'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Track Anonymous Users'),
      '#description' => $this->t('Include anonymous users in analytics tracking.'),
      '#default_value' => $config->get('track_anonymous') ?? TRUE,
    ];

    // Archive Redirect section.
    $form['redirect'] = [
      '#type' => 'details',
      '#title' => $this->t('Archive Page Redirect'),
      '#description' => $this->t('Redirect the PDF archive page (/pdf) to another URL. Useful if you want to use a custom archive page.'),
      '#open' => FALSE,
    ];

    $form['redirect']['archive_redirect_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Archive Redirect'),
      '#description' => $this->t('When enabled, visitors to the PDF archive page will be redirected to the specified URL.'),
      '#default_value' => $config->get('archive_redirect_enabled') ?? FALSE,
    ];

    $form['redirect']['archive_redirect_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Redirect Type'),
      '#description' => $this->t('301 = Permanent (SEO-friendly, cached by browsers). 302 = Temporary (not cached).'),
      '#options' => [
        '301' => $this->t('301 Permanent Redirect'),
        '302' => $this->t('302 Temporary Redirect'),
      ],
      '#default_value' => $config->get('archive_redirect_type') ?? '301',
      '#states' => [
        'visible' => [
          ':input[name="archive_redirect_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['redirect']['archive_redirect_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Redirect URL'),
      '#description' => $this->t('The URL to redirect archive visitors to. Must be a valid URL starting with http:// or https://'),
      '#default_value' => $config->get('archive_redirect_url') ?? '',
      '#states' => [
        'visible' => [
          ':input[name="archive_redirect_enabled"]' => ['checked' => TRUE],
        ],
        'required' => [
          ':input[name="archive_redirect_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['password'] = [
      '#type' => 'details',
      '#title' => $this->t('Password Protection Settings'),
      '#open' => TRUE,
    ];

    $form['password']['password_session_duration'] = [
      '#type' => 'number',
      '#title' => $this->t('Password Session Duration (seconds)'),
      '#description' => $this->t('How long a user stays authenticated after entering correct password.'),
      '#default_value' => $config->get('password_session_duration') ?? 3600,
      '#min' => 60,
      '#max' => 86400,
    ];

    $form['password']['max_password_attempts'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum Password Attempts'),
      '#description' => $this->t('Maximum number of incorrect password attempts before temporary lockout.'),
      '#default_value' => $config->get('max_password_attempts') ?? 5,
      '#min' => 1,
      '#max' => 20,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('pdf_embed_seo_premium.settings')
      ->set('enable_analytics', $form_state->getValue('enable_analytics'))
      ->set('enable_password_protection', $form_state->getValue('enable_password_protection'))
      ->set('enable_reading_progress', $form_state->getValue('enable_reading_progress'))
      ->set('analytics_retention_days', $form_state->getValue('analytics_retention_days'))
      ->set('track_anonymous', $form_state->getValue('track_anonymous'))
      ->set('password_session_duration', $form_state->getValue('password_session_duration'))
      ->set('max_password_attempts', $form_state->getValue('max_password_attempts'))
      ->set('archive_redirect_enabled', $form_state->getValue('archive_redirect_enabled'))
      ->set('archive_redirect_type', $form_state->getValue('archive_redirect_type'))
      ->set('archive_redirect_url', $form_state->getValue('archive_redirect_url'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Activate license handler.
   *
   * In production, this would validate the license key against a license server.
   * For now, it simulates license validation.
   */
  public function activateLicense(array &$form, FormStateInterface $form_state) {
    $license_key = $form_state->getValue('license_key');

    if (empty($license_key)) {
      $this->messenger()->addError($this->t('Please enter a license key.'));
      return;
    }

    // In production, this would call a license server API.
    // For now, we'll accept any key that starts with 'PDF-' and is 32+ chars.
    if (strlen($license_key) >= 32 && strpos($license_key, 'PDF-') === 0) {
      // Valid license - set expiration to 1 year from now.
      $expires = date('Y-m-d H:i:s', strtotime('+1 year'));

      $this->config('pdf_embed_seo_premium.settings')
        ->set('license_key', $license_key)
        ->set('license_status', 'valid')
        ->set('license_expires', $expires)
        ->save();

      $this->messenger()->addStatus($this->t('License activated successfully! Premium features are now enabled.'));
    }
    else {
      $this->config('pdf_embed_seo_premium.settings')
        ->set('license_key', $license_key)
        ->set('license_status', 'invalid')
        ->save();

      $this->messenger()->addError($this->t('Invalid license key. Please check your license key and try again.'));
    }
  }

  /**
   * Deactivate license handler.
   */
  public function deactivateLicense(array &$form, FormStateInterface $form_state) {
    $this->config('pdf_embed_seo_premium.settings')
      ->set('license_key', '')
      ->set('license_status', 'inactive')
      ->set('license_expires', NULL)
      ->save();

    $this->messenger()->addStatus($this->t('License deactivated. Premium features have been disabled.'));
  }

}

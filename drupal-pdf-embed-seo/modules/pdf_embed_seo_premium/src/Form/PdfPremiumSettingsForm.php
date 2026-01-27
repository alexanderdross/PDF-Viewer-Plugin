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
      ->save();

    parent::submitForm($form, $form_state);
  }

}

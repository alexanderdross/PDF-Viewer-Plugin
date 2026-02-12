<?php

namespace Drupal\pdf_embed_seo_pro_plus\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * White label branding service for Pro+ Enterprise.
 */
class WhiteLabel {

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a WhiteLabel object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Check if white label mode is enabled.
   *
   * @return bool
   *   TRUE if enabled.
   */
  public function isEnabled(): bool {
    $config = $this->configFactory->get('pdf_embed_seo_pro_plus.settings');
    return (bool) $config->get('enable_white_label');
  }

  /**
   * Get white label configuration.
   *
   * @return array
   *   White label settings.
   */
  public function getConfig(): array {
    $config = $this->configFactory->get('pdf_embed_seo_pro_plus.settings');

    return [
      'enabled' => (bool) $config->get('enable_white_label'),
      'company_name' => $config->get('white_label_company') ?? '',
      'logo_url' => $config->get('white_label_logo') ?? '',
      'favicon_url' => $config->get('white_label_favicon') ?? '',
      'primary_color' => $config->get('white_label_primary_color') ?? '#0073aa',
      'secondary_color' => $config->get('white_label_secondary_color') ?? '#23282d',
      'accent_color' => $config->get('white_label_accent_color') ?? '#00a0d2',
      'hide_branding' => (bool) $config->get('white_label_hide_branding'),
      'custom_css' => $config->get('white_label_custom_css') ?? '',
      'custom_js' => $config->get('white_label_custom_js') ?? '',
      'footer_text' => $config->get('white_label_footer') ?? '',
      'support_url' => $config->get('white_label_support_url') ?? '',
      'support_email' => $config->get('white_label_support_email') ?? '',
    ];
  }

  /**
   * Update white label configuration.
   *
   * @param array $settings
   *   Settings to update.
   *
   * @return bool
   *   TRUE on success.
   */
  public function updateConfig(array $settings): bool {
    $config = $this->configFactory->getEditable('pdf_embed_seo_pro_plus.settings');

    $allowed = [
      'enable_white_label' => 'enable_white_label',
      'company_name' => 'white_label_company',
      'logo_url' => 'white_label_logo',
      'favicon_url' => 'white_label_favicon',
      'primary_color' => 'white_label_primary_color',
      'secondary_color' => 'white_label_secondary_color',
      'accent_color' => 'white_label_accent_color',
      'hide_branding' => 'white_label_hide_branding',
      'custom_css' => 'white_label_custom_css',
      'custom_js' => 'white_label_custom_js',
      'footer_text' => 'white_label_footer',
      'support_url' => 'white_label_support_url',
      'support_email' => 'white_label_support_email',
    ];

    foreach ($settings as $key => $value) {
      if (isset($allowed[$key])) {
        $config->set($allowed[$key], $value);
      }
    }

    $config->save();
    return TRUE;
  }

  /**
   * Get CSS variables for theming.
   *
   * @return string
   *   CSS custom properties.
   */
  public function getCssVariables(): string {
    if (!$this->isEnabled()) {
      return '';
    }

    $config = $this->getConfig();

    $css = ":root {\n";
    $css .= "  --pdf-primary-color: {$config['primary_color']};\n";
    $css .= "  --pdf-secondary-color: {$config['secondary_color']};\n";
    $css .= "  --pdf-accent-color: {$config['accent_color']};\n";

    // Generate derived colors
    $css .= "  --pdf-primary-hover: " . $this->adjustBrightness($config['primary_color'], -10) . ";\n";
    $css .= "  --pdf-primary-light: " . $this->adjustBrightness($config['primary_color'], 40) . ";\n";

    $css .= "}\n";

    // Add custom CSS
    if (!empty($config['custom_css'])) {
      $css .= "\n/* Custom CSS */\n";
      $css .= $config['custom_css'];
    }

    return $css;
  }

  /**
   * Get JavaScript for white label.
   *
   * @return string
   *   JavaScript code.
   */
  public function getJavaScript(): string {
    if (!$this->isEnabled()) {
      return '';
    }

    $config = $this->getConfig();
    $js = '';

    // Replace branding if enabled
    if ($config['hide_branding']) {
      $js .= "document.querySelectorAll('.pdf-embed-seo-branding').forEach(function(el) { el.style.display = 'none'; });\n";
    }

    // Set company name
    if (!empty($config['company_name'])) {
      $company = addslashes($config['company_name']);
      $js .= "document.querySelectorAll('.pdf-viewer-title').forEach(function(el) { el.setAttribute('data-company', '{$company}'); });\n";
    }

    // Add custom JS
    if (!empty($config['custom_js'])) {
      $js .= "\n// Custom JavaScript\n";
      $js .= $config['custom_js'];
    }

    return $js;
  }

  /**
   * Get branding HTML for viewer.
   *
   * @return string
   *   HTML markup.
   */
  public function getBrandingHtml(): string {
    $config = $this->getConfig();

    if (!$this->isEnabled()) {
      return '<div class="pdf-embed-seo-branding">Powered by PDF Embed SEO</div>';
    }

    if ($config['hide_branding']) {
      return '';
    }

    $html = '<div class="pdf-viewer-branding">';

    if (!empty($config['logo_url'])) {
      $html .= '<img src="' . htmlspecialchars($config['logo_url']) . '" alt="' . htmlspecialchars($config['company_name']) . '" class="pdf-viewer-logo">';
    }
    elseif (!empty($config['company_name'])) {
      $html .= '<span class="pdf-viewer-company">' . htmlspecialchars($config['company_name']) . '</span>';
    }

    $html .= '</div>';

    return $html;
  }

  /**
   * Get footer HTML.
   *
   * @return string
   *   Footer HTML.
   */
  public function getFooterHtml(): string {
    $config = $this->getConfig();

    if (!$this->isEnabled() || empty($config['footer_text'])) {
      return '';
    }

    $html = '<div class="pdf-viewer-footer">';
    $html .= htmlspecialchars($config['footer_text']);

    if (!empty($config['support_url'])) {
      $html .= ' | <a href="' . htmlspecialchars($config['support_url']) . '" target="_blank">Support</a>';
    }

    $html .= '</div>';

    return $html;
  }

  /**
   * Adjust color brightness.
   *
   * @param string $hex
   *   Hex color code.
   * @param int $percent
   *   Percentage to adjust (-100 to 100).
   *
   * @return string
   *   Adjusted hex color.
   */
  protected function adjustBrightness(string $hex, int $percent): string {
    $hex = ltrim($hex, '#');

    if (strlen($hex) === 3) {
      $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $r = max(0, min(255, $r + ($r * $percent / 100)));
    $g = max(0, min(255, $g + ($g * $percent / 100)));
    $b = max(0, min(255, $b + ($b * $percent / 100)));

    return sprintf('#%02x%02x%02x', $r, $g, $b);
  }

  /**
   * Validate logo URL.
   *
   * @param string $url
   *   The URL to validate.
   *
   * @return bool
   *   TRUE if valid.
   */
  public function validateLogoUrl(string $url): bool {
    if (empty($url)) {
      return TRUE;
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
      return FALSE;
    }

    // Check for valid image extension
    $path = parse_url($url, PHP_URL_PATH);
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    return in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'], TRUE);
  }

  /**
   * Validate color hex code.
   *
   * @param string $color
   *   The color to validate.
   *
   * @return bool
   *   TRUE if valid.
   */
  public function validateColor(string $color): bool {
    return (bool) preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color);
  }

  /**
   * Get email template with branding.
   *
   * @param string $template_name
   *   Template name.
   * @param array $variables
   *   Template variables.
   *
   * @return string
   *   Branded email HTML.
   */
  public function getEmailTemplate(string $template_name, array $variables = []): string {
    $config = $this->getConfig();

    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family: Arial, sans-serif;">';

    // Header
    $html .= '<div style="background-color: ' . $config['primary_color'] . '; padding: 20px; text-align: center;">';
    if (!empty($config['logo_url'])) {
      $html .= '<img src="' . htmlspecialchars($config['logo_url']) . '" alt="Logo" style="max-height: 50px;">';
    }
    elseif (!empty($config['company_name'])) {
      $html .= '<h1 style="color: #fff; margin: 0;">' . htmlspecialchars($config['company_name']) . '</h1>';
    }
    $html .= '</div>';

    // Content placeholder
    $html .= '<div style="padding: 20px;">{{CONTENT}}</div>';

    // Footer
    $html .= '<div style="background-color: #f5f5f5; padding: 10px; text-align: center; font-size: 12px; color: #666;">';
    if (!empty($config['footer_text'])) {
      $html .= htmlspecialchars($config['footer_text']);
    }
    $html .= '</div>';

    $html .= '</body></html>';

    return $html;
  }

}

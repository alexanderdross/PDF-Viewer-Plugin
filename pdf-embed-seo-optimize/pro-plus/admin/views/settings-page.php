<?php
/**
 * Pro+ Settings Page
 *
 * @package    PDF_Embed_SEO_Optimize
 * @subpackage Pro_Plus
 * @since      1.3.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$settings = pdf_embed_seo_pro_plus()->get_settings();
?>

<div class="wrap pdf-settings-page">
    <h1><?php esc_html_e( 'PDF Embed & SEO Pro+ Settings', 'pdf-embed-seo-optimize' ); ?></h1>

    <form method="post" action="options.php">
        <?php settings_fields( 'pdf_embed_seo_pro_plus_settings' ); ?>

        <!-- Feature Toggles Section -->
        <div id="section-features" class="pdf-settings-section">
            <div class="pdf-settings-section-header">
                <h3>
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php esc_html_e( 'Feature Toggles', 'pdf-embed-seo-optimize' ); ?>
                </h3>
                <span class="dashicons dashicons-arrow-down pdf-settings-toggle"></span>
            </div>
            <div class="pdf-settings-section-content">
                <p class="description"><?php esc_html_e( 'Enable or disable Pro+ enterprise features.', 'pdf-embed-seo-optimize' ); ?></p>

                <div class="pdf-feature-toggles">
                    <div class="pdf-feature-toggle">
                        <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[enable_advanced_analytics]" id="enable_advanced_analytics" value="1" <?php checked( $settings['enable_advanced_analytics'] ); ?>>
                        <label for="enable_advanced_analytics">
                            <span class="feature-title"><?php esc_html_e( 'Advanced Analytics', 'pdf-embed-seo-optimize' ); ?></span>
                            <span class="feature-description"><?php esc_html_e( 'Heatmaps, engagement scoring, geographic tracking', 'pdf-embed-seo-optimize' ); ?></span>
                        </label>
                    </div>

                    <div class="pdf-feature-toggle">
                        <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[enable_security]" id="enable_security" value="1" <?php checked( $settings['enable_security'] ); ?>>
                        <label for="enable_security">
                            <span class="feature-title"><?php esc_html_e( 'Security & 2FA', 'pdf-embed-seo-optimize' ); ?></span>
                            <span class="feature-description"><?php esc_html_e( 'Two-factor authentication, IP whitelisting, audit logs', 'pdf-embed-seo-optimize' ); ?></span>
                        </label>
                    </div>

                    <div class="pdf-feature-toggle">
                        <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[enable_webhooks]" id="enable_webhooks" value="1" <?php checked( $settings['enable_webhooks'] ); ?>>
                        <label for="enable_webhooks">
                            <span class="feature-title"><?php esc_html_e( 'Webhooks', 'pdf-embed-seo-optimize' ); ?></span>
                            <span class="feature-description"><?php esc_html_e( 'External integrations via webhook notifications', 'pdf-embed-seo-optimize' ); ?></span>
                        </label>
                    </div>

                    <div class="pdf-feature-toggle">
                        <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[enable_white_label]" id="enable_white_label" value="1" <?php checked( $settings['enable_white_label'] ); ?>>
                        <label for="enable_white_label">
                            <span class="feature-title"><?php esc_html_e( 'White Label', 'pdf-embed-seo-optimize' ); ?></span>
                            <span class="feature-description"><?php esc_html_e( 'Custom branding and attribution removal', 'pdf-embed-seo-optimize' ); ?></span>
                        </label>
                    </div>

                    <div class="pdf-feature-toggle">
                        <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[enable_versioning]" id="enable_versioning" value="1" <?php checked( $settings['enable_versioning'] ); ?>>
                        <label for="enable_versioning">
                            <span class="feature-title"><?php esc_html_e( 'Document Versioning', 'pdf-embed-seo-optimize' ); ?></span>
                            <span class="feature-description"><?php esc_html_e( 'Track and restore document versions', 'pdf-embed-seo-optimize' ); ?></span>
                        </label>
                    </div>

                    <div class="pdf-feature-toggle">
                        <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[enable_annotations]" id="enable_annotations" value="1" <?php checked( $settings['enable_annotations'] ); ?>>
                        <label for="enable_annotations">
                            <span class="feature-title"><?php esc_html_e( 'Annotations & Signatures', 'pdf-embed-seo-optimize' ); ?></span>
                            <span class="feature-description"><?php esc_html_e( 'User annotations and digital signatures', 'pdf-embed-seo-optimize' ); ?></span>
                        </label>
                    </div>

                    <div class="pdf-feature-toggle">
                        <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[enable_compliance]" id="enable_compliance" value="1" <?php checked( $settings['enable_compliance'] ); ?>>
                        <label for="enable_compliance">
                            <span class="feature-title"><?php esc_html_e( 'Compliance', 'pdf-embed-seo-optimize' ); ?></span>
                            <span class="feature-description"><?php esc_html_e( 'GDPR and HIPAA compliance tools', 'pdf-embed-seo-optimize' ); ?></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Section -->
        <div id="section-analytics" class="pdf-settings-section">
            <div class="pdf-settings-section-header">
                <h3>
                    <span class="dashicons dashicons-chart-area"></span>
                    <?php esc_html_e( 'Advanced Analytics', 'pdf-embed-seo-optimize' ); ?>
                </h3>
                <span class="dashicons dashicons-arrow-down pdf-settings-toggle"></span>
            </div>
            <div class="pdf-settings-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Tracking Options', 'pdf-embed-seo-optimize' ); ?></th>
                        <td>
                            <p>
                                <label>
                                    <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[heatmaps_enabled]" value="1" <?php checked( $settings['heatmaps_enabled'] ); ?>>
                                    <?php esc_html_e( 'Enable click heatmaps', 'pdf-embed-seo-optimize' ); ?>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[engagement_scoring]" value="1" <?php checked( $settings['engagement_scoring'] ); ?>>
                                    <?php esc_html_e( 'Calculate engagement scores', 'pdf-embed-seo-optimize' ); ?>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[geographic_tracking]" value="1" <?php checked( $settings['geographic_tracking'] ); ?>>
                                    <?php esc_html_e( 'Track geographic location', 'pdf-embed-seo-optimize' ); ?>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[device_analytics]" value="1" <?php checked( $settings['device_analytics'] ); ?>>
                                    <?php esc_html_e( 'Track device information', 'pdf-embed-seo-optimize' ); ?>
                                </label>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Security Section -->
        <div id="section-security" class="pdf-settings-section">
            <div class="pdf-settings-section-header">
                <h3>
                    <span class="dashicons dashicons-shield"></span>
                    <?php esc_html_e( 'Security & Access Control', 'pdf-embed-seo-optimize' ); ?>
                </h3>
                <span class="dashicons dashicons-arrow-down pdf-settings-toggle"></span>
            </div>
            <div class="pdf-settings-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Two-Factor Authentication', 'pdf-embed-seo-optimize' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[two_factor_enabled]" value="1" <?php checked( $settings['two_factor_enabled'] ); ?>>
                                <?php esc_html_e( 'Enable 2FA for protected documents', 'pdf-embed-seo-optimize' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ip_whitelist"><?php esc_html_e( 'Global IP Whitelist', 'pdf-embed-seo-optimize' ); ?></label>
                        </th>
                        <td>
                            <textarea name="pdf_embed_seo_pro_plus_settings[ip_whitelist]" id="ip_whitelist" rows="5" class="large-text code"><?php echo esc_textarea( $settings['ip_whitelist'] ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'One IP address or CIDR range per line. Leave empty to allow all.', 'pdf-embed-seo-optimize' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="audit_log_retention"><?php esc_html_e( 'Audit Log Retention', 'pdf-embed-seo-optimize' ); ?></label>
                        </th>
                        <td>
                            <input type="number" name="pdf_embed_seo_pro_plus_settings[audit_log_retention]" id="audit_log_retention" value="<?php echo esc_attr( $settings['audit_log_retention'] ); ?>" min="7" max="365" class="small-text">
                            <?php esc_html_e( 'days', 'pdf-embed-seo-optimize' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="max_failed_attempts"><?php esc_html_e( 'Max Failed Attempts', 'pdf-embed-seo-optimize' ); ?></label>
                        </th>
                        <td>
                            <input type="number" name="pdf_embed_seo_pro_plus_settings[max_failed_attempts]" id="max_failed_attempts" value="<?php echo esc_attr( $settings['max_failed_attempts'] ); ?>" min="1" max="20" class="small-text">
                            <p class="description"><?php esc_html_e( 'Maximum password/2FA attempts before lockout.', 'pdf-embed-seo-optimize' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Webhooks Section -->
        <div id="section-webhooks" class="pdf-settings-section">
            <div class="pdf-settings-section-header">
                <h3>
                    <span class="dashicons dashicons-rest-api"></span>
                    <?php esc_html_e( 'Webhooks & Integrations', 'pdf-embed-seo-optimize' ); ?>
                </h3>
                <span class="dashicons dashicons-arrow-down pdf-settings-toggle"></span>
            </div>
            <div class="pdf-settings-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="webhook_url"><?php esc_html_e( 'Webhook URL', 'pdf-embed-seo-optimize' ); ?></label>
                        </th>
                        <td>
                            <input type="url" name="pdf_embed_seo_pro_plus_settings[webhook_url]" id="webhook_url" value="<?php echo esc_url( $settings['webhook_url'] ); ?>" class="regular-text">
                            <button type="button" class="button pdf-test-webhook"><?php esc_html_e( 'Test Webhook', 'pdf-embed-seo-optimize' ); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="webhook_secret"><?php esc_html_e( 'Webhook Secret', 'pdf-embed-seo-optimize' ); ?></label>
                        </th>
                        <td>
                            <input type="password" name="pdf_embed_seo_pro_plus_settings[webhook_secret]" id="webhook_secret" value="<?php echo esc_attr( $settings['webhook_secret'] ); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e( 'Used to sign webhook payloads for verification.', 'pdf-embed-seo-optimize' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Webhook Events', 'pdf-embed-seo-optimize' ); ?></th>
                        <td>
                            <div class="pdf-webhook-events">
                                <?php
                                $events = array(
                                    'view'             => __( 'PDF Viewed', 'pdf-embed-seo-optimize' ),
                                    'download'         => __( 'PDF Downloaded', 'pdf-embed-seo-optimize' ),
                                    'password_attempt' => __( 'Password Attempt', 'pdf-embed-seo-optimize' ),
                                    'create'           => __( 'PDF Created', 'pdf-embed-seo-optimize' ),
                                    'update'           => __( 'PDF Updated', 'pdf-embed-seo-optimize' ),
                                    'delete'           => __( 'PDF Deleted', 'pdf-embed-seo-optimize' ),
                                );
                                $selected_events = $settings['webhook_events'] ?? array();
                                foreach ( $events as $key => $label ) :
                                ?>
                                    <label>
                                        <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[webhook_events][]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $selected_events, true ) ); ?>>
                                        <?php echo esc_html( $label ); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Compliance Section -->
        <div id="section-compliance" class="pdf-settings-section">
            <div class="pdf-settings-section-header">
                <h3>
                    <span class="dashicons dashicons-privacy"></span>
                    <?php esc_html_e( 'Compliance & Data Retention', 'pdf-embed-seo-optimize' ); ?>
                </h3>
                <span class="dashicons dashicons-arrow-down pdf-settings-toggle"></span>
            </div>
            <div class="pdf-settings-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Compliance Modes', 'pdf-embed-seo-optimize' ); ?></th>
                        <td>
                            <p>
                                <label>
                                    <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[gdpr_mode]" value="1" <?php checked( $settings['gdpr_mode'] ); ?>>
                                    <?php esc_html_e( 'GDPR Mode - Enable cookie consent and data export', 'pdf-embed-seo-optimize' ); ?>
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[hipaa_mode]" value="1" <?php checked( $settings['hipaa_mode'] ); ?>>
                                    <?php esc_html_e( 'HIPAA Mode - Enhanced security for healthcare data', 'pdf-embed-seo-optimize' ); ?>
                                </label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="data_retention_days"><?php esc_html_e( 'Data Retention', 'pdf-embed-seo-optimize' ); ?></label>
                        </th>
                        <td>
                            <input type="number" name="pdf_embed_seo_pro_plus_settings[data_retention_days]" id="data_retention_days" value="<?php echo esc_attr( $settings['data_retention_days'] ); ?>" min="30" max="3650" class="small-text">
                            <?php esc_html_e( 'days', 'pdf-embed-seo-optimize' ); ?>
                            <p class="description"><?php esc_html_e( 'Analytics and tracking data older than this will be automatically deleted.', 'pdf-embed-seo-optimize' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <?php submit_button(); ?>
    </form>
</div>

<?php
/**
 * Pro+ Admin functionality.
 *
 * @package    PDF_Embed_SEO_Optimize
 * @subpackage Pro_Plus
 * @since      1.3.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Pro+ Admin class.
 *
 * @since 1.3.0
 */
class PDF_Embed_SEO_Pro_Plus_Admin {

    /**
     * Constructor.
     *
     * @since 1.3.0
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.3.0
     */
    private function init_hooks() {
        // Add Pro+ settings page.
        add_action( 'admin_menu', array( $this, 'add_settings_submenu' ), 25 );
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // Add Pro+ meta boxes.
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_pdf_document', array( $this, 'save_meta_boxes' ), 20 );

        // Admin columns.
        add_filter( 'manage_pdf_document_posts_columns', array( $this, 'add_columns' ), 15 );
        add_action( 'manage_pdf_document_posts_custom_column', array( $this, 'render_columns' ), 15, 2 );

        // Dashboard widget.
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );

        // Admin bar Pro+ indicator.
        add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_item' ), 100 );
    }

    /**
     * Add settings submenu.
     *
     * @since 1.3.0
     */
    public function add_settings_submenu() {
        add_submenu_page(
            'edit.php?post_type=pdf_document',
            __( 'Pro+ Settings', 'pdf-embed-seo-optimize' ),
            __( 'Pro+ Settings', 'pdf-embed-seo-optimize' ),
            'manage_options',
            'pdf-pro-plus-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Register settings.
     *
     * @since 1.3.0
     */
    public function register_settings() {
        register_setting( 'pdf_embed_seo_pro_plus_settings', 'pdf_embed_seo_pro_plus_settings', array(
            'type'              => 'array',
            'sanitize_callback' => array( $this, 'sanitize_settings' ),
        ) );

        // Feature toggles section.
        add_settings_section(
            'pdf_pro_plus_features',
            __( 'Feature Toggles', 'pdf-embed-seo-optimize' ),
            array( $this, 'render_features_section' ),
            'pdf-pro-plus-settings'
        );

        // Advanced Analytics section.
        add_settings_section(
            'pdf_pro_plus_analytics',
            __( 'Advanced Analytics', 'pdf-embed-seo-optimize' ),
            array( $this, 'render_analytics_section' ),
            'pdf-pro-plus-settings'
        );

        // Security section.
        add_settings_section(
            'pdf_pro_plus_security',
            __( 'Security & Access Control', 'pdf-embed-seo-optimize' ),
            array( $this, 'render_security_section' ),
            'pdf-pro-plus-settings'
        );

        // Webhooks section.
        add_settings_section(
            'pdf_pro_plus_webhooks',
            __( 'Webhooks & Integrations', 'pdf-embed-seo-optimize' ),
            array( $this, 'render_webhooks_section' ),
            'pdf-pro-plus-settings'
        );

        // White Label section.
        add_settings_section(
            'pdf_pro_plus_white_label',
            __( 'White Label & Branding', 'pdf-embed-seo-optimize' ),
            array( $this, 'render_white_label_section' ),
            'pdf-pro-plus-settings'
        );

        // Compliance section.
        add_settings_section(
            'pdf_pro_plus_compliance',
            __( 'Compliance & Data Retention', 'pdf-embed-seo-optimize' ),
            array( $this, 'render_compliance_section' ),
            'pdf-pro-plus-settings'
        );
    }

    /**
     * Sanitize settings.
     *
     * @since 1.3.0
     * @param array $input Input values.
     * @return array
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();

        // Feature toggles.
        $sanitized['enable_advanced_analytics'] = ! empty( $input['enable_advanced_analytics'] );
        $sanitized['enable_security']           = ! empty( $input['enable_security'] );
        $sanitized['enable_webhooks']           = ! empty( $input['enable_webhooks'] );
        $sanitized['enable_white_label']        = ! empty( $input['enable_white_label'] );
        $sanitized['enable_versioning']         = ! empty( $input['enable_versioning'] );
        $sanitized['enable_annotations']        = ! empty( $input['enable_annotations'] );
        $sanitized['enable_compliance']         = ! empty( $input['enable_compliance'] );

        // Advanced Analytics.
        $sanitized['heatmaps_enabled']     = ! empty( $input['heatmaps_enabled'] );
        $sanitized['engagement_scoring']   = ! empty( $input['engagement_scoring'] );
        $sanitized['geographic_tracking']  = ! empty( $input['geographic_tracking'] );
        $sanitized['device_analytics']     = ! empty( $input['device_analytics'] );

        // Security.
        $sanitized['two_factor_enabled']   = ! empty( $input['two_factor_enabled'] );
        $sanitized['ip_whitelist']         = sanitize_textarea_field( $input['ip_whitelist'] ?? '' );
        $sanitized['audit_log_retention']  = absint( $input['audit_log_retention'] ?? 90 );
        $sanitized['max_failed_attempts']  = absint( $input['max_failed_attempts'] ?? 5 );

        // Webhooks.
        $sanitized['webhook_url']    = esc_url_raw( $input['webhook_url'] ?? '' );
        $sanitized['webhook_secret'] = sanitize_text_field( $input['webhook_secret'] ?? '' );
        $sanitized['webhook_events'] = isset( $input['webhook_events'] ) && is_array( $input['webhook_events'] )
            ? array_map( 'sanitize_text_field', $input['webhook_events'] )
            : array();

        // White Label.
        $sanitized['custom_branding']  = ! empty( $input['custom_branding'] );
        $sanitized['hide_powered_by']  = ! empty( $input['hide_powered_by'] );
        $sanitized['custom_logo_url']  = esc_url_raw( $input['custom_logo_url'] ?? '' );
        $sanitized['custom_css']       = wp_strip_all_tags( $input['custom_css'] ?? '' );

        // Versioning.
        $sanitized['keep_versions'] = absint( $input['keep_versions'] ?? 10 );
        $sanitized['auto_version']  = ! empty( $input['auto_version'] );

        // Annotations.
        $sanitized['allow_user_annotations'] = ! empty( $input['allow_user_annotations'] );
        $sanitized['signature_enabled']      = ! empty( $input['signature_enabled'] );

        // Compliance.
        $sanitized['gdpr_mode']           = ! empty( $input['gdpr_mode'] );
        $sanitized['hipaa_mode']          = ! empty( $input['hipaa_mode'] );
        $sanitized['data_retention_days'] = absint( $input['data_retention_days'] ?? 365 );

        return $sanitized;
    }

    /**
     * Render settings page.
     *
     * @since 1.3.0
     */
    public function render_settings_page() {
        include PDF_EMBED_SEO_PRO_PLUS_DIR . 'admin/views/settings-page.php';
    }

    /**
     * Render features section description.
     *
     * @since 1.3.0
     */
    public function render_features_section() {
        echo '<p>' . esc_html__( 'Enable or disable Pro+ enterprise features.', 'pdf-embed-seo-optimize' ) . '</p>';
    }

    /**
     * Render analytics section description.
     *
     * @since 1.3.0
     */
    public function render_analytics_section() {
        echo '<p>' . esc_html__( 'Configure advanced analytics and engagement tracking.', 'pdf-embed-seo-optimize' ) . '</p>';
    }

    /**
     * Render security section description.
     *
     * @since 1.3.0
     */
    public function render_security_section() {
        echo '<p>' . esc_html__( 'Configure security features including 2FA, IP whitelisting, and audit logs.', 'pdf-embed-seo-optimize' ) . '</p>';
    }

    /**
     * Render webhooks section description.
     *
     * @since 1.3.0
     */
    public function render_webhooks_section() {
        echo '<p>' . esc_html__( 'Configure webhooks for external integrations.', 'pdf-embed-seo-optimize' ) . '</p>';
    }

    /**
     * Render white label section description.
     *
     * @since 1.3.0
     */
    public function render_white_label_section() {
        echo '<p>' . esc_html__( 'Customize branding and remove plugin attribution.', 'pdf-embed-seo-optimize' ) . '</p>';
    }

    /**
     * Render compliance section description.
     *
     * @since 1.3.0
     */
    public function render_compliance_section() {
        echo '<p>' . esc_html__( 'Configure GDPR, HIPAA, and data retention settings.', 'pdf-embed-seo-optimize' ) . '</p>';
    }

    /**
     * Add meta boxes.
     *
     * @since 1.3.0
     */
    public function add_meta_boxes() {
        // Version history meta box.
        add_meta_box(
            'pdf_pro_plus_versions',
            __( 'Version History', 'pdf-embed-seo-optimize' ) . ' <span class="pdf-pro-plus-badge">PRO+</span>',
            array( $this, 'render_versions_meta_box' ),
            'pdf_document',
            'side',
            'default'
        );

        // Security settings meta box.
        add_meta_box(
            'pdf_pro_plus_security',
            __( 'Security Settings', 'pdf-embed-seo-optimize' ) . ' <span class="pdf-pro-plus-badge">PRO+</span>',
            array( $this, 'render_security_meta_box' ),
            'pdf_document',
            'side',
            'default'
        );

        // Engagement score meta box.
        add_meta_box(
            'pdf_pro_plus_engagement',
            __( 'Engagement Score', 'pdf-embed-seo-optimize' ) . ' <span class="pdf-pro-plus-badge">PRO+</span>',
            array( $this, 'render_engagement_meta_box' ),
            'pdf_document',
            'side',
            'default'
        );
    }

    /**
     * Render versions meta box.
     *
     * @since 1.3.0
     * @param WP_Post $post Post object.
     */
    public function render_versions_meta_box( $post ) {
        $pro_plus = pdf_embed_seo_pro_plus();
        if ( $pro_plus->versioning ) {
            $pro_plus->versioning->render_meta_box( $post );
        } else {
            echo '<p>' . esc_html__( 'Versioning is disabled. Enable it in Pro+ Settings.', 'pdf-embed-seo-optimize' ) . '</p>';
        }
    }

    /**
     * Render security meta box.
     *
     * @since 1.3.0
     * @param WP_Post $post Post object.
     */
    public function render_security_meta_box( $post ) {
        wp_nonce_field( 'pdf_pro_plus_security', 'pdf_pro_plus_security_nonce' );

        $two_fa_required = get_post_meta( $post->ID, '_pdf_pro_plus_2fa_required', true );
        $ip_whitelist    = get_post_meta( $post->ID, '_pdf_pro_plus_ip_whitelist', true );
        ?>
        <p>
            <label>
                <input type="checkbox" name="pdf_pro_plus_2fa_required" value="1" <?php checked( $two_fa_required, '1' ); ?>>
                <?php esc_html_e( 'Require 2FA to access', 'pdf-embed-seo-optimize' ); ?>
            </label>
        </p>
        <p>
            <label for="pdf_pro_plus_ip_whitelist"><?php esc_html_e( 'IP Whitelist (one per line):', 'pdf-embed-seo-optimize' ); ?></label>
            <textarea name="pdf_pro_plus_ip_whitelist" id="pdf_pro_plus_ip_whitelist" class="widefat" rows="3"><?php echo esc_textarea( $ip_whitelist ); ?></textarea>
            <span class="description"><?php esc_html_e( 'Leave empty to allow all IPs.', 'pdf-embed-seo-optimize' ); ?></span>
        </p>
        <?php
    }

    /**
     * Render engagement meta box.
     *
     * @since 1.3.0
     * @param WP_Post $post Post object.
     */
    public function render_engagement_meta_box( $post ) {
        $pro_plus = pdf_embed_seo_pro_plus();
        if ( $pro_plus->advanced_analytics ) {
            $score = $pro_plus->advanced_analytics->get_engagement_score( $post->ID );
            $trend = $pro_plus->advanced_analytics->get_engagement_trend( $post->ID );
            ?>
            <div class="pdf-engagement-score">
                <div class="score-value" style="font-size: 48px; font-weight: bold; text-align: center; color: <?php echo esc_attr( $this->get_score_color( $score ) ); ?>;">
                    <?php echo esc_html( $score !== null ? $score : '-' ); ?>
                </div>
                <div class="score-label" style="text-align: center; color: #666;">
                    <?php esc_html_e( 'out of 100', 'pdf-embed-seo-optimize' ); ?>
                </div>
                <?php if ( $trend ) : ?>
                <div class="score-trend" style="text-align: center; margin-top: 10px;">
                    <span class="trend-icon" style="color: <?php echo $trend > 0 ? '#46b450' : '#dc3232'; ?>;">
                        <?php echo $trend > 0 ? '&#9650;' : '&#9660;'; ?>
                    </span>
                    <span class="trend-value">
                        <?php echo esc_html( abs( $trend ) ); ?>% <?php esc_html_e( 'from last week', 'pdf-embed-seo-optimize' ); ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
            <?php
        } else {
            echo '<p>' . esc_html__( 'Advanced Analytics is disabled. Enable it in Pro+ Settings.', 'pdf-embed-seo-optimize' ) . '</p>';
        }
    }

    /**
     * Get score color.
     *
     * @since 1.3.0
     * @param int|null $score Score value.
     * @return string
     */
    private function get_score_color( $score ) {
        if ( $score === null ) {
            return '#999';
        }
        if ( $score >= 70 ) {
            return '#46b450';
        }
        if ( $score >= 40 ) {
            return '#ffb900';
        }
        return '#dc3232';
    }

    /**
     * Save meta boxes.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     */
    public function save_meta_boxes( $post_id ) {
        // Check nonce.
        if ( ! isset( $_POST['pdf_pro_plus_security_nonce'] ) ||
             ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pdf_pro_plus_security_nonce'] ) ), 'pdf_pro_plus_security' ) ) {
            return;
        }

        // Check permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save security settings.
        $two_fa = isset( $_POST['pdf_pro_plus_2fa_required'] ) ? '1' : '';
        update_post_meta( $post_id, '_pdf_pro_plus_2fa_required', $two_fa );

        $ip_whitelist = isset( $_POST['pdf_pro_plus_ip_whitelist'] )
            ? sanitize_textarea_field( wp_unslash( $_POST['pdf_pro_plus_ip_whitelist'] ) )
            : '';
        update_post_meta( $post_id, '_pdf_pro_plus_ip_whitelist', $ip_whitelist );
    }

    /**
     * Add columns.
     *
     * @since 1.3.0
     * @param array $columns Columns.
     * @return array
     */
    public function add_columns( $columns ) {
        $new_columns = array();

        foreach ( $columns as $key => $value ) {
            $new_columns[ $key ] = $value;

            // Add engagement column after views.
            if ( 'pdf_views' === $key ) {
                $new_columns['pdf_engagement'] = __( 'Engagement', 'pdf-embed-seo-optimize' );
            }
        }

        return $new_columns;
    }

    /**
     * Render columns.
     *
     * @since 1.3.0
     * @param string $column  Column name.
     * @param int    $post_id Post ID.
     */
    public function render_columns( $column, $post_id ) {
        if ( 'pdf_engagement' === $column ) {
            $pro_plus = pdf_embed_seo_pro_plus();
            if ( $pro_plus->advanced_analytics ) {
                $score = $pro_plus->advanced_analytics->get_engagement_score( $post_id );
                if ( $score !== null ) {
                    $color = $this->get_score_color( $score );
                    echo '<span style="font-weight: bold; color: ' . esc_attr( $color ) . ';">' . esc_html( $score ) . '</span>';
                } else {
                    echo '<span style="color: #999;">-</span>';
                }
            } else {
                echo '<span style="color: #999;">-</span>';
            }
        }
    }

    /**
     * Add dashboard widget.
     *
     * @since 1.3.0
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'pdf_pro_plus_overview',
            __( 'PDF Pro+ Overview', 'pdf-embed-seo-optimize' ),
            array( $this, 'render_dashboard_widget' )
        );
    }

    /**
     * Render dashboard widget.
     *
     * @since 1.3.0
     */
    public function render_dashboard_widget() {
        $pro_plus = pdf_embed_seo_pro_plus();
        ?>
        <div class="pdf-pro-plus-dashboard">
            <div class="pro-plus-stats">
                <div class="stat-item">
                    <span class="stat-value">
                        <?php
                        $pdf_count = wp_count_posts( 'pdf_document' );
                        echo esc_html( $pdf_count->publish ?? 0 );
                        ?>
                    </span>
                    <span class="stat-label"><?php esc_html_e( 'Total PDFs', 'pdf-embed-seo-optimize' ); ?></span>
                </div>
                <?php if ( $pro_plus->advanced_analytics ) : ?>
                <div class="stat-item">
                    <span class="stat-value">
                        <?php echo esc_html( $pro_plus->advanced_analytics->get_total_views_today() ); ?>
                    </span>
                    <span class="stat-label"><?php esc_html_e( 'Views Today', 'pdf-embed-seo-optimize' ); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">
                        <?php echo esc_html( $pro_plus->advanced_analytics->get_avg_engagement_score() ); ?>
                    </span>
                    <span class="stat-label"><?php esc_html_e( 'Avg. Engagement', 'pdf-embed-seo-optimize' ); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <p style="margin-top: 15px;">
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-analytics' ) ); ?>" class="button">
                    <?php esc_html_e( 'View Full Analytics', 'pdf-embed-seo-optimize' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-pro-plus-settings' ) ); ?>" class="button">
                    <?php esc_html_e( 'Pro+ Settings', 'pdf-embed-seo-optimize' ); ?>
                </a>
            </p>
        </div>
        <style>
            .pdf-pro-plus-dashboard .pro-plus-stats {
                display: flex;
                gap: 20px;
                margin-bottom: 15px;
            }
            .pdf-pro-plus-dashboard .stat-item {
                text-align: center;
                flex: 1;
            }
            .pdf-pro-plus-dashboard .stat-value {
                display: block;
                font-size: 28px;
                font-weight: bold;
                color: #764ba2;
            }
            .pdf-pro-plus-dashboard .stat-label {
                display: block;
                font-size: 12px;
                color: #666;
            }
        </style>
        <?php
    }

    /**
     * Add admin bar item.
     *
     * @since 1.3.0
     * @param WP_Admin_Bar $admin_bar Admin bar instance.
     */
    public function add_admin_bar_item( $admin_bar ) {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        $admin_bar->add_node( array(
            'id'     => 'pdf-pro-plus',
            'title'  => '<span class="ab-icon dashicons dashicons-pdf"></span> PDF Pro+',
            'href'   => admin_url( 'edit.php?post_type=pdf_document&page=pdf-pro-plus-settings' ),
            'meta'   => array(
                'title' => __( 'PDF Embed & SEO Pro+ Settings', 'pdf-embed-seo-optimize' ),
            ),
        ) );

        $admin_bar->add_node( array(
            'parent' => 'pdf-pro-plus',
            'id'     => 'pdf-pro-plus-analytics',
            'title'  => __( 'Advanced Analytics', 'pdf-embed-seo-optimize' ),
            'href'   => admin_url( 'edit.php?post_type=pdf_document&page=pdf-analytics' ),
        ) );

        $admin_bar->add_node( array(
            'parent' => 'pdf-pro-plus',
            'id'     => 'pdf-pro-plus-webhooks',
            'title'  => __( 'Webhooks', 'pdf-embed-seo-optimize' ),
            'href'   => admin_url( 'edit.php?post_type=pdf_document&page=pdf-pro-plus-settings#webhooks' ),
        ) );

        $admin_bar->add_node( array(
            'parent' => 'pdf-pro-plus',
            'id'     => 'pdf-pro-plus-compliance',
            'title'  => __( 'Compliance', 'pdf-embed-seo-optimize' ),
            'href'   => admin_url( 'edit.php?post_type=pdf_document&page=pdf-pro-plus-settings#compliance' ),
        ) );
    }
}

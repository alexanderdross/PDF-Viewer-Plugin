<?php
/**
 * Pro+ Compliance - GDPR, HIPAA, Data Retention.
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
 * Compliance class.
 *
 * @since 1.3.0
 */
class PDF_Embed_SEO_Pro_Plus_Compliance {

    /**
     * Consent log table name.
     *
     * @var string
     */
    private $consent_table;

    /**
     * Data export log table name.
     *
     * @var string
     */
    private $export_table;

    /**
     * Constructor.
     *
     * @since 1.3.0
     */
    public function __construct() {
        global $wpdb;
        $this->consent_table = $wpdb->prefix . 'pdf_consent_log';
        $this->export_table  = $wpdb->prefix . 'pdf_data_exports';

        $this->init_hooks();
        $this->maybe_create_tables();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.3.0
     */
    private function init_hooks() {
        // GDPR data export.
        add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_data_exporter' ) );

        // GDPR data erasure.
        add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_data_eraser' ) );

        // Cookie consent check.
        add_action( 'template_redirect', array( $this, 'check_consent' ) );

        // Consent form submission.
        add_action( 'wp_ajax_pdf_record_consent', array( $this, 'ajax_record_consent' ) );
        add_action( 'wp_ajax_nopriv_pdf_record_consent', array( $this, 'ajax_record_consent' ) );

        // Data retention cleanup.
        add_action( 'pdf_pro_plus_data_retention_cleanup', array( $this, 'cleanup_expired_data' ) );

        if ( ! wp_next_scheduled( 'pdf_pro_plus_data_retention_cleanup' ) ) {
            wp_schedule_event( time(), 'daily', 'pdf_pro_plus_data_retention_cleanup' );
        }

        // Admin notices for compliance.
        add_action( 'admin_notices', array( $this, 'compliance_notices' ) );

        // Enqueue consent scripts.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_consent_scripts' ) );

        // REST API endpoints.
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    /**
     * Create database tables.
     *
     * @since 1.3.0
     */
    private function maybe_create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Consent log table.
        $sql_consent = "CREATE TABLE IF NOT EXISTS {$this->consent_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            session_id varchar(64) NOT NULL,
            consent_type varchar(50) NOT NULL,
            consent_given tinyint(1) NOT NULL DEFAULT 0,
            consent_text text,
            ip_address varchar(45),
            user_agent varchar(500),
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY session_id (session_id),
            KEY consent_type (consent_type)
        ) {$charset_collate};";

        // Data export log table.
        $sql_export = "CREATE TABLE IF NOT EXISTS {$this->export_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            requested_by bigint(20) NOT NULL,
            export_type varchar(50) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            file_path varchar(500),
            created_at datetime NOT NULL,
            completed_at datetime DEFAULT NULL,
            expires_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY status (status)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_consent );
        dbDelta( $sql_export );
    }

    /**
     * Check if GDPR mode is enabled.
     *
     * @since 1.3.0
     * @return bool
     */
    public function is_gdpr_enabled() {
        $settings = pdf_embed_seo_pro_plus()->get_settings();
        return ! empty( $settings['gdpr_mode'] );
    }

    /**
     * Check if HIPAA mode is enabled.
     *
     * @since 1.3.0
     * @return bool
     */
    public function is_hipaa_enabled() {
        $settings = pdf_embed_seo_pro_plus()->get_settings();
        return ! empty( $settings['hipaa_mode'] );
    }

    /**
     * Get data retention days.
     *
     * @since 1.3.0
     * @return int
     */
    public function get_retention_days() {
        $settings = pdf_embed_seo_pro_plus()->get_settings();
        return absint( $settings['data_retention_days'] ?? 365 );
    }

    /**
     * Check consent on PDF pages.
     *
     * @since 1.3.0
     */
    public function check_consent() {
        if ( ! is_singular( 'pdf_document' ) ) {
            return;
        }

        if ( ! $this->is_gdpr_enabled() ) {
            return;
        }

        // Check if consent has been given.
        if ( $this->has_consent( 'analytics' ) ) {
            return;
        }

        // Add consent banner.
        add_action( 'wp_footer', array( $this, 'render_consent_banner' ) );
    }

    /**
     * Check if user has given consent.
     *
     * @since 1.3.0
     * @param string $consent_type Consent type.
     * @return bool
     */
    public function has_consent( $consent_type ) {
        // Check cookie first.
        $cookie_name = 'pdf_consent_' . $consent_type;

        if ( isset( $_COOKIE[ $cookie_name ] ) && $_COOKIE[ $cookie_name ] === 'yes' ) {
            return true;
        }

        // Check database for logged-in users.
        if ( is_user_logged_in() ) {
            global $wpdb;

            $consent = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT consent_given FROM {$this->consent_table}
                    WHERE user_id = %d AND consent_type = %s
                    ORDER BY created_at DESC LIMIT 1",
                    get_current_user_id(),
                    $consent_type
                )
            );

            return (bool) $consent;
        }

        return false;
    }

    /**
     * Record consent.
     *
     * @since 1.3.0
     * @param string $consent_type Consent type.
     * @param bool   $given        Whether consent was given.
     * @param string $consent_text Consent text shown.
     * @return bool
     */
    public function record_consent( $consent_type, $given, $consent_text = '' ) {
        global $wpdb;

        $session_id = $this->get_session_id();

        $wpdb->insert(
            $this->consent_table,
            array(
                'user_id'       => get_current_user_id() ?: null,
                'session_id'    => $session_id,
                'consent_type'  => $consent_type,
                'consent_given' => $given ? 1 : 0,
                'consent_text'  => $consent_text,
                'ip_address'    => $this->anonymize_ip( $this->get_client_ip() ),
                'user_agent'    => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 500 ) : null,
                'created_at'    => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
        );

        // Set cookie.
        $cookie_name = 'pdf_consent_' . $consent_type;
        setcookie( $cookie_name, $given ? 'yes' : 'no', time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );

        return $wpdb->insert_id > 0;
    }

    /**
     * Enqueue consent scripts.
     *
     * @since 1.3.0
     */
    public function enqueue_consent_scripts() {
        if ( ! is_singular( 'pdf_document' ) ) {
            return;
        }

        if ( ! $this->is_gdpr_enabled() ) {
            return;
        }

        wp_enqueue_script(
            'pdf-pro-plus-consent',
            PDF_EMBED_SEO_PRO_PLUS_URL . 'public/js/consent.js',
            array( 'jquery' ),
            PDF_EMBED_SEO_PRO_PLUS_VERSION,
            true
        );

        wp_localize_script( 'pdf-pro-plus-consent', 'pdfConsent', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'pdf_consent' ),
            'i18n'    => array(
                'accept'   => __( 'Accept', 'pdf-embed-seo-optimize' ),
                'decline'  => __( 'Decline', 'pdf-embed-seo-optimize' ),
                'settings' => __( 'Cookie Settings', 'pdf-embed-seo-optimize' ),
            ),
        ) );
    }

    /**
     * Render consent banner.
     *
     * @since 1.3.0
     */
    public function render_consent_banner() {
        $privacy_policy_url = get_privacy_policy_url();
        ?>
        <div id="pdf-consent-banner" class="pdf-consent-banner" style="display: none;">
            <div class="consent-content">
                <p>
                    <?php esc_html_e( 'We use cookies to track your reading progress and provide analytics. By using this site, you consent to our use of cookies.', 'pdf-embed-seo-optimize' ); ?>
                    <?php if ( $privacy_policy_url ) : ?>
                        <a href="<?php echo esc_url( $privacy_policy_url ); ?>" target="_blank"><?php esc_html_e( 'Learn more', 'pdf-embed-seo-optimize' ); ?></a>
                    <?php endif; ?>
                </p>
                <div class="consent-actions">
                    <button type="button" class="consent-accept"><?php esc_html_e( 'Accept All', 'pdf-embed-seo-optimize' ); ?></button>
                    <button type="button" class="consent-decline"><?php esc_html_e( 'Decline', 'pdf-embed-seo-optimize' ); ?></button>
                    <button type="button" class="consent-settings"><?php esc_html_e( 'Customize', 'pdf-embed-seo-optimize' ); ?></button>
                </div>
            </div>
        </div>
        <style>
            .pdf-consent-banner {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #2c3e50;
                color: #fff;
                padding: 20px;
                z-index: 99999;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.2);
            }
            .pdf-consent-banner .consent-content {
                max-width: 1200px;
                margin: 0 auto;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
            }
            .pdf-consent-banner p {
                margin: 0;
                flex: 1;
            }
            .pdf-consent-banner a {
                color: #3498db;
            }
            .pdf-consent-banner .consent-actions {
                display: flex;
                gap: 10px;
            }
            .pdf-consent-banner button {
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-weight: bold;
            }
            .pdf-consent-banner .consent-accept {
                background: #27ae60;
                color: #fff;
            }
            .pdf-consent-banner .consent-decline {
                background: #e74c3c;
                color: #fff;
            }
            .pdf-consent-banner .consent-settings {
                background: #34495e;
                color: #fff;
            }
            @media (max-width: 768px) {
                .pdf-consent-banner .consent-content {
                    flex-direction: column;
                    text-align: center;
                }
            }
        </style>
        <?php
    }

    /**
     * AJAX: Record consent.
     *
     * @since 1.3.0
     */
    public function ajax_record_consent() {
        check_ajax_referer( 'pdf_consent', 'nonce' );

        $consent_type = isset( $_POST['consent_type'] ) ? sanitize_text_field( wp_unslash( $_POST['consent_type'] ) ) : 'analytics';
        $given        = isset( $_POST['given'] ) && $_POST['given'] === 'true';

        if ( $this->record_consent( $consent_type, $given ) ) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    /**
     * Register data exporter for WordPress privacy tools.
     *
     * @since 1.3.0
     * @param array $exporters Registered exporters.
     * @return array
     */
    public function register_data_exporter( $exporters ) {
        $exporters['pdf-embed-seo-pro-plus'] = array(
            'exporter_friendly_name' => __( 'PDF Embed & SEO Pro+', 'pdf-embed-seo-optimize' ),
            'callback'               => array( $this, 'export_personal_data' ),
        );
        return $exporters;
    }

    /**
     * Export personal data.
     *
     * @since 1.3.0
     * @param string $email_address Email address.
     * @param int    $page          Page number.
     * @return array
     */
    public function export_personal_data( $email_address, $page = 1 ) {
        global $wpdb;

        $user = get_user_by( 'email', $email_address );

        if ( ! $user ) {
            return array(
                'data' => array(),
                'done' => true,
            );
        }

        $data = array();

        // Export consent records.
        $consents = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->consent_table} WHERE user_id = %d",
                $user->ID
            )
        );

        if ( $consents ) {
            $consent_data = array();
            foreach ( $consents as $consent ) {
                $consent_data[] = array(
                    'name'  => __( 'Consent Record', 'pdf-embed-seo-optimize' ),
                    'value' => sprintf(
                        /* translators: 1: Consent type, 2: Given/Not given, 3: Date */
                        __( 'Type: %1$s, Given: %2$s, Date: %3$s', 'pdf-embed-seo-optimize' ),
                        $consent->consent_type,
                        $consent->consent_given ? __( 'Yes', 'pdf-embed-seo-optimize' ) : __( 'No', 'pdf-embed-seo-optimize' ),
                        $consent->created_at
                    ),
                );
            }

            $data[] = array(
                'group_id'          => 'pdf-consent',
                'group_label'       => __( 'PDF Consent Records', 'pdf-embed-seo-optimize' ),
                'group_description' => __( 'Your consent preferences for PDF tracking.', 'pdf-embed-seo-optimize' ),
                'item_id'           => 'consent-' . $user->ID,
                'data'              => $consent_data,
            );
        }

        // Export analytics data.
        $analytics_table = $wpdb->prefix . 'pdf_advanced_analytics';

        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$analytics_table}'" ) === $analytics_table ) {
            $analytics = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT post_id, page_number, time_on_page, created_at FROM {$analytics_table} WHERE user_id = %d",
                    $user->ID
                )
            );

            if ( $analytics ) {
                $analytics_data = array();
                foreach ( $analytics as $record ) {
                    $post = get_post( $record->post_id );
                    $analytics_data[] = array(
                        'name'  => __( 'PDF View', 'pdf-embed-seo-optimize' ),
                        'value' => sprintf(
                            /* translators: 1: Document title, 2: Page number, 3: Time, 4: Date */
                            __( 'Document: %1$s, Page: %2$d, Time: %3$ds, Date: %4$s', 'pdf-embed-seo-optimize' ),
                            $post ? $post->post_title : __( 'Unknown', 'pdf-embed-seo-optimize' ),
                            $record->page_number,
                            $record->time_on_page,
                            $record->created_at
                        ),
                    );
                }

                $data[] = array(
                    'group_id'          => 'pdf-analytics',
                    'group_label'       => __( 'PDF Reading Analytics', 'pdf-embed-seo-optimize' ),
                    'group_description' => __( 'Your PDF reading activity.', 'pdf-embed-seo-optimize' ),
                    'item_id'           => 'analytics-' . $user->ID,
                    'data'              => $analytics_data,
                );
            }
        }

        return array(
            'data' => $data,
            'done' => true,
        );
    }

    /**
     * Register data eraser for WordPress privacy tools.
     *
     * @since 1.3.0
     * @param array $erasers Registered erasers.
     * @return array
     */
    public function register_data_eraser( $erasers ) {
        $erasers['pdf-embed-seo-pro-plus'] = array(
            'eraser_friendly_name' => __( 'PDF Embed & SEO Pro+', 'pdf-embed-seo-optimize' ),
            'callback'             => array( $this, 'erase_personal_data' ),
        );
        return $erasers;
    }

    /**
     * Erase personal data.
     *
     * @since 1.3.0
     * @param string $email_address Email address.
     * @param int    $page          Page number.
     * @return array
     */
    public function erase_personal_data( $email_address, $page = 1 ) {
        global $wpdb;

        $user = get_user_by( 'email', $email_address );

        if ( ! $user ) {
            return array(
                'items_removed'  => 0,
                'items_retained' => false,
                'messages'       => array(),
                'done'           => true,
            );
        }

        $items_removed = 0;

        // Erase consent records.
        $deleted = $wpdb->delete(
            $this->consent_table,
            array( 'user_id' => $user->ID ),
            array( '%d' )
        );
        $items_removed += $deleted ?: 0;

        // Erase analytics data.
        $analytics_table = $wpdb->prefix . 'pdf_advanced_analytics';

        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$analytics_table}'" ) === $analytics_table ) {
            $deleted = $wpdb->delete(
                $analytics_table,
                array( 'user_id' => $user->ID ),
                array( '%d' )
            );
            $items_removed += $deleted ?: 0;
        }

        // Erase annotations.
        $annotations_table = $wpdb->prefix . 'pdf_annotations';

        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$annotations_table}'" ) === $annotations_table ) {
            $deleted = $wpdb->delete(
                $annotations_table,
                array( 'user_id' => $user->ID ),
                array( '%d' )
            );
            $items_removed += $deleted ?: 0;
        }

        // Erase audit logs.
        $audit_table = $wpdb->prefix . 'pdf_audit_log';

        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$audit_table}'" ) === $audit_table ) {
            $deleted = $wpdb->delete(
                $audit_table,
                array( 'user_id' => $user->ID ),
                array( '%d' )
            );
            $items_removed += $deleted ?: 0;
        }

        return array(
            'items_removed'  => $items_removed,
            'items_retained' => false,
            'messages'       => array(),
            'done'           => true,
        );
    }

    /**
     * Cleanup expired data based on retention policy.
     *
     * @since 1.3.0
     */
    public function cleanup_expired_data() {
        global $wpdb;

        $retention_days = $this->get_retention_days();

        // Cleanup analytics.
        $analytics_table = $wpdb->prefix . 'pdf_advanced_analytics';

        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$analytics_table}'" ) === $analytics_table ) {
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$analytics_table} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
                    $retention_days
                )
            );
        }

        // Cleanup consent logs (keep for longer for compliance).
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$this->consent_table} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
                $retention_days * 2
            )
        );

        // Cleanup old data exports.
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$this->export_table} WHERE expires_at IS NOT NULL AND expires_at < NOW()"
            )
        );

        /**
         * Action fired after data cleanup.
         *
         * @since 1.3.0
         * @param int $retention_days Data retention period in days.
         */
        do_action( 'pdf_pro_plus_data_cleanup_completed', $retention_days );
    }

    /**
     * Show compliance notices in admin.
     *
     * @since 1.3.0
     */
    public function compliance_notices() {
        $screen = get_current_screen();

        if ( ! $screen || 'pdf_document' !== $screen->post_type ) {
            return;
        }

        if ( $this->is_hipaa_enabled() && ! $this->is_hipaa_compliant() ) {
            ?>
            <div class="notice notice-warning">
                <p>
                    <strong><?php esc_html_e( 'HIPAA Mode Active', 'pdf-embed-seo-optimize' ); ?></strong>
                    <?php esc_html_e( 'Please ensure your server and hosting environment meet HIPAA compliance requirements.', 'pdf-embed-seo-optimize' ); ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Check if HIPAA compliance requirements are met.
     *
     * @since 1.3.0
     * @return bool
     */
    private function is_hipaa_compliant() {
        // Basic checks - in production, this would be more comprehensive.
        $checks = array(
            'ssl'        => is_ssl(),
            'encryption' => defined( 'LOGGED_IN_KEY' ) && strlen( LOGGED_IN_KEY ) >= 32,
        );

        return ! in_array( false, $checks, true );
    }

    /**
     * Register REST routes.
     *
     * @since 1.3.0
     */
    public function register_rest_routes() {
        register_rest_route( 'pdf-embed-seo/v1', '/compliance/consent', array(
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'rest_record_consent' ),
                'permission_callback' => '__return_true',
            ),
        ) );

        register_rest_route( 'pdf-embed-seo/v1', '/compliance/status', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'rest_get_status' ),
                'permission_callback' => function () {
                    return current_user_can( 'manage_options' );
                },
            ),
        ) );
    }

    /**
     * REST: Record consent.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function rest_record_consent( $request ) {
        $consent_type = $request->get_param( 'type' ) ?: 'analytics';
        $given        = (bool) $request->get_param( 'given' );

        if ( $this->record_consent( $consent_type, $given ) ) {
            return new WP_REST_Response( array( 'success' => true ), 200 );
        }

        return new WP_REST_Response( array( 'success' => false ), 500 );
    }

    /**
     * REST: Get compliance status.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function rest_get_status( $request ) {
        return new WP_REST_Response( array(
            'gdpr_enabled'    => $this->is_gdpr_enabled(),
            'hipaa_enabled'   => $this->is_hipaa_enabled(),
            'hipaa_compliant' => $this->is_hipaa_compliant(),
            'retention_days'  => $this->get_retention_days(),
        ), 200 );
    }

    /**
     * Get session ID.
     *
     * @since 1.3.0
     * @return string
     */
    private function get_session_id() {
        if ( isset( $_COOKIE['pdf_session_id'] ) ) {
            return sanitize_text_field( wp_unslash( $_COOKIE['pdf_session_id'] ) );
        }

        $session_id = wp_generate_uuid4();
        setcookie( 'pdf_session_id', $session_id, time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );

        return $session_id;
    }

    /**
     * Get client IP.
     *
     * @since 1.3.0
     * @return string
     */
    private function get_client_ip() {
        $ip = '';

        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ips = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
            $ip  = trim( $ips[0] );
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }

        return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
    }

    /**
     * Anonymize IP address.
     *
     * @since 1.3.0
     * @param string $ip IP address.
     * @return string
     */
    private function anonymize_ip( $ip ) {
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            return preg_replace( '/\.\d+$/', '.0', $ip );
        } elseif ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
            return substr( $ip, 0, strrpos( $ip, ':' ) ) . ':0:0:0:0:0';
        }
        return '';
    }
}

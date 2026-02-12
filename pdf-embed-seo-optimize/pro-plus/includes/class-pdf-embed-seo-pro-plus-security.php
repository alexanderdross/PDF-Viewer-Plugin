<?php
/**
 * Pro+ Security - 2FA, IP Whitelisting, Audit Logs.
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
 * Security class.
 *
 * @since 1.3.0
 */
class PDF_Embed_SEO_Pro_Plus_Security {

    /**
     * Audit log table name.
     *
     * @var string
     */
    private $audit_table;

    /**
     * 2FA codes table name.
     *
     * @var string
     */
    private $codes_table;

    /**
     * Constructor.
     *
     * @since 1.3.0
     */
    public function __construct() {
        global $wpdb;
        $this->audit_table = $wpdb->prefix . 'pdf_audit_log';
        $this->codes_table = $wpdb->prefix . 'pdf_2fa_codes';

        $this->init_hooks();
        $this->maybe_create_tables();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.3.0
     */
    private function init_hooks() {
        // Check access on PDF view.
        add_action( 'template_redirect', array( $this, 'check_pdf_access' ), 5 );

        // 2FA verification endpoint.
        add_action( 'wp_ajax_pdf_verify_2fa', array( $this, 'ajax_verify_2fa' ) );
        add_action( 'wp_ajax_nopriv_pdf_verify_2fa', array( $this, 'ajax_verify_2fa' ) );

        // Send 2FA code endpoint.
        add_action( 'wp_ajax_pdf_send_2fa', array( $this, 'ajax_send_2fa_code' ) );
        add_action( 'wp_ajax_nopriv_pdf_send_2fa', array( $this, 'ajax_send_2fa_code' ) );

        // Log PDF events.
        add_action( 'pdf_embed_seo_pdf_viewed', array( $this, 'log_view' ), 10, 2 );
        add_action( 'pdf_embed_seo_pdf_downloaded', array( $this, 'log_download' ), 10, 2 );
        add_action( 'pdf_embed_seo_password_verified', array( $this, 'log_password_attempt' ), 10, 3 );

        // Cleanup old audit logs.
        add_action( 'pdf_pro_plus_cleanup_audit_logs', array( $this, 'cleanup_audit_logs' ) );

        if ( ! wp_next_scheduled( 'pdf_pro_plus_cleanup_audit_logs' ) ) {
            wp_schedule_event( time(), 'daily', 'pdf_pro_plus_cleanup_audit_logs' );
        }
    }

    /**
     * Create database tables.
     *
     * @since 1.3.0
     */
    private function maybe_create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Audit log table.
        $sql_audit = "CREATE TABLE IF NOT EXISTS {$this->audit_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) DEFAULT NULL,
            user_id bigint(20) DEFAULT NULL,
            event_type varchar(50) NOT NULL,
            event_data text,
            ip_address varchar(45),
            user_agent varchar(500),
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY event_type (event_type),
            KEY created_at (created_at)
        ) {$charset_collate};";

        // 2FA codes table.
        $sql_codes = "CREATE TABLE IF NOT EXISTS {$this->codes_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            email varchar(255) NOT NULL,
            code varchar(10) NOT NULL,
            attempts int(11) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL,
            expires_at datetime NOT NULL,
            verified tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY post_id_email (post_id, email),
            KEY expires_at (expires_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_audit );
        dbDelta( $sql_codes );
    }

    /**
     * Check PDF access on template redirect.
     *
     * @since 1.3.0
     */
    public function check_pdf_access() {
        if ( ! is_singular( 'pdf_document' ) ) {
            return;
        }

        $post_id = get_the_ID();

        // Check IP whitelist.
        if ( ! $this->check_ip_whitelist( $post_id ) ) {
            $this->log_event( $post_id, 'ip_blocked', array(
                'ip' => $this->get_client_ip(),
            ) );
            wp_die(
                esc_html__( 'Access denied. Your IP address is not authorized to view this document.', 'pdf-embed-seo-optimize' ),
                esc_html__( 'Access Denied', 'pdf-embed-seo-optimize' ),
                array( 'response' => 403 )
            );
        }

        // Check 2FA requirement.
        if ( $this->requires_2fa( $post_id ) && ! $this->is_2fa_verified( $post_id ) ) {
            // Show 2FA form.
            add_filter( 'the_content', array( $this, 'show_2fa_form' ), 999 );
        }
    }

    /**
     * Check IP whitelist for a PDF.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return bool
     */
    private function check_ip_whitelist( $post_id ) {
        // Check per-PDF whitelist.
        $pdf_whitelist = get_post_meta( $post_id, '_pdf_pro_plus_ip_whitelist', true );

        // Check global whitelist.
        $settings         = pdf_embed_seo_pro_plus()->get_settings();
        $global_whitelist = $settings['ip_whitelist'] ?? '';

        // If no whitelist configured, allow all.
        if ( empty( $pdf_whitelist ) && empty( $global_whitelist ) ) {
            return true;
        }

        // Combine whitelists.
        $whitelist = trim( $pdf_whitelist . "\n" . $global_whitelist );

        if ( empty( $whitelist ) ) {
            return true;
        }

        $client_ip      = $this->get_client_ip();
        $allowed_ips    = array_filter( array_map( 'trim', explode( "\n", $whitelist ) ) );

        foreach ( $allowed_ips as $allowed_ip ) {
            // Check for CIDR notation.
            if ( strpos( $allowed_ip, '/' ) !== false ) {
                if ( $this->ip_in_range( $client_ip, $allowed_ip ) ) {
                    return true;
                }
            } elseif ( $client_ip === $allowed_ip ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is in CIDR range.
     *
     * @since 1.3.0
     * @param string $ip   IP address.
     * @param string $cidr CIDR notation.
     * @return bool
     */
    private function ip_in_range( $ip, $cidr ) {
        list( $subnet, $mask ) = explode( '/', $cidr );

        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            $ip_long     = ip2long( $ip );
            $subnet_long = ip2long( $subnet );
            $mask_long   = -1 << ( 32 - intval( $mask ) );

            return ( $ip_long & $mask_long ) === ( $subnet_long & $mask_long );
        }

        return false;
    }

    /**
     * Check if PDF requires 2FA.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return bool
     */
    private function requires_2fa( $post_id ) {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( empty( $settings['two_factor_enabled'] ) ) {
            return false;
        }

        return get_post_meta( $post_id, '_pdf_pro_plus_2fa_required', true ) === '1';
    }

    /**
     * Check if 2FA has been verified for this session.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return bool
     */
    private function is_2fa_verified( $post_id ) {
        if ( ! session_id() ) {
            session_start();
        }

        $verified_pdfs = isset( $_SESSION['pdf_2fa_verified'] ) ? $_SESSION['pdf_2fa_verified'] : array();

        return in_array( $post_id, $verified_pdfs, true );
    }

    /**
     * Show 2FA form instead of PDF content.
     *
     * @since 1.3.0
     * @param string $content Post content.
     * @return string
     */
    public function show_2fa_form( $content ) {
        ob_start();
        include PDF_EMBED_SEO_PRO_PLUS_DIR . 'admin/views/2fa-form.php';
        return ob_get_clean();
    }

    /**
     * AJAX: Send 2FA code.
     *
     * @since 1.3.0
     */
    public function ajax_send_2fa_code() {
        check_ajax_referer( 'pdf_2fa_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

        if ( ! $post_id || ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid request.', 'pdf-embed-seo-optimize' ) ) );
        }

        // Generate 6-digit code.
        $code = sprintf( '%06d', wp_rand( 0, 999999 ) );

        // Store code in database.
        global $wpdb;

        // Invalidate old codes.
        $wpdb->update(
            $this->codes_table,
            array( 'expires_at' => current_time( 'mysql' ) ),
            array(
                'post_id' => $post_id,
                'email'   => $email,
            ),
            array( '%s' ),
            array( '%d', '%s' )
        );

        // Insert new code.
        $wpdb->insert(
            $this->codes_table,
            array(
                'post_id'    => $post_id,
                'email'      => $email,
                'code'       => wp_hash( $code ),
                'attempts'   => 0,
                'created_at' => current_time( 'mysql' ),
                'expires_at' => gmdate( 'Y-m-d H:i:s', time() + 600 ), // 10 minutes.
                'verified'   => 0,
            ),
            array( '%d', '%s', '%s', '%d', '%s', '%s', '%d' )
        );

        // Send email.
        $post  = get_post( $post_id );
        $title = $post ? $post->post_title : __( 'PDF Document', 'pdf-embed-seo-optimize' );

        $subject = sprintf(
            /* translators: %s: Site name */
            __( 'Your verification code for %s', 'pdf-embed-seo-optimize' ),
            get_bloginfo( 'name' )
        );

        $message = sprintf(
            /* translators: 1: Document title, 2: Verification code, 3: Minutes */
            __( "To access \"%1\$s\", please use the following verification code:\n\n%2\$s\n\nThis code will expire in %3\$d minutes.\n\nIf you did not request this code, please ignore this email.", 'pdf-embed-seo-optimize' ),
            $title,
            $code,
            10
        );

        $sent = wp_mail( $email, $subject, $message );

        if ( $sent ) {
            $this->log_event( $post_id, '2fa_code_sent', array( 'email' => $this->mask_email( $email ) ) );
            wp_send_json_success( array( 'message' => __( 'Verification code sent to your email.', 'pdf-embed-seo-optimize' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to send email. Please try again.', 'pdf-embed-seo-optimize' ) ) );
        }
    }

    /**
     * AJAX: Verify 2FA code.
     *
     * @since 1.3.0
     */
    public function ajax_verify_2fa() {
        check_ajax_referer( 'pdf_2fa_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
        $code    = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';

        if ( ! $post_id || ! $email || ! $code ) {
            wp_send_json_error( array( 'message' => __( 'Invalid request.', 'pdf-embed-seo-optimize' ) ) );
        }

        global $wpdb;

        // Get the latest valid code for this post/email.
        $record = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->codes_table}
                WHERE post_id = %d AND email = %s AND expires_at > NOW() AND verified = 0
                ORDER BY created_at DESC LIMIT 1",
                $post_id,
                $email
            )
        );

        if ( ! $record ) {
            wp_send_json_error( array( 'message' => __( 'Code expired or not found. Please request a new code.', 'pdf-embed-seo-optimize' ) ) );
        }

        // Check max attempts.
        $settings     = pdf_embed_seo_pro_plus()->get_settings();
        $max_attempts = $settings['max_failed_attempts'] ?? 5;

        if ( $record->attempts >= $max_attempts ) {
            $this->log_event( $post_id, '2fa_max_attempts', array( 'email' => $this->mask_email( $email ) ) );
            wp_send_json_error( array( 'message' => __( 'Maximum attempts exceeded. Please request a new code.', 'pdf-embed-seo-optimize' ) ) );
        }

        // Verify code.
        if ( ! wp_check_password( $code, $record->code ) ) {
            // Increment attempts.
            $wpdb->update(
                $this->codes_table,
                array( 'attempts' => $record->attempts + 1 ),
                array( 'id' => $record->id ),
                array( '%d' ),
                array( '%d' )
            );

            $this->log_event( $post_id, '2fa_failed', array( 'email' => $this->mask_email( $email ) ) );
            wp_send_json_error( array( 'message' => __( 'Invalid code. Please try again.', 'pdf-embed-seo-optimize' ) ) );
        }

        // Mark as verified.
        $wpdb->update(
            $this->codes_table,
            array( 'verified' => 1 ),
            array( 'id' => $record->id ),
            array( '%d' ),
            array( '%d' )
        );

        // Store in session.
        if ( ! session_id() ) {
            session_start();
        }

        if ( ! isset( $_SESSION['pdf_2fa_verified'] ) ) {
            $_SESSION['pdf_2fa_verified'] = array();
        }

        $_SESSION['pdf_2fa_verified'][] = $post_id;

        $this->log_event( $post_id, '2fa_verified', array( 'email' => $this->mask_email( $email ) ) );

        wp_send_json_success( array(
            'message'  => __( 'Verification successful!', 'pdf-embed-seo-optimize' ),
            'redirect' => get_permalink( $post_id ),
        ) );
    }

    /**
     * Mask email for logging.
     *
     * @since 1.3.0
     * @param string $email Email address.
     * @return string
     */
    private function mask_email( $email ) {
        $parts = explode( '@', $email );
        if ( count( $parts ) !== 2 ) {
            return '***';
        }

        $name   = $parts[0];
        $domain = $parts[1];

        $masked_name = substr( $name, 0, 2 ) . str_repeat( '*', max( 0, strlen( $name ) - 2 ) );

        return $masked_name . '@' . $domain;
    }

    /**
     * Log an event.
     *
     * @since 1.3.0
     * @param int|null $post_id    Post ID.
     * @param string   $event_type Event type.
     * @param array    $event_data Additional data.
     */
    public function log_event( $post_id, $event_type, $event_data = array() ) {
        global $wpdb;

        $wpdb->insert(
            $this->audit_table,
            array(
                'post_id'    => $post_id,
                'user_id'    => get_current_user_id() ?: null,
                'event_type' => $event_type,
                'event_data' => wp_json_encode( $event_data ),
                'ip_address' => $this->get_client_ip(),
                'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 500 ) : null,
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s', '%s', '%s', '%s', '%s' )
        );
    }

    /**
     * Log PDF view.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @param int $count   View count.
     */
    public function log_view( $post_id, $count ) {
        $this->log_event( $post_id, 'view', array( 'total_views' => $count ) );
    }

    /**
     * Log PDF download.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @param int $count   Download count.
     */
    public function log_download( $post_id, $count ) {
        $this->log_event( $post_id, 'download', array( 'total_downloads' => $count ) );
    }

    /**
     * Log password attempt.
     *
     * @since 1.3.0
     * @param int    $post_id  Post ID.
     * @param bool   $success  Whether attempt was successful.
     * @param string $password Password attempted (not logged for security).
     */
    public function log_password_attempt( $post_id, $success, $password ) {
        $this->log_event( $post_id, $success ? 'password_success' : 'password_failed', array() );
    }

    /**
     * Cleanup old audit logs.
     *
     * @since 1.3.0
     */
    public function cleanup_audit_logs() {
        global $wpdb;

        $settings  = pdf_embed_seo_pro_plus()->get_settings();
        $retention = $settings['audit_log_retention'] ?? 90;

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$this->audit_table} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
                $retention
            )
        );

        // Also cleanup expired 2FA codes.
        $wpdb->query( "DELETE FROM {$this->codes_table} WHERE expires_at < NOW()" );
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
     * Get audit log entries.
     *
     * @since 1.3.0
     * @param array $args Query arguments.
     * @return array
     */
    public function get_audit_log( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'post_id'    => null,
            'user_id'    => null,
            'event_type' => null,
            'limit'      => 100,
            'offset'     => 0,
            'orderby'    => 'created_at',
            'order'      => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array( '1=1' );

        if ( $args['post_id'] ) {
            $where[] = $wpdb->prepare( 'post_id = %d', $args['post_id'] );
        }

        if ( $args['user_id'] ) {
            $where[] = $wpdb->prepare( 'user_id = %d', $args['user_id'] );
        }

        if ( $args['event_type'] ) {
            $where[] = $wpdb->prepare( 'event_type = %s', $args['event_type'] );
        }

        $where_clause = implode( ' AND ', $where );
        $orderby      = esc_sql( $args['orderby'] );
        $order        = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->audit_table} WHERE {$where_clause} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d",
                $args['limit'],
                $args['offset']
            )
        );

        return $results ?: array();
    }
}

<?php
/**
 * Pro+ Webhooks - External Integrations.
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
 * Webhooks class.
 *
 * @since 1.3.0
 */
class PDF_Embed_SEO_Pro_Plus_Webhooks {

    /**
     * Webhook log table name.
     *
     * @var string
     */
    private $log_table;

    /**
     * Constructor.
     *
     * @since 1.3.0
     */
    public function __construct() {
        global $wpdb;
        $this->log_table = $wpdb->prefix . 'pdf_webhook_log';

        $this->init_hooks();
        $this->maybe_create_tables();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.3.0
     */
    private function init_hooks() {
        // PDF events.
        add_action( 'pdf_embed_seo_pdf_viewed', array( $this, 'on_pdf_viewed' ), 100, 2 );
        add_action( 'pdf_embed_seo_pdf_downloaded', array( $this, 'on_pdf_downloaded' ), 100, 2 );
        add_action( 'pdf_embed_seo_password_verified', array( $this, 'on_password_attempt' ), 100, 3 );

        // Post events.
        add_action( 'save_post_pdf_document', array( $this, 'on_pdf_saved' ), 100, 3 );
        add_action( 'trashed_post', array( $this, 'on_pdf_trashed' ), 100 );
        add_action( 'untrashed_post', array( $this, 'on_pdf_restored' ), 100 );

        // Admin AJAX for testing.
        add_action( 'wp_ajax_pdf_test_webhook', array( $this, 'ajax_test_webhook' ) );

        // Retry failed webhooks.
        add_action( 'pdf_pro_plus_retry_webhooks', array( $this, 'retry_failed_webhooks' ) );

        if ( ! wp_next_scheduled( 'pdf_pro_plus_retry_webhooks' ) ) {
            wp_schedule_event( time(), 'hourly', 'pdf_pro_plus_retry_webhooks' );
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

        $sql = "CREATE TABLE IF NOT EXISTS {$this->log_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            payload text NOT NULL,
            response_code int(11) DEFAULT NULL,
            response_body text,
            attempts int(11) NOT NULL DEFAULT 1,
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_at datetime NOT NULL,
            sent_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY status (status),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Check if event is enabled.
     *
     * @since 1.3.0
     * @param string $event Event name.
     * @return bool
     */
    private function is_event_enabled( $event ) {
        $settings = pdf_embed_seo_pro_plus()->get_settings();
        $events   = $settings['webhook_events'] ?? array();

        return in_array( $event, $events, true );
    }

    /**
     * Get webhook URL.
     *
     * @since 1.3.0
     * @return string
     */
    private function get_webhook_url() {
        $settings = pdf_embed_seo_pro_plus()->get_settings();
        return $settings['webhook_url'] ?? '';
    }

    /**
     * Get webhook secret.
     *
     * @since 1.3.0
     * @return string
     */
    private function get_webhook_secret() {
        $settings = pdf_embed_seo_pro_plus()->get_settings();
        return $settings['webhook_secret'] ?? '';
    }

    /**
     * Send webhook.
     *
     * @since 1.3.0
     * @param string $event   Event type.
     * @param array  $payload Webhook payload.
     * @param bool   $async   Whether to send asynchronously.
     * @return bool|int Log ID on success, false on failure.
     */
    public function send_webhook( $event, $payload, $async = true ) {
        $url = $this->get_webhook_url();

        if ( empty( $url ) ) {
            return false;
        }

        // Add standard fields.
        $payload = array_merge( array(
            'event'     => $event,
            'timestamp' => gmdate( 'c' ),
            'site_url'  => home_url(),
            'site_name' => get_bloginfo( 'name' ),
        ), $payload );

        // Log the webhook.
        global $wpdb;

        $wpdb->insert(
            $this->log_table,
            array(
                'event_type' => $event,
                'payload'    => wp_json_encode( $payload ),
                'status'     => 'pending',
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s', '%s' )
        );

        $log_id = $wpdb->insert_id;

        if ( $async ) {
            // Schedule async delivery.
            wp_schedule_single_event( time(), 'pdf_pro_plus_deliver_webhook', array( $log_id ) );
            add_action( 'pdf_pro_plus_deliver_webhook', array( $this, 'deliver_webhook' ) );
            return $log_id;
        }

        // Send immediately.
        return $this->deliver_webhook( $log_id );
    }

    /**
     * Deliver a webhook.
     *
     * @since 1.3.0
     * @param int $log_id Webhook log ID.
     * @return bool
     */
    public function deliver_webhook( $log_id ) {
        global $wpdb;

        $log = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->log_table} WHERE id = %d",
                $log_id
            )
        );

        if ( ! $log ) {
            return false;
        }

        $url     = $this->get_webhook_url();
        $secret  = $this->get_webhook_secret();
        $payload = $log->payload;

        // Generate signature.
        $signature = hash_hmac( 'sha256', $payload, $secret );

        // Send request.
        $response = wp_remote_post( $url, array(
            'body'        => $payload,
            'headers'     => array(
                'Content-Type'       => 'application/json',
                'X-PDF-Signature'    => $signature,
                'X-PDF-Event'        => $log->event_type,
                'X-PDF-Delivery-ID'  => $log_id,
            ),
            'timeout'     => 30,
            'sslverify'   => true,
        ) );

        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        // Update log.
        $status = ( $response_code >= 200 && $response_code < 300 ) ? 'delivered' : 'failed';

        $wpdb->update(
            $this->log_table,
            array(
                'response_code' => $response_code,
                'response_body' => substr( $response_body, 0, 1000 ),
                'status'        => $status,
                'sent_at'       => current_time( 'mysql' ),
                'attempts'      => $log->attempts,
            ),
            array( 'id' => $log_id ),
            array( '%d', '%s', '%s', '%s', '%d' ),
            array( '%d' )
        );

        return $status === 'delivered';
    }

    /**
     * Retry failed webhooks.
     *
     * @since 1.3.0
     */
    public function retry_failed_webhooks() {
        global $wpdb;

        // Get failed webhooks with less than 5 attempts.
        $failed = $wpdb->get_results(
            "SELECT id FROM {$this->log_table}
            WHERE status = 'failed' AND attempts < 5
            AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
            LIMIT 10"
        );

        foreach ( $failed as $log ) {
            $wpdb->update(
                $this->log_table,
                array( 'attempts' => $wpdb->get_var( $wpdb->prepare( "SELECT attempts FROM {$this->log_table} WHERE id = %d", $log->id ) ) + 1 ),
                array( 'id' => $log->id ),
                array( '%d' ),
                array( '%d' )
            );

            $this->deliver_webhook( $log->id );
        }
    }

    /**
     * On PDF viewed.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @param int $count   View count.
     */
    public function on_pdf_viewed( $post_id, $count ) {
        if ( ! $this->is_event_enabled( 'view' ) ) {
            return;
        }

        $post = get_post( $post_id );

        $this->send_webhook( 'pdf.viewed', array(
            'pdf_id'      => $post_id,
            'pdf_title'   => $post ? $post->post_title : '',
            'pdf_url'     => get_permalink( $post_id ),
            'view_count'  => $count,
            'viewer_ip'   => $this->get_anonymized_ip(),
        ) );
    }

    /**
     * On PDF downloaded.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @param int $count   Download count.
     */
    public function on_pdf_downloaded( $post_id, $count ) {
        if ( ! $this->is_event_enabled( 'download' ) ) {
            return;
        }

        $post = get_post( $post_id );

        $this->send_webhook( 'pdf.downloaded', array(
            'pdf_id'         => $post_id,
            'pdf_title'      => $post ? $post->post_title : '',
            'pdf_url'        => get_permalink( $post_id ),
            'download_count' => $count,
            'downloader_ip'  => $this->get_anonymized_ip(),
        ) );
    }

    /**
     * On password attempt.
     *
     * @since 1.3.0
     * @param int    $post_id  Post ID.
     * @param bool   $success  Whether successful.
     * @param string $password Password (not sent).
     */
    public function on_password_attempt( $post_id, $success, $password ) {
        if ( ! $this->is_event_enabled( 'password_attempt' ) ) {
            return;
        }

        $post = get_post( $post_id );

        $this->send_webhook( 'pdf.password_attempt', array(
            'pdf_id'    => $post_id,
            'pdf_title' => $post ? $post->post_title : '',
            'success'   => $success,
            'ip'        => $this->get_anonymized_ip(),
        ) );
    }

    /**
     * On PDF saved.
     *
     * @since 1.3.0
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @param bool    $update  Whether this is an update.
     */
    public function on_pdf_saved( $post_id, $post, $update ) {
        if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
            return;
        }

        $event = $update ? 'pdf.updated' : 'pdf.created';

        if ( ! $this->is_event_enabled( $update ? 'update' : 'create' ) ) {
            return;
        }

        $this->send_webhook( $event, array(
            'pdf_id'     => $post_id,
            'pdf_title'  => $post->post_title,
            'pdf_url'    => get_permalink( $post_id ),
            'pdf_status' => $post->post_status,
            'author_id'  => $post->post_author,
        ) );
    }

    /**
     * On PDF trashed.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     */
    public function on_pdf_trashed( $post_id ) {
        if ( get_post_type( $post_id ) !== 'pdf_document' ) {
            return;
        }

        if ( ! $this->is_event_enabled( 'delete' ) ) {
            return;
        }

        $post = get_post( $post_id );

        $this->send_webhook( 'pdf.trashed', array(
            'pdf_id'    => $post_id,
            'pdf_title' => $post ? $post->post_title : '',
        ) );
    }

    /**
     * On PDF restored.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     */
    public function on_pdf_restored( $post_id ) {
        if ( get_post_type( $post_id ) !== 'pdf_document' ) {
            return;
        }

        if ( ! $this->is_event_enabled( 'restore' ) ) {
            return;
        }

        $post = get_post( $post_id );

        $this->send_webhook( 'pdf.restored', array(
            'pdf_id'    => $post_id,
            'pdf_title' => $post ? $post->post_title : '',
            'pdf_url'   => get_permalink( $post_id ),
        ) );
    }

    /**
     * AJAX: Test webhook.
     *
     * @since 1.3.0
     */
    public function ajax_test_webhook() {
        check_ajax_referer( 'pdf_pro_plus_admin', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'pdf-embed-seo-optimize' ) ) );
        }

        $result = $this->send_webhook( 'test', array(
            'message' => __( 'This is a test webhook from PDF Embed & SEO Pro+', 'pdf-embed-seo-optimize' ),
            'test'    => true,
        ), false );

        if ( $result ) {
            wp_send_json_success( array( 'message' => __( 'Test webhook sent successfully!', 'pdf-embed-seo-optimize' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to send test webhook. Check your settings.', 'pdf-embed-seo-optimize' ) ) );
        }
    }

    /**
     * Get anonymized IP.
     *
     * @since 1.3.0
     * @return string
     */
    private function get_anonymized_ip() {
        $ip = '';

        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ips = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
            $ip  = trim( $ips[0] );
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }

        // Anonymize for privacy.
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            return preg_replace( '/\.\d+$/', '.0', $ip );
        } elseif ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
            return substr( $ip, 0, strrpos( $ip, ':' ) ) . ':0:0:0:0:0';
        }

        return '';
    }

    /**
     * Get webhook log.
     *
     * @since 1.3.0
     * @param array $args Query arguments.
     * @return array
     */
    public function get_webhook_log( $args = array() ) {
        global $wpdb;

        $defaults = array(
            'event_type' => null,
            'status'     => null,
            'limit'      => 50,
            'offset'     => 0,
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array( '1=1' );

        if ( $args['event_type'] ) {
            $where[] = $wpdb->prepare( 'event_type = %s', $args['event_type'] );
        }

        if ( $args['status'] ) {
            $where[] = $wpdb->prepare( 'status = %s', $args['status'] );
        }

        $where_clause = implode( ' AND ', $where );

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->log_table} WHERE {$where_clause} ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $args['limit'],
                $args['offset']
            )
        );
    }
}

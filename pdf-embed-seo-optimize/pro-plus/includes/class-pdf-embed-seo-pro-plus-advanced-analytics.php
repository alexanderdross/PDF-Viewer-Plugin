<?php
/**
 * Pro+ Advanced Analytics - Heatmaps, Engagement Scoring, Geographic Tracking.
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
 * Advanced Analytics class.
 *
 * @since 1.3.0
 */
class PDF_Embed_SEO_Pro_Plus_Advanced_Analytics {

    /**
     * Table name for advanced analytics.
     *
     * @var string
     */
    private $table_name;

    /**
     * Heatmap table name.
     *
     * @var string
     */
    private $heatmap_table;

    /**
     * Constructor.
     *
     * @since 1.3.0
     */
    public function __construct() {
        global $wpdb;
        $this->table_name    = $wpdb->prefix . 'pdf_advanced_analytics';
        $this->heatmap_table = $wpdb->prefix . 'pdf_heatmaps';

        $this->init_hooks();
        $this->maybe_create_tables();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.3.0
     */
    private function init_hooks() {
        // Track page interactions.
        add_action( 'wp_ajax_pdf_track_page_view', array( $this, 'ajax_track_page_view' ) );
        add_action( 'wp_ajax_nopriv_pdf_track_page_view', array( $this, 'ajax_track_page_view' ) );

        // Track scroll depth.
        add_action( 'wp_ajax_pdf_track_scroll', array( $this, 'ajax_track_scroll' ) );
        add_action( 'wp_ajax_nopriv_pdf_track_scroll', array( $this, 'ajax_track_scroll' ) );

        // Track time on page.
        add_action( 'wp_ajax_pdf_track_time', array( $this, 'ajax_track_time' ) );
        add_action( 'wp_ajax_nopriv_pdf_track_time', array( $this, 'ajax_track_time' ) );

        // Enqueue tracking scripts.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_tracking_scripts' ) );

        // Calculate engagement scores daily.
        add_action( 'pdf_pro_plus_calculate_engagement', array( $this, 'calculate_all_engagement_scores' ) );

        // Schedule cron if not scheduled.
        if ( ! wp_next_scheduled( 'pdf_pro_plus_calculate_engagement' ) ) {
            wp_schedule_event( time(), 'daily', 'pdf_pro_plus_calculate_engagement' );
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

        // Advanced analytics table.
        $sql_analytics = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            session_id varchar(64) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            page_number int(11) NOT NULL DEFAULT 1,
            time_on_page int(11) NOT NULL DEFAULT 0,
            scroll_depth int(11) NOT NULL DEFAULT 0,
            zoom_level decimal(4,2) NOT NULL DEFAULT 1.00,
            interactions int(11) NOT NULL DEFAULT 0,
            country_code varchar(2) DEFAULT NULL,
            region varchar(100) DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            device_type varchar(20) DEFAULT NULL,
            browser varchar(50) DEFAULT NULL,
            os varchar(50) DEFAULT NULL,
            screen_width int(11) DEFAULT NULL,
            screen_height int(11) DEFAULT NULL,
            referrer_url varchar(500) DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY session_id (session_id),
            KEY created_at (created_at)
        ) {$charset_collate};";

        // Heatmap data table.
        $sql_heatmap = "CREATE TABLE IF NOT EXISTS {$this->heatmap_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            page_number int(11) NOT NULL,
            x_position decimal(5,2) NOT NULL,
            y_position decimal(5,2) NOT NULL,
            click_count int(11) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY post_id_page (post_id, page_number),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_analytics );
        dbDelta( $sql_heatmap );
    }

    /**
     * Enqueue tracking scripts.
     *
     * @since 1.3.0
     */
    public function enqueue_tracking_scripts() {
        if ( ! is_singular( 'pdf_document' ) ) {
            return;
        }

        wp_enqueue_script(
            'pdf-pro-plus-tracking',
            PDF_EMBED_SEO_PRO_PLUS_URL . 'public/js/tracking.js',
            array( 'jquery' ),
            PDF_EMBED_SEO_PRO_PLUS_VERSION,
            true
        );

        wp_localize_script( 'pdf-pro-plus-tracking', 'pdfTracking', array(
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'postId'      => get_the_ID(),
            'sessionId'   => $this->get_session_id(),
            'nonce'       => wp_create_nonce( 'pdf_tracking' ),
            'trackScroll' => true,
            'trackTime'   => true,
            'trackClicks' => true,
        ) );
    }

    /**
     * Get or create session ID.
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
     * AJAX: Track page view within PDF.
     *
     * @since 1.3.0
     */
    public function ajax_track_page_view() {
        check_ajax_referer( 'pdf_tracking', 'nonce' );

        $post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        $session_id  = isset( $_POST['session_id'] ) ? sanitize_text_field( wp_unslash( $_POST['session_id'] ) ) : '';
        $page_number = isset( $_POST['page_number'] ) ? absint( $_POST['page_number'] ) : 1;
        $zoom_level  = isset( $_POST['zoom_level'] ) ? floatval( $_POST['zoom_level'] ) : 1.0;

        if ( ! $post_id || ! $session_id ) {
            wp_send_json_error( 'Missing required data' );
        }

        global $wpdb;

        // Check if record exists.
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$this->table_name} WHERE post_id = %d AND session_id = %s AND page_number = %d AND DATE(created_at) = CURDATE()",
                $post_id,
                $session_id,
                $page_number
            )
        );

        $geo_data    = $this->get_geo_data();
        $device_data = $this->get_device_data();

        if ( $existing ) {
            // Update existing record.
            $wpdb->update(
                $this->table_name,
                array(
                    'interactions' => $wpdb->prepare( 'interactions + 1' ),
                    'zoom_level'   => $zoom_level,
                    'updated_at'   => current_time( 'mysql' ),
                ),
                array( 'id' => $existing->id ),
                array( '%s', '%f', '%s' ),
                array( '%d' )
            );
        } else {
            // Insert new record.
            $wpdb->insert(
                $this->table_name,
                array(
                    'post_id'       => $post_id,
                    'session_id'    => $session_id,
                    'user_id'       => get_current_user_id() ?: null,
                    'page_number'   => $page_number,
                    'zoom_level'    => $zoom_level,
                    'interactions'  => 1,
                    'country_code'  => $geo_data['country_code'],
                    'region'        => $geo_data['region'],
                    'city'          => $geo_data['city'],
                    'device_type'   => $device_data['device_type'],
                    'browser'       => $device_data['browser'],
                    'os'            => $device_data['os'],
                    'screen_width'  => $device_data['screen_width'],
                    'screen_height' => $device_data['screen_height'],
                    'referrer_url'  => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : null,
                    'created_at'    => current_time( 'mysql' ),
                    'updated_at'    => current_time( 'mysql' ),
                ),
                array( '%d', '%s', '%d', '%d', '%f', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s' )
            );
        }

        wp_send_json_success();
    }

    /**
     * AJAX: Track scroll depth.
     *
     * @since 1.3.0
     */
    public function ajax_track_scroll() {
        check_ajax_referer( 'pdf_tracking', 'nonce' );

        $post_id      = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        $session_id   = isset( $_POST['session_id'] ) ? sanitize_text_field( wp_unslash( $_POST['session_id'] ) ) : '';
        $page_number  = isset( $_POST['page_number'] ) ? absint( $_POST['page_number'] ) : 1;
        $scroll_depth = isset( $_POST['scroll_depth'] ) ? min( 100, absint( $_POST['scroll_depth'] ) ) : 0;

        if ( ! $post_id || ! $session_id ) {
            wp_send_json_error( 'Missing required data' );
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$this->table_name} SET scroll_depth = GREATEST(scroll_depth, %d), updated_at = %s
                WHERE post_id = %d AND session_id = %s AND page_number = %d AND DATE(created_at) = CURDATE()",
                $scroll_depth,
                current_time( 'mysql' ),
                $post_id,
                $session_id,
                $page_number
            )
        );

        wp_send_json_success();
    }

    /**
     * AJAX: Track time on page.
     *
     * @since 1.3.0
     */
    public function ajax_track_time() {
        check_ajax_referer( 'pdf_tracking', 'nonce' );

        $post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        $session_id  = isset( $_POST['session_id'] ) ? sanitize_text_field( wp_unslash( $_POST['session_id'] ) ) : '';
        $page_number = isset( $_POST['page_number'] ) ? absint( $_POST['page_number'] ) : 1;
        $seconds     = isset( $_POST['seconds'] ) ? absint( $_POST['seconds'] ) : 0;

        if ( ! $post_id || ! $session_id ) {
            wp_send_json_error( 'Missing required data' );
        }

        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$this->table_name} SET time_on_page = time_on_page + %d, updated_at = %s
                WHERE post_id = %d AND session_id = %s AND page_number = %d AND DATE(created_at) = CURDATE()",
                $seconds,
                current_time( 'mysql' ),
                $post_id,
                $session_id,
                $page_number
            )
        );

        wp_send_json_success();
    }

    /**
     * Get geographic data from IP.
     *
     * @since 1.3.0
     * @return array
     */
    private function get_geo_data() {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( empty( $settings['geographic_tracking'] ) ) {
            return array(
                'country_code' => null,
                'region'       => null,
                'city'         => null,
            );
        }

        // Use IP-based geolocation (implement with your preferred service).
        // This is a placeholder - in production, use MaxMind, IP2Location, etc.
        $ip = $this->get_client_ip();

        // For privacy, we can hash or anonymize the IP.
        if ( ! empty( $settings['gdpr_mode'] ) ) {
            $ip = $this->anonymize_ip( $ip );
        }

        // Placeholder - implement actual geo lookup.
        return array(
            'country_code' => null,
            'region'       => null,
            'city'         => null,
        );
    }

    /**
     * Get device data from user agent.
     *
     * @since 1.3.0
     * @return array
     */
    private function get_device_data() {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( empty( $settings['device_analytics'] ) ) {
            return array(
                'device_type'   => null,
                'browser'       => null,
                'os'            => null,
                'screen_width'  => null,
                'screen_height' => null,
            );
        }

        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

        // Simple device detection.
        $device_type = 'desktop';
        if ( preg_match( '/mobile|android|iphone|ipod/i', $user_agent ) ) {
            $device_type = 'mobile';
        } elseif ( preg_match( '/tablet|ipad/i', $user_agent ) ) {
            $device_type = 'tablet';
        }

        // Simple browser detection.
        $browser = 'unknown';
        if ( preg_match( '/Chrome/i', $user_agent ) && ! preg_match( '/Edge/i', $user_agent ) ) {
            $browser = 'Chrome';
        } elseif ( preg_match( '/Firefox/i', $user_agent ) ) {
            $browser = 'Firefox';
        } elseif ( preg_match( '/Safari/i', $user_agent ) && ! preg_match( '/Chrome/i', $user_agent ) ) {
            $browser = 'Safari';
        } elseif ( preg_match( '/Edge/i', $user_agent ) ) {
            $browser = 'Edge';
        } elseif ( preg_match( '/MSIE|Trident/i', $user_agent ) ) {
            $browser = 'IE';
        }

        // Simple OS detection.
        $os = 'unknown';
        if ( preg_match( '/Windows/i', $user_agent ) ) {
            $os = 'Windows';
        } elseif ( preg_match( '/Mac/i', $user_agent ) ) {
            $os = 'macOS';
        } elseif ( preg_match( '/Linux/i', $user_agent ) ) {
            $os = 'Linux';
        } elseif ( preg_match( '/iPhone|iPad/i', $user_agent ) ) {
            $os = 'iOS';
        } elseif ( preg_match( '/Android/i', $user_agent ) ) {
            $os = 'Android';
        }

        return array(
            'device_type'   => $device_type,
            'browser'       => $browser,
            'os'            => $os,
            'screen_width'  => isset( $_POST['screen_width'] ) ? absint( $_POST['screen_width'] ) : null,
            'screen_height' => isset( $_POST['screen_height'] ) ? absint( $_POST['screen_height'] ) : null,
        );
    }

    /**
     * Get client IP address.
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
     * Anonymize IP address for GDPR compliance.
     *
     * @since 1.3.0
     * @param string $ip IP address.
     * @return string
     */
    private function anonymize_ip( $ip ) {
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            // IPv4: mask last octet.
            return preg_replace( '/\.\d+$/', '.0', $ip );
        } elseif ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
            // IPv6: mask last 80 bits.
            return substr( $ip, 0, strrpos( $ip, ':' ) ) . ':0:0:0:0:0';
        }
        return '';
    }

    /**
     * Calculate engagement score for a PDF.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return int|null Engagement score (0-100).
     */
    public function get_engagement_score( $post_id ) {
        $score = get_post_meta( $post_id, '_pdf_pro_plus_engagement_score', true );
        return $score !== '' ? intval( $score ) : null;
    }

    /**
     * Get engagement trend (week over week change).
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return int|null Percentage change.
     */
    public function get_engagement_trend( $post_id ) {
        global $wpdb;

        // This week's metrics.
        $this_week = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    AVG(time_on_page) as avg_time,
                    AVG(scroll_depth) as avg_scroll,
                    COUNT(DISTINCT session_id) as sessions
                FROM {$this->table_name}
                WHERE post_id = %d AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
                $post_id
            )
        );

        // Last week's metrics.
        $last_week = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    AVG(time_on_page) as avg_time,
                    AVG(scroll_depth) as avg_scroll,
                    COUNT(DISTINCT session_id) as sessions
                FROM {$this->table_name}
                WHERE post_id = %d AND created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)",
                $post_id
            )
        );

        if ( ! $last_week || ! $last_week->sessions ) {
            return null;
        }

        // Calculate simple engagement metric.
        $this_engagement = ( $this_week->avg_time + $this_week->avg_scroll ) / 2;
        $last_engagement = ( $last_week->avg_time + $last_week->avg_scroll ) / 2;

        if ( $last_engagement == 0 ) {
            return null;
        }

        $change = ( ( $this_engagement - $last_engagement ) / $last_engagement ) * 100;

        return round( $change );
    }

    /**
     * Calculate engagement scores for all PDFs.
     *
     * @since 1.3.0
     */
    public function calculate_all_engagement_scores() {
        global $wpdb;

        $posts = get_posts( array(
            'post_type'      => 'pdf_document',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ) );

        foreach ( $posts as $post_id ) {
            $this->calculate_engagement_score( $post_id );
        }
    }

    /**
     * Calculate engagement score for a single PDF.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     */
    private function calculate_engagement_score( $post_id ) {
        global $wpdb;

        $metrics = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    AVG(time_on_page) as avg_time,
                    AVG(scroll_depth) as avg_scroll,
                    AVG(interactions) as avg_interactions,
                    COUNT(DISTINCT session_id) as unique_sessions,
                    COUNT(*) as total_page_views
                FROM {$this->table_name}
                WHERE post_id = %d AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
                $post_id
            )
        );

        if ( ! $metrics || ! $metrics->unique_sessions ) {
            update_post_meta( $post_id, '_pdf_pro_plus_engagement_score', 0 );
            return;
        }

        // Engagement score calculation:
        // - Time on page: 30% weight (normalized to 0-30, assuming 5+ minutes is max engagement)
        // - Scroll depth: 30% weight (0-30)
        // - Pages per session: 20% weight (normalized to 0-20)
        // - Interaction rate: 20% weight (normalized to 0-20)

        $time_score        = min( 30, ( $metrics->avg_time / 300 ) * 30 );
        $scroll_score      = ( $metrics->avg_scroll / 100 ) * 30;
        $pages_per_session = $metrics->total_page_views / $metrics->unique_sessions;
        $pages_score       = min( 20, ( $pages_per_session / 10 ) * 20 );
        $interaction_score = min( 20, ( $metrics->avg_interactions / 10 ) * 20 );

        $total_score = round( $time_score + $scroll_score + $pages_score + $interaction_score );

        update_post_meta( $post_id, '_pdf_pro_plus_engagement_score', $total_score );
    }

    /**
     * Get total views today.
     *
     * @since 1.3.0
     * @return int
     */
    public function get_total_views_today() {
        global $wpdb;

        $count = $wpdb->get_var(
            "SELECT COUNT(DISTINCT session_id) FROM {$this->table_name} WHERE DATE(created_at) = CURDATE()"
        );

        return intval( $count );
    }

    /**
     * Get average engagement score across all PDFs.
     *
     * @since 1.3.0
     * @return int
     */
    public function get_avg_engagement_score() {
        global $wpdb;

        $avg = $wpdb->get_var(
            "SELECT AVG(meta_value) FROM {$wpdb->postmeta} WHERE meta_key = '_pdf_pro_plus_engagement_score' AND meta_value > 0"
        );

        return round( floatval( $avg ) );
    }

    /**
     * Get heatmap data for a PDF page.
     *
     * @since 1.3.0
     * @param int $post_id     Post ID.
     * @param int $page_number Page number.
     * @return array
     */
    public function get_heatmap_data( $post_id, $page_number ) {
        global $wpdb;

        $data = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT x_position, y_position, SUM(click_count) as total_clicks
                FROM {$this->heatmap_table}
                WHERE post_id = %d AND page_number = %d
                GROUP BY x_position, y_position",
                $post_id,
                $page_number
            ),
            ARRAY_A
        );

        return $data ?: array();
    }

    /**
     * Get geographic distribution data.
     *
     * @since 1.3.0
     * @param int $post_id Post ID (optional, null for all).
     * @return array
     */
    public function get_geographic_distribution( $post_id = null ) {
        global $wpdb;

        $where = '';
        if ( $post_id ) {
            $where = $wpdb->prepare( 'AND post_id = %d', $post_id );
        }

        $data = $wpdb->get_results(
            "SELECT country_code, COUNT(DISTINCT session_id) as sessions
            FROM {$this->table_name}
            WHERE country_code IS NOT NULL {$where}
            GROUP BY country_code
            ORDER BY sessions DESC
            LIMIT 20",
            ARRAY_A
        );

        return $data ?: array();
    }

    /**
     * Get device distribution data.
     *
     * @since 1.3.0
     * @param int $post_id Post ID (optional, null for all).
     * @return array
     */
    public function get_device_distribution( $post_id = null ) {
        global $wpdb;

        $where = '';
        if ( $post_id ) {
            $where = $wpdb->prepare( 'AND post_id = %d', $post_id );
        }

        $data = $wpdb->get_results(
            "SELECT device_type, browser, os, COUNT(DISTINCT session_id) as sessions
            FROM {$this->table_name}
            WHERE device_type IS NOT NULL {$where}
            GROUP BY device_type, browser, os
            ORDER BY sessions DESC",
            ARRAY_A
        );

        return $data ?: array();
    }
}

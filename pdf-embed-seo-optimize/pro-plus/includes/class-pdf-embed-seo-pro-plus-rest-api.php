<?php
/**
 * Pro+ REST API - Extended API Endpoints.
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
 * REST API class.
 *
 * @since 1.3.0
 */
class PDF_Embed_SEO_Pro_Plus_REST_API {

    /**
     * API namespace.
     *
     * @var string
     */
    const NAMESPACE = 'pdf-embed-seo/v1';

    /**
     * Constructor.
     *
     * @since 1.3.0
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register REST routes.
     *
     * @since 1.3.0
     */
    public function register_routes() {
        // Pro+ status endpoint.
        register_rest_route( self::NAMESPACE, '/pro-plus/status', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_status' ),
                'permission_callback' => '__return_true',
            ),
        ) );

        // Advanced analytics endpoints.
        register_rest_route( self::NAMESPACE, '/pro-plus/analytics/heatmaps/(?P<id>\d+)', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_heatmap_data' ),
                'permission_callback' => array( $this, 'can_view_analytics' ),
                'args'                => array(
                    'id'   => array(
                        'required'          => true,
                        'validate_callback' => array( $this, 'validate_post_id' ),
                    ),
                    'page' => array(
                        'default'           => 1,
                        'validate_callback' => function ( $param ) {
                            return is_numeric( $param ) && $param > 0;
                        },
                    ),
                ),
            ),
        ) );

        register_rest_route( self::NAMESPACE, '/pro-plus/analytics/engagement', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_engagement_overview' ),
                'permission_callback' => array( $this, 'can_view_analytics' ),
                'args'                => array(
                    'period' => array(
                        'default' => '30',
                        'enum'    => array( '7', '30', '90', '365' ),
                    ),
                ),
            ),
        ) );

        register_rest_route( self::NAMESPACE, '/pro-plus/analytics/geographic', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_geographic_data' ),
                'permission_callback' => array( $this, 'can_view_analytics' ),
            ),
        ) );

        register_rest_route( self::NAMESPACE, '/pro-plus/analytics/devices', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_device_data' ),
                'permission_callback' => array( $this, 'can_view_analytics' ),
            ),
        ) );

        // Security endpoints.
        register_rest_route( self::NAMESPACE, '/pro-plus/security/audit-log', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_audit_log' ),
                'permission_callback' => array( $this, 'can_manage_security' ),
                'args'                => array(
                    'post_id'    => array(
                        'validate_callback' => array( $this, 'validate_optional_post_id' ),
                    ),
                    'event_type' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'page'       => array(
                        'default' => 1,
                    ),
                    'per_page'   => array(
                        'default' => 50,
                    ),
                ),
            ),
        ) );

        register_rest_route( self::NAMESPACE, '/pro-plus/security/2fa/send', array(
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'send_2fa_code' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'post_id' => array(
                        'required'          => true,
                        'validate_callback' => array( $this, 'validate_post_id' ),
                    ),
                    'email'   => array(
                        'required'          => true,
                        'validate_callback' => function ( $param ) {
                            return is_email( $param );
                        },
                    ),
                ),
            ),
        ) );

        register_rest_route( self::NAMESPACE, '/pro-plus/security/2fa/verify', array(
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'verify_2fa_code' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'post_id' => array(
                        'required'          => true,
                        'validate_callback' => array( $this, 'validate_post_id' ),
                    ),
                    'email'   => array(
                        'required'          => true,
                        'validate_callback' => function ( $param ) {
                            return is_email( $param );
                        },
                    ),
                    'code'    => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ),
        ) );

        // Webhooks endpoints.
        register_rest_route( self::NAMESPACE, '/pro-plus/webhooks/test', array(
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'test_webhook' ),
                'permission_callback' => array( $this, 'can_manage_webhooks' ),
            ),
        ) );

        register_rest_route( self::NAMESPACE, '/pro-plus/webhooks/log', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_webhook_log' ),
                'permission_callback' => array( $this, 'can_manage_webhooks' ),
                'args'                => array(
                    'status'     => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'event_type' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'page'       => array(
                        'default' => 1,
                    ),
                    'per_page'   => array(
                        'default' => 50,
                    ),
                ),
            ),
        ) );

        // Versioning endpoints.
        register_rest_route( self::NAMESPACE, '/pro-plus/documents/(?P<id>\d+)/versions', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_versions' ),
                'permission_callback' => array( $this, 'can_edit_document' ),
                'args'                => array(
                    'id' => array(
                        'required'          => true,
                        'validate_callback' => array( $this, 'validate_post_id' ),
                    ),
                ),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'create_version' ),
                'permission_callback' => array( $this, 'can_edit_document' ),
                'args'                => array(
                    'id'        => array(
                        'required'          => true,
                        'validate_callback' => array( $this, 'validate_post_id' ),
                    ),
                    'changelog' => array(
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ),
                ),
            ),
        ) );

        register_rest_route( self::NAMESPACE, '/pro-plus/versions/(?P<version_id>\d+)/restore', array(
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'restore_version' ),
                'permission_callback' => array( $this, 'can_restore_version' ),
                'args'                => array(
                    'version_id' => array(
                        'required'          => true,
                        'validate_callback' => function ( $param ) {
                            return is_numeric( $param );
                        },
                    ),
                ),
            ),
        ) );

        // White label endpoints.
        register_rest_route( self::NAMESPACE, '/pro-plus/branding', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_branding' ),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'update_branding' ),
                'permission_callback' => array( $this, 'can_manage_branding' ),
            ),
        ) );

        // Settings endpoint.
        register_rest_route( self::NAMESPACE, '/pro-plus/settings', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_settings' ),
                'permission_callback' => array( $this, 'can_manage_settings' ),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'update_settings' ),
                'permission_callback' => array( $this, 'can_manage_settings' ),
            ),
        ) );

        // License endpoint.
        register_rest_route( self::NAMESPACE, '/pro-plus/license', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_license_status' ),
                'permission_callback' => array( $this, 'can_manage_settings' ),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'activate_license' ),
                'permission_callback' => array( $this, 'can_manage_settings' ),
                'args'                => array(
                    'license_key' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
            ),
        ) );
    }

    /**
     * Validate post ID.
     *
     * @since 1.3.0
     * @param mixed $param Parameter value.
     * @return bool
     */
    public function validate_post_id( $param ) {
        return is_numeric( $param ) && get_post_type( $param ) === 'pdf_document';
    }

    /**
     * Validate optional post ID.
     *
     * @since 1.3.0
     * @param mixed $param Parameter value.
     * @return bool
     */
    public function validate_optional_post_id( $param ) {
        if ( empty( $param ) ) {
            return true;
        }
        return $this->validate_post_id( $param );
    }

    /**
     * Check if user can view analytics.
     *
     * @since 1.3.0
     * @return bool
     */
    public function can_view_analytics() {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Check if user can manage security.
     *
     * @since 1.3.0
     * @return bool
     */
    public function can_manage_security() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Check if user can manage webhooks.
     *
     * @since 1.3.0
     * @return bool
     */
    public function can_manage_webhooks() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Check if user can edit document.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return bool
     */
    public function can_edit_document( $request ) {
        $post_id = $request->get_param( 'id' );
        return current_user_can( 'edit_post', $post_id );
    }

    /**
     * Check if user can restore version.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return bool
     */
    public function can_restore_version( $request ) {
        $version_id = $request->get_param( 'version_id' );
        $pro_plus   = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->versioning ) {
            return false;
        }

        $version = $pro_plus->versioning->get_version( $version_id );

        if ( ! $version ) {
            return false;
        }

        return current_user_can( 'edit_post', $version->post_id );
    }

    /**
     * Check if user can manage branding.
     *
     * @since 1.3.0
     * @return bool
     */
    public function can_manage_branding() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Check if user can manage settings.
     *
     * @since 1.3.0
     * @return bool
     */
    public function can_manage_settings() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Get Pro+ status.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function get_status( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        return new WP_REST_Response( array(
            'active'         => pdf_embed_seo_is_pro_plus(),
            'version'        => PDF_EMBED_SEO_PRO_PLUS_VERSION,
            'license_status' => $pro_plus->get_license_status(),
            'features'       => array(
                'advanced_analytics' => ! empty( $pro_plus->advanced_analytics ),
                'security'           => ! empty( $pro_plus->security ),
                'webhooks'           => ! empty( $pro_plus->webhooks ),
                'white_label'        => ! empty( $pro_plus->white_label ),
                'versioning'         => ! empty( $pro_plus->versioning ),
                'annotations'        => ! empty( $pro_plus->annotations ),
                'compliance'         => ! empty( $pro_plus->compliance ),
            ),
        ), 200 );
    }

    /**
     * Get heatmap data.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function get_heatmap_data( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->advanced_analytics ) {
            return new WP_Error( 'feature_disabled', __( 'Advanced analytics is disabled.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $post_id = $request->get_param( 'id' );
        $page    = $request->get_param( 'page' );

        $data = $pro_plus->advanced_analytics->get_heatmap_data( $post_id, $page );

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Get engagement overview.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function get_engagement_overview( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->advanced_analytics ) {
            return new WP_Error( 'feature_disabled', __( 'Advanced analytics is disabled.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $period = $request->get_param( 'period' );

        // Get top documents by engagement.
        $posts = get_posts( array(
            'post_type'      => 'pdf_document',
            'posts_per_page' => 10,
            'meta_key'       => '_pdf_pro_plus_engagement_score',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
        ) );

        $documents = array();
        foreach ( $posts as $post ) {
            $score = get_post_meta( $post->ID, '_pdf_pro_plus_engagement_score', true );
            $trend = $pro_plus->advanced_analytics->get_engagement_trend( $post->ID );

            $documents[] = array(
                'id'               => $post->ID,
                'title'            => $post->post_title,
                'engagement_score' => intval( $score ),
                'trend'            => $trend,
            );
        }

        return new WP_REST_Response( array(
            'period'           => $period,
            'total_views_today' => $pro_plus->advanced_analytics->get_total_views_today(),
            'avg_engagement'    => $pro_plus->advanced_analytics->get_avg_engagement_score(),
            'top_documents'     => $documents,
        ), 200 );
    }

    /**
     * Get geographic data.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function get_geographic_data( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->advanced_analytics ) {
            return new WP_Error( 'feature_disabled', __( 'Advanced analytics is disabled.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $data = $pro_plus->advanced_analytics->get_geographic_distribution();

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Get device data.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function get_device_data( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->advanced_analytics ) {
            return new WP_Error( 'feature_disabled', __( 'Advanced analytics is disabled.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $data = $pro_plus->advanced_analytics->get_device_distribution();

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Get audit log.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function get_audit_log( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->security ) {
            return new WP_Error( 'feature_disabled', __( 'Security is disabled.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $per_page = min( 100, absint( $request->get_param( 'per_page' ) ) );
        $page     = absint( $request->get_param( 'page' ) );

        $logs = $pro_plus->security->get_audit_log( array(
            'post_id'    => $request->get_param( 'post_id' ),
            'event_type' => $request->get_param( 'event_type' ),
            'limit'      => $per_page,
            'offset'     => ( $page - 1 ) * $per_page,
        ) );

        $data = array();
        foreach ( $logs as $log ) {
            $data[] = array(
                'id'         => $log->id,
                'post_id'    => $log->post_id,
                'user_id'    => $log->user_id,
                'event_type' => $log->event_type,
                'event_data' => json_decode( $log->event_data, true ),
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at,
            );
        }

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Send 2FA code.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function send_2fa_code( $request ) {
        // This is handled by the Security class via AJAX.
        // REST endpoint provided for API clients.
        return new WP_Error( 'not_implemented', __( 'Use AJAX endpoint for 2FA.', 'pdf-embed-seo-optimize' ), array( 'status' => 501 ) );
    }

    /**
     * Verify 2FA code.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function verify_2fa_code( $request ) {
        // This is handled by the Security class via AJAX.
        // REST endpoint provided for API clients.
        return new WP_Error( 'not_implemented', __( 'Use AJAX endpoint for 2FA.', 'pdf-embed-seo-optimize' ), array( 'status' => 501 ) );
    }

    /**
     * Test webhook.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function test_webhook( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->webhooks ) {
            return new WP_Error( 'feature_disabled', __( 'Webhooks are disabled.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $result = $pro_plus->webhooks->send_webhook( 'test', array(
            'message' => __( 'Test webhook from REST API', 'pdf-embed-seo-optimize' ),
            'test'    => true,
        ), false );

        if ( $result ) {
            return new WP_REST_Response( array( 'success' => true ), 200 );
        }

        return new WP_Error( 'webhook_failed', __( 'Failed to send test webhook.', 'pdf-embed-seo-optimize' ), array( 'status' => 500 ) );
    }

    /**
     * Get webhook log.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function get_webhook_log( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->webhooks ) {
            return new WP_Error( 'feature_disabled', __( 'Webhooks are disabled.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $per_page = min( 100, absint( $request->get_param( 'per_page' ) ) );
        $page     = absint( $request->get_param( 'page' ) );

        $logs = $pro_plus->webhooks->get_webhook_log( array(
            'status'     => $request->get_param( 'status' ),
            'event_type' => $request->get_param( 'event_type' ),
            'limit'      => $per_page,
            'offset'     => ( $page - 1 ) * $per_page,
        ) );

        return new WP_REST_Response( $logs, 200 );
    }

    /**
     * Get versions.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function get_versions( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->versioning ) {
            return new WP_Error( 'feature_disabled', __( 'Versioning is disabled.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $post_id  = $request->get_param( 'id' );
        $versions = $pro_plus->versioning->get_versions( $post_id );

        $data = array();
        foreach ( $versions as $version ) {
            $data[] = array(
                'id'             => $version->id,
                'version_number' => $version->version_number,
                'file_name'      => $version->file_name,
                'file_size'      => $version->file_size,
                'changelog'      => $version->changelog,
                'created_by'     => $version->created_by,
                'created_at'     => $version->created_at,
            );
        }

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Create version.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function create_version( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->versioning ) {
            return new WP_Error( 'feature_disabled', __( 'Versioning is disabled.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $post_id   = $request->get_param( 'id' );
        $changelog = $request->get_param( 'changelog' );
        $file_id   = get_post_meta( $post_id, '_pdf_file_id', true );

        if ( ! $file_id ) {
            return new WP_Error( 'no_file', __( 'No PDF file attached to this document.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $version_id = $pro_plus->versioning->create_version( $post_id, $file_id, $changelog );

        if ( $version_id ) {
            $version = $pro_plus->versioning->get_version( $version_id );

            return new WP_REST_Response( array(
                'id'             => $version->id,
                'version_number' => $version->version_number,
                'created_at'     => $version->created_at,
            ), 201 );
        }

        return new WP_Error( 'version_failed', __( 'Failed to create version.', 'pdf-embed-seo-optimize' ), array( 'status' => 500 ) );
    }

    /**
     * Restore version.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function restore_version( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        if ( ! $pro_plus->versioning ) {
            return new WP_Error( 'feature_disabled', __( 'Versioning is disabled.', 'pdf-embed-seo-optimize' ), array( 'status' => 400 ) );
        }

        $version_id = $request->get_param( 'version_id' );

        if ( $pro_plus->versioning->restore_version( $version_id ) ) {
            return new WP_REST_Response( array( 'success' => true ), 200 );
        }

        return new WP_Error( 'restore_failed', __( 'Failed to restore version.', 'pdf-embed-seo-optimize' ), array( 'status' => 500 ) );
    }

    /**
     * Get branding settings.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function get_branding( $request ) {
        $branding = apply_filters( 'pdf_embed_seo_branding', array(
            'name'        => 'PDF Embed & SEO',
            'description' => '',
            'logo_url'    => '',
            'color'       => '#764ba2',
        ) );

        return new WP_REST_Response( $branding, 200 );
    }

    /**
     * Update branding settings.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function update_branding( $request ) {
        $name = $request->get_param( 'name' );

        if ( $name ) {
            update_option( 'pdf_pro_plus_brand_name', sanitize_text_field( $name ) );
        }

        $color = $request->get_param( 'color' );

        if ( $color ) {
            update_option( 'pdf_pro_plus_brand_color', sanitize_hex_color( $color ) );
        }

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    /**
     * Get settings.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function get_settings( $request ) {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        // Remove sensitive data.
        unset( $settings['webhook_secret'] );

        return new WP_REST_Response( $settings, 200 );
    }

    /**
     * Update settings.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function update_settings( $request ) {
        $current  = pdf_embed_seo_pro_plus()->get_settings();
        $new      = array_merge( $current, $request->get_params() );

        update_option( 'pdf_embed_seo_pro_plus_settings', $new );

        return new WP_REST_Response( array( 'success' => true ), 200 );
    }

    /**
     * Get license status.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function get_license_status( $request ) {
        $pro_plus = pdf_embed_seo_pro_plus();

        return new WP_REST_Response( array(
            'status'  => $pro_plus->get_license_status(),
            'is_valid' => $pro_plus->is_license_valid(),
            'expires' => get_option( 'pdf_embed_seo_pro_plus_license_expires', '' ),
        ), 200 );
    }

    /**
     * Activate license.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function activate_license( $request ) {
        $license_key = $request->get_param( 'license_key' );

        update_option( 'pdf_embed_seo_pro_plus_license_key', $license_key );

        // Re-validate.
        $pro_plus = pdf_embed_seo_pro_plus();

        // Force recheck by clearing status.
        delete_option( 'pdf_embed_seo_pro_plus_license_status' );

        // Reinitialize to validate.
        return new WP_REST_Response( array(
            'status' => $pro_plus->get_license_status(),
            'valid'  => $pro_plus->is_license_valid(),
        ), 200 );
    }
}

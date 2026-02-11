<?php
/**
 * Pro+ Annotations - PDF Annotations & Digital Signatures.
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
 * Annotations class.
 *
 * @since 1.3.0
 */
class PDF_Embed_SEO_Pro_Plus_Annotations {

    /**
     * Annotations table name.
     *
     * @var string
     */
    private $table_name;

    /**
     * Signatures table name.
     *
     * @var string
     */
    private $signatures_table;

    /**
     * Constructor.
     *
     * @since 1.3.0
     */
    public function __construct() {
        global $wpdb;
        $this->table_name       = $wpdb->prefix . 'pdf_annotations';
        $this->signatures_table = $wpdb->prefix . 'pdf_signatures';

        $this->init_hooks();
        $this->maybe_create_tables();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.3.0
     */
    private function init_hooks() {
        // AJAX handlers.
        add_action( 'wp_ajax_pdf_save_annotation', array( $this, 'ajax_save_annotation' ) );
        add_action( 'wp_ajax_pdf_delete_annotation', array( $this, 'ajax_delete_annotation' ) );
        add_action( 'wp_ajax_pdf_get_annotations', array( $this, 'ajax_get_annotations' ) );
        add_action( 'wp_ajax_nopriv_pdf_get_annotations', array( $this, 'ajax_get_annotations' ) );

        // Signature handlers.
        add_action( 'wp_ajax_pdf_save_signature', array( $this, 'ajax_save_signature' ) );
        add_action( 'wp_ajax_pdf_verify_signature', array( $this, 'ajax_verify_signature' ) );

        // Enqueue annotation scripts.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

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

        // Annotations table.
        $sql_annotations = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            page_number int(11) NOT NULL,
            annotation_type varchar(20) NOT NULL,
            x_position decimal(10,4) NOT NULL,
            y_position decimal(10,4) NOT NULL,
            width decimal(10,4) DEFAULT NULL,
            height decimal(10,4) DEFAULT NULL,
            content text,
            color varchar(20) DEFAULT '#ffff00',
            opacity decimal(3,2) DEFAULT 1.00,
            metadata text,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY page_number (post_id, page_number)
        ) {$charset_collate};";

        // Signatures table.
        $sql_signatures = "CREATE TABLE IF NOT EXISTS {$this->signatures_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            page_number int(11) NOT NULL,
            x_position decimal(10,4) NOT NULL,
            y_position decimal(10,4) NOT NULL,
            width decimal(10,4) NOT NULL,
            height decimal(10,4) NOT NULL,
            signature_data longtext NOT NULL,
            signature_hash varchar(64) NOT NULL,
            signer_name varchar(255) NOT NULL,
            signer_email varchar(255) NOT NULL,
            signer_ip varchar(45),
            signed_at datetime NOT NULL,
            verified tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            KEY signature_hash (signature_hash)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_annotations );
        dbDelta( $sql_signatures );
    }

    /**
     * Enqueue annotation scripts.
     *
     * @since 1.3.0
     */
    public function enqueue_scripts() {
        if ( ! is_singular( 'pdf_document' ) ) {
            return;
        }

        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( empty( $settings['allow_user_annotations'] ) && empty( $settings['signature_enabled'] ) ) {
            return;
        }

        wp_enqueue_script(
            'pdf-pro-plus-annotations',
            PDF_EMBED_SEO_PRO_PLUS_URL . 'public/js/annotations.js',
            array( 'jquery', 'pdf-embed-seo-pro-plus' ),
            PDF_EMBED_SEO_PRO_PLUS_VERSION,
            true
        );

        wp_localize_script( 'pdf-pro-plus-annotations', 'pdfAnnotations', array(
            'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
            'restUrl'            => rest_url( 'pdf-embed-seo/v1' ),
            'postId'             => get_the_ID(),
            'userId'             => get_current_user_id(),
            'nonce'              => wp_create_nonce( 'pdf_annotations' ),
            'canAnnotate'        => $this->can_annotate( get_the_ID() ),
            'canSign'            => $this->can_sign( get_the_ID() ),
            'annotationsEnabled' => ! empty( $settings['allow_user_annotations'] ),
            'signaturesEnabled'  => ! empty( $settings['signature_enabled'] ),
            'i18n'               => array(
                'addNote'      => __( 'Add Note', 'pdf-embed-seo-optimize' ),
                'addHighlight' => __( 'Add Highlight', 'pdf-embed-seo-optimize' ),
                'addSignature' => __( 'Add Signature', 'pdf-embed-seo-optimize' ),
                'delete'       => __( 'Delete', 'pdf-embed-seo-optimize' ),
                'save'         => __( 'Save', 'pdf-embed-seo-optimize' ),
                'cancel'       => __( 'Cancel', 'pdf-embed-seo-optimize' ),
                'signHere'     => __( 'Sign Here', 'pdf-embed-seo-optimize' ),
                'clearSign'    => __( 'Clear', 'pdf-embed-seo-optimize' ),
            ),
        ) );
    }

    /**
     * Check if user can annotate.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return bool
     */
    public function can_annotate( $post_id ) {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( empty( $settings['allow_user_annotations'] ) ) {
            return false;
        }

        // Must be logged in.
        if ( ! is_user_logged_in() ) {
            return false;
        }

        // Check post-specific settings.
        $disable = get_post_meta( $post_id, '_pdf_pro_plus_disable_annotations', true );

        return empty( $disable );
    }

    /**
     * Check if user can sign.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return bool
     */
    public function can_sign( $post_id ) {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( empty( $settings['signature_enabled'] ) ) {
            return false;
        }

        // Must be logged in.
        if ( ! is_user_logged_in() ) {
            return false;
        }

        return true;
    }

    /**
     * Check if post has annotations.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return bool
     */
    public function has_annotations( $post_id ) {
        return $this->get_annotation_count( $post_id ) > 0;
    }

    /**
     * Get annotation count.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return int
     */
    public function get_annotation_count( $post_id ) {
        global $wpdb;

        return intval( $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} WHERE post_id = %d",
                $post_id
            )
        ) );
    }

    /**
     * Get annotations for a post.
     *
     * @since 1.3.0
     * @param int      $post_id     Post ID.
     * @param int|null $page_number Page number (optional).
     * @param int|null $user_id     User ID (optional).
     * @return array
     */
    public function get_annotations( $post_id, $page_number = null, $user_id = null ) {
        global $wpdb;

        $where = array( 'post_id = %d' );
        $args  = array( $post_id );

        if ( $page_number !== null ) {
            $where[] = 'page_number = %d';
            $args[]  = $page_number;
        }

        if ( $user_id !== null ) {
            $where[] = 'user_id = %d';
            $args[]  = $user_id;
        }

        $where_clause = implode( ' AND ', $where );

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE {$where_clause} ORDER BY page_number, created_at",
                ...$args
            )
        );
    }

    /**
     * Save annotation.
     *
     * @since 1.3.0
     * @param array $data Annotation data.
     * @return int|false Annotation ID or false.
     */
    public function save_annotation( $data ) {
        global $wpdb;

        $defaults = array(
            'post_id'         => 0,
            'user_id'         => get_current_user_id(),
            'page_number'     => 1,
            'annotation_type' => 'note',
            'x_position'      => 0,
            'y_position'      => 0,
            'width'           => null,
            'height'          => null,
            'content'         => '',
            'color'           => '#ffff00',
            'opacity'         => 1.0,
            'metadata'        => array(),
        );

        $data = wp_parse_args( $data, $defaults );

        if ( ! $data['post_id'] || ! $data['user_id'] ) {
            return false;
        }

        $wpdb->insert(
            $this->table_name,
            array(
                'post_id'         => $data['post_id'],
                'user_id'         => $data['user_id'],
                'page_number'     => $data['page_number'],
                'annotation_type' => $data['annotation_type'],
                'x_position'      => $data['x_position'],
                'y_position'      => $data['y_position'],
                'width'           => $data['width'],
                'height'          => $data['height'],
                'content'         => $data['content'],
                'color'           => $data['color'],
                'opacity'         => $data['opacity'],
                'metadata'        => wp_json_encode( $data['metadata'] ),
                'created_at'      => current_time( 'mysql' ),
                'updated_at'      => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%d', '%s', '%f', '%f', '%f', '%f', '%s', '%s', '%f', '%s', '%s', '%s' )
        );

        return $wpdb->insert_id;
    }

    /**
     * Update annotation.
     *
     * @since 1.3.0
     * @param int   $annotation_id Annotation ID.
     * @param array $data          Update data.
     * @return bool
     */
    public function update_annotation( $annotation_id, $data ) {
        global $wpdb;

        $allowed = array( 'content', 'color', 'opacity', 'x_position', 'y_position', 'width', 'height' );
        $update  = array();
        $format  = array();

        foreach ( $allowed as $key ) {
            if ( isset( $data[ $key ] ) ) {
                $update[ $key ] = $data[ $key ];
                $format[]       = in_array( $key, array( 'x_position', 'y_position', 'width', 'height', 'opacity' ), true ) ? '%f' : '%s';
            }
        }

        if ( empty( $update ) ) {
            return false;
        }

        $update['updated_at'] = current_time( 'mysql' );
        $format[]             = '%s';

        return $wpdb->update(
            $this->table_name,
            $update,
            array( 'id' => $annotation_id ),
            $format,
            array( '%d' )
        ) !== false;
    }

    /**
     * Delete annotation.
     *
     * @since 1.3.0
     * @param int $annotation_id Annotation ID.
     * @return bool
     */
    public function delete_annotation( $annotation_id ) {
        global $wpdb;

        return $wpdb->delete(
            $this->table_name,
            array( 'id' => $annotation_id ),
            array( '%d' )
        ) !== false;
    }

    /**
     * AJAX: Save annotation.
     *
     * @since 1.3.0
     */
    public function ajax_save_annotation() {
        check_ajax_referer( 'pdf_annotations', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        if ( ! $post_id || ! $this->can_annotate( $post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'pdf-embed-seo-optimize' ) ) );
        }

        $data = array(
            'post_id'         => $post_id,
            'page_number'     => isset( $_POST['page_number'] ) ? absint( $_POST['page_number'] ) : 1,
            'annotation_type' => isset( $_POST['annotation_type'] ) ? sanitize_text_field( wp_unslash( $_POST['annotation_type'] ) ) : 'note',
            'x_position'      => isset( $_POST['x_position'] ) ? floatval( $_POST['x_position'] ) : 0,
            'y_position'      => isset( $_POST['y_position'] ) ? floatval( $_POST['y_position'] ) : 0,
            'width'           => isset( $_POST['width'] ) ? floatval( $_POST['width'] ) : null,
            'height'          => isset( $_POST['height'] ) ? floatval( $_POST['height'] ) : null,
            'content'         => isset( $_POST['content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['content'] ) ) : '',
            'color'           => isset( $_POST['color'] ) ? sanitize_hex_color( wp_unslash( $_POST['color'] ) ) : '#ffff00',
            'opacity'         => isset( $_POST['opacity'] ) ? min( 1, max( 0, floatval( $_POST['opacity'] ) ) ) : 1,
        );

        $annotation_id = $this->save_annotation( $data );

        if ( $annotation_id ) {
            wp_send_json_success( array(
                'id'      => $annotation_id,
                'message' => __( 'Annotation saved.', 'pdf-embed-seo-optimize' ),
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to save annotation.', 'pdf-embed-seo-optimize' ) ) );
        }
    }

    /**
     * AJAX: Delete annotation.
     *
     * @since 1.3.0
     */
    public function ajax_delete_annotation() {
        check_ajax_referer( 'pdf_annotations', 'nonce' );

        $annotation_id = isset( $_POST['annotation_id'] ) ? absint( $_POST['annotation_id'] ) : 0;

        if ( ! $annotation_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid annotation.', 'pdf-embed-seo-optimize' ) ) );
        }

        global $wpdb;

        $annotation = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id = %d",
                $annotation_id
            )
        );

        if ( ! $annotation ) {
            wp_send_json_error( array( 'message' => __( 'Annotation not found.', 'pdf-embed-seo-optimize' ) ) );
        }

        // Only owner or admin can delete.
        if ( $annotation->user_id !== get_current_user_id() && ! current_user_can( 'edit_post', $annotation->post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'pdf-embed-seo-optimize' ) ) );
        }

        if ( $this->delete_annotation( $annotation_id ) ) {
            wp_send_json_success( array( 'message' => __( 'Annotation deleted.', 'pdf-embed-seo-optimize' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete annotation.', 'pdf-embed-seo-optimize' ) ) );
        }
    }

    /**
     * AJAX: Get annotations.
     *
     * @since 1.3.0
     */
    public function ajax_get_annotations() {
        check_ajax_referer( 'pdf_annotations', 'nonce' );

        $post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        $page_number = isset( $_POST['page_number'] ) ? absint( $_POST['page_number'] ) : null;

        if ( ! $post_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid request.', 'pdf-embed-seo-optimize' ) ) );
        }

        $annotations = $this->get_annotations( $post_id, $page_number );

        $data = array();
        foreach ( $annotations as $annotation ) {
            $user = get_userdata( $annotation->user_id );

            $data[] = array(
                'id'              => $annotation->id,
                'page_number'     => $annotation->page_number,
                'annotation_type' => $annotation->annotation_type,
                'x_position'      => floatval( $annotation->x_position ),
                'y_position'      => floatval( $annotation->y_position ),
                'width'           => $annotation->width ? floatval( $annotation->width ) : null,
                'height'          => $annotation->height ? floatval( $annotation->height ) : null,
                'content'         => $annotation->content,
                'color'           => $annotation->color,
                'opacity'         => floatval( $annotation->opacity ),
                'user_name'       => $user ? $user->display_name : __( 'Unknown', 'pdf-embed-seo-optimize' ),
                'is_owner'        => $annotation->user_id === get_current_user_id(),
                'created_at'      => $annotation->created_at,
            );
        }

        wp_send_json_success( $data );
    }

    /**
     * AJAX: Save signature.
     *
     * @since 1.3.0
     */
    public function ajax_save_signature() {
        check_ajax_referer( 'pdf_annotations', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        if ( ! $post_id || ! $this->can_sign( $post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'pdf-embed-seo-optimize' ) ) );
        }

        $user = wp_get_current_user();

        $signature_data = isset( $_POST['signature_data'] ) ? sanitize_text_field( wp_unslash( $_POST['signature_data'] ) ) : '';

        if ( empty( $signature_data ) ) {
            wp_send_json_error( array( 'message' => __( 'Signature data is required.', 'pdf-embed-seo-optimize' ) ) );
        }

        global $wpdb;

        $signature_hash = hash( 'sha256', $signature_data . $user->ID . time() );

        $wpdb->insert(
            $this->signatures_table,
            array(
                'post_id'        => $post_id,
                'user_id'        => $user->ID,
                'page_number'    => isset( $_POST['page_number'] ) ? absint( $_POST['page_number'] ) : 1,
                'x_position'     => isset( $_POST['x_position'] ) ? floatval( $_POST['x_position'] ) : 0,
                'y_position'     => isset( $_POST['y_position'] ) ? floatval( $_POST['y_position'] ) : 0,
                'width'          => isset( $_POST['width'] ) ? floatval( $_POST['width'] ) : 200,
                'height'         => isset( $_POST['height'] ) ? floatval( $_POST['height'] ) : 80,
                'signature_data' => $signature_data,
                'signature_hash' => $signature_hash,
                'signer_name'    => $user->display_name,
                'signer_email'   => $user->user_email,
                'signer_ip'      => $this->get_client_ip(),
                'signed_at'      => current_time( 'mysql' ),
                'verified'       => 1,
            ),
            array( '%d', '%d', '%d', '%f', '%f', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%d' )
        );

        $signature_id = $wpdb->insert_id;

        if ( $signature_id ) {
            /**
             * Action fired when a signature is added.
             *
             * @since 1.3.0
             * @param int $signature_id Signature ID.
             * @param int $post_id      Post ID.
             * @param int $user_id      User ID.
             */
            do_action( 'pdf_pro_plus_signature_added', $signature_id, $post_id, $user->ID );

            wp_send_json_success( array(
                'id'      => $signature_id,
                'hash'    => $signature_hash,
                'message' => __( 'Signature saved successfully.', 'pdf-embed-seo-optimize' ),
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to save signature.', 'pdf-embed-seo-optimize' ) ) );
        }
    }

    /**
     * AJAX: Verify signature.
     *
     * @since 1.3.0
     */
    public function ajax_verify_signature() {
        $hash = isset( $_POST['hash'] ) ? sanitize_text_field( wp_unslash( $_POST['hash'] ) ) : '';

        if ( ! $hash ) {
            wp_send_json_error( array( 'message' => __( 'Invalid signature hash.', 'pdf-embed-seo-optimize' ) ) );
        }

        global $wpdb;

        $signature = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->signatures_table} WHERE signature_hash = %s",
                $hash
            )
        );

        if ( ! $signature ) {
            wp_send_json_error( array(
                'verified' => false,
                'message'  => __( 'Signature not found.', 'pdf-embed-seo-optimize' ),
            ) );
        }

        $post = get_post( $signature->post_id );

        wp_send_json_success( array(
            'verified'    => true,
            'signer_name' => $signature->signer_name,
            'signed_at'   => $signature->signed_at,
            'document'    => $post ? $post->post_title : '',
            'message'     => __( 'Signature verified.', 'pdf-embed-seo-optimize' ),
        ) );
    }

    /**
     * Get signatures for a post.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return array
     */
    public function get_signatures( $post_id ) {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->signatures_table} WHERE post_id = %d ORDER BY signed_at",
                $post_id
            )
        );
    }

    /**
     * Register REST routes.
     *
     * @since 1.3.0
     */
    public function register_rest_routes() {
        register_rest_route( 'pdf-embed-seo/v1', '/documents/(?P<id>\d+)/annotations', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'rest_get_annotations' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'id' => array(
                        'validate_callback' => function ( $param ) {
                            return is_numeric( $param );
                        },
                    ),
                    'page' => array(
                        'validate_callback' => function ( $param ) {
                            return is_numeric( $param );
                        },
                    ),
                ),
            ),
        ) );

        register_rest_route( 'pdf-embed-seo/v1', '/documents/(?P<id>\d+)/signatures', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'rest_get_signatures' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                    'id' => array(
                        'validate_callback' => function ( $param ) {
                            return is_numeric( $param );
                        },
                    ),
                ),
            ),
        ) );
    }

    /**
     * REST: Get annotations.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function rest_get_annotations( $request ) {
        $post_id = $request->get_param( 'id' );
        $page    = $request->get_param( 'page' );

        $annotations = $this->get_annotations( $post_id, $page );

        $data = array();
        foreach ( $annotations as $annotation ) {
            $data[] = array(
                'id'              => $annotation->id,
                'page_number'     => $annotation->page_number,
                'annotation_type' => $annotation->annotation_type,
                'x_position'      => floatval( $annotation->x_position ),
                'y_position'      => floatval( $annotation->y_position ),
                'content'         => $annotation->content,
                'color'           => $annotation->color,
            );
        }

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * REST: Get signatures.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function rest_get_signatures( $request ) {
        $post_id    = $request->get_param( 'id' );
        $signatures = $this->get_signatures( $post_id );

        $data = array();
        foreach ( $signatures as $signature ) {
            $data[] = array(
                'id'          => $signature->id,
                'page_number' => $signature->page_number,
                'signer_name' => $signature->signer_name,
                'signed_at'   => $signature->signed_at,
                'verified'    => (bool) $signature->verified,
            );
        }

        return new WP_REST_Response( $data, 200 );
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
}

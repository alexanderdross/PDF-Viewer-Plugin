<?php
/**
 * Pro+ Versioning - Document Version Control.
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
 * Versioning class.
 *
 * @since 1.3.0
 */
class PDF_Embed_SEO_Pro_Plus_Versioning {

    /**
     * Versions table name.
     *
     * @var string
     */
    private $table_name;

    /**
     * Constructor.
     *
     * @since 1.3.0
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'pdf_versions';

        $this->init_hooks();
        $this->maybe_create_tables();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.3.0
     */
    private function init_hooks() {
        // Create version on PDF file change.
        add_action( 'save_post_pdf_document', array( $this, 'maybe_create_version' ), 15, 3 );

        // Add version column to admin list.
        add_filter( 'manage_pdf_document_posts_columns', array( $this, 'add_version_column' ), 20 );
        add_action( 'manage_pdf_document_posts_custom_column', array( $this, 'render_version_column' ), 20, 2 );

        // AJAX handlers.
        add_action( 'wp_ajax_pdf_restore_version', array( $this, 'ajax_restore_version' ) );
        add_action( 'wp_ajax_pdf_delete_version', array( $this, 'ajax_delete_version' ) );
        add_action( 'wp_ajax_pdf_compare_versions', array( $this, 'ajax_compare_versions' ) );

        // REST API endpoints.
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

        // Cleanup old versions.
        add_action( 'pdf_pro_plus_cleanup_versions', array( $this, 'cleanup_old_versions' ) );

        if ( ! wp_next_scheduled( 'pdf_pro_plus_cleanup_versions' ) ) {
            wp_schedule_event( time(), 'daily', 'pdf_pro_plus_cleanup_versions' );
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

        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            version_number int(11) NOT NULL,
            file_id bigint(20) NOT NULL,
            file_url varchar(500) NOT NULL,
            file_name varchar(255) NOT NULL,
            file_size bigint(20) NOT NULL DEFAULT 0,
            file_hash varchar(64) NOT NULL,
            changelog text,
            created_by bigint(20) NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY post_version (post_id, version_number),
            KEY post_id (post_id),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Maybe create a version when PDF file changes.
     *
     * @since 1.3.0
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @param bool    $update  Whether this is an update.
     */
    public function maybe_create_version( $post_id, $post, $update ) {
        if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
            return;
        }

        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( empty( $settings['auto_version'] ) ) {
            return;
        }

        $file_id = get_post_meta( $post_id, '_pdf_file_id', true );

        if ( ! $file_id ) {
            return;
        }

        // Get file hash.
        $file_path = get_attached_file( $file_id );

        if ( ! $file_path || ! file_exists( $file_path ) ) {
            return;
        }

        $file_hash = md5_file( $file_path );

        // Check if file has changed.
        $last_version = $this->get_latest_version( $post_id );

        if ( $last_version && $last_version->file_hash === $file_hash ) {
            return; // No change.
        }

        // Create new version.
        $this->create_version( $post_id, $file_id );
    }

    /**
     * Create a version.
     *
     * @since 1.3.0
     * @param int    $post_id   Post ID.
     * @param int    $file_id   File attachment ID.
     * @param string $changelog Optional changelog.
     * @return int|false Version ID on success, false on failure.
     */
    public function create_version( $post_id, $file_id, $changelog = '' ) {
        global $wpdb;

        $file_path = get_attached_file( $file_id );

        if ( ! $file_path || ! file_exists( $file_path ) ) {
            return false;
        }

        $file_url  = wp_get_attachment_url( $file_id );
        $file_name = basename( $file_path );
        $file_size = filesize( $file_path );
        $file_hash = md5_file( $file_path );

        // Get next version number.
        $version_number = $this->get_next_version_number( $post_id );

        $wpdb->insert(
            $this->table_name,
            array(
                'post_id'        => $post_id,
                'version_number' => $version_number,
                'file_id'        => $file_id,
                'file_url'       => $file_url,
                'file_name'      => $file_name,
                'file_size'      => $file_size,
                'file_hash'      => $file_hash,
                'changelog'      => $changelog,
                'created_by'     => get_current_user_id(),
                'created_at'     => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%d', '%s', '%s', '%d', '%s', '%s', '%d', '%s' )
        );

        $version_id = $wpdb->insert_id;

        if ( $version_id ) {
            // Update current version meta.
            update_post_meta( $post_id, '_pdf_current_version', $version_number );

            /**
             * Action fired when a new version is created.
             *
             * @since 1.3.0
             * @param int $version_id     Version ID.
             * @param int $post_id        Post ID.
             * @param int $version_number Version number.
             */
            do_action( 'pdf_pro_plus_version_created', $version_id, $post_id, $version_number );
        }

        return $version_id;
    }

    /**
     * Get next version number for a post.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return int
     */
    private function get_next_version_number( $post_id ) {
        global $wpdb;

        $max = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT MAX(version_number) FROM {$this->table_name} WHERE post_id = %d",
                $post_id
            )
        );

        return intval( $max ) + 1;
    }

    /**
     * Get latest version for a post.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return object|null
     */
    public function get_latest_version( $post_id ) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE post_id = %d ORDER BY version_number DESC LIMIT 1",
                $post_id
            )
        );
    }

    /**
     * Get all versions for a post.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return array
     */
    public function get_versions( $post_id ) {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE post_id = %d ORDER BY version_number DESC",
                $post_id
            )
        );
    }

    /**
     * Get a specific version.
     *
     * @since 1.3.0
     * @param int $version_id Version ID.
     * @return object|null
     */
    public function get_version( $version_id ) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id = %d",
                $version_id
            )
        );
    }

    /**
     * Check if post has versions.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return bool
     */
    public function has_versions( $post_id ) {
        return $this->get_version_count( $post_id ) > 0;
    }

    /**
     * Get version count for a post.
     *
     * @since 1.3.0
     * @param int $post_id Post ID.
     * @return int
     */
    public function get_version_count( $post_id ) {
        global $wpdb;

        return intval( $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} WHERE post_id = %d",
                $post_id
            )
        ) );
    }

    /**
     * Restore a version.
     *
     * @since 1.3.0
     * @param int $version_id Version ID.
     * @return bool
     */
    public function restore_version( $version_id ) {
        $version = $this->get_version( $version_id );

        if ( ! $version ) {
            return false;
        }

        // Update post meta to use this file.
        update_post_meta( $version->post_id, '_pdf_file_id', $version->file_id );
        update_post_meta( $version->post_id, '_pdf_file_url', $version->file_url );
        update_post_meta( $version->post_id, '_pdf_current_version', $version->version_number );

        /**
         * Action fired when a version is restored.
         *
         * @since 1.3.0
         * @param int $version_id     Version ID.
         * @param int $post_id        Post ID.
         * @param int $version_number Version number.
         */
        do_action( 'pdf_pro_plus_version_restored', $version_id, $version->post_id, $version->version_number );

        return true;
    }

    /**
     * Delete a version.
     *
     * @since 1.3.0
     * @param int $version_id Version ID.
     * @return bool
     */
    public function delete_version( $version_id ) {
        global $wpdb;

        $version = $this->get_version( $version_id );

        if ( ! $version ) {
            return false;
        }

        // Don't delete the current active version.
        $current = get_post_meta( $version->post_id, '_pdf_current_version', true );

        if ( intval( $current ) === intval( $version->version_number ) ) {
            return false;
        }

        $result = $wpdb->delete(
            $this->table_name,
            array( 'id' => $version_id ),
            array( '%d' )
        );

        return $result !== false;
    }

    /**
     * Add version column.
     *
     * @since 1.3.0
     * @param array $columns Columns.
     * @return array
     */
    public function add_version_column( $columns ) {
        $new_columns = array();

        foreach ( $columns as $key => $value ) {
            $new_columns[ $key ] = $value;

            if ( 'title' === $key ) {
                $new_columns['pdf_version'] = __( 'Version', 'pdf-embed-seo-optimize' );
            }
        }

        return $new_columns;
    }

    /**
     * Render version column.
     *
     * @since 1.3.0
     * @param string $column  Column name.
     * @param int    $post_id Post ID.
     */
    public function render_version_column( $column, $post_id ) {
        if ( 'pdf_version' !== $column ) {
            return;
        }

        $current_version = get_post_meta( $post_id, '_pdf_current_version', true );
        $version_count   = $this->get_version_count( $post_id );

        if ( $current_version ) {
            echo 'v' . esc_html( $current_version );
            if ( $version_count > 1 ) {
                echo ' <span class="pdf-version-count">(' . esc_html( $version_count ) . ' ' . esc_html__( 'versions', 'pdf-embed-seo-optimize' ) . ')</span>';
            }
        } else {
            echo '<span style="color: #999;">-</span>';
        }
    }

    /**
     * Render versions meta box.
     *
     * @since 1.3.0
     * @param WP_Post $post Post object.
     */
    public function render_meta_box( $post ) {
        $versions        = $this->get_versions( $post->ID );
        $current_version = get_post_meta( $post->ID, '_pdf_current_version', true );

        wp_nonce_field( 'pdf_pro_plus_versioning', 'pdf_pro_plus_versioning_nonce' );
        ?>
        <div class="pdf-versions-list">
            <?php if ( empty( $versions ) ) : ?>
                <p class="description"><?php esc_html_e( 'No versions saved yet. Save the document to create a version.', 'pdf-embed-seo-optimize' ); ?></p>
            <?php else : ?>
                <ul class="pdf-version-items">
                    <?php foreach ( $versions as $version ) : ?>
                        <li class="pdf-version-item <?php echo intval( $current_version ) === intval( $version->version_number ) ? 'current' : ''; ?>">
                            <div class="version-info">
                                <strong>v<?php echo esc_html( $version->version_number ); ?></strong>
                                <?php if ( intval( $current_version ) === intval( $version->version_number ) ) : ?>
                                    <span class="current-badge"><?php esc_html_e( 'Current', 'pdf-embed-seo-optimize' ); ?></span>
                                <?php endif; ?>
                                <span class="version-date"><?php echo esc_html( human_time_diff( strtotime( $version->created_at ), time() ) ); ?> <?php esc_html_e( 'ago', 'pdf-embed-seo-optimize' ); ?></span>
                            </div>
                            <div class="version-meta">
                                <span class="file-size"><?php echo esc_html( size_format( $version->file_size ) ); ?></span>
                            </div>
                            <?php if ( intval( $current_version ) !== intval( $version->version_number ) ) : ?>
                                <div class="version-actions">
                                    <button type="button" class="button button-small pdf-restore-version" data-version="<?php echo esc_attr( $version->id ); ?>">
                                        <?php esc_html_e( 'Restore', 'pdf-embed-seo-optimize' ); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <style>
            .pdf-version-items { margin: 0; padding: 0; list-style: none; }
            .pdf-version-item { padding: 8px 0; border-bottom: 1px solid #ddd; }
            .pdf-version-item:last-child { border-bottom: none; }
            .pdf-version-item.current { background: #f0f6fc; margin: 0 -12px; padding: 8px 12px; }
            .version-info { display: flex; align-items: center; gap: 8px; }
            .current-badge { background: #764ba2; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
            .version-date { color: #666; font-size: 12px; }
            .version-meta { color: #666; font-size: 11px; margin-top: 2px; }
            .version-actions { margin-top: 5px; }
        </style>
        <?php
    }

    /**
     * AJAX: Restore version.
     *
     * @since 1.3.0
     */
    public function ajax_restore_version() {
        check_ajax_referer( 'pdf_pro_plus_admin', 'nonce' );

        $version_id = isset( $_POST['version_id'] ) ? absint( $_POST['version_id'] ) : 0;

        if ( ! $version_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid version.', 'pdf-embed-seo-optimize' ) ) );
        }

        $version = $this->get_version( $version_id );

        if ( ! $version || ! current_user_can( 'edit_post', $version->post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'pdf-embed-seo-optimize' ) ) );
        }

        if ( $this->restore_version( $version_id ) ) {
            wp_send_json_success( array(
                'message' => sprintf(
                    /* translators: %d: Version number */
                    __( 'Restored to version %d.', 'pdf-embed-seo-optimize' ),
                    $version->version_number
                ),
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to restore version.', 'pdf-embed-seo-optimize' ) ) );
        }
    }

    /**
     * AJAX: Delete version.
     *
     * @since 1.3.0
     */
    public function ajax_delete_version() {
        check_ajax_referer( 'pdf_pro_plus_admin', 'nonce' );

        $version_id = isset( $_POST['version_id'] ) ? absint( $_POST['version_id'] ) : 0;

        if ( ! $version_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid version.', 'pdf-embed-seo-optimize' ) ) );
        }

        $version = $this->get_version( $version_id );

        if ( ! $version || ! current_user_can( 'edit_post', $version->post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'pdf-embed-seo-optimize' ) ) );
        }

        if ( $this->delete_version( $version_id ) ) {
            wp_send_json_success( array( 'message' => __( 'Version deleted.', 'pdf-embed-seo-optimize' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Cannot delete the current version.', 'pdf-embed-seo-optimize' ) ) );
        }
    }

    /**
     * AJAX: Compare versions.
     *
     * @since 1.3.0
     */
    public function ajax_compare_versions() {
        check_ajax_referer( 'pdf_pro_plus_admin', 'nonce' );

        $version_a = isset( $_POST['version_a'] ) ? absint( $_POST['version_a'] ) : 0;
        $version_b = isset( $_POST['version_b'] ) ? absint( $_POST['version_b'] ) : 0;

        if ( ! $version_a || ! $version_b ) {
            wp_send_json_error( array( 'message' => __( 'Invalid versions.', 'pdf-embed-seo-optimize' ) ) );
        }

        $va = $this->get_version( $version_a );
        $vb = $this->get_version( $version_b );

        if ( ! $va || ! $vb ) {
            wp_send_json_error( array( 'message' => __( 'Versions not found.', 'pdf-embed-seo-optimize' ) ) );
        }

        wp_send_json_success( array(
            'version_a' => array(
                'number'   => $va->version_number,
                'file_url' => $va->file_url,
                'size'     => size_format( $va->file_size ),
                'date'     => $va->created_at,
            ),
            'version_b' => array(
                'number'   => $vb->version_number,
                'file_url' => $vb->file_url,
                'size'     => size_format( $vb->file_size ),
                'date'     => $vb->created_at,
            ),
        ) );
    }

    /**
     * Register REST routes.
     *
     * @since 1.3.0
     */
    public function register_rest_routes() {
        register_rest_route( 'pdf-embed-seo/v1', '/documents/(?P<id>\d+)/versions', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'rest_get_versions' ),
                'permission_callback' => array( $this, 'rest_can_view_versions' ),
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
     * REST: Get versions.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response
     */
    public function rest_get_versions( $request ) {
        $post_id  = $request->get_param( 'id' );
        $versions = $this->get_versions( $post_id );

        $data = array();
        foreach ( $versions as $version ) {
            $data[] = array(
                'id'             => $version->id,
                'version_number' => $version->version_number,
                'file_size'      => $version->file_size,
                'changelog'      => $version->changelog,
                'created_at'     => $version->created_at,
            );
        }

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * REST: Check if user can view versions.
     *
     * @since 1.3.0
     * @param WP_REST_Request $request Request object.
     * @return bool
     */
    public function rest_can_view_versions( $request ) {
        $post_id = $request->get_param( 'id' );
        return current_user_can( 'edit_post', $post_id );
    }

    /**
     * Cleanup old versions.
     *
     * @since 1.3.0
     */
    public function cleanup_old_versions() {
        global $wpdb;

        $settings     = pdf_embed_seo_pro_plus()->get_settings();
        $keep_count   = $settings['keep_versions'] ?? 10;

        // Get all posts with versions.
        $posts = $wpdb->get_col( "SELECT DISTINCT post_id FROM {$this->table_name}" );

        foreach ( $posts as $post_id ) {
            $versions = $this->get_versions( $post_id );

            if ( count( $versions ) <= $keep_count ) {
                continue;
            }

            // Delete oldest versions beyond the keep count.
            $to_delete = array_slice( $versions, $keep_count );

            foreach ( $to_delete as $version ) {
                $this->delete_version( $version->id );
            }
        }
    }
}

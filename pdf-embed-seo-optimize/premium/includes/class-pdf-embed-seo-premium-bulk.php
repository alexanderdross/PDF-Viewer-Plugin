<?php
/**
 * Premium Bulk Operations
 *
 * Adds bulk edit and bulk import functionality for PDFs.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium bulk operations class.
 */
class PDF_Embed_SEO_Premium_Bulk {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Bulk edit actions.
		add_filter( 'bulk_actions-edit-pdf_document', array( $this, 'register_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-pdf_document', array( $this, 'handle_bulk_actions' ), 10, 3 );
		add_action( 'admin_notices', array( $this, 'bulk_action_notices' ) );

		// Quick edit support.
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_fields' ), 10, 2 );
		add_action( 'save_post_pdf_document', array( $this, 'save_quick_edit' ), 10, 2 );
		add_action( 'admin_footer-edit.php', array( $this, 'quick_edit_javascript' ) );

		// Bulk import page.
		add_action( 'admin_menu', array( $this, 'add_import_page' ) );

		// Handle import.
		add_action( 'admin_init', array( $this, 'handle_bulk_import' ) );

		// AJAX for import progress.
		add_action( 'wp_ajax_pdf_bulk_import_progress', array( $this, 'ajax_import_progress' ) );
	}

	/**
	 * Register bulk actions.
	 *
	 * @param array $actions Existing bulk actions.
	 * @return array
	 */
	public function register_bulk_actions( $actions ) {
		$actions['pdf_enable_download']  = __( 'Enable Download', 'pdf-embed-seo-optimize' );
		$actions['pdf_disable_download'] = __( 'Disable Download', 'pdf-embed-seo-optimize' );
		$actions['pdf_enable_print']     = __( 'Enable Print', 'pdf-embed-seo-optimize' );
		$actions['pdf_disable_print']    = __( 'Disable Print', 'pdf-embed-seo-optimize' );

		return $actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * @param string $redirect_to Redirect URL.
	 * @param string $action      Action name.
	 * @param array  $post_ids    Selected post IDs.
	 * @return string
	 */
	public function handle_bulk_actions( $redirect_to, $action, $post_ids ) {
		$actions_map = array(
			'pdf_enable_download'  => array( '_pdf_allow_download', '1' ),
			'pdf_disable_download' => array( '_pdf_allow_download', '' ),
			'pdf_enable_print'     => array( '_pdf_allow_print', '1' ),
			'pdf_disable_print'    => array( '_pdf_allow_print', '' ),
		);

		if ( ! isset( $actions_map[ $action ] ) ) {
			return $redirect_to;
		}

		list( $meta_key, $meta_value ) = $actions_map[ $action ];

		$updated = 0;

		foreach ( $post_ids as $post_id ) {
			if ( current_user_can( 'edit_post', $post_id ) ) {
				update_post_meta( $post_id, $meta_key, $meta_value );
				$updated++;
			}
		}

		return add_query_arg(
			array(
				'pdf_bulk_updated' => $updated,
				'pdf_bulk_action'  => $action,
			),
			$redirect_to
		);
	}

	/**
	 * Display bulk action notices.
	 *
	 * @return void
	 */
	public function bulk_action_notices() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only, nonce verified during action.
		if ( ! isset( $_GET['pdf_bulk_updated'] ) || ! isset( $_GET['pdf_bulk_action'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only, nonce verified during action.
		$updated = absint( $_GET['pdf_bulk_updated'] );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only, nonce verified during action.
		$action  = sanitize_key( $_GET['pdf_bulk_action'] );

		$messages = array(
			/* translators: %d: Number of PDFs updated */
			'pdf_enable_download'  => __( 'Download enabled for %d PDF(s).', 'pdf-embed-seo-optimize' ),
			/* translators: %d: Number of PDFs updated */
			'pdf_disable_download' => __( 'Download disabled for %d PDF(s).', 'pdf-embed-seo-optimize' ),
			/* translators: %d: Number of PDFs updated */
			'pdf_enable_print'     => __( 'Print enabled for %d PDF(s).', 'pdf-embed-seo-optimize' ),
			/* translators: %d: Number of PDFs updated */
			'pdf_disable_print'    => __( 'Print disabled for %d PDF(s).', 'pdf-embed-seo-optimize' ),
		);

		if ( isset( $messages[ $action ] ) ) {
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html( sprintf( $messages[ $action ], $updated ) )
			);
		}
	}

	/**
	 * Add quick edit fields.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type   Post type.
	 * @return void
	 */
	public function quick_edit_fields( $column_name, $post_type ) {
		if ( 'pdf_document' !== $post_type || 'title' !== $column_name ) {
			return;
		}

		wp_nonce_field( 'pdf_quick_edit', 'pdf_quick_edit_nonce' );
		?>
		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<span class="title"><?php esc_html_e( 'PDF Settings', 'pdf-embed-seo-optimize' ); ?></span>
				<label class="alignleft">
					<input type="checkbox" name="pdf_allow_download" value="1" />
					<span class="checkbox-title"><?php esc_html_e( 'Allow Download', 'pdf-embed-seo-optimize' ); ?></span>
				</label>
				<label class="alignleft">
					<input type="checkbox" name="pdf_allow_print" value="1" />
					<span class="checkbox-title"><?php esc_html_e( 'Allow Print', 'pdf-embed-seo-optimize' ); ?></span>
				</label>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Save quick edit data.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public function save_quick_edit( $post_id, $post ) {
		// Verify nonce.
		if ( ! isset( $_POST['pdf_quick_edit_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pdf_quick_edit_nonce'] ), 'pdf_quick_edit' ) ) {
			return;
		}

		// Check if this is a quick edit.
		if ( ! isset( $_POST['_inline_edit'] ) ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save download permission.
		$allow_download = isset( $_POST['pdf_allow_download'] ) ? '1' : '';
		update_post_meta( $post_id, '_pdf_allow_download', $allow_download );

		// Save print permission.
		$allow_print = isset( $_POST['pdf_allow_print'] ) ? '1' : '';
		update_post_meta( $post_id, '_pdf_allow_print', $allow_print );
	}

	/**
	 * Output JavaScript for quick edit.
	 *
	 * @return void
	 */
	public function quick_edit_javascript() {
		$screen = get_current_screen();
		if ( 'edit-pdf_document' !== $screen->id ) {
			return;
		}
		?>
		<script>
		jQuery(function($) {
			var $inlineEdit = inlineEditPost.edit;
			inlineEditPost.edit = function(id) {
				$inlineEdit.apply(this, arguments);

				var postId = 0;
				if (typeof(id) === 'object') {
					postId = parseInt(this.getId(id));
				}

				if (postId > 0) {
					var $row = $('#post-' + postId);
					var allowDownload = $row.find('.column-pdf_download').text().trim() === '<?php esc_html_e( 'Yes', 'pdf-embed-seo-optimize' ); ?>';
					var allowPrint = $row.find('.column-pdf_print').text().trim() === '<?php esc_html_e( 'Yes', 'pdf-embed-seo-optimize' ); ?>';

					$('input[name="pdf_allow_download"]', '.inline-edit-row').prop('checked', allowDownload);
					$('input[name="pdf_allow_print"]', '.inline-edit-row').prop('checked', allowPrint);
				}
			};
		});
		</script>
		<?php
	}

	/**
	 * Add bulk import page.
	 *
	 * @return void
	 */
	public function add_import_page() {
		add_submenu_page(
			'edit.php?post_type=pdf_document',
			__( 'Bulk Import PDFs', 'pdf-embed-seo-optimize' ),
			__( 'Bulk Import', 'pdf-embed-seo-optimize' ),
			'edit_posts',
			'pdf-bulk-import',
			array( $this, 'render_import_page' )
		);
	}

	/**
	 * Render bulk import page.
	 *
	 * @return void
	 */
	public function render_import_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Bulk Import PDFs', 'pdf-embed-seo-optimize' ); ?></h1>

			<div class="pdf-import-instructions">
				<h2><?php esc_html_e( 'Instructions', 'pdf-embed-seo-optimize' ); ?></h2>
				<ol>
					<li><?php esc_html_e( 'Select multiple PDF files from your computer.', 'pdf-embed-seo-optimize' ); ?></li>
					<li><?php esc_html_e( 'Configure default settings for all imported PDFs.', 'pdf-embed-seo-optimize' ); ?></li>
					<li><?php esc_html_e( 'Click "Import" to create PDF documents for each file.', 'pdf-embed-seo-optimize' ); ?></li>
					<li><?php esc_html_e( 'Edit individual PDFs after import to customize settings.', 'pdf-embed-seo-optimize' ); ?></li>
				</ol>
			</div>

			<form method="post" enctype="multipart/form-data" class="pdf-import-form">
				<?php wp_nonce_field( 'pdf_bulk_import', 'pdf_bulk_import_nonce' ); ?>

				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="pdf_files"><?php esc_html_e( 'PDF Files', 'pdf-embed-seo-optimize' ); ?></label>
						</th>
						<td>
							<input type="file" name="pdf_files[]" id="pdf_files" multiple accept=".pdf,application/pdf" required />
							<p class="description"><?php esc_html_e( 'Select one or more PDF files to import.', 'pdf-embed-seo-optimize' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Default Status', 'pdf-embed-seo-optimize' ); ?></th>
						<td>
							<select name="import_status">
								<option value="draft"><?php esc_html_e( 'Draft', 'pdf-embed-seo-optimize' ); ?></option>
								<option value="publish"><?php esc_html_e( 'Published', 'pdf-embed-seo-optimize' ); ?></option>
								<option value="pending"><?php esc_html_e( 'Pending Review', 'pdf-embed-seo-optimize' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Default Permissions', 'pdf-embed-seo-optimize' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="import_allow_download" value="1" />
								<?php esc_html_e( 'Allow Download', 'pdf-embed-seo-optimize' ); ?>
							</label>
							<br />
							<label>
								<input type="checkbox" name="import_allow_print" value="1" />
								<?php esc_html_e( 'Allow Print', 'pdf-embed-seo-optimize' ); ?>
							</label>
						</td>
					</tr>
					<?php if ( taxonomy_exists( 'pdf_category' ) ) : ?>
					<tr>
						<th scope="row">
							<label for="import_category"><?php esc_html_e( 'Category', 'pdf-embed-seo-optimize' ); ?></label>
						</th>
						<td>
							<?php
							wp_dropdown_categories(
								array(
									'taxonomy'         => 'pdf_category',
									'name'             => 'import_category',
									'id'               => 'import_category',
									'show_option_none' => __( '-- Select Category --', 'pdf-embed-seo-optimize' ),
									'hide_empty'       => false,
								)
							);
							?>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<th scope="row"><?php esc_html_e( 'Auto-generate Thumbnails', 'pdf-embed-seo-optimize' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="import_generate_thumbnails" value="1" checked />
								<?php esc_html_e( 'Generate thumbnails from first page', 'pdf-embed-seo-optimize' ); ?>
							</label>
						</td>
					</tr>
				</table>

				<?php submit_button( __( 'Import PDFs', 'pdf-embed-seo-optimize' ), 'primary', 'pdf_bulk_import_submit' ); ?>
			</form>

			<div class="pdf-import-progress" style="display: none;">
				<h3><?php esc_html_e( 'Import Progress', 'pdf-embed-seo-optimize' ); ?></h3>
				<div class="pdf-progress-bar">
					<div class="pdf-progress-fill"></div>
				</div>
				<p class="pdf-progress-status"></p>
			</div>

			<p class="pdf-embed-seo-optimize-credit" style="text-align: center; margin-top: 30px; color: #666; font-size: 13px;">
				<?php
				printf(
					/* translators: %1$s: heart symbol, %2$s: Dross:Media link */
					esc_html__( 'made with %1$s by %2$s', 'pdf-embed-seo-optimize' ),
					'<span style="color: #e25555;" aria-hidden="true">♥</span><span class="screen-reader-text">' . esc_html__( 'love', 'pdf-embed-seo-optimize' ) . '</span>',
					'<a href="https://dross.net/media/" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr__( 'Visit Dross:Media website (opens in new tab)', 'pdf-embed-seo-optimize' ) . '" title="' . esc_attr__( 'Visit Dross:Media website', 'pdf-embed-seo-optimize' ) . '">Dross:Media</a>'
				);
				?>
			</p>
		</div>

		<style>
			.pdf-import-instructions {
				background: #fff;
				border: 1px solid #c3c4c7;
				padding: 15px 20px;
				margin: 20px 0;
			}
			.pdf-import-instructions h2 {
				margin-top: 0;
			}
			.pdf-import-form {
				background: #fff;
				border: 1px solid #c3c4c7;
				padding: 20px;
			}
			.pdf-progress-bar {
				height: 20px;
				background: #ddd;
				border-radius: 4px;
				overflow: hidden;
			}
			.pdf-progress-fill {
				height: 100%;
				background: #2271b1;
				width: 0%;
				transition: width 0.3s ease;
			}
		</style>
		<?php
	}

	/**
	 * Handle bulk import.
	 *
	 * @return void
	 */
	public function handle_bulk_import() {
		if ( ! isset( $_POST['pdf_bulk_import_submit'] ) ) {
			return;
		}

		if ( ! isset( $_POST['pdf_bulk_import_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pdf_bulk_import_nonce'] ), 'pdf_bulk_import' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( empty( $_FILES['pdf_files']['name'][0] ) ) {
			add_settings_error( 'pdf_import', 'no_files', __( 'Please select at least one PDF file.', 'pdf-embed-seo-optimize' ), 'error' );
			return;
		}

		$status               = isset( $_POST['import_status'] ) ? sanitize_key( $_POST['import_status'] ) : 'draft';
		$allow_download       = isset( $_POST['import_allow_download'] ) ? '1' : '';
		$allow_print          = isset( $_POST['import_allow_print'] ) ? '1' : '';
		$category             = isset( $_POST['import_category'] ) ? absint( $_POST['import_category'] ) : 0;
		$generate_thumbnails  = isset( $_POST['import_generate_thumbnails'] );

		$imported = 0;
		$errors   = array();

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- File data validated below and passed to media_handle_sideload.
		$files = $_FILES['pdf_files'];

		for ( $i = 0; $i < count( $files['name'] ); $i++ ) {
			if ( UPLOAD_ERR_OK !== $files['error'][ $i ] ) {
				$errors[] = sprintf(
					/* translators: %s: File name. */
					__( 'Error uploading %s.', 'pdf-embed-seo-optimize' ),
					$files['name'][ $i ]
				);
				continue;
			}

			// Check file type.
			$file_type = wp_check_filetype( $files['name'][ $i ] );
			if ( 'pdf' !== strtolower( $file_type['ext'] ) ) {
				$errors[] = sprintf(
					/* translators: %s: File name. */
					__( '%s is not a valid PDF file.', 'pdf-embed-seo-optimize' ),
					$files['name'][ $i ]
				);
				continue;
			}

			// Create file array for upload.
			$file = array(
				'name'     => $files['name'][ $i ],
				'type'     => $files['type'][ $i ],
				'tmp_name' => $files['tmp_name'][ $i ],
				'error'    => $files['error'][ $i ],
				'size'     => $files['size'][ $i ],
			);

			// Upload to media library.
			$attachment_id = media_handle_sideload( $file, 0 );

			if ( is_wp_error( $attachment_id ) ) {
				$errors[] = sprintf(
					/* translators: %s: File name. */
					__( 'Failed to upload %s.', 'pdf-embed-seo-optimize' ),
					$files['name'][ $i ]
				);
				continue;
			}

			// Create PDF document.
			$title = pathinfo( $files['name'][ $i ], PATHINFO_FILENAME );
			$title = str_replace( array( '-', '_' ), ' ', $title );
			$title = ucwords( $title );

			$post_id = wp_insert_post(
				array(
					'post_type'   => 'pdf_document',
					'post_title'  => $title,
					'post_status' => $status,
					'post_author' => get_current_user_id(),
				)
			);

			if ( is_wp_error( $post_id ) ) {
				$errors[] = sprintf(
					/* translators: %s: File name. */
					__( 'Failed to create post for %s.', 'pdf-embed-seo-optimize' ),
					$files['name'][ $i ]
				);
				continue;
			}

			// Save meta.
			update_post_meta( $post_id, '_pdf_file_id', $attachment_id );
			update_post_meta( $post_id, '_pdf_file_url', wp_get_attachment_url( $attachment_id ) );
			update_post_meta( $post_id, '_pdf_allow_download', $allow_download );
			update_post_meta( $post_id, '_pdf_allow_print', $allow_print );

			// Assign category.
			if ( $category > 0 && taxonomy_exists( 'pdf_category' ) ) {
				wp_set_object_terms( $post_id, $category, 'pdf_category' );
			}

			// Generate thumbnail.
			if ( $generate_thumbnails ) {
				/**
				 * Fires after PDF settings are saved, triggering thumbnail generation.
				 *
				 * @since 1.2.0
				 *
				 * @param int   $post_id  Post ID.
				 * @param array $settings PDF settings.
				 */
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Hook uses plugin slug prefix 'pdf_embed_seo_optimize_'.
				do_action(
					'pdf_embed_seo_optimize_settings_saved',
					$post_id,
					array(
						'pdf_file_id'      => $attachment_id,
						'allow_download'   => $allow_download,
						'allow_print'      => $allow_print,
					)
				);
			}

			$imported++;
		}

		// Display results.
		if ( $imported > 0 ) {
			add_settings_error(
				'pdf_import',
				'import_success',
				sprintf(
					/* translators: %d: Number of imported PDFs. */
					__( 'Successfully imported %d PDF(s).', 'pdf-embed-seo-optimize' ),
					$imported
				),
				'success'
			);
		}

		foreach ( $errors as $error ) {
			add_settings_error( 'pdf_import', 'import_error', $error, 'error' );
		}

		// Redirect to show notices.
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect( admin_url( 'edit.php?post_type=pdf_document&page=pdf-bulk-import&settings-updated=true' ) );
		exit;
	}

	/**
	 * AJAX handler for import progress.
	 *
	 * @return void
	 */
	public function ajax_import_progress() {
		check_ajax_referer( 'pdf_import_progress', 'nonce' );

		// This would be used for async imports in the future.
		wp_send_json_success( array( 'progress' => 100 ) );
	}
}

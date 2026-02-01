<?php
/**
 * PDF Thumbnail Generator for PDF Embed & SEO Optimize.
 *
 * Generates thumbnail images from PDF first pages using ImageMagick or Ghostscript.
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PDF_Embed_SEO_Thumbnail
 *
 * Handles automatic PDF thumbnail generation.
 */
class PDF_Embed_SEO_Thumbnail {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into PDF save to generate thumbnail.
		add_action( 'pdf_embed_seo_optimize_settings_saved', array( $this, 'maybe_generate_thumbnail' ), 10, 2 );

		// Add AJAX handler for manual regeneration.
		add_action( 'wp_ajax_pdf_embed_seo_generate_thumbnail', array( $this, 'ajax_generate_thumbnail' ) );

		// Add button to admin meta box.
		add_action( 'admin_footer-post.php', array( $this, 'add_generate_thumbnail_script' ) );
		add_action( 'admin_footer-post-new.php', array( $this, 'add_generate_thumbnail_script' ) );
	}

	/**
	 * Check if thumbnail generation is possible.
	 *
	 * @return array Status array with 'available' boolean and 'method' string.
	 */
	public static function check_availability() {
		$result = array(
			'available' => false,
			'method'    => 'none',
			'message'   => '',
		);

		// Check for ImageMagick with PDF support.
		if ( extension_loaded( 'imagick' ) ) {
			try {
				$imagick = new Imagick();
				$formats = $imagick->queryFormats( 'PDF' );
				if ( ! empty( $formats ) ) {
					$result['available'] = true;
					$result['method']    = 'imagick';
					$result['message']   = __( 'ImageMagick with PDF support available.', 'pdf-embed-seo-optimize' );
					return $result;
				}
			} catch ( Exception $e ) {
				// ImageMagick doesn't support PDF.
			}
		}

		// Check for Ghostscript.
		$gs_paths = array( 'gs', '/usr/bin/gs', '/usr/local/bin/gs' );
		foreach ( $gs_paths as $gs_path ) {
			$output = array();
			$return = 0;
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec
			@exec( escapeshellcmd( $gs_path ) . ' --version 2>&1', $output, $return );
			if ( 0 === $return && ! empty( $output ) ) {
				$result['available'] = true;
				$result['method']    = 'ghostscript';
				$result['gs_path']   = $gs_path;
				$result['message']   = sprintf(
					/* translators: %s: Ghostscript version */
					__( 'Ghostscript %s available.', 'pdf-embed-seo-optimize' ),
					$output[0]
				);
				return $result;
			}
		}

		$result['message'] = __( 'Neither ImageMagick with PDF support nor Ghostscript is available. Thumbnails must be uploaded manually.', 'pdf-embed-seo-optimize' );
		return $result;
	}

	/**
	 * Maybe generate thumbnail when PDF is saved.
	 *
	 * @param int   $post_id  The post ID.
	 * @param array $settings The saved settings.
	 * @return void
	 */
	public function maybe_generate_thumbnail( $post_id, $settings ) {
		// Check if auto-generation is enabled.
		$auto_generate = PDF_Embed_SEO::get_setting( 'auto_generate_thumbnails', true );
		if ( ! $auto_generate ) {
			return;
		}

		// Don't overwrite existing featured image.
		if ( has_post_thumbnail( $post_id ) ) {
			return;
		}

		// Get PDF file.
		$pdf_file_id = get_post_meta( $post_id, '_pdf_file_id', true );
		if ( ! $pdf_file_id ) {
			return;
		}

		// Generate thumbnail.
		$this->generate_thumbnail( $post_id, $pdf_file_id );
	}

	/**
	 * Generate thumbnail from PDF.
	 *
	 * @param int $post_id     The PDF document post ID.
	 * @param int $pdf_file_id The PDF attachment ID.
	 * @return int|WP_Error Attachment ID on success, WP_Error on failure.
	 */
	public function generate_thumbnail( $post_id, $pdf_file_id ) {
		$availability = self::check_availability();
		if ( ! $availability['available'] ) {
			return new WP_Error( 'no_converter', $availability['message'] );
		}

		$pdf_path = get_attached_file( $pdf_file_id );
		if ( ! $pdf_path || ! file_exists( $pdf_path ) ) {
			return new WP_Error( 'file_not_found', __( 'PDF file not found.', 'pdf-embed-seo-optimize' ) );
		}

		// Create temp directory for thumbnail.
		$upload_dir = wp_upload_dir();
		$temp_dir   = $upload_dir['basedir'] . '/pdf-thumbnails';
		if ( ! file_exists( $temp_dir ) ) {
			wp_mkdir_p( $temp_dir );

			// Add index.php for security.
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents( $temp_dir . '/index.php', '<?php // Silence is golden.' );
		}

		// Generate unique filename.
		$filename  = 'pdf-thumb-' . $post_id . '-' . time() . '.jpg';
		$temp_path = $temp_dir . '/' . $filename;

		// Generate thumbnail using available method.
		if ( 'imagick' === $availability['method'] ) {
			$result = $this->generate_with_imagick( $pdf_path, $temp_path );
		} else {
			$result = $this->generate_with_ghostscript( $pdf_path, $temp_path, $availability['gs_path'] );
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Verify thumbnail was created.
		if ( ! file_exists( $temp_path ) ) {
			return new WP_Error( 'generation_failed', __( 'Failed to generate thumbnail.', 'pdf-embed-seo-optimize' ) );
		}

		// Import thumbnail into WordPress media library.
		$attachment_id = $this->import_thumbnail( $temp_path, $post_id );

		// Clean up temp file.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
		@unlink( $temp_path );

		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		// Set as featured image.
		set_post_thumbnail( $post_id, $attachment_id );

		return $attachment_id;
	}

	/**
	 * Generate thumbnail using ImageMagick.
	 *
	 * @param string $pdf_path   Path to PDF file.
	 * @param string $thumb_path Path for thumbnail output.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	private function generate_with_imagick( $pdf_path, $thumb_path ) {
		try {
			$imagick = new Imagick();

			// Set resolution before reading for better quality.
			$imagick->setResolution( 150, 150 );

			// Read only the first page.
			$imagick->readImage( $pdf_path . '[0]' );

			// Flatten to remove transparency (use white background).
			$imagick->setImageBackgroundColor( 'white' );
			$imagick->setImageAlphaChannel( Imagick::ALPHACHANNEL_REMOVE );
			$imagick = $imagick->mergeImageLayers( Imagick::LAYERMETHOD_FLATTEN );

			// Convert to JPEG.
			$imagick->setImageFormat( 'jpeg' );
			$imagick->setImageCompressionQuality( 90 );

			// Resize to reasonable thumbnail size (max 800px).
			$imagick->thumbnailImage( 800, 0 );

			// Write file.
			$imagick->writeImage( $thumb_path );
			$imagick->clear();
			$imagick->destroy();

			return true;
		} catch ( Exception $e ) {
			return new WP_Error( 'imagick_error', $e->getMessage() );
		}
	}

	/**
	 * Generate thumbnail using Ghostscript.
	 *
	 * @param string $pdf_path   Path to PDF file.
	 * @param string $thumb_path Path for thumbnail output.
	 * @param string $gs_path    Path to Ghostscript executable.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	private function generate_with_ghostscript( $pdf_path, $thumb_path, $gs_path ) {
		// Build Ghostscript command.
		$cmd = sprintf(
			'%s -dNOPAUSE -dBATCH -dSAFER -dFirstPage=1 -dLastPage=1 -sDEVICE=jpeg -dJPEGQ=90 -r150 -dTextAlphaBits=4 -dGraphicsAlphaBits=4 -sOutputFile=%s %s 2>&1',
			escapeshellcmd( $gs_path ),
			escapeshellarg( $thumb_path ),
			escapeshellarg( $pdf_path )
		);

		$output = array();
		$return = 0;

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec
		exec( $cmd, $output, $return );

		if ( 0 !== $return ) {
			return new WP_Error(
				'ghostscript_error',
				sprintf(
					/* translators: %s: Error message */
					__( 'Ghostscript error: %s', 'pdf-embed-seo-optimize' ),
					implode( "\n", $output )
				)
			);
		}

		return true;
	}

	/**
	 * Import thumbnail into WordPress media library.
	 *
	 * @param string $file_path Path to the thumbnail file.
	 * @param int    $post_id   The parent post ID.
	 * @return int|WP_Error Attachment ID on success, WP_Error on failure.
	 */
	private function import_thumbnail( $file_path, $post_id ) {
		// Include necessary WordPress files.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Get post title for attachment.
		$post_title = get_the_title( $post_id );

		// Prepare file for upload.
		$upload_dir = wp_upload_dir();
		$filename   = 'pdf-thumbnail-' . $post_id . '-' . time() . '.jpg';
		$dest_path  = $upload_dir['path'] . '/' . $filename;

		// Copy file to uploads directory.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_copy
		if ( ! copy( $file_path, $dest_path ) ) {
			return new WP_Error( 'copy_failed', __( 'Failed to copy thumbnail to uploads directory.', 'pdf-embed-seo-optimize' ) );
		}

		// Get file type.
		$filetype = wp_check_filetype( $filename );

		// Prepare attachment data.
		$attachment = array(
			'guid'           => $upload_dir['url'] . '/' . $filename,
			'post_mime_type' => $filetype['type'],
			'post_title'     => sprintf(
				/* translators: %s: PDF document title */
				__( '%s - Thumbnail', 'pdf-embed-seo-optimize' ),
				$post_title
			),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		// Insert attachment.
		$attachment_id = wp_insert_attachment( $attachment, $dest_path, $post_id );

		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		// Generate attachment metadata.
		$metadata = wp_generate_attachment_metadata( $attachment_id, $dest_path );
		wp_update_attachment_metadata( $attachment_id, $metadata );

		return $attachment_id;
	}

	/**
	 * AJAX handler for manual thumbnail generation.
	 *
	 * @return void
	 */
	public function ajax_generate_thumbnail() {
		// Verify nonce.
		check_ajax_referer( 'pdf_embed_seo_generate_thumbnail', 'nonce' );

		// Check permissions.
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'pdf-embed-seo-optimize' ) ) );
		}

		// Get PDF file ID.
		$pdf_file_id = get_post_meta( $post_id, '_pdf_file_id', true );
		if ( ! $pdf_file_id ) {
			wp_send_json_error( array( 'message' => __( 'No PDF file attached.', 'pdf-embed-seo-optimize' ) ) );
		}

		// Generate thumbnail.
		$result = $this->generate_thumbnail( $post_id, $pdf_file_id );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		// Get thumbnail URL for response.
		$thumbnail_url = wp_get_attachment_image_url( $result, 'thumbnail' );

		wp_send_json_success(
			array(
				'message'       => __( 'Thumbnail generated successfully!', 'pdf-embed-seo-optimize' ),
				'attachment_id' => $result,
				'thumbnail_url' => $thumbnail_url,
			)
		);
	}

	/**
	 * Add JavaScript for thumbnail generation button.
	 *
	 * @return void
	 */
	public function add_generate_thumbnail_script() {
		global $post_type;

		if ( 'pdf_document' !== $post_type ) {
			return;
		}

		$availability = self::check_availability();
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Check if we're on the PDF document edit screen.
			if ($('#postimagediv').length === 0) {
				return;
			}

			var availability = <?php echo wp_json_encode( $availability ); ?>;
			var postId = $('#post_ID').val();
			var nonce = '<?php echo esc_js( wp_create_nonce( 'pdf_embed_seo_generate_thumbnail' ) ); ?>';

			// Add generate button after the featured image meta box.
			var buttonHtml = '<p class="pdf-thumbnail-generator" style="margin-top: 10px;">';

			if (availability.available) {
				buttonHtml += '<button type="button" class="button pdf-generate-thumbnail-btn">';
				buttonHtml += '<?php echo esc_js( __( 'Generate from PDF', 'pdf-embed-seo-optimize' ) ); ?>';
				buttonHtml += '</button>';
				buttonHtml += '<span class="spinner" style="float: none; margin-top: 0;"></span>';
				buttonHtml += '<span class="pdf-thumbnail-status" style="display: block; margin-top: 5px; font-style: italic; color: #666;"></span>';
			} else {
				buttonHtml += '<em style="color: #999; font-size: 12px;">';
				buttonHtml += '<?php echo esc_js( __( 'Auto-generation unavailable. Upload thumbnail manually.', 'pdf-embed-seo-optimize' ) ); ?>';
				buttonHtml += '</em>';
			}

			buttonHtml += '</p>';

			$('#postimagediv .inside').append(buttonHtml);

			// Handle button click.
			$(document).on('click', '.pdf-generate-thumbnail-btn', function(e) {
				e.preventDefault();

				var $button = $(this);
				var $spinner = $button.siblings('.spinner');
				var $status = $button.siblings('.pdf-thumbnail-status');

				// Check if PDF is selected.
				var pdfFileId = $('#pdf_file_id').val();
				if (!pdfFileId || pdfFileId === '0') {
					$status.text('<?php echo esc_js( __( 'Please select a PDF file first.', 'pdf-embed-seo-optimize' ) ); ?>').css('color', '#d63638');
					return;
				}

				$button.prop('disabled', true);
				$spinner.addClass('is-active');
				$status.text('<?php echo esc_js( __( 'Generating thumbnail...', 'pdf-embed-seo-optimize' ) ); ?>').css('color', '#666');

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'pdf_embed_seo_generate_thumbnail',
						nonce: nonce,
						post_id: postId
					},
					success: function(response) {
						$button.prop('disabled', false);
						$spinner.removeClass('is-active');

						if (response.success) {
							$status.text(response.data.message).css('color', '#00a32a');
							// Refresh the featured image meta box.
							wp.media.featuredImage.frame().close();
							wp.media.featuredImage.set(response.data.attachment_id);
						} else {
							$status.text(response.data.message).css('color', '#d63638');
						}
					},
					error: function() {
						$button.prop('disabled', false);
						$spinner.removeClass('is-active');
						$status.text('<?php echo esc_js( __( 'An error occurred. Please try again.', 'pdf-embed-seo-optimize' ) ); ?>').css('color', '#d63638');
					}
				});
			});
		});
		</script>
		<?php
	}
}

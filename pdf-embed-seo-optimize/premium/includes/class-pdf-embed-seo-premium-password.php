<?php
/**
 * Premium Password Protection
 *
 * Adds password protection for individual PDFs.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium password protection class.
 */
class PDF_Embed_SEO_Premium_Password {

	/**
	 * Meta key for password.
	 *
	 * @var string
	 */
	const META_KEY_PASSWORD = '_pdf_password';

	/**
	 * Meta key for password enabled.
	 *
	 * @var string
	 */
	const META_KEY_ENABLED = '_pdf_password_enabled';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Frontend password check.
		add_action( 'template_redirect', array( $this, 'check_password_protection' ) );

		// AJAX handlers.
		add_action( 'wp_ajax_pdf_verify_password', array( $this, 'ajax_verify_password' ) );
		add_action( 'wp_ajax_nopriv_pdf_verify_password', array( $this, 'ajax_verify_password' ) );

		// Admin hooks.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_pdf_document', array( $this, 'save_meta' ), 10, 2 );

		// Filter PDF URL delivery.
		add_filter( 'pdf_embed_seo_can_access_pdf', array( $this, 'check_access' ), 10, 2 );
	}

	/**
	 * Add meta box for password settings.
	 *
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box(
			'pdf_password_settings',
			__( 'Password Protection', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_meta_box' ),
			'pdf_document',
			'side',
			'default'
		);
	}

	/**
	 * Render meta box content.
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	public function render_meta_box( $post ) {
		$enabled  = get_post_meta( $post->ID, self::META_KEY_ENABLED, true );
		$password = get_post_meta( $post->ID, self::META_KEY_PASSWORD, true );

		wp_nonce_field( 'pdf_password_nonce', 'pdf_password_nonce_field' );
		?>
		<p>
			<label>
				<input type="checkbox" name="pdf_password_enabled" value="1" <?php checked( $enabled, '1' ); ?> />
				<?php esc_html_e( 'Enable password protection', 'pdf-embed-seo-optimize' ); ?>
			</label>
		</p>
		<p class="pdf-password-field" style="<?php echo $enabled ? '' : 'display:none;'; ?>">
			<label for="pdf_password"><?php esc_html_e( 'Password:', 'pdf-embed-seo-optimize' ); ?></label><br />
			<input type="password" id="pdf_password" name="pdf_password" value="" class="widefat" placeholder="<?php echo $password ? esc_attr__( 'Leave empty to keep current', 'pdf-embed-seo-optimize' ) : ''; ?>" />
			<?php if ( $password ) : ?>
				<span class="description"><?php esc_html_e( 'Password is set.', 'pdf-embed-seo-optimize' ); ?></span>
			<?php endif; ?>
		</p>
		<script>
			jQuery(function($) {
				$('input[name="pdf_password_enabled"]').on('change', function() {
					$('.pdf-password-field').toggle(this.checked);
				});
			});
		</script>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return void
	 */
	public function save_meta( $post_id, $post ) {
		// Verify nonce.
		if ( ! isset( $_POST['pdf_password_nonce_field'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pdf_password_nonce_field'] ), 'pdf_password_nonce' ) ) {
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

		// Save enabled status.
		$enabled = isset( $_POST['pdf_password_enabled'] ) ? '1' : '';
		update_post_meta( $post_id, self::META_KEY_ENABLED, $enabled );

		// Save password (only if provided).
		if ( isset( $_POST['pdf_password'] ) && ! empty( $_POST['pdf_password'] ) ) {
			$password = sanitize_text_field( wp_unslash( $_POST['pdf_password'] ) );
			$hashed   = wp_hash_password( $password );
			update_post_meta( $post_id, self::META_KEY_PASSWORD, $hashed );
		}
	}

	/**
	 * Check if PDF is password protected.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public static function is_protected( $post_id ) {
		$enabled  = get_post_meta( $post_id, self::META_KEY_ENABLED, true );
		$password = get_post_meta( $post_id, self::META_KEY_PASSWORD, true );
		return '1' === $enabled && ! empty( $password );
	}

	/**
	 * Check if user has access to protected PDF.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public static function has_access( $post_id ) {
		// If not protected, always has access.
		if ( ! self::is_protected( $post_id ) ) {
			return true;
		}

		// Check session for unlocked PDFs.
		if ( ! session_id() ) {
			session_start();
		}

		$unlocked = isset( $_SESSION['pdf_unlocked'] ) ? $_SESSION['pdf_unlocked'] : array();
		return in_array( $post_id, $unlocked, true );
	}

	/**
	 * Verify password for a PDF.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $password Password to verify.
	 * @return bool
	 */
	public static function verify_password( $post_id, $password ) {
		$stored_hash = get_post_meta( $post_id, self::META_KEY_PASSWORD, true );

		if ( empty( $stored_hash ) ) {
			return false;
		}

		return wp_check_password( $password, $stored_hash );
	}

	/**
	 * Grant access to a protected PDF.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public static function grant_access( $post_id ) {
		if ( ! session_id() ) {
			session_start();
		}

		if ( ! isset( $_SESSION['pdf_unlocked'] ) ) {
			$_SESSION['pdf_unlocked'] = array();
		}

		if ( ! in_array( $post_id, $_SESSION['pdf_unlocked'], true ) ) {
			$_SESSION['pdf_unlocked'][] = $post_id;
		}
	}

	/**
	 * Check password protection on template redirect.
	 *
	 * @return void
	 */
	public function check_password_protection() {
		if ( ! is_singular( 'pdf_document' ) ) {
			return;
		}

		$post_id = get_the_ID();

		if ( ! self::is_protected( $post_id ) ) {
			return;
		}

		// Check if already unlocked.
		if ( self::has_access( $post_id ) ) {
			return;
		}

		// Handle password form submission.
		if ( isset( $_POST['pdf_password_submit'] ) && isset( $_POST['pdf_access_password'] ) ) {
			if ( ! isset( $_POST['pdf_password_check_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pdf_password_check_nonce'] ), 'pdf_password_check' ) ) {
				return;
			}

			$password = sanitize_text_field( wp_unslash( $_POST['pdf_access_password'] ) );

			if ( self::verify_password( $post_id, $password ) ) {
				self::grant_access( $post_id );
				wp_safe_redirect( get_permalink( $post_id ) );
				exit;
			}

			// Password incorrect - will show error on form.
			add_filter( 'pdf_embed_seo_password_error', '__return_true' );
		}
	}

	/**
	 * Filter PDF access check.
	 *
	 * @param bool $can_access Whether user can access.
	 * @param int  $post_id    Post ID.
	 * @return bool
	 */
	public function check_access( $can_access, $post_id ) {
		if ( ! $can_access ) {
			return false;
		}

		return self::has_access( $post_id );
	}

	/**
	 * AJAX handler for password verification.
	 *
	 * @return void
	 */
	public function ajax_verify_password() {
		check_ajax_referer( 'pdf_password_verify', 'nonce' );

		$post_id  = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$password = isset( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';

		if ( ! $post_id || ! $password ) {
			wp_send_json_error( array( 'message' => __( 'Invalid request.', 'pdf-embed-seo-optimize' ) ) );
		}

		if ( self::verify_password( $post_id, $password ) ) {
			self::grant_access( $post_id );
			wp_send_json_success( array( 'message' => __( 'Access granted.', 'pdf-embed-seo-optimize' ) ) );
		}

		wp_send_json_error( array( 'message' => __( 'Incorrect password.', 'pdf-embed-seo-optimize' ) ) );
	}

	/**
	 * Get password form HTML.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public static function get_password_form( $post_id ) {
		$error = apply_filters( 'pdf_embed_seo_password_error', false );

		ob_start();
		?>
		<div class="pdf-password-form-container">
			<div class="pdf-password-form">
				<h3><?php esc_html_e( 'This PDF is password protected', 'pdf-embed-seo-optimize' ); ?></h3>
				<p><?php esc_html_e( 'Please enter the password to view this document.', 'pdf-embed-seo-optimize' ); ?></p>

				<?php if ( $error ) : ?>
					<div class="pdf-password-error">
						<?php esc_html_e( 'Incorrect password. Please try again.', 'pdf-embed-seo-optimize' ); ?>
					</div>
				<?php endif; ?>

				<form method="post" action="">
					<?php wp_nonce_field( 'pdf_password_check', 'pdf_password_check_nonce' ); ?>
					<p>
						<label for="pdf_access_password"><?php esc_html_e( 'Password:', 'pdf-embed-seo-optimize' ); ?></label>
						<input type="password" name="pdf_access_password" id="pdf_access_password" required />
					</p>
					<p>
						<button type="submit" name="pdf_password_submit" class="button"><?php esc_html_e( 'Submit', 'pdf-embed-seo-optimize' ); ?></button>
					</p>
				</form>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

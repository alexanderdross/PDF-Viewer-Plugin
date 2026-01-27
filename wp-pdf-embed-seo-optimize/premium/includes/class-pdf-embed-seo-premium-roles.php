<?php
/**
 * Premium Role Restrictions
 *
 * Adds user role-based access restrictions for PDFs.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium role restrictions class.
 */
class PDF_Embed_SEO_Premium_Roles {

	/**
	 * Meta key for allowed roles.
	 *
	 * @var string
	 */
	const META_KEY_ROLES = '_pdf_allowed_roles';

	/**
	 * Meta key for role restriction enabled.
	 *
	 * @var string
	 */
	const META_KEY_ENABLED = '_pdf_role_restriction_enabled';

	/**
	 * Meta key for require login.
	 *
	 * @var string
	 */
	const META_KEY_REQUIRE_LOGIN = '_pdf_require_login';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Admin hooks.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_pdf_document', array( $this, 'save_meta' ), 10, 2 );

		// Frontend access check.
		add_filter( 'pdf_embed_seo_can_access_pdf', array( $this, 'check_role_access' ), 15, 2 );
		add_action( 'template_redirect', array( $this, 'check_login_required' ) );
	}

	/**
	 * Add meta box for role settings.
	 *
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box(
			'pdf_role_settings',
			__( 'Access Restrictions', 'wp-pdf-embed-seo-optimize' ),
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
		$enabled       = get_post_meta( $post->ID, self::META_KEY_ENABLED, true );
		$require_login = get_post_meta( $post->ID, self::META_KEY_REQUIRE_LOGIN, true );
		$allowed_roles = get_post_meta( $post->ID, self::META_KEY_ROLES, true );

		if ( ! is_array( $allowed_roles ) ) {
			$allowed_roles = array();
		}

		$all_roles = wp_roles()->get_names();

		wp_nonce_field( 'pdf_roles_nonce', 'pdf_roles_nonce_field' );
		?>
		<p>
			<label>
				<input type="checkbox" name="pdf_require_login" value="1" <?php checked( $require_login, '1' ); ?> />
				<?php esc_html_e( 'Require login to view', 'wp-pdf-embed-seo-optimize' ); ?>
			</label>
		</p>

		<p>
			<label>
				<input type="checkbox" name="pdf_role_restriction_enabled" value="1" <?php checked( $enabled, '1' ); ?> class="pdf-role-restriction-toggle" />
				<?php esc_html_e( 'Restrict to specific roles', 'wp-pdf-embed-seo-optimize' ); ?>
			</label>
		</p>

		<div class="pdf-role-selection" style="<?php echo $enabled ? '' : 'display:none;'; ?>">
			<p><strong><?php esc_html_e( 'Allowed Roles:', 'wp-pdf-embed-seo-optimize' ); ?></strong></p>
			<?php foreach ( $all_roles as $role_key => $role_name ) : ?>
				<p>
					<label>
						<input type="checkbox" name="pdf_allowed_roles[]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $allowed_roles, true ) ); ?> />
						<?php echo esc_html( translate_user_role( $role_name ) ); ?>
					</label>
				</p>
			<?php endforeach; ?>
		</div>

		<script>
			jQuery(function($) {
				$('.pdf-role-restriction-toggle').on('change', function() {
					$('.pdf-role-selection').toggle(this.checked);
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
		if ( ! isset( $_POST['pdf_roles_nonce_field'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pdf_roles_nonce_field'] ), 'pdf_roles_nonce' ) ) {
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

		// Save require login.
		$require_login = isset( $_POST['pdf_require_login'] ) ? '1' : '';
		update_post_meta( $post_id, self::META_KEY_REQUIRE_LOGIN, $require_login );

		// Save restriction enabled.
		$enabled = isset( $_POST['pdf_role_restriction_enabled'] ) ? '1' : '';
		update_post_meta( $post_id, self::META_KEY_ENABLED, $enabled );

		// Save allowed roles.
		$allowed_roles = array();
		if ( isset( $_POST['pdf_allowed_roles'] ) && is_array( $_POST['pdf_allowed_roles'] ) ) {
			$allowed_roles = array_map( 'sanitize_text_field', wp_unslash( $_POST['pdf_allowed_roles'] ) );
		}
		update_post_meta( $post_id, self::META_KEY_ROLES, $allowed_roles );
	}

	/**
	 * Check if PDF requires login.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public static function requires_login( $post_id ) {
		return '1' === get_post_meta( $post_id, self::META_KEY_REQUIRE_LOGIN, true );
	}

	/**
	 * Check if PDF has role restrictions.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public static function has_role_restriction( $post_id ) {
		return '1' === get_post_meta( $post_id, self::META_KEY_ENABLED, true );
	}

	/**
	 * Get allowed roles for a PDF.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	public static function get_allowed_roles( $post_id ) {
		$roles = get_post_meta( $post_id, self::META_KEY_ROLES, true );
		return is_array( $roles ) ? $roles : array();
	}

	/**
	 * Check if user has role access to PDF.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public static function user_has_role_access( $post_id ) {
		// If no role restriction, allow access.
		if ( ! self::has_role_restriction( $post_id ) ) {
			return true;
		}

		// Must be logged in.
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$allowed_roles = self::get_allowed_roles( $post_id );

		// If no roles specified, allow all logged-in users.
		if ( empty( $allowed_roles ) ) {
			return true;
		}

		$user = wp_get_current_user();

		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $allowed_roles, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Filter PDF access based on role.
	 *
	 * @param bool $can_access Whether user can access.
	 * @param int  $post_id    Post ID.
	 * @return bool
	 */
	public function check_role_access( $can_access, $post_id ) {
		if ( ! $can_access ) {
			return false;
		}

		// Check login requirement.
		if ( self::requires_login( $post_id ) && ! is_user_logged_in() ) {
			return false;
		}

		return self::user_has_role_access( $post_id );
	}

	/**
	 * Redirect to login if required.
	 *
	 * @return void
	 */
	public function check_login_required() {
		if ( ! is_singular( 'pdf_document' ) ) {
			return;
		}

		$post_id = get_the_ID();

		// Check if login required and user not logged in.
		if ( self::requires_login( $post_id ) && ! is_user_logged_in() ) {
			// Store redirect URL.
			$redirect_url = get_permalink( $post_id );
			wp_safe_redirect( wp_login_url( $redirect_url ) );
			exit;
		}
	}

	/**
	 * Get access denied message HTML.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public static function get_access_denied_message( $post_id ) {
		$require_login    = self::requires_login( $post_id );
		$has_restrictions = self::has_role_restriction( $post_id );

		ob_start();
		?>
		<div class="pdf-access-denied">
			<h3><?php esc_html_e( 'Access Restricted', 'wp-pdf-embed-seo-optimize' ); ?></h3>
			<?php if ( $require_login && ! is_user_logged_in() ) : ?>
				<p><?php esc_html_e( 'You must be logged in to view this document.', 'wp-pdf-embed-seo-optimize' ); ?></p>
				<p><a href="<?php echo esc_url( wp_login_url( get_permalink( $post_id ) ) ); ?>" class="button"><?php esc_html_e( 'Log In', 'wp-pdf-embed-seo-optimize' ); ?></a></p>
			<?php elseif ( $has_restrictions ) : ?>
				<p><?php esc_html_e( 'You do not have permission to view this document.', 'wp-pdf-embed-seo-optimize' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}

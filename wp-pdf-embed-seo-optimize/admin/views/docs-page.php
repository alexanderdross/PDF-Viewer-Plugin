<?php
/**
 * Documentation page view.
 *
 * @package PDF_Embed_SEO
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap pdf-embed-seo-optimize-docs">
	<h1><?php esc_html_e( 'PDF Embed & SEO Optimize: Docs & Usage', 'wp-pdf-embed-seo-optimize' ); ?></h1>

	<p class="pdf-embed-seo-optimize-docs-intro">
		<?php esc_html_e( 'Welcome to the documentation for PDF Embed & SEO Optimize. Below you will find details on how to control print/download permissions, use the shortcodes included with this plugin, and how to integrate them into your theme or custom templates for the best user experience.', 'wp-pdf-embed-seo-optimize' ); ?>
	</p>

	<?php if ( ! function_exists( 'pdf_embed_seo_is_premium' ) || ! pdf_embed_seo_is_premium() ) : ?>
	<!-- Premium Upgrade CTA -->
	<div class="pdf-embed-seo-optimize-premium-cta">
		<div class="premium-cta-header">
			<span class="dashicons dashicons-star-filled"></span>
			<h2><?php esc_html_e( 'Unlock Premium Features', 'wp-pdf-embed-seo-optimize' ); ?></h2>
		</div>
		<p class="premium-cta-intro">
			<?php esc_html_e( 'Take your PDF management to the next level with PDF Embed & SEO Optimize Pro. Get advanced analytics, password protection, reading progress tracking, and full REST API access.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>
		<div class="premium-features-comparison">
			<div class="feature-column free-column">
				<h3><?php esc_html_e( 'Free Version', 'wp-pdf-embed-seo-optimize' ); ?></h3>
				<ul>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'PDF.js Viewer Integration', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Clean SEO-friendly URLs', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Print/Download Controls', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Gutenberg Block', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Yoast SEO Integration', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Schema.org Markup', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Basic REST API', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'View Statistics', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li class="not-included"><span class="dashicons dashicons-no"></span> <?php esc_html_e( 'Password Protection', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li class="not-included"><span class="dashicons dashicons-no"></span> <?php esc_html_e( 'Analytics Dashboard', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li class="not-included"><span class="dashicons dashicons-no"></span> <?php esc_html_e( 'Reading Progress', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li class="not-included"><span class="dashicons dashicons-no"></span> <?php esc_html_e( 'Categories & Tags', 'wp-pdf-embed-seo-optimize' ); ?></li>
				</ul>
			</div>
			<div class="feature-column premium-column">
				<h3><span class="dashicons dashicons-star-filled"></span> <?php esc_html_e( 'Pro Version', 'wp-pdf-embed-seo-optimize' ); ?></h3>
				<ul>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Everything in Free, plus:', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <strong><?php esc_html_e( 'Password Protection', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Secure sensitive PDFs', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <strong><?php esc_html_e( 'Analytics Dashboard', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Track views, time spent, popular docs', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <strong><?php esc_html_e( 'Reading Progress', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Remember user position', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <strong><?php esc_html_e( 'Categories & Tags', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Organize your PDFs', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <strong><?php esc_html_e( 'Role-Based Access', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Control who sees what', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <strong><?php esc_html_e( 'Bulk Import', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Import multiple PDFs at once', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <strong><?php esc_html_e( 'PDF Sitemap', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Dedicated XML sitemap', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <strong><?php esc_html_e( 'Full REST API', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Analytics, progress, password endpoints', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <strong><?php esc_html_e( 'CSV Export', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Export analytics data', 'wp-pdf-embed-seo-optimize' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <strong><?php esc_html_e( 'Priority Support', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Fast, dedicated help', 'wp-pdf-embed-seo-optimize' ); ?></li>
				</ul>
			</div>
		</div>
		<div class="premium-cta-action">
			<a href="https://pdfviewer.drossmedia.de" target="_blank" rel="noopener noreferrer" class="button button-primary button-hero">
				<span class="dashicons dashicons-cart"></span>
				<?php esc_html_e( 'Upgrade to Pro Now', 'wp-pdf-embed-seo-optimize' ); ?>
			</a>
			<p class="premium-cta-guarantee">
				<span class="dashicons dashicons-shield"></span>
				<?php esc_html_e( '30-Day Money-Back Guarantee', 'wp-pdf-embed-seo-optimize' ); ?>
			</p>
		</div>
	</div>
	<?php else : ?>
	<!-- Premium Active Badge -->
	<div class="pdf-embed-seo-optimize-premium-active">
		<span class="dashicons dashicons-star-filled"></span>
		<strong><?php esc_html_e( 'Pro Version Active', 'wp-pdf-embed-seo-optimize' ); ?></strong>
		<?php esc_html_e( 'Thank you for supporting PDF Embed & SEO Optimize!', 'wp-pdf-embed-seo-optimize' ); ?>
	</div>
	<?php endif; ?>

	<div class="pdf-embed-seo-optimize-docs-toc">
		<h2><?php esc_html_e( 'Table of Contents', 'wp-pdf-embed-seo-optimize' ); ?></h2>
		<ol>
			<?php if ( function_exists( 'pdf_embed_seo_is_premium' ) && pdf_embed_seo_is_premium() ) : ?>
			<li><a href="#license"><?php esc_html_e( 'License Activation', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<?php endif; ?>
			<li><a href="#print-download"><?php esc_html_e( 'Print & Download Permissions', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#gutenberg-block"><?php esc_html_e( 'Gutenberg Block', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#thumbnails"><?php esc_html_e( 'PDF Thumbnails', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#pdf-viewer-sitemap"><?php esc_html_e( '[pdf_viewer_sitemap] Shortcode', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#pdf-viewer"><?php esc_html_e( '[pdf_viewer] Shortcode', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#custom-templates"><?php esc_html_e( 'Using [pdf_viewer] in Custom Template Files', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#url-structure"><?php esc_html_e( 'URL Structure', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#yoast-seo"><?php esc_html_e( 'Yoast SEO Integration', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#rest-api"><?php esc_html_e( 'REST API', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#wordpress-hooks"><?php esc_html_e( 'WordPress Hooks', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<?php if ( function_exists( 'pdf_embed_seo_is_premium' ) && pdf_embed_seo_is_premium() ) : ?>
			<li><a href="#premium-features"><?php esc_html_e( 'Premium Features Guide', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<?php else : ?>
			<li><a href="#premium-preview"><?php esc_html_e( 'Premium Features Preview', 'wp-pdf-embed-seo-optimize' ); ?></a></li>
			<?php endif; ?>
		</ol>
	</div>

	<?php if ( function_exists( 'pdf_embed_seo_is_premium' ) && pdf_embed_seo_is_premium() ) : ?>
	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="license">
		<h2><?php esc_html_e( '0. License Activation', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'Your license key unlocks premium features and enables automatic updates. Keep your license active to receive the latest features and security updates.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'License Status', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<?php
		$license_status = get_option( 'pdf_embed_seo_premium_license_status', 'inactive' );
		$license_tier = get_option( 'pdf_embed_seo_premium_license_tier', '' );
		$license_expires = get_option( 'pdf_embed_seo_premium_license_expires', '' );
		?>
		<table class="widefat" style="max-width: 500px;">
			<tbody>
				<tr>
					<td><strong><?php esc_html_e( 'Status', 'wp-pdf-embed-seo-optimize' ); ?></strong></td>
					<td>
						<?php if ( $license_status === 'valid' ) : ?>
							<span style="color: #46b450;"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Active', 'wp-pdf-embed-seo-optimize' ); ?></span>
						<?php else : ?>
							<span style="color: #dc3232;"><span class="dashicons dashicons-warning"></span> <?php esc_html_e( 'Inactive', 'wp-pdf-embed-seo-optimize' ); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<?php if ( $license_tier ) : ?>
				<tr>
					<td><strong><?php esc_html_e( 'License Tier', 'wp-pdf-embed-seo-optimize' ); ?></strong></td>
					<td><?php echo esc_html( ucfirst( $license_tier ) ); ?></td>
				</tr>
				<?php endif; ?>
				<?php if ( $license_expires ) : ?>
				<tr>
					<td><strong><?php esc_html_e( 'Expires', 'wp-pdf-embed-seo-optimize' ); ?></strong></td>
					<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license_expires ) ) ); ?></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Activating Your License', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<ol>
			<li><?php esc_html_e( 'Go to PDF Documents > Settings > License tab', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Enter your license key (received via email after purchase)', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Click "Activate License"', 'wp-pdf-embed-seo-optimize' ); ?></li>
		</ol>

		<h3><?php esc_html_e( 'License Tiers', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Tier', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Sites', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Features', 'wp-pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><strong><?php esc_html_e( 'Starter', 'wp-pdf-embed-seo-optimize' ); ?></strong></td>
					<td><?php esc_html_e( '1 site', 'wp-pdf-embed-seo-optimize' ); ?></td>
					<td><?php esc_html_e( 'Analytics, Password Protection', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Professional', 'wp-pdf-embed-seo-optimize' ); ?></strong></td>
					<td><?php esc_html_e( '5 sites', 'wp-pdf-embed-seo-optimize' ); ?></td>
					<td><?php esc_html_e( '+ Reading Progress, XML Sitemap, Categories/Tags', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Agency', 'wp-pdf-embed-seo-optimize' ); ?></strong></td>
					<td><?php esc_html_e( 'Unlimited', 'wp-pdf-embed-seo-optimize' ); ?></td>
					<td><?php esc_html_e( '+ Priority Support, Bulk Import, Full API', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<div class="notice notice-info inline" style="padding: 10px; margin: 10px 0;">
			<span class="dashicons dashicons-info" style="color: #0073aa;"></span>
			<?php esc_html_e( 'Need to upgrade? Visit pdfviewer.drossmedia.de to upgrade your license tier.', 'wp-pdf-embed-seo-optimize' ); ?>
		</div>
	</div>
	<?php endif; ?>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="print-download">
		<h2><?php esc_html_e( '1. Print & Download Permissions', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'Control whether visitors can print or download each PDF document. When disabled, the print/download buttons will not appear in the PDF viewer toolbar.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Per-PDF Settings', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php esc_html_e( 'When editing any PDF document, look for the "PDF Settings" meta box in the right sidebar:', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>
		<ul>
			<li><strong><?php esc_html_e( 'Allow Download', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Check to show the download button in the viewer', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><strong><?php esc_html_e( 'Allow Print', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Check to show the print button in the viewer', 'wp-pdf-embed-seo-optimize' ); ?></li>
		</ul>

		<h3><?php esc_html_e( 'Default Settings', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php
			printf(
				/* translators: %s: Settings page link */
				esc_html__( 'You can set default permissions for all new PDFs in %s:', 'wp-pdf-embed-seo-optimize' ),
				'<a href="' . esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-embed-seo-settings' ) ) . '">' . esc_html__( 'PDF Documents > Settings', 'wp-pdf-embed-seo-optimize' ) . '</a>'
			);
			?>
		</p>
		<ul>
			<li><strong><?php esc_html_e( 'Allow Download by Default', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'New PDFs will have download enabled', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><strong><?php esc_html_e( 'Allow Print by Default', 'wp-pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'New PDFs will have print enabled', 'wp-pdf-embed-seo-optimize' ); ?></li>
		</ul>

		<div class="notice notice-info inline" style="padding: 10px; margin: 10px 0;">
			<span class="dashicons dashicons-info" style="color: #0073aa;"></span>
			<?php esc_html_e( 'Note: These settings only control the visibility of buttons in the viewer. The PDF file URL is loaded via AJAX to provide an additional layer of protection.', 'wp-pdf-embed-seo-optimize' ); ?>
		</div>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="gutenberg-block">
		<h2><?php esc_html_e( '2. Gutenberg Block', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'The PDF Viewer block allows you to embed PDF documents directly in the WordPress block editor (Gutenberg). This is the recommended method for embedding PDFs in posts and pages.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'How to Use:', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<ol>
			<li><?php esc_html_e( 'In the block editor, click the "+" button to add a new block', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Search for "PDF Viewer" or find it under the "Embed" category', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Select the PDF document you want to embed from the dropdown', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Optionally adjust width and height in the block settings panel', 'wp-pdf-embed-seo-optimize' ); ?></li>
		</ol>

		<h3><?php esc_html_e( 'Block Settings:', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Setting', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Default', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'wp-pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><strong><?php esc_html_e( 'PDF Document', 'wp-pdf-embed-seo-optimize' ); ?></strong></td>
					<td>-</td>
					<td><?php esc_html_e( 'Select which PDF to display', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Width', 'wp-pdf-embed-seo-optimize' ); ?></strong></td>
					<td>100%</td>
					<td><?php esc_html_e( 'Width of the viewer (e.g., 100%, 800px)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Height', 'wp-pdf-embed-seo-optimize' ); ?></strong></td>
					<td>600px</td>
					<td><?php esc_html_e( 'Height of the viewer (e.g., 600px, 80vh)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Alignment', 'wp-pdf-embed-seo-optimize' ); ?></strong></td>
					<td>None</td>
					<td><?php esc_html_e( 'Supports Wide and Full width alignments', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<div class="notice notice-info inline" style="padding: 10px; margin: 10px 0;">
			<span class="dashicons dashicons-info" style="color: #0073aa;"></span>
			<?php esc_html_e( 'Tip: The block shows a preview in the editor with the PDF title and thumbnail. Use the sidebar settings to customize dimensions.', 'wp-pdf-embed-seo-optimize' ); ?>
		</div>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="thumbnails">
		<h2><?php esc_html_e( '3. PDF Thumbnails', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'Thumbnails improve the visual appearance of your PDF archive pages and social sharing. You can either upload thumbnails manually or let the plugin generate them automatically from the first page of each PDF.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Automatic Thumbnail Generation', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<?php
		$availability = PDF_Embed_SEO_Thumbnail::check_availability();
		if ( $availability['available'] ) :
			?>
			<div class="notice notice-success inline" style="padding: 10px; margin: 10px 0;">
				<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
				<strong><?php esc_html_e( 'Auto-generation is available!', 'wp-pdf-embed-seo-optimize' ); ?></strong>
				<?php echo esc_html( $availability['message'] ); ?>
			</div>
		<?php else : ?>
			<div class="notice notice-warning inline" style="padding: 10px; margin: 10px 0;">
				<span class="dashicons dashicons-warning" style="color: #ffb900;"></span>
				<?php echo esc_html( $availability['message'] ); ?>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'When auto-generation is enabled and available:', 'wp-pdf-embed-seo-optimize' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'A thumbnail is automatically created from the first page of the PDF when you save', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'The thumbnail is set as the featured image if none exists', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Requires ImageMagick with PDF support or Ghostscript on your server', 'wp-pdf-embed-seo-optimize' ); ?></li>
		</ul>

		<h3><?php esc_html_e( 'Manual Thumbnail Generation', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<p><?php esc_html_e( 'You can also manually generate thumbnails at any time:', 'wp-pdf-embed-seo-optimize' ); ?></p>
		<ol>
			<li><?php esc_html_e( 'Edit any PDF document', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Look for the "Featured Image" meta box', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Click the "Generate from PDF" button', 'wp-pdf-embed-seo-optimize' ); ?></li>
		</ol>

		<h3><?php esc_html_e( 'Manual Upload', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<p><?php esc_html_e( 'If you prefer custom thumbnails, simply use WordPress\'s standard "Set featured image" feature to upload any image from your Media Library.', 'wp-pdf-embed-seo-optimize' ); ?></p>

		<h3><?php esc_html_e( 'Settings', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php
			printf(
				/* translators: %s: Settings page link */
				esc_html__( 'Configure auto-generation in %s:', 'wp-pdf-embed-seo-optimize' ),
				'<a href="' . esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-embed-seo-optimize-settings' ) ) . '">' . esc_html__( 'PDF Documents > Settings', 'wp-pdf-embed-seo-optimize' ) . '</a>'
			);
			?>
		</p>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="pdf-viewer-sitemap">
		<h2><?php esc_html_e( '4. [pdf_viewer_sitemap]', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'This shortcode displays a simple HTML sitemap of all published PDF Viewer posts. You can insert it into any page or post to list all your PDF Viewer entries in an unordered list, sorted alphabetically.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Example Usage:', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer_sitemap]</pre>

		<p>
			<?php esc_html_e( 'When placed on a page, this will produce a linked list of every PDF Viewer custom post, allowing visitors to easily view and access all of your PDF documents.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Optional Attributes:', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Attribute', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Default', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'wp-pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>orderby</code></td>
					<td>title</td>
					<td><?php esc_html_e( 'Sort by: title, date, menu_order', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>order</code></td>
					<td>ASC</td>
					<td><?php esc_html_e( 'Sort order: ASC or DESC', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>limit</code></td>
					<td>-1</td>
					<td><?php esc_html_e( 'Number of PDFs to show (-1 for all)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example with Attributes:', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer_sitemap orderby="date" order="DESC" limit="10"]</pre>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="pdf-viewer">
		<h2><?php esc_html_e( '5. [pdf_viewer]', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'This shortcode embeds a single PDF into your content. When used on a single PDF Viewer post, it automatically outputs the PDF for that post.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Basic Example:', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer]</pre>

		<p>
			<?php esc_html_e( 'When you insert this shortcode on a PDF Viewer post, it will dynamically use the current post\'s data to embed the PDF.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Embedding a Specific PDF:', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php esc_html_e( 'The shortcode supports specifying a particular PDF by providing its custom post ID as an attribute. For example:', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer id="384"]</pre>

		<h3><?php esc_html_e( 'Optional Attributes:', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Attribute', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Default', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'wp-pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>id</code></td>
					<td><?php esc_html_e( 'Current post', 'wp-pdf-embed-seo-optimize' ); ?></td>
					<td><?php esc_html_e( 'The PDF document post ID', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>width</code></td>
					<td>100%</td>
					<td><?php esc_html_e( 'Width of the viewer', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>height</code></td>
					<td>800px</td>
					<td><?php esc_html_e( 'Height of the viewer', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example with Dimensions:', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer id="384" width="100%" height="600px"]</pre>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="custom-templates">
		<h2><?php esc_html_e( '6. Using [pdf_viewer] in Custom Template Files', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'Whether you\'re using a page builder (like Elementor, Divi, Beaver Builder, etc.) or you\'ve created a custom template file for your PDF Viewer custom post type, you can insert the shortcode so it automatically displays the PDF of the current post.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Option A: Using a Shortcode or HTML Module in Your Page Builder', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php esc_html_e( 'Most page builders offer a widget or module where you can insert custom shortcodes. Simply add a Text or Shortcode module and insert:', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer]</pre>
		<p>
			<?php esc_html_e( 'This will automatically output the embedded PDF on the PDF Viewer post.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Option B: Embedding the Shortcode in a Custom Template File', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php esc_html_e( 'If you\'re creating a custom template file (for example, in your theme or via a page builder\'s custom code module), you can embed the shortcode using PHP. For example, in your custom template file for the PDF Viewer custom post type:', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>
		<pre class="pdf-embed-seo-optimize-code">&lt;?php
if ( function_exists( 'do_shortcode' ) ) {
    // Automatically uses the current post's PDF data.
    echo do_shortcode( '[pdf_viewer]' );
}
?&gt;</pre>
		<p>
			<?php esc_html_e( 'This PHP snippet dynamically outputs the embedded PDF for the current PDF Viewer post without the need for specifying an ID.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'Both options allow you to integrate the shortcode seamlessly into your custom layouts.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="url-structure">
		<h2><?php esc_html_e( '7. URL Structure', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<p><?php esc_html_e( 'PDF Embed & SEO Optimize uses clean, SEO-friendly URLs:', 'wp-pdf-embed-seo-optimize' ); ?></p>

		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Page Type', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'URL Structure', 'wp-pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'PDF Archive (all PDFs)', 'wp-pdf-embed-seo-optimize' ); ?></td>
					<td><code><?php echo esc_html( home_url( '/pdf/' ) ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Single PDF Document', 'wp-pdf-embed-seo-optimize' ); ?></td>
					<td><code><?php echo esc_html( home_url( '/pdf/your-pdf-slug/' ) ); ?></code></td>
				</tr>
				<tr>
					<td>
						<?php esc_html_e( 'XML Sitemap', 'wp-pdf-embed-seo-optimize' ); ?>
						<?php if ( ! function_exists( 'pdf_embed_seo_is_premium' ) || ! pdf_embed_seo_is_premium() ) : ?>
							<span class="premium-badge"><?php esc_html_e( 'PRO', 'wp-pdf-embed-seo-optimize' ); ?></span>
						<?php endif; ?>
					</td>
					<td><code><?php echo esc_html( home_url( '/pdf/sitemap.xml' ) ); ?></code></td>
				</tr>
			</tbody>
		</table>

		<?php if ( function_exists( 'pdf_embed_seo_is_premium' ) && pdf_embed_seo_is_premium() ) : ?>
		<h3><?php esc_html_e( 'XML Sitemap Features', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<ul class="pdf-embed-seo-optimize-feature-list">
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Dedicated sitemap for all PDF documents', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Beautiful XSL-styled browser view', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Auto-includes PDF metadata (title, description, thumbnail)', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Updates automatically when PDFs change', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Submit to Google Search Console for better indexing', 'wp-pdf-embed-seo-optimize' ); ?></li>
		</ul>
		<?php endif; ?>

		<p>
			<strong><?php esc_html_e( 'Note:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'The PDF slug can be customized via Yoast SEO or the standard WordPress slug editor. This allows you to create descriptive, SEO-friendly URLs for each document.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="yoast-seo">
		<h2><?php esc_html_e( '8. Yoast SEO Integration', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<?php if ( defined( 'WPSEO_VERSION' ) ) : ?>
			<div class="notice notice-success inline" style="padding: 10px; margin: 10px 0;">
				<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
				<strong><?php esc_html_e( 'Yoast SEO is active!', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			</div>
		<?php else : ?>
			<div class="notice notice-warning inline" style="padding: 10px; margin: 10px 0;">
				<span class="dashicons dashicons-warning" style="color: #ffb900;"></span>
				<?php esc_html_e( 'Yoast SEO is not currently active. Install and activate it to unlock full SEO control.', 'wp-pdf-embed-seo-optimize' ); ?>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'When Yoast SEO is active, you can configure the following for each PDF document:', 'wp-pdf-embed-seo-optimize' ); ?></p>

		<ul class="pdf-embed-seo-optimize-feature-list">
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'SEO Title - Custom page title for search results', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Meta Description - Custom description for search results', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'URL Slug - Clean, descriptive URL for the PDF', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Featured Image - Thumbnail for social sharing and archive pages', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'OpenGraph Tags - Control how PDF appears on Facebook/LinkedIn', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Twitter Cards - Control how PDF appears on Twitter/X', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Schema Markup - DigitalDocument schema for rich search results', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Sitemap Inclusion - PDFs automatically added to XML sitemap', 'wp-pdf-embed-seo-optimize' ); ?></li>
		</ul>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="rest-api">
		<h2><?php esc_html_e( '9. REST API', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'PDF Embed & SEO Optimize provides a RESTful API for accessing PDF documents programmatically. This allows integration with external applications, mobile apps, headless WordPress setups, and custom frontends.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'API Base URL', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code"><?php echo esc_html( rest_url( 'pdf-embed-seo/v1/' ) ); ?></pre>

		<h3><?php esc_html_e( 'Public Endpoints (Free)', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Method', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Endpoint', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'wp-pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>GET</code></td>
					<td><code>/documents</code></td>
					<td><?php esc_html_e( 'List all published PDF documents with pagination', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/documents/{id}</code></td>
					<td><?php esc_html_e( 'Get single PDF document details', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/documents/{id}/data</code></td>
					<td><?php esc_html_e( 'Get PDF file URL securely (for viewer)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>POST</code></td>
					<td><code>/documents/{id}/view</code></td>
					<td><?php esc_html_e( 'Track a PDF view (increment view count)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/settings</code></td>
					<td><?php esc_html_e( 'Get public plugin settings', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3>
			<?php esc_html_e( 'Premium Endpoints', 'wp-pdf-embed-seo-optimize' ); ?>
			<?php if ( ! function_exists( 'pdf_embed_seo_is_premium' ) || ! pdf_embed_seo_is_premium() ) : ?>
				<span class="premium-badge"><?php esc_html_e( 'PRO', 'wp-pdf-embed-seo-optimize' ); ?></span>
			<?php endif; ?>
		</h3>

		<?php if ( ! function_exists( 'pdf_embed_seo_is_premium' ) || ! pdf_embed_seo_is_premium() ) : ?>
		<div class="premium-api-teaser">
			<p><?php esc_html_e( 'Upgrade to Pro to unlock these powerful API endpoints for advanced integrations:', 'wp-pdf-embed-seo-optimize' ); ?></p>
		<?php endif; ?>

		<table class="widefat <?php echo ( ! function_exists( 'pdf_embed_seo_is_premium' ) || ! pdf_embed_seo_is_premium() ) ? 'premium-locked' : ''; ?>">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Method', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Endpoint', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'wp-pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>GET</code></td>
					<td><code>/analytics</code></td>
					<td><?php esc_html_e( 'Get analytics overview (admin only)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/analytics/documents</code></td>
					<td><?php esc_html_e( 'Get per-document analytics (admin only)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/analytics/export</code></td>
					<td><?php esc_html_e( 'Export analytics as CSV/JSON (admin only)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/documents/{id}/progress</code></td>
					<td><?php esc_html_e( 'Get reading progress for a PDF', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>POST</code></td>
					<td><code>/documents/{id}/progress</code></td>
					<td><?php esc_html_e( 'Save reading progress (page, scroll, zoom)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>POST</code></td>
					<td><code>/documents/{id}/verify-password</code></td>
					<td><?php esc_html_e( 'Verify password for protected PDFs', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/categories</code></td>
					<td><?php esc_html_e( 'Get all PDF categories', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/tags</code></td>
					<td><?php esc_html_e( 'Get all PDF tags', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>POST</code></td>
					<td><code>/bulk/import</code></td>
					<td><?php esc_html_e( 'Start bulk PDF import (admin only)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/bulk/import/status</code></td>
					<td><?php esc_html_e( 'Get bulk import status (admin only)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<?php if ( ! function_exists( 'pdf_embed_seo_is_premium' ) || ! pdf_embed_seo_is_premium() ) : ?>
		<div class="premium-api-cta">
			<a href="https://pdfviewer.drossmedia.de" target="_blank" rel="noopener noreferrer" class="button button-primary">
				<span class="dashicons dashicons-unlock"></span>
				<?php esc_html_e( 'Unlock Premium API Endpoints', 'wp-pdf-embed-seo-optimize' ); ?>
			</a>
		</div>
		</div>
		<?php endif; ?>

		<h3><?php esc_html_e( 'Query Parameters for /documents', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Parameter', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Default', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'wp-pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>page</code></td>
					<td>1</td>
					<td><?php esc_html_e( 'Page number for pagination', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>per_page</code></td>
					<td>10</td>
					<td><?php esc_html_e( 'Items per page (max 100)', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>search</code></td>
					<td>-</td>
					<td><?php esc_html_e( 'Search term to filter documents', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>orderby</code></td>
					<td>date</td>
					<td><?php esc_html_e( 'Sort by: date, title, modified, views', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>order</code></td>
					<td>desc</td>
					<td><?php esc_html_e( 'Sort order: asc or desc', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example: List Documents', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">curl -X GET "<?php echo esc_url( rest_url( 'pdf-embed-seo/v1/documents?per_page=5&orderby=views' ) ); ?>"</pre>

		<h3><?php esc_html_e( 'Example: Get Single Document', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">curl -X GET "<?php echo esc_url( rest_url( 'pdf-embed-seo/v1/documents/123' ) ); ?>"</pre>

		<h3><?php esc_html_e( 'Example Response', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">{
  "id": 123,
  "title": "Annual Report 2024",
  "slug": "annual-report-2024",
  "url": "<?php echo esc_url( home_url( '/pdf/annual-report-2024/' ) ); ?>",
  "excerpt": "Company annual report...",
  "date": "2024-01-15T10:30:00+00:00",
  "views": 1542,
  "thumbnail": "<?php echo esc_url( home_url( '/wp-content/uploads/thumb.jpg' ) ); ?>",
  "allow_download": true,
  "allow_print": false
}</pre>

		<h3><?php esc_html_e( 'Authentication', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<p><?php esc_html_e( 'Public endpoints require no authentication. Admin-only endpoints require authentication via:', 'wp-pdf-embed-seo-optimize' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'Cookie authentication (when logged into WordPress)', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Application Passwords (WordPress 5.6+)', 'wp-pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'OAuth / JWT via third-party plugins', 'wp-pdf-embed-seo-optimize' ); ?></li>
		</ul>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="wordpress-hooks">
		<h2><?php esc_html_e( '10. WordPress Hooks', 'wp-pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'wp-pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'Developers can use these action and filter hooks to extend or customize the plugin functionality.', 'wp-pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Actions', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Hook Name', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Parameters', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'wp-pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>pdf_embed_seo_pdf_viewed</code></td>
					<td><code>$post_id, $count</code></td>
					<td><?php esc_html_e( 'Fired when a PDF is viewed. Use for custom analytics.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_premium_init</code></td>
					<td>-</td>
					<td><?php esc_html_e( 'Fired when premium features are initialized.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_settings_saved</code></td>
					<td><code>$post_id, $settings</code></td>
					<td><?php esc_html_e( 'Fired when PDF settings are saved.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example: Track PDF Views', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">add_action( 'pdf_embed_seo_pdf_viewed', function( $post_id, $count ) {
    // Send to your analytics service
    my_analytics_track( 'pdf_view', [
        'pdf_id' => $post_id,
        'title'  => get_the_title( $post_id ),
        'views'  => $count,
    ]);
}, 10, 2 );</pre>

		<h3><?php esc_html_e( 'Filters', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Hook Name', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Parameters', 'wp-pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'wp-pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>pdf_embed_seo_post_type_args</code></td>
					<td><code>$args</code></td>
					<td><?php esc_html_e( 'Modify the PDF document post type registration arguments.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_schema_data</code></td>
					<td><code>$schema, $post_id</code></td>
					<td><?php esc_html_e( 'Modify Schema.org data for a single PDF.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_archive_schema_data</code></td>
					<td><code>$schema</code></td>
					<td><?php esc_html_e( 'Modify Schema.org data for the archive page.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_archive_query</code></td>
					<td><code>$posts_per_page</code></td>
					<td><?php esc_html_e( 'Modify the archive page posts per page setting.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_sitemap_query_args</code></td>
					<td><code>$query_args, $atts</code></td>
					<td><?php esc_html_e( 'Modify the sitemap shortcode query arguments.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_archive_title</code></td>
					<td><code>$title</code></td>
					<td><?php esc_html_e( 'Modify the archive page title.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_archive_description</code></td>
					<td><code>$description</code></td>
					<td><?php esc_html_e( 'Modify the archive page description.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_viewer_options</code></td>
					<td><code>$options, $post_id</code></td>
					<td><?php esc_html_e( 'Modify PDF.js viewer options.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_allowed_types</code></td>
					<td><code>$types</code></td>
					<td><?php esc_html_e( 'Modify allowed MIME types for upload.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_rest_document</code></td>
					<td><code>$data, $post, $detailed</code></td>
					<td><?php esc_html_e( 'Modify REST API document response.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_rest_document_data</code></td>
					<td><code>$data, $post_id</code></td>
					<td><?php esc_html_e( 'Modify REST API document data response.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_rest_settings</code></td>
					<td><code>$settings</code></td>
					<td><?php esc_html_e( 'Modify REST API settings response.', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<?php if ( function_exists( 'pdf_embed_seo_is_premium' ) && pdf_embed_seo_is_premium() ) : ?>
				<tr>
					<td><code>pdf_embed_seo_password_error</code></td>
					<td><code>$error</code></td>
					<td><?php esc_html_e( 'Custom password validation error (Premium).', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_verify_password</code></td>
					<td><code>$is_valid, $post_id, $password</code></td>
					<td><?php esc_html_e( 'Override password verification (Premium).', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_rest_analytics</code></td>
					<td><code>$data, $period</code></td>
					<td><?php esc_html_e( 'Modify REST API analytics response (Premium).', 'wp-pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example: Add Custom Schema Data', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">add_filter( 'pdf_embed_seo_schema_data', function( $schema, $post_id ) {
    // Add custom author information
    $schema['author'] = [
        '@type' => 'Person',
        'name'  => get_post_meta( $post_id, '_pdf_author', true ),
    ];
    return $schema;
}, 10, 2 );</pre>

		<h3><?php esc_html_e( 'Example: Customize Archive Title', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">add_filter( 'pdf_embed_seo_archive_title', function( $title ) {
    return 'Our Document Library';
});</pre>

		<h3><?php esc_html_e( 'Example: Add Custom Field to REST API', 'wp-pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">add_filter( 'pdf_embed_seo_rest_document', function( $data, $post, $detailed ) {
    // Add custom category
    $data['department'] = get_post_meta( $post->ID, '_pdf_department', true );
    return $data;
}, 10, 3 );</pre>
	</div>

	<hr>

	<p class="pdf-embed-seo-optimize-docs-footer">
		<em>
			<?php
			printf(
				/* translators: %s: Plugin name */
				esc_html__( 'That\'s it! We hope this documentation helps you quickly set up and use our %s plugin. If you have any questions or run into issues, please refer to our support resources.', 'wp-pdf-embed-seo-optimize' ),
				'<strong>PDF Embed & SEO Optimize</strong>'
			);
			?>
		</em>
	</p>

	<p class="pdf-embed-seo-optimize-docs-credit">
		<?php
		printf(
			/* translators: %s: Dross:Media link */
			esc_html__( 'made with %1$s by %2$s', 'wp-pdf-embed-seo-optimize' ),
			'<span style="color: #e25555;">♥</span>',
			'<a href="https://dross.net/media/" target="_blank" rel="noopener noreferrer">Dross:Media</a>'
		);
		?>
	</p>
</div>

<style>
.pdf-embed-seo-optimize-docs {
	max-width: 900px;
}
.pdf-embed-seo-optimize-docs-intro {
	font-size: 14px;
	line-height: 1.6;
}
.pdf-embed-seo-optimize-docs-toc {
	background: #f9f9f9;
	padding: 15px 20px;
	border-left: 4px solid #0073aa;
	margin: 20px 0;
}
.pdf-embed-seo-optimize-docs-toc h2 {
	margin-top: 0;
}
.pdf-embed-seo-optimize-docs-section {
	margin: 30px 0;
}
.pdf-embed-seo-optimize-code {
	background: #f1f1f1;
	padding: 10px 15px;
	border-left: 3px solid #ccc;
	font-family: monospace;
	overflow-x: auto;
	white-space: pre-wrap;
}
.pdf-embed-seo-optimize-feature-list {
	list-style: none;
	padding: 0;
}
.pdf-embed-seo-optimize-feature-list li {
	padding: 5px 0;
}
.pdf-embed-seo-optimize-feature-list .dashicons-yes {
	color: #46b450;
}
.pdf-embed-seo-optimize-docs-footer {
	background: #fef7e5;
	padding: 15px;
	border-left: 4px solid #ffb900;
	margin-top: 30px;
}
.pdf-embed-seo-optimize-docs-credit {
	text-align: center;
	margin-top: 30px;
	color: #666;
	font-size: 13px;
}
.pdf-embed-seo-optimize-docs-credit a {
	color: #0073aa;
	text-decoration: none;
}
.pdf-embed-seo-optimize-docs-credit a:hover {
	text-decoration: underline;
}

/* Premium CTA Styles */
.pdf-embed-seo-optimize-premium-cta {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	border-radius: 12px;
	padding: 30px;
	margin: 25px 0;
	color: #fff;
	box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.pdf-embed-seo-optimize-premium-cta .premium-cta-header {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 15px;
}
.pdf-embed-seo-optimize-premium-cta .premium-cta-header .dashicons {
	font-size: 28px;
	width: 28px;
	height: 28px;
	color: #ffd700;
}
.pdf-embed-seo-optimize-premium-cta .premium-cta-header h2 {
	margin: 0;
	color: #fff;
	font-size: 24px;
}
.pdf-embed-seo-optimize-premium-cta .premium-cta-intro {
	font-size: 15px;
	opacity: 0.95;
	margin-bottom: 25px;
	line-height: 1.6;
}
.pdf-embed-seo-optimize-premium-cta .premium-features-comparison {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 20px;
	margin-bottom: 25px;
}
@media (max-width: 768px) {
	.pdf-embed-seo-optimize-premium-cta .premium-features-comparison {
		grid-template-columns: 1fr;
	}
}
.pdf-embed-seo-optimize-premium-cta .feature-column {
	background: rgba(255,255,255,0.1);
	border-radius: 8px;
	padding: 20px;
}
.pdf-embed-seo-optimize-premium-cta .feature-column h3 {
	margin: 0 0 15px 0;
	font-size: 16px;
	color: #fff;
	display: flex;
	align-items: center;
	gap: 8px;
}
.pdf-embed-seo-optimize-premium-cta .feature-column h3 .dashicons {
	color: #ffd700;
}
.pdf-embed-seo-optimize-premium-cta .feature-column ul {
	list-style: none;
	padding: 0;
	margin: 0;
}
.pdf-embed-seo-optimize-premium-cta .feature-column li {
	padding: 6px 0;
	font-size: 13px;
	display: flex;
	align-items: flex-start;
	gap: 8px;
}
.pdf-embed-seo-optimize-premium-cta .feature-column li .dashicons {
	flex-shrink: 0;
	margin-top: 2px;
}
.pdf-embed-seo-optimize-premium-cta .feature-column li .dashicons-yes {
	color: #7cff7c;
}
.pdf-embed-seo-optimize-premium-cta .feature-column li .dashicons-no {
	color: rgba(255,255,255,0.4);
}
.pdf-embed-seo-optimize-premium-cta .feature-column li.not-included {
	opacity: 0.5;
}
.pdf-embed-seo-optimize-premium-cta .premium-column {
	background: rgba(255,255,255,0.2);
	border: 2px solid rgba(255,215,0,0.5);
}
.pdf-embed-seo-optimize-premium-cta .premium-cta-action {
	text-align: center;
}
.pdf-embed-seo-optimize-premium-cta .premium-cta-action .button-hero {
	background: #ffd700;
	border-color: #e6c200;
	color: #333;
	font-size: 16px;
	padding: 12px 35px;
	height: auto;
	display: inline-flex;
	align-items: center;
	gap: 10px;
	text-shadow: none;
	box-shadow: 0 4px 15px rgba(0,0,0,0.2);
	transition: all 0.3s ease;
}
.pdf-embed-seo-optimize-premium-cta .premium-cta-action .button-hero:hover {
	background: #ffe44d;
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}
.pdf-embed-seo-optimize-premium-cta .premium-cta-action .button-hero .dashicons {
	font-size: 20px;
	width: 20px;
	height: 20px;
}
.pdf-embed-seo-optimize-premium-cta .premium-cta-guarantee {
	margin-top: 15px;
	font-size: 13px;
	opacity: 0.9;
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 6px;
}
.pdf-embed-seo-optimize-premium-cta .premium-cta-guarantee .dashicons {
	color: #7cff7c;
}

/* Premium Badge in headings */
.premium-badge {
	display: inline-block;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: #fff;
	font-size: 10px;
	font-weight: 600;
	padding: 3px 8px;
	border-radius: 4px;
	margin-left: 10px;
	vertical-align: middle;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

/* Premium API Teaser */
.premium-api-teaser {
	background: linear-gradient(135deg, #f8f9ff 0%, #f0f1ff 100%);
	border: 2px dashed #667eea;
	border-radius: 8px;
	padding: 20px;
	margin: 15px 0;
}
.premium-api-teaser p {
	margin: 0 0 15px 0;
	color: #555;
}
.premium-api-teaser table.premium-locked {
	opacity: 0.7;
}
.premium-api-cta {
	text-align: center;
	margin-top: 20px;
	padding-top: 15px;
	border-top: 1px solid #ddd;
}
.premium-api-cta .button {
	display: inline-flex;
	align-items: center;
	gap: 8px;
}
.premium-api-cta .button .dashicons {
	font-size: 16px;
	width: 16px;
	height: 16px;
}

/* Premium Active Badge */
.pdf-embed-seo-optimize-premium-active {
	background: linear-gradient(135deg, #46b450 0%, #2e7d32 100%);
	color: #fff;
	padding: 15px 20px;
	border-radius: 8px;
	margin: 20px 0;
	display: flex;
	align-items: center;
	gap: 10px;
}
.pdf-embed-seo-optimize-premium-active .dashicons {
	color: #ffd700;
	font-size: 24px;
	width: 24px;
	height: 24px;
}
</style>

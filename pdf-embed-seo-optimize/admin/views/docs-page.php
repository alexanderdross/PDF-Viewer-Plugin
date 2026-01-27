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
	<h1><?php esc_html_e( 'PDF Embed & SEO Optimize: Docs & Usage', 'pdf-embed-seo-optimize' ); ?></h1>

	<p class="pdf-embed-seo-optimize-docs-intro">
		<?php esc_html_e( 'Welcome to the documentation for PDF Embed & SEO Optimize. Below you will find details on how to control print/download permissions, use the shortcodes included with this plugin, and how to integrate them into your theme or custom templates for the best user experience.', 'pdf-embed-seo-optimize' ); ?>
	</p>

	<div class="pdf-embed-seo-optimize-docs-toc">
		<h2><?php esc_html_e( 'Table of Contents', 'pdf-embed-seo-optimize' ); ?></h2>
		<ol>
			<li><a href="#print-download"><?php esc_html_e( 'Print & Download Permissions', 'pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#gutenberg-block"><?php esc_html_e( 'Gutenberg Block', 'pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#thumbnails"><?php esc_html_e( 'PDF Thumbnails', 'pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#pdf-viewer-sitemap"><?php esc_html_e( '[pdf_viewer_sitemap] Shortcode', 'pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#pdf-viewer"><?php esc_html_e( '[pdf_viewer] Shortcode', 'pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#custom-templates"><?php esc_html_e( 'Using [pdf_viewer] in Custom Template Files', 'pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#url-structure"><?php esc_html_e( 'URL Structure', 'pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#yoast-seo"><?php esc_html_e( 'Yoast SEO Integration', 'pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#rest-api"><?php esc_html_e( 'REST API', 'pdf-embed-seo-optimize' ); ?></a></li>
			<li><a href="#wordpress-hooks"><?php esc_html_e( 'WordPress Hooks', 'pdf-embed-seo-optimize' ); ?></a></li>
		</ol>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="print-download">
		<h2><?php esc_html_e( '1. Print & Download Permissions', 'pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'Control whether visitors can print or download each PDF document. When disabled, the print/download buttons will not appear in the PDF viewer toolbar.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Per-PDF Settings', 'pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php esc_html_e( 'When editing any PDF document, look for the "PDF Settings" meta box in the right sidebar:', 'pdf-embed-seo-optimize' ); ?>
		</p>
		<ul>
			<li><strong><?php esc_html_e( 'Allow Download', 'pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Check to show the download button in the viewer', 'pdf-embed-seo-optimize' ); ?></li>
			<li><strong><?php esc_html_e( 'Allow Print', 'pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'Check to show the print button in the viewer', 'pdf-embed-seo-optimize' ); ?></li>
		</ul>

		<h3><?php esc_html_e( 'Default Settings', 'pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php
			printf(
				/* translators: %s: Settings page link */
				esc_html__( 'You can set default permissions for all new PDFs in %s:', 'pdf-embed-seo-optimize' ),
				'<a href="' . esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-embed-seo-settings' ) ) . '">' . esc_html__( 'PDF Documents > Settings', 'pdf-embed-seo-optimize' ) . '</a>'
			);
			?>
		</p>
		<ul>
			<li><strong><?php esc_html_e( 'Allow Download by Default', 'pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'New PDFs will have download enabled', 'pdf-embed-seo-optimize' ); ?></li>
			<li><strong><?php esc_html_e( 'Allow Print by Default', 'pdf-embed-seo-optimize' ); ?></strong> - <?php esc_html_e( 'New PDFs will have print enabled', 'pdf-embed-seo-optimize' ); ?></li>
		</ul>

		<div class="notice notice-info inline" style="padding: 10px; margin: 10px 0;">
			<span class="dashicons dashicons-info" style="color: #0073aa;"></span>
			<?php esc_html_e( 'Note: These settings only control the visibility of buttons in the viewer. The PDF file URL is loaded via AJAX to provide an additional layer of protection.', 'pdf-embed-seo-optimize' ); ?>
		</div>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="gutenberg-block">
		<h2><?php esc_html_e( '2. Gutenberg Block', 'pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'The PDF Viewer block allows you to embed PDF documents directly in the WordPress block editor (Gutenberg). This is the recommended method for embedding PDFs in posts and pages.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'How to Use:', 'pdf-embed-seo-optimize' ); ?></h3>
		<ol>
			<li><?php esc_html_e( 'In the block editor, click the "+" button to add a new block', 'pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Search for "PDF Viewer" or find it under the "Embed" category', 'pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Select the PDF document you want to embed from the dropdown', 'pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Optionally adjust width and height in the block settings panel', 'pdf-embed-seo-optimize' ); ?></li>
		</ol>

		<h3><?php esc_html_e( 'Block Settings:', 'pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Setting', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Default', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><strong><?php esc_html_e( 'PDF Document', 'pdf-embed-seo-optimize' ); ?></strong></td>
					<td>-</td>
					<td><?php esc_html_e( 'Select which PDF to display', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Width', 'pdf-embed-seo-optimize' ); ?></strong></td>
					<td>100%</td>
					<td><?php esc_html_e( 'Width of the viewer (e.g., 100%, 800px)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Height', 'pdf-embed-seo-optimize' ); ?></strong></td>
					<td>600px</td>
					<td><?php esc_html_e( 'Height of the viewer (e.g., 600px, 80vh)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Alignment', 'pdf-embed-seo-optimize' ); ?></strong></td>
					<td>None</td>
					<td><?php esc_html_e( 'Supports Wide and Full width alignments', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<div class="notice notice-info inline" style="padding: 10px; margin: 10px 0;">
			<span class="dashicons dashicons-info" style="color: #0073aa;"></span>
			<?php esc_html_e( 'Tip: The block shows a preview in the editor with the PDF title and thumbnail. Use the sidebar settings to customize dimensions.', 'pdf-embed-seo-optimize' ); ?>
		</div>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="thumbnails">
		<h2><?php esc_html_e( '3. PDF Thumbnails', 'pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'Thumbnails improve the visual appearance of your PDF archive pages and social sharing. You can either upload thumbnails manually or let the plugin generate them automatically from the first page of each PDF.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Automatic Thumbnail Generation', 'pdf-embed-seo-optimize' ); ?></h3>
		<?php
		$availability = PDF_Embed_SEO_Thumbnail::check_availability();
		if ( $availability['available'] ) :
			?>
			<div class="notice notice-success inline" style="padding: 10px; margin: 10px 0;">
				<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
				<strong><?php esc_html_e( 'Auto-generation is available!', 'pdf-embed-seo-optimize' ); ?></strong>
				<?php echo esc_html( $availability['message'] ); ?>
			</div>
		<?php else : ?>
			<div class="notice notice-warning inline" style="padding: 10px; margin: 10px 0;">
				<span class="dashicons dashicons-warning" style="color: #ffb900;"></span>
				<?php echo esc_html( $availability['message'] ); ?>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'When auto-generation is enabled and available:', 'pdf-embed-seo-optimize' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'A thumbnail is automatically created from the first page of the PDF when you save', 'pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'The thumbnail is set as the featured image if none exists', 'pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Requires ImageMagick with PDF support or Ghostscript on your server', 'pdf-embed-seo-optimize' ); ?></li>
		</ul>

		<h3><?php esc_html_e( 'Manual Thumbnail Generation', 'pdf-embed-seo-optimize' ); ?></h3>
		<p><?php esc_html_e( 'You can also manually generate thumbnails at any time:', 'pdf-embed-seo-optimize' ); ?></p>
		<ol>
			<li><?php esc_html_e( 'Edit any PDF document', 'pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Look for the "Featured Image" meta box', 'pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Click the "Generate from PDF" button', 'pdf-embed-seo-optimize' ); ?></li>
		</ol>

		<h3><?php esc_html_e( 'Manual Upload', 'pdf-embed-seo-optimize' ); ?></h3>
		<p><?php esc_html_e( 'If you prefer custom thumbnails, simply use WordPress\'s standard "Set featured image" feature to upload any image from your Media Library.', 'pdf-embed-seo-optimize' ); ?></p>

		<h3><?php esc_html_e( 'Settings', 'pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php
			printf(
				/* translators: %s: Settings page link */
				esc_html__( 'Configure auto-generation in %s:', 'pdf-embed-seo-optimize' ),
				'<a href="' . esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-embed-seo-optimize-settings' ) ) . '">' . esc_html__( 'PDF Documents > Settings', 'pdf-embed-seo-optimize' ) . '</a>'
			);
			?>
		</p>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="pdf-viewer-sitemap">
		<h2><?php esc_html_e( '4. [pdf_viewer_sitemap]', 'pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'This shortcode displays a simple HTML sitemap of all published PDF Viewer posts. You can insert it into any page or post to list all your PDF Viewer entries in an unordered list, sorted alphabetically.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Example Usage:', 'pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer_sitemap]</pre>

		<p>
			<?php esc_html_e( 'When placed on a page, this will produce a linked list of every PDF Viewer custom post, allowing visitors to easily view and access all of your PDF documents.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Optional Attributes:', 'pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Attribute', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Default', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>orderby</code></td>
					<td>title</td>
					<td><?php esc_html_e( 'Sort by: title, date, menu_order', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>order</code></td>
					<td>ASC</td>
					<td><?php esc_html_e( 'Sort order: ASC or DESC', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>limit</code></td>
					<td>-1</td>
					<td><?php esc_html_e( 'Number of PDFs to show (-1 for all)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example with Attributes:', 'pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer_sitemap orderby="date" order="DESC" limit="10"]</pre>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="pdf-viewer">
		<h2><?php esc_html_e( '5. [pdf_viewer]', 'pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'This shortcode embeds a single PDF into your content. When used on a single PDF Viewer post, it automatically outputs the PDF for that post.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Basic Example:', 'pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer]</pre>

		<p>
			<?php esc_html_e( 'When you insert this shortcode on a PDF Viewer post, it will dynamically use the current post\'s data to embed the PDF.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Embedding a Specific PDF:', 'pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php esc_html_e( 'The shortcode supports specifying a particular PDF by providing its custom post ID as an attribute. For example:', 'pdf-embed-seo-optimize' ); ?>
		</p>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer id="384"]</pre>

		<h3><?php esc_html_e( 'Optional Attributes:', 'pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Attribute', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Default', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>id</code></td>
					<td><?php esc_html_e( 'Current post', 'pdf-embed-seo-optimize' ); ?></td>
					<td><?php esc_html_e( 'The PDF document post ID', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>width</code></td>
					<td>100%</td>
					<td><?php esc_html_e( 'Width of the viewer', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>height</code></td>
					<td>800px</td>
					<td><?php esc_html_e( 'Height of the viewer', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example with Dimensions:', 'pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer id="384" width="100%" height="600px"]</pre>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="custom-templates">
		<h2><?php esc_html_e( '6. Using [pdf_viewer] in Custom Template Files', 'pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'Whether you\'re using a page builder (like Elementor, Divi, Beaver Builder, etc.) or you\'ve created a custom template file for your PDF Viewer custom post type, you can insert the shortcode so it automatically displays the PDF of the current post.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Option A: Using a Shortcode or HTML Module in Your Page Builder', 'pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php esc_html_e( 'Most page builders offer a widget or module where you can insert custom shortcodes. Simply add a Text or Shortcode module and insert:', 'pdf-embed-seo-optimize' ); ?>
		</p>
		<pre class="pdf-embed-seo-optimize-code">[pdf_viewer]</pre>
		<p>
			<?php esc_html_e( 'This will automatically output the embedded PDF on the PDF Viewer post.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Option B: Embedding the Shortcode in a Custom Template File', 'pdf-embed-seo-optimize' ); ?></h3>
		<p>
			<?php esc_html_e( 'If you\'re creating a custom template file (for example, in your theme or via a page builder\'s custom code module), you can embed the shortcode using PHP. For example, in your custom template file for the PDF Viewer custom post type:', 'pdf-embed-seo-optimize' ); ?>
		</p>
		<pre class="pdf-embed-seo-optimize-code">&lt;?php
if ( function_exists( 'do_shortcode' ) ) {
    // Automatically uses the current post's PDF data.
    echo do_shortcode( '[pdf_viewer]' );
}
?&gt;</pre>
		<p>
			<?php esc_html_e( 'This PHP snippet dynamically outputs the embedded PDF for the current PDF Viewer post without the need for specifying an ID.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'Both options allow you to integrate the shortcode seamlessly into your custom layouts.', 'pdf-embed-seo-optimize' ); ?>
		</p>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="url-structure">
		<h2><?php esc_html_e( '7. URL Structure', 'pdf-embed-seo-optimize' ); ?></h2>

		<p><?php esc_html_e( 'PDF Embed & SEO Optimize uses clean, SEO-friendly URLs:', 'pdf-embed-seo-optimize' ); ?></p>

		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Page Type', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'URL Structure', 'pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'PDF Archive (all PDFs)', 'pdf-embed-seo-optimize' ); ?></td>
					<td><code><?php echo esc_html( home_url( '/pdf/' ) ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Single PDF Document', 'pdf-embed-seo-optimize' ); ?></td>
					<td><code><?php echo esc_html( home_url( '/pdf/your-pdf-slug/' ) ); ?></code></td>
				</tr>
			</tbody>
		</table>

		<p>
			<strong><?php esc_html_e( 'Note:', 'pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'The PDF slug can be customized via Yoast SEO or the standard WordPress slug editor. This allows you to create descriptive, SEO-friendly URLs for each document.', 'pdf-embed-seo-optimize' ); ?>
		</p>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="yoast-seo">
		<h2><?php esc_html_e( '8. Yoast SEO Integration', 'pdf-embed-seo-optimize' ); ?></h2>

		<?php if ( defined( 'WPSEO_VERSION' ) ) : ?>
			<div class="notice notice-success inline" style="padding: 10px; margin: 10px 0;">
				<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
				<strong><?php esc_html_e( 'Yoast SEO is active!', 'pdf-embed-seo-optimize' ); ?></strong>
			</div>
		<?php else : ?>
			<div class="notice notice-warning inline" style="padding: 10px; margin: 10px 0;">
				<span class="dashicons dashicons-warning" style="color: #ffb900;"></span>
				<?php esc_html_e( 'Yoast SEO is not currently active. Install and activate it to unlock full SEO control.', 'pdf-embed-seo-optimize' ); ?>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'When Yoast SEO is active, you can configure the following for each PDF document:', 'pdf-embed-seo-optimize' ); ?></p>

		<ul class="pdf-embed-seo-optimize-feature-list">
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'SEO Title - Custom page title for search results', 'pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Meta Description - Custom description for search results', 'pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'URL Slug - Clean, descriptive URL for the PDF', 'pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Featured Image - Thumbnail for social sharing and archive pages', 'pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'OpenGraph Tags - Control how PDF appears on Facebook/LinkedIn', 'pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Twitter Cards - Control how PDF appears on Twitter/X', 'pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Schema Markup - DigitalDocument schema for rich search results', 'pdf-embed-seo-optimize' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Sitemap Inclusion - PDFs automatically added to XML sitemap', 'pdf-embed-seo-optimize' ); ?></li>
		</ul>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="rest-api">
		<h2><?php esc_html_e( '9. REST API', 'pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'PDF Embed & SEO Optimize provides a RESTful API for accessing PDF documents programmatically. This allows integration with external applications, mobile apps, headless WordPress setups, and custom frontends.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'API Base URL', 'pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code"><?php echo esc_html( rest_url( 'pdf-embed-seo/v1/' ) ); ?></pre>

		<h3><?php esc_html_e( 'Public Endpoints (Free)', 'pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Method', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Endpoint', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>GET</code></td>
					<td><code>/documents</code></td>
					<td><?php esc_html_e( 'List all published PDF documents with pagination', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/documents/{id}</code></td>
					<td><?php esc_html_e( 'Get single PDF document details', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/documents/{id}/data</code></td>
					<td><?php esc_html_e( 'Get PDF file URL securely (for viewer)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>POST</code></td>
					<td><code>/documents/{id}/view</code></td>
					<td><?php esc_html_e( 'Track a PDF view (increment view count)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/settings</code></td>
					<td><?php esc_html_e( 'Get public plugin settings', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<?php if ( function_exists( 'pdf_embed_seo_is_premium' ) && pdf_embed_seo_is_premium() ) : ?>
		<h3><?php esc_html_e( 'Premium Endpoints', 'pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Method', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Endpoint', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>GET</code></td>
					<td><code>/analytics</code></td>
					<td><?php esc_html_e( 'Get analytics overview (admin only)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/analytics/documents</code></td>
					<td><?php esc_html_e( 'Get per-document analytics (admin only)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/analytics/export</code></td>
					<td><?php esc_html_e( 'Export analytics as CSV/JSON (admin only)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/documents/{id}/progress</code></td>
					<td><?php esc_html_e( 'Get reading progress for a PDF', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>POST</code></td>
					<td><code>/documents/{id}/progress</code></td>
					<td><?php esc_html_e( 'Save reading progress (page, scroll, zoom)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>POST</code></td>
					<td><code>/documents/{id}/verify-password</code></td>
					<td><?php esc_html_e( 'Verify password for protected PDFs', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/categories</code></td>
					<td><?php esc_html_e( 'Get all PDF categories', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/tags</code></td>
					<td><?php esc_html_e( 'Get all PDF tags', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>POST</code></td>
					<td><code>/bulk/import</code></td>
					<td><?php esc_html_e( 'Start bulk PDF import (admin only)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>GET</code></td>
					<td><code>/bulk/import/status</code></td>
					<td><?php esc_html_e( 'Get bulk import status (admin only)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>
		<?php endif; ?>

		<h3><?php esc_html_e( 'Query Parameters for /documents', 'pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Parameter', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Default', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>page</code></td>
					<td>1</td>
					<td><?php esc_html_e( 'Page number for pagination', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>per_page</code></td>
					<td>10</td>
					<td><?php esc_html_e( 'Items per page (max 100)', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>search</code></td>
					<td>-</td>
					<td><?php esc_html_e( 'Search term to filter documents', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>orderby</code></td>
					<td>date</td>
					<td><?php esc_html_e( 'Sort by: date, title, modified, views', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>order</code></td>
					<td>desc</td>
					<td><?php esc_html_e( 'Sort order: asc or desc', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example: List Documents', 'pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">curl -X GET "<?php echo esc_url( rest_url( 'pdf-embed-seo/v1/documents?per_page=5&orderby=views' ) ); ?>"</pre>

		<h3><?php esc_html_e( 'Example: Get Single Document', 'pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">curl -X GET "<?php echo esc_url( rest_url( 'pdf-embed-seo/v1/documents/123' ) ); ?>"</pre>

		<h3><?php esc_html_e( 'Example Response', 'pdf-embed-seo-optimize' ); ?></h3>
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

		<h3><?php esc_html_e( 'Authentication', 'pdf-embed-seo-optimize' ); ?></h3>
		<p><?php esc_html_e( 'Public endpoints require no authentication. Admin-only endpoints require authentication via:', 'pdf-embed-seo-optimize' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'Cookie authentication (when logged into WordPress)', 'pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'Application Passwords (WordPress 5.6+)', 'pdf-embed-seo-optimize' ); ?></li>
			<li><?php esc_html_e( 'OAuth / JWT via third-party plugins', 'pdf-embed-seo-optimize' ); ?></li>
		</ul>
	</div>

	<hr>

	<div class="pdf-embed-seo-optimize-docs-section" id="wordpress-hooks">
		<h2><?php esc_html_e( '10. WordPress Hooks', 'pdf-embed-seo-optimize' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-embed-seo-optimize' ); ?></strong>
			<?php esc_html_e( 'Developers can use these action and filter hooks to extend or customize the plugin functionality.', 'pdf-embed-seo-optimize' ); ?>
		</p>

		<h3><?php esc_html_e( 'Actions', 'pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Hook Name', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Parameters', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>pdf_embed_seo_pdf_viewed</code></td>
					<td><code>$post_id, $count</code></td>
					<td><?php esc_html_e( 'Fired when a PDF is viewed. Use for custom analytics.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_premium_init</code></td>
					<td>-</td>
					<td><?php esc_html_e( 'Fired when premium features are initialized.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_settings_saved</code></td>
					<td><code>$post_id, $settings</code></td>
					<td><?php esc_html_e( 'Fired when PDF settings are saved.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example: Track PDF Views', 'pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">add_action( 'pdf_embed_seo_pdf_viewed', function( $post_id, $count ) {
    // Send to your analytics service
    my_analytics_track( 'pdf_view', [
        'pdf_id' => $post_id,
        'title'  => get_the_title( $post_id ),
        'views'  => $count,
    ]);
}, 10, 2 );</pre>

		<h3><?php esc_html_e( 'Filters', 'pdf-embed-seo-optimize' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Hook Name', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Parameters', 'pdf-embed-seo-optimize' ); ?></th>
					<th><?php esc_html_e( 'Description', 'pdf-embed-seo-optimize' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>pdf_embed_seo_post_type_args</code></td>
					<td><code>$args</code></td>
					<td><?php esc_html_e( 'Modify the PDF document post type registration arguments.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_schema_data</code></td>
					<td><code>$schema, $post_id</code></td>
					<td><?php esc_html_e( 'Modify Schema.org data for a single PDF.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_archive_schema_data</code></td>
					<td><code>$schema</code></td>
					<td><?php esc_html_e( 'Modify Schema.org data for the archive page.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_archive_query</code></td>
					<td><code>$posts_per_page</code></td>
					<td><?php esc_html_e( 'Modify the archive page posts per page setting.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_sitemap_query_args</code></td>
					<td><code>$query_args, $atts</code></td>
					<td><?php esc_html_e( 'Modify the sitemap shortcode query arguments.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_archive_title</code></td>
					<td><code>$title</code></td>
					<td><?php esc_html_e( 'Modify the archive page title.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_archive_description</code></td>
					<td><code>$description</code></td>
					<td><?php esc_html_e( 'Modify the archive page description.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_viewer_options</code></td>
					<td><code>$options, $post_id</code></td>
					<td><?php esc_html_e( 'Modify PDF.js viewer options.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_allowed_types</code></td>
					<td><code>$types</code></td>
					<td><?php esc_html_e( 'Modify allowed MIME types for upload.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_rest_document</code></td>
					<td><code>$data, $post, $detailed</code></td>
					<td><?php esc_html_e( 'Modify REST API document response.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_rest_document_data</code></td>
					<td><code>$data, $post_id</code></td>
					<td><?php esc_html_e( 'Modify REST API document data response.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_rest_settings</code></td>
					<td><code>$settings</code></td>
					<td><?php esc_html_e( 'Modify REST API settings response.', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<?php if ( function_exists( 'pdf_embed_seo_is_premium' ) && pdf_embed_seo_is_premium() ) : ?>
				<tr>
					<td><code>pdf_embed_seo_password_error</code></td>
					<td><code>$error</code></td>
					<td><?php esc_html_e( 'Custom password validation error (Premium).', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_verify_password</code></td>
					<td><code>$is_valid, $post_id, $password</code></td>
					<td><?php esc_html_e( 'Override password verification (Premium).', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<tr>
					<td><code>pdf_embed_seo_rest_analytics</code></td>
					<td><code>$data, $period</code></td>
					<td><?php esc_html_e( 'Modify REST API analytics response (Premium).', 'pdf-embed-seo-optimize' ); ?></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example: Add Custom Schema Data', 'pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">add_filter( 'pdf_embed_seo_schema_data', function( $schema, $post_id ) {
    // Add custom author information
    $schema['author'] = [
        '@type' => 'Person',
        'name'  => get_post_meta( $post_id, '_pdf_author', true ),
    ];
    return $schema;
}, 10, 2 );</pre>

		<h3><?php esc_html_e( 'Example: Customize Archive Title', 'pdf-embed-seo-optimize' ); ?></h3>
		<pre class="pdf-embed-seo-optimize-code">add_filter( 'pdf_embed_seo_archive_title', function( $title ) {
    return 'Our Document Library';
});</pre>

		<h3><?php esc_html_e( 'Example: Add Custom Field to REST API', 'pdf-embed-seo-optimize' ); ?></h3>
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
				esc_html__( 'That\'s it! We hope this documentation helps you quickly set up and use our %s plugin. If you have any questions or run into issues, please refer to our support resources.', 'pdf-embed-seo-optimize' ),
				'<strong>PDF Embed & SEO Optimize</strong>'
			);
			?>
		</em>
	</p>

	<p class="pdf-embed-seo-optimize-docs-credit">
		<?php
		printf(
			/* translators: %s: Dross:Media link */
			esc_html__( 'made with %1$s by %2$s', 'pdf-embed-seo-optimize' ),
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
</style>

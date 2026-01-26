<?php
/**
 * Documentation page view.
 *
 * @package PDF_Viewer_2026
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap pdf-viewer-2026-docs">
	<h1><?php esc_html_e( 'PDF Viewer 2026: Docs & Usage', 'pdf-viewer-2026' ); ?></h1>

	<p class="pdf-viewer-2026-docs-intro">
		<?php esc_html_e( 'Welcome to the documentation for PDF Viewer 2026. Below you will find details on how to use the shortcodes included with this plugin and how to integrate them into your theme or custom templates for the best user experience.', 'pdf-viewer-2026' ); ?>
	</p>

	<div class="pdf-viewer-2026-docs-toc">
		<h2><?php esc_html_e( 'Table of Contents', 'pdf-viewer-2026' ); ?></h2>
		<ol>
			<li><a href="#pdf-viewer-sitemap"><?php esc_html_e( '[pdf_viewer_sitemap]', 'pdf-viewer-2026' ); ?></a></li>
			<li><a href="#pdf-viewer"><?php esc_html_e( '[pdf_viewer]', 'pdf-viewer-2026' ); ?></a></li>
			<li><a href="#custom-templates"><?php esc_html_e( 'Using [pdf_viewer] in Custom Template Files', 'pdf-viewer-2026' ); ?></a></li>
			<li><a href="#url-structure"><?php esc_html_e( 'URL Structure', 'pdf-viewer-2026' ); ?></a></li>
			<li><a href="#yoast-seo"><?php esc_html_e( 'Yoast SEO Integration', 'pdf-viewer-2026' ); ?></a></li>
		</ol>
	</div>

	<hr>

	<div class="pdf-viewer-2026-docs-section" id="pdf-viewer-sitemap">
		<h2><?php esc_html_e( '1. [pdf_viewer_sitemap]', 'pdf-viewer-2026' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-viewer-2026' ); ?></strong>
			<?php esc_html_e( 'This shortcode displays a simple HTML sitemap of all published PDF Viewer posts. You can insert it into any page or post to list all your PDF Viewer entries in an unordered list, sorted alphabetically.', 'pdf-viewer-2026' ); ?>
		</p>

		<h3><?php esc_html_e( 'Example Usage:', 'pdf-viewer-2026' ); ?></h3>
		<pre class="pdf-viewer-2026-code">[pdf_viewer_sitemap]</pre>

		<p>
			<?php esc_html_e( 'When placed on a page, this will produce a linked list of every PDF Viewer custom post, allowing visitors to easily view and access all of your PDF documents.', 'pdf-viewer-2026' ); ?>
		</p>

		<h3><?php esc_html_e( 'Optional Attributes:', 'pdf-viewer-2026' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Attribute', 'pdf-viewer-2026' ); ?></th>
					<th><?php esc_html_e( 'Default', 'pdf-viewer-2026' ); ?></th>
					<th><?php esc_html_e( 'Description', 'pdf-viewer-2026' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>orderby</code></td>
					<td>title</td>
					<td><?php esc_html_e( 'Sort by: title, date, menu_order', 'pdf-viewer-2026' ); ?></td>
				</tr>
				<tr>
					<td><code>order</code></td>
					<td>ASC</td>
					<td><?php esc_html_e( 'Sort order: ASC or DESC', 'pdf-viewer-2026' ); ?></td>
				</tr>
				<tr>
					<td><code>limit</code></td>
					<td>-1</td>
					<td><?php esc_html_e( 'Number of PDFs to show (-1 for all)', 'pdf-viewer-2026' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example with Attributes:', 'pdf-viewer-2026' ); ?></h3>
		<pre class="pdf-viewer-2026-code">[pdf_viewer_sitemap orderby="date" order="DESC" limit="10"]</pre>
	</div>

	<hr>

	<div class="pdf-viewer-2026-docs-section" id="pdf-viewer">
		<h2><?php esc_html_e( '2. [pdf_viewer]', 'pdf-viewer-2026' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-viewer-2026' ); ?></strong>
			<?php esc_html_e( 'This shortcode embeds a single PDF into your content. When used on a single PDF Viewer post, it automatically outputs the PDF for that post.', 'pdf-viewer-2026' ); ?>
		</p>

		<h3><?php esc_html_e( 'Basic Example:', 'pdf-viewer-2026' ); ?></h3>
		<pre class="pdf-viewer-2026-code">[pdf_viewer]</pre>

		<p>
			<?php esc_html_e( 'When you insert this shortcode on a PDF Viewer post, it will dynamically use the current post\'s data to embed the PDF.', 'pdf-viewer-2026' ); ?>
		</p>

		<h3><?php esc_html_e( 'Embedding a Specific PDF:', 'pdf-viewer-2026' ); ?></h3>
		<p>
			<?php esc_html_e( 'The shortcode supports specifying a particular PDF by providing its custom post ID as an attribute. For example:', 'pdf-viewer-2026' ); ?>
		</p>
		<pre class="pdf-viewer-2026-code">[pdf_viewer id="384"]</pre>

		<h3><?php esc_html_e( 'Optional Attributes:', 'pdf-viewer-2026' ); ?></h3>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Attribute', 'pdf-viewer-2026' ); ?></th>
					<th><?php esc_html_e( 'Default', 'pdf-viewer-2026' ); ?></th>
					<th><?php esc_html_e( 'Description', 'pdf-viewer-2026' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>id</code></td>
					<td><?php esc_html_e( 'Current post', 'pdf-viewer-2026' ); ?></td>
					<td><?php esc_html_e( 'The PDF document post ID', 'pdf-viewer-2026' ); ?></td>
				</tr>
				<tr>
					<td><code>width</code></td>
					<td>100%</td>
					<td><?php esc_html_e( 'Width of the viewer', 'pdf-viewer-2026' ); ?></td>
				</tr>
				<tr>
					<td><code>height</code></td>
					<td>800px</td>
					<td><?php esc_html_e( 'Height of the viewer', 'pdf-viewer-2026' ); ?></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Example with Dimensions:', 'pdf-viewer-2026' ); ?></h3>
		<pre class="pdf-viewer-2026-code">[pdf_viewer id="384" width="100%" height="600px"]</pre>
	</div>

	<hr>

	<div class="pdf-viewer-2026-docs-section" id="custom-templates">
		<h2><?php esc_html_e( '3. Using [pdf_viewer] in Custom Template Files', 'pdf-viewer-2026' ); ?></h2>

		<p>
			<strong><?php esc_html_e( 'Purpose:', 'pdf-viewer-2026' ); ?></strong>
			<?php esc_html_e( 'Whether you\'re using a page builder (like Elementor, Divi, Beaver Builder, etc.) or you\'ve created a custom template file for your PDF Viewer custom post type, you can insert the shortcode so it automatically displays the PDF of the current post.', 'pdf-viewer-2026' ); ?>
		</p>

		<h3><?php esc_html_e( 'Option A: Using a Shortcode or HTML Module in Your Page Builder', 'pdf-viewer-2026' ); ?></h3>
		<p>
			<?php esc_html_e( 'Most page builders offer a widget or module where you can insert custom shortcodes. Simply add a Text or Shortcode module and insert:', 'pdf-viewer-2026' ); ?>
		</p>
		<pre class="pdf-viewer-2026-code">[pdf_viewer]</pre>
		<p>
			<?php esc_html_e( 'This will automatically output the embedded PDF on the PDF Viewer post.', 'pdf-viewer-2026' ); ?>
		</p>

		<h3><?php esc_html_e( 'Option B: Embedding the Shortcode in a Custom Template File', 'pdf-viewer-2026' ); ?></h3>
		<p>
			<?php esc_html_e( 'If you\'re creating a custom template file (for example, in your theme or via a page builder\'s custom code module), you can embed the shortcode using PHP. For example, in your custom template file for the PDF Viewer custom post type:', 'pdf-viewer-2026' ); ?>
		</p>
		<pre class="pdf-viewer-2026-code">&lt;?php
if ( function_exists( 'do_shortcode' ) ) {
    // Automatically uses the current post's PDF data.
    echo do_shortcode( '[pdf_viewer]' );
}
?&gt;</pre>
		<p>
			<?php esc_html_e( 'This PHP snippet dynamically outputs the embedded PDF for the current PDF Viewer post without the need for specifying an ID.', 'pdf-viewer-2026' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'Both options allow you to integrate the shortcode seamlessly into your custom layouts.', 'pdf-viewer-2026' ); ?>
		</p>
	</div>

	<hr>

	<div class="pdf-viewer-2026-docs-section" id="url-structure">
		<h2><?php esc_html_e( '4. URL Structure', 'pdf-viewer-2026' ); ?></h2>

		<p><?php esc_html_e( 'PDF Viewer 2026 uses clean, SEO-friendly URLs:', 'pdf-viewer-2026' ); ?></p>

		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Page Type', 'pdf-viewer-2026' ); ?></th>
					<th><?php esc_html_e( 'URL Structure', 'pdf-viewer-2026' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'PDF Archive (all PDFs)', 'pdf-viewer-2026' ); ?></td>
					<td><code><?php echo esc_html( home_url( '/pdf/' ) ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Single PDF Document', 'pdf-viewer-2026' ); ?></td>
					<td><code><?php echo esc_html( home_url( '/pdf/your-pdf-slug/' ) ); ?></code></td>
				</tr>
			</tbody>
		</table>

		<p>
			<strong><?php esc_html_e( 'Note:', 'pdf-viewer-2026' ); ?></strong>
			<?php esc_html_e( 'The PDF slug can be customized via Yoast SEO or the standard WordPress slug editor. This allows you to create descriptive, SEO-friendly URLs for each document.', 'pdf-viewer-2026' ); ?>
		</p>
	</div>

	<hr>

	<div class="pdf-viewer-2026-docs-section" id="yoast-seo">
		<h2><?php esc_html_e( '5. Yoast SEO Integration', 'pdf-viewer-2026' ); ?></h2>

		<?php if ( defined( 'WPSEO_VERSION' ) ) : ?>
			<div class="notice notice-success inline" style="padding: 10px; margin: 10px 0;">
				<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
				<strong><?php esc_html_e( 'Yoast SEO is active!', 'pdf-viewer-2026' ); ?></strong>
			</div>
		<?php else : ?>
			<div class="notice notice-warning inline" style="padding: 10px; margin: 10px 0;">
				<span class="dashicons dashicons-warning" style="color: #ffb900;"></span>
				<?php esc_html_e( 'Yoast SEO is not currently active. Install and activate it to unlock full SEO control.', 'pdf-viewer-2026' ); ?>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'When Yoast SEO is active, you can configure the following for each PDF document:', 'pdf-viewer-2026' ); ?></p>

		<ul class="pdf-viewer-2026-feature-list">
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'SEO Title - Custom page title for search results', 'pdf-viewer-2026' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Meta Description - Custom description for search results', 'pdf-viewer-2026' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'URL Slug - Clean, descriptive URL for the PDF', 'pdf-viewer-2026' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Featured Image - Thumbnail for social sharing and archive pages', 'pdf-viewer-2026' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'OpenGraph Tags - Control how PDF appears on Facebook/LinkedIn', 'pdf-viewer-2026' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Twitter Cards - Control how PDF appears on Twitter/X', 'pdf-viewer-2026' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Schema Markup - DigitalDocument schema for rich search results', 'pdf-viewer-2026' ); ?></li>
			<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Sitemap Inclusion - PDFs automatically added to XML sitemap', 'pdf-viewer-2026' ); ?></li>
		</ul>
	</div>

	<hr>

	<p class="pdf-viewer-2026-docs-footer">
		<em>
			<?php
			printf(
				/* translators: %s: Plugin name */
				esc_html__( 'That\'s it! We hope this documentation helps you quickly set up and use our %s plugin. If you have any questions or run into issues, please refer to our support resources.', 'pdf-viewer-2026' ),
				'<strong>PDF Viewer 2026</strong>'
			);
			?>
		</em>
	</p>
</div>

<style>
.pdf-viewer-2026-docs {
	max-width: 900px;
}
.pdf-viewer-2026-docs-intro {
	font-size: 14px;
	line-height: 1.6;
}
.pdf-viewer-2026-docs-toc {
	background: #f9f9f9;
	padding: 15px 20px;
	border-left: 4px solid #0073aa;
	margin: 20px 0;
}
.pdf-viewer-2026-docs-toc h2 {
	margin-top: 0;
}
.pdf-viewer-2026-docs-section {
	margin: 30px 0;
}
.pdf-viewer-2026-code {
	background: #f1f1f1;
	padding: 10px 15px;
	border-left: 3px solid #ccc;
	font-family: monospace;
	overflow-x: auto;
	white-space: pre-wrap;
}
.pdf-viewer-2026-feature-list {
	list-style: none;
	padding: 0;
}
.pdf-viewer-2026-feature-list li {
	padding: 5px 0;
}
.pdf-viewer-2026-feature-list .dashicons-yes {
	color: #46b450;
}
.pdf-viewer-2026-docs-footer {
	background: #fef7e5;
	padding: 15px;
	border-left: 4px solid #ffb900;
	margin-top: 30px;
}
</style>

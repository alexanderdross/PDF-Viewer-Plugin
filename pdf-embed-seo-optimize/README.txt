=== PDF Embed & SEO Optimize (Free Version) ===
Contributors: drossmedia
Tags: pdf, pdf viewer, seo, embed, document
Requires at least: 5.8
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.2.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A powerful WordPress plugin that integrates Mozilla's PDF.js viewer to serve PDFs through clean URLs with full SEO optimization.

== Description ==

**PDF Embed & SEO Optimize** is a powerful WordPress plugin that transforms how you display PDF documents on your website. Instead of linking directly to PDF files (exposing URLs like `yoursite.com/uploads/document.pdf`), this plugin renders PDFs through a custom viewer with clean, SEO-friendly URLs like `yoursite.com/pdf/document-name/`.

= Key Features =

* **Clean URL Structure** - No more `.pdf` extensions in URLs. Each PDF gets its own SEO-optimized page.
* **Mozilla PDF.js Integration** - Industry-standard PDF rendering right in the browser (bundled locally, no external dependencies).
* **Gutenberg Block** - Native block editor support for embedding PDFs in posts and pages.
* **Auto-Generate Thumbnails** - Automatically create thumbnails from PDF first pages using ImageMagick or Ghostscript.
* **Yoast SEO Compatible** - Full control over SEO title, meta description, slug, OpenGraph tags, and Twitter Cards.
* **Print/Download Control** - Allow or restrict printing and downloading on a per-PDF basis.
* **PDF Archive Page** - Automatically generate an archive page listing all your PDFs at `/pdf/`.
* **View Statistics** - Track how many times each PDF has been viewed.
* **Responsive Design** - Works beautifully on desktop, tablet, and mobile devices.
* **Shortcode Support** - Embed PDF viewers anywhere using `[pdf_viewer]` shortcode.
* **Schema Markup** - Automatic DigitalDocument schema for rich search results.

= URL Structure =

* **Archive Page:** `yoursite.com/pdf/` - Lists all published PDF documents
* **Single PDF:** `yoursite.com/pdf/your-document-slug/` - Individual PDF viewer page

= Gutenberg Block =

The easiest way to embed PDFs in the block editor:

1. Add a new block and search for "PDF Viewer"
2. Select your PDF document from the dropdown
3. Adjust width and height in the block settings

= Shortcodes =

**Display a specific PDF:**
`[pdf_viewer id="123"]`

**Display PDF on its own post:**
`[pdf_viewer]`

**List all PDFs:**
`[pdf_viewer_sitemap]`

= Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* Yoast SEO (optional, for advanced SEO features)

== Installation ==

1. Upload the `pdf-embed-seo-optimize` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'PDF Documents' in the admin menu to start adding PDFs
4. Configure settings under 'PDF Documents' > 'Settings'

== Frequently Asked Questions ==

= How do I add a new PDF? =

Go to PDF Documents > Add New in your WordPress admin. Enter a title, upload your PDF file, configure the settings, and publish.

= Can I control whether users can download or print PDFs? =

Yes! Each PDF document has individual settings for allowing or disallowing downloads and printing. You can also set default permissions in the plugin settings.

= Does this work with Yoast SEO? =

Absolutely! When Yoast SEO is active, you can configure the SEO title, meta description, slug, OpenGraph tags, and Twitter Cards for each PDF document.

= Can I embed a PDF viewer in a regular post or page? =

Yes, use the `[pdf_viewer id="123"]` shortcode, replacing 123 with your PDF document's ID.

= Are the original PDF URLs hidden? =

The direct PDF URLs are not exposed in the page source. The viewer fetches PDF data via AJAX, making it harder for users to discover the direct download link (especially when downloads are disabled).

= Can I customize the viewer appearance? =

Yes, you can choose between light and dark themes in the plugin settings. You can also override the templates in your theme.

== Screenshots ==

1. PDF Documents list in WordPress admin
2. Single PDF document edit screen with meta boxes
3. Frontend PDF viewer with toolbar
4. PDF archive page showing all documents
5. Yoast SEO integration for meta tags
6. Plugin settings page

== Changelog ==

= 1.2.4 =
* Premium: AI & Schema Optimization meta box for GEO/AEO/LLM optimization
* Premium: AI Summary, FAQ Schema, Table of Contents, Reading Time, and more
* Free: AI Optimization preview meta box with premium upgrade CTA
* Free: Premium settings preview on settings page
* Fixed: Schema validation with proper WebPage/DigitalDocument separation

= 1.2.3 =
* GEO/AEO/LLM schema optimization (SpeakableSpecification, potentialAction, accessMode)
* Standalone Open Graph and Twitter Card meta tags (when Yoast is not active)
* Enhanced DigitalDocument schema (identifier, fileFormat, inLanguage, publisher)
* Plugin Check compliance fixes

= 1.2.2 =
* Archive display options (list/grid views)
* Schema.org BreadcrumbList markup
* Visible breadcrumb navigation with accessibility support

= 1.2.1 =
* Version bump for release
* Documentation improvements

= 1.2.0 =
* Added REST API endpoints
* Added reading progress tracking (Premium)
* Added password verification endpoint (Premium)
* Added XML Sitemap at /pdf/sitemap.xml (Premium)
* Added comprehensive developer documentation

= 1.1.0 =
* Added comprehensive UAT/QA test documentation
* Added Dross:Media credit links with accessibility attributes to admin pages
* Made search engine ping optional for PDF sitemap (disabled by default for privacy)
* Security: All external requests are now opt-in
* Improved accessibility across admin interface

= 1.0.0 =
* Initial release
* Custom post type for PDF documents
* Mozilla PDF.js viewer integration (bundled locally - no external CDN)
* Gutenberg block for embedding PDFs in the block editor
* Auto-generate thumbnails from PDF first pages (requires ImageMagick or Ghostscript)
* Yoast SEO compatibility
* Print/download permission controls
* View statistics tracking
* Shortcode support
* Archive page with grid layout
* Light and dark viewer themes
* Schema markup for rich results (DigitalDocument and CollectionPage)

== Upgrade Notice ==

= 1.1.0 =
Improved privacy and accessibility. Search engine pings are now opt-in. Added UAT documentation.

= 1.0.0 =
Initial release of PDF Embed & SEO Optimize.

== Privacy Policy ==

This plugin does not collect any personal data. It stores:
* PDF view counts (anonymous)
* Plugin settings

**No external services are used.** The PDF.js library is bundled locally with the plugin - no data is sent to any external servers.

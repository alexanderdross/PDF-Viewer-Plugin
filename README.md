# PDF Embed & SEO Optimize

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize" width="128">
</p>

<p align="center">
  <strong>A powerful PDF management solution for WordPress, Drupal, and React/Next.js</strong><br>
  Display PDFs with Mozilla's PDF.js viewer, SEO optimization, and full control over print/download permissions.
</p>

<p align="center">
  <a href="https://pdfviewer.drossmedia.de">Website</a> •
  <a href="https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/DOCUMENTATION.md">Documentation</a> •
  <a href="https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/PRO.md">Get Premium</a> •
  <a href="https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/FEATURES.md">Features</a>
</p>

**Current Version:** 1.2.11 | **Platforms:** WordPress, Drupal, React/Next.js

---

A powerful WordPress plugin that integrates Mozilla's PDF.js viewer to serve PDFs through a viewer URL, enhancing SEO with Schema Data, Open Graph Tags, Twitter Cards, and other Meta Tags.

## Features

- **Clean URL Structure** - Display PDFs at `/pdf/document-name/` instead of exposing `.pdf` file URLs
- **Mozilla PDF.js Integration** - Industry-standard PDF rendering in the browser (bundled locally - no external CDN)
- **Gutenberg Block** - Native block editor support for embedding PDFs in posts and pages
- **Auto-Generate Thumbnails** - Automatically create thumbnails from PDF first pages using ImageMagick or Ghostscript
- **Yoast SEO Compatible** - Full control over SEO title, meta description, slug, OpenGraph tags, and Twitter Cards
- **Print/Download Control** - Allow or restrict printing and downloading on a per-PDF basis
- **PDF Archive Page** - Automatically generated archive page at `/pdf/` listing all PDFs
- **View Statistics** - Track how many times each PDF has been viewed
- **Responsive Design** - Works on desktop, tablet, and mobile devices
- **Shortcode Support** - Embed PDF viewers anywhere using `[pdf_viewer]` shortcode
- **Schema Markup** - Automatic DigitalDocument schema for rich search results

## Installation

1. Download or clone this repository
2. Copy the `pdf-embed-seo-optimize` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to 'PDF Documents' in the admin menu to start adding PDFs

## Usage

### Adding a PDF Document

1. Navigate to **PDF Documents > Add New** in WordPress admin
2. Enter a title for your PDF
3. Upload your PDF file using the "Select PDF File" button
4. Configure print/download permissions
5. Set SEO settings via Yoast SEO meta box (if installed)
6. Publish

### Gutenberg Block (Recommended)

1. In the block editor, click "+" to add a new block
2. Search for "PDF Viewer" or find it under "Embed"
3. Select your PDF document from the dropdown
4. Adjust width and height in the block settings panel

### Shortcodes

```
[pdf_viewer]              - Display current PDF (on PDF document pages)
[pdf_viewer id="123"]     - Display specific PDF by ID
[pdf_viewer_sitemap]      - List all published PDFs
```

### URL Structure

| URL | Description |
|-----|-------------|
| `/pdf/` | Archive page showing all PDF documents |
| `/pdf/your-document-slug/` | Individual PDF viewer page |

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- Yoast SEO (optional, for advanced SEO features)

## External Services

**None.** All resources are bundled locally with the plugin.

- **PDF.js** - Mozilla's PDF rendering library (bundled locally)
  - Used for: Rendering PDF documents in the browser
  - Privacy: No external requests are made; all assets served from your domain

## Screenshots

### Admin - PDF Documents List
Dedicated admin section for managing PDF documents with custom columns for file info, permissions, and view counts.

### Admin - Edit PDF Document
Upload PDFs, configure permissions, and use Yoast SEO for full SEO control.

### Frontend - PDF Viewer
Clean, responsive PDF viewer with navigation, zoom, and optional print/download buttons.

## Development

### File Structure

```
pdf-embed-seo-optimize/
├── pdf-embed-seo-optimize.php           # Main plugin file
├── uninstall.php                  # Cleanup on uninstall
├── README.txt                     # WordPress.org readme
├── includes/
│   ├── class-pdf-embed-seo-optimize-post-type.php
│   ├── class-pdf-embed-seo-optimize-admin.php
│   ├── class-pdf-embed-seo-optimize-frontend.php
│   ├── class-pdf-embed-seo-optimize-shortcodes.php
│   ├── class-pdf-embed-seo-optimize-yoast.php
│   ├── class-pdf-embed-seo-optimize-block.php        # Gutenberg block
│   └── class-pdf-embed-seo-optimize-thumbnail.php    # Thumbnail generator
├── admin/
│   ├── css/admin-styles.css
│   ├── js/admin-scripts.js
│   └── views/
├── assets/
│   └── pdfjs/                     # Bundled PDF.js library
│       ├── pdf.min.js
│       └── pdf.worker.min.js
├── blocks/
│   └── pdf-viewer/                # Gutenberg block assets
│       ├── editor.js
│       └── editor.css
├── public/
│   ├── css/viewer-styles.css
│   ├── js/viewer-scripts.js
│   └── views/
└── languages/
```

### WordPress Coding Standards

This plugin follows [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/):

- Proper escaping with `esc_html()`, `esc_attr()`, `esc_url()`
- Nonce verification for all form submissions
- Capability checks for admin functions
- Prepared statements for database queries
- Internationalization ready with text domain `pdf-embed-seo-optimize`

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Author

**Dross:Media** - [GitHub](https://github.com/alexanderdross)

## Premium Features

Upgrade to Premium for advanced features:

- **Analytics Dashboard** - Detailed view/download statistics
- **Download Tracking** - Track downloads separately from views
- **Password Protection** - Secure PDFs with passwords
- **Reading Progress** - Resume where users left off
- **Expiring Access Links** - Time-limited URLs for PDFs
- **Role-Based Access** - Restrict by user role
- **AI Schema Optimization** - GEO/AEO/LLM optimization
- **XML Sitemap** - Dedicated PDF sitemap
- **Categories & Tags** - Organize PDFs
- **Bulk Import** - Import multiple PDFs from CSV
- **REST API** - 14+ premium endpoints

**Get Premium:** [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de)

## Changelog

### 1.2.11 (Current)
- **Drupal Code Review Fixes** - CSRF protection, rate limiting, session cache context
- **Media Library Integration** - PDFs managed via Drupal's Media Library
- **New Services** - Rate limiter and access token storage with database backend
- **Database Migration** - Scalable token storage with automatic cleanup

### 1.2.10
- **iOS Print Support** - Enhanced print functionality for Safari/iOS
- **Comprehensive Print CSS** - Professional print output across all platforms

### 1.2.9
- **Drupal Performance** - Removed entity saves during page views
- **GDPR Compliance** - IP anonymization setting (enabled by default)
- **Cache Improvements** - Proper cache tag invalidation

See [CHANGELOG.md](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/CHANGELOG.md) for complete version history.

### Platform-Specific Changelogs
- [WordPress Changelog](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/CHANGELOG-WORDPRESS.md)
- [Drupal Changelog](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/CHANGELOG-DRUPAL.md)
- [React/Next.js Changelog](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/CHANGELOG-REACT.md)

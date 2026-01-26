# PDF Embed & SEO Optimize

A powerful WordPress plugin that integrates Mozilla's PDF.js viewer to serve PDFs through a viewer URL, enhancing SEO with Schema Data, Open Graph Tags, Twitter Cards, and other Meta Tags.

## Features

- **Clean URL Structure** - Display PDFs at `/pdf/document-name/` instead of exposing `.pdf` file URLs
- **Mozilla PDF.js Integration** - Industry-standard PDF rendering in the browser
- **Yoast SEO Compatible** - Full control over SEO title, meta description, slug, OpenGraph tags, and Twitter Cards
- **Print/Download Control** - Allow or restrict printing and downloading on a per-PDF basis
- **PDF Archive Page** - Automatically generated archive page at `/pdf/` listing all PDFs
- **View Statistics** - Track how many times each PDF has been viewed
- **Responsive Design** - Works on desktop, tablet, and mobile devices
- **Shortcode Support** - Embed PDF viewers anywhere using `[pdf_viewer]` shortcode
- **Schema Markup** - Automatic DigitalDocument schema for rich search results

## Installation

1. Download or clone this repository
2. Copy the `pdf-viewer-2026` folder to `/wp-content/plugins/`
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

This plugin uses the following external service:

- **PDF.js** from [CDNJS](https://cdnjs.cloudflare.com/) - Mozilla's PDF rendering library
  - Used for: Rendering PDF documents in the browser
  - Privacy: No user data is sent to CDNJS

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
pdf-viewer-2026/
├── pdf-viewer-2026.php           # Main plugin file
├── uninstall.php                  # Cleanup on uninstall
├── README.txt                     # WordPress.org readme
├── includes/
│   ├── class-pdf-viewer-2026-post-type.php
│   ├── class-pdf-viewer-2026-admin.php
│   ├── class-pdf-viewer-2026-frontend.php
│   ├── class-pdf-viewer-2026-shortcodes.php
│   └── class-pdf-viewer-2026-yoast.php
├── admin/
│   ├── css/admin-styles.css
│   ├── js/admin-scripts.js
│   └── views/
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
- Internationalization ready with text domain `pdf-viewer-2026`

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Author

**Dross:Media** - [GitHub](https://github.com/alexanderdross)

## Changelog

### 1.0.0
- Initial release
- Custom post type for PDF documents
- Mozilla PDF.js viewer integration
- Yoast SEO compatibility
- Print/download permission controls
- View statistics tracking
- Shortcode support

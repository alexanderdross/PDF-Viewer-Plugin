# PDF Viewer 2026 - WordPress Plugin

A WordPress plugin that uses Mozilla's PDF.js library to securely display PDFs without exposing direct file URLs.

## Project Overview

This plugin provides a secure, SEO-friendly way to display PDF documents on WordPress sites. Instead of linking directly to PDF files (which exposes the `.pdf` URL), this plugin renders PDFs through a custom viewer with clean, SEO-optimized URLs.

### Key Features

- **Secure PDF Display**: PDFs are rendered through PDF.js, hiding original file URLs
- **Clean URL Structure**: URLs like `www.domain.com/pdf/my-document/` instead of `www.domain.com/uploads/file.pdf`
- **PDF Archive Page**: Overview page at `/pdf/` listing all published PDFs
- **Yoast SEO Integration**: Full control over slugs, titles, meta tags, OG tags, and Twitter cards
- **Admin Controls**: Per-PDF settings for print/download permissions
- **WordPress Guidelines Compliant**: Built following official WordPress plugin development standards

---

## Architecture

### Custom Post Type: `pdf_document`

The plugin registers a custom post type to manage PDF documents:

```
Post Type: pdf_document
Slug: /pdf/
Supports: title, editor, thumbnail, excerpt, custom-fields
Rewrite: /pdf/%postname%/
```

### Data Structure

Each PDF document stores:
- **Post Title**: Display title for the PDF
- **Post Content**: Optional description
- **Featured Image**: Thumbnail for archive pages
- **Meta Fields**:
  - `_pdf_file_id` - Attachment ID of the uploaded PDF
  - `_pdf_file_url` - Direct URL to the PDF (used internally only)
  - `_pdf_allow_download` - Boolean: allow download button
  - `_pdf_allow_print` - Boolean: allow print functionality
  - `_pdf_view_count` - Integer: track view statistics

### URL Structure

| URL | Description |
|-----|-------------|
| `/pdf/` | Archive page showing all PDFs |
| `/pdf/document-slug/` | Individual PDF viewer page |

---

## Technical Implementation

### 1. PDF.js Integration

The plugin uses Mozilla's PDF.js library (https://mozilla.github.io/pdf.js/):

- **Version**: Latest stable release via CDN or bundled
- **Viewer**: Custom viewer template with WordPress integration
- **Features**: Page navigation, zoom, search, optional print/download

### 2. Security Measures

- PDF files stored in protected directory or with restricted direct access
- Nonce verification for AJAX requests
- Capability checks for admin functions
- Sanitization/escaping of all inputs/outputs

### 3. Yoast SEO Integration

The plugin integrates with Yoast SEO through:

- Custom post type registration with `public => true` for SEO support
- Proper `<head>` output allowing Yoast to inject meta tags
- Support for Yoast's Schema.org integration
- OpenGraph and Twitter Card meta tag support

### 4. Admin Interface

**PDF Document Edit Screen:**
- File upload/selection via Media Library
- Print permission toggle
- Download permission toggle
- View statistics display
- Preview link

**Settings Page:**
- Default print/download permissions
- PDF.js viewer customization options
- URL slug configuration
- Archive page settings

---

## File Structure

```
pdf-viewer-2026/
├── pdf-viewer-2026.php              # Main plugin file
├── uninstall.php                     # Cleanup on uninstall
├── README.txt                        # WordPress.org readme
├── CLAUDE.md                         # Project documentation
├── LICENSE                           # GPL v2 or later
│
├── includes/
│   ├── class-pdf-viewer-2026.php           # Main plugin class
│   ├── class-pdf-viewer-post-type.php      # Custom post type registration
│   ├── class-pdf-viewer-admin.php          # Admin functionality
│   ├── class-pdf-viewer-frontend.php       # Frontend rendering
│   ├── class-pdf-viewer-ajax.php           # AJAX handlers
│   └── class-pdf-viewer-yoast.php          # Yoast SEO integration
│
├── admin/
│   ├── css/
│   │   └── admin-styles.css                # Admin stylesheet
│   ├── js/
│   │   └── admin-scripts.js                # Admin JavaScript
│   └── views/
│       ├── meta-box-pdf-settings.php       # PDF settings meta box
│       └── settings-page.php               # Plugin settings page
│
├── public/
│   ├── css/
│   │   └── viewer-styles.css               # PDF viewer stylesheet
│   ├── js/
│   │   └── viewer-scripts.js               # PDF viewer JavaScript
│   └── views/
│       ├── single-pdf-document.php         # Single PDF template
│       └── archive-pdf-document.php        # Archive template
│
├── assets/
│   └── pdfjs/                              # PDF.js library files
│       ├── pdf.min.js
│       ├── pdf.worker.min.js
│       └── web/
│           └── viewer.html
│
└── languages/
    └── pdf-viewer-2026.pot                 # Translation template
```

---

## WordPress Guidelines Compliance

### Coding Standards

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- Use WordPress coding style for PHP, JS, and CSS
- Prefix all functions, classes, and global variables with `pdf_viewer_2026_`
- Use proper escaping functions: `esc_html()`, `esc_attr()`, `esc_url()`
- Use `wp_nonce_field()` and `wp_verify_nonce()` for form security

### Security Best Practices

- Validate and sanitize all user inputs
- Use prepared statements for database queries
- Check user capabilities before performing actions
- Escape all output

### Performance

- Enqueue scripts/styles only when needed
- Use WordPress caching APIs
- Minimize database queries
- Lazy load PDF.js only on viewer pages

### Internationalization

- All strings wrapped in translation functions: `__()`, `_e()`, `esc_html__()`
- Text domain: `pdf-viewer-2026`
- POT file for translations

---

## Development Phases

### Phase 1: Core Infrastructure
- [ ] Create main plugin file with proper headers
- [ ] Register custom post type `pdf_document`
- [ ] Set up basic file structure
- [ ] Create activation/deactivation hooks

### Phase 2: Admin Interface
- [ ] Create meta boxes for PDF settings
- [ ] Implement Media Library integration for PDF upload
- [ ] Add print/download permission toggles
- [ ] Create plugin settings page

### Phase 3: PDF.js Integration
- [ ] Include PDF.js library
- [ ] Create viewer template
- [ ] Implement security measures (disable context menu, etc.)
- [ ] Add print/download controls based on permissions

### Phase 4: Frontend Display
- [ ] Create single PDF document template
- [ ] Create archive template
- [ ] Style the viewer for responsive display
- [ ] Add page navigation controls

### Phase 5: Yoast SEO Integration
- [ ] Ensure proper meta tag support
- [ ] Test OpenGraph integration
- [ ] Test Twitter Cards
- [ ] Verify sitemap inclusion

### Phase 6: Security & Polish
- [ ] Security audit
- [ ] Performance optimization
- [ ] Accessibility improvements
- [ ] Documentation

---

## Hooks and Filters

### Actions

```php
// Fired when a PDF is viewed
do_action( 'pdf_viewer_2026_pdf_viewed', $post_id );

// Fired when PDF settings are saved
do_action( 'pdf_viewer_2026_settings_saved', $post_id, $settings );
```

### Filters

```php
// Modify PDF.js viewer options
apply_filters( 'pdf_viewer_2026_viewer_options', $options, $post_id );

// Modify allowed file types
apply_filters( 'pdf_viewer_2026_allowed_types', array( 'application/pdf' ) );

// Customize archive query
apply_filters( 'pdf_viewer_2026_archive_query', $query_args );
```

---

## Database

### Custom Tables

None required - uses WordPress post meta for all data storage.

### Options

```
pdf_viewer_2026_settings - Serialized array of plugin settings
pdf_viewer_2026_version - Current plugin version for upgrades
```

---

## Dependencies

- WordPress 5.8+
- PHP 7.4+
- Mozilla PDF.js (included)
- Optional: Yoast SEO for enhanced SEO features

---

## Testing Checklist

- [ ] PDF upload and display works correctly
- [ ] URLs are clean and SEO-friendly
- [ ] Print/download restrictions work
- [ ] Yoast SEO meta boxes appear
- [ ] Archive page displays all PDFs
- [ ] Mobile responsive design
- [ ] Security: direct PDF access is restricted
- [ ] Performance: lazy loading works
- [ ] i18n: all strings are translatable

---

## Changelog

### 1.0.0 (In Development)
- Initial release
- Custom post type for PDFs
- PDF.js viewer integration
- Yoast SEO compatibility
- Print/download controls

---

## Contributing

This plugin follows WordPress coding standards. Before contributing:

1. Run PHP CodeSniffer with WordPress standards
2. Test on multiple WordPress versions
3. Ensure no PHP warnings/notices
4. Update translation files if strings changed

---

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

This is free software, and you are welcome to redistribute it under certain conditions.

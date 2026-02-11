# PDF Embed & SEO Optimize for WordPress

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize for WordPress" width="100">
</p>

<p align="center">
  <strong>The most powerful PDF viewer plugin for WordPress</strong><br>
  SEO-optimized, secure, and feature-rich PDF management.
</p>

---

**Current Version:** 1.2.11
**Requires:** WordPress 5.8+ | PHP 7.4+
**License:** GPL v2 (Free), Commercial (Premium)

---

## Quick Start

### Installation

**Free Version:**
```bash
# Via WP-CLI
wp plugin install pdf-embed-seo-optimize --activate

# Or via WordPress Admin
# Plugins > Add New > Search "PDF Embed SEO Optimize"
```

**Premium Version:**
1. Purchase from [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de)
2. Download the premium ZIP
3. Plugins > Add New > Upload Plugin
4. Activate and enter your license key

### Create Your First PDF Document

1. Go to **PDF Documents > Add New**
2. Enter a title
3. Upload your PDF in the "PDF File" meta box
4. Configure print/download settings
5. Publish

Your PDF is now available at `/pdf/your-document-slug/`

---

## Features

### Viewer & Display

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Mozilla PDF.js Viewer (v4.0) | ✓ | ✓ |
| Light & Dark Themes | ✓ | ✓ |
| Responsive Design | ✓ | ✓ |
| Print Control (per PDF) | ✓ | ✓ |
| Download Control (per PDF) | ✓ | ✓ |
| Configurable Viewer Height | ✓ | ✓ |
| Gutenberg Block | ✓ | ✓ |
| Shortcodes | ✓ | ✓ |
| iOS/Safari Print Support | ✓ | ✓ |
| Text Search in Viewer | - | ✓ |
| Bookmark Navigation | - | ✓ |

### Content Management

| Feature | Free | Premium |
|---------|:----:|:-------:|
| PDF Document Post Type | ✓ | ✓ |
| Title, Description, Slug | ✓ | ✓ |
| File Upload & Management | ✓ | ✓ |
| Featured Image Support | ✓ | ✓ |
| Auto-Generate Thumbnails | ✓ | ✓ |
| Quick Edit Support | ✓ | ✓ |
| Multi-language Support | ✓ | ✓ |
| Categories & Tags | - | ✓ |
| Role-Based Access | - | ✓ |
| Bulk Edit Actions | - | ✓ |
| Bulk Import (CSV/ZIP) | - | ✓ |

### SEO & URLs

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Clean URLs (`/pdf/slug/`) | ✓ | ✓ |
| Schema.org DigitalDocument | ✓ | ✓ |
| Schema.org BreadcrumbList | ✓ | ✓ |
| Yoast SEO Integration | ✓ | ✓ |
| OpenGraph & Twitter Cards | ✓ | ✓ |
| GEO/AEO/LLM Basic Schema | ✓ | ✓ |
| XML Sitemap (`/pdf/sitemap.xml`) | - | ✓ |
| AI Summary & FAQ Schema | - | ✓ |
| Search Engine Ping | - | ✓ |

### Archive & Listing

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Archive Page (`/pdf/`) | ✓ | ✓ |
| Pagination | ✓ | ✓ |
| Grid/List Display Modes | ✓ | ✓ |
| Search & Sorting | ✓ | ✓ |
| Visible Breadcrumb Navigation | ✓ | ✓ |
| Full-Width Layout (No Sidebars) | ✓ | ✓ |
| Custom Archive Heading | ✓ | ✓ |
| Content Alignment Options | ✓ | ✓ |
| Custom Font/Background Colors | ✓ | ✓ |
| Category/Tag Filters | - | ✓ |
| Archive Page Redirect | - | ✓ |

### Statistics & Analytics

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Basic View Counter | ✓ | ✓ |
| Analytics Dashboard | - | ✓ |
| Detailed Tracking (IP, UA, referrer) | - | ✓ |
| Download Tracking | - | ✓ |
| Popular Documents Report | - | ✓ |
| CSV/JSON Export | - | ✓ |
| Time Period Filters | - | ✓ |

### Security & Access

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Secure PDF URLs | ✓ | ✓ |
| Nonce/CSRF Protection | ✓ | ✓ |
| Capability Checks | ✓ | ✓ |
| Password Protection | - | ✓ |
| Brute-Force Protection | - | ✓ |
| Role Restrictions | - | ✓ |
| Expiring Access Links | - | ✓ |

### Reading Experience

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Page Navigation | ✓ | ✓ |
| Zoom Controls | ✓ | ✓ |
| Full Screen Mode | ✓ | ✓ |
| Reading Progress Tracking | - | ✓ |
| Resume Reading | - | ✓ |

---

## Shortcodes

### [pdf_viewer]

Embed a PDF viewer anywhere.

```html
[pdf_viewer id="123"]
[pdf_viewer id="123" width="100%" height="600px"]
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `id` | Required | PDF Document post ID |
| `width` | `100%` | Viewer width |
| `height` | `800px` | Viewer height |

### [pdf_viewer_sitemap]

Display a list of all PDF documents.

```html
[pdf_viewer_sitemap]
[pdf_viewer_sitemap orderby="date" order="DESC" limit="10"]
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `orderby` | `title` | Sort: title, date, modified, menu_order |
| `order` | `ASC` | Direction: ASC, DESC |
| `limit` | `-1` | Number of documents (-1 = all) |

---

## Gutenberg Block

1. In the block editor, click "+" to add a block
2. Search for "PDF Viewer"
3. Select the PDF document from the dropdown
4. Adjust width/height in block settings

---

## REST API

### Base URL

```
/wp-json/pdf-embed-seo/v1/
```

### Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/documents` | List all PDFs | None |
| `GET` | `/documents/{id}` | Get single PDF | None |
| `GET` | `/documents/{id}/data` | Get PDF URL | None |
| `POST` | `/documents/{id}/view` | Track view | None |
| `GET` | `/settings` | Get settings | None |
| `GET` | `/analytics` | Analytics (Premium) | Admin |
| `GET/POST` | `/documents/{id}/progress` | Progress (Premium) | None |
| `POST` | `/documents/{id}/verify-password` | Verify password (Premium) | None |

See [full API documentation](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/DOCUMENTATION.md#rest-api-reference) for details.

---

## WordPress Hooks

### Actions

```php
// Track PDF views externally
add_action('pdf_embed_seo_pdf_viewed', function($post_id, $count) {
    // Send to analytics
}, 10, 2);

// Premium initialization
add_action('pdf_embed_seo_premium_init', function() {
    // Premium loaded
});

// Settings saved
add_action('pdf_embed_seo_optimize_settings_saved', function($post_id, $settings) {
    // Handle settings change
}, 10, 2);
```

### Filters

```php
// Customize archive title
add_filter('pdf_embed_seo_archive_title', function($title) {
    return 'Document Library';
});

// Add custom schema data
add_filter('pdf_embed_seo_schema_data', function($schema, $post_id) {
    $schema['author'] = [
        '@type' => 'Person',
        'name' => get_post_meta($post_id, '_pdf_author', true),
    ];
    return $schema;
}, 10, 2);

// Customize REST API response
add_filter('pdf_embed_seo_rest_document', function($data, $post, $detailed) {
    $data['custom_field'] = get_post_meta($post->ID, '_custom', true);
    return $data;
}, 10, 3);
```

See [full hooks reference](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/DOCUMENTATION.md#wordpress-hooks) for all available hooks.

---

## Template Overrides

Copy templates to your theme to customize:

| Template | Description |
|----------|-------------|
| `single-pdf_document.php` | Single PDF page |
| `archive-pdf_document.php` | Archive page |

---

## Requirements

| Requirement | Version |
|-------------|---------|
| WordPress | 5.8+ |
| PHP | 7.4+ |
| MySQL | 5.6+ or MariaDB 10.1+ |

### Optional Dependencies

| Dependency | Purpose |
|------------|---------|
| Yoast SEO | Enhanced SEO integration |
| ImageMagick | Auto-generate thumbnails |
| Ghostscript | Auto-generate thumbnails |

---

## File Structure

```
pdf-embed-seo-optimize/
├── pdf-embed-seo-optimize.php      # Main plugin file
├── uninstall.php                   # Cleanup on uninstall
├── includes/
│   ├── class-...-post-type.php     # CPT registration
│   ├── class-...-admin.php         # Admin functionality
│   ├── class-...-frontend.php      # Frontend rendering
│   ├── class-...-yoast.php         # Yoast integration
│   ├── class-...-shortcodes.php    # Shortcode handlers
│   ├── class-...-block.php         # Gutenberg block
│   ├── class-...-thumbnail.php     # Thumbnail generation
│   └── class-...-rest-api.php      # REST API
├── admin/                          # Admin assets
├── public/                         # Frontend assets
├── assets/pdfjs/                   # PDF.js library
└── premium/                        # Premium features
    ├── class-...-premium.php       # Premium loader
    └── includes/                   # Premium classes
```

---

## Premium Installation

1. **Purchase** from [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de)
2. **Download** the premium ZIP file
3. **Upload** via Plugins > Add New > Upload Plugin
4. **Activate** the plugin
5. **Activate License** at PDF Documents > Settings > License

---

## Downloads

| Package | Link |
|---------|------|
| WordPress Free + Premium | [pdf-embed-seo-all-modules-v1.2.11.zip](https://github.com/alexanderdross/PDF-Viewer-2026/raw/main/dist/pdf-embed-seo-all-modules-v1.2.11.zip) |
| Complete Package (All Platforms) | [pdf-embed-seo-complete-v1.2.11.zip](https://github.com/alexanderdross/PDF-Viewer-2026/raw/main/dist/pdf-embed-seo-complete-v1.2.11.zip) |

---

## Related Documentation

- [Pro Features](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/PRO.md)
- [Complete Feature Matrix](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/FEATURES.md)
- [Full Documentation](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/DOCUMENTATION.md)
- [WordPress Changelog](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/CHANGELOG-WORDPRESS.md)
- [Free vs Premium Comparison](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/COMPARISON.md)

---

*Made with love by [Dross:Media](https://dross.net/media/)*

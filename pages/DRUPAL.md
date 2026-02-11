# PDF Embed & SEO Optimize for Drupal

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize for Drupal" width="100">
</p>

<p align="center">
  <strong>Professional PDF viewer module for Drupal 10 & 11</strong><br>
  SEO-optimized, secure, and fully integrated with Drupal's architecture.
</p>

---

**Current Version:** 1.2.11
**Requires:** Drupal 10/11 | PHP 8.1+
**License:** GPL v2 (Free), Commercial (Premium)

---

## Quick Start

### Installation

**Via Composer (Recommended):**
```bash
composer require drossmedia/pdf_embed_seo
drush en pdf_embed_seo

# For Premium
drush en pdf_embed_seo_premium
```

**Manual Installation:**
1. Download from [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de)
2. Extract to `/modules/contrib/pdf_embed_seo/`
3. Enable via Admin > Extend
4. Configure at Admin > Configuration > Content > PDF Embed & SEO

### Create Your First PDF Document

1. Go to **Content > PDF Documents > Add PDF Document**
2. Enter a title and description
3. Upload your PDF file
4. Configure print/download settings
5. Save

Your PDF is now available at `/pdf/your-document-slug`

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
| PDF Viewer Block | ✓ | ✓ |
| iOS/Safari Print Support | ✓ | ✓ |
| Text Search in Viewer | - | ✓ |
| Bookmark Navigation | - | ✓ |

### Content Management

| Feature | Free | Premium |
|---------|:----:|:-------:|
| PDF Document Entity | ✓ | ✓ |
| Title, Description, Slug | ✓ | ✓ |
| File Upload & Management | ✓ | ✓ |
| Thumbnail Support | ✓ | ✓ |
| Auto-Generate Thumbnails | ✓ | ✓ |
| Multi-language Support | ✓ | ✓ |
| Owner/User Tracking | ✓ | ✓ |
| Media Library Integration | ✓ | ✓ |
| Categories & Tags | - | ✓ |
| Role-Based Access | - | ✓ |
| Bulk Import (CSV/ZIP) | - | ✓ |

### SEO & URLs

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Clean URLs (`/pdf/slug`) | ✓ | ✓ |
| Auto Path Alias | ✓ | ✓ |
| Schema.org DigitalDocument | ✓ | ✓ |
| Schema.org CollectionPage | ✓ | ✓ |
| Schema.org BreadcrumbList | ✓ | ✓ |
| OpenGraph & Twitter Cards | ✓ | ✓ |
| GEO/AEO/LLM Basic Schema | ✓ | ✓ |
| XML Sitemap (`/pdf/sitemap.xml`) | - | ✓ |
| AI Summary & FAQ Schema | - | ✓ |

### Archive & Listing

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Archive Page (`/pdf`) | ✓ | ✓ |
| Pagination | ✓ | ✓ |
| Grid/List Display Modes | ✓ | ✓ |
| Search & Sorting | ✓ | ✓ |
| Visible Breadcrumb Navigation | ✓ | ✓ |
| Full-Width Layout (No Sidebars) | ✓ | ✓ |
| Custom Archive Heading | ✓ | ✓ |
| Content Alignment Options | ✓ | ✓ |
| Custom Font/Background Colors | ✓ | ✓ |
| Category/Tag Filters | - | ✓ |

### Statistics & Analytics

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Basic View Counter | ✓ | ✓ |
| Analytics Dashboard | - | ✓ |
| Detailed Tracking (IP, UA, referrer) | - | ✓ |
| Download Tracking | - | ✓ |
| IP Anonymization (GDPR) | - | ✓ |
| Popular Documents Report | - | ✓ |
| CSV/JSON Export | - | ✓ |

### Security & Access

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Secure PDF URLs | ✓ | ✓ |
| CSRF Protection | ✓ | ✓ |
| Permission System | ✓ | ✓ |
| Entity Access Control | ✓ | ✓ |
| Password Protection | - | ✓ |
| Password Hashing (Drupal service) | - | ✓ |
| Session Cache Context | - | ✓ |
| Brute-Force Protection | - | ✓ |
| Rate Limiting | - | ✓ |
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

## Drupal Block

### PDF Viewer Block

1. Go to **Admin > Structure > Block Layout**
2. Click "Place block" in the desired region
3. Search for "PDF Viewer"
4. Configure:
   - Select PDF document
   - Set viewer height
   - Toggle title visibility
5. Save

---

## REST API

### Base URL

```
/api/pdf-embed-seo/v1/
```

### Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/documents` | List all PDFs | None |
| `GET` | `/documents/{id}` | Get single PDF | None |
| `GET` | `/documents/{id}/data` | Get PDF URL | None |
| `POST` | `/documents/{id}/view` | Track view | CSRF |
| `GET` | `/settings` | Get settings | None |
| `GET` | `/analytics` | Analytics (Premium) | Admin |
| `GET/POST` | `/documents/{id}/progress` | Progress (Premium) | CSRF |
| `POST` | `/documents/{id}/verify-password` | Verify password (Premium) | CSRF |
| `POST` | `/documents/{id}/download` | Track download (Premium) | CSRF |
| `POST` | `/documents/{id}/expiring-link` | Create link (Premium) | Admin |

**Note:** POST endpoints require CSRF token for security.

See [full API documentation](https://github.com/alexanderdross/PDF-Viewer-Plugin/blob/main/DOCUMENTATION.md#rest-api-reference) for details.

---

## Drupal Hooks

### Alter Hooks

```php
/**
 * Implements hook_pdf_embed_seo_document_data_alter().
 */
function mymodule_pdf_embed_seo_document_data_alter(array &$data, $document) {
  $data['custom_field'] = $document->get('field_custom')->value;
}

/**
 * Implements hook_pdf_embed_seo_schema_alter().
 */
function mymodule_pdf_embed_seo_schema_alter(array &$schema, $document) {
  $schema['author'] = [
    '@type' => 'Person',
    'name' => $document->get('field_author')->value,
  ];
}

/**
 * Implements hook_pdf_embed_seo_viewer_options_alter().
 */
function mymodule_pdf_embed_seo_viewer_options_alter(array &$options, $document) {
  $options['theme'] = 'dark';
}

/**
 * Implements hook_pdf_embed_seo_api_settings_alter().
 */
function mymodule_pdf_embed_seo_api_settings_alter(array &$settings) {
  $settings['custom_setting'] = TRUE;
}

/**
 * Implements hook_pdf_embed_seo_verify_password_alter().
 * (Premium only)
 */
function mymodule_pdf_embed_seo_verify_password_alter(&$is_valid, $document, $password) {
  // Custom password verification logic
}
```

### Event Hooks

```php
/**
 * Implements hook_pdf_embed_seo_view_tracked().
 */
function mymodule_pdf_embed_seo_view_tracked($document_id, $view_count) {
  // Track in external analytics
}

/**
 * Implements hook_pdf_embed_seo_document_saved().
 */
function mymodule_pdf_embed_seo_document_saved($document) {
  // Handle document save
}
```

---

## Template Overrides

Copy templates to your theme to customize:

| Template | Description |
|----------|-------------|
| `pdf-document.html.twig` | Single PDF display |
| `pdf-viewer.html.twig` | PDF.js viewer |
| `pdf-archive.html.twig` | Archive listing |
| `pdf-archive-item.html.twig` | Archive item |
| `pdf-password-form.html.twig` | Password form (Premium) |
| `pdf-analytics-dashboard.html.twig` | Analytics (Premium) |

### Theme Suggestions

The module provides theme suggestions for full-width templates:

- `page--pdf.html.twig` - All PDF routes
- `page--pdf--archive.html.twig` - Archive page
- `page--pdf--document.html.twig` - Single document

---

## Services

### Free Services

| Service | Description |
|---------|-------------|
| `pdf_embed_seo.thumbnail_generator` | Generate PDF thumbnails |

### Premium Services

| Service | Description |
|---------|-------------|
| `pdf_embed_seo.analytics_tracker` | Track views and downloads |
| `pdf_embed_seo.progress_tracker` | Track reading progress |
| `pdf_embed_seo.rate_limiter` | Brute force protection |
| `pdf_embed_seo.access_token_storage` | Expiring link tokens |
| `pdf_embed_seo.schema_enhancer` | GEO/AEO schema optimization |
| `pdf_embed_seo.access_manager` | Role-based access control |
| `pdf_embed_seo.bulk_operations` | CSV/ZIP import |
| `pdf_embed_seo.viewer_enhancer` | Search, bookmarks |

---

## Requirements

| Requirement | Version |
|-------------|---------|
| Drupal | 10 or 11 |
| PHP | 8.1+ |

### Core Module Dependencies

- `node`
- `file`
- `taxonomy`
- `path`
- `path_alias`
- `media`

### Optional Dependencies

| Dependency | Purpose |
|------------|---------|
| Pathauto | Auto path aliases |
| ImageMagick | Auto-generate thumbnails |
| Ghostscript | Auto-generate thumbnails |

---

## File Structure

```
drupal-pdf-embed-seo/
├── pdf_embed_seo.info.yml          # Module info
├── pdf_embed_seo.module            # Hook implementations
├── pdf_embed_seo.install           # Install/uninstall hooks
├── pdf_embed_seo.routing.yml       # Route definitions
├── pdf_embed_seo.services.yml      # Service definitions
├── pdf_embed_seo.permissions.yml   # Permission definitions
├── config/
│   ├── install/                    # Default config
│   └── schema/                     # Config schema
├── src/
│   ├── Entity/                     # Entity classes
│   ├── Controller/                 # Route controllers
│   ├── Form/                       # Forms
│   ├── Plugin/                     # Blocks, REST resources
│   └── Service/                    # Services
├── templates/                      # Twig templates
├── assets/                         # CSS, JS, PDF.js
└── modules/
    └── pdf_embed_seo_premium/      # Premium submodule
```

---

## Database Schema

### v1.2.11 Tables (Premium)

| Table | Purpose |
|-------|---------|
| `pdf_embed_seo_analytics` | View/download tracking |
| `pdf_embed_seo_progress` | Reading progress |
| `pdf_embed_seo_access_tokens` | Expiring link tokens |
| `pdf_embed_seo_rate_limit` | Brute force tracking |

---

## Security Features (v1.2.11)

The Drupal module includes comprehensive security measures:

### CSRF Protection
All POST API endpoints require CSRF tokens (`_csrf_token: 'TRUE'`).

### Rate Limiting
Password verification is rate-limited:
- 5 attempts per 5 minutes per IP/document
- 15 minute block after exceeded

### Session Cache Context
Password-protected routes include session cache context to prevent cross-session cache leaks.

### IP Anonymization
GDPR-compliant IP anonymization:
- IPv4: Last octet zeroed (192.168.1.x → 192.168.1.0)
- IPv6: Last 80 bits zeroed
- Enabled by default

### Password Hashing
Passwords use Drupal's password service (bcrypt) - never stored in plain text.

---

## Premium Installation

1. **Purchase** from [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de)
2. **Download** the Drupal module
3. **Extract** to `/modules/contrib/pdf_embed_seo/modules/pdf_embed_seo_premium/`
4. **Enable** via Admin > Extend
5. **Configure** at Admin > Configuration > Content > PDF Premium Settings

---

## Downloads

| Package | Link |
|---------|------|
| Drupal Free + Premium | [drupal-pdf-embed-seo-v1.2.11.zip](https://github.com/alexanderdross/PDF-Viewer-Plugin/raw/main/dist/drupal-pdf-embed-seo-v1.2.11.zip) |
| Complete Package (All Platforms) | [pdf-embed-seo-complete-v1.2.11.zip](https://github.com/alexanderdross/PDF-Viewer-Plugin/raw/main/dist/pdf-embed-seo-complete-v1.2.11.zip) |

---

## Related Documentation

- [Pro Features](https://github.com/alexanderdross/PDF-Viewer-Plugin/blob/main/PRO.md)
- [Complete Feature Matrix](https://github.com/alexanderdross/PDF-Viewer-Plugin/blob/main/FEATURES.md)
- [Full Documentation](https://github.com/alexanderdross/PDF-Viewer-Plugin/blob/main/DOCUMENTATION.md)
- [Drupal Changelog](https://github.com/alexanderdross/PDF-Viewer-Plugin/blob/main/CHANGELOG-DRUPAL.md)
- [Free vs Premium Comparison](https://github.com/alexanderdross/PDF-Viewer-Plugin/blob/main/COMPARISON.md)

---

*Made with love by [Dross:Media](https://dross.net/media/)*

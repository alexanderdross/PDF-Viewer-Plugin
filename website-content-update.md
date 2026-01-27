# Website Content Update - PDF Embed & SEO Optimize

This document contains updated content for the website pages. Copy the relevant sections to update each page.

---

## Module Summary (GitHub Folder Structure)

| Module | GitHub Folder | Platform | Description |
|--------|---------------|----------|-------------|
| **WP Free** | `wp-pdf-embed-seo-optimize/` | WordPress 5.8+ | Core PDF viewer, SEO, basic REST API |
| **WP Premium** | `wp-pdf-embed-seo-optimize/premium/` | WordPress 5.8+ | Analytics, passwords, progress, sitemap |
| **Drupal Free** | `drupal-pdf-embed-seo/` | Drupal 10/11 | Core PDF viewer, SEO, basic REST API |
| **Drupal Premium** | `drupal-pdf-embed-seo/modules/pdf_embed_seo_premium/` | Drupal 10/11 | Analytics, passwords, progress, sitemap |

---

## 1. Premium Features Page (`/pro/`)

### Hero Section

```markdown
# PDF Embed & SEO Optimize Pro

Unlock the full potential of your PDF management with advanced analytics, password protection, reading progress tracking, and more.

**Version 1.2.1** | WordPress & Drupal
```

### Pricing Tiers

| Feature | Starter | Professional | Agency |
|---------|:-------:|:------------:|:------:|
| **Price** | $49/year | $99/year | $199/year |
| **Sites** | 1 site | 5 sites | Unlimited |
| **Updates** | 1 year | 1 year | 1 year |
| **Support** | Email | Priority Email | Priority + Chat |
| | | | |
| **Core Premium Features** | | | |
| Analytics Dashboard | ✓ | ✓ | ✓ |
| Password Protection | ✓ | ✓ | ✓ |
| Detailed View Tracking | ✓ | ✓ | ✓ |
| | | | |
| **Advanced Features** | | | |
| Reading Progress | - | ✓ | ✓ |
| XML Sitemap (`/pdf/sitemap.xml`) | - | ✓ | ✓ |
| Categories & Tags | - | ✓ | ✓ |
| CSV/JSON Export | - | ✓ | ✓ |
| | | | |
| **Professional Features** | | | |
| Role-Based Access | - | - | ✓ |
| Bulk Import (CSV/ZIP) | - | - | ✓ |
| Full REST API | - | - | ✓ |
| White-Label Options | - | - | ✓ |

### Premium Features Detail

#### Analytics Dashboard
Track every PDF view with detailed statistics:
- Total views across all documents
- Popular documents ranking
- Recent views log with timestamps
- Unique visitors tracking (IP-based)
- User agent and referrer tracking
- Time spent on each PDF
- Filter by date range (7 days, 30 days, 90 days, 12 months)
- Export data as CSV or JSON

#### Password Protection
Secure sensitive PDFs with password protection:
- Per-PDF password settings
- Secure password hashing
- Session-based authentication
- Configurable session duration
- Brute-force protection (max attempts)
- Beautiful password prompt UI
- AJAX-based verification (no page reload)

#### Reading Progress
Remember where users left off:
- Auto-save reading position
- Resume from last page on return
- Track scroll position and zoom level
- Works for logged-in and anonymous users
- Session-based storage for guests
- Database storage for registered users

#### XML Sitemap
Dedicated PDF sitemap for better SEO:
- Available at `/pdf/sitemap.xml`
- Beautiful XSL-styled browser view
- Includes all PDF metadata
- Auto-updates when PDFs change
- Submit to Google Search Console
- Proper cache headers

#### Categories & Tags
Organize your PDF library:
- Hierarchical categories
- Flat tags taxonomy
- Filter archive by category/tag
- Category and tag archive pages
- REST API endpoints for taxonomies

#### Full REST API (Agency)
Complete API access for integrations:
- Analytics endpoints
- Reading progress endpoints
- Password verification endpoint
- Category and tag endpoints
- Bulk import endpoints

---

## 2. WordPress Page (`/wordpress-pdf-viewer/`)

### Feature Comparison Table

| Feature | Free | Pro |
|---------|:----:|:---:|
| **Viewer & Display** | | |
| Mozilla PDF.js Viewer (v4.0) | ✓ | ✓ |
| Light & Dark Themes | ✓ | ✓ |
| Responsive Design | ✓ | ✓ |
| Print Control (per PDF) | ✓ | ✓ |
| Download Control (per PDF) | ✓ | ✓ |
| Configurable Viewer Height | ✓ | ✓ |
| Gutenberg Block | ✓ | ✓ |
| Shortcodes | ✓ | ✓ |
| Text Search in Viewer | - | ✓ |
| Bookmark Navigation | - | ✓ |
| | | |
| **Content Management** | | |
| PDF Document Post Type | ✓ | ✓ |
| Title, Description, Slug | ✓ | ✓ |
| File Upload & Management | ✓ | ✓ |
| Featured Image Support | ✓ | ✓ |
| Auto-Generate Thumbnails | ✓ | ✓ |
| Quick Edit Support | ✓ | ✓ |
| Categories & Tags | - | ✓ |
| Role-Based Access | - | ✓ |
| Bulk Edit Actions | - | ✓ |
| Bulk Import (CSV/ZIP) | - | ✓ |
| | | |
| **SEO & URLs** | | |
| Clean URLs (`/pdf/slug/`) | ✓ | ✓ |
| Schema.org DigitalDocument | ✓ | ✓ |
| Yoast SEO Integration | ✓ | ✓ |
| OpenGraph & Twitter Cards | ✓ | ✓ |
| XML Sitemap (`/pdf/sitemap.xml`) | - | ✓ |
| Search Engine Ping | - | ✓ |
| | | |
| **Archive & Listing** | | |
| Archive Page (`/pdf/`) | ✓ | ✓ |
| Pagination | ✓ | ✓ |
| Grid/List Display | ✓ | ✓ |
| Search & Sorting | ✓ | ✓ |
| Category/Tag Filters | - | ✓ |
| | | |
| **REST API** | | |
| GET /documents | ✓ | ✓ |
| GET /documents/{id} | ✓ | ✓ |
| GET /documents/{id}/data | ✓ | ✓ |
| POST /documents/{id}/view | ✓ | ✓ |
| GET /settings | ✓ | ✓ |
| GET /analytics | - | ✓ |
| GET/POST /progress | - | ✓ |
| POST /verify-password | - | ✓ |
| GET /categories, /tags | - | ✓ |
| POST /bulk/import | - | ✓ |
| | | |
| **Statistics & Analytics** | | |
| Basic View Counter | ✓ | ✓ |
| Analytics Dashboard | - | ✓ |
| Detailed Tracking (IP, UA, referrer) | - | ✓ |
| Popular Documents Report | - | ✓ |
| CSV/JSON Export | - | ✓ |
| | | |
| **Security** | | |
| Secure PDF URLs | ✓ | ✓ |
| Nonce Verification | ✓ | ✓ |
| Capability Checks | ✓ | ✓ |
| Password Protection | - | ✓ |
| Role Restrictions | - | ✓ |
| | | |
| **Reading Experience** | | |
| Page Navigation | ✓ | ✓ |
| Zoom Controls | ✓ | ✓ |
| Full Screen Mode | ✓ | ✓ |
| Reading Progress Tracking | - | ✓ |
| Resume Reading | - | ✓ |
| | | |
| **Developer** | | |
| WordPress Hooks | ✓ | ✓ |
| Template Overrides | ✓ | ✓ |
| JavaScript Events | ✓ | ✓ |

### Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- Optional: Yoast SEO (for enhanced SEO)
- Optional: ImageMagick or Ghostscript (for auto thumbnails)

### Installation

1. Download the plugin ZIP file
2. Go to Plugins > Add New > Upload Plugin
3. Upload and activate the plugin
4. Go to PDF Documents to start adding PDFs

---

## 3. Drupal Page (`/drupal-pdf-viewer/`)

### Feature Comparison Table

| Feature | Free | Pro |
|---------|:----:|:---:|
| **Viewer & Display** | | |
| Mozilla PDF.js Viewer (v4.0) | ✓ | ✓ |
| Light & Dark Themes | ✓ | ✓ |
| Responsive Design | ✓ | ✓ |
| Print Control (per PDF) | ✓ | ✓ |
| Download Control (per PDF) | ✓ | ✓ |
| Configurable Viewer Height | ✓ | ✓ |
| PDF Viewer Block | ✓ | ✓ |
| | | |
| **Content Management** | | |
| PDF Document Entity | ✓ | ✓ |
| Title, Description, Slug | ✓ | ✓ |
| File Upload & Management | ✓ | ✓ |
| Thumbnail Support | ✓ | ✓ |
| Auto-Generate Thumbnails | ✓ | ✓ |
| Multi-language Support | ✓ | ✓ |
| Owner/User Tracking | ✓ | ✓ |
| | | |
| **SEO & URLs** | | |
| Clean URLs (`/pdf/slug`) | ✓ | ✓ |
| Auto Path Alias | ✓ | ✓ |
| Schema.org DigitalDocument | ✓ | ✓ |
| Schema.org CollectionPage | ✓ | ✓ |
| XML Sitemap (`/pdf/sitemap.xml`) | - | ✓ |
| | | |
| **Archive & Listing** | | |
| Archive Page (`/pdf`) | ✓ | ✓ |
| Pagination | ✓ | ✓ |
| Grid/List Display | ✓ | ✓ |
| Search & Sorting | ✓ | ✓ |
| | | |
| **REST API** | | |
| GET /documents | ✓ | ✓ |
| GET /documents/{id} | ✓ | ✓ |
| GET /documents/{id}/data | ✓ | ✓ |
| POST /documents/{id}/view | ✓ | ✓ |
| GET /settings | ✓ | ✓ |
| GET /analytics | - | ✓ |
| GET/POST /progress | - | ✓ |
| POST /verify-password | - | ✓ |
| | | |
| **Statistics & Analytics** | | |
| Basic View Counter | ✓ | ✓ |
| Analytics Dashboard | - | ✓ |
| Detailed Tracking (IP, UA, referrer) | - | ✓ |
| Time Spent Tracking | - | ✓ |
| Popular Documents Report | - | ✓ |
| CSV/JSON Export | - | ✓ |
| | | |
| **Security** | | |
| Secure PDF URLs | ✓ | ✓ |
| CSRF Protection | ✓ | ✓ |
| Permission System | ✓ | ✓ |
| Entity Access Control | ✓ | ✓ |
| Password Protection | - | ✓ |
| | | |
| **Reading Experience** | | |
| Page Navigation | ✓ | ✓ |
| Zoom Controls | ✓ | ✓ |
| Full Screen Mode | ✓ | ✓ |
| Reading Progress Tracking | - | ✓ |
| Resume Reading | - | ✓ |
| | | |
| **Developer** | | |
| Drupal Hooks (alter, events) | ✓ | ✓ |
| Twig Template Overrides | ✓ | ✓ |
| Drupal Services | ✓ | ✓ |
| Cache Tags & Contexts | ✓ | ✓ |

### Requirements

- Drupal 10 or 11
- PHP 8.1 or higher
- Optional: ImageMagick or Ghostscript (for auto thumbnails)

### Installation

**Via Composer (recommended):**
```bash
composer require drossmedia/pdf_embed_seo
drush en pdf_embed_seo
```

**Manual Installation:**
1. Download and extract to `/modules/contrib/pdf_embed_seo`
2. Enable via Admin > Extend
3. Configure at Admin > Configuration > Content > PDF Embed & SEO

---

## 4. Documentation Page (`/documentation/`)

### Table of Contents

1. [Getting Started](#getting-started)
2. [Configuration](#configuration)
3. [REST API Reference](#rest-api-reference)
4. [WordPress Hooks](#wordpress-hooks)
5. [Drupal Hooks](#drupal-hooks)
6. [Shortcodes & Blocks](#shortcodes--blocks)
7. [Theming & Templates](#theming--templates)
8. [Premium Features](#premium-features)

---

### Getting Started

#### WordPress Installation

```bash
# Via WordPress Admin
1. Go to Plugins > Add New
2. Search for "PDF Embed SEO Optimize"
3. Click Install, then Activate

# Via WP-CLI
wp plugin install pdf-embed-seo-optimize --activate
```

#### Drupal Installation

```bash
# Via Composer
composer require drossmedia/pdf_embed_seo
drush en pdf_embed_seo

# For Premium
drush en pdf_embed_seo_premium
```

---

### Configuration

#### WordPress Settings

Navigate to: **PDF Documents > Settings**

| Setting | Default | Description |
|---------|---------|-------------|
| Allow Download by Default | Yes | New PDFs allow downloads |
| Allow Print by Default | Yes | New PDFs allow printing |
| Auto-generate Thumbnails | Yes | Create thumbnails from PDF |
| Viewer Theme | Light | Light or Dark theme |
| Archive Posts per Page | 12 | PDFs per archive page |

#### Drupal Settings

Navigate to: **Admin > Configuration > Content > PDF Embed & SEO**

| Setting | Default | Description |
|---------|---------|-------------|
| Default Allow Download | Yes | New PDFs allow downloads |
| Default Allow Print | Yes | New PDFs allow printing |
| Auto-generate Thumbnails | Yes | Create thumbnails from PDF |
| Viewer Theme | Light | Light or Dark theme |
| Viewer Height | 800px | Default viewer height |
| Archive Display Mode | Grid | Grid or List layout |
| Documents per Page | 12 | PDFs per archive page |

---

### REST API Reference

#### Base URLs

| Platform | Base URL |
|----------|----------|
| WordPress | `/wp-json/pdf-embed-seo/v1/` |
| Drupal | `/api/pdf-embed-seo/v1/` |

#### Public Endpoints (Free)

##### GET /documents

List all published PDF documents.

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 10 | Items per page (max 100) |
| `search` | string | - | Search term |
| `orderby` | string | date | Sort: date, title, modified, views |
| `order` | string | desc | Direction: asc, desc |

**Example Request:**
```bash
curl -X GET "https://example.com/wp-json/pdf-embed-seo/v1/documents?per_page=5&orderby=views"
```

**Example Response:**
```json
{
  "documents": [
    {
      "id": 123,
      "title": "Annual Report 2024",
      "slug": "annual-report-2024",
      "url": "https://example.com/pdf/annual-report-2024/",
      "excerpt": "Company annual report for fiscal year 2024...",
      "date": "2024-01-15T10:30:00+00:00",
      "modified": "2024-06-20T14:45:00+00:00",
      "views": 1542,
      "thumbnail": "https://example.com/wp-content/uploads/thumb.jpg",
      "allow_download": true,
      "allow_print": false
    }
  ],
  "total": 45,
  "pages": 9
}
```

##### GET /documents/{id}

Get single PDF document details.

**Example Request:**
```bash
curl -X GET "https://example.com/wp-json/pdf-embed-seo/v1/documents/123"
```

##### GET /documents/{id}/data

Get PDF file URL securely (for viewer integration).

**Example Response:**
```json
{
  "id": 123,
  "pdf_url": "https://example.com/wp-content/uploads/2024/01/report.pdf",
  "allow_download": true,
  "allow_print": false
}
```

##### POST /documents/{id}/view

Track a PDF view (increment view counter).

**Example Request:**
```bash
curl -X POST "https://example.com/wp-json/pdf-embed-seo/v1/documents/123/view"
```

**Example Response:**
```json
{
  "success": true,
  "views": 1543
}
```

##### GET /settings

Get public plugin settings.

**Example Response:**
```json
{
  "viewer_theme": "light",
  "default_allow_download": true,
  "default_allow_print": true,
  "archive_url": "https://example.com/pdf/",
  "is_premium": false
}
```

---

#### Premium Endpoints

##### GET /analytics

Get analytics overview (requires admin permission).

**Query Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `period` | string | 30days | 7days, 30days, 90days, 12months, all |

**Example Request:**
```bash
curl -X GET "https://example.com/wp-json/pdf-embed-seo/v1/analytics?period=30days" \
  -H "X-WP-Nonce: your-nonce"
```

**Example Response:**
```json
{
  "period": "30days",
  "total_views": 15234,
  "unique_visitors": 8721,
  "total_documents": 45,
  "top_documents": [
    {
      "id": 123,
      "title": "Annual Report",
      "views": 1542,
      "unique_views": 892
    }
  ],
  "views_by_day": [
    {"date": "2024-06-01", "views": 234},
    {"date": "2024-06-02", "views": 312}
  ]
}
```

##### GET /documents/{id}/progress

Get reading progress for current user/session.

**Example Response:**
```json
{
  "document_id": 123,
  "progress": {
    "page": 15,
    "scroll": 0.45,
    "zoom": 1.25,
    "last_read": "2024-06-20T14:45:00+00:00"
  }
}
```

##### POST /documents/{id}/progress

Save reading progress.

**Request Body:**
```json
{
  "page": 15,
  "scroll": 0.45,
  "zoom": 1.25
}
```

**Example Response:**
```json
{
  "success": true,
  "document_id": 123,
  "progress": {
    "page": 15,
    "scroll": 0.45,
    "zoom": 1.25,
    "last_read": "2024-06-20T14:45:00+00:00"
  }
}
```

##### POST /documents/{id}/verify-password

Verify password for protected PDF.

**Request Body:**
```json
{
  "password": "user-entered-password"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "access_token": "csrf_token_here",
  "expires_in": 3600
}
```

**Error Response (403):**
```json
{
  "success": false,
  "message": "Incorrect password."
}
```

##### GET /categories

Get all PDF categories (Premium).

**Example Response:**
```json
{
  "categories": [
    {
      "id": 5,
      "name": "Reports",
      "slug": "reports",
      "count": 12,
      "parent": 0
    }
  ]
}
```

##### GET /tags

Get all PDF tags (Premium).

**Example Response:**
```json
{
  "tags": [
    {
      "id": 8,
      "name": "2024",
      "slug": "2024",
      "count": 8
    }
  ]
}
```

---

### WordPress Hooks

#### Actions

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_pdf_viewed` | `$post_id, $count` | Fired when PDF is viewed |
| `pdf_embed_seo_premium_init` | - | Premium features initialized |
| `pdf_embed_seo_settings_saved` | `$post_id, $settings` | Settings saved |

**Example: Track views in external analytics**
```php
add_action( 'pdf_embed_seo_pdf_viewed', function( $post_id, $count ) {
    // Send to Google Analytics, etc.
    my_analytics_track( 'pdf_view', [
        'pdf_id' => $post_id,
        'title'  => get_the_title( $post_id ),
        'views'  => $count,
    ]);
}, 10, 2 );
```

#### Filters

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_post_type_args` | `$args` | Modify CPT registration |
| `pdf_embed_seo_schema_data` | `$schema, $post_id` | Modify Schema.org data |
| `pdf_embed_seo_archive_schema_data` | `$schema` | Modify archive schema |
| `pdf_embed_seo_archive_query` | `$posts_per_page` | Modify archive query |
| `pdf_embed_seo_archive_title` | `$title` | Modify archive title |
| `pdf_embed_seo_archive_description` | `$description` | Modify archive description |
| `pdf_embed_seo_viewer_options` | `$options, $post_id` | Modify viewer options |
| `pdf_embed_seo_allowed_types` | `$types` | Modify allowed MIME types |
| `pdf_embed_seo_rest_document` | `$data, $post, $detailed` | Modify REST response |
| `pdf_embed_seo_rest_settings` | `$settings` | Modify REST settings |

**Example: Add custom schema data**
```php
add_filter( 'pdf_embed_seo_schema_data', function( $schema, $post_id ) {
    $schema['author'] = [
        '@type' => 'Person',
        'name'  => get_post_meta( $post_id, '_pdf_author', true ),
    ];
    return $schema;
}, 10, 2 );
```

**Example: Customize archive title**
```php
add_filter( 'pdf_embed_seo_archive_title', function( $title ) {
    return 'Document Library';
});
```

**Example: Add field to REST API**
```php
add_filter( 'pdf_embed_seo_rest_document', function( $data, $post, $detailed ) {
    $data['department'] = get_post_meta( $post->ID, '_pdf_department', true );
    return $data;
}, 10, 3 );
```

#### Premium Filters

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_password_error` | `$error` | Custom password error |
| `pdf_embed_seo_verify_password` | `$is_valid, $post_id, $password` | Override password check |
| `pdf_embed_seo_rest_analytics` | `$data, $period` | Modify analytics response |

---

### Drupal Hooks

#### Alter Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_document_data_alter` | Modify API document data |
| `hook_pdf_embed_seo_api_settings_alter` | Modify API settings |
| `hook_pdf_embed_seo_viewer_options_alter` | Modify viewer options |
| `hook_pdf_embed_seo_schema_alter` | Modify Schema.org output |
| `hook_pdf_embed_seo_verify_password_alter` | Override password verification (Premium) |

**Example: Modify document data**
```php
/**
 * Implements hook_pdf_embed_seo_document_data_alter().
 */
function mymodule_pdf_embed_seo_document_data_alter(array &$data, $document) {
  $data['department'] = $document->get('field_department')->value;
}
```

**Example: Custom schema**
```php
/**
 * Implements hook_pdf_embed_seo_schema_alter().
 */
function mymodule_pdf_embed_seo_schema_alter(array &$schema, $document) {
  $schema['author'] = [
    '@type' => 'Person',
    'name' => $document->get('field_author')->value,
  ];
}
```

#### Event Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_view_tracked` | PDF view was tracked |
| `hook_pdf_embed_seo_document_saved` | PDF document saved |

---

### Shortcodes & Blocks

#### WordPress Shortcodes

##### [pdf_viewer]

Embed a single PDF viewer.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `id` | Current post | PDF document ID |
| `width` | 100% | Viewer width |
| `height` | 800px | Viewer height |

```html
[pdf_viewer id="123" width="100%" height="600px"]
```

##### [pdf_viewer_sitemap]

Display a list of all PDFs.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `orderby` | title | Sort: title, date, menu_order |
| `order` | ASC | Direction: ASC, DESC |
| `limit` | -1 | Number of PDFs (-1 for all) |

```html
[pdf_viewer_sitemap orderby="date" order="DESC" limit="10"]
```

#### WordPress Gutenberg Block

1. In the block editor, click "+" to add a block
2. Search for "PDF Viewer"
3. Select the PDF document from the dropdown
4. Adjust width/height in block settings

#### Drupal Block

1. Go to Admin > Structure > Block Layout
2. Place a "PDF Viewer" block
3. Configure:
   - Select PDF document
   - Set viewer height
   - Toggle title visibility

---

### Theming & Templates

#### WordPress Templates

Override in your theme:

| Template | Description |
|----------|-------------|
| `single-pdf_document.php` | Single PDF page |
| `archive-pdf_document.php` | Archive page |

#### Drupal Templates

Override in your theme:

| Template | Description |
|----------|-------------|
| `pdf-document.html.twig` | Single PDF display |
| `pdf-viewer.html.twig` | PDF.js viewer |
| `pdf-archive.html.twig` | Archive listing |
| `pdf-archive-item.html.twig` | Archive item |
| `pdf-password-form.html.twig` | Password form (Premium) |
| `pdf-analytics-dashboard.html.twig` | Analytics (Premium) |

#### CSS Classes

| Class | Description |
|-------|-------------|
| `.pdf-viewer-wrapper` | Main viewer container |
| `.pdf-viewer-toolbar` | Viewer toolbar |
| `.pdf-viewer-container` | PDF canvas container |
| `.pdf-viewer-theme-light` | Light theme |
| `.pdf-viewer-theme-dark` | Dark theme |
| `.pdf-archive` | Archive wrapper |
| `.pdf-archive-item` | Archive item |
| `.pdf-download-button` | Download button |

#### JavaScript Events

| Event | Description |
|-------|-------------|
| `pdfLoaded` | PDF document loaded |
| `pageRendered` | Page rendered |
| `pageChanged` | Page navigation |
| `zoomChanged` | Zoom level changed |

---

### Premium Features

#### License Activation

**WordPress:**
1. Go to PDF Documents > Settings > License
2. Enter your license key
3. Click "Activate License"

**Drupal:**
1. Go to Admin > Configuration > Content > PDF Premium Settings
2. Enter your license key
3. Click "Save configuration"

#### License Tiers

| Tier | Sites | Features |
|------|-------|----------|
| **Starter** | 1 | Analytics, Password Protection |
| **Professional** | 5 | + Progress, Sitemap, Categories/Tags |
| **Agency** | Unlimited | + Bulk Import, Full API, Priority Support |

#### Analytics Dashboard

Access at:
- **WordPress:** PDF Documents > Analytics
- **Drupal:** Admin > Reports > PDF Analytics

Features:
- Total views and unique visitors
- Popular documents chart
- Recent views log
- Time period filters
- CSV/JSON export

#### Password Protection

Enable per-PDF:
1. Edit the PDF document
2. Check "Password Protected"
3. Enter password
4. Save

Settings:
- Session duration (how long access remains valid)
- Max attempts (brute-force protection)

#### Reading Progress

Automatically enabled when premium is active:
- Saves current page, scroll position, zoom level
- Prompts user to resume on return
- Works for logged-in and anonymous users

#### XML Sitemap

Available at: `/pdf/sitemap.xml`

Features:
- All published PDFs included
- PDF metadata (title, description, thumbnail)
- XSL-styled browser view
- Auto-updates on changes

Submit to Google Search Console for better indexing.

---

### Support

- **Documentation:** https://pdfviewer.drossmedia.de/docs
- **Support:** support@drossmedia.de
- **GitHub:** https://github.com/drossmedia/pdf-embed-seo-optimize

---

*Made with love by [Dross:Media](https://dross.net/media/)*

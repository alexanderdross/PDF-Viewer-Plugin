# PDF Embed & SEO Optimize - Documentation

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize" width="100">
</p>

Complete guide for installing, configuring, and using the PDF Embed & SEO Optimize plugin.

**Website:** [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de) | **Version:** 1.2.4

---

## Table of Contents

1. [Installation](#installation)
2. [Quick Start](#quick-start)
3. [Configuration](#configuration)
4. [Using the Plugin](#using-the-plugin)
5. [REST API Reference](#rest-api-reference)
6. [Premium Features](#premium-features)
7. [Developer Guide](#developer-guide)
8. [Troubleshooting](#troubleshooting)

---

## Installation

### WordPress Installation

#### Via WordPress Admin
1. Go to **Plugins > Add New**
2. Search for "PDF Embed SEO Optimize"
3. Click **Install Now**, then **Activate**

#### Via WP-CLI
```bash
wp plugin install pdf-embed-seo-optimize --activate
```

#### Manual Installation
1. Download the plugin ZIP from https://pdfviewer.drossmedia.de
2. Go to **Plugins > Add New > Upload Plugin**
3. Upload the ZIP file and activate

### Drupal Installation

#### Via Composer (Recommended)
```bash
composer require drossmedia/pdf_embed_seo
drush en pdf_embed_seo -y
```

#### Manual Installation
1. Download and extract to `/modules/contrib/pdf_embed_seo`
2. Go to **Admin > Extend**
3. Enable "PDF Embed & SEO Optimize"

### Premium Installation

#### WordPress
1. Download premium ZIP from your account
2. Upload to `/wp-content/plugins/pdf-embed-seo-optimize/premium/`
3. Go to **PDF Documents > License**
4. Enter your license key and activate

#### Drupal
1. Download premium module
2. Place in `/modules/contrib/pdf_embed_seo/modules/pdf_embed_seo_premium/`
3. Enable via **Admin > Extend**
4. Configure at **Admin > Configuration > Content > PDF Premium Settings**

---

## Quick Start

### WordPress

1. **Add a PDF Document**
   - Go to **PDF Documents > Add New**
   - Enter title and description
   - Upload PDF file in the sidebar
   - Set Print/Download permissions
   - Click **Publish**

2. **View Your PDF**
   - Visit `yoursite.com/pdf/your-document-slug/`
   - Or click **View** from the admin list

3. **Embed in a Page**
   - Edit any page/post
   - Add "PDF Viewer" block
   - Select your PDF document
   - Publish

### Drupal

1. **Add a PDF Document**
   - Go to **Content > PDF Documents > Add**
   - Fill in title and description
   - Upload PDF file
   - Configure permissions
   - Save

2. **View Your PDF**
   - Visit `yoursite.com/pdf/your-document-slug`
   - Or click **View** from admin list

3. **Place a Block**
   - Go to **Structure > Block Layout**
   - Place "PDF Viewer" block in desired region
   - Configure block settings

---

## Configuration

### WordPress Settings

Navigate to: **PDF Documents > Settings**

| Setting | Default | Description |
|---------|---------|-------------|
| Allow Download by Default | Yes | New PDFs allow downloads |
| Allow Print by Default | Yes | New PDFs allow printing |
| Auto-generate Thumbnails | Yes | Create thumbnails from PDF first page |
| Viewer Theme | Light | Light or Dark theme |
| Viewer Height | 800px | Default viewer height |
| Archive Posts per Page | 12 | PDFs shown per archive page |

### Drupal Settings

Navigate to: **Admin > Configuration > Content > PDF Embed & SEO**

| Setting | Default | Description |
|---------|---------|-------------|
| Default Allow Download | Yes | New PDFs allow downloads |
| Default Allow Print | Yes | New PDFs allow printing |
| Auto-generate Thumbnails | Yes | Create thumbnails automatically |
| Viewer Theme | Light | Light or Dark theme |
| Viewer Height | 800px | Default viewer height |
| Archive Display Mode | Grid | Grid or List layout |
| Documents per Page | 12 | PDFs per archive page |

---

## Using the Plugin

### PDF Archive Page

Your PDF archive is automatically available at:
- **WordPress**: `yoursite.com/pdf/`
- **Drupal**: `yoursite.com/pdf`

Features:
- Paginated list of all published PDFs
- Grid or list display modes
- Search functionality
- Sort by date, title, or views

### Single PDF Page

Each PDF has its own SEO-friendly page:
- **URL**: `yoursite.com/pdf/document-slug/`
- Full PDF viewer with PDF.js
- Schema.org DigitalDocument markup
- GEO/AEO/LLM optimized schema
- Social sharing meta tags (Open Graph & Twitter Cards)

### SEO & AI Optimization

The plugin includes comprehensive SEO and AI optimization:

#### Schema.org Structured Data
- **DigitalDocument** with full metadata (name, description, author, dates)
- **SpeakableSpecification** for voice assistants (Alexa, Google Assistant)
- **potentialAction** (ReadAction, DownloadAction, ViewAction, SearchAction)
- **accessibilityFeature** and **accessMode** for accessibility

#### Social Meta Tags
Open Graph and Twitter Card meta tags are output automatically:
- With Yoast SEO: Uses Yoast's output
- Without Yoast: Plugin outputs standalone meta tags
  - `og:type`, `og:title`, `og:description`, `og:url`, `og:image`
  - `twitter:card`, `twitter:title`, `twitter:description`, `twitter:image`

#### GEO/AEO/LLM Optimization
Optimized for generative and answer engines:
- Speakable content selectors for voice search
- Learning resource type and genre classification
- Keywords from tags, topics from categories
- Structured actions for AI understanding

### Shortcodes (WordPress)

#### Basic PDF Viewer
```
[pdf_viewer id="123"]
```

#### With Custom Dimensions
```
[pdf_viewer id="123" width="100%" height="600px"]
```

#### PDF Sitemap List
```
[pdf_viewer_sitemap orderby="title" order="ASC" limit="10"]
```

### Gutenberg Block (WordPress)

1. In the block editor, click **+** to add block
2. Search for "PDF Viewer"
3. Select PDF document from dropdown
4. Adjust settings in block sidebar

### Drupal Block

1. Go to **Structure > Block Layout**
2. Click **Place block** in desired region
3. Select "PDF Viewer"
4. Configure:
   - Select PDF document
   - Set viewer height
   - Toggle title visibility
5. Save block

---

## REST API Reference

### Base URLs

| Platform | Base URL |
|----------|----------|
| WordPress | `/wp-json/pdf-embed-seo/v1/` |
| Drupal | `/api/pdf-embed-seo/v1/` |

### Authentication

- **Public endpoints**: No authentication required
- **Admin endpoints**: Requires authentication
  - WordPress: Use nonce or Application Passwords
  - Drupal: Use session cookie or OAuth

---

### Public Endpoints

#### GET /documents

List all published PDF documents.

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | int | 1 | Page number |
| `per_page` | int | 10 | Items per page (max 100) |
| `search` | string | - | Search term |
| `orderby` | string | date | Sort: date, title, modified, views |
| `order` | string | desc | Direction: asc, desc |

**Example Request:**
```bash
curl -X GET "https://example.com/wp-json/pdf-embed-seo/v1/documents?per_page=5&orderby=views&order=desc"
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
      "excerpt": "Company annual report...",
      "date": "2024-01-15T10:30:00+00:00",
      "modified": "2024-06-20T14:45:00+00:00",
      "views": 1542,
      "thumbnail": "https://example.com/uploads/thumb.jpg",
      "allow_download": true,
      "allow_print": false
    }
  ],
  "total": 45,
  "pages": 9
}
```

---

#### GET /documents/{id}

Get single PDF document details.

**Example Request:**
```bash
curl -X GET "https://example.com/wp-json/pdf-embed-seo/v1/documents/123"
```

**Example Response:**
```json
{
  "id": 123,
  "title": "Annual Report 2024",
  "slug": "annual-report-2024",
  "url": "https://example.com/pdf/annual-report-2024/",
  "description": "Full company annual report for fiscal year 2024...",
  "date": "2024-01-15T10:30:00+00:00",
  "modified": "2024-06-20T14:45:00+00:00",
  "views": 1542,
  "thumbnail": "https://example.com/uploads/thumb.jpg",
  "allow_download": true,
  "allow_print": false,
  "author": {
    "id": 1,
    "name": "Admin"
  }
}
```

---

#### GET /documents/{id}/data

Get PDF file URL securely (for viewer integration).

**Example Request:**
```bash
curl -X GET "https://example.com/wp-json/pdf-embed-seo/v1/documents/123/data"
```

**Example Response:**
```json
{
  "id": 123,
  "pdf_url": "https://example.com/wp-content/uploads/2024/01/report.pdf",
  "allow_download": true,
  "allow_print": false
}
```

---

#### POST /documents/{id}/view

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

---

#### GET /settings

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

### Premium Endpoints

#### GET /analytics

Get analytics overview (requires admin authentication).

**Parameters:**

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

---

#### GET/POST /documents/{id}/progress

Get or save reading progress.

**GET Response:**
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

**POST Request Body:**
```json
{
  "page": 15,
  "scroll": 0.45,
  "zoom": 1.25
}
```

---

#### POST /documents/{id}/verify-password

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

---

## Premium Features

### Analytics Dashboard

**WordPress:** PDF Documents > Analytics
**Drupal:** Admin > Reports > PDF Analytics

Features:
- Total views and unique visitors chart
- Popular documents ranking
- Recent views log with details
- Time period filters
- Export to CSV or JSON

### Password Protection

1. Edit a PDF document
2. Check "Password Protected"
3. Enter password
4. Save

Settings:
- Session duration (default: 1 hour)
- Max attempts before lockout (default: 5)

### Reading Progress

Automatically enabled with premium:
- Saves page, scroll, and zoom
- Prompts to resume on return
- Works for guests (session) and users (database)

### XML Sitemap

Available at: `/pdf/sitemap.xml`

- All published PDFs included
- XSL-styled browser view
- Submit to Google Search Console

### AI & Voice Search Optimization (GEO/AEO/LLM)

Premium users get access to an advanced "AI & Schema Optimization" meta box when editing PDFs:

#### AI Summary & Key Points
- **AI Summary (TL;DR)**: A concise 1-2 sentence summary for AI assistants
- **Key Points**: Bullet points that AI can use for quick answers
- Schema: `abstract`, `ItemList` with key takeaways

#### Document Metadata
- **Reading Time**: Estimated minutes to read → `timeRequired: PT10M`
- **Difficulty Level**: Beginner/Intermediate/Advanced/Expert → `educationalLevel`
- **Document Type**: Guide, Whitepaper, Report, E-Book, etc. → `additionalType`
- **Target Audience**: Who the document is for → `audience` schema

#### FAQ Schema
- Add Question/Answer pairs
- Outputs separate `FAQPage` schema
- Appears in Google FAQ rich results
- Voice assistants can directly answer questions

#### Table of Contents
- Add sections with page numbers
- Creates `hasPart` schema with deep links
- Enables structured navigation for AI crawlers

#### Educational Content
- **Prerequisites**: What readers should know first → `coursePrerequisites`
- **Learning Outcomes**: What readers will learn → `teaches`

#### Custom Speakable
- Define priority content for voice search
- Takes precedence over auto-detected content

#### Related Documents
- Link to related PDFs → `isRelatedTo` schema
- Helps AI understand content relationships

---

## Developer Guide

### WordPress Hooks

#### Actions
```php
// After PDF is viewed
add_action( 'pdf_embed_seo_pdf_viewed', function( $post_id, $count ) {
    // Your code here
}, 10, 2 );
```

#### Filters
```php
// Modify Schema.org data
add_filter( 'pdf_embed_seo_schema_data', function( $schema, $post_id ) {
    $schema['author'] = [
        '@type' => 'Person',
        'name'  => get_post_meta( $post_id, '_author', true ),
    ];
    return $schema;
}, 10, 2 );

// Modify REST API response
add_filter( 'pdf_embed_seo_rest_document', function( $data, $post, $detailed ) {
    $data['custom_field'] = get_post_meta( $post->ID, '_custom', true );
    return $data;
}, 10, 3 );
```

### Drupal Hooks

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
```

### Template Overrides

**WordPress:**
Copy to your theme:
- `single-pdf_document.php` - Single PDF page
- `archive-pdf_document.php` - Archive page

**Drupal:**
Copy to your theme:
- `pdf-document.html.twig`
- `pdf-viewer.html.twig`
- `pdf-archive.html.twig`

### CSS Classes

| Class | Description |
|-------|-------------|
| `.pdf-viewer-wrapper` | Main viewer container |
| `.pdf-viewer-toolbar` | Toolbar area |
| `.pdf-viewer-container` | PDF canvas |
| `.pdf-viewer-theme-light` | Light theme |
| `.pdf-viewer-theme-dark` | Dark theme |
| `.pdf-archive` | Archive wrapper |
| `.pdf-archive-item` | Archive item |

### JavaScript Events

```javascript
document.addEventListener('pdfLoaded', function(e) {
  console.log('PDF loaded:', e.detail.documentId);
});

document.addEventListener('pageChanged', function(e) {
  console.log('Page changed to:', e.detail.page);
});
```

---

## Troubleshooting

### PDF Not Displaying

1. Check PHP memory limit (minimum 128M)
2. Verify PDF file is valid
3. Check browser console for errors
4. Ensure PDF.js assets are loaded

### Thumbnails Not Generating

1. Verify ImageMagick or Ghostscript is installed
2. Check PHP exec() is enabled
3. Verify temp directory is writable

### REST API Not Working

1. Check permalinks are enabled
2. Verify REST API is not blocked
3. Test with authentication for admin endpoints

### Password Protection Issues

1. Clear browser cookies
2. Check session storage
3. Verify password is saved correctly

---

## Support

- **Website:** https://pdfviewer.drossmedia.de
- **Documentation:** https://pdfviewer.drossmedia.de/documentation/
- **Support:** support@drossmedia.de

---

*Made with ♥ by [Dross:Media](https://dross.net/media/)*

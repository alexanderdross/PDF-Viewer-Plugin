# PDF Embed & SEO Optimize - Multi-Platform Documentation

A comprehensive PDF management solution available for WordPress and Drupal that uses Mozilla's PDF.js library to securely display PDFs with SEO optimization.

**Current Version:** 1.2.7
**Platforms:** WordPress (Free & Premium), Drupal 10/11
**License:** GPL v2 or later

---

## Project Overview

This project provides four modules:

| Module | Directory | Platform | Features |
|--------|-----------|----------|----------|
| WP Free | `pdf-embed-seo-optimize/` | WordPress 5.8+ | Core PDF viewer, SEO, REST API |
| WP Premium | `pdf-embed-seo-optimize/premium/` | WordPress 5.8+ | Analytics, passwords, progress, sitemap |
| Drupal Free | `drupal-pdf-embed-seo/` | Drupal 10/11 | Core PDF viewer, SEO, REST API |
| Drupal Premium | `drupal-pdf-embed-seo/modules/pdf_embed_seo_premium/` | Drupal 10/11 | Analytics, passwords, progress, sitemap |

---

## User Guide (WordPress)

### Creating a PDF Document

When you create a new PDF Document (**PDF Documents → Add New**), use the **PDF File** meta box to upload or select your PDF file.

**Important:** The PDF is automatically displayed on its dedicated page (e.g., `/pdf/your-document-title/`). You do NOT need to add any shortcode in the content area.

| Element | Purpose |
|---------|---------|
| **Title** | The document title (appears in URL, breadcrumbs, and SEO) |
| **Content Editor** | Optional description text shown below the PDF viewer |
| **PDF File Meta Box** | Upload/select the PDF file to display |
| **PDF Settings** | Control download/print permissions |
| **PDF Cover Image** | Featured image for archive listings and social sharing |
| **Excerpt** | Short description for archive listings |

### Embedding PDFs on Other Pages (Shortcodes)

Use shortcodes to embed an **existing PDF Document** into any page, post, or widget area.

#### `[pdf_viewer]` - Embed a PDF Viewer

```
[pdf_viewer id="123"]
[pdf_viewer id="123" width="100%" height="600px"]
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `id` | (required) | The PDF Document post ID |
| `width` | `100%` | Viewer width (CSS value) |
| `height` | `800px` | Viewer height (CSS value) |

**Example:** To embed PDF Document #561 on your homepage:
1. Go to **Pages → Edit Homepage**
2. Add the shortcode: `[pdf_viewer id="561"]`
3. Save the page

**Note:** The `id` must be a PDF Document ID (found in the URL when editing: `post.php?post=561`), not a Media Library attachment ID.

#### `[pdf_viewer_sitemap]` - List All PDF Documents

```
[pdf_viewer_sitemap]
[pdf_viewer_sitemap orderby="date" order="DESC" limit="10"]
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `orderby` | `title` | Sort by: `title`, `date`, `modified`, `menu_order` |
| `order` | `ASC` | Sort direction: `ASC` or `DESC` |
| `limit` | `-1` | Number of documents (-1 for all) |

### Quick Reference: When to Use What

| Scenario | Solution |
|----------|----------|
| Create a standalone PDF page with its own URL | Create a **PDF Document** (PDF Documents → Add New) |
| Embed a PDF viewer on an existing page/post | Use `[pdf_viewer id="123"]` shortcode |
| Show a list of all PDFs on a page | Use `[pdf_viewer_sitemap]` shortcode |
| Link to the PDF archive | Link to `/pdf/` |

### Common Mistake to Avoid

**Do NOT add `[pdf_viewer]` shortcode inside a PDF Document's content area.** The PDF is already displayed automatically via the PDF File meta box. Adding the shortcode would show the PDF twice.

---

## Architecture Overview

### WordPress Plugin Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    PDF_Embed_SEO (Main)                      │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐ │
│  │  Post Type  │  │  Frontend   │  │      REST API       │ │
│  │  Handler    │  │  Renderer   │  │  (Free Endpoints)   │ │
│  └─────────────┘  └─────────────┘  └─────────────────────┘ │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐ │
│  │    Admin    │  │    Yoast    │  │     Shortcodes      │ │
│  │   Handler   │  │ Integration │  │     & Blocks        │ │
│  └─────────────┘  └─────────────┘  └─────────────────────┘ │
├─────────────────────────────────────────────────────────────┤
│                 PDF_Embed_SEO_Premium (Optional)             │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌─────────────┐ │
│  │ Analytics │ │ Password  │ │  Reading  │ │   Premium   │ │
│  │ Dashboard │ │ Protection│ │  Progress │ │  REST API   │ │
│  └───────────┘ └───────────┘ └───────────┘ └─────────────┘ │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌─────────────┐ │
│  │Taxonomies │ │   Roles   │ │   Bulk    │ │   Sitemap   │ │
│  │ Cat/Tags  │ │  Access   │ │  Import   │ │   (XML)     │ │
│  └───────────┘ └───────────┘ └───────────┘ └─────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Drupal Module Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                  pdf_embed_seo Module (Free)                 │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐  ┌─────────────────┐                   │
│  │  PdfDocument    │  │  Controllers    │                   │
│  │  Entity         │  │  (View/Archive) │                   │
│  └─────────────────┘  └─────────────────┘                   │
│  ┌─────────────────┐  ┌─────────────────┐                   │
│  │  REST Resources │  │    Services     │                   │
│  │  (Basic API)    │  │  (Thumbnails)   │                   │
│  └─────────────────┘  └─────────────────┘                   │
│  ┌─────────────────┐  ┌─────────────────┐                   │
│  │  Block Plugin   │  │     Forms       │                   │
│  │  (PDF Viewer)   │  │  (Settings)     │                   │
│  └─────────────────┘  └─────────────────┘                   │
├─────────────────────────────────────────────────────────────┤
│              pdf_embed_seo_premium (Optional)                │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌─────────────┐ │
│  │ Analytics │ │ Password  │ │  Reading  │ │   Premium   │ │
│  │ Dashboard │ │ Protection│ │  Progress │ │  REST API   │ │
│  └───────────┘ └───────────┘ └───────────┘ └─────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

---

## File Structure

### WordPress Plugin (Free)

```
pdf-embed-seo-optimize/
├── pdf-embed-seo-optimize.php           # Main plugin file (v1.2.0)
├── uninstall.php                        # Cleanup on uninstall
├── README.txt                           # WordPress.org readme
├── LICENSE                              # GPL v2 or later
│
├── includes/
│   ├── class-pdf-embed-seo-optimize-post-type.php    # CPT registration
│   ├── class-pdf-embed-seo-optimize-admin.php        # Admin functionality
│   ├── class-pdf-embed-seo-optimize-frontend.php     # Frontend rendering
│   ├── class-pdf-embed-seo-optimize-yoast.php        # Yoast SEO integration
│   ├── class-pdf-embed-seo-optimize-shortcodes.php   # Shortcode handlers
│   ├── class-pdf-embed-seo-optimize-block.php        # Gutenberg block
│   ├── class-pdf-embed-seo-optimize-thumbnail.php    # Thumbnail generation
│   └── class-pdf-embed-seo-optimize-rest-api.php     # REST API (Free)
│
├── admin/
│   ├── css/admin-styles.css
│   ├── js/admin-scripts.js
│   └── views/
│       ├── meta-box-pdf-settings.php
│       ├── settings-page.php
│       └── docs-page.php                # Documentation with premium CTA
│
├── public/
│   ├── css/viewer-styles.css
│   ├── js/viewer-scripts.js
│   └── views/
│       ├── single-pdf-document.php
│       └── archive-pdf-document.php
│
├── assets/pdfjs/                        # PDF.js library (bundled)
│
└── languages/
    └── pdf-embed-seo-optimize.pot
```

### WordPress Plugin (Premium)

```
pdf-embed-seo-optimize/premium/
├── class-pdf-embed-seo-premium.php              # Premium loader (v1.2.0)
├── COMPARISON.md                                # Free vs Pro comparison
│
├── includes/
│   ├── class-pdf-embed-seo-premium-admin.php        # Premium admin UI
│   ├── class-pdf-embed-seo-premium-analytics.php    # Analytics dashboard
│   ├── class-pdf-embed-seo-premium-bulk.php         # Bulk import
│   ├── class-pdf-embed-seo-premium-password.php     # Password protection
│   ├── class-pdf-embed-seo-premium-roles.php        # Role-based access
│   ├── class-pdf-embed-seo-premium-sitemap.php      # XML sitemap
│   ├── class-pdf-embed-seo-premium-taxonomies.php   # Categories & tags
│   ├── class-pdf-embed-seo-premium-viewer.php       # Enhanced viewer
│   └── class-pdf-embed-seo-premium-rest-api.php     # Premium REST API
│
└── assets/
    ├── css/premium-admin.css
    ├── css/premium-viewer.css
    ├── js/premium-admin.js
    ├── js/premium-viewer.js
    └── sitemap-style.xsl
```

### Drupal Module (Free)

```
drupal-pdf-embed-seo/
├── pdf_embed_seo.info.yml               # Module info (v1.2.0)
├── pdf_embed_seo.module                 # Hook implementations
├── pdf_embed_seo.install                # Install/uninstall hooks
├── pdf_embed_seo.routing.yml            # Route definitions
├── pdf_embed_seo.services.yml           # Service definitions
├── pdf_embed_seo.permissions.yml        # Permission definitions
├── pdf_embed_seo.libraries.yml          # Asset libraries
├── pdf_embed_seo.links.menu.yml         # Admin menu links
├── pdf_embed_seo.links.action.yml       # Action links
├── README.md                            # Documentation
│
├── config/
│   ├── install/pdf_embed_seo.settings.yml
│   └── schema/pdf_embed_seo.schema.yml
│
├── src/
│   ├── Entity/
│   │   ├── PdfDocument.php              # Entity class
│   │   └── PdfDocumentInterface.php     # Entity interface
│   │
│   ├── Controller/
│   │   ├── PdfViewController.php        # Single PDF view
│   │   ├── PdfArchiveController.php     # Archive listing
│   │   ├── PdfDataController.php        # AJAX data endpoint
│   │   └── PdfApiController.php         # REST API controller
│   │
│   ├── Form/
│   │   ├── PdfDocumentForm.php          # Entity form
│   │   └── PdfEmbedSeoSettingsForm.php  # Settings form
│   │
│   ├── Plugin/
│   │   ├── Block/PdfViewerBlock.php     # Block plugin
│   │   └── rest/resource/
│   │       ├── PdfDocumentResource.php  # Documents REST
│   │       └── PdfDataResource.php      # Data REST
│   │
│   ├── Service/
│   │   └── PdfThumbnailGenerator.php    # Thumbnail service
│   │
│   ├── PdfDocumentAccessControlHandler.php
│   └── PdfDocumentListBuilder.php
│
├── templates/
│   ├── pdf-document.html.twig
│   ├── pdf-viewer.html.twig
│   ├── pdf-archive.html.twig
│   └── pdf-archive-item.html.twig
│
├── assets/
│   ├── css/
│   │   ├── pdf-viewer.css
│   │   ├── pdf-viewer-dark.css
│   │   ├── pdf-archive.css
│   │   └── pdf-admin.css
│   └── js/
│       ├── pdf-viewer.js
│       └── pdf-admin.js
│
├── modules/
│   └── pdf_embed_seo_premium/           # Premium submodule
│       └── (see below)
│
└── tests/qa/
    └── UAT-TEST-PLAN-DRUPAL.md
```

### Drupal Module (Premium)

```
drupal-pdf-embed-seo/modules/pdf_embed_seo_premium/
├── pdf_embed_seo_premium.info.yml       # Module info (v1.2.0)
├── pdf_embed_seo_premium.module         # Hook implementations
├── pdf_embed_seo_premium.install        # Install/uninstall hooks
├── pdf_embed_seo_premium.routing.yml    # Route definitions
├── pdf_embed_seo_premium.services.yml   # Service definitions
├── pdf_embed_seo_premium.permissions.yml# Permission definitions
├── pdf_embed_seo_premium.links.menu.yml # Admin menu links
├── README.md                            # Documentation
│
├── config/
│   ├── install/pdf_embed_seo_premium.settings.yml
│   └── schema/pdf_embed_seo_premium.schema.yml
│
├── src/
│   ├── Controller/
│   │   ├── PdfAnalyticsController.php   # Analytics dashboard
│   │   └── PdfPremiumApiController.php  # Premium REST API
│   │
│   ├── Form/
│   │   └── PdfPremiumSettingsForm.php   # Premium settings
│   │
│   └── Service/
│       ├── PdfAnalyticsTracker.php      # Analytics service
│       └── PdfProgressTracker.php       # Progress service
│
└── templates/
    ├── pdf-analytics-dashboard.html.twig
    └── pdf-password-form.html.twig
```

---

## REST API Reference

### API Base URLs

| Platform | Base URL |
|----------|----------|
| WordPress | `/wp-json/pdf-embed-seo/v1/` |
| Drupal | `/api/pdf-embed-seo/v1/` |

### Public Endpoints (Free)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/documents` | List all published PDFs | None |
| `GET` | `/documents/{id}` | Get single PDF details | None |
| `GET` | `/documents/{id}/data` | Get PDF file URL securely | None |
| `POST` | `/documents/{id}/view` | Track PDF view | None |
| `GET` | `/settings` | Get public settings | None |

### Premium Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/analytics` | Analytics overview | Admin |
| `GET` | `/analytics/documents` | Per-document analytics | Admin |
| `GET` | `/analytics/export` | Export analytics CSV/JSON | Admin |
| `GET` | `/documents/{id}/progress` | Get reading progress | None |
| `POST` | `/documents/{id}/progress` | Save reading progress | None |
| `POST` | `/documents/{id}/verify-password` | Verify PDF password | None |
| `POST` | `/documents/{id}/download` | Track PDF download | None |
| `POST` | `/documents/{id}/expiring-link` | Generate expiring access link | Admin |
| `GET` | `/documents/{id}/expiring-link/{token}` | Validate expiring link | None |
| `GET` | `/categories` | List PDF categories | None |
| `GET` | `/tags` | List PDF tags | None |
| `POST` | `/bulk/import` | Start bulk import | Admin |
| `GET` | `/bulk/import/status` | Get import status | Admin |

### Query Parameters for `/documents`

| Parameter | Default | Description |
|-----------|---------|-------------|
| `page` | 1 | Page number |
| `per_page` | 10 | Items per page (max 100) |
| `search` | - | Search term |
| `orderby` | date | Sort: date, title, modified, views |
| `order` | desc | Sort direction: asc, desc |

### Response Format

```json
{
  "id": 123,
  "title": "Document Title",
  "slug": "document-slug",
  "url": "https://site.com/pdf/document-slug/",
  "excerpt": "Description...",
  "date": "2024-01-15T10:30:00+00:00",
  "modified": "2024-06-20T14:45:00+00:00",
  "views": 1542,
  "thumbnail": "https://site.com/uploads/thumb.jpg",
  "allow_download": true,
  "allow_print": false
}
```

---

## WordPress Hooks Reference

### Actions

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_pdf_viewed` | `$post_id, $count` | PDF was viewed |
| `pdf_embed_seo_premium_init` | - | Premium features initialized |
| `pdf_embed_seo_optimize_settings_saved` | `$post_id, $settings` | Settings saved (renamed in v1.2.6) |

### Filters

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_post_type_args` | `$args` | Modify CPT registration |
| `pdf_embed_seo_schema_data` | `$schema, $post_id` | Modify Schema.org data |
| `pdf_embed_seo_archive_schema_data` | `$schema` | Modify archive schema |
| `pdf_embed_seo_archive_query` | `$posts_per_page` | Modify archive query |
| `pdf_embed_seo_archive_title` | `$title` | Modify archive title |
| `pdf_embed_seo_archive_description` | `$description` | Modify archive description |
| `pdf_embed_seo_sitemap_query_args` | `$query_args, $atts` | Modify sitemap query |
| `pdf_embed_seo_viewer_options` | `$options, $post_id` | Modify viewer options |
| `pdf_embed_seo_allowed_types` | `$types` | Modify allowed MIME types |
| `pdf_embed_seo_rest_document` | `$data, $post, $detailed` | Modify REST response |
| `pdf_embed_seo_rest_document_data` | `$data, $post_id` | Modify REST data response |
| `pdf_embed_seo_rest_settings` | `$settings` | Modify REST settings |

### Premium Filters

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_password_error` | `$error` | Custom password error |
| `pdf_embed_seo_verify_password` | `$is_valid, $post_id, $password` | Override password check |
| `pdf_embed_seo_rest_analytics` | `$data, $period` | Modify analytics response |

---

## Drupal Hooks Reference

### Alter Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_document_data_alter` | Modify API document data |
| `hook_pdf_embed_seo_api_settings_alter` | Modify API settings |
| `hook_pdf_embed_seo_verify_password_alter` | Override password verification |
| `hook_pdf_embed_seo_viewer_options_alter` | Modify viewer options |
| `hook_pdf_embed_seo_schema_alter` | Modify Schema.org output |

### Event Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_view_tracked` | PDF view was tracked |
| `hook_pdf_embed_seo_document_saved` | PDF document saved |

---

## Data Models

### WordPress Post Meta

| Meta Key | Type | Description |
|----------|------|-------------|
| `_pdf_file_id` | int | Attachment ID |
| `_pdf_file_url` | string | Direct PDF URL (internal) |
| `_pdf_allow_download` | bool | Allow download |
| `_pdf_allow_print` | bool | Allow print |
| `_pdf_standalone_mode` | bool | Standalone fullscreen mode |
| `_pdf_view_count` | int | View count |
| `_pdf_download_count` | int | Download count (Premium) |
| `_pdf_password_protected` | bool | Password enabled (Premium) |
| `_pdf_password` | string | Hashed password (Premium) |
| `_pdf_ai_summary` | string | AI summary/TL;DR (Premium) |
| `_pdf_key_points` | string | Key takeaways (Premium) |
| `_pdf_reading_time` | int | Reading time in minutes (Premium) |
| `_pdf_difficulty_level` | string | Difficulty level (Premium) |
| `_pdf_document_type` | string | Document type (Premium) |
| `_pdf_target_audience` | string | Target audience (Premium) |
| `_pdf_faq_items` | array | FAQ Q&A pairs (Premium) |
| `_pdf_toc_items` | array | Table of contents (Premium) |
| `_pdf_custom_speakable` | string | Custom speakable content (Premium) |
| `_pdf_related_documents` | array | Related PDF IDs (Premium) |
| `_pdf_prerequisites` | string | Prerequisites (Premium) |
| `_pdf_learning_outcomes` | string | Learning outcomes (Premium) |

### WordPress Options

| Option | Description |
|--------|-------------|
| `pdf_embed_seo_settings` | Serialized plugin settings |
| `pdf_embed_seo_version` | Current version |
| `pdf_embed_seo_premium_license_status` | License status (Premium) |
| `pdf_embed_seo_premium_settings` | Premium settings (Premium) |

### Drupal Entity Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | int | Entity ID |
| `uuid` | string | UUID |
| `title` | string | Document title |
| `description` | text | Description |
| `pdf_file` | file | PDF file reference |
| `thumbnail` | image | Thumbnail image |
| `slug` | string | URL slug |
| `allow_download` | boolean | Allow download |
| `allow_print` | boolean | Allow print |
| `view_count` | integer | View count |
| `password_protected` | boolean | Password enabled |
| `password` | string | Hashed password |
| `status` | boolean | Published status |
| `created` | timestamp | Created date |
| `changed` | timestamp | Modified date |

---

## URL Structure

| Page | WordPress | Drupal |
|------|-----------|--------|
| Archive | `/pdf/` | `/pdf` |
| Single PDF | `/pdf/{slug}/` | `/pdf/{slug}` |
| XML Sitemap (Premium) | `/pdf/sitemap.xml` | `/pdf/sitemap.xml` |
| Admin List | `/wp-admin/edit.php?post_type=pdf_document` | `/admin/content/pdf-documents` |
| Settings | `/wp-admin/edit.php?post_type=pdf_document&page=pdf-embed-seo-settings` | `/admin/config/content/pdf-embed-seo` |
| Analytics | `/wp-admin/edit.php?post_type=pdf_document&page=pdf-analytics` | `/admin/reports/pdf-analytics` |

---

## Feature Matrix

### Viewer & Display

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Mozilla PDF.js Viewer (v4.0) | ✓ | ✓ |
| Light Theme | ✓ | ✓ |
| Dark Theme | ✓ | ✓ |
| Responsive Design | ✓ | ✓ |
| Print Control (per PDF) | ✓ | ✓ |
| Download Control (per PDF) | ✓ | ✓ |
| Configurable Viewer Height | ✓ | ✓ |
| Gutenberg Block (WP) | ✓ | ✓ |
| PDF Viewer Block (Drupal) | ✓ | ✓ |
| Shortcodes (WP) | ✓ | ✓ |
| Text Search in Viewer | - | ✓ |
| Bookmark Navigation | - | ✓ |

### Content Management

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Custom Post Type / Entity | ✓ | ✓ |
| Title, Description, Slug | ✓ | ✓ |
| File Upload & Management | ✓ | ✓ |
| Featured Image / Thumbnail | ✓ | ✓ |
| Auto-Generate Thumbnails | ✓ | ✓ |
| Published/Draft Status | ✓ | ✓ |
| Owner/Author Tracking | ✓ | ✓ |
| Admin List with Columns | ✓ | ✓ |
| Quick Edit Support (WP) | ✓ | ✓ |
| Multi-language Support | ✓ | ✓ |
| Categories Taxonomy | - | ✓ |
| Tags Taxonomy | - | ✓ |
| Role-Based Access Control | - | ✓ |
| Bulk Edit Actions | - | ✓ |
| Bulk Import (CSV/ZIP) | - | ✓ |

### SEO & URLs

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Clean URL Structure (`/pdf/slug/`) | ✓ | ✓ |
| Auto Path/Slug Generation | ✓ | ✓ |
| Schema.org DigitalDocument | ✓ | ✓ |
| Schema.org CollectionPage | ✓ | ✓ |
| Yoast SEO Integration (WP) | ✓ | ✓ |
| OpenGraph Meta Tags | ✓ | ✓ |
| Twitter Card Support | ✓ | ✓ |
| GEO/AEO Basic (speakable, potentialAction) | ✓ | ✓ |
| XML Sitemap (`/pdf/sitemap.xml`) | - | ✓ |
| Sitemap XSL Stylesheet | - | ✓ |
| Search Engine Ping | - | ✓ |

### GEO/AEO/LLM Optimization (Premium)

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Basic Speakable Schema | ✓ | ✓ |
| potentialAction (Read/Download) | ✓ | ✓ |
| accessMode & accessibilityFeature | ✓ | ✓ |
| **AI Summary (TL;DR)** | - | ✓ |
| **Key Points / Takeaways** | - | ✓ |
| **FAQ Schema (FAQPage)** | - | ✓ |
| **Table of Contents Schema** | - | ✓ |
| **Reading Time Estimate** | - | ✓ |
| **Difficulty Level** | - | ✓ |
| **Document Type Classification** | - | ✓ |
| **Target Audience** | - | ✓ |
| **Custom Speakable Content** | - | ✓ |
| **Related Documents Schema** | - | ✓ |
| **Prerequisites & Learning Outcomes** | - | ✓ |

### Archive & Listing

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Archive Page (`/pdf`) | ✓ | ✓ |
| Pagination Support | ✓ | ✓ |
| Grid/List Display Modes | ✓ | ✓ |
| Sorting Options | ✓ | ✓ |
| Search Filtering | ✓ | ✓ |
| Category Filter | - | ✓ |
| Tag Filter | - | ✓ |

### REST API

| Feature | Free | Premium |
|---------|:----:|:-------:|
| GET /documents (list) | ✓ | ✓ |
| GET /documents/{id} (single) | ✓ | ✓ |
| GET /documents/{id}/data (secure) | ✓ | ✓ |
| POST /documents/{id}/view (track) | ✓ | ✓ |
| GET /settings | ✓ | ✓ |
| GET /analytics | - | ✓ |
| GET /analytics/documents | - | ✓ |
| GET /analytics/export | - | ✓ |
| GET/POST /documents/{id}/progress | - | ✓ |
| POST /documents/{id}/verify-password | - | ✓ |
| POST /documents/{id}/download | - | ✓ |
| POST /documents/{id}/expiring-link | - | ✓ |
| GET /documents/{id}/expiring-link/{token} | - | ✓ |
| GET /categories | - | ✓ |
| GET /tags | - | ✓ |
| POST /bulk/import | - | ✓ |

### Statistics & Analytics

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Basic View Counter | ✓ | ✓ |
| View Count Display | ✓ | ✓ |
| Analytics Dashboard | - | ✓ |
| Detailed View Tracking | - | ✓ |
| **Download Tracking** | - | ✓ |
| IP, User Agent, Referrer | - | ✓ |
| Time Spent Tracking | - | ✓ |
| Popular Documents Report | - | ✓ |
| Recent Views Log | - | ✓ |
| Analytics Export (CSV/JSON) | - | ✓ |
| Time Period Filters | - | ✓ |

### Security & Access

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Nonce/CSRF Protection | ✓ | ✓ |
| Permission System | ✓ | ✓ |
| Capability/Access Checks | ✓ | ✓ |
| Secure PDF URL (no direct links) | ✓ | ✓ |
| Input Sanitization | ✓ | ✓ |
| Output Escaping | ✓ | ✓ |
| Password Protection | - | ✓ |
| Password Hashing (secure) | - | ✓ |
| Session-Based Access | - | ✓ |
| Login Requirement Option | - | ✓ |
| Role Restrictions | - | ✓ |
| **Expiring Access Links** | - | ✓ |
| **Time-Limited URLs** | - | ✓ |
| **Max Uses per Link** | - | ✓ |

### Reading Experience

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Page Navigation | ✓ | ✓ |
| Zoom Controls | ✓ | ✓ |
| Full Screen Mode | ✓ | ✓ |
| Reading Progress Tracking | - | ✓ |
| Resume Reading Feature | - | ✓ |
| Page/Scroll/Zoom Save | - | ✓ |

### Developer

| Feature | Free | Premium |
|---------|:----:|:-------:|
| WordPress Hooks (actions/filters) | ✓ | ✓ |
| Drupal Hooks (alter/events) | ✓ | ✓ |
| Template Overrides | ✓ | ✓ |
| CSS Classes for Styling | ✓ | ✓ |
| JavaScript Events | ✓ | ✓ |
| Cache Tags & Contexts | ✓ | ✓ |
| Analytics Tracker Service | - | ✓ |
| Progress Tracker Service | - | ✓ |
| Priority Support | - | ✓ |

---

## Dependencies

### WordPress
- WordPress 5.8+
- PHP 7.4+
- Mozilla PDF.js (bundled)
- Optional: Yoast SEO
- Optional: ImageMagick or Ghostscript (thumbnails)

### Drupal
- Drupal 10 or 11
- PHP 8.1+
- Core modules: node, file, taxonomy, path, path_alias
- Optional: ImageMagick or Ghostscript (thumbnails)

---

## Security Measures

1. **PDF URL Protection**: Direct URLs hidden via AJAX/API
2. **Nonce Verification**: All AJAX requests verified
3. **Capability Checks**: Admin functions require proper permissions
4. **Input Sanitization**: All inputs sanitized
5. **Output Escaping**: All outputs escaped
6. **CSRF Protection**: Forms protected with tokens
7. **Password Hashing**: Passwords stored hashed (Premium)

---

## Premium Purchase URL

**https://pdfviewer.drossmedia.de**

---

## Changelog

### 1.2.7 (Current)
- Sidebar/Widget Area Removal - PDF pages now display full-width without sidebars
  - WordPress: Removed `get_sidebar()` from archive and single templates
  - WordPress: Added CSS to hide sidebars on archive pages (`.post-type-archive-pdf_document`)
  - Drupal: Added `hook_theme_suggestions_page_alter()` for full-width page templates
  - Drupal: Added `hook_preprocess_page()` to clear sidebar regions on PDF routes
  - Drupal: Added `hook_preprocess_html()` to add `.page-pdf` body classes for CSS targeting
  - Drupal: Added CSS rules to hide common sidebar selectors
- Unit tests for sidebar removal (WordPress and Drupal)
- Archive Page Styling Settings (WordPress)
  - Custom H1 heading for archive page (default: "PDF Documents")
  - Heading alignment options: left, center (default), right
  - Custom font color for archive header
  - Custom background color for archive header
  - Custom heading also updates 2nd breadcrumb item (HTML and Schema.org BreadcrumbList)
- Fix "Security check failed" error on cached pages
  - Switched PDF viewer from AJAX to REST API endpoint (`/documents/{id}/data`)
  - REST API doesn't require nonces for public read operations, fixing cache compatibility
  - Updated both single page viewer and shortcode implementations

### 1.2.6
- WordPress Plugin Check compliance fixes:
  - Fixed unescaped SQL table name parameters in premium REST API and analytics
  - Fixed interpolated SQL variables with proper `esc_sql()` sanitization
  - Updated `get_posts()` to use `post__not_in` instead of deprecated `exclude` parameter
- Hook renamed: `pdf_embed_seo_settings_saved` → `pdf_embed_seo_optimize_settings_saved`
- Drupal security fixes:
  - Implemented proper password hashing using Drupal's password service
  - Fixed XSS vulnerability in PdfViewerBlock with proper HTML escaping

### 1.2.5
- Download Tracking - Track PDF downloads separately from views
- Expiring Access Links - Generate time-limited URLs for PDFs with max usage limits
- Drupal Premium feature parity with WordPress:
  - Schema Optimization (GEO/AEO/LLM) service
  - Role-Based Access Control service
  - Bulk Import operations service
  - Viewer Enhancements (search, bookmarks) service
- Extended Drupal REST API with 14+ new endpoints matching WordPress
- New REST endpoints: `/documents/{id}/download`, `/documents/{id}/expiring-link`

### 1.2.4
- Premium AI & Schema Optimization meta box for GEO/AEO/LLM optimization
- AI Summary, FAQ Schema, Table of Contents, Reading Time, Difficulty Level
- Target Audience, Prerequisites, Learning Outcomes schema fields
- Custom Speakable Content and Related Documents
- AI Optimization preview meta box for free users
- Premium settings preview on free version settings page

### 1.2.3
- GEO/AEO/LLM schema optimization (SpeakableSpecification, potentialAction, accessMode)
- Standalone Open Graph and Twitter Card meta tags (without Yoast)
- Enhanced DigitalDocument schema (identifier, fileFormat, inLanguage, publisher)
- Plugin Check compliance fixes (escaping, direct file access protection)

### 1.2.2
- Archive display options (list/grid views)
- Schema.org BreadcrumbList markup
- Visible breadcrumb navigation with accessibility support
- Archive page redirect feature (Premium)

### 1.2.1
- Version bump for release
- Documentation improvements

### 1.2.0
- Added REST API endpoints for all platforms
- Added reading progress tracking (Premium)
- Added password verification endpoint (Premium)
- Added XML Sitemap at `/pdf/sitemap.xml` (Premium)
- Separated Drupal into free base + premium submodule
- Added comprehensive developer documentation
- Added premium upgrade CTA to docs page
- Updated feature comparison tables

### 1.1.5
- Version sync across all modules
- Bug fixes and improvements

### 1.1.0
- Added UAT/QA test documentation
- Added Drupal module

### 1.0.0
- Initial release
- WordPress plugin with PDF.js viewer
- Yoast SEO integration
- Print/download controls

---

## Credits

Made with ♥ by [Dross:Media](https://dross.net/media/)

**License:** GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

# PDF Embed & SEO Optimize for Drupal

A powerful Drupal module that integrates Mozilla's PDF.js viewer to display PDFs with clean URLs, SEO optimization, and access controls.

**Current Version:** 1.2.0
**Platforms:** Drupal 10, Drupal 11
**License:** GPL v2 or later

## Free vs Premium

This module comes in two parts:
- **pdf_embed_seo** (Free) - Core PDF viewing functionality
- **pdf_embed_seo_premium** (Premium) - Advanced features like analytics, password protection, and reading progress

Get premium at: **https://pdfviewer.drossmedia.de**

---

## Feature Comparison

| Feature | Free | Premium |
|---------|:----:|:-------:|
| **Viewer & Display** | | |
| Mozilla PDF.js Viewer | ✓ | ✓ |
| Light Theme | ✓ | ✓ |
| Dark Theme | ✓ | ✓ |
| Responsive Design | ✓ | ✓ |
| Print Control (per PDF) | ✓ | ✓ |
| Download Control (per PDF) | ✓ | ✓ |
| Configurable Viewer Height | ✓ | ✓ |
| PDF Viewer Block | ✓ | ✓ |
| **Content Management** | | |
| PDF Document Entity | ✓ | ✓ |
| Title, Description, Slug Fields | ✓ | ✓ |
| File Upload & Management | ✓ | ✓ |
| Thumbnail Support | ✓ | ✓ |
| Auto-Generate Thumbnails | ✓ | ✓ |
| Published/Unpublished Status | ✓ | ✓ |
| Owner/User Tracking | ✓ | ✓ |
| Multi-language Support | ✓ | ✓ |
| Admin List with Columns | ✓ | ✓ |
| **SEO & URLs** | | |
| Clean URL Structure (`/pdf/slug/`) | ✓ | ✓ |
| Auto Path Alias Generation | ✓ | ✓ |
| Schema.org Markup (DigitalDocument) | ✓ | ✓ |
| Archive Schema (CollectionPage) | ✓ | ✓ |
| **Archive & Listing** | | |
| Archive Page (`/pdf`) | ✓ | ✓ |
| Pagination Support | ✓ | ✓ |
| Grid/List Display Modes | ✓ | ✓ |
| Sorting Options | ✓ | ✓ |
| Search Filtering | ✓ | ✓ |
| **REST API** | | |
| GET /documents (list) | ✓ | ✓ |
| GET /documents/{id} (single) | ✓ | ✓ |
| GET /documents/{id}/data (secure URL) | ✓ | ✓ |
| POST /documents/{id}/view (track) | ✓ | ✓ |
| GET /settings | ✓ | ✓ |
| **Statistics** | | |
| Basic View Counter | ✓ | ✓ |
| View Count Display | ✓ | ✓ |
| **Security** | | |
| Nonce/CSRF Protection | ✓ | ✓ |
| Permission System | ✓ | ✓ |
| Entity Access Control | ✓ | ✓ |
| Secure PDF URL (no direct links) | ✓ | ✓ |
| **Theming** | | |
| Twig Template Overrides | ✓ | ✓ |
| CSS Classes for Styling | ✓ | ✓ |
| **Developer** | | |
| Drupal Hooks (alter, events) | ✓ | ✓ |
| Cache Tags & Contexts | ✓ | ✓ |
| **Premium Features** | | |
| Analytics Dashboard | - | ✓ |
| Detailed View Tracking (IP, UA, referrer) | - | ✓ |
| Popular Documents Report | - | ✓ |
| Recent Views Log | - | ✓ |
| Analytics Export (CSV/JSON) | - | ✓ |
| Password Protection | - | ✓ |
| Password Verification API | - | ✓ |
| Reading Progress Tracking | - | ✓ |
| Resume Reading Feature | - | ✓ |
| XML Sitemap (`/pdf/sitemap.xml`) | - | ✓ |
| Sitemap XSL Stylesheet | - | ✓ |
| GET /analytics endpoint | - | ✓ |
| GET/POST /progress endpoints | - | ✓ |
| POST /verify-password endpoint | - | ✓ |

---

## Requirements

- Drupal 10 or 11
- PHP 8.1 or higher
- ImageMagick or Ghostscript (optional, for thumbnail generation)

## Installation

1. Download the module to your Drupal site's `modules` directory
2. Enable the module via Drush: `drush en pdf_embed_seo`
3. Or enable via the admin UI: Admin > Extend > PDF Embed & SEO Optimize
4. Configure settings at Admin > Configuration > Content > PDF Embed & SEO

## Configuration

### General Settings
- **Default Allow Download**: Whether new PDFs allow downloads by default
- **Default Allow Print**: Whether new PDFs allow printing by default
- **Auto-generate Thumbnails**: Automatically create thumbnails from PDF first pages

### Viewer Settings
- **Viewer Theme**: Choose between light and dark themes
- **Viewer Height**: Default height for the PDF viewer

### Archive Settings
- **Documents Per Page**: Number of PDFs to show on the archive page
- **Display Mode**: Grid or list layout

## Usage

### Adding PDF Documents
1. Go to Admin > Content > PDF Documents
2. Click "Add PDF Document"
3. Fill in the title and description
4. Upload your PDF file
5. Configure print/download permissions
6. Save

### Embedding PDFs with Block
Use the PDF Viewer block to embed PDFs in any region:
1. Go to Admin > Structure > Block Layout
2. Place a new "PDF Viewer" block
3. Select the PDF document to display
4. Configure height and title visibility

### URL Structure
| Page | URL |
|------|-----|
| Archive | `/pdf` |
| Single PDF | `/pdf/{slug}` |
| XML Sitemap (Premium) | `/pdf/sitemap.xml` |
| Admin List | `/admin/content/pdf-documents` |
| Settings | `/admin/config/content/pdf-embed-seo` |
| Analytics (Premium) | `/admin/reports/pdf-analytics` |

---

## Permissions

### Free Permissions
| Permission | Description |
|------------|-------------|
| Administer PDF Embed & SEO settings | Configure module settings |
| Access PDF document overview | View the admin list of PDFs |
| View PDF documents | View published PDFs on the frontend |
| Create PDF documents | Create new PDF documents |
| Edit PDF documents | Edit any PDF document |
| Edit own PDF documents | Edit only your own PDF documents |
| Delete PDF documents | Delete any PDF document |
| Delete own PDF documents | Delete only your own PDF documents |

### Premium Permissions
| Permission | Description |
|------------|-------------|
| View PDF analytics | Access the analytics dashboard |
| Export PDF analytics | Export analytics data to CSV/JSON |
| Bypass PDF password protection | View protected PDFs without password |
| Download protected PDFs | Download PDFs when disabled for others |
| Administer PDF Premium settings | Configure premium settings |

---

## Theming

### Template Files
Override these templates in your theme:
| Template | Description |
|----------|-------------|
| `pdf-document.html.twig` | Single PDF document display |
| `pdf-viewer.html.twig` | The PDF.js viewer |
| `pdf-archive.html.twig` | Archive page listing |
| `pdf-archive-item.html.twig` | Individual archive item |

### CSS Classes
| Class | Description |
|-------|-------------|
| `.pdf-viewer-wrapper` | Main viewer container |
| `.pdf-viewer-toolbar` | Toolbar with controls |
| `.pdf-viewer-container` | Canvas container |
| `.pdf-viewer-theme-light` | Light theme modifier |
| `.pdf-viewer-theme-dark` | Dark theme modifier |
| `.pdf-archive` | Archive page wrapper |
| `.pdf-archive-item` | Individual archive item |
| `.pdf-download-button` | Download button |

---

## REST API

### API Base URL
```
/api/pdf-embed-seo/v1/
```

### Free Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/documents` | List all published PDF documents |
| `GET` | `/documents/{id}` | Get single PDF document details |
| `GET` | `/documents/{id}/data` | Get PDF file URL securely |
| `POST` | `/documents/{id}/view` | Track a PDF view |
| `GET` | `/settings` | Get public module settings |

### Premium Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/analytics` | Get analytics overview |
| `GET` | `/documents/{id}/progress` | Get reading progress |
| `POST` | `/documents/{id}/progress` | Save reading progress |
| `POST` | `/documents/{id}/verify-password` | Verify PDF password |

### Query Parameters for /documents

| Parameter | Default | Description |
|-----------|---------|-------------|
| `page` | 0 | Page offset for pagination |
| `limit` | 50 | Items per page (max 100) |
| `sort` | created | Sort by: created, title, view_count |
| `direction` | DESC | Sort direction: ASC or DESC |

### Example Response
```json
{
  "id": 123,
  "title": "Annual Report 2024",
  "slug": "annual-report-2024",
  "url": "https://example.com/pdf/annual-report-2024",
  "description": "Company annual report...",
  "created": "2024-01-15T10:30:00+00:00",
  "modified": "2024-06-20T14:45:00+00:00",
  "views": 1542,
  "thumbnail": "https://example.com/sites/default/files/thumbnails/report.jpg",
  "allow_download": true,
  "allow_print": false
}
```

---

## Drupal Hooks

### Alter Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_document_data_alter` | Modify PDF data returned by API |
| `hook_pdf_embed_seo_api_settings_alter` | Modify API settings response |
| `hook_pdf_embed_seo_viewer_options_alter` | Modify PDF.js viewer options |
| `hook_pdf_embed_seo_schema_alter` | Modify Schema.org output |

### Event Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_view_tracked` | Fired when a PDF view is tracked |
| `hook_pdf_embed_seo_document_saved` | Fired when a PDF document is saved |

### Premium Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_verify_password_alter` | Override password verification |

### Example: Modify Document Data
```php
/**
 * Implements hook_pdf_embed_seo_document_data_alter().
 */
function mymodule_pdf_embed_seo_document_data_alter(array &$data, $document) {
  $data['department'] = $document->get('field_department')->value;
}
```

### Example: Custom Schema Data
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

---

## JavaScript Events

The PDF viewer triggers these JavaScript events:

| Event | Description |
|-------|-------------|
| `pdfLoaded` | When PDF document is loaded |
| `pageRendered` | When a page is rendered |
| `pageChanged` | When user navigates to a different page |
| `zoomChanged` | When zoom level changes |

---

## Services

| Service | Description |
|---------|-------------|
| `pdf_embed_seo.thumbnail_generator` | Generate PDF thumbnails |

### Premium Services

| Service | Description |
|---------|-------------|
| `pdf_embed_seo.analytics_tracker` | Track and query view statistics |
| `pdf_embed_seo.progress_tracker` | Save and retrieve reading progress |

---

## Changelog

### 1.2.0
- Added REST API endpoints for external integrations
- Added reading progress tracking (Premium)
- Added password verification endpoint (Premium)
- Added XML sitemap at /pdf/sitemap.xml (Premium)
- Added Drupal hooks for extensibility
- Separated free and premium into base module + submodule
- Improved API documentation

### 1.1.5
- Version sync with WordPress plugin
- Bug fixes and improvements

### 1.0.0
- Initial release
- Custom entity type for PDF documents
- PDF.js viewer integration
- SEO optimization with Schema.org
- Print/download controls
- View statistics
- Block plugin
- Archive page

---

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Credits

Made with ♥ by [Dross:Media](https://dross.net/media/)

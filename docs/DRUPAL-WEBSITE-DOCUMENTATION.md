# Drupal Documentation for PDF Embed & SEO Optimize

**Current Version:** 1.2.9
**Platforms:** Drupal 10, Drupal 11
**License:** GPL v2 or later

---

## Installation

### Requirements

- Drupal 10 or 11
- PHP 8.1 or higher
- Required core modules: Node, File, Taxonomy, Path, Path Alias
- Optional: ImageMagick or Ghostscript (for automatic thumbnail generation)

### Manual Installation (Recommended)

1. Download the module from [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de)
2. Extract files to `modules/contrib/pdf_embed_seo/`
3. Enable via Drush:
   ```bash
   drush en pdf_embed_seo
   ```
4. Or enable via admin UI: **Admin > Extend > PDF Embed & SEO Optimize**
5. Configure settings at **Admin > Configuration > Content > PDF Embed & SEO**

### Premium Installation

1. Ensure the base module `pdf_embed_seo` is installed and enabled
2. Enable the premium module:
   ```bash
   drush en pdf_embed_seo_premium
   ```
3. Configure at **Admin > Configuration > Content > PDF Premium Settings**
4. Enter your license key to activate premium features

---

## Content Management

### Creating PDF Documents

1. Navigate to **Admin > Content > PDF Documents**
2. Click **Add PDF Document**
3. Fill in the required fields:
   - **Title**: Document title (appears in URL, breadcrumbs, SEO)
   - **Description**: SEO-optimized description shown below the viewer
   - **PDF File**: Upload your PDF file
4. Configure optional settings:
   - **Allow Download**: Enable/disable download button
   - **Allow Print**: Enable/disable print functionality
   - **Thumbnail**: Upload cover image or auto-generate from PDF
5. Save the document

The module automatically generates a clean URL at `/pdf/{slug}` with built-in Schema.org markup.

---

## Configuration

Access settings at: **Admin > Configuration > Content > PDF Embed & SEO**

### General Settings

| Setting | Description | Default |
|---------|-------------|---------|
| Default Allow Download | Whether new PDFs allow downloads | Enabled |
| Default Allow Print | Whether new PDFs allow printing | Enabled |
| Auto-generate Thumbnails | Create thumbnails from PDF first pages | Enabled |

### Privacy & GDPR Settings

| Setting | Description | Default |
|---------|-------------|---------|
| IP Anonymization | Anonymizes IP addresses in analytics for GDPR compliance | Enabled |

**IP Anonymization Details:**
- IPv4: Zeros the last octet (e.g., `192.168.1.123` → `192.168.1.0`)
- IPv6: Zeros the last 80 bits (keeps first 48 bits)

### Viewer Settings

| Setting | Description | Default |
|---------|-------------|---------|
| Viewer Theme | Light or dark theme | Light |
| Viewer Height | Default height for PDF viewer | 800px |

### Archive Settings

| Setting | Description | Default |
|---------|-------------|---------|
| Documents Per Page | Number of PDFs on archive page | 12 |
| Display Mode | Grid or list layout | Grid |
| Content Alignment | Left, center, or right alignment | Center |
| Font Color | Custom font color for archive items | - |
| Background Color | Custom background color for archive area | - |
| Layout Width | Boxed or full-width layout | Boxed |

---

## URL Structure

| Page | URL |
|------|-----|
| Archive Listing | `/pdf` |
| Single Document | `/pdf/{slug}` |
| XML Sitemap (Premium) | `/pdf/sitemap.xml` |
| Admin List | `/admin/content/pdf-documents` |
| Settings | `/admin/config/content/pdf-embed-seo` |
| Analytics (Premium) | `/admin/reports/pdf-analytics` |

---

## Embedding PDFs

### Using the Block UI

1. Navigate to **Admin > Structure > Block Layout**
2. Click **Place block** in your desired region
3. Select **PDF Viewer** block
4. Configure:
   - Select the PDF document to display
   - Set custom height (optional)
   - Toggle title visibility
5. Save the block placement

### Programmatic Embedding

Use Drupal's block render system:

```php
// Load and render a PDF viewer block
$block_manager = \Drupal::service('plugin.manager.block');
$plugin_block = $block_manager->createInstance('pdf_viewer_block', [
  'pdf_document_id' => 123,
  'height' => '600px',
  'show_title' => FALSE,
]);

$render = $plugin_block->build();
```

Or use a render array in Twig:

```twig
{{ drupal_block('pdf_viewer_block', {pdf_document_id: 123, height: '600px'}) }}
```

---

## Theming

### Template Files

Override these templates in your theme's `templates/` folder:

| Template | Description |
|----------|-------------|
| `pdf-document.html.twig` | Single PDF document page |
| `pdf-viewer.html.twig` | The PDF.js viewer component |
| `pdf-archive.html.twig` | Archive page listing |
| `pdf-archive-item.html.twig` | Individual archive item |
| `pdf-password-form.html.twig` | Password entry form (Premium) |
| `pdf-analytics-dashboard.html.twig` | Analytics dashboard (Premium) |

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
| `.page-pdf` | Body class on PDF pages |
| `.page-pdf-archive` | Body class on archive page |
| `.page-pdf-document` | Body class on single PDF page |

### Full-Width Layout

PDF pages automatically display full-width without sidebars. The module:
- Clears sidebar regions on PDF routes
- Adds `.page-pdf` body classes for CSS targeting
- Provides CSS rules to hide common sidebar selectors

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

## REST API

### Base URL

```
/api/pdf-embed-seo/v1/
```

### Free Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/documents` | List all published PDF documents | None |
| `GET` | `/documents/{id}` | Get single PDF document details | None |
| `GET` | `/documents/{id}/data` | Get PDF file URL securely | None |
| `POST` | `/documents/{id}/view` | Track a PDF view | None |
| `GET` | `/settings` | Get public module settings | None |

### Premium Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/analytics` | Get analytics overview | Admin |
| `GET` | `/analytics/documents` | Per-document analytics | Admin |
| `GET` | `/analytics/export` | Export analytics CSV/JSON | Admin |
| `GET` | `/documents/{id}/progress` | Get reading progress | None |
| `POST` | `/documents/{id}/progress` | Save reading progress | None |
| `POST` | `/documents/{id}/verify-password` | Verify PDF password | None |
| `POST` | `/documents/{id}/download` | Track PDF download | None |
| `POST` | `/documents/{id}/expiring-link` | Generate expiring link | Admin |
| `GET` | `/documents/{id}/expiring-link/{token}` | Validate expiring link | None |
| `GET` | `/categories` | List PDF categories | None |
| `GET` | `/tags` | List PDF tags | None |
| `POST` | `/bulk/import` | Start bulk import | Admin |
| `GET` | `/bulk/import/{id}/status` | Get import status | Admin |

### Query Parameters for `/documents`

| Parameter | Default | Description |
|-----------|---------|-------------|
| `page` | 0 | Page offset for pagination |
| `limit` | 50 | Items per page (max 100) |
| `sort` | created | Sort by: `created`, `title`, `view_count` |
| `direction` | DESC | Sort direction: `ASC` or `DESC` |

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

## JavaScript Events

The PDF viewer triggers custom JavaScript events for integration:

| Event | Description |
|-------|-------------|
| `pdfLoaded` | Fired when PDF document is fully loaded |
| `pageRendered` | Fired when a page finishes rendering |
| `pageChanged` | Fired when user navigates to a different page |
| `zoomChanged` | Fired when zoom level changes |

### Example Usage

```javascript
document.addEventListener('pdfLoaded', function(e) {
  console.log('PDF loaded:', e.detail.documentId);
});

document.addEventListener('pageChanged', function(e) {
  console.log('Page changed to:', e.detail.pageNumber);
});
```

---

## Drupal Hooks

### Alter Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_document_data_alter` | Modify PDF data returned by API |
| `hook_pdf_embed_seo_api_settings_alter` | Modify API settings response |
| `hook_pdf_embed_seo_schema_alter` | Modify Schema.org output |
| `hook_pdf_embed_seo_viewer_options_alter` | Modify viewer configuration |
| `hook_pdf_embed_seo_verify_password_alter` | Override password verification (Premium) |

### Event Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_view_tracked` | Fired when a PDF view is tracked |
| `hook_pdf_embed_seo_document_saved` | Fired when a PDF document is saved |

### Example: Modify Document Data

```php
/**
 * Implements hook_pdf_embed_seo_document_data_alter().
 */
function mymodule_pdf_embed_seo_document_data_alter(array &$data, $document) {
  // Add custom field to API response
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

### Example: React to View Tracking

```php
/**
 * Implements hook_pdf_embed_seo_view_tracked().
 */
function mymodule_pdf_embed_seo_view_tracked($pdf_document, $view_count) {
  if ($view_count == 1000) {
    \Drupal::logger('mymodule')->notice('Document @title reached 1000 views!', [
      '@title' => $pdf_document->label(),
    ]);
  }
}
```

---

## Services

### Free Services

| Service | Description |
|---------|-------------|
| `pdf_embed_seo.thumbnail_generator` | Generate PDF thumbnails |

### Premium Services

| Service | Description |
|---------|-------------|
| `pdf_embed_seo.analytics_tracker` | Track and query view statistics |
| `pdf_embed_seo.progress_tracker` | Save and retrieve reading progress |
| `pdf_embed_seo.schema_enhancer` | GEO/AEO/LLM schema optimization |
| `pdf_embed_seo.access_manager` | Role-based access control |
| `pdf_embed_seo.viewer_enhancer` | Enhanced viewer features |
| `pdf_embed_seo.bulk_operations` | Bulk import and update operations |

### Example: Using Analytics Tracker

```php
$tracker = \Drupal::service('pdf_embed_seo.analytics_tracker');

// Get document statistics
$stats = $tracker->getDocumentStats($pdf_document);
// Returns: ['total_views' => 100, 'unique_visitors' => 75, 'views_today' => 5]

// Get popular documents
$popular = $tracker->getPopularDocuments(10, 30); // top 10, last 30 days
```

### Example: Using Progress Tracker

```php
$tracker = \Drupal::service('pdf_embed_seo.progress_tracker');

// Get progress for current user
$progress = $tracker->getProgress($pdf_document);
// Returns: ['page' => 15, 'scroll' => 0.45, 'zoom' => 1.25]

// Save progress
$tracker->saveProgress($pdf_document, [
  'page' => 20,
  'scroll' => 0.0,
  'zoom' => 1.0,
]);
```

---

## Premium Features

### Analytics Dashboard

Access at: **Admin > Reports > PDF Analytics**

- Total views across all PDFs
- Popular documents ranking
- Recent views log with IP, user agent, referrer
- Time period filters (7 days, 30 days, 90 days, 12 months, all time)
- Export to CSV or JSON

### Download Tracking

Track PDF downloads separately from views:
- Separate download counter per document
- Download statistics in analytics dashboard
- User attribution for downloads
- REST API endpoint for headless tracking

### Password Protection

Protect individual PDFs with passwords:
- Secure password hashing
- Configurable session duration
- Brute-force protection (max attempts)
- AJAX-based verification (no page reload)

### Reading Progress

Remember user reading position:
- Auto-save current page, scroll position, zoom level
- Resume reading prompt on return visits
- Works for authenticated and anonymous users

### Expiring Access Links

Generate time-limited URLs for sharing:
- Configurable expiration time
- Maximum usage limits
- Token-based secure access
- Usage tracking

### Role-Based Access Control

Restrict PDF access by user role:
- Require login option
- Role restriction per document
- Custom access denied messages
- Login redirect for anonymous users

### XML Sitemap

Dedicated sitemap at `/pdf/sitemap.xml`:
- All published PDF documents
- XSL stylesheet for human-readable view
- Auto-updates on document changes
- Proper caching for performance

---

## Changelog

### 1.2.9
- **Performance**: Views tracked directly in analytics table (no entity saves)
- **Performance**: Cache tag invalidation for lists
- **Performance**: Cache metadata on PdfViewerBlock
- **Privacy**: IP anonymization for GDPR compliance (enabled by default)
- **Fix**: Archive list view icon alignment
- **Fix**: Boxed layout for grid and list views

### 1.2.8
- Archive settings: Content Alignment, Font Color, Background Color
- Grid/List styling: Font color applies to cards, titles, excerpts
- Seamless background color coverage

### 1.2.7
- Full-width PDF pages (sidebar removal)
- Body classes for CSS targeting (`.page-pdf`)

### 1.2.6
- Security: Proper password hashing with Drupal's password service
- Security: XSS vulnerability fix in PdfViewerBlock

### 1.2.5
- Download Tracking
- Expiring Access Links
- Schema Optimization (GEO/AEO/LLM) service
- Role-Based Access Control service
- Bulk Import operations
- Extended REST API (14+ premium endpoints)

### 1.2.0
- REST API endpoints
- Reading progress tracking (Premium)
- Password verification API (Premium)
- XML Sitemap (Premium)
- Drupal hooks for extensibility

### 1.0.0
- Initial release
- PDF.js viewer integration
- Schema.org SEO optimization
- Print/download controls
- View statistics
- Block plugin
- Archive page

---

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Credits

Made with ♥ by [Dross:Media](https://dross.net/media/)

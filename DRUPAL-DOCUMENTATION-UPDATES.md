# Drupal Documentation Updates Required (v1.2.9)

This document compares the current website documentation at https://pdfviewer.drossmedia.de/documentation/#drupal with the actual Drupal module v1.2.9 functionality.

---

## Summary of Discrepancies

| Section | Status | Notes |
|---------|--------|-------|
| Installation | Minor Update | Add manual installation option |
| Configuration | Major Update | Many new settings missing |
| Templates | Minor Update | Missing `pdf-archive-item.html.twig` |
| Developer Hooks | Major Update | Only 3 of 10+ hooks documented |
| REST API (Free) | Correct | All 5 endpoints documented |
| REST API (Premium) | Major Update | Only 4 of 14+ endpoints documented |
| Permissions | Missing | Not documented at all |
| Services | Missing | Not documented at all |
| JavaScript Events | Missing | Not documented at all |
| GDPR/Privacy | Missing | IP anonymization not documented |
| Archive Settings | Missing | Not documented at all |

---

## Detailed Updates Required

### 1. Installation Section

**Current Documentation:**
```
composer require drossmedia/pdf_embed_seo
drush en pdf_embed_seo
```

**Recommended Update:**
```markdown
## Installation

### Option 1: Composer (Recommended)
```bash
composer require drossmedia/pdf_embed_seo
drush en pdf_embed_seo
```

### Option 2: Manual Installation
1. Download and extract the module to `modules/contrib/pdf_embed_seo/`
2. Enable via Drush: `drush en pdf_embed_seo`
3. Or enable via UI: **Admin > Extend > PDF Embed & SEO Optimize**

### Requirements
- **Drupal:** 10 or 11
- **PHP:** 8.1 or higher
- **Core Dependencies:** Node, File, Taxonomy, Path, Path Alias modules
- **Optional:** ImageMagick or Ghostscript (for automatic thumbnail generation)

### Premium Module Installation
```bash
drush en pdf_embed_seo_premium
```
Configure at: **Admin > Configuration > Content > PDF Premium Settings**
```

---

### 2. Configuration Section

**Current Documentation:** Lists only 5 basic settings.

**Add These Missing Settings:**

```markdown
## Configuration

Settings are managed at: **Admin > Configuration > Content > PDF Embed & SEO**

### Display Settings

| Setting | Default | Description |
|---------|---------|-------------|
| Default Download Permission | Disabled | Allow download for new documents |
| Default Print Permission | Disabled | Allow print for new documents |
| Viewer Theme | Light | Choose 'light' or 'dark' theme |
| Viewer Height | 800px | CSS height value for the PDF viewer |

### Thumbnail Settings

| Setting | Default | Description |
|---------|---------|-------------|
| Auto-generate Thumbnails | Enabled | Automatically create thumbnails from PDF first page |
| Thumbnail Width | 300px | Width of generated thumbnails |
| Thumbnail Height | 400px | Height of generated thumbnails |

### Archive Page Settings

| Setting | Default | Description |
|---------|---------|-------------|
| Archive Title | "PDF Documents" | Custom heading for the archive page |
| Archive Description | (empty) | Meta description for archive page SEO |
| Posts Per Page | 12 | Number of documents per archive page |
| Display Style | Grid | Choose 'grid' or 'list' view |
| Show Descriptions | Enabled | Display document excerpts |
| Show View Count | Enabled | Display view statistics |
| Content Alignment | Center | Align content: 'left', 'center', or 'right' |
| Layout Width | Boxed | Choose 'boxed' or 'full-width' layout |
| Font Color | (inherit) | Custom font color (hex or CSS color) |
| Background Color | (inherit) | Custom background color for archive area |
| Item Background Color | (inherit) | Background color for individual grid cards |

### SEO Settings

| Setting | Default | Description |
|---------|---------|-------------|
| Enable Schema.org Markup | Enabled | Output DigitalDocument and CollectionPage schema |
| Show Breadcrumbs | Enabled | Display breadcrumb navigation with Schema.org markup |

### Privacy & GDPR Settings

| Setting | Default | Description |
|---------|---------|-------------|
| Anonymize IP Addresses | Enabled | Anonymize visitor IPs for GDPR compliance (zeros last octet for IPv4, last 80 bits for IPv6) |

### Branding

| Setting | Default | Description |
|---------|---------|-------------|
| Favicon URL | (empty) | Custom favicon for PDF pages |
```

---

### 3. Templates Section

**Current Documentation:** Lists 4 templates.

**Add Missing Template:**

```markdown
### Twig Template Overrides

Copy templates from `modules/pdf_embed_seo/templates/` to your theme's `templates/` directory:

| Template | Purpose |
|----------|---------|
| `pdf-document.html.twig` | Single PDF document page layout |
| `pdf-viewer.html.twig` | PDF.js viewer component |
| `pdf-archive.html.twig` | Archive listing page |
| `pdf-archive-item.html.twig` | Individual archive item (grid card or list row) |
| `pdf-password-form.html.twig` | Password protection form (Premium) |

After customization, clear cache: `drush cr`

#### Template Variables

**pdf-viewer.html.twig:**
- `pdf_document` - The PDF document entity
- `pdf_url` - Secure URL to the PDF file
- `allow_download` - Boolean for download permission
- `allow_print` - Boolean for print permission
- `viewer_theme` - 'light' or 'dark'
- `width` - CSS width value
- `height` - CSS height value

**pdf-archive.html.twig:**
- `documents` - Array of PDF document entities
- `pager` - Pagination render array
- `filters` - Active filter values
- `display_style` - 'grid' or 'list'
- `archive_title` - Page heading
- `site_name` - Site name for Schema.org
- `content_alignment` - Alignment setting
- `colors` - Array of color settings

**pdf-archive-item.html.twig:**
- `document` - The PDF document entity
- `thumbnail` - Thumbnail URL
- `url` - Link to PDF page
- `show_description` - Boolean to show excerpt
- `show_view_count` - Boolean to show views
```

---

### 4. Developer Hooks Section

**Current Documentation:** Lists only 3 alter hooks.

**Complete Hooks Reference:**

```markdown
## Developer Hooks

### Theme Hooks

Implement `hook_theme()` to modify theme definitions:

| Hook | Template | Description |
|------|----------|-------------|
| `pdf_document` | `pdf-document.html.twig` | Single PDF document display |
| `pdf_viewer` | `pdf-viewer.html.twig` | PDF.js viewer component |
| `pdf_archive` | `pdf-archive.html.twig` | Archive listing page |
| `pdf_archive_item` | `pdf-archive-item.html.twig` | Individual archive item |

### Alter Hooks

**hook_pdf_embed_seo_document_data_alter(&$data, $document)**

Modify API document data before response:

```php
function mymodule_pdf_embed_seo_document_data_alter(array &$data, $document) {
  // Add custom field to API response
  $data['custom_field'] = $document->get('field_custom')->value;
}
```

**hook_pdf_embed_seo_schema_alter(&$schema, $document)**

Customize Schema.org structured data:

```php
function mymodule_pdf_embed_seo_schema_alter(array &$schema, $document) {
  $schema['author'] = [
    '@type' => 'Person',
    'name' => $document->get('field_author')->value,
  ];
}
```

**hook_pdf_embed_seo_viewer_options_alter(&$options, $document)**

Modify PDF.js viewer configuration:

```php
function mymodule_pdf_embed_seo_viewer_options_alter(array &$options, $document) {
  $options['defaultZoom'] = 'page-width';
  $options['sidebarOpen'] = TRUE;
}
```

**hook_pdf_embed_seo_api_settings_alter(&$settings)**

Modify REST API settings response:

```php
function mymodule_pdf_embed_seo_api_settings_alter(array &$settings) {
  $settings['custom_setting'] = 'value';
}
```

**hook_pdf_embed_seo_verify_password_alter(&$is_valid, $document, $password)** (Premium)

Override password verification logic:

```php
function mymodule_pdf_embed_seo_verify_password_alter(&$is_valid, $document, $password) {
  // Custom password validation
  if ($password === 'master-key') {
    $is_valid = TRUE;
  }
}
```

### Event Hooks

**hook_pdf_embed_seo_view_tracked($document_id, $analytics_data)**

Triggered when a PDF view is tracked:

```php
function mymodule_pdf_embed_seo_view_tracked($document_id, $analytics_data) {
  \Drupal::logger('mymodule')->info('PDF @id viewed', ['@id' => $document_id]);
}
```

### Entity Hooks

Standard Drupal entity hooks are available:

- `hook_pdf_document_insert($document)` - Document created
- `hook_pdf_document_update($document)` - Document updated
- `hook_pdf_document_delete($document)` - Document deleted

### Preprocessing Hooks

- `template_preprocess_pdf_document(&$variables)` - Preprocess single document
- `template_preprocess_pdf_viewer(&$variables)` - Preprocess viewer
- `template_preprocess_pdf_archive(&$variables)` - Preprocess archive
- `template_preprocess_pdf_archive_item(&$variables)` - Preprocess archive item
```

---

### 5. REST API Section - Premium Endpoints

**Current Documentation:** Lists only 4 premium endpoints.

**Complete Premium Endpoints:**

```markdown
### Premium REST API Endpoints

Base URL: `/api/pdf-embed-seo/v1/`

#### Analytics Endpoints

| Method | Endpoint | Description | Permission |
|--------|----------|-------------|------------|
| GET | `/analytics` | Analytics overview (views, visitors, popular docs) | view pdf analytics |
| GET | `/analytics/documents` | Per-document analytics breakdown | view pdf analytics |
| GET | `/analytics/export` | Export analytics (CSV/JSON format) | export pdf analytics |

**Query Parameters for `/analytics`:**
- `period` - Time period: `7d`, `30d`, `90d`, `365d`, `all` (default: `30d`)

**Query Parameters for `/analytics/export`:**
- `format` - Export format: `csv` or `json` (default: `json`)
- `period` - Time period filter

#### Taxonomy Endpoints

| Method | Endpoint | Description | Permission |
|--------|----------|-------------|------------|
| GET | `/categories` | List all PDF categories | Public |
| GET | `/tags` | List all PDF tags | Public |

#### Progress Tracking Endpoints

| Method | Endpoint | Description | Permission |
|--------|----------|-------------|------------|
| GET | `/documents/{id}/progress` | Get user's reading progress | Public |
| POST | `/documents/{id}/progress` | Save reading progress | Public |

**POST Body for Progress:**
```json
{
  "page": 5,
  "scroll_position": 250,
  "zoom_level": 1.5
}
```

#### Password Protection Endpoints

| Method | Endpoint | Description | Permission |
|--------|----------|-------------|------------|
| POST | `/documents/{id}/verify-password` | Verify document password | Public |

**POST Body:**
```json
{
  "password": "user-entered-password"
}
```

#### Download Tracking Endpoint

| Method | Endpoint | Description | Permission |
|--------|----------|-------------|------------|
| POST | `/documents/{id}/download` | Track PDF download | Public |

#### Expiring Access Links Endpoints

| Method | Endpoint | Description | Permission |
|--------|----------|-------------|------------|
| POST | `/documents/{id}/expiring-link` | Generate time-limited access link | administer pdf embed seo |
| GET | `/documents/{id}/expiring-link/{token}` | Validate expiring link | Public |

**POST Body for Expiring Link:**
```json
{
  "expires_in": 3600,
  "max_uses": 5
}
```

**Response:**
```json
{
  "url": "https://site.com/api/pdf-embed-seo/v1/documents/123/expiring-link/abc123token",
  "token": "abc123token",
  "expires_at": "2024-01-15T12:00:00+00:00",
  "max_uses": 5
}
```

#### Bulk Import Endpoints

| Method | Endpoint | Description | Permission |
|--------|----------|-------------|------------|
| POST | `/bulk/import` | Start bulk import from CSV/ZIP | administer pdf embed seo |
| GET | `/bulk/import/status` | Get latest import status | administer pdf embed seo |
| GET | `/bulk/import/{batch_id}/status` | Get specific import status | administer pdf embed seo |

**POST Body for Bulk Import:**
```json
{
  "file_id": 456,
  "category": "reports",
  "default_settings": {
    "allow_download": true,
    "allow_print": false
  }
}
```
```

---

### 6. NEW SECTION: Permissions

**Add This New Section:**

```markdown
## Permissions

### Free Module Permissions

| Permission | Machine Name | Description |
|------------|--------------|-------------|
| Administer PDF Embed & SEO | `administer pdf embed seo` | Configure global module settings |
| Access PDF document overview | `access pdf document overview` | View admin document list |
| View PDF documents | `view pdf document` | View published PDFs (public) |
| Create PDF documents | `create pdf document` | Create new documents |
| Edit PDF documents | `edit pdf document` | Edit any PDF document |
| Edit own PDF documents | `edit own pdf document` | Edit only own documents |
| Delete PDF documents | `delete pdf document` | Delete any PDF document |
| Delete own PDF documents | `delete own pdf document` | Delete only own documents |

### Premium Module Permissions

| Permission | Machine Name | Description |
|------------|--------------|-------------|
| View PDF analytics | `view pdf analytics` | Access analytics dashboard |
| Export PDF analytics | `export pdf analytics` | Export analytics to CSV/JSON |
| Bypass PDF password | `bypass pdf password` | View protected PDFs without password |
| Download protected PDFs | `download protected pdf` | Download PDFs with download disabled |
| Administer PDF Premium | `administer pdf premium settings` | Configure premium features |

### Recommended Role Configuration

**Anonymous Users:**
- View PDF documents

**Authenticated Users:**
- View PDF documents
- Create PDF documents
- Edit own PDF documents
- Delete own PDF documents

**Content Editor:**
- All authenticated permissions
- Edit PDF documents
- Delete PDF documents
- View PDF analytics

**Administrator:**
- All permissions
```

---

### 7. NEW SECTION: Services

**Add This New Section:**

```markdown
## Services

### Free Module Services

**pdf_embed_seo.thumbnail_generator**

Generates thumbnail images from PDF first pages:

```php
$thumbnail_generator = \Drupal::service('pdf_embed_seo.thumbnail_generator');
$thumbnail_uri = $thumbnail_generator->generate($pdf_file_entity);
```

### Premium Module Services

**pdf_embed_seo.analytics_tracker**

Track and query view/download statistics:

```php
$analytics = \Drupal::service('pdf_embed_seo.analytics_tracker');

// Track a view
$analytics->trackView($document_id);

// Get analytics for a document
$stats = $analytics->getDocumentStats($document_id, '30d');
```

**pdf_embed_seo.progress_tracker**

Save and retrieve reading progress:

```php
$progress = \Drupal::service('pdf_embed_seo.progress_tracker');

// Save progress
$progress->save($document_id, [
  'page' => 5,
  'scroll_position' => 250,
  'zoom_level' => 1.5,
]);

// Get progress
$saved = $progress->get($document_id);
```

**pdf_embed_seo.schema_enhancer**

Enhance Schema.org output with GEO/AEO/LLM optimizations:

```php
$enhancer = \Drupal::service('pdf_embed_seo.schema_enhancer');
$schema = $enhancer->enhance($document, $base_schema);
```

**pdf_embed_seo.access_manager**

Check role-based access:

```php
$access = \Drupal::service('pdf_embed_seo.access_manager');
if ($access->canView($document)) {
  // User has access
}
```

**pdf_embed_seo.viewer_enhancer**

Configure enhanced viewer features:

```php
$viewer = \Drupal::service('pdf_embed_seo.viewer_enhancer');
$options = $viewer->getEnhancedOptions($document);
```

**pdf_embed_seo.bulk_operations**

Perform bulk import/update operations:

```php
$bulk = \Drupal::service('pdf_embed_seo.bulk_operations');
$batch_id = $bulk->startImport($file_id, $options);
$status = $bulk->getStatus($batch_id);
```
```

---

### 8. NEW SECTION: JavaScript Events

**Add This New Section:**

```markdown
## JavaScript API

### Drupal Settings

Access viewer configuration via `drupalSettings.pdfEmbedSeo`:

```javascript
(function ($, Drupal, drupalSettings) {
  const config = drupalSettings.pdfEmbedSeo;
  console.log('Viewer height:', config.viewerHeight);
  console.log('Theme:', config.theme);
})(jQuery, Drupal, drupalSettings);
```

### JavaScript Events

Listen for PDF viewer events:

```javascript
document.addEventListener('pdfLoaded', function(event) {
  console.log('PDF loaded:', event.detail.documentId);
  console.log('Total pages:', event.detail.numPages);
});

document.addEventListener('pageRendered', function(event) {
  console.log('Page rendered:', event.detail.pageNumber);
});

document.addEventListener('pageChanged', function(event) {
  console.log('Navigated to page:', event.detail.pageNumber);
});

document.addEventListener('zoomChanged', function(event) {
  console.log('Zoom level:', event.detail.scale);
});
```

### Custom Viewer Integration

```javascript
Drupal.behaviors.myPdfBehavior = {
  attach: function (context, settings) {
    once('my-pdf-behavior', '.pdf-viewer-container', context).forEach(function (element) {
      // Custom PDF viewer logic
      const viewer = element.querySelector('iframe');

      viewer.addEventListener('load', function() {
        // Viewer is ready
      });
    });
  }
};
```
```

---

### 9. NEW SECTION: URL Structure

**Add This Section:**

```markdown
## URL Structure

| Page | Path | Controller |
|------|------|------------|
| Archive | `/pdf` | PdfArchiveController |
| Single PDF | `/pdf/{slug}` | PdfViewController |
| XML Sitemap (Premium) | `/pdf/sitemap.xml` | PdfSitemapController |
| Sitemap Stylesheet | `/pdf/sitemap-style.xsl` | PdfSitemapController |
| Admin List | `/admin/content/pdf-documents` | Entity List Builder |
| Add Document | `/admin/content/pdf-documents/add` | PdfDocumentForm |
| Edit Document | `/admin/content/pdf-documents/{id}/edit` | PdfDocumentForm |
| Settings | `/admin/config/content/pdf-embed-seo` | PdfEmbedSeoSettingsForm |
| Premium Settings | `/admin/config/content/pdf-embed-seo/premium` | PdfPremiumSettingsForm |
| Analytics Dashboard | `/admin/reports/pdf-analytics` | PdfAnalyticsController |
| Analytics Export | `/admin/reports/pdf-analytics/export` | PdfAnalyticsController |
```

---

### 10. NEW SECTION: Database Schema

**Add This Section:**

```markdown
## Database Schema

### pdf_embed_seo_analytics Table

The module creates a dedicated analytics table for view tracking:

| Column | Type | Description |
|--------|------|-------------|
| id | serial | Primary key |
| pdf_document_id | int | PDF entity ID (indexed) |
| user_id | int | User ID, 0 for anonymous (indexed) |
| ip_address | varchar(45) | Visitor IP (anonymized if GDPR enabled) |
| user_agent | varchar(255) | Browser user agent string |
| referer | varchar(255) | HTTP referrer URL |
| timestamp | int | Unix timestamp (indexed) |

**Note:** Views are tracked directly to this table without triggering entity saves, ensuring optimal performance and avoiding cache invalidation on every page view.
```

---

### 11. Update Version Information

**Add Version Badge:**

```markdown
## Drupal Module

**Current Version:** 1.2.9
**Compatibility:** Drupal 10, Drupal 11
**PHP Requirement:** 8.1+
```

---

## Complete Updated Documentation (Recommended)

Below is the complete recommended Drupal documentation section to replace the current website content:

---

# Drupal PDF Embed & SEO Optimize Documentation (v1.2.9)

## Installation

### Option 1: Composer (Recommended)
```bash
composer require drossmedia/pdf_embed_seo
drush en pdf_embed_seo
```

### Option 2: Manual Installation
1. Download and extract the module to `modules/contrib/pdf_embed_seo/`
2. Enable via Drush: `drush en pdf_embed_seo`
3. Or enable via UI: **Admin > Extend > PDF Embed & SEO Optimize**

### Premium Module
```bash
drush en pdf_embed_seo_premium
```

### Requirements
- **Drupal:** 10 or 11
- **PHP:** 8.1+
- **Dependencies:** Node, File, Taxonomy, Path, Path Alias
- **Optional:** ImageMagick or Ghostscript (thumbnails)

---

## Configuration

Settings: **Admin > Configuration > Content > PDF Embed & SEO**

### Display Settings
| Setting | Default | Description |
|---------|---------|-------------|
| Default Download Permission | Disabled | Allow download for new documents |
| Default Print Permission | Disabled | Allow print for new documents |
| Viewer Theme | Light | 'light' or 'dark' theme |
| Viewer Height | 800px | CSS height value |

### Thumbnail Settings
| Setting | Default | Description |
|---------|---------|-------------|
| Auto-generate Thumbnails | Enabled | Create thumbnails from PDF |
| Thumbnail Width | 300px | Generated thumbnail width |
| Thumbnail Height | 400px | Generated thumbnail height |

### Archive Page Settings
| Setting | Default | Description |
|---------|---------|-------------|
| Archive Title | "PDF Documents" | Custom page heading |
| Posts Per Page | 12 | Documents per page |
| Display Style | Grid | 'grid' or 'list' |
| Content Alignment | Center | 'left', 'center', 'right' |
| Show Descriptions | Enabled | Display excerpts |
| Show View Count | Enabled | Display view stats |

### SEO Settings
| Setting | Default | Description |
|---------|---------|-------------|
| Enable Schema.org | Enabled | DigitalDocument markup |
| Show Breadcrumbs | Enabled | Breadcrumb navigation |

### Privacy (GDPR)
| Setting | Default | Description |
|---------|---------|-------------|
| Anonymize IP Addresses | Enabled | GDPR-compliant IP anonymization |

---

## Content Integration

### Block Placement
Add PDF Viewer block via **Structure > Block Layout**.

### Template Overrides
Copy from `modules/pdf_embed_seo/templates/` to your theme:

| Template | Purpose |
|----------|---------|
| `pdf-document.html.twig` | Single PDF page |
| `pdf-viewer.html.twig` | Viewer component |
| `pdf-archive.html.twig` | Archive listing |
| `pdf-archive-item.html.twig` | Archive item |
| `pdf-password-form.html.twig` | Password form (Premium) |

Clear cache after changes: `drush cr`

### Programmatic Embedding
```twig
{{ drupal_block('pdf_viewer_block', {
  'pdf_id': node.field_pdf.target_id,
  'width': '100%',
  'height': '600px'
}) }}
```

---

## Developer Hooks

### Alter Hooks

```php
// Modify API document data
function mymodule_pdf_embed_seo_document_data_alter(array &$data, $document) {
  $data['custom_field'] = $document->get('field_custom')->value;
}

// Customize Schema.org output
function mymodule_pdf_embed_seo_schema_alter(array &$schema, $document) {
  $schema['author'] = [
    '@type' => 'Person',
    'name' => $document->get('field_author')->value,
  ];
}

// Modify viewer options
function mymodule_pdf_embed_seo_viewer_options_alter(array &$options, $document) {
  $options['defaultZoom'] = 'page-width';
}

// Modify API settings response
function mymodule_pdf_embed_seo_api_settings_alter(array &$settings) {
  $settings['custom_setting'] = 'value';
}

// Override password verification (Premium)
function mymodule_pdf_embed_seo_verify_password_alter(&$is_valid, $document, $password) {
  if ($password === 'master-key') {
    $is_valid = TRUE;
  }
}
```

### Event Hooks

```php
// Triggered when view is tracked
function mymodule_pdf_embed_seo_view_tracked($document_id, $analytics_data) {
  \Drupal::logger('mymodule')->info('PDF @id viewed', ['@id' => $document_id]);
}
```

---

## REST API Reference

Base URL: `/api/pdf-embed-seo/v1/`

### Public Endpoints (Free)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/documents` | List all published PDFs |
| GET | `/documents/{id}` | Get document details |
| GET | `/documents/{id}/data` | Get secure PDF URL |
| POST | `/documents/{id}/view` | Track view |
| GET | `/settings` | Get public settings |

### Premium Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/analytics` | Analytics overview | view pdf analytics |
| GET | `/analytics/documents` | Per-document stats | view pdf analytics |
| GET | `/analytics/export` | Export CSV/JSON | export pdf analytics |
| GET | `/categories` | List categories | Public |
| GET | `/tags` | List tags | Public |
| GET | `/documents/{id}/progress` | Get reading progress | Public |
| POST | `/documents/{id}/progress` | Save progress | Public |
| POST | `/documents/{id}/verify-password` | Verify password | Public |
| POST | `/documents/{id}/download` | Track download | Public |
| POST | `/documents/{id}/expiring-link` | Generate link | Admin |
| GET | `/documents/{id}/expiring-link/{token}` | Validate link | Public |
| POST | `/bulk/import` | Start bulk import | Admin |
| GET | `/bulk/import/status` | Get import status | Admin |

---

## Permissions

### Free Permissions
| Permission | Machine Name |
|------------|--------------|
| Administer settings | `administer pdf embed seo` |
| Access admin list | `access pdf document overview` |
| View documents | `view pdf document` |
| Create documents | `create pdf document` |
| Edit any document | `edit pdf document` |
| Edit own documents | `edit own pdf document` |
| Delete any document | `delete pdf document` |
| Delete own documents | `delete own pdf document` |

### Premium Permissions
| Permission | Machine Name |
|------------|--------------|
| View analytics | `view pdf analytics` |
| Export analytics | `export pdf analytics` |
| Bypass password | `bypass pdf password` |
| Download protected | `download protected pdf` |
| Administer premium | `administer pdf premium settings` |

---

## Services

### Free Services
- `pdf_embed_seo.thumbnail_generator` - Generate PDF thumbnails

### Premium Services
- `pdf_embed_seo.analytics_tracker` - View/download tracking
- `pdf_embed_seo.progress_tracker` - Reading progress
- `pdf_embed_seo.schema_enhancer` - GEO/AEO/LLM schema
- `pdf_embed_seo.access_manager` - Role-based access
- `pdf_embed_seo.viewer_enhancer` - Enhanced viewer features
- `pdf_embed_seo.bulk_operations` - Bulk import/update

---

## JavaScript Events

```javascript
document.addEventListener('pdfLoaded', (e) => console.log('Pages:', e.detail.numPages));
document.addEventListener('pageChanged', (e) => console.log('Page:', e.detail.pageNumber));
document.addEventListener('zoomChanged', (e) => console.log('Zoom:', e.detail.scale));
```

---

## URL Structure

| Page | Path |
|------|------|
| Archive | `/pdf` |
| Single PDF | `/pdf/{slug}` |
| XML Sitemap | `/pdf/sitemap.xml` (Premium) |
| Admin List | `/admin/content/pdf-documents` |
| Settings | `/admin/config/content/pdf-embed-seo` |
| Analytics | `/admin/reports/pdf-analytics` (Premium) |

---

## Premium Features

- **Analytics Dashboard** - Views, visitors, popular documents
- **Password Protection** - Secure PDFs with hashed passwords
- **Reading Progress** - Auto-save page, scroll, zoom
- **XML Sitemap** - SEO-friendly sitemap at `/pdf/sitemap.xml`
- **Download Tracking** - Separate download statistics
- **Expiring Links** - Time-limited URLs with usage limits
- **Categories & Tags** - Hierarchical organization
- **Role-Based Access** - Restrict by user role
- **Bulk Import** - CSV/ZIP import with category assignment
- **Export** - CSV/JSON analytics export

---

*Documentation updated for v1.2.9*

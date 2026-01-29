# Drupal Premium Module - Complete Feature List

**Module:** `pdf_embed_seo_premium`
**Version:** 1.2.5
**Platform:** Drupal 10/11
**Purchase:** https://pdfviewer.drossmedia.de

---

## Premium Features

### Analytics & Tracking

- **Analytics Dashboard** - Detailed view statistics with charts and reports at `/admin/reports/pdf-analytics`
- **Download Tracking** - Track PDF downloads separately from views
- **Per-Document Stats** - Detailed breakdown by individual PDF
- **Time Period Filters** - View data for 7 days, 30 days, 90 days, 12 months, or all time
- **Popular Documents Report** - Ranking of most-viewed documents
- **Recent Views Log** - List of recent PDF views with timestamp, IP, user agent, referrer
- **Time Spent Tracking** - Duration spent viewing each PDF
- **Pages Viewed Tracking** - Number of pages viewed per session
- **User Attribution** - Track authenticated vs anonymous users
- **CSV/JSON Export** - Export analytics data for external analysis

### Security & Access Control

- **Password Protection** - Protect individual PDFs with passwords
- **Secure Password Hashing** - WordPress-compatible password hashing
- **Session-Based Access** - Configurable session duration after authentication
- **Brute-Force Protection** - Maximum failed attempts limit with lockout
- **AJAX Password Verification** - No page reload on password entry
- **Expiring Access Links** - Generate time-limited URLs for PDFs
- **Token-Based Access** - Secure random tokens for each link
- **Max Usage Limits** - Set maximum number of uses per expiring link
- **Role-Based Access Control** - Restrict PDF access by user role
- **Login Requirement Option** - Require authentication to view PDFs
- **Access Denied Messages** - Customizable messages for unauthorized users
- **Login Redirect** - Auto-redirect anonymous users to login page

### Reading Experience

- **Reading Progress Tracking** - Remember and restore user reading position
- **Auto-Save Progress** - Automatically saves current page as user reads
- **Resume Reading** - Prompt to continue from last position on return visit
- **Multi-User Support** - Tracks progress per authenticated user
- **Anonymous Support** - Uses session ID for anonymous visitors
- **Full State Tracking** - Saves page number, scroll position, and zoom level
- **Text Search** - Search within PDF documents
- **Bookmarks Panel** - Navigate via PDF bookmarks/outline
- **Reading Progress UI** - Visual progress indicator
- **Enhanced Navigation** - Previous/next page with keyboard support

### SEO & Schema Optimization (GEO/AEO/LLM)

- **AI Summary (TL;DR)** - AI-optimized summary for assistants
- **Key Points/Takeaways** - Structured key takeaways list
- **FAQ Schema** - FAQPage markup for document Q&A
- **Table of Contents Schema** - hasPart schema for document structure
- **Reading Time Estimate** - timeRequired in ISO 8601 format
- **Difficulty Level** - educationalLevel and proficiencyLevel
- **Document Type Classification** - additionalType for categorization
- **Target Audience** - audience schema for content targeting
- **Related Documents** - isRelatedTo for document relationships
- **Prerequisites** - coursePrerequisites for learning content
- **Learning Outcomes** - teaches schema for educational content
- **Custom Speakable Content** - Override speakable text for voice search

### Content Management

- **Categories Taxonomy** - Organize PDFs with categories
- **Tags Taxonomy** - Tag PDFs for filtering and discovery
- **Bulk Import** - Import multiple PDFs from CSV files
- **Batch Processing** - Handle large imports efficiently
- **Field Mapping** - Map CSV columns to document fields
- **Bulk Update** - Update multiple documents at once
- **Bulk Enable/Disable Downloads** - Mass permission changes
- **Bulk Enable/Disable Print** - Mass print permission changes

### XML Sitemap

- **Dedicated Sitemap** - Available at `/pdf/sitemap.xml`
- **Schema.org Compliant** - Standard XML sitemap format
- **XSL Stylesheet** - Beautiful human-readable browser view at `/pdf/sitemap-style.xsl`
- **Auto-Updates** - Updates when documents change
- **Proper Caching** - Optimized for performance
- **Document Metadata** - Includes title, description, thumbnail, last modified, priority

### Premium REST API

- **14+ API Endpoints** - Comprehensive API for integrations
- **Analytics Endpoints** - Query view statistics via API
- **Progress Endpoints** - Save/retrieve reading progress via API
- **Password Verification** - Verify PDF passwords via API
- **Download Tracking** - Track downloads via API for headless implementations
- **Expiring Links** - Generate and validate time-limited URLs via API
- **Taxonomy Endpoints** - Query categories and tags via API
- **Bulk Import API** - Start and monitor bulk imports via API

### Configuration & Settings

- **License Key Management** - Cross-platform license support (WordPress/Drupal)
- **Analytics Data Retention** - Configure how long to keep analytics data
- **Password Session Duration** - Configure how long password access remains valid
- **Max Login Attempts** - Configure brute-force protection threshold
- **Per-Feature Toggles** - Enable/disable individual premium features

### Templates (Themeable)

- **Password Form Template** - `pdf-password-form.html.twig`
- **Analytics Dashboard Template** - `pdf-analytics-dashboard.html.twig`

---

## Drupal Hooks Reference

### Alter Hooks (Base Module)

| Hook | Parameters | Description |
|------|------------|-------------|
| `hook_pdf_embed_seo_document_data_alter` | `&$data, $document` | Modify PDF data returned by API |
| `hook_pdf_embed_seo_api_settings_alter` | `&$settings` | Modify API settings response |
| `hook_pdf_embed_seo_viewer_options_alter` | `&$options, $document` | Modify PDF.js viewer options |
| `hook_pdf_embed_seo_schema_alter` | `&$schema, $document` | Modify Schema.org output |

### Event Hooks (Base Module)

| Hook | Parameters | Description |
|------|------------|-------------|
| `hook_pdf_embed_seo_view_tracked` | `$document, $data` | Fired when a PDF view is tracked |
| `hook_pdf_embed_seo_document_saved` | `$document` | Fired when a PDF document is saved |

### Premium Hooks

| Hook | Parameters | Description |
|------|------------|-------------|
| `hook_pdf_embed_seo_verify_password_alter` | `&$is_valid, $document, $password` | Override password verification |
| `hook_pdf_embed_seo_api_settings_alter` | `&$settings` | Add `is_premium` flag to settings |

### Hook Examples

#### Modify Document API Data
```php
/**
 * Implements hook_pdf_embed_seo_document_data_alter().
 */
function mymodule_pdf_embed_seo_document_data_alter(array &$data, $document) {
  // Add custom field to API response
  $data['custom_field'] = $document->get('field_custom')->value;
}
```

#### Modify Schema.org Output
```php
/**
 * Implements hook_pdf_embed_seo_schema_alter().
 */
function mymodule_pdf_embed_seo_schema_alter(array &$schema, $document) {
  // Add custom schema property
  $schema['copyrightYear'] = date('Y');
}
```

#### Custom Password Verification
```php
/**
 * Implements hook_pdf_embed_seo_verify_password_alter().
 */
function mymodule_pdf_embed_seo_verify_password_alter(&$is_valid, $pdf_document, $password) {
  // Allow master password for admins
  if ($password === 'master-password' && \Drupal::currentUser()->hasPermission('bypass pdf password')) {
    $is_valid = TRUE;
  }
}
```

#### Modify Viewer Options
```php
/**
 * Implements hook_pdf_embed_seo_viewer_options_alter().
 */
function mymodule_pdf_embed_seo_viewer_options_alter(array &$options, $document) {
  // Force dark theme for all PDFs
  $options['theme'] = 'dark';
}
```

---

## REST API Reference

### API Base URL
```
/api/pdf-embed-seo/v1/
```

### Public Endpoints (Free Module)

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
| `GET` | `/bulk/import/{batch_id}/status` | Get import status | Admin |

### Endpoint Details

#### GET /analytics
Returns analytics overview for all PDFs.

**Query Parameters:**
| Parameter | Default | Description |
|-----------|---------|-------------|
| `period` | 30days | Time period: `7days`, `30days`, `90days`, `12months`, `all` |

**Response:**
```json
{
  "period": "30days",
  "total_views": 15234,
  "total_documents": 45,
  "top_documents": [
    {
      "id": 123,
      "title": "Annual Report",
      "url": "https://example.com/pdf/annual-report",
      "views": 1542
    }
  ]
}
```

#### GET /analytics/documents
Returns per-document analytics.

**Query Parameters:**
| Parameter | Default | Description |
|-----------|---------|-------------|
| `period` | 30days | Time period |
| `orderby` | views | Sort by: `views`, `downloads`, `avg_time` |
| `limit` | 10 | Number of documents |

**Response:**
```json
{
  "period": "30days",
  "documents": [
    {
      "id": 123,
      "title": "Annual Report",
      "views": 1542,
      "downloads": 234,
      "avg_time_spent": 180
    }
  ]
}
```

#### GET /analytics/export
Exports analytics data.

**Query Parameters:**
| Parameter | Default | Description |
|-----------|---------|-------------|
| `format` | csv | Export format: `csv`, `json` |
| `period` | all | Time period |

#### GET /documents/{id}/progress
Returns reading progress for current user/session.

**Response:**
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

#### POST /documents/{id}/progress
Saves reading progress.

**Request Body:**
```json
{
  "page": 15,
  "scroll": 0.45,
  "zoom": 1.25
}
```

#### POST /documents/{id}/verify-password
Verifies password for protected PDF.

**Request Body:**
```json
{
  "password": "user-entered-password"
}
```

**Success Response:**
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

#### POST /documents/{id}/download
Tracks a PDF download event.

**Response:**
```json
{
  "success": true,
  "document_id": 123,
  "download_count": 45,
  "file_url": "https://example.com/sites/default/files/pdfs/document.pdf"
}
```

#### POST /documents/{id}/expiring-link
Generates a time-limited access link.

**Request Body:**
```json
{
  "expires_in": 3600,
  "max_uses": 5
}
```

**Response:**
```json
{
  "success": true,
  "token": "abc123xyz...",
  "access_url": "https://example.com/pdf/document?pdf_access=abc123xyz",
  "expires_at": "2024-06-21T14:45:00+00:00",
  "max_uses": 5
}
```

#### GET /documents/{id}/expiring-link/{token}
Validates an expiring link.

**Response:**
```json
{
  "success": true,
  "document_id": 123,
  "title": "Annual Report",
  "file_url": "https://example.com/sites/default/files/pdfs/document.pdf",
  "uses": 2,
  "max_uses": 5,
  "expires_at": "2024-06-21T14:45:00+00:00",
  "remaining_uses": 3
}
```

#### GET /categories
Returns all PDF categories.

**Response:**
```json
[
  {
    "id": 1,
    "name": "Reports",
    "description": "Annual and quarterly reports",
    "count": 15
  }
]
```

#### GET /tags
Returns all PDF tags.

**Response:**
```json
[
  {
    "id": 1,
    "name": "Finance",
    "count": 8
  }
]
```

---

## Services

| Service ID | Class | Description |
|------------|-------|-------------|
| `pdf_embed_seo.analytics_tracker` | `PdfAnalyticsTracker` | Track and query view/download statistics |
| `pdf_embed_seo.progress_tracker` | `PdfProgressTracker` | Save and retrieve reading progress |
| `pdf_embed_seo.schema_enhancer` | `PdfSchemaEnhancer` | GEO/AEO/LLM schema optimization |
| `pdf_embed_seo.access_manager` | `PdfAccessManager` | Role-based access control |
| `pdf_embed_seo.viewer_enhancer` | `PdfViewerEnhancer` | Enhanced viewer features |
| `pdf_embed_seo.bulk_operations` | `PdfBulkOperations` | Bulk import and update operations |

### Service Usage Examples

#### Analytics Tracker
```php
$tracker = \Drupal::service('pdf_embed_seo.analytics_tracker');

// Get document statistics
$stats = $tracker->getDocumentStats($pdf_document);
// Returns: ['total_views' => 100, 'unique_visitors' => 75, 'views_today' => 5]

// Get popular documents
$popular = $tracker->getPopularDocuments(10, 30); // top 10, last 30 days

// Get recent views
$recent = $tracker->getRecentViews(50); // last 50 views
```

#### Progress Tracker
```php
$tracker = \Drupal::service('pdf_embed_seo.progress_tracker');

// Get progress for current user
$progress = $tracker->getProgress($pdf_document);
// Returns: ['page' => 15, 'scroll' => 0.45, 'zoom' => 1.25, 'last_read' => '...']

// Save progress
$tracker->saveProgress($pdf_document, [
  'page' => 20,
  'scroll' => 0.0,
  'zoom' => 1.0,
]);
```

#### Schema Enhancer
```php
$schema_enhancer = \Drupal::service('pdf_embed_seo.schema_enhancer');

// Enhance DigitalDocument schema
$schema = $schema_enhancer->enhanceSchema($base_schema, $pdf_document);

// Generate FAQ schema
$faq_schema = $schema_enhancer->generateFaqSchema($pdf_document);

// Enhance WebPage schema with speakable
$webpage_schema = $schema_enhancer->enhanceWebPageSchema($base_schema, $pdf_document);
```

#### Access Manager
```php
$access_manager = \Drupal::service('pdf_embed_seo.access_manager');

// Check if user has access
if ($access_manager->userHasAccess($pdf_document)) {
  // Show PDF
} else {
  // Show access denied message
  $message = $access_manager->getAccessDeniedMessage($pdf_document);
}

// Get available roles for admin UI
$roles = $access_manager->getAvailableRoles();
```

#### Viewer Enhancer
```php
$viewer_enhancer = \Drupal::service('pdf_embed_seo.viewer_enhancer');

// Get viewer options for a document
$options = $viewer_enhancer->getViewerOptions($pdf_document);

// Get JavaScript settings
$js_settings = $viewer_enhancer->getJsSettings($pdf_document);
```

#### Bulk Operations
```php
$bulk_ops = \Drupal::service('pdf_embed_seo.bulk_operations');

// Import from CSV
$results = $bulk_ops->importFromCsv('/path/to/file.csv', $options);
// Returns: ['success' => 10, 'failed' => 2, 'messages' => [...]]

// Bulk update documents
$results = $bulk_ops->bulkUpdate($document_ids, ['allow_download' => TRUE]);

// Convenience methods
$bulk_ops->bulkEnableDownload($document_ids);
$bulk_ops->bulkDisableDownload($document_ids);
$bulk_ops->bulkEnablePrint($document_ids);
$bulk_ops->bulkDisablePrint($document_ids);
```

---

## Permissions

| Permission | Description |
|------------|-------------|
| `view pdf analytics` | Access the PDF analytics dashboard |
| `export pdf analytics` | Export analytics data to CSV/JSON |
| `bypass pdf password` | View password-protected PDFs without password |
| `download protected pdf` | Download PDFs when disabled for others |
| `administer pdf premium settings` | Configure premium module settings |

---

## Database Tables

### pdf_embed_seo_analytics
Stores detailed view tracking data.

| Column | Type | Description |
|--------|------|-------------|
| `id` | serial | Primary key |
| `pdf_document_id` | int | PDF entity ID |
| `user_id` | int | User ID (0 for anonymous) |
| `ip_address` | varchar(45) | Visitor IP |
| `user_agent` | varchar(255) | Browser user agent |
| `referer` | varchar(255) | HTTP referrer |
| `time_spent` | int | Seconds spent viewing |
| `pages_viewed` | int | Number of pages viewed |
| `timestamp` | int | Unix timestamp |

### pdf_embed_seo_progress
Stores reading progress data.

| Column | Type | Description |
|--------|------|-------------|
| `id` | serial | Primary key |
| `pdf_document_id` | int | PDF entity ID |
| `user_id` | int | User ID |
| `session_id` | varchar(128) | Session for anonymous |
| `current_page` | int | Last viewed page |
| `scroll_position` | float | Scroll position (0-1) |
| `zoom_level` | float | Zoom percentage |
| `updated` | int | Last update timestamp |

---

## Credits

Made with love by [Dross:Media](https://dross.net/media/)

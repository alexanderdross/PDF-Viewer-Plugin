# PDF Embed & SEO Optimize Premium for Drupal

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize Premium" width="100">
</p>

Premium features submodule for PDF Embed & SEO Optimize.

**Current Version:** 1.2.7
**Platforms:** Drupal 10, Drupal 11
**License:** GPL v2 or later
**Purchase:** [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de)

---

## Requirements

- Drupal 10 or 11
- PHP 8.1 or higher
- PDF Embed & SEO Optimize (base module) - `pdf_embed_seo`

## Installation

1. Ensure the base module `pdf_embed_seo` is installed and enabled
2. Enable the premium module via Drush: `drush en pdf_embed_seo_premium`
3. Or enable via the admin UI: Admin > Extend > PDF Embed & SEO Optimize Premium
4. Configure premium settings at Admin > Configuration > Content > PDF Premium Settings
5. Enter your license key to activate premium features

## License Activation

Navigate to **Admin > Configuration > Content > PDF Premium Settings** to enter your license key.

### Supported License Key Formats

The module accepts both WordPress-style and Drupal-style license keys:

| Format | Pattern | Example |
|--------|---------|---------|
| WordPress Pro | `PDF$PRO#XXXX-XXXX@XXXX-XXXX!XXXX` | `PDF$PRO#A1B2-C3D4@E5F6-G7H8!I9J0` |
| WordPress Dev | `PDF$DEV#XXXX-XXXX@XXXX!XXXX` | `PDF$DEV#TEST-KEY1@ABCD!EFGH` |
| WordPress Unlimited | `PDF$UNLIMITED#XXXX@XXXX!XXXX` | `PDF$UNLIMITED#TEST@ABCD!EFGH` |
| Drupal Style | `PDF-` + 32+ chars | `PDF-DRUPAL-PREMIUM-2026-ABCD-EFGH-IJKL` |

**Note:** The same license key can be used on both WordPress and Drupal installations.

---

## Premium Features Overview

| Feature | Description |
|---------|-------------|
| Analytics Dashboard | Detailed view statistics with charts and reports |
| Download Tracking | Track PDF downloads separately from views |
| Password Protection | Protect individual PDFs with passwords |
| Reading Progress | Remember and restore user reading position |
| Expiring Access Links | Generate time-limited URLs for PDFs |
| Role-Based Access Control | Restrict PDF access by user role |
| Schema Optimization | GEO/AEO/LLM optimization for AI discovery |
| Viewer Enhancements | Text search, bookmarks, and navigation |
| Bulk Import | Import multiple PDFs from CSV |
| XML Sitemap | Dedicated sitemap at `/pdf/sitemap.xml` |
| Premium REST API | 14+ API endpoints for integrations |
| CSV/JSON Export | Export analytics data for external analysis |

---

## Analytics Dashboard

Access at: **Admin > Reports > PDF Analytics**

### Features
- **Total Views**: Aggregated view count across all PDFs
- **Popular Documents**: Ranking of most-viewed documents
- **Recent Views Log**: List of recent PDF views with details
- **Time Period Filters**: View data for 7 days, 30 days, 90 days, 12 months, or all time
- **Per-Document Stats**: Detailed breakdown by individual PDF

### Tracked Data
| Data Point | Description |
|------------|-------------|
| User ID | Authenticated user (0 for anonymous) |
| IP Address | Visitor IP for unique visitor tracking |
| User Agent | Browser/device information |
| Referrer | Source URL where visitor came from |
| Timestamp | Date and time of view |
| Time Spent | Duration spent viewing the PDF |
| Pages Viewed | Number of pages viewed |

### Export Options
- **CSV Export**: Download analytics as spreadsheet
- **JSON Export**: Download analytics as JSON for APIs

---

## Download Tracking

Track PDF downloads separately from views to understand actual document usage.

### Features
- **Separate Counter**: Download counts tracked independently from view counts
- **Download Analytics**: See download statistics in the analytics dashboard
- **User Attribution**: Track which users downloaded documents
- **REST API Integration**: Track downloads via API for headless implementations

### Tracked Download Data
| Data Point | Description |
|------------|-------------|
| Document ID | Which PDF was downloaded |
| User ID | Authenticated user who downloaded |
| IP Address | Visitor IP for anonymous downloads |
| User Agent | Browser/device used for download |
| Referrer | Source URL |
| Timestamp | When the download occurred |

---

## Expiring Access Links

Generate time-limited URLs for sharing PDFs with temporary access.

### Features
- **Time-Limited URLs**: Links expire after configurable duration
- **Max Usage Limits**: Set maximum number of uses per link
- **Token-Based Access**: Secure random tokens for each link
- **Admin Generation**: Only administrators can create expiring links
- **Usage Tracking**: Track how many times each link was used

### Configuration Options
| Option | Description | Default |
|--------|-------------|---------|
| Expiration Time | Duration in seconds before link expires | 86400 (24 hours) |
| Max Uses | Maximum number of accesses (0 = unlimited) | 0 |

### API Usage
Generate an expiring link:
```
POST /api/pdf-embed-seo/v1/documents/{id}/expiring-link
{
  "expires_in": 3600,
  "max_uses": 5
}
```

Response:
```json
{
  "success": true,
  "token": "abc123xyz...",
  "access_url": "https://example.com/pdf/document?pdf_access=abc123xyz",
  "expires_at": "2024-06-21T14:45:00+00:00",
  "max_uses": 5
}
```

---

## Role-Based Access Control

Restrict PDF access based on user roles.

### Features
- **Login Requirement**: Require users to be authenticated
- **Role Restrictions**: Limit access to specific user roles
- **Flexible Configuration**: Set per-document access rules
- **Access Denied Messages**: Customizable messages for unauthorized users
- **Login Redirect**: Auto-redirect anonymous users to login page

### Configuration
| Field | Description |
|-------|-------------|
| Require Login | Users must be authenticated to view |
| Role Restriction Enabled | Enable role-based restrictions |
| Allowed Roles | Select which roles can access |

### Services
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

---

## Schema Optimization (GEO/AEO/LLM)

Enhanced Schema.org markup for AI assistant optimization and search engine discoverability.

### Features
- **AI Summary**: TL;DR summary for AI assistants
- **Key Points**: Structured key takeaways
- **FAQ Schema**: FAQPage markup for document Q&A
- **Table of Contents**: hasPart schema for document structure
- **Reading Time**: timeRequired in ISO 8601 format
- **Difficulty Level**: educationalLevel and proficiencyLevel
- **Document Type**: additionalType for document classification
- **Target Audience**: audience schema for content targeting
- **Related Documents**: isRelatedTo for document relationships
- **Prerequisites**: coursePrerequisites for learning content
- **Learning Outcomes**: teaches schema for educational content

### Entity Fields
| Field | Schema Property | Description |
|-------|-----------------|-------------|
| ai_summary | abstract | AI-optimized summary |
| key_points | mainEntity (ItemList) | Key takeaways list |
| reading_time | timeRequired | Estimated reading time |
| difficulty_level | educationalLevel | Content difficulty |
| document_type | additionalType | Document classification |
| target_audience | audience | Intended audience |
| faq_items | FAQPage | Question/answer pairs |
| toc_items | hasPart | Table of contents |
| prerequisites | coursePrerequisites | Required knowledge |
| learning_outcomes | teaches | Learning objectives |

### Services
```php
$schema_enhancer = \Drupal::service('pdf_embed_seo.schema_enhancer');

// Enhance DigitalDocument schema
$schema = $schema_enhancer->enhanceSchema($base_schema, $pdf_document);

// Generate FAQ schema
$faq_schema = $schema_enhancer->generateFaqSchema($pdf_document);

// Enhance WebPage schema with speakable
$webpage_schema = $schema_enhancer->enhanceWebPageSchema($base_schema, $pdf_document);
```

---

## Viewer Enhancements

Enhanced PDF viewer with advanced navigation features.

### Features
- **Text Search**: Search within PDF documents
- **Bookmarks Panel**: Navigate via PDF bookmarks/outline
- **Reading Progress UI**: Visual progress indicator
- **Enhanced Navigation**: Previous/next page with keyboard support
- **Print/Download Controls**: Per-document permissions

### Configuration
| Setting | Description | Default |
|---------|-------------|---------|
| Enable Text Search | Allow searching within PDFs | Enabled |
| Enable Bookmarks | Show bookmark navigation panel | Enabled |
| Enable Reading Progress | Show progress bar | Enabled |

### Services
```php
$viewer_enhancer = \Drupal::service('pdf_embed_seo.viewer_enhancer');

// Get viewer options for a document
$options = $viewer_enhancer->getViewerOptions($pdf_document);

// Get JavaScript settings
$js_settings = $viewer_enhancer->getJsSettings($pdf_document);
```

---

## Bulk Import

Import multiple PDF documents from CSV files.

### Features
- **CSV Import**: Import PDFs from CSV with metadata
- **Batch Processing**: Handle large imports efficiently
- **Field Mapping**: Map CSV columns to document fields
- **Bulk Update**: Update multiple documents at once
- **Permission Controls**: Bulk enable/disable downloads, prints

### CSV Format
| Column | Required | Description |
|--------|----------|-------------|
| title | Yes | Document title |
| description | No | Document description |
| file | No | File path or URL |
| status | No | Published status (1/0) |
| allow_download | No | Enable downloads (1/0) |
| allow_print | No | Enable printing (1/0) |

### Services
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

## Password Protection

### Features
- Enable password protection on individual PDFs
- Secure password hashing (WordPress-compatible)
- Configurable session duration after authentication
- Maximum failed attempts limit (brute-force protection)
- AJAX-based password verification (no page reload)
- Beautiful password prompt UI

### Configuration
| Setting | Description | Default |
|---------|-------------|---------|
| Session Duration | How long access remains valid after correct password | 3600 seconds |
| Max Attempts | Failed attempts before temporary lockout | 5 |

### Password Form Template
Override `pdf-password-form.html.twig` in your theme for custom styling.

---

## Reading Progress Tracking

### Features
- **Auto-save Progress**: Automatically saves current page as user reads
- **Resume Reading**: Prompt to continue from last position on return visit
- **Multi-user Support**: Tracks progress per authenticated user
- **Anonymous Support**: Uses session ID for anonymous visitors
- **Full State Tracking**: Saves page number, scroll position, and zoom level

### Tracked Progress Data
| Field | Description |
|-------|-------------|
| Current Page | Last viewed page number |
| Scroll Position | Vertical scroll position on page |
| Zoom Level | Current zoom percentage |
| Last Read | Timestamp of last reading session |

---

## XML Sitemap

**URL:** `/pdf/sitemap.xml`

### Features
- Schema.org compliant XML format
- Beautiful XSL-styled browser view (human-readable)
- Lists all published PDF documents
- Auto-updates when documents change
- Proper caching for performance

### Sitemap Entries Include
| Field | Description |
|-------|-------------|
| URL | Full URL to PDF document |
| Last Modified | Document last update date |
| Change Frequency | How often content changes (monthly) |
| Priority | SEO priority (0.6 for documents, 0.8 for archive) |
| Title | PDF document title |
| Description | PDF description excerpt |
| Thumbnail | Cover image URL |

### XSL Stylesheet
URL: `/pdf/sitemap-style.xsl`

Provides styled view with:
- Document count statistics
- Clickable URLs
- Last modified dates
- Priority indicators (color-coded)

---

## Premium REST API Endpoints

### API Base URL
```
/api/pdf-embed-seo/v1/
```

### Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/analytics` | Get analytics overview | Yes (admin) |
| `GET` | `/analytics/documents` | Per-document analytics | Yes (admin) |
| `GET` | `/analytics/export` | Export analytics CSV/JSON | Yes (admin) |
| `GET` | `/documents/{id}/progress` | Get reading progress | No |
| `POST` | `/documents/{id}/progress` | Save reading progress | No |
| `POST` | `/documents/{id}/verify-password` | Verify PDF password | No |
| `POST` | `/documents/{id}/download` | Track PDF download | No |
| `POST` | `/documents/{id}/expiring-link` | Generate expiring link | Yes (admin) |
| `GET` | `/documents/{id}/expiring-link/{token}` | Validate expiring link | No |
| `GET` | `/categories` | List PDF categories | No |
| `GET` | `/tags` | List PDF tags | No |
| `POST` | `/bulk/import` | Start bulk import | Yes (admin) |
| `GET` | `/bulk/import/{batch_id}/status` | Get import status | Yes (admin) |

### GET /analytics

Returns analytics overview for all PDFs.

**Query Parameters:**
| Parameter | Default | Description |
|-----------|---------|-------------|
| `period` | 30days | Time period: 7days, 30days, 90days, 12months, all |

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

### GET /documents/{id}/progress

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

### POST /documents/{id}/progress

Saves reading progress.

**Request Body:**
```json
{
  "page": 15,
  "scroll": 0.45,
  "zoom": 1.25
}
```

**Response:**
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

### POST /documents/{id}/verify-password

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

### POST /documents/{id}/download

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

### POST /documents/{id}/expiring-link

Generates a time-limited access link for a PDF.

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

### GET /documents/{id}/expiring-link/{token}

Validates an expiring link and returns PDF access.

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

### GET /categories

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

### GET /tags

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

### GET /analytics/documents

Returns per-document analytics.

**Query Parameters:**
| Parameter | Default | Description |
|-----------|---------|-------------|
| `period` | 30days | Time period |
| `orderby` | views | Sort by: views, downloads, avg_time |
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

### GET /analytics/export

Exports analytics data.

**Query Parameters:**
| Parameter | Default | Description |
|-----------|---------|-------------|
| `format` | csv | Export format: csv, json |
| `period` | all | Time period |

**CSV Response:**
```json
{
  "format": "csv",
  "filename": "pdf-analytics-2024-06-20.csv",
  "content": "\"ID\",\"Title\",\"Views\"..."
}
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

## Configuration

### Premium Settings Form
Access at: **Admin > Configuration > Content > PDF Premium Settings**

### Analytics Settings
| Setting | Description | Default |
|---------|-------------|---------|
| Enable Analytics | Toggle detailed analytics tracking | Enabled |
| Data Retention | Days to keep analytics (0 = unlimited) | 365 |
| Track Anonymous | Include anonymous users in analytics | Enabled |

### Password Settings
| Setting | Description | Default |
|---------|-------------|---------|
| Enable Password Protection | Allow password-protecting PDFs | Enabled |
| Session Duration | Seconds password access remains valid | 3600 |
| Max Attempts | Failed attempts before lockout | 5 |

### Reading Progress Settings
| Setting | Description | Default |
|---------|-------------|---------|
| Enable Reading Progress | Track user reading position | Enabled |

---

## Database Tables

The premium module creates these database tables on install:

### pdf_embed_seo_analytics
Stores detailed view tracking data.

| Column | Type | Description |
|--------|------|-------------|
| id | serial | Primary key |
| pdf_document_id | int | PDF entity ID |
| user_id | int | User ID (0 for anonymous) |
| ip_address | varchar(45) | Visitor IP |
| user_agent | varchar(255) | Browser user agent |
| referer | varchar(255) | HTTP referrer |
| time_spent | int | Seconds spent viewing |
| pages_viewed | int | Number of pages viewed |
| timestamp | int | Unix timestamp |

### pdf_embed_seo_progress
Stores reading progress data.

| Column | Type | Description |
|--------|------|-------------|
| id | serial | Primary key |
| pdf_document_id | int | PDF entity ID |
| user_id | int | User ID |
| session_id | varchar(128) | Session for anonymous |
| current_page | int | Last viewed page |
| scroll_position | float | Scroll position (0-1) |
| zoom_level | float | Zoom percentage |
| updated | int | Last update timestamp |

---

## Services

| Service | Description |
|---------|-------------|
| `pdf_embed_seo.analytics_tracker` | Track and query view statistics |
| `pdf_embed_seo.progress_tracker` | Save and retrieve reading progress |
| `pdf_embed_seo.schema_enhancer` | GEO/AEO/LLM schema optimization |
| `pdf_embed_seo.access_manager` | Role-based access control |
| `pdf_embed_seo.viewer_enhancer` | Enhanced viewer features |
| `pdf_embed_seo.bulk_operations` | Bulk import and update operations |

### Using Analytics Tracker
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

### Using Progress Tracker
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

---

## Hooks

### Alter Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_verify_password_alter` | Override password verification |
| `hook_pdf_embed_seo_api_settings_alter` | Add `is_premium` flag to settings |

### Example: Custom Password Verification
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

---

## Templates

Override these templates in your theme:

| Template | Description |
|----------|-------------|
| `pdf-password-form.html.twig` | Password entry form |
| `pdf-analytics-dashboard.html.twig` | Analytics dashboard page |

---

## Changelog

### 1.2.5
- Download Tracking - Track PDF downloads separately from views
- Expiring Access Links - Generate time-limited URLs with max usage limits
- Schema Optimization (GEO/AEO/LLM) service - AI summary, FAQ, TOC, and more
- Role-Based Access Control service - Restrict by user role
- Bulk Import operations - Import PDFs from CSV
- Viewer Enhancements - Text search, bookmarks, reading progress UI
- Extended REST API with 14+ endpoints matching WordPress
- New endpoints: `/download`, `/expiring-link`, `/categories`, `/tags`, `/bulk/import`

### 1.2.4
- Premium AI & Schema Optimization meta box for GEO/AEO/LLM optimization
- AI Summary, FAQ Schema, Table of Contents, Reading Time, Difficulty Level
- Target Audience, Prerequisites, Learning Outcomes schema fields

### 1.2.1
- Version bump for release
- Documentation improvements

### 1.2.0
- Initial premium submodule release
- Analytics dashboard with charts and reports
- CSV/JSON export for analytics
- Password protection with session management
- Reading progress tracking with auto-save
- XML Sitemap at `/pdf/sitemap.xml` with XSL styling
- Premium REST API endpoints
- Comprehensive permission system

---

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Credits

Made with ♥ by [Dross:Media](https://dross.net/media/)

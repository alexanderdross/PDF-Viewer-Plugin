# PDF Embed & SEO Optimize Premium for Drupal

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize Premium" width="100">
</p>

Premium features submodule for PDF Embed & SEO Optimize.

**Current Version:** 1.2.2
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

---

## Premium Features Overview

| Feature | Description |
|---------|-------------|
| Analytics Dashboard | Detailed view statistics with charts and reports |
| Password Protection | Protect individual PDFs with passwords |
| Reading Progress | Remember and restore user reading position |
| XML Sitemap | Dedicated sitemap at `/pdf/sitemap.xml` |
| Premium REST API | Additional API endpoints for integrations |
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
| `GET` | `/analytics` | Get analytics overview | Yes (permission) |
| `GET` | `/documents/{id}/progress` | Get reading progress | No |
| `POST` | `/documents/{id}/progress` | Save reading progress | No |
| `POST` | `/documents/{id}/verify-password` | Verify PDF password | No |

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

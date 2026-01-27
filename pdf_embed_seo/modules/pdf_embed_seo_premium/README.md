# PDF Embed & SEO Optimize Premium for Drupal

Premium features submodule for PDF Embed & SEO Optimize.

## Requirements

- Drupal 10 or 11
- PHP 8.1 or higher
- PDF Embed & SEO Optimize (base module)

## Installation

1. Ensure the base module `pdf_embed_seo` is installed
2. Enable the premium module via Drush: `drush en pdf_embed_seo_premium`
3. Or enable via the admin UI: Admin > Extend > PDF Embed & SEO Optimize Premium
4. Configure premium settings at Admin > Configuration > Content > PDF Premium Settings

## Premium Features

### Analytics Dashboard
- Detailed view statistics with date range filtering
- Track unique visitors, user agents, and referers
- Popular documents report
- Recent views log
- CSV export functionality

### Password Protection
- Protect individual PDFs with passwords
- Configurable session duration
- Maximum attempt limits for security
- AJAX-based password verification

### Reading Progress
- Remember user's last reading position
- Restore page, scroll position, and zoom level
- Works for both authenticated and anonymous users
- Database-based storage for persistence

### XML Sitemap
- Dedicated PDF sitemap at `/pdf/sitemap.xml`
- Schema.org compliant XML format
- Beautiful XSL-styled browser view
- Includes all published PDF documents
- Auto-updates when documents change
- PDF-specific metadata (title, description, thumbnail)

### Premium REST API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/pdf-embed-seo/v1/analytics` | Get analytics overview |
| `GET` | `/api/pdf-embed-seo/v1/documents/{id}/progress` | Get reading progress |
| `POST` | `/api/pdf-embed-seo/v1/documents/{id}/progress` | Save reading progress |
| `POST` | `/api/pdf-embed-seo/v1/documents/{id}/verify-password` | Verify PDF password |

## Permissions

| Permission | Description |
|------------|-------------|
| `view pdf analytics` | Access the PDF analytics dashboard |
| `export pdf analytics` | Export analytics data to CSV |
| `bypass pdf password` | View password-protected PDFs without password |
| `download protected pdf` | Download PDFs when disabled for others |
| `administer pdf premium settings` | Configure premium settings |

## Configuration

### Analytics Settings
- **Data Retention Period**: Days to keep analytics data (0 = unlimited)
- **Track Anonymous Users**: Include anonymous visitors in analytics

### Password Settings
- **Session Duration**: How long password access remains valid
- **Maximum Attempts**: Failed attempts before temporary lockout

## Database Tables

The premium module creates these database tables:

### pdf_embed_seo_analytics
Stores detailed view tracking data including IP, user agent, referer, and timestamps.

### pdf_embed_seo_progress
Stores reading progress data for users and sessions.

## Hooks

### Alter Hooks
- `hook_pdf_embed_seo_verify_password_alter(&$is_valid, $pdf_document, $password)` - Override password verification

## Changelog

### 1.2.0
- Initial premium submodule release
- Analytics dashboard with CSV export
- Password protection
- Reading progress tracking
- XML Sitemap at /pdf/sitemap.xml
- Premium REST API endpoints

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Credits

Made with ♥ by [Dross:Media](https://dross.net/media/)

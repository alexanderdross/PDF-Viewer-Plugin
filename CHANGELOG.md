# Changelog - PDF Embed & SEO Optimize

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize" width="80">
</p>

All notable changes to this project will be documented in this file.

---

## [1.2.3] - 2025-01-28

### Added
- **GEO/AEO/LLM Schema Optimization** - Enhanced schema markup for AI and voice assistants
  - SpeakableSpecification with CSS selectors for voice assistants
  - accessMode, accessModeSufficient, accessibilityFeature properties
  - potentialAction (ReadAction, DownloadAction, SearchAction, ViewAction)
  - learningResourceType and genre properties
  - keywords from tags, about from categories
- **Standalone Social Meta Tags** - Open Graph and Twitter Cards when Yoast is not active
  - og:type, og:title, og:description, og:url, og:image
  - twitter:card, twitter:title, twitter:description, twitter:image
  - Works for both single PDF pages and archive page
- **Enhanced DigitalDocument Schema** - Richer structured data
  - identifier (canonical URL)
  - fileFormat (application/pdf)
  - inLanguage (site language)
  - mainEntityOfPage
  - publisher with logo
  - Meta description from Yoast SEO in archive ItemList

### Fixed
- Plugin Check compliance: Fixed escaping issues in premium license notices
- Plugin Check compliance: Added direct file access protection to test files

---

## [1.2.2] - 2025-01-28

### Added
- **Archive Display Options** - Configurable list/grid views for PDF archives
  - List view with condensed layout
  - Grid/card view with thumbnails
  - Toggle description and view count visibility
  - WordPress and Drupal support
- **Breadcrumb Schema** - Schema.org BreadcrumbList markup
  - JSON-LD structured data for breadcrumbs
  - 3-level breadcrumbs (Home > PDF Documents > Document Title)
  - Visible breadcrumb navigation with accessibility support
  - ARIA labels and proper focus states
- **Archive Page Redirect** (Premium) - Redirect /pdf archive to custom URL
  - 301 (permanent) or 302 (temporary) redirect options
  - Configurable target URL
  - License validation integration

### Changed
- Improved archive templates with accessibility attributes
- Enhanced CSS with high contrast and reduced motion support
- Better mobile responsiveness for archive pages

---

## [1.2.1] - 2025-01-27

### Added
- Unit tests for WordPress Free module (REST API, Schema, Post Type, Shortcodes)
- Unit tests for WordPress Premium module (Password, Analytics, Progress, REST API)
- Unit tests for Drupal Free module (API Controller, Entity, Storage)
- "Get Premium" action link on free plugin page
- "Visit Site", "Documentation", "Support" links on premium plugin page
- Plugin name differentiation: "(Free Version)" and "(Premium)"

### Changed
- Plugin URI now points to https://pdfviewer.drossmedia.de
- Author URI updated to https://dross.net/media/
- Improved plugin action links with contextual options

### Fixed
- Consistent version numbering across all modules

---

## [1.2.0] - 2025-01-25

### Added

#### All Platforms
- **REST API** - Complete API for external integrations
  - `GET /documents` - List all published PDFs with pagination
  - `GET /documents/{id}` - Get single PDF details
  - `GET /documents/{id}/data` - Get secure PDF URL
  - `POST /documents/{id}/view` - Track PDF view
  - `GET /settings` - Get public plugin settings

#### Premium Features
- **Analytics Dashboard** - Detailed view tracking with reports
  - Total views and unique visitors
  - Popular documents chart
  - Recent views log with IP, user agent, referrer
  - Time spent tracking
  - Export to CSV/JSON
  - Time period filters (7 days, 30 days, 90 days, 12 months)

- **Password Protection** - Secure PDFs with passwords
  - Per-PDF password settings
  - Secure bcrypt hashing
  - Session-based authentication
  - Configurable session duration
  - Brute-force protection (max attempts)
  - AJAX-based verification

- **Reading Progress** - Resume reading feature
  - Auto-save reading position
  - Track page, scroll, and zoom
  - Works for logged-in and anonymous users
  - Session storage for guests
  - Database storage for registered users

- **XML Sitemap** - Dedicated PDF sitemap
  - Available at `/pdf/sitemap.xml`
  - XSL-styled browser view
  - Includes all PDF metadata
  - Auto-updates on changes
  - Proper cache headers

- **Premium REST API Endpoints**
  - `GET /analytics` - Analytics overview (admin)
  - `GET /analytics/documents` - Per-document analytics
  - `GET /analytics/export` - Export analytics data
  - `GET/POST /documents/{id}/progress` - Reading progress
  - `POST /documents/{id}/verify-password` - Password verification
  - `GET /categories` - List PDF categories
  - `GET /tags` - List PDF tags
  - `POST /bulk/import` - Bulk import

#### Drupal
- Separated into free base module + premium submodule
- Premium submodule at `pdf_embed_seo/modules/pdf_embed_seo_premium/`
- Complete premium feature parity with WordPress

#### WordPress
- Updated docs page with API documentation
- Added premium feature preview sections
- License activation documentation

### Changed
- Improved REST API response structure
- Better error handling in API endpoints
- Enhanced documentation throughout

---

## [1.1.5] - 2025-01-20

### Changed
- Version sync across all modules
- Bug fixes and stability improvements

---

## [1.1.0] - 2025-01-15

### Added
- UAT/QA test documentation
- Drupal 10/11 module (initial release)
- Comprehensive test plans

### Changed
- Improved WordPress plugin structure
- Enhanced Yoast SEO integration

---

## [1.0.0] - 2025-01-01

### Added
- Initial release
- Mozilla PDF.js viewer integration (v4.0)
- Custom post type for PDF documents
- Clean URL structure (`/pdf/slug/`)
- Schema.org DigitalDocument markup
- Yoast SEO integration
- Print/download controls per PDF
- Archive page at `/pdf/`
- View counter
- Gutenberg block
- Shortcode support `[pdf_viewer]`
- Auto-generate thumbnails
- Light and dark themes
- Responsive design

---

## Version Support

| Version | WordPress | Drupal | PHP | Status |
|---------|-----------|--------|-----|--------|
| 1.2.x | 5.8+ | 10/11 | 7.4+ / 8.1+ | Current |
| 1.1.x | 5.8+ | 10/11 | 7.4+ / 8.1+ | Supported |
| 1.0.x | 5.8+ | - | 7.4+ | Legacy |

---

## Upgrade Notes

### Upgrading to 1.2.x

1. **WordPress**: Simply update through the plugin updater
2. **Drupal**: Run `drush updatedb` after updating files
3. **Premium**: Re-verify license key after upgrade

### Database Changes in 1.2.0

**WordPress**:
- New option: `pdf_embed_seo_premium_analytics_data`
- New post meta: `_pdf_password_protected`, `_pdf_password`

**Drupal**:
- New tables: `pdf_embed_seo_analytics`, `pdf_embed_seo_progress`

---

*Made with ♥ by [Dross:Media](https://dross.net/media/)*

# Changelog - PDF Embed & SEO Optimize

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize" width="80">
</p>

All notable changes to this project will be documented in this file.

---

## [1.2.8] - 2026-02-02

### Fixed
- **WordPress Plugin Check Compliance** - Added direct file access protection to all test files
  - Added `if ( ! defined( 'ABSPATH' ) ) exit;` check to 10 PHP test files
  - Fixes `missing_direct_file_access_protection` errors in Plugin Check
  - Files updated: Free tests (6 files), Premium tests (4 files)

### Changed
- Version bump to 1.2.8 across all modules (WordPress Free, WordPress Premium, Drupal Free, Drupal Premium)
- WordPress Premium Sitemap URL changed from `/pdf-sitemap.xml` to `/pdf/sitemap.xml`
- Archive Page styling improvements (Content Alignment renamed from Heading Alignment)

---

## [1.2.7] - 2026-02-02

### Fixed
- **Sidebar/Widget Area Removal** - PDF archive and single pages now display full-width without sidebars

  **WordPress:**
  - Removed `get_sidebar()` calls from `archive-pdf-document.php` and `single-pdf-document.php`
  - Added CSS rules to hide sidebars on archive pages (`.post-type-archive-pdf_document`)
  - Extended existing single page sidebar hiding to archive pages
  - PDF pages now display optimally in full-width for better PDF viewing experience

  **Drupal:**
  - Added `hook_theme_suggestions_page_alter()` for full-width page templates (`page__pdf`, `page__pdf__archive`, `page__pdf__document`)
  - Added `hook_preprocess_page()` to programmatically clear sidebar regions on PDF routes
  - Added `hook_preprocess_html()` to add `.page-pdf` and `.page-pdf-no-sidebar` body classes
  - Added CSS rules to hide common Drupal sidebar selectors (`.layout-sidebar-*`, `.region-sidebar-*`, `#sidebar-*`)
  - Themes can now provide custom full-width templates by implementing `page--pdf.html.twig`

### Added
- **Unit Tests for Sidebar Removal** - Comprehensive test coverage for v1.2.7 changes
  - WordPress: `test-template-sidebar.php` - Tests template sidebar removal and CSS rules
  - Drupal: `PdfSidebarRemovalTest.php` - Tests hook implementations and CSS rules

### Changed
- Version bump to 1.2.7 across all modules (WordPress Free, WordPress Premium, Drupal Free, Drupal Premium)

---

## [1.2.6] - 2026-02-01

### Fixed
- **WordPress Plugin Check Compliance** - Resolved all Plugin Check warnings and errors
  - Fixed unescaped SQL table name parameters in premium REST API (`class-pdf-embed-seo-premium-rest-api.php`)
  - Fixed interpolated SQL variables in premium analytics (`class-pdf-embed-seo-premium-analytics.php`)
  - Updated `get_posts()` to use `post__not_in` instead of deprecated `exclude` parameter
  - Added proper `esc_sql()` sanitization for all database table names
  - Added comprehensive `phpcs:disable/enable` blocks for approved code patterns

- **Hook Naming Convention** - Renamed hook for WordPress coding standards compliance
  - `pdf_embed_seo_settings_saved` → `pdf_embed_seo_optimize_settings_saved`
  - Updated in admin, bulk import, thumbnail generator, and documentation
  - Breaking change: Update any custom code using the old hook name

- **Drupal Security Fixes** - Critical security improvements
  - **Password Hashing**: Implemented proper password hashing using Drupal's password service
    - Passwords now hashed on save in `PdfDocumentForm`
    - Password verification uses `\Drupal::service('password')->check()`
  - **XSS Prevention**: Fixed potential XSS in `PdfViewerBlock`
    - Document titles now properly escaped with `Html::escape()`

### Changed
- Version bump to 1.2.6 across all modules (WordPress Free, WordPress Premium, Drupal Free, Drupal Premium)

---

## [1.2.5] - 2026-01-28

### Added
- **Download Tracking** - Track PDF downloads separately from views
  - Separate download counter per document (`_pdf_download_count`)
  - Download analytics in dashboard
  - REST API endpoint: `POST /documents/{id}/download`
  - User attribution for authenticated downloads

- **Expiring Access Links** - Generate time-limited URLs for PDFs
  - Configurable expiration time (5 min to 30 days)
  - Maximum usage limits per link
  - Secure token-based access
  - REST endpoints: `POST /documents/{id}/expiring-link`, `GET /documents/{id}/expiring-link/{token}`
  - Admin-only link generation

- **Drupal Premium Feature Parity** - Complete WordPress/Drupal consistency
  - PdfSchemaEnhancer service for GEO/AEO/LLM optimization
  - PdfAccessManager service for role-based access control
  - PdfBulkOperations service for CSV import and bulk updates
  - PdfViewerEnhancer service for text search, bookmarks, reading progress UI
  - Extended REST API with 14+ endpoints matching WordPress

### Changed
- Updated all documentation for consistency between platforms
- Version bump to 1.2.5 across all modules

### Fixed
- **Drupal PDF.js Assets Missing** - PDF.js library files now included in Drupal module
  - Copied `pdf.min.js` and `pdf.worker.min.js` to `drupal-pdf-embed-seo/assets/pdfjs/`
  - Enables PDF rendering without external dependencies
- **Drupal workerSrc Configuration** - Fixed PDF.js worker not loading
  - Added `workerSrc` to `drupalSettings.pdfEmbedSeo` in PdfViewController
  - Added `workerSrc` to `drupalSettings.pdfEmbedSeo` in pdf_embed_seo.module
  - Ensures proper PDF.js worker initialization for PDF rendering
- **Cross-Platform License Validation** - Drupal now accepts WordPress-style license keys
  - Support for `PDF$PRO#`, `PDF$UNLIMITED#`, `PDF$DEV#` patterns
  - Backwards compatible with Drupal-style `PDF-` keys
- **Bulk Import Status API** - Unified route across platforms
  - WordPress-compatible `/bulk/import/status` endpoint added to Drupal
  - Legacy route preserved for backwards compatibility
- **Analytics Response Parity** - Added `date_range` field to Drupal analytics
- **Documentation Parity** - Updated Drupal help text with all 13 premium features

---

## [1.2.4] - 2025-01-28

### Added
- **Premium AI & Schema Optimization Meta Box** - Comprehensive GEO/AEO/LLM optimization for PDF documents
  - AI Summary (TL;DR) field for voice assistants → `abstract` schema
  - Key Points & Takeaways → `ItemList` schema for quick answers
  - FAQ Schema (FAQPage) for Google rich results
  - Table of Contents Schema (`hasPart`) with deep links
  - Reading Time estimate → `timeRequired` schema (e.g., PT10M)
  - Difficulty Level → `educationalLevel` schema
  - Document Type classification → `additionalType` schema
  - Target Audience → `audience` schema
  - Custom Speakable Content for voice search priority
  - Related Documents → `isRelatedTo` schema for content relationships
  - Prerequisites → `coursePrerequisites` schema
  - Learning Outcomes → `teaches` schema
- **AI Optimization Preview Meta Box (Free)** - Preview of premium AI features with Get Premium CTA
- **Premium Settings Preview (Free Settings Page)** - Shows disabled premium features for upgrade awareness

### Changed
- Updated documentation across all markdown files
- Enhanced premium feature visibility for free users

### Fixed
- Schema validation: Properly separated WebPage schema from DigitalDocument
- Premium settings now correctly display when license is valid

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
- **Premium: AI & Voice Search Optimization Meta Box** - Advanced schema features
  - AI Summary (TL;DR) for voice assistants → `abstract` schema
  - Key Points & Takeaways → `ItemList` schema
  - FAQ Schema (FAQPage) for Google rich results
  - Table of Contents Schema (`hasPart`) with deep links
  - Reading Time estimate → `timeRequired` schema
  - Difficulty Level → `educationalLevel` schema
  - Document Type classification → `additionalType` schema
  - Target Audience → `audience` schema
  - Custom Speakable Content for voice search
  - Related Documents → `isRelatedTo` schema
  - Prerequisites → `coursePrerequisites` schema
  - Learning Outcomes → `teaches` schema
- **AI Optimization Preview** - Preview meta box for free users with Get Premium CTA

### Fixed
- Plugin Check compliance: Fixed escaping issues in premium license notices
- Plugin Check compliance: Added direct file access protection to test files
- Schema validation: Moved speakable to separate WebPage schema (only valid on WebPage/Article)

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

# Changelog - WordPress (PDF Embed & SEO Optimize)

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize" width="80">
</p>

WordPress-specific changes for PDF Embed & SEO Optimize plugin.

For the complete unified changelog, see [CHANGELOG.md](./CHANGELOG.md).

---

## [1.2.11] - 2026-02-10

No WordPress-specific changes in this release. See [Drupal changelog](./CHANGELOG-DRUPAL.md) for platform-specific updates.

---

## [1.2.10] - 2026-02-05

### Added
- **iOS Print Support Improvements**
  - Enhanced existing print CSS with canvas optimization and page-break handling

### Changed
- Version bump to 1.2.10

---

## [1.2.9] - 2026-02-05

### Fixed
- **Archive page list view** - Icon alignment fix (changed from `inline-flex` to `flex`)
- **Boxed layout fix** - Added explicit width and box-sizing to content wrapper, grid, and list nav

### Changed
- Version bump to 1.2.9

---

## [1.2.8] - 2026-02-04

### Fixed
- **Plugin Check Compliance** - Added direct file access protection to all test files
  - Added `if ( ! defined( 'ABSPATH' ) ) exit;` check to 10 PHP test files
  - Fixes `missing_direct_file_access_protection` errors in Plugin Check

### Changed
- **Premium Sitemap URL** - Changed from `/pdf-sitemap.xml` to `/pdf/sitemap.xml`
  - Added 301 redirect from legacy `/pdf-sitemap.xml` for backwards compatibility
  - Added automatic redirect to Yoast SEO's `pdf_document-sitemap.xml` when Yoast is active
- **Archive Settings** - Renamed "Heading Alignment" to "Content Alignment"
  - Content alignment now applies to entire archive page (header, list, and grid)
  - Font color and background color settings now apply to content items
- **Grid/List View Styling**
  - Font color setting now applies to grid card titles, excerpts, and metadata
  - Item background color setting now applies to individual grid cards
  - CSS inheritance for custom colors on child elements
- Version bump to 1.2.8

---

## [1.2.7] - 2026-02-02

### Fixed
- **Sidebar/Widget Area Removal** - PDF pages now display full-width
  - Removed `get_sidebar()` calls from archive and single templates
  - Added CSS rules to hide sidebars on archive pages (`.post-type-archive-pdf_document`)
- **"Security check failed" error on cached pages**
  - Switched PDF viewer from AJAX to REST API endpoint (`/documents/{id}/data`)
  - REST API doesn't require nonces for public read operations

### Added
- **Archive Page Styling Settings**
  - Custom H1 heading for archive page (default: "PDF Documents")
  - Heading alignment options: left, center (default), right
  - Custom font color for archive header
  - Custom background color for archive header
  - Custom heading also updates 2nd breadcrumb item (HTML and Schema.org)
- **Unit Tests** - `test-template-sidebar.php` for sidebar removal

### Changed
- Version bump to 1.2.7

---

## [1.2.6] - 2026-02-01

### Fixed
- **Plugin Check Compliance**
  - Fixed unescaped SQL table name parameters in premium REST API
  - Fixed interpolated SQL variables in premium analytics
  - Updated `get_posts()` to use `post__not_in` instead of deprecated `exclude` parameter
  - Added proper `esc_sql()` sanitization for all database table names

### Changed
- **Hook Renamed** - `pdf_embed_seo_settings_saved` → `pdf_embed_seo_optimize_settings_saved`
  - **Breaking change**: Update any custom code using the old hook name
- Version bump to 1.2.6

---

## [1.2.5] - 2026-01-28

### Added
- **Download Tracking** - Track PDF downloads separately from views
  - Separate download counter per document (`_pdf_download_count`)
  - Download analytics in dashboard
  - REST API endpoint: `POST /documents/{id}/download`

- **Expiring Access Links** - Generate time-limited URLs for PDFs
  - Configurable expiration time (5 min to 30 days)
  - Maximum usage limits per link
  - Secure token-based access
  - REST endpoints: `POST /documents/{id}/expiring-link`, `GET /documents/{id}/expiring-link/{token}`

### Changed
- Version bump to 1.2.5

---

## [1.2.4] - 2025-01-28

### Added
- **Premium AI & Schema Optimization Meta Box**
  - AI Summary (TL;DR) field → `abstract` schema
  - Key Points & Takeaways → `ItemList` schema
  - FAQ Schema (FAQPage) for Google rich results
  - Table of Contents Schema (`hasPart`) with deep links
  - Reading Time estimate → `timeRequired` schema
  - Difficulty Level → `educationalLevel` schema
  - Document Type → `additionalType` schema
  - Target Audience → `audience` schema
  - Custom Speakable Content for voice search
  - Related Documents → `isRelatedTo` schema
  - Prerequisites → `coursePrerequisites` schema
  - Learning Outcomes → `teaches` schema
- **AI Optimization Preview Meta Box (Free)** - Preview with Get Premium CTA
- **Premium Settings Preview** - Disabled premium features on free settings page

### Changed
- Version bump to 1.2.4

---

## [1.2.3] - 2025-01-28

### Added
- **GEO/AEO/LLM Schema Optimization**
  - SpeakableSpecification with CSS selectors
  - accessMode, accessModeSufficient, accessibilityFeature properties
  - potentialAction (ReadAction, DownloadAction, SearchAction, ViewAction)
  - learningResourceType and genre properties
- **Standalone Social Meta Tags** - Open Graph and Twitter Cards (without Yoast)
- **Enhanced DigitalDocument Schema** - identifier, fileFormat, inLanguage, publisher

### Fixed
- Plugin Check compliance: Fixed escaping issues in premium license notices
- Plugin Check compliance: Added direct file access protection to test files

### Changed
- Version bump to 1.2.3

---

## [1.2.2] - 2025-01-28

### Added
- **Archive Display Options** - List/grid views for PDF archives
- **Breadcrumb Schema** - Schema.org BreadcrumbList markup with visible navigation
- **Archive Page Redirect** (Premium) - Redirect /pdf archive to custom URL

### Changed
- Version bump to 1.2.2

---

## [1.2.1] - 2025-01-27

### Added
- Unit tests for WordPress Free module (REST API, Schema, Post Type, Shortcodes)
- Unit tests for WordPress Premium module (Password, Analytics, Progress, REST API)
- "Get Premium" action link on free plugin page
- Plugin name differentiation: "(Free Version)" and "(Premium)"

### Changed
- Plugin URI now points to https://pdfviewer.drossmedia.de
- Author URI updated to https://dross.net/media/
- Version bump to 1.2.1

---

## [1.2.0] - 2025-01-25

### Added
- **REST API** - Complete API for external integrations
  - `GET /documents` - List all published PDFs
  - `GET /documents/{id}` - Get single PDF details
  - `GET /documents/{id}/data` - Get secure PDF URL
  - `POST /documents/{id}/view` - Track PDF view
  - `GET /settings` - Get public plugin settings

- **Premium Features**
  - Analytics Dashboard with export (CSV/JSON)
  - Password Protection with bcrypt hashing
  - Reading Progress tracking
  - XML Sitemap at `/pdf/sitemap.xml`
  - Premium REST API endpoints

### Changed
- Updated docs page with API documentation
- Added premium feature preview sections
- Version bump to 1.2.0

---

## [1.1.5] - 2025-01-20

### Changed
- Version sync across all modules
- Bug fixes and stability improvements

---

## [1.1.0] - 2025-01-15

### Added
- UAT/QA test documentation

### Changed
- Improved plugin structure
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

## Requirements

| Version | WordPress | PHP | Status |
|---------|-----------|-----|--------|
| 1.2.x | 5.8+ | 7.4+ | Current |
| 1.1.x | 5.8+ | 7.4+ | Supported |
| 1.0.x | 5.8+ | 7.4+ | Legacy |

---

*Made with love by [Dross:Media](https://dross.net/media/)*

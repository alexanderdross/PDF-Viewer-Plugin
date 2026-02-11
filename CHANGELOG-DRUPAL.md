# Changelog - Drupal (PDF Embed & SEO)

<p align="center">
  <strong>pdf_embed_seo</strong> + <strong>pdf_embed_seo_premium</strong>
</p>

Drupal-specific changes for PDF Embed & SEO module.

For the complete unified changelog, see [CHANGELOG.md](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/CHANGELOG.md).

---

## [1.2.11] - 2026-02-10

### Added
- **Media Library Integration** - PDFs can now be managed via Drupal's Media Library
  - Added Drupal Media module as a dependency
  - Created `PdfDocument` MediaSource plugin for PDF files
  - Created `PdfViewerFormatter` field formatter for displaying PDFs in Media entities
  - PDFs can be managed alongside images, videos, and other media types

- **New Premium Services**
  - `pdf_embed_seo.rate_limiter` - Rate limiting service for brute force protection
  - `pdf_embed_seo.access_token_storage` - Token storage with database backend and automatic cleanup

- **New Database Tables (Premium)**
  - `pdf_embed_seo_access_tokens` - Expiring access links storage (replaces State API)
  - `pdf_embed_seo_rate_limit` - Brute force tracking with automatic cleanup

### Fixed (Code Review - Complete Resolution)
- **Security: CSRF Protection** - Added `_csrf_token: 'TRUE'` to all POST API endpoints
  - Affected endpoints: track_view, track_download, progress, verify_password
  - All POST requests now require valid CSRF token

- **Security: Rate Limiting** - Added brute force protection for password verification
  - 5 attempts per 5 minutes allowed
  - 15 minute block after exceeding limit
  - Automatic cleanup via cron

- **Security: Session Cache Context** - Added session cache context to password-protected PDF routes
  - Prevents cross-session cache leaks
  - Ensures password verification is per-user

- **Performance: Computed View Count** - Converted `view_count` entity field to computed field
  - Reads directly from analytics table
  - No more entity saves during page views
  - Eliminates cache invalidation on every view

- **Scalability: Token Storage Migration** - Replaced State API token storage
  - Dedicated database table `pdf_embed_seo_access_tokens`
  - Automatic cleanup of expired tokens
  - Better performance for high-traffic sites

- **Scalability: Rate Limit Table** - Proper brute force tracking
  - Dedicated `pdf_embed_seo_rate_limit` table
  - Cron cleanup of old records

### Changed
- **Database Updates**
  - Update hook `pdf_embed_seo_premium_update_9001()` creates new tables
  - Migrates existing State API tokens to new table
  - Cron hook cleans up expired tokens and old rate limit records

- **Architecture Improvements**
  - Backwards-compatible: Falls back to State API if new tables don't exist
  - Graceful service checks using `\Drupal::hasService()`

- Version bump to 1.2.11

---

## [1.2.10] - 2026-02-05

### Added
- **iOS Print Support** - Changed print implementation to open PDF in new window
  - Matches WordPress approach for consistency
  - Added 500ms delay for Safari/iOS compatibility
  - Added fallback to canvas print if popup is blocked

- **Comprehensive Print CSS** (previously missing)
  - Added `@page` rules for proper A4 portrait sizing and margins
  - Added `-webkit-print-color-adjust` and `print-color-adjust` for proper color printing
  - Added `page-break-inside: avoid` and `break-inside: avoid` for canvas elements
  - Hide all toolbar, control, loading, and error elements in print output
  - Remove decorative styles for clean print output

### Changed
- Version bump to 1.2.10

---

## [1.2.9] - 2026-02-05

### Fixed (Critical Code Review Items)
- **Performance: Removed entity saves during page views**
  - View tracking no longer saves the entity
  - Prevents cache invalidation on every page view
  - Views tracked directly in analytics table

- **Performance: Added cache tag invalidation for lists**
  - Implemented `hook_ENTITY_TYPE_insert/update/delete`
  - Properly invalidates `pdf_document_list` cache tag

- **Performance: Added cache metadata to PdfViewerBlock**
  - Block now includes proper `#cache` configuration
  - Includes tags, contexts, and max-age

- **Security: Fixed Pathauto service dependency**
  - Gracefully handles missing Pathauto service
  - Fallback URL-safe string generator prevents fatal errors

- **Privacy: Added IP anonymization for GDPR compliance**
  - New setting to anonymize IP addresses
  - Zeros last octet (IPv4) or last 80 bits (IPv6)
  - Enabled by default for GDPR compliance

### Added
- **GDPR IP Anonymization Setting** - New checkbox in settings form
- **Cache Tag Invalidation Hooks** - `pdf_embed_seo_pdf_document_insert()`, `_update()`, `_delete()`
- **Archive list view fix** - Icon alignment (changed from `inline-flex` to `flex`)
- **Boxed layout fix** - Explicit width and box-sizing to content wrapper

### Changed
- Version bump to 1.2.9

---

## [1.2.8] - 2026-02-04

### Added
- **Archive Settings**
  - Content Alignment setting (left, center, right)
  - Font Color setting for content items
  - Background Color setting for content items
  - Settings apply to entire archive page (header, list, grid)

### Changed
- **Grid/List View Styling**
  - Font color applies to grid card titles, excerpts, and metadata
  - Item background color applies to individual grid cards
  - CSS inheritance for custom colors on child elements
- Version bump to 1.2.8

---

## [1.2.7] - 2026-02-02

### Fixed
- **Sidebar/Widget Area Removal** - PDF pages now display full-width
  - Added `hook_theme_suggestions_page_alter()` for full-width page templates
  - Added `hook_preprocess_page()` to programmatically clear sidebar regions
  - Added `hook_preprocess_html()` to add `.page-pdf` body classes
  - Added CSS rules to hide common Drupal sidebar selectors
  - Themes can provide custom templates via `page--pdf.html.twig`

### Added
- **Unit Tests** - `PdfSidebarRemovalTest.php` for sidebar removal functionality

### Changed
- Version bump to 1.2.7

---

## [1.2.6] - 2026-02-01

### Fixed
- **Security: Password Hashing** - Implemented proper password hashing
  - Passwords now hashed on save in `PdfDocumentForm`
  - Password verification uses `\Drupal::service('password')->check()`

- **Security: XSS Prevention** - Fixed potential XSS in `PdfViewerBlock`
  - Document titles now properly escaped with `Html::escape()`

### Changed
- Version bump to 1.2.6

---

## [1.2.5] - 2026-01-28

### Added
- **Download Tracking** - Track PDF downloads separately from views
  - REST API endpoint: `POST /documents/{id}/download`

- **Expiring Access Links** - Generate time-limited URLs for PDFs
  - Configurable expiration time (5 min to 30 days)
  - Maximum usage limits per link
  - REST endpoints: `POST /documents/{id}/expiring-link`, `GET /documents/{id}/expiring-link/{token}`

- **Premium Feature Parity with WordPress**
  - `PdfSchemaEnhancer` service for GEO/AEO/LLM optimization
  - `PdfAccessManager` service for role-based access control
  - `PdfBulkOperations` service for CSV import and bulk updates
  - `PdfViewerEnhancer` service for text search, bookmarks, reading progress UI
  - Extended REST API with 14+ endpoints matching WordPress

### Fixed
- **PDF.js Assets Missing** - PDF.js library files now included
  - Copied `pdf.min.js` and `pdf.worker.min.js` to `assets/pdfjs/`
  - Enables PDF rendering without external dependencies

- **workerSrc Configuration** - Fixed PDF.js worker not loading
  - Added `workerSrc` to `drupalSettings.pdfEmbedSeo` in PdfViewController
  - Added `workerSrc` to `drupalSettings.pdfEmbedSeo` in module file

- **Cross-Platform License Validation** - Accepts WordPress-style license keys
  - Support for `PDF$PRO#`, `PDF$UNLIMITED#`, `PDF$DEV#` patterns
  - Backwards compatible with Drupal-style `PDF-` keys

- **Bulk Import Status API** - WordPress-compatible `/bulk/import/status` endpoint
- **Analytics Response Parity** - Added `date_range` field to analytics
- **Documentation Parity** - Updated help text with all 13 premium features

### Changed
- Version bump to 1.2.5

---

## [1.2.4] - 2025-01-28

### Added
- Premium GEO/AEO/LLM schema optimization fields (matching WordPress)

### Changed
- Version bump to 1.2.4

---

## [1.2.3] - 2025-01-28

### Added
- **GEO/AEO/LLM Schema Optimization**
  - SpeakableSpecification with CSS selectors
  - accessMode, accessModeSufficient, accessibilityFeature properties
  - potentialAction (ReadAction, DownloadAction)
- **Standalone Social Meta Tags** - Open Graph and Twitter Cards

### Changed
- Version bump to 1.2.3

---

## [1.2.2] - 2025-01-28

### Added
- **Archive Display Options** - List/grid views
- **Breadcrumb Schema** - Schema.org BreadcrumbList markup

### Changed
- Version bump to 1.2.2

---

## [1.2.1] - 2025-01-27

### Added
- Unit tests for Drupal Free module (API Controller, Entity, Storage)

### Changed
- Version bump to 1.2.1

---

## [1.2.0] - 2025-01-25

### Added
- **REST API** - Complete API for external integrations
  - `GET /documents` - List all published PDFs
  - `GET /documents/{id}` - Get single PDF details
  - `GET /documents/{id}/data` - Get secure PDF URL
  - `POST /documents/{id}/view` - Track PDF view
  - `GET /settings` - Get public module settings

- **Premium Submodule** (`pdf_embed_seo_premium`)
  - Separated into free base module + premium submodule
  - Located at `pdf_embed_seo/modules/pdf_embed_seo_premium/`
  - Analytics Dashboard
  - Password Protection
  - Reading Progress tracking
  - XML Sitemap at `/pdf/sitemap.xml`
  - Premium REST API endpoints

### Changed
- Version bump to 1.2.0

---

## [1.1.5] - 2025-01-20

### Changed
- Version sync across all modules
- Bug fixes and stability improvements

---

## [1.1.0] - 2025-01-15

### Added
- Initial Drupal 10/11 module release
- UAT/QA test documentation
- Comprehensive test plans

---

## Requirements

| Version | Drupal | PHP | Status |
|---------|--------|-----|--------|
| 1.2.x | 10/11 | 8.1+ | Current |
| 1.1.x | 10/11 | 8.1+ | Supported |

### Dependencies
- Core modules: node, file, taxonomy, path, path_alias
- Optional: Media (for Media Library integration)
- Optional: ImageMagick or Ghostscript (thumbnails)
- Optional: Pathauto (automatic URL aliases)

---

*Made with love by [Dross:Media](https://dross.net/media/)*

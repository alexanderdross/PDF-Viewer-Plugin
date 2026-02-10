# Changelog - PDF Embed & SEO Optimize (Drupal)

All notable changes to the Drupal module will be documented in this file.

## [1.2.11] - 2026-02-10

### Security
- **CSRF Protection**: Added CSRF token validation to all POST API endpoints:
  - `/api/pdf-embed-seo/v1/documents/{id}/view` (track view)
  - `/api/pdf-embed-seo/v1/documents/{id}/download` (track download)
  - `/api/pdf-embed-seo/v1/documents/{id}/progress` (save progress)
  - `/api/pdf-embed-seo/v1/documents/{id}/verify-password` (password verification)
- **Brute Force Protection**: Added rate limiting for password verification
  - Maximum 5 attempts per document per IP within 5 minutes
  - 15 minute block after exceeding limit
  - Returns HTTP 429 with retry-after information
- **Session Cache Context**: Password-protected PDFs now properly vary cache by session
  - Prevents unlocked PDF content from being served to unauthenticated sessions

### Performance
- **Computed View Count**: The `view_count` entity field is now a computed field
  - Reads count directly from analytics table on demand
  - No longer triggers entity saves during page views
  - Eliminates cache invalidation issues from view tracking

### New Features
- **Media Library Integration**: Full integration with Drupal's Media system
  - New `PdfDocument` MediaSource plugin for the Media Library
  - New `PdfViewerFormatter` field formatter for embedding PDFs
  - PDFs can now be managed alongside other media types
  - Added `drupal:media` as a module dependency
- **Access Token Storage**: New database-backed token storage
  - Replaces State API for better scalability
  - Automatic cleanup of expired tokens via cron
  - New `pdf_embed_seo_access_tokens` database table
- **Rate Limiting Service**: New dedicated service for brute force protection
  - New `pdf_embed_seo_rate_limit` database table
  - Configurable limits per action type
  - Automatic cleanup of old records via cron

### Premium Module Changes
- New services registered:
  - `pdf_embed_seo.rate_limiter` - Rate limiting service
  - `pdf_embed_seo.access_token_storage` - Token storage service
- Update hook `pdf_embed_seo_premium_update_9001()`:
  - Creates new database tables
  - Migrates existing State API tokens to database
- Cron hook additions:
  - Cleans up expired access tokens
  - Cleans up old rate limit records (>24 hours)
  - Cleans up analytics data based on retention setting

### Technical Details
- All changes are backwards-compatible
- Services check for table existence before operations
- Graceful fallback to State API if new tables unavailable
- No breaking changes to existing API contracts

### Files Changed
**Free Module:**
- `pdf_embed_seo.info.yml` - Added media dependency, version bump
- `pdf_embed_seo.routing.yml` - Added CSRF tokens
- `src/Entity/PdfDocument.php` - Computed view_count field
- `src/Field/ComputedViewCount.php` - New computed field class
- `src/Controller/PdfViewController.php` - Session cache context
- `src/Plugin/media/Source/PdfDocument.php` - New Media Source
- `src/Plugin/Field/FieldFormatter/PdfViewerFormatter.php` - New formatter

**Premium Module:**
- `pdf_embed_seo_premium.info.yml` - Version bump
- `pdf_embed_seo_premium.install` - New tables and update hooks
- `pdf_embed_seo_premium.module` - Cron cleanup
- `pdf_embed_seo_premium.routing.yml` - CSRF tokens
- `pdf_embed_seo_premium.services.yml` - New services
- `src/Service/RateLimiter.php` - New service
- `src/Service/AccessTokenStorage.php` - New service
- `src/Controller/PdfPremiumApiController.php` - Rate limiting integration

---

## [1.2.10] - 2026-02-08

### iOS Print Support
- Changed print implementation to open PDF in new window for native browser printing
- Added 500ms delay for Safari/iOS compatibility
- Added fallback to canvas print if popup is blocked

### Print CSS
- Added `@page` rules for proper A4 portrait sizing
- Added `-webkit-print-color-adjust` for proper color printing
- Hide toolbar elements in print output

---

## [1.2.9] - 2026-01-30

### Code Review Fixes (Phase 1)
- **Performance**: Removed entity saves during page views
- **Performance**: Added cache tag invalidation for lists
- **Performance**: Added cache metadata to PdfViewerBlock
- **Security**: Fixed Pathauto service dependency with graceful fallback
- **Privacy**: Added IP anonymization setting for GDPR compliance

---

## Upgrade Notes

### From 1.2.10 to 1.2.11

1. **Database Update Required**: Run `drush updb` or visit `/admin/reports/updates` to execute update hook 9001
2. **Media Module**: The Media module is now required - ensure it's enabled before updating
3. **Cron**: Ensure cron is running for automatic cleanup of expired tokens and rate limits
4. **Cache Clear**: Clear all caches after update: `drush cr`

### Breaking Changes
None. All changes are backwards-compatible.

### Deprecations
- State API token storage is deprecated in favor of database storage
- The `view_count` field no longer stores data (reads from analytics table)

# Changelog - React/Next.js (PDF Embed & SEO)

<p align="center">
  <code>@pdf-embed-seo/core</code> | <code>@pdf-embed-seo/react</code> | <code>@pdf-embed-seo/react-premium</code>
</p>

React and Next.js-specific changes for PDF Embed & SEO packages.

For the complete unified changelog, see [CHANGELOG.md](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/CHANGELOG.md).

---

## [1.2.11] - 2026-02-10

No React-specific changes in this release. See [Drupal changelog](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/CHANGELOG-DRUPAL.md) for platform-specific updates.

---

## [1.2.10] - 2026-02-05

### Added
- **iOS Print Support** - Changed print implementation to open PDF in new window
  - Native browser printing for better Safari/iOS compatibility
  - Added 500ms delay for Safari/iOS before triggering print dialog
  - Added fallback to canvas print if popup is blocked

- **Comprehensive Print CSS** (previously missing)
  - Added `@page` rules for proper A4 portrait sizing and margins
  - Added `-webkit-print-color-adjust` and `print-color-adjust` for proper color printing
  - Added `page-break-inside: avoid` and `break-inside: avoid` for canvas elements
  - Hide all toolbar, control, loading, and error elements in print output
  - Remove decorative styles (borders, shadows, backgrounds) for clean print output

### Changed
- Version bump to 1.2.10

---

## [1.2.9] - 2026-02-05

### Fixed
- **Archive page list view** - Icon alignment fix (changed from `inline-flex` to `flex`)
- **Boxed layout fix** - Added explicit width and box-sizing to content wrapper, grid, and list nav

### Changed
- Version bump to 1.2.9 (synced with WordPress/Drupal)

---

## [1.2.8] - 2026-02-04

### Added
- **Grid/List View Styling**
  - Custom font color support for grid cards and list items
  - Custom background color for individual grid cards
  - CSS inheritance for custom colors on child elements

### Changed
- Version bump to 1.2.8 (synced with WordPress/Drupal)

---

## [1.2.7] - 2026-02-02

### Changed
- Version bump to 1.2.7 (synced with WordPress/Drupal)
- No React-specific changes

---

## [1.2.6] - 2026-02-01

### Changed
- Version bump to 1.2.6 (synced with WordPress/Drupal)
- No React-specific changes

---

## [1.2.5] - 2026-01-28

### Added
- **Download Tracking Hook** - `useDownloadTracking` for tracking downloads via API
- **Expiring Links Support** - API client methods for generating/validating expiring links

### Changed
- Version bump to 1.2.5 (synced with WordPress/Drupal)

---

## [1.2.4] - 2025-01-28

### Changed
- Version bump to 1.2.4 (synced with WordPress/Drupal)
- No React-specific changes

---

## [1.2.3] - 2025-01-28

### Added
- **PdfSeo Component Enhancements**
  - GEO/AEO/LLM schema optimization
  - SpeakableSpecification support
  - accessMode and accessibilityFeature properties
  - potentialAction (ReadAction, DownloadAction)

### Changed
- Version bump to 1.2.3 (synced with WordPress/Drupal)

---

## [1.2.2] - 2025-01-28

### Added
- **PdfArchive Component Enhancements**
  - Grid/list display mode toggle
  - Sorting options
  - Search filtering
- **Breadcrumb Schema** - Added to PdfSeo component

### Changed
- Version bump to 1.2.2 (synced with WordPress/Drupal)

---

## [1.2.1] - 2025-01-27

### Changed
- Version bump to 1.2.1 (synced with WordPress/Drupal)

---

## [1.2.0] - 2025-01-25

### Added
- **Initial React/Next.js Package Release**

- **@pdf-embed-seo/core** (MIT)
  - TypeScript types for all PDF document interfaces
  - API client for WordPress/Drupal REST endpoints
  - PDF.js integration utilities
  - Schema.org generators

- **@pdf-embed-seo/react** (MIT)
  - `PdfViewer` - PDF viewer component with PDF.js
  - `PdfArchive` - Archive listing component
  - `PdfSeo` - SEO schema component
  - `PdfProvider` - Context provider for configuration
  - `usePdf` - Single document hook
  - `usePdfList` - Document list hook with pagination
  - `usePdfViewer` - Viewer state management hook
  - `useProgress` - Reading progress hook
  - Next.js optimized exports at `@pdf-embed-seo/react/nextjs`

- **@pdf-embed-seo/react-premium** (Commercial)
  - `PdfAnalytics` - Analytics dashboard component
  - `PdfPasswordModal` - Password protection modal
  - `PdfProgressBar` - Reading progress bar
  - `PdfSearch` - In-document text search
  - `PdfBookmarks` - Bookmark navigation
  - `useAnalytics` - Analytics tracking hook
  - `usePassword` - Password verification hook

### Changed
- Version 1.2.0 (synced with WordPress/Drupal)

---

## Requirements

| Version | React | Next.js | Node.js | Status |
|---------|-------|---------|---------|--------|
| 1.2.x | 18+ or 19+ | 13, 14, or 15 | 18+ | Current |

### Dependencies
- `pdfjs-dist` - Mozilla PDF.js (bundled)
- TypeScript 5+ (recommended)

### Backend Requirements
- WordPress with PDF Embed SEO plugin, OR
- Drupal with pdf_embed_seo module

---

## Installation

```bash
# Free packages
npm install @pdf-embed-seo/react
# or
pnpm add @pdf-embed-seo/react

# Premium packages (requires license)
npm install @pdf-embed-seo/react-premium
```

---

*Made with love by [Dross:Media](https://dross.net/media/)*

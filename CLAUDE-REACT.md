# PDF Embed & SEO Optimize - Multi-Platform Documentation

A comprehensive PDF management solution available for WordPress, Drupal, and React/Next.js that uses Mozilla's PDF.js library to securely display PDFs with SEO optimization.

**Current Version:** 1.2.8 (WP/Drupal), 1.3.0 (React - Planned)
**Platforms:** WordPress (Free & Premium), Drupal 10/11, React/Next.js
**License:** GPL v2 or later (WP/Drupal), MIT (React Free), Commercial (React Pro)

---

## Project Overview

This project provides six modules across three platforms:

| Module | Directory/Package | Platform | Features |
|--------|-------------------|----------|----------|
| WP Free | `pdf-embed-seo-optimize/` | WordPress 5.8+ | Core PDF viewer, SEO, REST API |
| WP Premium | `pdf-embed-seo-optimize/premium/` | WordPress 5.8+ | Analytics, passwords, progress, sitemap |
| Drupal Free | `drupal-pdf-embed-seo/` | Drupal 10/11 | Core PDF viewer, SEO, REST API |
| Drupal Premium | `drupal-pdf-embed-seo/modules/pdf_embed_seo_premium/` | Drupal 10/11 | Analytics, passwords, progress, sitemap |
| React Free | `@pdf-embed-seo/react` | React 18+, Next.js 13+ | Components, hooks, SEO, API client |
| React Pro | `@pdf-embed-seo/react-premium` | React 18+, Next.js 13+ | Analytics, passwords, progress, sitemap |

---

## Platform Selection Guide

| Use Case | Recommended Platform |
|----------|---------------------|
| Content-heavy site with non-technical editors | WordPress |
| Enterprise/government with existing Drupal | Drupal |
| Custom web application | React/Next.js |
| Headless CMS architecture | React/Next.js + WP/Drupal backend |
| Static site with PDFs | React/Next.js (standalone mode) |
| E-commerce with PDF catalogs | WordPress or React/Next.js |

---

## User Guide (WordPress)

### Creating a PDF Document

When you create a new PDF Document (**PDF Documents → Add New**), use the **PDF File** meta box to upload or select your PDF file.

**Important:** The PDF is automatically displayed on its dedicated page (e.g., `/pdf/your-document-title/`). You do NOT need to add any shortcode in the content area.

| Element | Purpose |
|---------|---------|
| **Title** | The document title (appears in URL, breadcrumbs, and SEO) |
| **Content Editor** | Optional description text shown below the PDF viewer |
| **PDF File Meta Box** | Upload/select the PDF file to display |
| **PDF Settings** | Control download/print permissions |
| **PDF Cover Image** | Featured image for archive listings and social sharing |
| **Excerpt** | Short description for archive listings |

### Embedding PDFs on Other Pages (Shortcodes)

Use shortcodes to embed an **existing PDF Document** into any page, post, or widget area.

#### `[pdf_viewer]` - Embed a PDF Viewer

```
[pdf_viewer id="123"]
[pdf_viewer id="123" width="100%" height="600px"]
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `id` | (required) | The PDF Document post ID |
| `width` | `100%` | Viewer width (CSS value) |
| `height` | `800px` | Viewer height (CSS value) |

#### `[pdf_viewer_sitemap]` - List All PDF Documents

```
[pdf_viewer_sitemap]
[pdf_viewer_sitemap orderby="date" order="DESC" limit="10"]
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `orderby` | `title` | Sort by: `title`, `date`, `modified`, `menu_order` |
| `order` | `ASC` | Sort direction: `ASC` or `DESC` |
| `limit` | `-1` | Number of documents (-1 for all) |

### Common Mistake to Avoid

**Do NOT add `[pdf_viewer]` shortcode inside a PDF Document's content area.** The PDF is already displayed automatically via the PDF File meta box.

---

## User Guide (React/Next.js)

### Installation

```bash
# Free version
npm install @pdf-embed-seo/react

# Pro version (requires license)
npm install @pdf-embed-seo/react @pdf-embed-seo/react-premium
```

### Quick Start

```tsx
import { PdfViewer, PdfProvider } from '@pdf-embed-seo/react';

function App() {
  return (
    <PdfProvider>
      <PdfViewer src="/documents/example.pdf" height="600px" />
    </PdfProvider>
  );
}
```

### Next.js App Router Example

```tsx
// app/pdf/[slug]/page.tsx
import { PdfViewer, PdfJsonLd, PdfBreadcrumbs } from '@pdf-embed-seo/react';
import { generatePdfMetadata, getPdfDocument } from '@pdf-embed-seo/react/nextjs';

// Generate SEO metadata
export async function generateMetadata({ params }) {
  const document = await getPdfDocument(params.slug);
  return generatePdfMetadata(document, {
    siteUrl: process.env.NEXT_PUBLIC_SITE_URL,
  });
}

// Generate static paths for SSG
export async function generateStaticParams() {
  const documents = await getAllPdfDocuments();
  return documents.map((doc) => ({ slug: doc.slug }));
}

export default async function PdfPage({ params }) {
  const document = await getPdfDocument(params.slug);

  return (
    <main>
      <PdfJsonLd document={document} includeBreadcrumbs />
      <PdfBreadcrumbs document={document} />
      <h1>{document.title}</h1>
      <PdfViewer src={document} height="800px" />
      {document.excerpt && <p>{document.excerpt}</p>}
    </main>
  );
}
```

### Archive Page Example

```tsx
// app/pdf/page.tsx
import { PdfArchive, PdfJsonLd } from '@pdf-embed-seo/react';

export const metadata = {
  title: 'PDF Documents',
  description: 'Browse our collection of PDF documents',
};

export default function PdfArchivePage() {
  return (
    <main>
      <PdfJsonLd type="CollectionPage" />
      <h1>PDF Documents</h1>
      <PdfArchive
        view="grid"
        columns={3}
        perPage={12}
        showSearch
        showSort
      />
    </main>
  );
}
```

### Backend Integration Modes

#### Standalone Mode (No CMS)

```tsx
<PdfProvider mode="standalone">
  <PdfViewer src="/pdfs/document.pdf" />
</PdfProvider>
```

#### WordPress Backend (Headless)

```tsx
<PdfProvider
  mode="wordpress"
  config={{
    apiUrl: 'https://your-wp-site.com/wp-json/pdf-embed-seo/v1',
  }}
>
  <PdfArchive />
</PdfProvider>
```

#### Drupal Backend (Headless)

```tsx
<PdfProvider
  mode="drupal"
  config={{
    apiUrl: 'https://your-drupal-site.com/api/pdf-embed-seo/v1',
  }}
>
  <PdfArchive />
</PdfProvider>
```

### React Hooks

```tsx
import {
  usePdfDocument,
  usePdfDocuments,
  usePdfViewer,
  usePdfSeo
} from '@pdf-embed-seo/react';

// Fetch single document
const { document, isLoading, error } = usePdfDocument(id);

// Fetch document list with pagination
const { documents, pagination, setPage, setSearch } = usePdfDocuments({
  perPage: 10,
});

// Viewer state management
const { currentPage, zoom, setPage, setZoom, toggleFullscreen } = usePdfViewer(ref);

// Generate SEO data
const { jsonLd, metaTags } = usePdfSeo(document);
```

---

## Architecture Overview

### WordPress Plugin Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    PDF_Embed_SEO (Main)                      │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐ │
│  │  Post Type  │  │  Frontend   │  │      REST API       │ │
│  │  Handler    │  │  Renderer   │  │  (Free Endpoints)   │ │
│  └─────────────┘  └─────────────┘  └─────────────────────┘ │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐ │
│  │    Admin    │  │    Yoast    │  │     Shortcodes      │ │
│  │   Handler   │  │ Integration │  │     & Blocks        │ │
│  └─────────────┘  └─────────────┘  └─────────────────────┘ │
├─────────────────────────────────────────────────────────────┤
│                 PDF_Embed_SEO_Premium (Optional)             │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌─────────────┐ │
│  │ Analytics │ │ Password  │ │  Reading  │ │   Premium   │ │
│  │ Dashboard │ │ Protection│ │  Progress │ │  REST API   │ │
│  └───────────┘ └───────────┘ └───────────┘ └─────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Drupal Module Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                  pdf_embed_seo Module (Free)                 │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐  ┌─────────────────┐                   │
│  │  PdfDocument    │  │  Controllers    │                   │
│  │  Entity         │  │  (View/Archive) │                   │
│  └─────────────────┘  └─────────────────┘                   │
│  ┌─────────────────┐  ┌─────────────────┐                   │
│  │  REST Resources │  │    Services     │                   │
│  │  (Basic API)    │  │  (Thumbnails)   │                   │
│  └─────────────────┘  └─────────────────┘                   │
├─────────────────────────────────────────────────────────────┤
│              pdf_embed_seo_premium (Optional)                │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌─────────────┐ │
│  │ Analytics │ │ Password  │ │  Reading  │ │   Premium   │ │
│  │ Dashboard │ │ Protection│ │  Progress │ │  REST API   │ │
│  └───────────┘ └───────────┘ └───────────┘ └─────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### React/Next.js Module Architecture

```
┌─────────────────────────────────────────────────────────────┐
│              @pdf-embed-seo/react (Free)                     │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐  ┌─────────────────┐                   │
│  │   PdfViewer     │  │   PdfArchive    │                   │
│  │   Component     │  │   Component     │                   │
│  └─────────────────┘  └─────────────────┘                   │
│  ┌─────────────────┐  ┌─────────────────┐                   │
│  │   PdfSeo        │  │   PdfProvider   │                   │
│  │   Components    │  │   (Context)     │                   │
│  └─────────────────┘  └─────────────────┘                   │
│  ┌─────────────────┐  ┌─────────────────┐                   │
│  │   React Hooks   │  │   API Client    │                   │
│  │   (usePdf...)   │  │   (WP/Drupal)   │                   │
│  └─────────────────┘  └─────────────────┘                   │
├─────────────────────────────────────────────────────────────┤
│           @pdf-embed-seo/react-premium (React Pro)           │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌─────────────┐ │
│  │ Analytics │ │ Password  │ │  Reading  │ │  Enhanced   │ │
│  │   Hooks   │ │   Modal   │ │  Progress │ │   Viewer    │ │
│  └───────────┘ └───────────┘ └───────────┘ └─────────────┘ │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌─────────────┐ │
│  │  Search   │ │ Bookmarks │ │  Sitemap  │ │    Admin    │ │
│  │  Feature  │ │ Component │ │ Generator │ │  Dashboard  │ │
│  └───────────┘ └───────────┘ └───────────┘ └─────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

---

## File Structure

### WordPress Plugin (Free)

```
pdf-embed-seo-optimize/
├── pdf-embed-seo-optimize.php           # Main plugin file
├── uninstall.php                        # Cleanup on uninstall
├── README.txt                           # WordPress.org readme
├── includes/
│   ├── class-pdf-embed-seo-optimize-post-type.php
│   ├── class-pdf-embed-seo-optimize-admin.php
│   ├── class-pdf-embed-seo-optimize-frontend.php
│   ├── class-pdf-embed-seo-optimize-yoast.php
│   ├── class-pdf-embed-seo-optimize-shortcodes.php
│   ├── class-pdf-embed-seo-optimize-block.php
│   ├── class-pdf-embed-seo-optimize-thumbnail.php
│   └── class-pdf-embed-seo-optimize-rest-api.php
├── admin/
│   ├── css/admin-styles.css
│   ├── js/admin-scripts.js
│   └── views/
├── public/
│   ├── css/viewer-styles.css
│   ├── js/viewer-scripts.js
│   └── views/
├── assets/pdfjs/                        # PDF.js library (bundled)
└── languages/
```

### Drupal Module (Free)

```
drupal-pdf-embed-seo/
├── pdf_embed_seo.info.yml
├── pdf_embed_seo.module
├── pdf_embed_seo.install
├── pdf_embed_seo.routing.yml
├── pdf_embed_seo.services.yml
├── config/
│   ├── install/pdf_embed_seo.settings.yml
│   └── schema/pdf_embed_seo.schema.yml
├── src/
│   ├── Entity/PdfDocument.php
│   ├── Controller/
│   ├── Form/
│   ├── Plugin/Block/
│   └── Service/
├── templates/
├── assets/
└── modules/pdf_embed_seo_premium/       # Premium submodule
```

### React/Next.js Module

```
react-pdf-embed-seo/
├── packages/
│   ├── core/                            # @pdf-embed-seo/core
│   │   ├── src/
│   │   │   ├── types/                   # TypeScript definitions
│   │   │   ├── utils/                   # Shared utilities
│   │   │   ├── api/                     # API clients (WP, Drupal)
│   │   │   └── constants/
│   │   └── package.json
│   │
│   ├── react/                           # @pdf-embed-seo/react
│   │   ├── src/
│   │   │   ├── components/
│   │   │   │   ├── PdfViewer/
│   │   │   │   ├── PdfArchive/
│   │   │   │   ├── PdfSeo/
│   │   │   │   └── PdfProvider/
│   │   │   ├── hooks/
│   │   │   │   ├── usePdfDocument.ts
│   │   │   │   ├── usePdfDocuments.ts
│   │   │   │   ├── usePdfViewer.ts
│   │   │   │   └── usePdfSeo.ts
│   │   │   ├── nextjs/                  # Next.js specific
│   │   │   │   ├── metadata.ts
│   │   │   │   ├── static-params.ts
│   │   │   │   └── route-handlers.ts
│   │   │   └── styles/
│   │   └── package.json
│   │
│   └── react-premium/                   # @pdf-embed-seo/react-premium (React Pro)
│       ├── src/
│       │   ├── components/
│       │   │   ├── PdfPasswordModal/
│       │   │   ├── PdfProgressBar/
│       │   │   ├── PdfAnalytics/
│       │   │   ├── PdfSearch/
│       │   │   └── PdfBookmarks/
│       │   ├── hooks/
│       │   │   ├── usePasswordProtection.ts
│       │   │   ├── useReadingProgress.ts
│       │   │   └── useAnalytics.ts
│       │   └── nextjs/
│       │       ├── middleware.ts
│       │       └── sitemap.ts
│       └── package.json
│
├── apps/
│   └── demo-nextjs/                     # Demo application
├── package.json                         # Monorepo root
├── turbo.json
└── tsconfig.base.json
```

---

## REST API Reference

### API Base URLs

| Platform | Base URL |
|----------|----------|
| WordPress | `/wp-json/pdf-embed-seo/v1/` |
| Drupal | `/api/pdf-embed-seo/v1/` |
| React (custom) | Configurable via `PdfProvider` |

### Public Endpoints (Free)

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
| `GET` | `/analytics` | Analytics overview | Admin |
| `GET` | `/analytics/documents` | Per-document analytics | Admin |
| `GET` | `/analytics/export` | Export analytics CSV/JSON | Admin |
| `GET/POST` | `/documents/{id}/progress` | Reading progress | None |
| `POST` | `/documents/{id}/verify-password` | Verify PDF password | None |
| `POST` | `/documents/{id}/download` | Track PDF download | None |
| `POST` | `/documents/{id}/expiring-link` | Generate expiring link | Admin |
| `GET` | `/categories` | List PDF categories | None |
| `GET` | `/tags` | List PDF tags | None |

### Response Format

```json
{
  "id": 123,
  "title": "Document Title",
  "slug": "document-slug",
  "url": "https://site.com/pdf/document-slug/",
  "excerpt": "Description...",
  "date": "2024-01-15T10:30:00+00:00",
  "modified": "2024-06-20T14:45:00+00:00",
  "views": 1542,
  "thumbnail": "https://site.com/uploads/thumb.jpg",
  "allow_download": true,
  "allow_print": false
}
```

---

## React Component API Reference

### PdfViewer

```tsx
interface PdfViewerProps {
  // Required
  src: string | PdfDocument;

  // Dimensions
  width?: string | number;        // Default: '100%'
  height?: string | number;       // Default: '800px'

  // Controls
  allowDownload?: boolean;        // Default: true
  allowPrint?: boolean;           // Default: true
  showToolbar?: boolean;          // Default: true
  showPageNav?: boolean;          // Default: true
  showZoom?: boolean;             // Default: true

  // Display
  theme?: 'light' | 'dark' | 'system';
  initialPage?: number;
  initialZoom?: number | 'auto' | 'page-fit' | 'page-width';

  // Events
  onDocumentLoad?: (doc: PdfDocumentInfo) => void;
  onPageChange?: (page: number) => void;
  onZoomChange?: (zoom: number) => void;
  onError?: (error: Error) => void;

  // Premium only
  enableSearch?: boolean;
  enableBookmarks?: boolean;
  enableProgress?: boolean;

  // Styling
  className?: string;
  style?: React.CSSProperties;
}
```

### PdfArchive

```tsx
interface PdfArchiveProps {
  // Data source
  documents?: PdfDocument[];      // Controlled mode
  apiEndpoint?: string;           // Fetch from API

  // Display
  view?: 'grid' | 'list';
  columns?: 1 | 2 | 3 | 4;
  showThumbnails?: boolean;
  showViewCount?: boolean;
  showExcerpt?: boolean;

  // Pagination
  perPage?: number;
  showPagination?: boolean;

  // Filtering
  showSearch?: boolean;
  showSort?: boolean;
  defaultSort?: 'date' | 'title' | 'views';

  // Premium only
  showCategoryFilter?: boolean;
  showTagFilter?: boolean;

  // Events
  onDocumentClick?: (doc: PdfDocument) => void;

  // Custom rendering
  renderCard?: (doc: PdfDocument) => React.ReactNode;
}
```

### PdfJsonLd

```tsx
interface PdfJsonLdProps {
  document?: PdfDocument;
  type?: 'DigitalDocument' | 'CollectionPage';
  includeBreadcrumbs?: boolean;
  includeSpeakable?: boolean;

  // Premium only
  includeFaq?: boolean;
  includeTableOfContents?: boolean;
}
```

### PdfProvider

```tsx
interface PdfProviderProps {
  children: React.ReactNode;

  // Mode
  mode?: 'standalone' | 'wordpress' | 'drupal' | 'custom';

  // Configuration
  config?: {
    apiUrl?: string;
    licenseKey?: string;          // Premium
    theme?: 'light' | 'dark' | 'system';
    locale?: string;
  };
}
```

---

## WordPress Hooks Reference

### Actions

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_pdf_viewed` | `$post_id, $count` | PDF was viewed |
| `pdf_embed_seo_premium_init` | - | Premium features initialized |
| `pdf_embed_seo_optimize_settings_saved` | `$post_id, $settings` | Settings saved |

### Filters

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_post_type_args` | `$args` | Modify CPT registration |
| `pdf_embed_seo_schema_data` | `$schema, $post_id` | Modify Schema.org data |
| `pdf_embed_seo_viewer_options` | `$options, $post_id` | Modify viewer options |
| `pdf_embed_seo_rest_document` | `$data, $post, $detailed` | Modify REST response |

---

## Drupal Hooks Reference

### Alter Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_document_data_alter` | Modify API document data |
| `hook_pdf_embed_seo_viewer_options_alter` | Modify viewer options |
| `hook_pdf_embed_seo_schema_alter` | Modify Schema.org output |

---

## URL Structure

| Page | WordPress | Drupal | Next.js (suggested) |
|------|-----------|--------|---------------------|
| Archive | `/pdf/` | `/pdf` | `/pdf` |
| Single PDF | `/pdf/{slug}/` | `/pdf/{slug}` | `/pdf/[slug]` |
| XML Sitemap | `/pdf/sitemap.xml` | `/pdf/sitemap.xml` | `/pdf/sitemap.xml` |

---

## Feature Matrix

| Feature | WP Free | WP Prem | Drupal Free | Drupal Prem | React Free | React Pro |
|---------|:-------:|:-------:|:-----------:|:-----------:|:----------:|:----------:|
| PDF.js Viewer | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Light/Dark Theme | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| System Theme | - | - | - | - | ✓ | ✓ |
| Print/Download Control | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Schema.org SEO | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| OpenGraph/Twitter | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Archive Component | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| REST API | ✓ | ✓ | ✓ | ✓ | Client | Client |
| SSR/SSG Support | - | - | - | - | ✓ | ✓ |
| React Hooks | - | - | - | - | ✓ | ✓ |
| TypeScript | - | - | - | - | ✓ | ✓ |
| Password Protection | - | ✓ | - | ✓ | - | ✓ |
| Reading Progress | - | ✓ | - | ✓ | - | ✓ |
| Analytics | - | ✓ | - | ✓ | - | ✓ |
| Text Search | - | ✓ | - | ✓ | - | ✓ |
| Bookmarks | - | ✓ | - | ✓ | - | ✓ |
| XML Sitemap | - | ✓ | - | ✓ | - | ✓ |
| Categories/Tags | - | ✓ | - | ✓ | - | ✓ |

---

## Dependencies

### WordPress
- WordPress 5.8+
- PHP 7.4+
- Mozilla PDF.js (bundled)
- Optional: Yoast SEO
- Optional: ImageMagick or Ghostscript (thumbnails)

### Drupal
- Drupal 10 or 11
- PHP 8.1+
- Core modules: node, file, taxonomy, path, path_alias
- Optional: ImageMagick or Ghostscript (thumbnails)

### React/Next.js
- React 18.0+ or 19.0+
- Next.js 13.0+ (optional, for SSR/SSG)
- TypeScript 5.0+ (recommended)
- pdfjs-dist 4.0+ (peer dependency)

---

## Security Measures

### All Platforms
1. **PDF URL Protection**: Direct URLs hidden via API
2. **Input Sanitization**: All inputs sanitized
3. **Output Escaping**: All outputs escaped
4. **CSRF Protection**: Forms protected with tokens

### WordPress/Drupal Specific
5. **Nonce Verification**: All AJAX requests verified
6. **Capability Checks**: Admin functions require permissions
7. **Password Hashing**: Passwords stored hashed (Premium)

### React/Next.js Specific
8. **API Route Protection**: Server-side validation
9. **Environment Variables**: Secrets in server-only env vars
10. **Content Security Policy**: Recommended CSP headers

---

## Premium Purchase URL

**https://pdfviewer.drossmedia.de**

---

## Changelog

### 1.3.0 (Planned - React Module)
- Initial React/Next.js module release
- `@pdf-embed-seo/react` npm package (free)
- `@pdf-embed-seo/react-premium` npm package (pro)
- React components: PdfViewer, PdfArchive, PdfSeo, PdfProvider
- React hooks: usePdfDocument, usePdfDocuments, usePdfViewer, usePdfSeo
- Next.js App Router integration
- Next.js Pages Router integration
- SSR/SSG support
- TypeScript support
- API clients for WordPress and Drupal backends
- Standalone mode (no backend required)

### 1.2.8 (Current - WP/Drupal)
- Version bump to 1.2.8 across all modules

### 1.2.7
- Sidebar/Widget Area Removal - PDF pages now display full-width
- Archive Page Styling Settings (WordPress)
- Fix "Security check failed" error on cached pages

### 1.2.6
- WordPress Plugin Check compliance fixes
- Hook renamed: `pdf_embed_seo_settings_saved` → `pdf_embed_seo_optimize_settings_saved`
- Drupal security fixes (password hashing, XSS)

### 1.2.5
- Download Tracking
- Expiring Access Links
- Drupal Premium feature parity with WordPress

### 1.2.0
- REST API endpoints for all platforms
- Reading progress tracking (Premium)
- XML Sitemap (Premium)

---

## Credits

Made with ♥ by [Dross:Media](https://dross.net/media/)

**License:**
- WordPress/Drupal: GPL v2 or later
- React Free: MIT
- React Pro: Commercial

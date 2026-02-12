# PDF Embed & SEO Optimize - React/Next.js Module Implementation Plan

## Executive Summary

This document outlines the implementation plan for adding React/Next.js support to the PDF Embed & SEO Optimize solution, creating a third platform alongside the existing WordPress plugin and Drupal module.

**Package Name:** `@pdf-embed-seo/react`
**Premium Package:** `@pdf-embed-seo/react-premium`
**Target Version:** 1.3.0

---

## 1. Architecture Overview

### 1.1 Package Structure

The React module will be distributed as npm packages:

```
react-pdf-embed-seo/
├── packages/
│   ├── core/                    # @pdf-embed-seo/core (shared utilities)
│   ├── react/                   # @pdf-embed-seo/react (free components)
│   └── react-premium/           # @pdf-embed-seo/react-premium (premium)
├── apps/
│   └── demo-nextjs/             # Demo Next.js application
├── package.json                 # Monorepo root (pnpm workspaces)
├── turbo.json                   # Turborepo configuration
└── tsconfig.base.json           # Shared TypeScript config
```

### 1.2 Module Architecture

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
│  │   (usePdf...)   │  │   (fetch/axios) │                   │
│  └─────────────────┘  └─────────────────┘                   │
├─────────────────────────────────────────────────────────────┤
│           @pdf-embed-seo/react-premium (Optional)            │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌─────────────┐ │
│  │ Analytics │ │ Password  │ │  Reading  │ │   Premium   │ │
│  │   Hooks   │ │   Modal   │ │  Progress │ │  Components │ │
│  └───────────┘ └───────────┘ └───────────┘ └─────────────┘ │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌─────────────┐ │
│  │  Search   │ │ Bookmarks │ │  Sitemap  │ │   Admin     │ │
│  │  Feature  │ │ Component │ │ Generator │ │  Dashboard  │ │
│  └───────────┘ └───────────┘ └───────────┘ └─────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Implementation Phases

### Phase 1: Core Foundation (Priority: Critical)

**Objective:** Establish the base package structure and core viewer component.

#### Tasks:

1. **Project Setup**
   - Initialize monorepo with pnpm workspaces
   - Configure Turborepo for build orchestration
   - Set up TypeScript with strict mode
   - Configure ESLint and Prettier
   - Set up Vitest for testing
   - Configure Changesets for versioning

2. **Core Package (@pdf-embed-seo/core)**
   - PDF.js wrapper utilities
   - Type definitions for PDF documents
   - Shared constants and configuration
   - API client abstraction (works with WP, Drupal, or standalone)

3. **PdfViewer Component**
   - Wrap Mozilla PDF.js in React component
   - Support controlled and uncontrolled modes
   - Implement print/download controls
   - Light and dark theme support
   - Responsive design
   - Full-screen mode
   - Page navigation controls
   - Zoom controls

4. **PdfProvider Context**
   - Global configuration provider
   - Theme management
   - API endpoint configuration
   - License validation (for premium)

### Phase 2: SEO Components (Priority: High)

**Objective:** Implement SEO optimization components for Next.js.

#### Tasks:

1. **PdfJsonLd Component**
   - Schema.org DigitalDocument markup
   - CollectionPage for archives
   - BreadcrumbList generation
   - FAQPage schema (premium)

2. **PdfMeta Component**
   - OpenGraph meta tags
   - Twitter Card meta tags
   - Canonical URL handling
   - Next.js Metadata API integration

3. **PdfSpeakable Component**
   - SpeakableSpecification schema
   - potentialAction (Read/Download)
   - accessMode and accessibilityFeature

4. **Next.js Integration**
   - App Router support (generateMetadata)
   - Pages Router support (next/head)
   - generateStaticParams helper for SSG
   - Server Components support

### Phase 3: Archive & Listing (Priority: High)

**Objective:** Implement archive and listing components.

#### Tasks:

1. **PdfArchive Component**
   - Grid and list view modes
   - Pagination support
   - Search filtering
   - Sort options
   - Category/tag filtering (premium)

2. **PdfCard Component**
   - Thumbnail display
   - Document metadata
   - View count badge
   - Click-through to document

3. **PdfBreadcrumbs Component**
   - Accessible breadcrumb navigation
   - Schema.org BreadcrumbList
   - Customizable separator

### Phase 4: React Hooks (Priority: High)

**Objective:** Create custom hooks for PDF functionality.

#### Tasks:

1. **usePdfDocument Hook**
   - Fetch single document
   - Loading and error states
   - Caching with SWR/React Query option

2. **usePdfDocuments Hook**
   - Fetch document list
   - Pagination state
   - Filter and search state
   - Infinite scroll support

3. **usePdfViewer Hook**
   - Viewer state management
   - Page navigation
   - Zoom control
   - Full-screen toggle

4. **usePdfSeo Hook**
   - Generate SEO metadata
   - Schema.org data generation
   - Meta tag preparation

### Phase 5: API Integration (Priority: Medium)

**Objective:** Create API client for backend integration.

#### Tasks:

1. **API Client Factory**
   - WordPress REST API adapter
   - Drupal REST API adapter
   - Standalone/custom API adapter
   - Mock adapter for development

2. **API Types**
   - Document types
   - Settings types
   - Analytics types (premium)
   - Progress types (premium)

3. **Server Actions (Next.js)**
   - Track view server action
   - Track download server action
   - Save progress server action (premium)

### Phase 6: Premium Features (Priority: Medium)

**Objective:** Implement premium-only features.

#### Tasks:

1. **Password Protection**
   - PdfPasswordModal component
   - usePasswordProtection hook
   - Session storage for unlocked docs

2. **Reading Progress**
   - useReadingProgress hook
   - PdfProgressBar component
   - Resume reading functionality
   - Page/scroll/zoom persistence

3. **Analytics Dashboard**
   - PdfAnalyticsDashboard component
   - useAnalytics hook
   - Chart components (views, downloads)
   - Export functionality

4. **Enhanced Viewer**
   - Text search in PDF
   - Bookmark navigation
   - Annotation support

5. **Sitemap Generation**
   - generatePdfSitemap utility
   - Next.js sitemap.xml integration
   - XSL stylesheet

### Phase 7: Next.js Specific Features (Priority: Medium)

**Objective:** Deep Next.js integration.

#### Tasks:

1. **Route Handlers**
   - PDF proxy endpoint (secure URL)
   - Analytics endpoint
   - Progress endpoint

2. **Middleware**
   - Password protection middleware
   - Role-based access middleware

3. **File Conventions**
   - Example page.tsx templates
   - Example layout.tsx templates
   - Example generateStaticParams

4. **Edge Runtime Support**
   - Edge-compatible components
   - Edge-compatible hooks

### Phase 8: Documentation & Examples (Priority: High)

**Objective:** Comprehensive documentation and examples.

#### Tasks:

1. **API Documentation**
   - Component props documentation
   - Hook API documentation
   - Type definitions documentation

2. **Guides**
   - Getting started guide
   - Next.js App Router integration
   - Next.js Pages Router integration
   - Backend integration guide
   - Styling customization guide

3. **Examples**
   - Basic viewer example
   - Archive page example
   - SEO optimization example
   - Premium features example
   - Custom styling example

---

## 3. File Structure Detail

### 3.1 Core Package

```
packages/core/
├── package.json
├── tsconfig.json
├── src/
│   ├── index.ts                 # Public exports
│   ├── types/
│   │   ├── document.ts          # PdfDocument type
│   │   ├── settings.ts          # Settings type
│   │   ├── api.ts               # API response types
│   │   └── index.ts
│   ├── utils/
│   │   ├── pdfjs-loader.ts      # Dynamic PDF.js loading
│   │   ├── schema-generator.ts  # Schema.org generation
│   │   ├── meta-generator.ts    # Meta tag generation
│   │   └── index.ts
│   ├── api/
│   │   ├── client.ts            # Base API client
│   │   ├── wordpress.ts         # WordPress adapter
│   │   ├── drupal.ts            # Drupal adapter
│   │   ├── standalone.ts        # Standalone adapter
│   │   └── index.ts
│   └── constants/
│       ├── defaults.ts          # Default configuration
│       └── index.ts
└── dist/                        # Build output
```

### 3.2 React Package (Free)

```
packages/react/
├── package.json
├── tsconfig.json
├── src/
│   ├── index.ts                 # Public exports
│   ├── components/
│   │   ├── PdfViewer/
│   │   │   ├── PdfViewer.tsx
│   │   │   ├── PdfViewer.module.css
│   │   │   ├── PdfToolbar.tsx
│   │   │   ├── PdfPageNav.tsx
│   │   │   ├── PdfZoomControls.tsx
│   │   │   └── index.ts
│   │   ├── PdfArchive/
│   │   │   ├── PdfArchive.tsx
│   │   │   ├── PdfArchive.module.css
│   │   │   ├── PdfCard.tsx
│   │   │   ├── PdfGrid.tsx
│   │   │   ├── PdfList.tsx
│   │   │   └── index.ts
│   │   ├── PdfSeo/
│   │   │   ├── PdfJsonLd.tsx
│   │   │   ├── PdfMeta.tsx
│   │   │   ├── PdfBreadcrumbs.tsx
│   │   │   └── index.ts
│   │   ├── PdfProvider/
│   │   │   ├── PdfProvider.tsx
│   │   │   ├── PdfContext.ts
│   │   │   └── index.ts
│   │   └── index.ts
│   ├── hooks/
│   │   ├── usePdfDocument.ts
│   │   ├── usePdfDocuments.ts
│   │   ├── usePdfViewer.ts
│   │   ├── usePdfSeo.ts
│   │   ├── usePdfTheme.ts
│   │   └── index.ts
│   ├── nextjs/
│   │   ├── metadata.ts          # generateMetadata helper
│   │   ├── static-params.ts     # generateStaticParams helper
│   │   ├── route-handlers.ts    # Route handler utilities
│   │   └── index.ts
│   └── styles/
│       ├── viewer.css
│       ├── viewer-dark.css
│       ├── archive.css
│       └── index.css
└── dist/
```

### 3.3 React Pro Package

```
packages/react-premium/
├── package.json
├── tsconfig.json
├── src/
│   ├── index.ts
│   ├── components/
│   │   ├── PdfPasswordModal/
│   │   │   ├── PdfPasswordModal.tsx
│   │   │   └── index.ts
│   │   ├── PdfProgressBar/
│   │   │   ├── PdfProgressBar.tsx
│   │   │   └── index.ts
│   │   ├── PdfAnalytics/
│   │   │   ├── PdfAnalyticsDashboard.tsx
│   │   │   ├── PdfViewsChart.tsx
│   │   │   ├── PdfDownloadsChart.tsx
│   │   │   └── index.ts
│   │   ├── PdfSearch/
│   │   │   ├── PdfSearchBar.tsx
│   │   │   ├── PdfSearchResults.tsx
│   │   │   └── index.ts
│   │   ├── PdfBookmarks/
│   │   │   ├── PdfBookmarkList.tsx
│   │   │   └── index.ts
│   │   └── index.ts
│   ├── hooks/
│   │   ├── usePasswordProtection.ts
│   │   ├── useReadingProgress.ts
│   │   ├── useAnalytics.ts
│   │   ├── usePdfSearch.ts
│   │   ├── useBookmarks.ts
│   │   └── index.ts
│   ├── nextjs/
│   │   ├── middleware.ts        # Auth middleware helpers
│   │   ├── sitemap.ts           # Sitemap generation
│   │   └── index.ts
│   └── styles/
│       ├── premium.css
│       └── analytics.css
└── dist/
```

---

## 4. Component API Design

### 4.1 PdfViewer Component

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
  initialPage?: number;           // Default: 1
  initialZoom?: number | 'auto' | 'page-fit' | 'page-width';

  // Events
  onDocumentLoad?: (doc: PdfDocumentInfo) => void;
  onPageChange?: (page: number) => void;
  onZoomChange?: (zoom: number) => void;
  onError?: (error: Error) => void;

  // Premium
  enableSearch?: boolean;         // Premium only
  enableBookmarks?: boolean;      // Premium only
  enableProgress?: boolean;       // Premium only

  // Styling
  className?: string;
  style?: React.CSSProperties;
}

// Usage
<PdfViewer
  src={document}
  height="600px"
  allowDownload={false}
  theme="dark"
  onPageChange={(page) => console.log(`Page: ${page}`)}
/>
```

### 4.2 PdfArchive Component

```tsx
interface PdfArchiveProps {
  // Data
  documents?: PdfDocument[];      // Controlled mode
  apiEndpoint?: string;           // Uncontrolled mode (fetch from API)

  // Display
  view?: 'grid' | 'list';         // Default: 'grid'
  columns?: 1 | 2 | 3 | 4;        // Grid columns (responsive)
  showThumbnails?: boolean;       // Default: true
  showViewCount?: boolean;        // Default: true
  showExcerpt?: boolean;          // Default: true

  // Pagination
  perPage?: number;               // Default: 12
  showPagination?: boolean;       // Default: true

  // Filtering
  showSearch?: boolean;           // Default: true
  showSort?: boolean;             // Default: true
  defaultSort?: 'date' | 'title' | 'views';

  // Premium
  showCategoryFilter?: boolean;   // Premium only
  showTagFilter?: boolean;        // Premium only

  // Events
  onDocumentClick?: (doc: PdfDocument) => void;

  // Rendering
  renderCard?: (doc: PdfDocument) => React.ReactNode;

  // Styling
  className?: string;
}

// Usage
<PdfArchive
  apiEndpoint="/api/pdf-documents"
  view="grid"
  columns={3}
  perPage={9}
  showSearch
/>
```

### 4.3 PdfJsonLd Component

```tsx
interface PdfJsonLdProps {
  document: PdfDocument;
  type?: 'DigitalDocument' | 'CollectionPage';
  includeBreadcrumbs?: boolean;
  includeSpeakable?: boolean;

  // Premium
  includeFaq?: boolean;           // Premium only
  includeTableOfContents?: boolean; // Premium only
}

// Usage (Next.js App Router)
export default function PdfPage({ document }) {
  return (
    <>
      <PdfJsonLd document={document} includeBreadcrumbs />
      <PdfViewer src={document} />
    </>
  );
}
```

### 4.4 Hooks API

```tsx
// usePdfDocument
const { document, isLoading, error, refetch } = usePdfDocument(id, {
  apiEndpoint: '/api/documents',
});

// usePdfDocuments
const {
  documents,
  pagination,
  isLoading,
  error,
  setPage,
  setSearch,
  setSort
} = usePdfDocuments({
  apiEndpoint: '/api/documents',
  perPage: 10,
  initialSort: 'date',
});

// usePdfViewer
const {
  currentPage,
  totalPages,
  zoom,
  isFullscreen,
  setPage,
  setZoom,
  toggleFullscreen,
  nextPage,
  prevPage,
} = usePdfViewer(viewerRef);

// usePdfSeo
const { jsonLd, metaTags, breadcrumbs } = usePdfSeo(document, {
  siteUrl: 'https://example.com',
  siteName: 'My Site',
});
```

---

## 5. Next.js Integration

### 5.1 App Router Example

```tsx
// app/pdf/[slug]/page.tsx
import { PdfViewer, PdfJsonLd, PdfBreadcrumbs } from '@pdf-embed-seo/react';
import { generatePdfMetadata, getPdfDocument } from '@pdf-embed-seo/react/nextjs';

export async function generateMetadata({ params }) {
  const document = await getPdfDocument(params.slug);
  return generatePdfMetadata(document, {
    siteUrl: process.env.NEXT_PUBLIC_SITE_URL,
  });
}

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
    </main>
  );
}
```

### 5.2 API Route Handler

```tsx
// app/api/pdf/[id]/route.ts
import { createPdfRouteHandler } from '@pdf-embed-seo/react/nextjs';

export const { GET, POST } = createPdfRouteHandler({
  // Connect to WordPress, Drupal, or database
  adapter: 'wordpress',
  apiUrl: process.env.WP_API_URL,
});
```

### 5.3 Sitemap Generation (Premium)

```tsx
// app/pdf/sitemap.xml/route.ts
import { generatePdfSitemap } from '@pdf-embed-seo/react-premium/nextjs';

export async function GET() {
  const documents = await getAllPdfDocuments();
  return generatePdfSitemap(documents, {
    siteUrl: process.env.NEXT_PUBLIC_SITE_URL,
  });
}
```

---

## 6. Standalone Mode vs Backend Integration

### 6.1 Standalone Mode

For sites without WordPress/Drupal backend:

```tsx
// Store documents in local JSON, database, or CMS
<PdfProvider
  mode="standalone"
  config={{
    storageAdapter: 'local', // or 'database', 'cms'
  }}
>
  <PdfViewer src="/pdfs/document.pdf" />
</PdfProvider>
```

### 6.2 WordPress Integration

```tsx
<PdfProvider
  mode="wordpress"
  config={{
    apiUrl: 'https://mysite.com/wp-json/pdf-embed-seo/v1',
  }}
>
  <PdfArchive />
</PdfProvider>
```

### 6.3 Drupal Integration

```tsx
<PdfProvider
  mode="drupal"
  config={{
    apiUrl: 'https://mysite.com/api/pdf-embed-seo/v1',
  }}
>
  <PdfArchive />
</PdfProvider>
```

---

## 7. Feature Parity Matrix

| Feature | WP | Drupal | React | Notes |
|---------|:--:|:------:|:-----:|-------|
| PDF.js Viewer | ✓ | ✓ | ✓ | Core feature |
| Light/Dark Theme | ✓ | ✓ | ✓ | + system preference |
| Print/Download Control | ✓ | ✓ | ✓ | |
| Schema.org SEO | ✓ | ✓ | ✓ | Next.js optimized |
| OpenGraph/Twitter | ✓ | ✓ | ✓ | Next.js Metadata API |
| Archive Page | ✓ | ✓ | ✓ | Component-based |
| REST API Client | N/A | N/A | ✓ | Connects to WP/Drupal |
| SSR/SSG Support | N/A | N/A | ✓ | Next.js specific |
| React Hooks | N/A | N/A | ✓ | React specific |
| Password Protection | ✓ | ✓ | ✓ | Premium |
| Reading Progress | ✓ | ✓ | ✓ | Premium |
| Analytics | ✓ | ✓ | ✓ | Premium |
| XML Sitemap | ✓ | ✓ | ✓ | Premium |

---

## 8. Dependencies

### Production Dependencies

```json
{
  "pdfjs-dist": "^4.0.0",
  "react": "^18.0.0 || ^19.0.0",
  "react-dom": "^18.0.0 || ^19.0.0"
}

// Peer dependencies
{
  "next": "^13.0.0 || ^14.0.0 || ^15.0.0"
}
```

### Development Dependencies

```json
{
  "@types/react": "^18.0.0",
  "typescript": "^5.0.0",
  "vitest": "^1.0.0",
  "@testing-library/react": "^14.0.0",
  "turbo": "^2.0.0",
  "tsup": "^8.0.0",
  "changesets": "^2.0.0"
}
```

---

## 9. Distribution

### NPM Packages

| Package | Description | License |
|---------|-------------|---------|
| `@pdf-embed-seo/core` | Shared utilities | MIT |
| `@pdf-embed-seo/react` | Free React components | MIT |
| `@pdf-embed-seo/react-premium` | Premium components | Commercial |

### Bundle Formats

- ESM (for modern bundlers)
- CJS (for Node.js/older bundlers)
- Types (TypeScript declarations)

### CDN Distribution

```html
<!-- UMD build for CDN usage -->
<script src="https://unpkg.com/@pdf-embed-seo/react/dist/umd/index.js"></script>
```

---

## 10. Testing Strategy

### Unit Tests

- Component rendering tests
- Hook behavior tests
- Utility function tests

### Integration Tests

- API client integration
- Next.js page rendering
- SEO output validation

### E2E Tests

- Playwright tests for viewer functionality
- Accessibility testing (axe-core)

---

## 11. Success Criteria

1. **Feature Parity:** All free features from WP/Drupal available
2. **TypeScript:** 100% TypeScript with strict mode
3. **Bundle Size:** Core viewer < 50KB gzipped (excluding PDF.js)
4. **Test Coverage:** > 80% coverage
5. **Accessibility:** WCAG 2.1 AA compliant
6. **Documentation:** Complete API docs and examples
7. **Next.js:** Full App Router and Pages Router support

---

## 12. Migration Path

For users with existing WordPress/Drupal installations:

1. Install React package alongside existing CMS
2. Configure API client to connect to existing REST API
3. Gradually migrate frontend to React/Next.js
4. Keep CMS as headless backend for content management

---

## Credits

Made with ♥ by [Dross:Media](https://dross.net/media/)

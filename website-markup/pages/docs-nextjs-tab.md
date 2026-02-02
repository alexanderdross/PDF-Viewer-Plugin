# React / Next.js Documentation

## Table of Contents

1. [Installation](#installation)
2. [Quick Start](#quick-start)
3. [Components](#components)
4. [Hooks](#hooks)
5. [Next.js Integration](#nextjs-integration)
6. [Backend Integration](#backend-integration)
7. [Premium Features](#premium-features)
8. [API Reference](#api-reference)

---

## Installation

### Free Package

```bash
# npm
npm install @pdf-embed-seo/react

# yarn
yarn add @pdf-embed-seo/react

# pnpm
pnpm add @pdf-embed-seo/react
```

### Premium Package

```bash
npm install @pdf-embed-seo/react-premium
```

### Requirements

- React 18.0 or higher
- Next.js 13.0+ (optional, for SSR/SSG features)
- TypeScript 5.0+ (optional, but recommended)

---

## Quick Start

### Basic Setup

Wrap your application with `PdfProvider`:

```tsx
// app/layout.tsx or _app.tsx
import { PdfProvider } from '@pdf-embed-seo/react';
import '@pdf-embed-seo/react/styles';

export default function RootLayout({ children }) {
  return (
    <html>
      <body>
        <PdfProvider
          config={{
            siteUrl: 'https://example.com',
            siteName: 'My Site',
            theme: 'light',
          }}
        >
          {children}
        </PdfProvider>
      </body>
    </html>
  );
}
```

### Display a PDF

```tsx
import { PdfViewer } from '@pdf-embed-seo/react';

function MyPage() {
  return (
    <PdfViewer
      src="/documents/report.pdf"
      height="600px"
      allowDownload
      allowPrint
    />
  );
}
```

---

## Components

### PdfViewer

The main PDF viewer component.

```tsx
import { PdfViewer } from '@pdf-embed-seo/react';

<PdfViewer
  src={document}           // URL string or PdfDocument object
  width="100%"             // CSS width
  height="800px"           // CSS height
  allowDownload={true}     // Show download button
  allowPrint={true}        // Show print button
  showToolbar={true}       // Show toolbar
  showPageNav={true}       // Show page navigation
  showZoom={true}          // Show zoom controls
  theme="light"            // 'light' | 'dark' | 'system'
  initialPage={1}          // Starting page
  initialZoom="auto"       // Zoom: number | 'auto' | 'page-fit' | 'page-width'
  onDocumentLoad={(info) => {}}  // Callback on load
  onPageChange={(page) => {}}    // Callback on page change
  onError={(error) => {}}        // Callback on error
  className=""             // Additional CSS class
/>
```

### PdfArchive

Display a grid or list of PDF documents.

```tsx
import { PdfArchive } from '@pdf-embed-seo/react';

<PdfArchive
  documents={documents}    // Array of PdfDocument (controlled)
  apiEndpoint="/api/pdf"   // API endpoint (uncontrolled)
  view="grid"              // 'grid' | 'list'
  columns={3}              // 1 | 2 | 3 | 4
  perPage={12}             // Documents per page
  showThumbnails={true}    // Show thumbnails
  showViewCount={true}     // Show view counts
  showExcerpt={true}       // Show excerpts
  showPagination={true}    // Show pagination
  showSearch={true}        // Show search box
  showSort={true}          // Show sort dropdown
  defaultSort="date"       // 'date' | 'title' | 'views'
  onDocumentClick={(doc) => {}}  // Click handler
  renderCard={(doc) => {}}       // Custom card renderer
/>
```

### PdfCard

Individual document card.

```tsx
import { PdfCard } from '@pdf-embed-seo/react';

<PdfCard
  document={document}
  showThumbnail={true}
  showViewCount={true}
  showExcerpt={true}
  showDate={true}
  onClick={() => {}}
  href="/pdf/document-slug"
/>
```

### PdfJsonLd

Schema.org JSON-LD markup.

```tsx
import { PdfJsonLd } from '@pdf-embed-seo/react';

<PdfJsonLd
  document={document}
  includeBreadcrumbs={true}
  includeSpeakable={true}
  includeFaq={true}       // Premium
/>
```

### PdfBreadcrumbs

Accessible breadcrumb navigation.

```tsx
import { PdfBreadcrumbs } from '@pdf-embed-seo/react';

<PdfBreadcrumbs
  document={document}
  homeLabel="Home"
  archiveLabel="PDF Documents"
  separator="/"
  includeSchema={true}
/>
```

---

## Hooks

### usePdfDocument

Fetch a single document.

```tsx
import { usePdfDocument } from '@pdf-embed-seo/react';

function Component({ id }) {
  const { document, isLoading, error, refetch } = usePdfDocument(id);

  if (isLoading) return <Loading />;
  if (error) return <Error error={error} />;

  return <PdfViewer src={document} />;
}
```

### usePdfDocumentBySlug

Fetch document by slug.

```tsx
import { usePdfDocumentBySlug } from '@pdf-embed-seo/react';

const { document, isLoading, error } = usePdfDocumentBySlug('my-document');
```

### usePdfDocuments

Fetch document list with pagination.

```tsx
import { usePdfDocuments } from '@pdf-embed-seo/react';

const {
  documents,
  pagination,
  isLoading,
  search,
  sortBy,
  setPage,
  setSearch,
  setSort,
  nextPage,
  prevPage,
} = usePdfDocuments({ perPage: 12 });
```

### usePdfViewer

Control viewer state.

```tsx
import { usePdfViewer } from '@pdf-embed-seo/react';

const {
  currentPage,
  totalPages,
  zoom,
  isFullscreen,
  setPage,
  nextPage,
  prevPage,
  setZoom,
  zoomIn,
  zoomOut,
  toggleFullscreen,
} = usePdfViewer({ initialPage: 1 });
```

### usePdfSeo

Generate SEO metadata.

```tsx
import { usePdfSeo } from '@pdf-embed-seo/react';

const { metaTags, jsonLd, breadcrumbs, nextMetadata } = usePdfSeo(document, {
  twitterHandle: '@example',
});
```

### usePdfTheme

Manage theme.

```tsx
import { usePdfTheme } from '@pdf-embed-seo/react';

const { theme, resolvedTheme, setTheme, toggleTheme } = usePdfTheme();
```

---

## Next.js Integration

### App Router (Recommended)

#### Generate Metadata

```tsx
// app/pdf/[slug]/page.tsx
import { generatePdfMetadata } from '@pdf-embed-seo/react/nextjs';

export async function generateMetadata({ params }) {
  const document = await getPdfDocument(params.slug);
  return generatePdfMetadata(document, {
    siteUrl: process.env.NEXT_PUBLIC_SITE_URL,
    siteName: 'My Site',
  });
}
```

#### Generate Static Params

```tsx
import { generatePdfStaticParams } from '@pdf-embed-seo/react/nextjs';

export async function generateStaticParams() {
  const documents = await getAllPdfDocuments();
  return generatePdfStaticParams(documents);
}
```

#### Route Handlers

```tsx
// app/api/pdf/[id]/route.ts
import { createPdfRouteHandler } from '@pdf-embed-seo/react/nextjs';

export const { GET, POST } = createPdfRouteHandler({
  adapter: 'wordpress',
  apiUrl: process.env.WP_API_URL,
});
```

### Pages Router

Use the `PdfMeta` component with `next/head`:

```tsx
import Head from 'next/head';
import { PdfMeta } from '@pdf-embed-seo/react';

function PdfPage({ document }) {
  return (
    <>
      <Head>
        <PdfMeta document={document} />
      </Head>
      <PdfViewer src={document} />
    </>
  );
}
```

---

## Backend Integration

### WordPress (Headless)

```tsx
<PdfProvider
  config={{
    mode: 'wordpress',
    apiUrl: 'https://your-site.com/wp-json/pdf-embed-seo/v1',
  }}
>
  <App />
</PdfProvider>
```

### Drupal (Headless)

```tsx
<PdfProvider
  config={{
    mode: 'drupal',
    apiUrl: 'https://your-site.com/api/pdf-embed-seo/v1',
  }}
>
  <App />
</PdfProvider>
```

### Standalone (No Backend)

```tsx
import { createStandaloneClient } from '@pdf-embed-seo/core';

const documents = [
  { id: 1, title: 'Report', slug: 'report', ... },
];

const client = createStandaloneClient(documents);

<PdfProvider config={{ mode: 'standalone' }} apiClient={client}>
  <App />
</PdfProvider>
```

---

## Premium Features

### Password Protection

```tsx
import { usePasswordProtection, PdfPasswordModal } from '@pdf-embed-seo/react-premium';

function ProtectedPdf({ document }) {
  const { isProtected, isUnlocked, verifyPassword } = usePasswordProtection(document);

  if (isProtected && !isUnlocked) {
    return <PdfPasswordModal isOpen onSubmit={verifyPassword} />;
  }

  return <PdfViewer src={document} />;
}
```

### Reading Progress

```tsx
import { useReadingProgress, PdfProgressBar } from '@pdf-embed-seo/react-premium';

function PdfWithProgress({ document }) {
  const { progress, percentComplete, saveProgress } = useReadingProgress(document);

  return (
    <>
      <PdfProgressBar progress={percentComplete} />
      <PdfViewer
        src={document}
        initialPage={progress?.page || 1}
        onPageChange={(page) => saveProgress({ page })}
      />
    </>
  );
}
```

### Analytics Dashboard

```tsx
import { PdfAnalyticsDashboard } from '@pdf-embed-seo/react-premium';

<PdfAnalyticsDashboard period="30days" showExport />
```

### Sitemap Generation

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

## API Reference

### Types

```typescript
interface PdfDocument {
  id: number | string;
  title: string;
  slug: string;
  url: string;
  pdfUrl?: string;
  excerpt?: string;
  date: string;
  modified: string;
  views: number;
  thumbnail?: string;
  allowDownload: boolean;
  allowPrint: boolean;
  passwordProtected?: boolean;
  pageCount?: number;
  // ... more fields
}

interface PdfProviderConfig {
  mode: 'standalone' | 'wordpress' | 'drupal';
  apiUrl?: string;
  theme?: 'light' | 'dark' | 'system';
  siteUrl?: string;
  siteName?: string;
}
```

### CSS Classes

| Class | Description |
|-------|-------------|
| `.pdf-viewer-wrapper` | Main viewer container |
| `.pdf-viewer-toolbar` | Viewer toolbar |
| `.pdf-viewer-container` | PDF canvas container |
| `.pdf-viewer-theme-light` | Light theme |
| `.pdf-viewer-theme-dark` | Dark theme |
| `.pdf-archive` | Archive wrapper |
| `.pdf-archive-grid` | Grid layout |
| `.pdf-archive-card` | Document card |

---

## Support

- **Documentation:** [pdfviewer.drossmedia.de/documentation](https://pdfviewer.drossmedia.de/documentation)
- **GitHub Issues:** [github.com/drossmedia/pdf-embed-seo-optimize/issues](https://github.com/drossmedia/pdf-embed-seo-optimize/issues)
- **Email:** support@drossmedia.de

---

*Made with ♥ by [Dross:Media](https://dross.net/media/)*

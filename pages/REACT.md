# PDF Embed & SEO Optimize for React & Next.js

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize for React" width="100">
</p>

<p align="center">
  <strong>Modern React components for PDF viewing and SEO</strong><br>
  TypeScript-first, hooks-based, fully customizable.
</p>

---

**Current Version:** 1.2.11
**Requires:** React 18+ | Node.js 18+ | TypeScript 5+ (recommended)
**License:** MIT (Free), Commercial (Pro)

---

## Quick Start

### Installation

```bash
# Free package
npm install @pdf-embed-seo/react
# or
pnpm add @pdf-embed-seo/react
# or
yarn add @pdf-embed-seo/react

# Pro package (requires license)
npm install @pdf-embed-seo/react-premium
```

### Basic Setup

```tsx
// app/layout.tsx (Next.js App Router)
import { PdfProvider } from '@pdf-embed-seo/react';
import '@pdf-embed-seo/react/styles';

export default function RootLayout({ children }) {
  return (
    <html>
      <body>
        <PdfProvider
          config={{
            apiBaseUrl: 'https://your-site.com/wp-json/pdf-embed-seo/v1',
            backendType: 'wordpress', // or 'drupal'
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

export default function PdfPage({ params }) {
  return (
    <PdfViewer
      documentId={params.id}
      height="800px"
      theme="light"
      allowDownload={true}
      allowPrint={true}
    />
  );
}
```

---

## NPM Packages

| Package | Version | License | Description |
|---------|---------|---------|-------------|
| `@pdf-embed-seo/core` | 1.2.11 | MIT | Core types, utilities, API client |
| `@pdf-embed-seo/react` | 1.2.11 | MIT | Free React components |
| `@pdf-embed-seo/react-premium` | 1.2.11 | Commercial | Pro React components |

---

## Features

### Viewer & Display

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Mozilla PDF.js Viewer (v4.0) | ✓ | ✓ |
| Light & Dark Themes | ✓ | ✓ |
| Responsive Design | ✓ | ✓ |
| Print Control (per PDF) | ✓ | ✓ |
| Download Control (per PDF) | ✓ | ✓ |
| Configurable Viewer Height | ✓ | ✓ |
| PdfViewer Component | ✓ | ✓ |
| iOS/Safari Print Support | ✓ | ✓ |
| Text Search (PdfSearch) | - | ✓ |
| Bookmark Navigation (PdfBookmarks) | - | ✓ |

### Components & Hooks

| Feature | Free | Premium |
|---------|:----:|:-------:|
| PdfViewer | ✓ | ✓ |
| PdfArchive | ✓ | ✓ |
| PdfSeo | ✓ | ✓ |
| PdfProvider | ✓ | ✓ |
| usePdf hook | ✓ | ✓ |
| usePdfList hook | ✓ | ✓ |
| usePdfViewer hook | ✓ | ✓ |
| useProgress hook | ✓ | ✓ |
| PdfPasswordModal | - | ✓ |
| PdfProgressBar | - | ✓ |
| PdfSearch | - | ✓ |
| PdfBookmarks | - | ✓ |
| PdfAnalytics | - | ✓ |
| useAnalytics hook | - | ✓ |
| usePassword hook | - | ✓ |
| useSearch hook | - | ✓ |
| useBookmarks hook | - | ✓ |

### SEO

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Schema.org DigitalDocument | ✓ | ✓ |
| Schema.org CollectionPage | ✓ | ✓ |
| Schema.org BreadcrumbList | ✓ | ✓ |
| OpenGraph Meta Tags | ✓ | ✓ |
| Twitter Card Support | ✓ | ✓ |
| PdfSeo Component | ✓ | ✓ |
| AI/GEO/AEO Schema | - | ✓ |

### Archive & Listing

| Feature | Free | Premium |
|---------|:----:|:-------:|
| PdfArchive Component | ✓ | ✓ |
| Grid/List Display Modes | ✓ | ✓ |
| Pagination | ✓ | ✓ |
| Search Filtering | ✓ | ✓ |
| Sorting Options | ✓ | ✓ |
| Category/Tag Filters | - | ✓ |

### Analytics & Security

| Feature | Free | Premium |
|---------|:----:|:-------:|
| Basic View Counter | ✓ | ✓ |
| Analytics Dashboard | - | ✓ |
| Download Tracking | - | ✓ |
| Password Protection | - | ✓ |
| Reading Progress | - | ✓ |
| Expiring Access Links | - | ✓ |

---

## Free Components

### PdfViewer

Display a PDF document with full controls.

```tsx
import { PdfViewer } from '@pdf-embed-seo/react';

<PdfViewer
  documentId={123}
  height="800px"
  theme="light"
  allowDownload={true}
  allowPrint={true}
  onLoad={(pdf) => console.log('Loaded', pdf)}
  onPageChange={(page) => console.log('Page', page)}
  onError={(error) => console.error(error)}
/>
```

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `documentId` | `string \| number` | Required | Document ID |
| `height` | `string` | `"800px"` | Viewer height |
| `theme` | `"light" \| "dark"` | `"light"` | Color theme |
| `allowDownload` | `boolean` | `true` | Enable download |
| `allowPrint` | `boolean` | `true` | Enable print |
| `onLoad` | `(pdf) => void` | - | Load callback |
| `onPageChange` | `(page) => void` | - | Page change callback |
| `onError` | `(error) => void` | - | Error callback |

### PdfArchive

Display a list or grid of PDF documents.

```tsx
import { PdfArchive } from '@pdf-embed-seo/react';

<PdfArchive
  displayMode="grid"
  perPage={12}
  showSearch={true}
  showPagination={true}
  orderBy="date"
  order="desc"
/>
```

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `displayMode` | `"grid" \| "list"` | `"grid"` | Display mode |
| `perPage` | `number` | `10` | Items per page |
| `showSearch` | `boolean` | `true` | Show search |
| `showPagination` | `boolean` | `true` | Show pagination |
| `orderBy` | `string` | `"date"` | Sort field |
| `order` | `"asc" \| "desc"` | `"desc"` | Sort direction |

### PdfSeo

Add SEO schema to your PDF pages.

```tsx
import { PdfSeo } from '@pdf-embed-seo/react';

<PdfSeo
  document={document}
  includeBreadcrumbs={true}
/>
```

### PdfProvider

Configure the API connection.

```tsx
import { PdfProvider } from '@pdf-embed-seo/react';

<PdfProvider
  config={{
    apiBaseUrl: 'https://your-site.com/wp-json/pdf-embed-seo/v1',
    backendType: 'wordpress', // or 'drupal'
    licenseKey: 'your-license-key', // For premium features
  }}
>
  {children}
</PdfProvider>
```

---

## Free Hooks

### usePdf

Fetch a single PDF document.

```tsx
import { usePdf } from '@pdf-embed-seo/react';

function PdfPage({ id }) {
  const { document, loading, error, refetch } = usePdf(id);

  if (loading) return <Spinner />;
  if (error) return <Error message={error.message} />;

  return (
    <div>
      <h1>{document.title}</h1>
      <p>{document.excerpt}</p>
      <p>Views: {document.views}</p>
      <PdfViewer documentId={id} />
    </div>
  );
}
```

**Returns:**

| Property | Type | Description |
|----------|------|-------------|
| `document` | `PdfDocument \| null` | Document data |
| `loading` | `boolean` | Loading state |
| `error` | `Error \| null` | Error if any |
| `refetch` | `() => void` | Refetch data |

### usePdfList

Fetch a paginated list of documents.

```tsx
import { usePdfList } from '@pdf-embed-seo/react';

function ArchivePage() {
  const { documents, pagination, loading, error, fetchPage } = usePdfList({
    page: 1,
    perPage: 10,
    orderBy: 'date',
    order: 'desc',
    search: '',
  });

  return (
    <div>
      {documents.map(doc => (
        <PdfCard key={doc.id} document={doc} />
      ))}
      <Pagination
        current={pagination.page}
        total={pagination.totalPages}
        onChange={fetchPage}
      />
    </div>
  );
}
```

**Options:**

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `page` | `number` | `1` | Current page |
| `perPage` | `number` | `10` | Items per page |
| `orderBy` | `string` | `"date"` | Sort field |
| `order` | `string` | `"desc"` | Sort direction |
| `search` | `string` | `""` | Search term |

### usePdfViewer

Manage viewer state.

```tsx
import { usePdfViewer } from '@pdf-embed-seo/react';

function CustomViewer({ documentId }) {
  const { viewerState, setPage, setZoom, setTheme } = usePdfViewer(documentId);

  return (
    <div>
      <p>Page {viewerState.currentPage} of {viewerState.totalPages}</p>
      <button onClick={() => setPage(viewerState.currentPage + 1)}>Next</button>
      <button onClick={() => setZoom(1.5)}>Zoom 150%</button>
      <button onClick={() => setTheme('dark')}>Dark Mode</button>
    </div>
  );
}
```

### useProgress

Track reading progress (requires premium backend).

```tsx
import { useProgress } from '@pdf-embed-seo/react';

function ProgressTracker({ documentId }) {
  const { progress, saveProgress, loading } = useProgress(documentId);

  useEffect(() => {
    if (progress?.page) {
      console.log(`Resume from page ${progress.page}`);
    }
  }, [progress]);

  const handlePageChange = (page) => {
    saveProgress({ page, scroll: 0, zoom: 1 });
  };

  return <PdfViewer documentId={documentId} onPageChange={handlePageChange} />;
}
```

---

## Pro Components

### PdfPasswordModal

Password protection UI.

```tsx
import { PdfViewer } from '@pdf-embed-seo/react';
import { PdfPasswordModal } from '@pdf-embed-seo/react-premium';
import '@pdf-embed-seo/react-premium/styles';

<PdfViewer documentId={id}>
  <PdfPasswordModal
    onSuccess={() => console.log('Access granted')}
    onError={(error) => console.error(error)}
  />
</PdfViewer>
```

### PdfProgressBar

Show reading progress.

```tsx
import { PdfViewer } from '@pdf-embed-seo/react';
import { PdfProgressBar } from '@pdf-embed-seo/react-premium';

<PdfViewer documentId={id}>
  <PdfProgressBar position="top" showPercentage={true} />
</PdfViewer>
```

### PdfSearch

In-document text search.

```tsx
import { PdfViewer } from '@pdf-embed-seo/react';
import { PdfSearch } from '@pdf-embed-seo/react-premium';

<PdfViewer documentId={id}>
  <PdfSearch placeholder="Search in document..." />
</PdfViewer>
```

### PdfBookmarks

Bookmark navigation.

```tsx
import { PdfViewer } from '@pdf-embed-seo/react';
import { PdfBookmarks } from '@pdf-embed-seo/react-premium';

<PdfViewer documentId={id}>
  <PdfBookmarks collapsible={true} />
</PdfViewer>
```

### PdfAnalytics

Analytics dashboard component.

```tsx
import { PdfAnalytics } from '@pdf-embed-seo/react-premium';

<PdfAnalytics
  period="30days"
  showChart={true}
  showTable={true}
/>
```

---

## Pro Hooks

### useAnalytics

Track views and downloads.

```tsx
import { useAnalytics } from '@pdf-embed-seo/react-premium';

function AnalyticsTracker({ documentId }) {
  const { analytics, trackView, trackDownload } = useAnalytics(documentId);

  useEffect(() => {
    trackView();
  }, []);

  return (
    <button onClick={trackDownload}>
      Download ({analytics?.downloads || 0})
    </button>
  );
}
```

### usePassword

Password verification.

```tsx
import { usePassword } from '@pdf-embed-seo/react-premium';

function ProtectedPdf({ id }) {
  const { isProtected, isUnlocked, verify, error } = usePassword(id);

  if (isProtected && !isUnlocked) {
    return (
      <form onSubmit={(e) => {
        e.preventDefault();
        verify(e.target.password.value);
      }}>
        <input name="password" type="password" />
        {error && <p>{error}</p>}
        <button type="submit">Unlock</button>
      </form>
    );
  }

  return <PdfViewer documentId={id} />;
}
```

### useSearch

In-document text search.

```tsx
import { useSearch } from '@pdf-embed-seo/react-premium';

function SearchableViewer({ pdfDocument }) {
  const { results, search, clearResults, currentMatch, nextMatch, prevMatch } = useSearch(pdfDocument);

  return (
    <div>
      <input
        onChange={(e) => search(e.target.value)}
        placeholder="Search..."
      />
      <p>{results.length} results</p>
      <button onClick={prevMatch}>Prev</button>
      <button onClick={nextMatch}>Next</button>
    </div>
  );
}
```

### useBookmarks

PDF bookmark navigation.

```tsx
import { useBookmarks } from '@pdf-embed-seo/react-premium';

function BookmarkNav({ pdfDocument }) {
  const { bookmarks, goToBookmark } = useBookmarks(pdfDocument);

  return (
    <ul>
      {bookmarks.map((bookmark, i) => (
        <li key={i} onClick={() => goToBookmark(bookmark)}>
          {bookmark.title}
        </li>
      ))}
    </ul>
  );
}
```

---

## Next.js Integration

### App Router

```tsx
// app/pdf/[id]/page.tsx
import { PdfViewer, PdfSeo } from '@pdf-embed-seo/react/nextjs';

export default async function PdfPage({ params }) {
  return (
    <>
      <PdfSeo documentId={params.id} />
      <PdfViewer documentId={params.id} />
    </>
  );
}
```

### Pages Router

```tsx
// pages/pdf/[id].tsx
import { PdfViewer, usePdf } from '@pdf-embed-seo/react';

export default function PdfPage({ id }) {
  const { document, loading, error } = usePdf(id);

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error.message}</div>;

  return <PdfViewer documentId={id} />;
}

export async function getServerSideProps({ params }) {
  return { props: { id: params.id } };
}
```

---

## TypeScript Types

All types are exported from the core package:

```tsx
import type {
  PdfDocument,
  PdfDocumentInfo,
  ViewerOptions,
  ArchiveOptions,
  PdfProviderConfig,
  PaginationInfo,
  ProgressData,
  AnalyticsData,
} from '@pdf-embed-seo/react';
```

---

## Requirements

| Requirement | Version |
|-------------|---------|
| Node.js | 18+ |
| React | 18+ or 19+ |
| Next.js (optional) | 13, 14, or 15 |
| TypeScript (recommended) | 5+ |

### Backend Requirements

The React package is a **frontend library** that connects to a WordPress or Drupal backend with the PDF Embed SEO plugin installed.

| Backend | API Base URL |
|---------|--------------|
| WordPress | `/wp-json/pdf-embed-seo/v1/` |
| Drupal | `/api/pdf-embed-seo/v1/` |

---

## File Structure

```
react-pdf-embed-seo/
├── packages/
│   ├── core/                       # @pdf-embed-seo/core
│   │   └── src/
│   │       ├── types/              # TypeScript interfaces
│   │       ├── api/                # API client
│   │       └── utils/              # Utilities
│   │
│   ├── react/                      # @pdf-embed-seo/react
│   │   └── src/
│   │       ├── components/         # React components
│   │       ├── hooks/              # React hooks
│   │       ├── nextjs/             # Next.js exports
│   │       └── styles/             # CSS styles
│   │
│   └── react-premium/              # @pdf-embed-seo/react-premium (Pro)
│       └── src/
│           ├── components/         # Pro components
│           ├── hooks/              # Pro hooks
│           └── styles/             # Pro styles
│
└── apps/
    └── demo-nextjs/                # Demo application
```

---

## Downloads & Installation

### NPM

```bash
# Free packages
npm install @pdf-embed-seo/core @pdf-embed-seo/react

# Pro package
npm install @pdf-embed-seo/react-premium
```

### CDN (UMD)

```html
<!-- Core -->
<script src="https://unpkg.com/@pdf-embed-seo/core@1.2.11/dist/umd/index.js"></script>

<!-- React -->
<script src="https://unpkg.com/@pdf-embed-seo/react@1.2.11/dist/umd/index.js"></script>

<!-- Styles -->
<link rel="stylesheet" href="https://unpkg.com/@pdf-embed-seo/react@1.2.11/dist/styles.css">
```

---

## Related Documentation

- [Pro Features](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/PRO.md)
- [Complete Feature Matrix](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/FEATURES.md)
- [React/Next.js Guide](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/CLAUDE-REACT.md)
- [React Changelog](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/CHANGELOG-REACT.md)
- [Full Documentation](https://github.com/alexanderdross/PDF-Viewer-2026/blob/main/DOCUMENTATION.md)

---

*Made with love by [Dross:Media](https://dross.net/media/)*

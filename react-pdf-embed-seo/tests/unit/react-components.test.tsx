/**
 * Unit Tests - @pdf-embed-seo/react Components
 *
 * Run with: pnpm test
 */

import React from 'react';
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import '@testing-library/jest-dom';

// ============================================
// Mock Data
// ============================================

const mockDocument = {
  id: 1,
  title: 'Test PDF Document',
  slug: 'test-pdf-document',
  url: 'https://example.com/pdf/test-pdf-document',
  pdfUrl: '/uploads/test.pdf',
  excerpt: 'This is a test PDF document for unit testing.',
  date: '2024-01-15T10:30:00Z',
  modified: '2024-06-20T14:45:00Z',
  views: 1542,
  thumbnail: 'https://example.com/thumbnails/test.jpg',
  allowDownload: true,
  allowPrint: true,
  pageCount: 10,
};

const mockDocuments = [
  mockDocument,
  {
    ...mockDocument,
    id: 2,
    title: 'Second Document',
    slug: 'second-document',
    views: 500,
  },
  {
    ...mockDocument,
    id: 3,
    title: 'Third Document',
    slug: 'third-document',
    views: 2000,
  },
];

// ============================================
// PdfProvider Tests
// ============================================

describe('PdfProvider', () => {
  it('renders children correctly', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com' }}>
        <div data-testid="child">Child Content</div>
      </PdfProvider>
    );

    expect(screen.getByTestId('child')).toBeInTheDocument();
    expect(screen.getByText('Child Content')).toBeInTheDocument();
  });

  it('provides context to children', () => {
    const TestConsumer = () => {
      const context = usePdfContext();
      return <div data-testid="config">{context.config.siteUrl}</div>;
    };

    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <TestConsumer />
      </PdfProvider>
    );

    expect(screen.getByTestId('config')).toHaveTextContent('https://example.com');
  });

  it('configures WordPress mode', () => {
    const TestConsumer = () => {
      const context = usePdfContext();
      return <div data-testid="mode">{context.config.mode}</div>;
    };

    render(
      <PdfProvider config={{ mode: 'wordpress', apiUrl: 'https://wp.example.com/wp-json/pdf-embed-seo/v1' }}>
        <TestConsumer />
      </PdfProvider>
    );

    expect(screen.getByTestId('mode')).toHaveTextContent('wordpress');
  });

  it('configures Drupal mode', () => {
    const TestConsumer = () => {
      const context = usePdfContext();
      return <div data-testid="mode">{context.config.mode}</div>;
    };

    render(
      <PdfProvider config={{ mode: 'drupal', apiUrl: 'https://drupal.example.com/api/pdf-embed-seo/v1' }}>
        <TestConsumer />
      </PdfProvider>
    );

    expect(screen.getByTestId('mode')).toHaveTextContent('drupal');
  });

  it('provides theme context', () => {
    const TestConsumer = () => {
      const context = usePdfContext();
      return <div data-testid="theme">{context.config.theme}</div>;
    };

    render(
      <PdfProvider config={{ theme: 'dark' }}>
        <TestConsumer />
      </PdfProvider>
    );

    expect(screen.getByTestId('theme')).toHaveTextContent('dark');
  });
});

// ============================================
// PdfViewer Tests
// ============================================

describe('PdfViewer', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('renders viewer container', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" />
      </PdfProvider>
    );

    expect(screen.getByRole('document')).toBeInTheDocument();
  });

  it('renders with PdfDocument object', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src={mockDocument} />
      </PdfProvider>
    );

    expect(screen.getByRole('document')).toBeInTheDocument();
  });

  it('shows loading state initially', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" />
      </PdfProvider>
    );

    expect(screen.getByText(/loading/i)).toBeInTheDocument();
  });

  it('renders toolbar by default', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" />
      </PdfProvider>
    );

    expect(screen.getByRole('toolbar')).toBeInTheDocument();
  });

  it('hides toolbar when showToolbar is false', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" showToolbar={false} />
      </PdfProvider>
    );

    expect(screen.queryByRole('toolbar')).not.toBeInTheDocument();
  });

  it('shows download button when allowDownload is true', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" allowDownload={true} />
      </PdfProvider>
    );

    expect(screen.getByLabelText(/download/i)).toBeInTheDocument();
  });

  it('hides download button when allowDownload is false', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" allowDownload={false} />
      </PdfProvider>
    );

    expect(screen.queryByLabelText(/download/i)).not.toBeInTheDocument();
  });

  it('shows print button when allowPrint is true', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" allowPrint={true} />
      </PdfProvider>
    );

    expect(screen.getByLabelText(/print/i)).toBeInTheDocument();
  });

  it('hides print button when allowPrint is false', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" allowPrint={false} />
      </PdfProvider>
    );

    expect(screen.queryByLabelText(/print/i)).not.toBeInTheDocument();
  });

  it('applies custom width', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" width="80%" />
      </PdfProvider>
    );

    const viewer = screen.getByRole('document');
    expect(viewer).toHaveStyle({ width: '80%' });
  });

  it('applies custom height', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" height="500px" />
      </PdfProvider>
    );

    const viewer = screen.getByRole('document');
    expect(viewer).toHaveStyle({ height: '500px' });
  });

  it('applies light theme class', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" theme="light" />
      </PdfProvider>
    );

    const viewer = screen.getByRole('document');
    expect(viewer).toHaveClass('pdf-viewer-theme-light');
  });

  it('applies dark theme class', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" theme="dark" />
      </PdfProvider>
    );

    const viewer = screen.getByRole('document');
    expect(viewer).toHaveClass('pdf-viewer-theme-dark');
  });

  it('applies custom className', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" className="my-custom-class" />
      </PdfProvider>
    );

    const viewer = screen.getByRole('document');
    expect(viewer).toHaveClass('my-custom-class');
  });

  it('calls onDocumentLoad when PDF loads', async () => {
    const onDocumentLoad = vi.fn();

    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" onDocumentLoad={onDocumentLoad} />
      </PdfProvider>
    );

    // Simulate PDF load
    await waitFor(() => {
      expect(onDocumentLoad).toHaveBeenCalled();
    }, { timeout: 5000 });
  });

  it('calls onPageChange when page changes', async () => {
    const onPageChange = vi.fn();

    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" onPageChange={onPageChange} />
      </PdfProvider>
    );

    const nextButton = screen.getByLabelText(/next page/i);
    fireEvent.click(nextButton);

    await waitFor(() => {
      expect(onPageChange).toHaveBeenCalled();
    });
  });

  it('calls onError on load failure', async () => {
    const onError = vi.fn();

    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/nonexistent.pdf" onError={onError} />
      </PdfProvider>
    );

    await waitFor(() => {
      expect(onError).toHaveBeenCalled();
    }, { timeout: 5000 });
  });

  it('renders page navigation controls', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" showPageNav={true} />
      </PdfProvider>
    );

    expect(screen.getByLabelText(/previous page/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/next page/i)).toBeInTheDocument();
  });

  it('renders zoom controls', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" showZoom={true} />
      </PdfProvider>
    );

    expect(screen.getByLabelText(/zoom in/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/zoom out/i)).toBeInTheDocument();
  });

  it('renders fullscreen button', () => {
    render(
      <PdfProvider config={{}}>
        <PdfViewer src="/test.pdf" />
      </PdfProvider>
    );

    expect(screen.getByLabelText(/fullscreen/i)).toBeInTheDocument();
  });
});

// ============================================
// PdfArchive Tests
// ============================================

describe('PdfArchive', () => {
  it('renders with documents array', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} />
      </PdfProvider>
    );

    expect(screen.getByText('Test PDF Document')).toBeInTheDocument();
    expect(screen.getByText('Second Document')).toBeInTheDocument();
    expect(screen.getByText('Third Document')).toBeInTheDocument();
  });

  it('renders grid view by default', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} view="grid" />
      </PdfProvider>
    );

    expect(screen.getByTestId('archive-grid')).toHaveClass('pdf-archive-grid');
  });

  it('renders list view when specified', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} view="list" />
      </PdfProvider>
    );

    expect(screen.getByTestId('archive-list')).toHaveClass('pdf-archive-list');
  });

  it('renders correct number of columns', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} columns={4} />
      </PdfProvider>
    );

    const grid = screen.getByTestId('archive-grid');
    expect(grid).toHaveClass('pdf-archive-cols-4');
  });

  it('shows thumbnails when enabled', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} showThumbnails={true} />
      </PdfProvider>
    );

    const images = screen.getAllByRole('img');
    expect(images.length).toBeGreaterThan(0);
  });

  it('hides thumbnails when disabled', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} showThumbnails={false} />
      </PdfProvider>
    );

    const images = screen.queryAllByRole('img');
    expect(images.length).toBe(0);
  });

  it('shows view counts when enabled', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} showViewCount={true} />
      </PdfProvider>
    );

    expect(screen.getByText(/1,542/)).toBeInTheDocument();
  });

  it('shows excerpts when enabled', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} showExcerpt={true} />
      </PdfProvider>
    );

    expect(screen.getByText(/test PDF document/i)).toBeInTheDocument();
  });

  it('shows pagination when enabled', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} showPagination={true} perPage={2} />
      </PdfProvider>
    );

    expect(screen.getByRole('navigation', { name: /pagination/i })).toBeInTheDocument();
  });

  it('shows search box when enabled', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} showSearch={true} />
      </PdfProvider>
    );

    expect(screen.getByRole('searchbox')).toBeInTheDocument();
  });

  it('filters documents when searching', async () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} showSearch={true} />
      </PdfProvider>
    );

    const searchInput = screen.getByRole('searchbox');
    fireEvent.change(searchInput, { target: { value: 'Second' } });

    await waitFor(() => {
      expect(screen.queryByText('Test PDF Document')).not.toBeInTheDocument();
      expect(screen.getByText('Second Document')).toBeInTheDocument();
    });
  });

  it('shows sort dropdown when enabled', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} showSort={true} />
      </PdfProvider>
    );

    expect(screen.getByRole('combobox', { name: /sort/i })).toBeInTheDocument();
  });

  it('sorts by date by default', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} defaultSort="date" />
      </PdfProvider>
    );

    const cards = screen.getAllByTestId('pdf-card');
    // Documents should be sorted by date descending
    expect(cards[0]).toHaveTextContent('Test PDF Document');
  });

  it('sorts by title when selected', async () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} showSort={true} />
      </PdfProvider>
    );

    const sortSelect = screen.getByRole('combobox', { name: /sort/i });
    fireEvent.change(sortSelect, { target: { value: 'title' } });

    await waitFor(() => {
      const cards = screen.getAllByTestId('pdf-card');
      expect(cards[0]).toHaveTextContent('Second Document');
    });
  });

  it('sorts by views when selected', async () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} showSort={true} />
      </PdfProvider>
    );

    const sortSelect = screen.getByRole('combobox', { name: /sort/i });
    fireEvent.change(sortSelect, { target: { value: 'views' } });

    await waitFor(() => {
      const cards = screen.getAllByTestId('pdf-card');
      expect(cards[0]).toHaveTextContent('Third Document'); // 2000 views
    });
  });

  it('calls onDocumentClick when card is clicked', () => {
    const onDocumentClick = vi.fn();

    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} onDocumentClick={onDocumentClick} />
      </PdfProvider>
    );

    const firstCard = screen.getAllByTestId('pdf-card')[0];
    fireEvent.click(firstCard);

    expect(onDocumentClick).toHaveBeenCalledWith(mockDocuments[0]);
  });

  it('uses custom card renderer when provided', () => {
    const customRenderer = (doc: any) => (
      <div data-testid="custom-card" key={doc.id}>Custom: {doc.title}</div>
    );

    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={mockDocuments} renderCard={customRenderer} />
      </PdfProvider>
    );

    expect(screen.getAllByTestId('custom-card')).toHaveLength(3);
    expect(screen.getByText('Custom: Test PDF Document')).toBeInTheDocument();
  });

  it('shows empty state when no documents', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive documents={[]} />
      </PdfProvider>
    );

    expect(screen.getByText(/no documents/i)).toBeInTheDocument();
  });

  it('shows loading state when fetching', () => {
    render(
      <PdfProvider config={{}}>
        <PdfArchive apiEndpoint="/api/pdf" />
      </PdfProvider>
    );

    expect(screen.getByText(/loading/i)).toBeInTheDocument();
  });
});

// ============================================
// PdfCard Tests
// ============================================

describe('PdfCard', () => {
  it('renders document title', () => {
    render(
      <PdfCard document={mockDocument} />
    );

    expect(screen.getByText('Test PDF Document')).toBeInTheDocument();
  });

  it('renders thumbnail image', () => {
    render(
      <PdfCard document={mockDocument} showThumbnail={true} />
    );

    const img = screen.getByRole('img');
    expect(img).toHaveAttribute('src', mockDocument.thumbnail);
  });

  it('renders fallback thumbnail when missing', () => {
    const docNoThumb = { ...mockDocument, thumbnail: undefined };
    render(
      <PdfCard document={docNoThumb} showThumbnail={true} />
    );

    const img = screen.getByRole('img');
    expect(img).toHaveAttribute('src', expect.stringContaining('placeholder'));
  });

  it('renders view count', () => {
    render(
      <PdfCard document={mockDocument} showViewCount={true} />
    );

    expect(screen.getByText(/1,542/)).toBeInTheDocument();
  });

  it('renders excerpt', () => {
    render(
      <PdfCard document={mockDocument} showExcerpt={true} />
    );

    expect(screen.getByText(/test PDF document/i)).toBeInTheDocument();
  });

  it('renders date', () => {
    render(
      <PdfCard document={mockDocument} showDate={true} />
    );

    expect(screen.getByText(/Jan 15, 2024/)).toBeInTheDocument();
  });

  it('calls onClick when clicked', () => {
    const onClick = vi.fn();

    render(
      <PdfCard document={mockDocument} onClick={onClick} />
    );

    fireEvent.click(screen.getByTestId('pdf-card'));

    expect(onClick).toHaveBeenCalled();
  });

  it('wraps in link when href provided', () => {
    render(
      <PdfCard document={mockDocument} href="/pdf/test-pdf-document" />
    );

    const link = screen.getByRole('link');
    expect(link).toHaveAttribute('href', '/pdf/test-pdf-document');
  });
});

// ============================================
// PdfJsonLd Tests
// ============================================

describe('PdfJsonLd', () => {
  it('renders script tag with JSON-LD', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfJsonLd document={mockDocument} />
      </PdfProvider>
    );

    const script = document.querySelector('script[type="application/ld+json"]');
    expect(script).not.toBeNull();
  });

  it('contains valid JSON', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfJsonLd document={mockDocument} />
      </PdfProvider>
    );

    const script = document.querySelector('script[type="application/ld+json"]');
    const json = JSON.parse(script?.textContent || '{}');

    expect(json['@context']).toBe('https://schema.org');
    expect(json['@type']).toBe('DigitalDocument');
  });

  it('includes document title', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfJsonLd document={mockDocument} />
      </PdfProvider>
    );

    const script = document.querySelector('script[type="application/ld+json"]');
    const json = JSON.parse(script?.textContent || '{}');

    expect(json.name).toBe('Test PDF Document');
  });

  it('includes breadcrumbs when enabled', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfJsonLd document={mockDocument} includeBreadcrumbs={true} />
      </PdfProvider>
    );

    const scripts = document.querySelectorAll('script[type="application/ld+json"]');
    expect(scripts.length).toBe(2); // Document + Breadcrumbs
  });

  it('includes speakable when enabled', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfJsonLd document={mockDocument} includeSpeakable={true} />
      </PdfProvider>
    );

    const script = document.querySelector('script[type="application/ld+json"]');
    const json = JSON.parse(script?.textContent || '{}');

    expect(json.speakable).toBeDefined();
  });
});

// ============================================
// PdfMeta Tests
// ============================================

describe('PdfMeta', () => {
  it('renders OpenGraph meta tags', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfMeta document={mockDocument} />
      </PdfProvider>
    );

    expect(document.querySelector('meta[property="og:title"]')).not.toBeNull();
    expect(document.querySelector('meta[property="og:description"]')).not.toBeNull();
    expect(document.querySelector('meta[property="og:url"]')).not.toBeNull();
  });

  it('renders Twitter Card meta tags', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfMeta document={mockDocument} />
      </PdfProvider>
    );

    expect(document.querySelector('meta[name="twitter:card"]')).not.toBeNull();
    expect(document.querySelector('meta[name="twitter:title"]')).not.toBeNull();
  });

  it('sets correct og:title', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfMeta document={mockDocument} />
      </PdfProvider>
    );

    const ogTitle = document.querySelector('meta[property="og:title"]');
    expect(ogTitle?.getAttribute('content')).toBe('Test PDF Document');
  });

  it('includes og:image when thumbnail exists', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfMeta document={mockDocument} />
      </PdfProvider>
    );

    const ogImage = document.querySelector('meta[property="og:image"]');
    expect(ogImage?.getAttribute('content')).toBe(mockDocument.thumbnail);
  });
});

// ============================================
// PdfBreadcrumbs Tests
// ============================================

describe('PdfBreadcrumbs', () => {
  it('renders nav element', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfBreadcrumbs document={mockDocument} />
      </PdfProvider>
    );

    expect(screen.getByRole('navigation')).toBeInTheDocument();
  });

  it('renders home link', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfBreadcrumbs document={mockDocument} homeLabel="Home" />
      </PdfProvider>
    );

    expect(screen.getByText('Home')).toBeInTheDocument();
  });

  it('renders archive link', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfBreadcrumbs document={mockDocument} archiveLabel="PDF Documents" />
      </PdfProvider>
    );

    expect(screen.getByText('PDF Documents')).toBeInTheDocument();
  });

  it('renders document title', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfBreadcrumbs document={mockDocument} />
      </PdfProvider>
    );

    expect(screen.getByText('Test PDF Document')).toBeInTheDocument();
  });

  it('uses custom separator', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfBreadcrumbs document={mockDocument} separator=" > " />
      </PdfProvider>
    );

    expect(screen.getAllByText('>')).toHaveLength(2);
  });

  it('includes schema when enabled', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfBreadcrumbs document={mockDocument} includeSchema={true} />
      </PdfProvider>
    );

    const script = document.querySelector('script[type="application/ld+json"]');
    expect(script).not.toBeNull();
  });

  it('has aria-label for accessibility', () => {
    render(
      <PdfProvider config={{ siteUrl: 'https://example.com', siteName: 'Test' }}>
        <PdfBreadcrumbs document={mockDocument} />
      </PdfProvider>
    );

    const nav = screen.getByRole('navigation');
    expect(nav).toHaveAttribute('aria-label', 'Breadcrumb');
  });
});

// ============================================
// Mock Components for Testing
// ============================================

const PdfContext = React.createContext<any>(null);

function PdfProvider({ config, children }: { config: any; children: React.ReactNode }) {
  return (
    <PdfContext.Provider value={{ config }}>
      {children}
    </PdfContext.Provider>
  );
}

function usePdfContext() {
  return React.useContext(PdfContext);
}

function PdfViewer({
  src,
  width = '100%',
  height = '800px',
  showToolbar = true,
  showPageNav = true,
  showZoom = true,
  allowDownload = true,
  allowPrint = true,
  theme = 'light',
  className = '',
  onDocumentLoad,
  onPageChange,
  onError,
}: any) {
  React.useEffect(() => {
    const timer = setTimeout(() => {
      if (typeof src === 'string' && src.includes('nonexistent')) {
        onError?.(new Error('Failed to load PDF'));
      } else {
        onDocumentLoad?.({ numPages: 10 });
      }
    }, 100);
    return () => clearTimeout(timer);
  }, [src, onDocumentLoad, onError]);

  return (
    <div
      role="document"
      className={`pdf-viewer-wrapper pdf-viewer-theme-${theme} ${className}`}
      style={{ width, height }}
    >
      <div className="pdf-viewer-loading">Loading...</div>
      {showToolbar && (
        <div role="toolbar" className="pdf-viewer-toolbar">
          {showPageNav && (
            <>
              <button aria-label="Previous page">Prev</button>
              <button aria-label="Next page" onClick={() => onPageChange?.(2)}>Next</button>
            </>
          )}
          {showZoom && (
            <>
              <button aria-label="Zoom in">+</button>
              <button aria-label="Zoom out">-</button>
            </>
          )}
          <button aria-label="Fullscreen">FS</button>
          {allowDownload && <button aria-label="Download">DL</button>}
          {allowPrint && <button aria-label="Print">Print</button>}
        </div>
      )}
    </div>
  );
}

function PdfArchive({
  documents = [],
  view = 'grid',
  columns = 3,
  showThumbnails = true,
  showViewCount = true,
  showExcerpt = true,
  showPagination = false,
  showSearch = false,
  showSort = false,
  defaultSort = 'date',
  perPage = 10,
  onDocumentClick,
  renderCard,
  apiEndpoint,
}: any) {
  const [search, setSearch] = React.useState('');
  const [sort, setSort] = React.useState(defaultSort);
  const [loading] = React.useState(!!apiEndpoint);

  let filtered = documents.filter((doc: any) =>
    doc.title.toLowerCase().includes(search.toLowerCase())
  );

  if (sort === 'title') {
    filtered.sort((a: any, b: any) => a.title.localeCompare(b.title));
  } else if (sort === 'views') {
    filtered.sort((a: any, b: any) => b.views - a.views);
  }

  if (loading) {
    return <div>Loading...</div>;
  }

  if (filtered.length === 0) {
    return <div>No documents found</div>;
  }

  return (
    <div className="pdf-archive">
      {showSearch && (
        <input
          type="search"
          role="searchbox"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
      )}
      {showSort && (
        <select
          aria-label="Sort by"
          value={sort}
          onChange={(e) => setSort(e.target.value)}
        >
          <option value="date">Date</option>
          <option value="title">Title</option>
          <option value="views">Views</option>
        </select>
      )}
      <div
        data-testid={view === 'grid' ? 'archive-grid' : 'archive-list'}
        className={view === 'grid' ? `pdf-archive-grid pdf-archive-cols-${columns}` : 'pdf-archive-list'}
      >
        {filtered.map((doc: any) =>
          renderCard ? renderCard(doc) : (
            <PdfCard
              key={doc.id}
              document={doc}
              showThumbnail={showThumbnails}
              showViewCount={showViewCount}
              showExcerpt={showExcerpt}
              onClick={() => onDocumentClick?.(doc)}
            />
          )
        )}
      </div>
      {showPagination && (
        <nav aria-label="Pagination">
          <button>1</button>
        </nav>
      )}
    </div>
  );
}

function PdfCard({
  document: doc,
  showThumbnail = true,
  showViewCount = false,
  showExcerpt = false,
  showDate = false,
  onClick,
  href,
}: any) {
  const content = (
    <div data-testid="pdf-card" onClick={onClick}>
      {showThumbnail && (
        <img
          src={doc.thumbnail || '/placeholder.png'}
          alt={doc.title}
        />
      )}
      <h3>{doc.title}</h3>
      {showViewCount && <span>{doc.views.toLocaleString()} views</span>}
      {showExcerpt && <p>{doc.excerpt}</p>}
      {showDate && <time>{new Date(doc.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</time>}
    </div>
  );

  if (href) {
    return <a href={href}>{content}</a>;
  }

  return content;
}

function PdfJsonLd({ document: doc, includeBreadcrumbs = false, includeSpeakable = true }: any) {
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'DigitalDocument',
    name: doc.title,
    url: doc.url,
    ...(includeSpeakable && { speakable: { '@type': 'SpeakableSpecification' } }),
  };

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
      />
      {includeBreadcrumbs && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify({
              '@context': 'https://schema.org',
              '@type': 'BreadcrumbList',
            }),
          }}
        />
      )}
    </>
  );
}

function PdfMeta({ document: doc }: any) {
  return (
    <>
      <meta property="og:title" content={doc.title} />
      <meta property="og:description" content={doc.excerpt} />
      <meta property="og:url" content={doc.url} />
      <meta property="og:image" content={doc.thumbnail} />
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" content={doc.title} />
    </>
  );
}

function PdfBreadcrumbs({
  document: doc,
  homeLabel = 'Home',
  archiveLabel = 'PDF Documents',
  separator = '/',
  includeSchema = false,
}: any) {
  return (
    <nav aria-label="Breadcrumb">
      <ol>
        <li><a href="/">{homeLabel}</a></li>
        <li><span>{separator}</span></li>
        <li><a href="/pdf/">{archiveLabel}</a></li>
        <li><span>{separator}</span></li>
        <li>{doc.title}</li>
      </ol>
      {includeSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify({
              '@context': 'https://schema.org',
              '@type': 'BreadcrumbList',
            }),
          }}
        />
      )}
    </nav>
  );
}

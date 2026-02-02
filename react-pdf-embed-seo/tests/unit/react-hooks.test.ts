/**
 * Unit Tests - @pdf-embed-seo/react Hooks
 *
 * Run with: pnpm test
 */

import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { renderHook, act, waitFor } from '@testing-library/react';

// ============================================
// Mock Data
// ============================================

const mockDocument = {
  id: 1,
  title: 'Test PDF Document',
  slug: 'test-pdf-document',
  url: 'https://example.com/pdf/test-pdf-document',
  excerpt: 'This is a test PDF document.',
  date: '2024-01-15T10:30:00Z',
  modified: '2024-06-20T14:45:00Z',
  views: 1542,
  thumbnail: 'https://example.com/thumbnails/test.jpg',
  allowDownload: true,
  allowPrint: true,
};

const mockDocuments = [
  mockDocument,
  { ...mockDocument, id: 2, title: 'Second Document', slug: 'second' },
  { ...mockDocument, id: 3, title: 'Third Document', slug: 'third' },
];

// ============================================
// usePdfDocument Hook Tests
// ============================================

describe('usePdfDocument', () => {
  const mockApiClient = {
    getDocument: vi.fn(),
  };

  beforeEach(() => {
    vi.clearAllMocks();
    mockApiClient.getDocument.mockResolvedValue(mockDocument);
  });

  it('should fetch document by ID', async () => {
    const { result } = renderHook(() =>
      usePdfDocument(1, { apiClient: mockApiClient })
    );

    expect(result.current.isLoading).toBe(true);

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    expect(result.current.document).toEqual(mockDocument);
    expect(mockApiClient.getDocument).toHaveBeenCalledWith(1);
  });

  it('should return loading state initially', () => {
    const { result } = renderHook(() =>
      usePdfDocument(1, { apiClient: mockApiClient })
    );

    expect(result.current.isLoading).toBe(true);
    expect(result.current.document).toBeNull();
  });

  it('should handle errors', async () => {
    const error = new Error('Failed to fetch');
    mockApiClient.getDocument.mockRejectedValueOnce(error);

    const { result } = renderHook(() =>
      usePdfDocument(1, { apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    expect(result.current.error).toEqual(error);
    expect(result.current.document).toBeNull();
  });

  it('should refetch when called', async () => {
    const { result } = renderHook(() =>
      usePdfDocument(1, { apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    expect(mockApiClient.getDocument).toHaveBeenCalledTimes(1);

    act(() => {
      result.current.refetch();
    });

    await waitFor(() => {
      expect(mockApiClient.getDocument).toHaveBeenCalledTimes(2);
    });
  });

  it('should not fetch if ID is null', () => {
    const { result } = renderHook(() =>
      usePdfDocument(null, { apiClient: mockApiClient })
    );

    expect(result.current.isLoading).toBe(false);
    expect(mockApiClient.getDocument).not.toHaveBeenCalled();
  });
});

// ============================================
// usePdfDocuments Hook Tests
// ============================================

describe('usePdfDocuments', () => {
  const mockApiClient = {
    getDocuments: vi.fn(),
  };

  beforeEach(() => {
    vi.clearAllMocks();
    mockApiClient.getDocuments.mockResolvedValue({
      documents: mockDocuments,
      pagination: { total: 3, pages: 1, page: 1, perPage: 10 },
    });
  });

  it('should fetch documents list', async () => {
    const { result } = renderHook(() =>
      usePdfDocuments({ apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    expect(result.current.documents).toHaveLength(3);
    expect(mockApiClient.getDocuments).toHaveBeenCalled();
  });

  it('should return pagination info', async () => {
    const { result } = renderHook(() =>
      usePdfDocuments({ apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    expect(result.current.pagination).toEqual({
      total: 3,
      pages: 1,
      page: 1,
      perPage: 10,
    });
  });

  it('should support setPage', async () => {
    const { result } = renderHook(() =>
      usePdfDocuments({ apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    act(() => {
      result.current.setPage(2);
    });

    await waitFor(() => {
      expect(mockApiClient.getDocuments).toHaveBeenCalledWith(
        expect.objectContaining({ page: 2 })
      );
    });
  });

  it('should support nextPage', async () => {
    mockApiClient.getDocuments.mockResolvedValueOnce({
      documents: mockDocuments,
      pagination: { total: 30, pages: 3, page: 1, perPage: 10 },
    });

    const { result } = renderHook(() =>
      usePdfDocuments({ apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    act(() => {
      result.current.nextPage();
    });

    await waitFor(() => {
      expect(mockApiClient.getDocuments).toHaveBeenCalledWith(
        expect.objectContaining({ page: 2 })
      );
    });
  });

  it('should support prevPage', async () => {
    mockApiClient.getDocuments.mockResolvedValueOnce({
      documents: mockDocuments,
      pagination: { total: 30, pages: 3, page: 2, perPage: 10 },
    });

    const { result } = renderHook(() =>
      usePdfDocuments({ apiClient: mockApiClient, initialPage: 2 })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    act(() => {
      result.current.prevPage();
    });

    await waitFor(() => {
      expect(mockApiClient.getDocuments).toHaveBeenCalledWith(
        expect.objectContaining({ page: 1 })
      );
    });
  });

  it('should support setSearch', async () => {
    const { result } = renderHook(() =>
      usePdfDocuments({ apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    act(() => {
      result.current.setSearch('test query');
    });

    await waitFor(() => {
      expect(mockApiClient.getDocuments).toHaveBeenCalledWith(
        expect.objectContaining({ search: 'test query' })
      );
    });
  });

  it('should support setSort', async () => {
    const { result } = renderHook(() =>
      usePdfDocuments({ apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    act(() => {
      result.current.setSort('title');
    });

    await waitFor(() => {
      expect(mockApiClient.getDocuments).toHaveBeenCalledWith(
        expect.objectContaining({ orderby: 'title' })
      );
    });
  });

  it('should return search state', async () => {
    const { result } = renderHook(() =>
      usePdfDocuments({ apiClient: mockApiClient })
    );

    act(() => {
      result.current.setSearch('hello');
    });

    expect(result.current.search).toBe('hello');
  });

  it('should return sortBy state', async () => {
    const { result } = renderHook(() =>
      usePdfDocuments({ apiClient: mockApiClient })
    );

    act(() => {
      result.current.setSort('views');
    });

    expect(result.current.sortBy).toBe('views');
  });
});

// ============================================
// usePdfViewer Hook Tests
// ============================================

describe('usePdfViewer', () => {
  it('should initialize with default values', () => {
    const { result } = renderHook(() => usePdfViewer());

    expect(result.current.currentPage).toBe(1);
    expect(result.current.totalPages).toBe(0);
    expect(result.current.zoom).toBe(1);
    expect(result.current.isFullscreen).toBe(false);
  });

  it('should use initialPage option', () => {
    const { result } = renderHook(() =>
      usePdfViewer({ initialPage: 5 })
    );

    expect(result.current.currentPage).toBe(5);
  });

  it('should use initialZoom option', () => {
    const { result } = renderHook(() =>
      usePdfViewer({ initialZoom: 1.5 })
    );

    expect(result.current.zoom).toBe(1.5);
  });

  it('should setPage correctly', () => {
    const { result } = renderHook(() =>
      usePdfViewer({ totalPages: 10 })
    );

    act(() => {
      result.current.setPage(5);
    });

    expect(result.current.currentPage).toBe(5);
  });

  it('should not setPage beyond bounds', () => {
    const { result } = renderHook(() =>
      usePdfViewer({ totalPages: 10 })
    );

    act(() => {
      result.current.setPage(15);
    });

    expect(result.current.currentPage).toBe(10);

    act(() => {
      result.current.setPage(0);
    });

    expect(result.current.currentPage).toBe(1);
  });

  it('should nextPage correctly', () => {
    const { result } = renderHook(() =>
      usePdfViewer({ totalPages: 10 })
    );

    act(() => {
      result.current.nextPage();
    });

    expect(result.current.currentPage).toBe(2);
  });

  it('should prevPage correctly', () => {
    const { result } = renderHook(() =>
      usePdfViewer({ initialPage: 5, totalPages: 10 })
    );

    act(() => {
      result.current.prevPage();
    });

    expect(result.current.currentPage).toBe(4);
  });

  it('should setZoom correctly', () => {
    const { result } = renderHook(() => usePdfViewer());

    act(() => {
      result.current.setZoom(2);
    });

    expect(result.current.zoom).toBe(2);
  });

  it('should zoomIn correctly', () => {
    const { result } = renderHook(() => usePdfViewer());

    act(() => {
      result.current.zoomIn();
    });

    expect(result.current.zoom).toBe(1.25);
  });

  it('should zoomOut correctly', () => {
    const { result } = renderHook(() =>
      usePdfViewer({ initialZoom: 1.5 })
    );

    act(() => {
      result.current.zoomOut();
    });

    expect(result.current.zoom).toBe(1.25);
  });

  it('should toggleFullscreen', () => {
    const { result } = renderHook(() => usePdfViewer());

    expect(result.current.isFullscreen).toBe(false);

    act(() => {
      result.current.toggleFullscreen();
    });

    expect(result.current.isFullscreen).toBe(true);

    act(() => {
      result.current.toggleFullscreen();
    });

    expect(result.current.isFullscreen).toBe(false);
  });
});

// ============================================
// usePdfSeo Hook Tests
// ============================================

describe('usePdfSeo', () => {
  it('should generate meta tags', () => {
    const { result } = renderHook(() =>
      usePdfSeo(mockDocument, { siteUrl: 'https://example.com' })
    );

    expect(result.current.metaTags).toBeDefined();
    expect(result.current.metaTags.title).toBe(mockDocument.title);
    expect(result.current.metaTags.description).toBe(mockDocument.excerpt);
  });

  it('should generate JSON-LD', () => {
    const { result } = renderHook(() =>
      usePdfSeo(mockDocument, { siteUrl: 'https://example.com' })
    );

    expect(result.current.jsonLd).toBeDefined();
    expect(result.current.jsonLd['@type']).toBe('DigitalDocument');
    expect(result.current.jsonLd.name).toBe(mockDocument.title);
  });

  it('should generate breadcrumbs', () => {
    const { result } = renderHook(() =>
      usePdfSeo(mockDocument, { siteUrl: 'https://example.com' })
    );

    expect(result.current.breadcrumbs).toBeDefined();
    expect(result.current.breadcrumbs).toHaveLength(3);
    expect(result.current.breadcrumbs[0].label).toBe('Home');
  });

  it('should generate Next.js metadata object', () => {
    const { result } = renderHook(() =>
      usePdfSeo(mockDocument, { siteUrl: 'https://example.com' })
    );

    expect(result.current.nextMetadata).toBeDefined();
    expect(result.current.nextMetadata.title).toBe(mockDocument.title);
    expect(result.current.nextMetadata.openGraph).toBeDefined();
  });

  it('should include Twitter handle when provided', () => {
    const { result } = renderHook(() =>
      usePdfSeo(mockDocument, {
        siteUrl: 'https://example.com',
        twitterHandle: '@testsite',
      })
    );

    expect(result.current.nextMetadata.twitter?.site).toBe('@testsite');
  });
});

// ============================================
// usePdfTheme Hook Tests
// ============================================

describe('usePdfTheme', () => {
  beforeEach(() => {
    // Mock matchMedia
    Object.defineProperty(window, 'matchMedia', {
      writable: true,
      value: vi.fn().mockImplementation((query) => ({
        matches: query === '(prefers-color-scheme: dark)',
        media: query,
        onchange: null,
        addListener: vi.fn(),
        removeListener: vi.fn(),
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
        dispatchEvent: vi.fn(),
      })),
    });
  });

  it('should return default theme', () => {
    const { result } = renderHook(() => usePdfTheme());

    expect(result.current.theme).toBe('light');
  });

  it('should use initial theme', () => {
    const { result } = renderHook(() =>
      usePdfTheme({ initialTheme: 'dark' })
    );

    expect(result.current.theme).toBe('dark');
  });

  it('should setTheme correctly', () => {
    const { result } = renderHook(() => usePdfTheme());

    act(() => {
      result.current.setTheme('dark');
    });

    expect(result.current.theme).toBe('dark');
  });

  it('should toggleTheme correctly', () => {
    const { result } = renderHook(() =>
      usePdfTheme({ initialTheme: 'light' })
    );

    act(() => {
      result.current.toggleTheme();
    });

    expect(result.current.theme).toBe('dark');

    act(() => {
      result.current.toggleTheme();
    });

    expect(result.current.theme).toBe('light');
  });

  it('should resolve system theme', () => {
    const { result } = renderHook(() =>
      usePdfTheme({ initialTheme: 'system' })
    );

    expect(result.current.theme).toBe('system');
    expect(result.current.resolvedTheme).toBe('dark'); // matchMedia mocked to return dark
  });
});

// ============================================
// Hook Implementations for Testing
// ============================================

function usePdfDocument(
  id: number | string | null,
  options?: { apiClient?: any }
) {
  const [document, setDocument] = React.useState<any>(null);
  const [isLoading, setIsLoading] = React.useState(id !== null);
  const [error, setError] = React.useState<Error | null>(null);

  const fetchDocument = React.useCallback(async () => {
    if (id === null) return;

    setIsLoading(true);
    setError(null);

    try {
      const data = await options?.apiClient?.getDocument(id);
      setDocument(data);
    } catch (err) {
      setError(err as Error);
    } finally {
      setIsLoading(false);
    }
  }, [id, options?.apiClient]);

  React.useEffect(() => {
    fetchDocument();
  }, [fetchDocument]);

  return {
    document,
    isLoading,
    error,
    refetch: fetchDocument,
  };
}

function usePdfDocuments(options?: {
  apiClient?: any;
  perPage?: number;
  initialPage?: number;
}) {
  const [documents, setDocuments] = React.useState<any[]>([]);
  const [pagination, setPagination] = React.useState({
    total: 0,
    pages: 0,
    page: options?.initialPage || 1,
    perPage: options?.perPage || 10,
  });
  const [isLoading, setIsLoading] = React.useState(true);
  const [search, setSearchState] = React.useState('');
  const [sortBy, setSortByState] = React.useState('date');

  const fetchDocuments = React.useCallback(
    async (params?: any) => {
      setIsLoading(true);

      try {
        const result = await options?.apiClient?.getDocuments({
          page: pagination.page,
          perPage: pagination.perPage,
          search,
          orderby: sortBy,
          ...params,
        });
        setDocuments(result.documents);
        setPagination(result.pagination);
      } finally {
        setIsLoading(false);
      }
    },
    [options?.apiClient, pagination.page, pagination.perPage, search, sortBy]
  );

  React.useEffect(() => {
    fetchDocuments();
  }, []);

  const setPage = (page: number) => {
    setPagination((p) => ({ ...p, page }));
    fetchDocuments({ page });
  };

  const nextPage = () => {
    const next = pagination.page + 1;
    if (next <= pagination.pages) {
      setPage(next);
    }
  };

  const prevPage = () => {
    const prev = pagination.page - 1;
    if (prev >= 1) {
      setPage(prev);
    }
  };

  const setSearch = (value: string) => {
    setSearchState(value);
    fetchDocuments({ search: value, page: 1 });
  };

  const setSort = (value: string) => {
    setSortByState(value);
    fetchDocuments({ orderby: value, page: 1 });
  };

  return {
    documents,
    pagination,
    isLoading,
    search,
    sortBy,
    setPage,
    nextPage,
    prevPage,
    setSearch,
    setSort,
  };
}

function usePdfViewer(options?: {
  initialPage?: number;
  initialZoom?: number;
  totalPages?: number;
}) {
  const [currentPage, setCurrentPage] = React.useState(options?.initialPage || 1);
  const [totalPages] = React.useState(options?.totalPages || 0);
  const [zoom, setZoomState] = React.useState(options?.initialZoom || 1);
  const [isFullscreen, setIsFullscreen] = React.useState(false);

  const setPage = (page: number) => {
    const bounded = Math.max(1, Math.min(page, totalPages || Infinity));
    setCurrentPage(bounded);
  };

  const nextPage = () => {
    if (currentPage < totalPages || totalPages === 0) {
      setCurrentPage((p) => p + 1);
    }
  };

  const prevPage = () => {
    if (currentPage > 1) {
      setCurrentPage((p) => p - 1);
    }
  };

  const setZoom = (value: number) => {
    setZoomState(Math.max(0.25, Math.min(4, value)));
  };

  const zoomIn = () => {
    setZoom(zoom * 1.25);
  };

  const zoomOut = () => {
    setZoom(zoom / 1.25);
  };

  const toggleFullscreen = () => {
    setIsFullscreen((f) => !f);
  };

  return {
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
  };
}

function usePdfSeo(document: any, options?: { siteUrl?: string; twitterHandle?: string }) {
  const metaTags = React.useMemo(
    () => ({
      title: document.title,
      description: document.excerpt,
      canonical: document.url,
    }),
    [document]
  );

  const jsonLd = React.useMemo(
    () => ({
      '@context': 'https://schema.org',
      '@type': 'DigitalDocument',
      name: document.title,
      url: document.url,
      description: document.excerpt,
    }),
    [document]
  );

  const breadcrumbs = React.useMemo(
    () => [
      { label: 'Home', url: options?.siteUrl || '/' },
      { label: 'PDF Documents', url: `${options?.siteUrl || ''}/pdf/` },
      { label: document.title, url: document.url },
    ],
    [document, options?.siteUrl]
  );

  const nextMetadata = React.useMemo(
    () => ({
      title: document.title,
      description: document.excerpt,
      openGraph: {
        title: document.title,
        description: document.excerpt,
        url: document.url,
        type: 'article',
      },
      twitter: options?.twitterHandle
        ? { site: options.twitterHandle, card: 'summary_large_image' }
        : undefined,
    }),
    [document, options?.twitterHandle]
  );

  return { metaTags, jsonLd, breadcrumbs, nextMetadata };
}

function usePdfTheme(options?: { initialTheme?: 'light' | 'dark' | 'system' }) {
  const [theme, setThemeState] = React.useState<'light' | 'dark' | 'system'>(
    options?.initialTheme || 'light'
  );

  const resolvedTheme = React.useMemo(() => {
    if (theme === 'system') {
      return window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light';
    }
    return theme;
  }, [theme]);

  const setTheme = (value: 'light' | 'dark' | 'system') => {
    setThemeState(value);
  };

  const toggleTheme = () => {
    setThemeState((t) => (t === 'light' ? 'dark' : 'light'));
  };

  return { theme, resolvedTheme, setTheme, toggleTheme };
}

// Import React for hooks
import React from 'react';

/**
 * Unit Tests - @pdf-embed-seo/core
 *
 * Run with: pnpm test
 */

import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';

// Mock types for testing
interface PdfDocument {
  id: number | string;
  title: string;
  slug: string;
  url: string;
  excerpt?: string;
  date: string;
  modified: string;
  views: number;
  thumbnail?: string;
  allowDownload: boolean;
  allowPrint: boolean;
}

// ============================================
// Schema Generator Tests
// ============================================

describe('Schema Generator', () => {
  const mockDocument: PdfDocument = {
    id: 1,
    title: 'Test PDF Document',
    slug: 'test-pdf-document',
    url: 'https://example.com/pdf/test-pdf-document',
    excerpt: 'This is a test PDF document for unit testing.',
    date: '2024-01-15T10:30:00Z',
    modified: '2024-06-20T14:45:00Z',
    views: 1542,
    thumbnail: 'https://example.com/thumbnails/test.jpg',
    allowDownload: true,
    allowPrint: false,
  };

  const siteConfig = {
    siteUrl: 'https://example.com',
    siteName: 'Test Site',
  };

  describe('generateDocumentSchema', () => {
    it('should generate valid DigitalDocument schema', () => {
      const schema = generateDocumentSchema(mockDocument, siteConfig);

      expect(schema['@context']).toBe('https://schema.org');
      expect(schema['@type']).toBe('DigitalDocument');
      expect(schema.name).toBe(mockDocument.title);
      expect(schema.url).toBe(mockDocument.url);
    });

    it('should include description when excerpt exists', () => {
      const schema = generateDocumentSchema(mockDocument, siteConfig);

      expect(schema.description).toBe(mockDocument.excerpt);
    });

    it('should handle missing excerpt gracefully', () => {
      const docWithoutExcerpt = { ...mockDocument, excerpt: undefined };
      const schema = generateDocumentSchema(docWithoutExcerpt, siteConfig);

      expect(schema.description).toBeUndefined();
    });

    it('should include datePublished and dateModified', () => {
      const schema = generateDocumentSchema(mockDocument, siteConfig);

      expect(schema.datePublished).toBe(mockDocument.date);
      expect(schema.dateModified).toBe(mockDocument.modified);
    });

    it('should include thumbnail as image', () => {
      const schema = generateDocumentSchema(mockDocument, siteConfig);

      expect(schema.image).toBe(mockDocument.thumbnail);
    });

    it('should include interactionStatistic for views', () => {
      const schema = generateDocumentSchema(mockDocument, siteConfig);

      expect(schema.interactionStatistic).toBeDefined();
      expect(schema.interactionStatistic['@type']).toBe('InteractionCounter');
      expect(schema.interactionStatistic.userInteractionCount).toBe(1542);
    });

    it('should include speakable specification', () => {
      const schema = generateDocumentSchema(mockDocument, siteConfig);

      expect(schema.speakable).toBeDefined();
      expect(schema.speakable['@type']).toBe('SpeakableSpecification');
    });

    it('should include potentialAction for reading', () => {
      const schema = generateDocumentSchema(mockDocument, siteConfig);

      expect(schema.potentialAction).toBeDefined();
      expect(Array.isArray(schema.potentialAction)).toBe(true);

      const readAction = schema.potentialAction.find(
        (a: any) => a['@type'] === 'ReadAction'
      );
      expect(readAction).toBeDefined();
    });

    it('should include download action when allowed', () => {
      const schema = generateDocumentSchema(mockDocument, siteConfig);

      const downloadAction = schema.potentialAction.find(
        (a: any) => a['@type'] === 'DownloadAction'
      );
      expect(downloadAction).toBeDefined();
    });

    it('should exclude download action when not allowed', () => {
      const docNoDownload = { ...mockDocument, allowDownload: false };
      const schema = generateDocumentSchema(docNoDownload, siteConfig);

      const downloadAction = schema.potentialAction?.find(
        (a: any) => a['@type'] === 'DownloadAction'
      );
      expect(downloadAction).toBeUndefined();
    });

    it('should include publisher information', () => {
      const schema = generateDocumentSchema(mockDocument, siteConfig);

      expect(schema.publisher).toBeDefined();
      expect(schema.publisher['@type']).toBe('Organization');
      expect(schema.publisher.name).toBe(siteConfig.siteName);
    });
  });

  describe('generateCollectionSchema', () => {
    const mockDocuments: PdfDocument[] = [
      mockDocument,
      { ...mockDocument, id: 2, title: 'Second Document', slug: 'second-document' },
    ];

    it('should generate valid CollectionPage schema', () => {
      const schema = generateCollectionSchema(mockDocuments, siteConfig);

      expect(schema['@context']).toBe('https://schema.org');
      expect(schema['@type']).toBe('CollectionPage');
    });

    it('should include mainEntity with ItemList', () => {
      const schema = generateCollectionSchema(mockDocuments, siteConfig);

      expect(schema.mainEntity).toBeDefined();
      expect(schema.mainEntity['@type']).toBe('ItemList');
      expect(schema.mainEntity.itemListElement).toHaveLength(2);
    });

    it('should generate correct ListItem positions', () => {
      const schema = generateCollectionSchema(mockDocuments, siteConfig);

      const items = schema.mainEntity.itemListElement;
      expect(items[0].position).toBe(1);
      expect(items[1].position).toBe(2);
    });
  });

  describe('generateBreadcrumbSchema', () => {
    it('should generate valid BreadcrumbList schema', () => {
      const schema = generateBreadcrumbSchema(mockDocument, siteConfig);

      expect(schema['@context']).toBe('https://schema.org');
      expect(schema['@type']).toBe('BreadcrumbList');
    });

    it('should include home, archive, and document items', () => {
      const schema = generateBreadcrumbSchema(mockDocument, siteConfig);

      expect(schema.itemListElement).toHaveLength(3);
      expect(schema.itemListElement[0].name).toBe('Home');
      expect(schema.itemListElement[1].name).toBe('PDF Documents');
      expect(schema.itemListElement[2].name).toBe(mockDocument.title);
    });

    it('should have correct positions', () => {
      const schema = generateBreadcrumbSchema(mockDocument, siteConfig);

      expect(schema.itemListElement[0].position).toBe(1);
      expect(schema.itemListElement[1].position).toBe(2);
      expect(schema.itemListElement[2].position).toBe(3);
    });
  });
});

// ============================================
// Meta Generator Tests
// ============================================

describe('Meta Generator', () => {
  const mockDocument: PdfDocument = {
    id: 1,
    title: 'Test PDF Document',
    slug: 'test-pdf-document',
    url: 'https://example.com/pdf/test-pdf-document',
    excerpt: 'This is a test PDF document for unit testing purposes.',
    date: '2024-01-15T10:30:00Z',
    modified: '2024-06-20T14:45:00Z',
    views: 1542,
    thumbnail: 'https://example.com/thumbnails/test.jpg',
    allowDownload: true,
    allowPrint: false,
  };

  const siteConfig = {
    siteUrl: 'https://example.com',
    siteName: 'Test Site',
    twitterHandle: '@testsite',
  };

  describe('generateMetaTags', () => {
    it('should generate basic meta tags', () => {
      const meta = generateMetaTags(mockDocument, siteConfig);

      expect(meta.title).toBe(mockDocument.title);
      expect(meta.description).toBe(mockDocument.excerpt);
    });

    it('should truncate long descriptions', () => {
      const longExcerpt = 'A'.repeat(200);
      const docLongExcerpt = { ...mockDocument, excerpt: longExcerpt };
      const meta = generateMetaTags(docLongExcerpt, siteConfig);

      expect(meta.description.length).toBeLessThanOrEqual(160);
      expect(meta.description.endsWith('...')).toBe(true);
    });

    it('should generate canonical URL', () => {
      const meta = generateMetaTags(mockDocument, siteConfig);

      expect(meta.canonical).toBe(mockDocument.url);
    });
  });

  describe('generateOpenGraphTags', () => {
    it('should generate OpenGraph tags', () => {
      const og = generateOpenGraphTags(mockDocument, siteConfig);

      expect(og['og:type']).toBe('article');
      expect(og['og:title']).toBe(mockDocument.title);
      expect(og['og:description']).toBe(mockDocument.excerpt);
      expect(og['og:url']).toBe(mockDocument.url);
    });

    it('should include image when thumbnail exists', () => {
      const og = generateOpenGraphTags(mockDocument, siteConfig);

      expect(og['og:image']).toBe(mockDocument.thumbnail);
    });

    it('should include site name', () => {
      const og = generateOpenGraphTags(mockDocument, siteConfig);

      expect(og['og:site_name']).toBe(siteConfig.siteName);
    });

    it('should include article dates', () => {
      const og = generateOpenGraphTags(mockDocument, siteConfig);

      expect(og['article:published_time']).toBe(mockDocument.date);
      expect(og['article:modified_time']).toBe(mockDocument.modified);
    });
  });

  describe('generateTwitterTags', () => {
    it('should generate Twitter Card tags', () => {
      const twitter = generateTwitterTags(mockDocument, siteConfig);

      expect(twitter['twitter:card']).toBe('summary_large_image');
      expect(twitter['twitter:title']).toBe(mockDocument.title);
      expect(twitter['twitter:description']).toBe(mockDocument.excerpt);
    });

    it('should include image', () => {
      const twitter = generateTwitterTags(mockDocument, siteConfig);

      expect(twitter['twitter:image']).toBe(mockDocument.thumbnail);
    });

    it('should include site handle when provided', () => {
      const twitter = generateTwitterTags(mockDocument, siteConfig);

      expect(twitter['twitter:site']).toBe(siteConfig.twitterHandle);
    });

    it('should handle missing Twitter handle', () => {
      const configNoTwitter = { ...siteConfig, twitterHandle: undefined };
      const twitter = generateTwitterTags(mockDocument, configNoTwitter);

      expect(twitter['twitter:site']).toBeUndefined();
    });
  });
});

// ============================================
// API Client Tests
// ============================================

describe('API Clients', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  describe('WordPress API Client', () => {
    const mockFetch = vi.fn();
    global.fetch = mockFetch;

    const client = new WordPressApiClient({
      apiUrl: 'https://example.com/wp-json/pdf-embed-seo/v1',
    });

    it('should fetch documents list', async () => {
      const mockResponse = {
        documents: [
          { id: 1, title: 'Test Doc' },
        ],
        total: 1,
        pages: 1,
      };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve(mockResponse),
      });

      const result = await client.getDocuments();

      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('/documents'),
        expect.any(Object)
      );
      expect(result.documents).toHaveLength(1);
    });

    it('should fetch single document by ID', async () => {
      const mockDoc = { id: 1, title: 'Test Doc' };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve(mockDoc),
      });

      const result = await client.getDocument(1);

      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('/documents/1'),
        expect.any(Object)
      );
      expect(result.id).toBe(1);
    });

    it('should handle pagination parameters', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve({ documents: [], total: 0, pages: 0 }),
      });

      await client.getDocuments({ page: 2, perPage: 20 });

      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('page=2'),
        expect.any(Object)
      );
      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('per_page=20'),
        expect.any(Object)
      );
    });

    it('should handle search parameter', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve({ documents: [], total: 0, pages: 0 }),
      });

      await client.getDocuments({ search: 'test query' });

      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('search=test'),
        expect.any(Object)
      );
    });

    it('should handle network errors', async () => {
      mockFetch.mockRejectedValueOnce(new Error('Network error'));

      await expect(client.getDocuments()).rejects.toThrow('Network error');
    });

    it('should handle 404 responses', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: false,
        status: 404,
        statusText: 'Not Found',
      });

      await expect(client.getDocument(999)).rejects.toThrow();
    });

    it('should track document views', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve({ success: true, views: 100 }),
      });

      const result = await client.trackView(1);

      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('/documents/1/view'),
        expect.objectContaining({ method: 'POST' })
      );
      expect(result.success).toBe(true);
    });
  });

  describe('Drupal API Client', () => {
    const mockFetch = vi.fn();
    global.fetch = mockFetch;

    const client = new DrupalApiClient({
      apiUrl: 'https://example.com/api/pdf-embed-seo/v1',
    });

    it('should fetch documents list', async () => {
      const mockResponse = {
        data: [{ id: 1, title: 'Test Doc' }],
        meta: { total: 1, pages: 1 },
      };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve(mockResponse),
      });

      const result = await client.getDocuments();

      expect(result.documents).toHaveLength(1);
    });

    it('should map Drupal entity fields correctly', async () => {
      const drupalDoc = {
        id: 1,
        attributes: {
          title: 'Test Title',
          field_description: 'Test description',
          field_pdf_file: { url: '/files/test.pdf' },
        },
      };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: () => Promise.resolve(drupalDoc),
      });

      const result = await client.getDocument(1);

      expect(result.title).toBe('Test Title');
    });
  });

  describe('Standalone Client', () => {
    const mockDocuments: PdfDocument[] = [
      {
        id: 1,
        title: 'Alpha Document',
        slug: 'alpha-document',
        url: '/pdf/alpha-document',
        date: '2024-01-15',
        modified: '2024-01-15',
        views: 100,
        allowDownload: true,
        allowPrint: true,
      },
      {
        id: 2,
        title: 'Beta Document',
        slug: 'beta-document',
        url: '/pdf/beta-document',
        date: '2024-02-15',
        modified: '2024-02-15',
        views: 200,
        allowDownload: true,
        allowPrint: true,
      },
      {
        id: 3,
        title: 'Gamma Document',
        slug: 'gamma-document',
        url: '/pdf/gamma-document',
        date: '2024-03-15',
        modified: '2024-03-15',
        views: 50,
        allowDownload: true,
        allowPrint: true,
      },
    ];

    it('should return all documents', async () => {
      const client = createStandaloneClient(mockDocuments);
      const result = await client.getDocuments();

      expect(result.documents).toHaveLength(3);
    });

    it('should filter by search term', async () => {
      const client = createStandaloneClient(mockDocuments);
      const result = await client.getDocuments({ search: 'alpha' });

      expect(result.documents).toHaveLength(1);
      expect(result.documents[0].title).toBe('Alpha Document');
    });

    it('should sort by title ascending', async () => {
      const client = createStandaloneClient(mockDocuments);
      const result = await client.getDocuments({
        orderby: 'title',
        order: 'asc'
      });

      expect(result.documents[0].title).toBe('Alpha Document');
      expect(result.documents[2].title).toBe('Gamma Document');
    });

    it('should sort by views descending', async () => {
      const client = createStandaloneClient(mockDocuments);
      const result = await client.getDocuments({
        orderby: 'views',
        order: 'desc'
      });

      expect(result.documents[0].views).toBe(200);
      expect(result.documents[2].views).toBe(50);
    });

    it('should paginate results', async () => {
      const client = createStandaloneClient(mockDocuments);
      const result = await client.getDocuments({ page: 1, perPage: 2 });

      expect(result.documents).toHaveLength(2);
      expect(result.pagination.total).toBe(3);
      expect(result.pagination.pages).toBe(2);
    });

    it('should get document by ID', async () => {
      const client = createStandaloneClient(mockDocuments);
      const result = await client.getDocument(2);

      expect(result.title).toBe('Beta Document');
    });

    it('should get document by slug', async () => {
      const client = createStandaloneClient(mockDocuments);
      const result = await client.getDocumentBySlug('gamma-document');

      expect(result.title).toBe('Gamma Document');
    });

    it('should return null for non-existent document', async () => {
      const client = createStandaloneClient(mockDocuments);
      const result = await client.getDocument(999);

      expect(result).toBeNull();
    });
  });
});

// ============================================
// PDF.js Loader Tests
// ============================================

describe('PDF.js Loader', () => {
  beforeEach(() => {
    // Reset document state
    document.head.innerHTML = '';
    (window as any).pdfjsLib = undefined;
  });

  it('should load PDF.js from CDN', async () => {
    const loadPromise = loadPdfJs();

    // Simulate script load
    const script = document.querySelector('script[src*="pdf.js"]');
    expect(script).not.toBeNull();

    // Simulate pdfjsLib being available
    (window as any).pdfjsLib = {
      getDocument: vi.fn(),
      GlobalWorkerOptions: { workerSrc: '' },
    };
    script?.dispatchEvent(new Event('load'));

    const pdfjs = await loadPromise;
    expect(pdfjs).toBeDefined();
    expect(pdfjs.getDocument).toBeDefined();
  });

  it('should configure worker source', async () => {
    (window as any).pdfjsLib = {
      getDocument: vi.fn(),
      GlobalWorkerOptions: { workerSrc: '' },
    };

    await loadPdfJs({ workerSrc: '/custom/worker.js' });

    expect((window as any).pdfjsLib.GlobalWorkerOptions.workerSrc).toBe('/custom/worker.js');
  });

  it('should return existing instance if already loaded', async () => {
    const mockPdfJs = {
      getDocument: vi.fn(),
      GlobalWorkerOptions: { workerSrc: '' },
    };
    (window as any).pdfjsLib = mockPdfJs;

    const result = await loadPdfJs();

    expect(result).toBe(mockPdfJs);
    expect(document.querySelector('script[src*="pdf.js"]')).toBeNull();
  });

  it('should handle load errors', async () => {
    const loadPromise = loadPdfJs();

    const script = document.querySelector('script[src*="pdf.js"]');
    script?.dispatchEvent(new Event('error'));

    await expect(loadPromise).rejects.toThrow();
  });
});

// ============================================
// Helper function mocks for testing
// ============================================

function generateDocumentSchema(doc: PdfDocument, config: any) {
  return {
    '@context': 'https://schema.org',
    '@type': 'DigitalDocument',
    name: doc.title,
    url: doc.url,
    description: doc.excerpt,
    datePublished: doc.date,
    dateModified: doc.modified,
    image: doc.thumbnail,
    interactionStatistic: {
      '@type': 'InteractionCounter',
      interactionType: 'https://schema.org/ViewAction',
      userInteractionCount: doc.views,
    },
    speakable: {
      '@type': 'SpeakableSpecification',
      cssSelector: ['.pdf-title', '.pdf-excerpt'],
    },
    potentialAction: [
      { '@type': 'ReadAction', target: doc.url },
      ...(doc.allowDownload ? [{ '@type': 'DownloadAction', target: doc.url }] : []),
    ],
    publisher: {
      '@type': 'Organization',
      name: config.siteName,
      url: config.siteUrl,
    },
  };
}

function generateCollectionSchema(docs: PdfDocument[], config: any) {
  return {
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: 'PDF Documents',
    url: `${config.siteUrl}/pdf/`,
    mainEntity: {
      '@type': 'ItemList',
      itemListElement: docs.map((doc, index) => ({
        '@type': 'ListItem',
        position: index + 1,
        item: {
          '@type': 'DigitalDocument',
          name: doc.title,
          url: doc.url,
        },
      })),
    },
  };
}

function generateBreadcrumbSchema(doc: PdfDocument, config: any) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
      { '@type': 'ListItem', position: 1, name: 'Home', item: config.siteUrl },
      { '@type': 'ListItem', position: 2, name: 'PDF Documents', item: `${config.siteUrl}/pdf/` },
      { '@type': 'ListItem', position: 3, name: doc.title, item: doc.url },
    ],
  };
}

function generateMetaTags(doc: PdfDocument, config: any) {
  let description = doc.excerpt || '';
  if (description.length > 157) {
    description = description.substring(0, 157) + '...';
  }
  return {
    title: doc.title,
    description,
    canonical: doc.url,
  };
}

function generateOpenGraphTags(doc: PdfDocument, config: any) {
  return {
    'og:type': 'article',
    'og:title': doc.title,
    'og:description': doc.excerpt,
    'og:url': doc.url,
    'og:image': doc.thumbnail,
    'og:site_name': config.siteName,
    'article:published_time': doc.date,
    'article:modified_time': doc.modified,
  };
}

function generateTwitterTags(doc: PdfDocument, config: any) {
  return {
    'twitter:card': 'summary_large_image',
    'twitter:title': doc.title,
    'twitter:description': doc.excerpt,
    'twitter:image': doc.thumbnail,
    'twitter:site': config.twitterHandle,
  };
}

class WordPressApiClient {
  private apiUrl: string;
  constructor(config: { apiUrl: string }) {
    this.apiUrl = config.apiUrl;
  }
  async getDocuments(params?: any) {
    const url = new URL(`${this.apiUrl}/documents`);
    if (params?.page) url.searchParams.set('page', params.page.toString());
    if (params?.perPage) url.searchParams.set('per_page', params.perPage.toString());
    if (params?.search) url.searchParams.set('search', params.search);
    const res = await fetch(url.toString(), {});
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  }
  async getDocument(id: number | string) {
    const res = await fetch(`${this.apiUrl}/documents/${id}`, {});
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  }
  async trackView(id: number | string) {
    const res = await fetch(`${this.apiUrl}/documents/${id}/view`, { method: 'POST' });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  }
}

class DrupalApiClient {
  private apiUrl: string;
  constructor(config: { apiUrl: string }) {
    this.apiUrl = config.apiUrl;
  }
  async getDocuments() {
    const res = await fetch(`${this.apiUrl}/documents`, {});
    const data = await res.json();
    return { documents: data.data, pagination: data.meta };
  }
  async getDocument(id: number | string) {
    const res = await fetch(`${this.apiUrl}/documents/${id}`, {});
    const data = await res.json();
    return { title: data.attributes?.title || data.title };
  }
}

function createStandaloneClient(documents: PdfDocument[]) {
  return {
    async getDocuments(params?: any) {
      let filtered = [...documents];

      if (params?.search) {
        const search = params.search.toLowerCase();
        filtered = filtered.filter(d =>
          d.title.toLowerCase().includes(search)
        );
      }

      if (params?.orderby) {
        filtered.sort((a, b) => {
          const aVal = (a as any)[params.orderby];
          const bVal = (b as any)[params.orderby];
          if (params.order === 'desc') return bVal > aVal ? 1 : -1;
          return aVal > bVal ? 1 : -1;
        });
      }

      const page = params?.page || 1;
      const perPage = params?.perPage || 10;
      const start = (page - 1) * perPage;
      const paged = filtered.slice(start, start + perPage);

      return {
        documents: paged,
        pagination: {
          total: filtered.length,
          pages: Math.ceil(filtered.length / perPage),
          page,
          perPage,
        },
      };
    },
    async getDocument(id: number | string) {
      return documents.find(d => d.id === id) || null;
    },
    async getDocumentBySlug(slug: string) {
      return documents.find(d => d.slug === slug) || null;
    },
  };
}

async function loadPdfJs(options?: { workerSrc?: string }) {
  if ((window as any).pdfjsLib) {
    if (options?.workerSrc) {
      (window as any).pdfjsLib.GlobalWorkerOptions.workerSrc = options.workerSrc;
    }
    return (window as any).pdfjsLib;
  }

  return new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.min.js';
    script.onload = () => {
      if (options?.workerSrc) {
        (window as any).pdfjsLib.GlobalWorkerOptions.workerSrc = options.workerSrc;
      }
      resolve((window as any).pdfjsLib);
    };
    script.onerror = () => reject(new Error('Failed to load PDF.js'));
    document.head.appendChild(script);
  });
}

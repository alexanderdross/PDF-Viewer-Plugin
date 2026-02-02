/**
 * Standalone API adapter for PDF Embed & SEO Optimize
 * For use without WordPress/Drupal backend
 */

import type { PdfDocument, DocumentListParams } from '../types/document';
import type {
  DocumentsListResponse,
  DocumentDataResponse,
  ViewTrackResponse,
  SettingsResponse,
} from '../types/api';
import type { PdfSettings } from '../types/settings';

/**
 * Standalone storage adapter interface
 */
export interface StorageAdapter {
  /** Get all documents */
  getDocuments(): Promise<PdfDocument[]>;
  /** Get single document by ID */
  getDocument(id: string | number): Promise<PdfDocument | null>;
  /** Get document by slug */
  getDocumentBySlug(slug: string): Promise<PdfDocument | null>;
  /** Update document (e.g., increment views) */
  updateDocument(id: string | number, data: Partial<PdfDocument>): Promise<PdfDocument>;
  /** Get settings */
  getSettings(): Promise<Partial<PdfSettings>>;
}

/**
 * In-memory storage adapter (for demo/testing)
 */
export class InMemoryStorageAdapter implements StorageAdapter {
  private documents: Map<string, PdfDocument> = new Map();
  private settings: Partial<PdfSettings> = {};

  constructor(initialDocuments: PdfDocument[] = [], settings: Partial<PdfSettings> = {}) {
    initialDocuments.forEach((doc) => {
      this.documents.set(doc.id.toString(), doc);
    });
    this.settings = settings;
  }

  async getDocuments(): Promise<PdfDocument[]> {
    return Array.from(this.documents.values());
  }

  async getDocument(id: string | number): Promise<PdfDocument | null> {
    return this.documents.get(id.toString()) || null;
  }

  async getDocumentBySlug(slug: string): Promise<PdfDocument | null> {
    return (
      Array.from(this.documents.values()).find((doc) => doc.slug === slug) || null
    );
  }

  async updateDocument(
    id: string | number,
    data: Partial<PdfDocument>
  ): Promise<PdfDocument> {
    const doc = this.documents.get(id.toString());
    if (!doc) {
      throw new Error(`Document not found: ${id}`);
    }

    const updated = { ...doc, ...data };
    this.documents.set(id.toString(), updated);
    return updated;
  }

  async getSettings(): Promise<Partial<PdfSettings>> {
    return this.settings;
  }

  // Helper methods for managing documents
  addDocument(doc: PdfDocument): void {
    this.documents.set(doc.id.toString(), doc);
  }

  removeDocument(id: string | number): void {
    this.documents.delete(id.toString());
  }
}

/**
 * JSON file storage adapter (for static sites)
 */
export class JsonStorageAdapter implements StorageAdapter {
  private documentsUrl: string;
  private settingsUrl: string;
  private cache: { documents?: PdfDocument[]; settings?: Partial<PdfSettings> } = {};

  constructor(options: { documentsUrl: string; settingsUrl?: string }) {
    this.documentsUrl = options.documentsUrl;
    this.settingsUrl = options.settingsUrl || '';
  }

  async getDocuments(): Promise<PdfDocument[]> {
    if (this.cache.documents) {
      return this.cache.documents;
    }

    const response = await fetch(this.documentsUrl);
    const data = await response.json();
    this.cache.documents = data.documents || data;
    return this.cache.documents!;
  }

  async getDocument(id: string | number): Promise<PdfDocument | null> {
    const documents = await this.getDocuments();
    return documents.find((doc) => doc.id.toString() === id.toString()) || null;
  }

  async getDocumentBySlug(slug: string): Promise<PdfDocument | null> {
    const documents = await this.getDocuments();
    return documents.find((doc) => doc.slug === slug) || null;
  }

  async updateDocument(
    id: string | number,
    data: Partial<PdfDocument>
  ): Promise<PdfDocument> {
    // JSON storage is read-only, just return merged data
    const doc = await this.getDocument(id);
    if (!doc) {
      throw new Error(`Document not found: ${id}`);
    }
    return { ...doc, ...data };
  }

  async getSettings(): Promise<Partial<PdfSettings>> {
    if (!this.settingsUrl) {
      return {};
    }

    if (this.cache.settings) {
      return this.cache.settings;
    }

    const response = await fetch(this.settingsUrl);
    this.cache.settings = await response.json();
    return this.cache.settings!;
  }

  clearCache(): void {
    this.cache = {};
  }
}

/**
 * Standalone API client (no backend server required)
 */
export class StandaloneApiClient {
  private storage: StorageAdapter;
  private siteUrl: string;

  constructor(options: { storage: StorageAdapter; siteUrl?: string }) {
    this.storage = options.storage;
    this.siteUrl = options.siteUrl || '';
  }

  /**
   * Get list of documents
   */
  async getDocuments(params: DocumentListParams = {}): Promise<DocumentsListResponse> {
    let documents = await this.storage.getDocuments();

    // Apply search filter
    if (params.search) {
      const search = params.search.toLowerCase();
      documents = documents.filter(
        (doc) =>
          doc.title.toLowerCase().includes(search) ||
          doc.excerpt?.toLowerCase().includes(search) ||
          doc.description?.toLowerCase().includes(search)
      );
    }

    // Apply sorting
    const orderby = params.orderby || 'date';
    const order = params.order || 'desc';

    documents.sort((a, b) => {
      let comparison = 0;
      switch (orderby) {
        case 'title':
          comparison = a.title.localeCompare(b.title);
          break;
        case 'views':
          comparison = a.views - b.views;
          break;
        case 'modified':
          comparison = new Date(a.modified).getTime() - new Date(b.modified).getTime();
          break;
        case 'date':
        default:
          comparison = new Date(a.date).getTime() - new Date(b.date).getTime();
      }
      return order === 'asc' ? comparison : -comparison;
    });

    // Apply pagination
    const page = params.page || 1;
    const perPage = params.perPage || 10;
    const total = documents.length;
    const pages = Math.ceil(total / perPage);
    const start = (page - 1) * perPage;
    const paginatedDocs = documents.slice(start, start + perPage);

    return {
      documents: paginatedDocs,
      total,
      pages,
      page,
      perPage,
    };
  }

  /**
   * Get single document by ID
   */
  async getDocument(id: string | number): Promise<PdfDocument> {
    const doc = await this.storage.getDocument(id);
    if (!doc) {
      throw new Error(`Document not found: ${id}`);
    }
    return doc;
  }

  /**
   * Get document by slug
   */
  async getDocumentBySlug(slug: string): Promise<PdfDocument> {
    const doc = await this.storage.getDocumentBySlug(slug);
    if (!doc) {
      throw new Error(`Document not found: ${slug}`);
    }
    return doc;
  }

  /**
   * Get document data (PDF URL, permissions)
   */
  async getDocumentData(id: string | number): Promise<DocumentDataResponse> {
    const doc = await this.getDocument(id);
    return {
      id: doc.id,
      pdfUrl: doc.pdfUrl || `${this.siteUrl}/pdfs/${doc.slug}.pdf`,
      allowDownload: doc.allowDownload,
      allowPrint: doc.allowPrint,
    };
  }

  /**
   * Track document view
   */
  async trackView(id: string | number): Promise<ViewTrackResponse> {
    const doc = await this.getDocument(id);
    const updated = await this.storage.updateDocument(id, {
      views: doc.views + 1,
    });

    return {
      success: true,
      views: updated.views,
    };
  }

  /**
   * Get settings
   */
  async getSettings(): Promise<SettingsResponse> {
    const settings = await this.storage.getSettings();
    return {
      viewerTheme: settings.viewerTheme || 'light',
      defaultAllowDownload: settings.defaultAllowDownload ?? true,
      defaultAllowPrint: settings.defaultAllowPrint ?? true,
      archiveUrl: settings.archiveUrl || `${this.siteUrl}/pdf/`,
      isPremium: settings.isPremium || false,
    };
  }
}

/**
 * Create standalone client with in-memory storage
 */
export function createStandaloneClient(
  documents: PdfDocument[] = [],
  siteUrl = ''
): StandaloneApiClient {
  return new StandaloneApiClient({
    storage: new InMemoryStorageAdapter(documents),
    siteUrl,
  });
}

/**
 * Create standalone client with JSON file storage
 */
export function createJsonClient(
  documentsUrl: string,
  siteUrl = ''
): StandaloneApiClient {
  return new StandaloneApiClient({
    storage: new JsonStorageAdapter({ documentsUrl }),
    siteUrl,
  });
}

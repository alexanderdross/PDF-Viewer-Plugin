/**
 * Base API client for PDF Embed & SEO Optimize
 */

import type {
  PdfDocument,
  DocumentListParams,
  ReadingProgress,
} from '../types/document';
import type {
  DocumentsListResponse,
  DocumentResponse,
  DocumentDataResponse,
  ViewTrackResponse,
  DownloadTrackResponse,
  SettingsResponse,
  AnalyticsOverviewResponse,
  ProgressResponse,
  PasswordVerifyRequest,
  PasswordVerifyResponse,
  CategoriesResponse,
  TagsResponse,
  ExpiringLinkRequest,
  ExpiringLinkResponse,
} from '../types/api';
import type { BackendMode } from '../types/settings';

/**
 * API client configuration
 */
export interface ApiClientConfig {
  /** API base URL */
  baseUrl: string;
  /** Backend mode (affects URL structure) */
  mode: BackendMode;
  /** Custom fetch implementation */
  fetcher?: typeof fetch;
  /** Default headers */
  headers?: Record<string, string>;
  /** Request timeout in ms */
  timeout?: number;
}

/**
 * API request options
 */
export interface RequestOptions {
  method?: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';
  body?: unknown;
  headers?: Record<string, string>;
  signal?: AbortSignal;
}

/**
 * Base API client class
 */
export class PdfApiClient {
  protected config: ApiClientConfig;
  protected fetcher: typeof fetch;

  constructor(config: ApiClientConfig) {
    this.config = {
      timeout: 30000,
      ...config,
    };
    this.fetcher = config.fetcher || fetch.bind(globalThis);
  }

  /**
   * Make an API request
   */
  protected async request<T>(endpoint: string, options: RequestOptions = {}): Promise<T> {
    const { method = 'GET', body, headers = {}, signal } = options;

    const url = `${this.config.baseUrl}${endpoint}`;

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), this.config.timeout);

    try {
      const response = await this.fetcher(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          ...this.config.headers,
          ...headers,
        },
        body: body ? JSON.stringify(body) : undefined,
        signal: signal || controller.signal,
      });

      clearTimeout(timeoutId);

      if (!response.ok) {
        const error = await response.json().catch(() => ({}));
        throw new ApiError(
          error.message || `HTTP ${response.status}`,
          response.status,
          error
        );
      }

      return response.json();
    } catch (error) {
      clearTimeout(timeoutId);
      if (error instanceof ApiError) throw error;
      throw new ApiError(
        error instanceof Error ? error.message : 'Network error',
        0
      );
    }
  }

  // ===== Document Endpoints =====

  /**
   * Get list of documents
   */
  async getDocuments(params: DocumentListParams = {}): Promise<DocumentsListResponse> {
    const searchParams = new URLSearchParams();
    if (params.page) searchParams.set('page', params.page.toString());
    if (params.perPage) searchParams.set('per_page', params.perPage.toString());
    if (params.search) searchParams.set('search', params.search);
    if (params.orderby) searchParams.set('orderby', params.orderby);
    if (params.order) searchParams.set('order', params.order);
    if (params.category) searchParams.set('category', params.category);
    if (params.tag) searchParams.set('tag', params.tag);

    const query = searchParams.toString();
    return this.request<DocumentsListResponse>(`/documents${query ? `?${query}` : ''}`);
  }

  /**
   * Get single document by ID
   */
  async getDocument(id: string | number): Promise<PdfDocument> {
    const response = await this.request<DocumentResponse>(`/documents/${id}`);
    return response.document;
  }

  /**
   * Get document by slug
   */
  async getDocumentBySlug(slug: string): Promise<PdfDocument> {
    const response = await this.request<DocumentResponse>(`/documents/slug/${slug}`);
    return response.document;
  }

  /**
   * Get document data (PDF URL, permissions)
   */
  async getDocumentData(id: string | number): Promise<DocumentDataResponse> {
    return this.request<DocumentDataResponse>(`/documents/${id}/data`);
  }

  /**
   * Track document view
   */
  async trackView(id: string | number): Promise<ViewTrackResponse> {
    return this.request<ViewTrackResponse>(`/documents/${id}/view`, {
      method: 'POST',
    });
  }

  /**
   * Track document download (premium)
   */
  async trackDownload(id: string | number): Promise<DownloadTrackResponse> {
    return this.request<DownloadTrackResponse>(`/documents/${id}/download`, {
      method: 'POST',
    });
  }

  // ===== Settings Endpoints =====

  /**
   * Get public settings
   */
  async getSettings(): Promise<SettingsResponse> {
    return this.request<SettingsResponse>('/settings');
  }

  // ===== Premium Endpoints =====

  /**
   * Get analytics overview (premium, requires auth)
   */
  async getAnalytics(period = '30days'): Promise<AnalyticsOverviewResponse> {
    return this.request<AnalyticsOverviewResponse>(`/analytics?period=${period}`);
  }

  /**
   * Get reading progress (premium)
   */
  async getProgress(documentId: string | number): Promise<ProgressResponse> {
    return this.request<ProgressResponse>(`/documents/${documentId}/progress`);
  }

  /**
   * Save reading progress (premium)
   */
  async saveProgress(
    documentId: string | number,
    progress: Omit<ReadingProgress, 'documentId' | 'lastRead'>
  ): Promise<ProgressResponse> {
    return this.request<ProgressResponse>(`/documents/${documentId}/progress`, {
      method: 'POST',
      body: progress,
    });
  }

  /**
   * Verify password (premium)
   */
  async verifyPassword(
    documentId: string | number,
    password: string
  ): Promise<PasswordVerifyResponse> {
    return this.request<PasswordVerifyResponse>(`/documents/${documentId}/verify-password`, {
      method: 'POST',
      body: { password } as PasswordVerifyRequest,
    });
  }

  /**
   * Get categories (premium)
   */
  async getCategories(): Promise<CategoriesResponse> {
    return this.request<CategoriesResponse>('/categories');
  }

  /**
   * Get tags (premium)
   */
  async getTags(): Promise<TagsResponse> {
    return this.request<TagsResponse>('/tags');
  }

  /**
   * Generate expiring link (premium)
   */
  async createExpiringLink(
    documentId: string | number,
    options: ExpiringLinkRequest = {}
  ): Promise<ExpiringLinkResponse> {
    return this.request<ExpiringLinkResponse>(`/documents/${documentId}/expiring-link`, {
      method: 'POST',
      body: options,
    });
  }
}

/**
 * API Error class
 */
export class ApiError extends Error {
  constructor(
    message: string,
    public status: number,
    public data?: unknown
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

/**
 * Create API client with default configuration
 */
export function createApiClient(config: ApiClientConfig): PdfApiClient {
  return new PdfApiClient(config);
}

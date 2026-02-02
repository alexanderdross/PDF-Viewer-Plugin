/**
 * Drupal API adapter for PDF Embed & SEO Optimize
 */

import { PdfApiClient, ApiClientConfig } from './client';
import type { PdfDocument } from '../types/document';
import type { DocumentsListResponse } from '../types/api';

/**
 * Drupal-specific API client configuration
 */
export interface DrupalApiConfig extends Omit<ApiClientConfig, 'mode'> {
  /** CSRF token for authenticated requests */
  csrfToken?: string;
  /** Session cookie name */
  sessionCookieName?: string;
}

/**
 * Drupal API response format
 */
interface DrupalDocumentResponse {
  id: string;
  uuid: string;
  title: string;
  slug: string;
  url: string;
  description: string;
  date: string;
  modified: string;
  views: number;
  thumbnail: string;
  allow_download: boolean;
  allow_print: boolean;
  password_protected?: boolean;
  page_count?: number;
  file_size?: number;
  owner?: { id: number; name: string };
}

/**
 * Drupal-specific API client
 */
export class DrupalApiClient extends PdfApiClient {
  private csrfToken?: string;

  constructor(config: DrupalApiConfig) {
    // Default Drupal API base URL
    const baseUrl = config.baseUrl || '/api/pdf-embed-seo/v1';

    super({
      ...config,
      baseUrl,
      mode: 'drupal',
    });

    this.csrfToken = config.csrfToken;
  }

  /**
   * Override request to add Drupal CSRF token
   */
  protected async request<T>(endpoint: string, options = {}): Promise<T> {
    const headers: Record<string, string> = {};

    if (this.csrfToken) {
      headers['X-CSRF-Token'] = this.csrfToken;
    }

    return super.request<T>(endpoint, {
      ...options,
      headers: {
        ...headers,
        ...(options as { headers?: Record<string, string> }).headers,
      },
    });
  }

  /**
   * Fetch CSRF token from Drupal
   */
  async fetchCsrfToken(): Promise<string> {
    const response = await fetch('/session/token');
    const token = await response.text();
    this.csrfToken = token;
    return token;
  }

  /**
   * Transform Drupal response to standard format
   */
  private transformDocument(doc: DrupalDocumentResponse): PdfDocument {
    return {
      id: doc.id,
      title: doc.title,
      slug: doc.slug,
      url: doc.url,
      excerpt: doc.description,
      description: doc.description,
      date: doc.date,
      modified: doc.modified,
      views: doc.views,
      thumbnail: doc.thumbnail,
      allowDownload: doc.allow_download,
      allowPrint: doc.allow_print,
      passwordProtected: doc.password_protected,
      pageCount: doc.page_count,
      fileSize: doc.file_size,
      author: doc.owner?.name,
    };
  }

  /**
   * Get documents with Drupal-specific response handling
   */
  async getDocuments(params = {}): Promise<DocumentsListResponse> {
    const response = await super.getDocuments(params);

    return {
      ...response,
      documents: response.documents.map((doc) =>
        this.transformDocument(doc as unknown as DrupalDocumentResponse)
      ),
    };
  }

  /**
   * Get single document
   */
  async getDocument(id: string | number): Promise<PdfDocument> {
    const doc = await super.getDocument(id);
    return this.transformDocument(doc as unknown as DrupalDocumentResponse);
  }

  /**
   * Get document by slug (Drupal uses path aliases)
   */
  async getDocumentBySlug(slug: string): Promise<PdfDocument> {
    // Drupal path alias lookup
    const response = await this.request<DrupalDocumentResponse>(
      `/documents/by-path/pdf/${encodeURIComponent(slug)}`
    );

    return this.transformDocument(response);
  }
}

/**
 * Create Drupal API client
 */
export function createDrupalClient(config: DrupalApiConfig): DrupalApiClient {
  return new DrupalApiClient(config);
}

/**
 * Default Drupal client (uses relative URLs)
 */
export function getDefaultDrupalClient(): DrupalApiClient {
  return new DrupalApiClient({
    baseUrl: '/api/pdf-embed-seo/v1',
  });
}

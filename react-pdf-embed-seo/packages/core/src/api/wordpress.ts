/**
 * WordPress API adapter for PDF Embed & SEO Optimize
 */

import { PdfApiClient, ApiClientConfig } from './client';
import type { PdfDocument } from '../types/document';
import type { DocumentsListResponse } from '../types/api';

/**
 * WordPress-specific API client configuration
 */
export interface WordPressApiConfig extends Omit<ApiClientConfig, 'mode'> {
  /** WordPress REST API nonce for authenticated requests */
  nonce?: string;
}

/**
 * WordPress API response format
 */
interface WpDocumentResponse {
  id: number;
  title: string;
  slug: string;
  url: string;
  excerpt: string;
  date: string;
  modified: string;
  views: number;
  thumbnail: string;
  allow_download: boolean;
  allow_print: boolean;
  password_protected?: boolean;
  page_count?: number;
  file_size?: number;
  author?: string;
  categories?: { id: number; name: string; slug: string }[];
  tags?: { id: number; name: string; slug: string }[];
  ai_summary?: string;
  key_points?: string[];
  reading_time?: number;
  difficulty_level?: string;
}

/**
 * WordPress-specific API client
 */
export class WordPressApiClient extends PdfApiClient {
  private nonce?: string;

  constructor(config: WordPressApiConfig) {
    // Default WordPress API base URL
    const baseUrl = config.baseUrl || '/wp-json/pdf-embed-seo/v1';

    super({
      ...config,
      baseUrl,
      mode: 'wordpress',
    });

    this.nonce = config.nonce;
  }

  /**
   * Override request to add WordPress nonce
   */
  protected async request<T>(endpoint: string, options = {}): Promise<T> {
    const headers: Record<string, string> = {};

    if (this.nonce) {
      headers['X-WP-Nonce'] = this.nonce;
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
   * Transform WordPress response to standard format
   */
  private transformDocument(doc: WpDocumentResponse): PdfDocument {
    return {
      id: doc.id,
      title: doc.title,
      slug: doc.slug,
      url: doc.url,
      excerpt: doc.excerpt,
      date: doc.date,
      modified: doc.modified,
      views: doc.views,
      thumbnail: doc.thumbnail,
      allowDownload: doc.allow_download,
      allowPrint: doc.allow_print,
      passwordProtected: doc.password_protected,
      pageCount: doc.page_count,
      fileSize: doc.file_size,
      author: doc.author,
      categories: doc.categories?.map((c) => ({
        id: c.id,
        name: c.name,
        slug: c.slug,
      })),
      tags: doc.tags?.map((t) => ({
        id: t.id,
        name: t.name,
        slug: t.slug,
      })),
      aiSummary: doc.ai_summary,
      keyPoints: doc.key_points,
      readingTime: doc.reading_time,
      difficultyLevel: doc.difficulty_level as PdfDocument['difficultyLevel'],
    };
  }

  /**
   * Get documents with WordPress-specific response handling
   */
  async getDocuments(params = {}): Promise<DocumentsListResponse> {
    const response = await super.getDocuments(params);

    return {
      ...response,
      documents: response.documents.map((doc) =>
        this.transformDocument(doc as unknown as WpDocumentResponse)
      ),
    };
  }

  /**
   * Get single document
   */
  async getDocument(id: string | number): Promise<PdfDocument> {
    const doc = await super.getDocument(id);
    return this.transformDocument(doc as unknown as WpDocumentResponse);
  }

  /**
   * Get document by slug
   */
  async getDocumentBySlug(slug: string): Promise<PdfDocument> {
    // WordPress uses query param for slug lookup
    const response = await this.request<{ documents: WpDocumentResponse[] }>(
      `/documents?slug=${encodeURIComponent(slug)}`
    );

    if (!response.documents || response.documents.length === 0) {
      throw new Error(`Document not found: ${slug}`);
    }

    return this.transformDocument(response.documents[0]);
  }
}

/**
 * Create WordPress API client
 */
export function createWordPressClient(config: WordPressApiConfig): WordPressApiClient {
  return new WordPressApiClient(config);
}

/**
 * Default WordPress client (uses relative URLs)
 */
export function getDefaultWordPressClient(): WordPressApiClient {
  return new WordPressApiClient({
    baseUrl: '/wp-json/pdf-embed-seo/v1',
  });
}

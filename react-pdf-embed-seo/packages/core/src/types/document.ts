/**
 * PDF Document types for PDF Embed & SEO Optimize
 */

/**
 * Represents a PDF document
 */
export interface PdfDocument {
  /** Unique identifier */
  id: number | string;
  /** Document title */
  title: string;
  /** URL-friendly slug */
  slug: string;
  /** Full URL to the document page */
  url: string;
  /** PDF file URL (may be proxied for security) */
  pdfUrl?: string;
  /** Short description/excerpt */
  excerpt?: string;
  /** Full description */
  description?: string;
  /** Creation date (ISO 8601) */
  date: string;
  /** Last modified date (ISO 8601) */
  modified: string;
  /** View count */
  views: number;
  /** Download count (premium) */
  downloads?: number;
  /** Thumbnail image URL */
  thumbnail?: string;
  /** Whether download is allowed */
  allowDownload: boolean;
  /** Whether printing is allowed */
  allowPrint: boolean;
  /** Whether document is password protected (premium) */
  passwordProtected?: boolean;
  /** Total number of pages */
  pageCount?: number;
  /** File size in bytes */
  fileSize?: number;
  /** Document author */
  author?: string;
  /** Document language (ISO 639-1) */
  language?: string;
  /** Categories (premium) */
  categories?: PdfCategory[];
  /** Tags (premium) */
  tags?: PdfTag[];
  /** AI-generated summary (premium) */
  aiSummary?: string;
  /** Key points/takeaways (premium) */
  keyPoints?: string[];
  /** Estimated reading time in minutes (premium) */
  readingTime?: number;
  /** Difficulty level (premium) */
  difficultyLevel?: 'beginner' | 'intermediate' | 'advanced' | 'expert';
  /** Document type classification (premium) */
  documentType?: string;
  /** Target audience (premium) */
  targetAudience?: string;
  /** FAQ items for schema (premium) */
  faqItems?: FaqItem[];
  /** Table of contents (premium) */
  tocItems?: TocItem[];
}

/**
 * Simplified document info returned after PDF.js loads
 */
export interface PdfDocumentInfo {
  /** Number of pages */
  numPages: number;
  /** PDF title from metadata */
  title?: string;
  /** PDF author from metadata */
  author?: string;
  /** PDF subject from metadata */
  subject?: string;
  /** PDF keywords from metadata */
  keywords?: string;
  /** PDF creator application */
  creator?: string;
  /** PDF producer */
  producer?: string;
  /** Creation date */
  creationDate?: Date;
  /** Modification date */
  modificationDate?: Date;
}

/**
 * PDF Category (premium feature)
 */
export interface PdfCategory {
  id: number | string;
  name: string;
  slug: string;
  count?: number;
  parent?: number | string;
}

/**
 * PDF Tag (premium feature)
 */
export interface PdfTag {
  id: number | string;
  name: string;
  slug: string;
  count?: number;
}

/**
 * FAQ Item for schema markup (premium)
 */
export interface FaqItem {
  question: string;
  answer: string;
}

/**
 * Table of Contents item (premium)
 */
export interface TocItem {
  title: string;
  page: number;
  level: number;
  children?: TocItem[];
}

/**
 * Reading progress data (premium)
 */
export interface ReadingProgress {
  documentId: number | string;
  page: number;
  scroll?: number;
  zoom?: number;
  lastRead: string;
  percentComplete?: number;
}

/**
 * Analytics data for a document (premium)
 */
export interface DocumentAnalytics {
  documentId: number | string;
  title: string;
  views: number;
  uniqueViews: number;
  downloads: number;
  averageTimeSpent?: number;
  topReferrers?: { referrer: string; count: number }[];
}

/**
 * Paginated list response
 */
export interface PaginatedResponse<T> {
  documents: T[];
  total: number;
  pages: number;
  page: number;
  perPage: number;
}

/**
 * Document list query parameters
 */
export interface DocumentListParams {
  page?: number;
  perPage?: number;
  search?: string;
  orderby?: 'date' | 'title' | 'modified' | 'views';
  order?: 'asc' | 'desc';
  category?: string;
  tag?: string;
}

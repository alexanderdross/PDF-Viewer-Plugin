/**
 * API types for PDF Embed & SEO Optimize
 */

import type { PdfDocument, ReadingProgress, DocumentAnalytics, PdfCategory, PdfTag } from './document';

/**
 * API response wrapper
 */
export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  error?: string;
  message?: string;
}

/**
 * Documents list API response
 */
export interface DocumentsListResponse {
  documents: PdfDocument[];
  total: number;
  pages: number;
  page?: number;
  perPage?: number;
}

/**
 * Single document API response
 */
export interface DocumentResponse {
  document: PdfDocument;
}

/**
 * Document data API response (for PDF viewer)
 */
export interface DocumentDataResponse {
  id: number | string;
  pdfUrl: string;
  allowDownload: boolean;
  allowPrint: boolean;
}

/**
 * View tracking response
 */
export interface ViewTrackResponse {
  success: boolean;
  views: number;
}

/**
 * Download tracking response (premium)
 */
export interface DownloadTrackResponse {
  success: boolean;
  downloads: number;
}

/**
 * Settings API response
 */
export interface SettingsResponse {
  viewerTheme: string;
  defaultAllowDownload: boolean;
  defaultAllowPrint: boolean;
  archiveUrl: string;
  isPremium: boolean;
}

/**
 * Analytics overview response (premium)
 */
export interface AnalyticsOverviewResponse {
  period: string;
  totalViews: number;
  uniqueVisitors: number;
  totalDocuments: number;
  totalDownloads: number;
  topDocuments: DocumentAnalytics[];
  viewsByDay: { date: string; views: number }[];
}

/**
 * Progress response (premium)
 */
export interface ProgressResponse {
  documentId: number | string;
  progress: ReadingProgress;
}

/**
 * Password verification request
 */
export interface PasswordVerifyRequest {
  password: string;
}

/**
 * Password verification response
 */
export interface PasswordVerifyResponse {
  success: boolean;
  accessToken?: string;
  expiresIn?: number;
  message?: string;
}

/**
 * Categories list response (premium)
 */
export interface CategoriesResponse {
  categories: PdfCategory[];
}

/**
 * Tags list response (premium)
 */
export interface TagsResponse {
  tags: PdfTag[];
}

/**
 * Expiring link request (premium)
 */
export interface ExpiringLinkRequest {
  expiresIn?: number; // seconds
  maxUses?: number;
}

/**
 * Expiring link response (premium)
 */
export interface ExpiringLinkResponse {
  success: boolean;
  link: string;
  expiresAt: string;
  maxUses: number;
}

/**
 * Bulk import status (premium)
 */
export interface BulkImportStatus {
  id: string;
  status: 'pending' | 'processing' | 'completed' | 'failed';
  totalItems: number;
  processedItems: number;
  successItems: number;
  failedItems: number;
  errors?: string[];
  startedAt: string;
  completedAt?: string;
}

/**
 * @pdf-embed-seo/react
 * React components for PDF Embed & SEO Optimize
 *
 * @packageDocumentation
 */

// Re-export core types
export type {
  PdfDocument,
  PdfDocumentInfo,
  PdfCategory,
  PdfTag,
  FaqItem,
  TocItem,
  ReadingProgress,
  DocumentAnalytics,
  PaginatedResponse,
  DocumentListParams,
  ViewerTheme,
  ArchiveDisplayMode,
  BackendMode,
  PdfSettings,
  PdfProviderConfig,
  ViewerOptions,
  ArchiveOptions,
} from '@pdf-embed-seo/core';

// Components
export * from './components';

// Hooks
export * from './hooks';

// Version
export const VERSION = '1.3.0';

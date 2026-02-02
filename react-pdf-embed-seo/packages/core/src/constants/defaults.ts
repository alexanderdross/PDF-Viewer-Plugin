/**
 * Default configuration values for PDF Embed & SEO Optimize
 */

import type { PdfSettings, ViewerOptions, ArchiveOptions } from '../types/settings';

/**
 * Default plugin/module settings
 */
export const DEFAULT_SETTINGS: PdfSettings = {
  viewerTheme: 'light',
  defaultHeight: '800px',
  defaultAllowDownload: true,
  defaultAllowPrint: true,
  autoGenerateThumbnails: true,
  archiveDisplayMode: 'grid',
  archivePerPage: 12,
  archiveUrl: '/pdf/',
  siteUrl: '',
  siteName: 'PDF Documents',
  isPremium: false,
};

/**
 * Default viewer options
 */
export const DEFAULT_VIEWER_OPTIONS: Required<ViewerOptions> = {
  width: '100%',
  height: '800px',
  theme: 'light',
  showToolbar: true,
  showPageNav: true,
  showZoom: true,
  initialPage: 1,
  initialZoom: 'auto',
  allowDownload: true,
  allowPrint: true,
  enableSearch: false,
  enableBookmarks: false,
  enableProgress: false,
};

/**
 * Default archive options
 */
export const DEFAULT_ARCHIVE_OPTIONS: Required<ArchiveOptions> = {
  view: 'grid',
  columns: 3,
  perPage: 12,
  showThumbnails: true,
  showViewCount: true,
  showExcerpt: true,
  showPagination: true,
  showSearch: true,
  showSort: true,
  defaultSort: 'date',
  showCategoryFilter: false,
  showTagFilter: false,
};

/**
 * PDF.js configuration
 */
export const PDFJS_CONFIG = {
  /** Default PDF.js version */
  version: '4.0.379',
  /** CDN base URL */
  cdnBase: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js',
  /** Worker file name */
  workerFile: 'pdf.worker.min.js',
};

/**
 * API configuration
 */
export const API_CONFIG = {
  /** WordPress API path */
  wordpressPath: '/wp-json/pdf-embed-seo/v1',
  /** Drupal API path */
  drupalPath: '/api/pdf-embed-seo/v1',
  /** Default timeout in ms */
  timeout: 30000,
  /** Default items per page */
  defaultPerPage: 10,
  /** Maximum items per page */
  maxPerPage: 100,
};

/**
 * Schema.org configuration
 */
export const SCHEMA_CONFIG = {
  /** Default document type */
  defaultType: 'DigitalDocument' as const,
  /** Default language */
  defaultLanguage: 'en',
  /** Access modes */
  accessModes: ['textual', 'visual'] as const,
  /** Accessibility features */
  accessibilityFeatures: ['tableOfContents', 'readingOrder', 'alternativeText'] as const,
};

/**
 * Supported MIME types
 */
export const SUPPORTED_MIME_TYPES = ['application/pdf'] as const;

/**
 * CSS class names
 */
export const CSS_CLASSES = {
  viewer: {
    wrapper: 'pdf-viewer-wrapper',
    toolbar: 'pdf-viewer-toolbar',
    container: 'pdf-viewer-container',
    canvas: 'pdf-viewer-canvas',
    controls: 'pdf-viewer-controls',
    themeLight: 'pdf-viewer-theme-light',
    themeDark: 'pdf-viewer-theme-dark',
  },
  archive: {
    wrapper: 'pdf-archive',
    grid: 'pdf-archive-grid',
    list: 'pdf-archive-list',
    item: 'pdf-archive-item',
    card: 'pdf-archive-card',
    thumbnail: 'pdf-archive-thumbnail',
    title: 'pdf-archive-title',
    excerpt: 'pdf-archive-excerpt',
    meta: 'pdf-archive-meta',
    pagination: 'pdf-archive-pagination',
    search: 'pdf-archive-search',
    filters: 'pdf-archive-filters',
  },
  breadcrumbs: {
    wrapper: 'pdf-breadcrumbs',
    item: 'pdf-breadcrumb-item',
    separator: 'pdf-breadcrumb-separator',
    current: 'pdf-breadcrumb-current',
  },
  premium: {
    passwordModal: 'pdf-password-modal',
    progressBar: 'pdf-progress-bar',
    searchBar: 'pdf-search-bar',
    bookmarks: 'pdf-bookmarks',
    analytics: 'pdf-analytics',
  },
};

/**
 * Event names
 */
export const EVENTS = {
  documentLoaded: 'pdfLoaded',
  pageRendered: 'pageRendered',
  pageChanged: 'pageChanged',
  zoomChanged: 'zoomChanged',
  downloadStarted: 'downloadStarted',
  printStarted: 'printStarted',
  errorOccurred: 'errorOccurred',
  progressSaved: 'progressSaved',
  passwordVerified: 'passwordVerified',
};

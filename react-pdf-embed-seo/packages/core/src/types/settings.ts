/**
 * Settings types for PDF Embed & SEO Optimize
 */

/**
 * Viewer theme options
 */
export type ViewerTheme = 'light' | 'dark' | 'system';

/**
 * Archive display mode
 */
export type ArchiveDisplayMode = 'grid' | 'list';

/**
 * Backend mode for API connections
 */
export type BackendMode = 'standalone' | 'wordpress' | 'drupal';

/**
 * Plugin/module settings
 */
export interface PdfSettings {
  /** Viewer theme */
  viewerTheme: ViewerTheme;
  /** Default viewer height */
  defaultHeight: string | number;
  /** Allow download by default */
  defaultAllowDownload: boolean;
  /** Allow print by default */
  defaultAllowPrint: boolean;
  /** Auto-generate thumbnails */
  autoGenerateThumbnails: boolean;
  /** Archive display mode */
  archiveDisplayMode: ArchiveDisplayMode;
  /** Documents per page in archive */
  archivePerPage: number;
  /** Archive URL/path */
  archiveUrl: string;
  /** Site URL for canonical links */
  siteUrl: string;
  /** Site name for schema */
  siteName: string;
  /** Whether premium features are active */
  isPremium: boolean;
  /** Premium license tier */
  licenseTier?: 'starter' | 'professional' | 'agency';
}

/**
 * Provider configuration
 */
export interface PdfProviderConfig {
  /** Backend mode */
  mode: BackendMode;
  /** API base URL */
  apiUrl?: string;
  /** Custom fetch function */
  fetcher?: typeof fetch;
  /** Default theme */
  theme?: ViewerTheme;
  /** Site URL for SEO */
  siteUrl?: string;
  /** Site name for SEO */
  siteName?: string;
  /** Premium license key (for validation) */
  licenseKey?: string;
  /** Custom PDF.js worker URL */
  pdfjsWorkerUrl?: string;
}

/**
 * Viewer options for individual PDF viewers
 */
export interface ViewerOptions {
  /** Viewer width */
  width?: string | number;
  /** Viewer height */
  height?: string | number;
  /** Theme override */
  theme?: ViewerTheme;
  /** Show toolbar */
  showToolbar?: boolean;
  /** Show page navigation */
  showPageNav?: boolean;
  /** Show zoom controls */
  showZoom?: boolean;
  /** Initial page number */
  initialPage?: number;
  /** Initial zoom level */
  initialZoom?: number | 'auto' | 'page-fit' | 'page-width';
  /** Allow download (overrides document setting) */
  allowDownload?: boolean;
  /** Allow print (overrides document setting) */
  allowPrint?: boolean;
  /** Enable text search (premium) */
  enableSearch?: boolean;
  /** Enable bookmarks (premium) */
  enableBookmarks?: boolean;
  /** Enable reading progress tracking (premium) */
  enableProgress?: boolean;
}

/**
 * Archive options for PdfArchive component
 */
export interface ArchiveOptions {
  /** Display mode */
  view?: ArchiveDisplayMode;
  /** Number of columns in grid view */
  columns?: 1 | 2 | 3 | 4;
  /** Documents per page */
  perPage?: number;
  /** Show thumbnails */
  showThumbnails?: boolean;
  /** Show view count */
  showViewCount?: boolean;
  /** Show excerpt */
  showExcerpt?: boolean;
  /** Show pagination */
  showPagination?: boolean;
  /** Show search box */
  showSearch?: boolean;
  /** Show sort dropdown */
  showSort?: boolean;
  /** Default sort field */
  defaultSort?: 'date' | 'title' | 'views';
  /** Show category filter (premium) */
  showCategoryFilter?: boolean;
  /** Show tag filter (premium) */
  showTagFilter?: boolean;
}

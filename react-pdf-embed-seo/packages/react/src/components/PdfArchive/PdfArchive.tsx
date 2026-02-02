/**
 * PdfArchive Component
 * Archive listing with grid/list views, pagination, and filtering
 */

'use client';

import React, { useState, useEffect, useCallback, useMemo } from 'react';
import type { PdfDocument, ArchiveOptions } from '@pdf-embed-seo/core';
import { CSS_CLASSES, DEFAULT_ARCHIVE_OPTIONS } from '@pdf-embed-seo/core';
import { usePdfContext } from '../PdfProvider/PdfContext';
import { PdfCard } from './PdfCard';
import { PdfGrid } from './PdfGrid';
import { PdfList } from './PdfList';

/**
 * PdfArchive props
 */
export interface PdfArchiveProps extends ArchiveOptions {
  /** Pre-loaded documents (controlled mode) */
  documents?: PdfDocument[];
  /** API endpoint for fetching documents (uncontrolled mode) */
  apiEndpoint?: string;
  /** Callback when document is clicked */
  onDocumentClick?: (doc: PdfDocument) => void;
  /** Custom card renderer */
  renderCard?: (doc: PdfDocument) => React.ReactNode;
  /** Loading state override */
  isLoading?: boolean;
  /** Error state override */
  error?: Error | null;
  /** Additional CSS class */
  className?: string;
  /** Title for the archive */
  title?: string;
  /** Description for the archive */
  description?: string;
}

/**
 * PdfArchive Component
 *
 * @example
 * ```tsx
 * <PdfArchive
 *   apiEndpoint="/api/documents"
 *   view="grid"
 *   columns={3}
 *   perPage={9}
 *   showSearch
 * />
 * ```
 */
export function PdfArchive({
  documents: propDocuments,
  apiEndpoint,
  view = DEFAULT_ARCHIVE_OPTIONS.view,
  columns = DEFAULT_ARCHIVE_OPTIONS.columns,
  perPage = DEFAULT_ARCHIVE_OPTIONS.perPage,
  showThumbnails = DEFAULT_ARCHIVE_OPTIONS.showThumbnails,
  showViewCount = DEFAULT_ARCHIVE_OPTIONS.showViewCount,
  showExcerpt = DEFAULT_ARCHIVE_OPTIONS.showExcerpt,
  showPagination = DEFAULT_ARCHIVE_OPTIONS.showPagination,
  showSearch = DEFAULT_ARCHIVE_OPTIONS.showSearch,
  showSort = DEFAULT_ARCHIVE_OPTIONS.showSort,
  defaultSort = DEFAULT_ARCHIVE_OPTIONS.defaultSort,
  showCategoryFilter = false,
  showTagFilter = false,
  onDocumentClick,
  renderCard,
  isLoading: propIsLoading,
  error: propError,
  className = '',
  title,
  description,
}: PdfArchiveProps): React.ReactElement {
  const { apiClient, isPremium } = usePdfContext();

  // State
  const [documents, setDocuments] = useState<PdfDocument[]>(propDocuments || []);
  const [isLoading, setIsLoading] = useState(!propDocuments);
  const [error, setError] = useState<Error | null>(null);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [searchQuery, setSearchQuery] = useState('');
  const [sortBy, setSortBy] = useState(defaultSort);
  const [sortOrder, setSortOrder] = useState<'asc' | 'desc'>('desc');

  // Use prop values if provided
  const loading = propIsLoading !== undefined ? propIsLoading : isLoading;
  const displayError = propError !== undefined ? propError : error;

  // Fetch documents from API
  const fetchDocuments = useCallback(async () => {
    if (!apiClient) return;

    setIsLoading(true);
    setError(null);

    try {
      const response = await apiClient.getDocuments({
        page: currentPage,
        perPage,
        search: searchQuery || undefined,
        orderby: sortBy,
        order: sortOrder,
      });

      setDocuments(response.documents);
      setTotalPages(response.pages);
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Failed to load documents'));
    } finally {
      setIsLoading(false);
    }
  }, [apiClient, currentPage, perPage, searchQuery, sortBy, sortOrder]);

  // Fetch on mount and when filters change
  useEffect(() => {
    if (propDocuments) {
      setDocuments(propDocuments);
      return;
    }

    if (apiClient) {
      fetchDocuments();
    }
  }, [propDocuments, apiClient, fetchDocuments]);

  // Client-side filtering for controlled mode
  const filteredDocuments = useMemo(() => {
    if (!propDocuments) return documents;

    let result = [...propDocuments];

    // Apply search filter
    if (searchQuery) {
      const query = searchQuery.toLowerCase();
      result = result.filter(
        (doc) =>
          doc.title.toLowerCase().includes(query) ||
          doc.excerpt?.toLowerCase().includes(query)
      );
    }

    // Apply sorting
    result.sort((a, b) => {
      let comparison = 0;
      switch (sortBy) {
        case 'title':
          comparison = a.title.localeCompare(b.title);
          break;
        case 'views':
          comparison = a.views - b.views;
          break;
        case 'date':
        default:
          comparison = new Date(a.date).getTime() - new Date(b.date).getTime();
      }
      return sortOrder === 'asc' ? comparison : -comparison;
    });

    return result;
  }, [propDocuments, documents, searchQuery, sortBy, sortOrder]);

  // Paginate for controlled mode
  const paginatedDocuments = useMemo(() => {
    if (!propDocuments) return filteredDocuments;

    const start = (currentPage - 1) * perPage;
    return filteredDocuments.slice(start, start + perPage);
  }, [propDocuments, filteredDocuments, currentPage, perPage]);

  // Calculate total pages for controlled mode
  useEffect(() => {
    if (propDocuments) {
      setTotalPages(Math.ceil(filteredDocuments.length / perPage));
    }
  }, [propDocuments, filteredDocuments.length, perPage]);

  // Handle search
  const handleSearch = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchQuery(e.target.value);
    setCurrentPage(1);
  }, []);

  // Handle sort change
  const handleSortChange = useCallback((e: React.ChangeEvent<HTMLSelectElement>) => {
    const [field, order] = e.target.value.split('-') as [typeof sortBy, typeof sortOrder];
    setSortBy(field);
    setSortOrder(order || 'desc');
    setCurrentPage(1);
  }, []);

  // Handle page change
  const handlePageChange = useCallback((page: number) => {
    setCurrentPage(page);
  }, []);

  // Handle document click
  const handleDocumentClick = useCallback(
    (doc: PdfDocument) => {
      onDocumentClick?.(doc);
    },
    [onDocumentClick]
  );

  // Render document card
  const renderDocumentCard = useCallback(
    (doc: PdfDocument) => {
      if (renderCard) {
        return renderCard(doc);
      }

      return (
        <PdfCard
          key={doc.id}
          document={doc}
          showThumbnail={showThumbnails}
          showViewCount={showViewCount}
          showExcerpt={showExcerpt}
          onClick={() => handleDocumentClick(doc)}
        />
      );
    },
    [renderCard, showThumbnails, showViewCount, showExcerpt, handleDocumentClick]
  );

  // Premium features check
  const canShowCategoryFilter = showCategoryFilter && isPremium;
  const canShowTagFilter = showTagFilter && isPremium;

  return (
    <div className={`${CSS_CLASSES.archive.wrapper} ${className}`}>
      {title && <h1 className="pdf-archive-title">{title}</h1>}
      {description && <p className="pdf-archive-description">{description}</p>}

      {(showSearch || showSort || canShowCategoryFilter || canShowTagFilter) && (
        <div className={CSS_CLASSES.archive.filters}>
          {showSearch && (
            <div className={CSS_CLASSES.archive.search}>
              <input
                type="search"
                placeholder="Search documents..."
                value={searchQuery}
                onChange={handleSearch}
                className="pdf-search-input"
                aria-label="Search documents"
              />
            </div>
          )}

          {showSort && (
            <div className="pdf-sort-wrapper">
              <select
                value={`${sortBy}-${sortOrder}`}
                onChange={handleSortChange}
                className="pdf-sort-select"
                aria-label="Sort documents"
              >
                <option value="date-desc">Newest first</option>
                <option value="date-asc">Oldest first</option>
                <option value="title-asc">Title A-Z</option>
                <option value="title-desc">Title Z-A</option>
                <option value="views-desc">Most viewed</option>
                <option value="views-asc">Least viewed</option>
              </select>
            </div>
          )}
        </div>
      )}

      {loading && (
        <div className="pdf-archive-loading">
          <div className="pdf-archive-spinner" />
          <span>Loading documents...</span>
        </div>
      )}

      {displayError && (
        <div className="pdf-archive-error">
          <span>Error: {displayError.message}</span>
        </div>
      )}

      {!loading && !displayError && paginatedDocuments.length === 0 && (
        <div className="pdf-archive-empty">
          <span>No documents found.</span>
        </div>
      )}

      {!loading && !displayError && paginatedDocuments.length > 0 && (
        <>
          {view === 'grid' ? (
            <PdfGrid columns={columns}>
              {paginatedDocuments.map(renderDocumentCard)}
            </PdfGrid>
          ) : (
            <PdfList>{paginatedDocuments.map(renderDocumentCard)}</PdfList>
          )}

          {showPagination && totalPages > 1 && (
            <div className={CSS_CLASSES.archive.pagination}>
              <button
                type="button"
                onClick={() => handlePageChange(currentPage - 1)}
                disabled={currentPage <= 1}
                className="pdf-pagination-button pdf-pagination-prev"
                aria-label="Previous page"
              >
                Previous
              </button>

              <span className="pdf-pagination-info">
                Page {currentPage} of {totalPages}
              </span>

              <button
                type="button"
                onClick={() => handlePageChange(currentPage + 1)}
                disabled={currentPage >= totalPages}
                className="pdf-pagination-button pdf-pagination-next"
                aria-label="Next page"
              >
                Next
              </button>
            </div>
          )}
        </>
      )}
    </div>
  );
}

export default PdfArchive;

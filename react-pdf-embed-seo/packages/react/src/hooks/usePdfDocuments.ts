/**
 * usePdfDocuments Hook
 * Fetch and manage a list of PDF documents with pagination and filtering
 */

import { useState, useEffect, useCallback, useMemo } from 'react';
import type { PdfDocument, DocumentListParams } from '@pdf-embed-seo/core';
import { usePdfContext } from '../components/PdfProvider/PdfContext';

/**
 * usePdfDocuments options
 */
export interface UsePdfDocumentsOptions {
  /** Custom API endpoint */
  apiEndpoint?: string;
  /** Items per page */
  perPage?: number;
  /** Initial sort field */
  initialSort?: 'date' | 'title' | 'views' | 'modified';
  /** Initial sort order */
  initialOrder?: 'asc' | 'desc';
  /** Initial search query */
  initialSearch?: string;
  /** Whether to fetch immediately */
  immediate?: boolean;
  /** Callback on successful fetch */
  onSuccess?: (documents: PdfDocument[], total: number) => void;
  /** Callback on error */
  onError?: (error: Error) => void;
}

/**
 * Pagination state
 */
export interface PaginationState {
  page: number;
  perPage: number;
  total: number;
  totalPages: number;
  hasNextPage: boolean;
  hasPrevPage: boolean;
}

/**
 * usePdfDocuments return value
 */
export interface UsePdfDocumentsResult {
  /** List of PDF documents */
  documents: PdfDocument[];
  /** Pagination state */
  pagination: PaginationState;
  /** Whether documents are loading */
  isLoading: boolean;
  /** Error if any */
  error: Error | null;
  /** Current search query */
  search: string;
  /** Current sort field */
  sortBy: string;
  /** Current sort order */
  sortOrder: 'asc' | 'desc';
  /** Set page number */
  setPage: (page: number) => void;
  /** Set search query */
  setSearch: (query: string) => void;
  /** Set sort options */
  setSort: (field: string, order?: 'asc' | 'desc') => void;
  /** Refetch documents */
  refetch: () => Promise<void>;
  /** Go to next page */
  nextPage: () => void;
  /** Go to previous page */
  prevPage: () => void;
}

/**
 * usePdfDocuments Hook
 *
 * @example
 * ```tsx
 * function ArchivePage() {
 *   const {
 *     documents,
 *     pagination,
 *     isLoading,
 *     search,
 *     setSearch,
 *     setPage,
 *   } = usePdfDocuments({ perPage: 12 });
 *
 *   return (
 *     <div>
 *       <SearchInput value={search} onChange={setSearch} />
 *       <PdfGrid documents={documents} />
 *       <Pagination {...pagination} onPageChange={setPage} />
 *     </div>
 *   );
 * }
 * ```
 */
export function usePdfDocuments(
  options: UsePdfDocumentsOptions = {}
): UsePdfDocumentsResult {
  const {
    perPage = 10,
    initialSort = 'date',
    initialOrder = 'desc',
    initialSearch = '',
    immediate = true,
    onSuccess,
    onError,
  } = options;

  const { apiClient } = usePdfContext();

  // State
  const [documents, setDocuments] = useState<PdfDocument[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [totalPages, setTotalPages] = useState(0);
  const [search, setSearch] = useState(initialSearch);
  const [sortBy, setSortBy] = useState(initialSort);
  const [sortOrder, setSortOrder] = useState<'asc' | 'desc'>(initialOrder);

  // Pagination state
  const pagination: PaginationState = useMemo(
    () => ({
      page,
      perPage,
      total,
      totalPages,
      hasNextPage: page < totalPages,
      hasPrevPage: page > 1,
    }),
    [page, perPage, total, totalPages]
  );

  // Fetch documents
  const fetchDocuments = useCallback(async () => {
    if (!apiClient) return;

    setIsLoading(true);
    setError(null);

    try {
      const params: DocumentListParams = {
        page,
        perPage,
        orderby: sortBy as DocumentListParams['orderby'],
        order: sortOrder,
      };

      if (search) {
        params.search = search;
      }

      const response = await apiClient.getDocuments(params);

      setDocuments(response.documents);
      setTotal(response.total);
      setTotalPages(response.pages);

      onSuccess?.(response.documents, response.total);
    } catch (err) {
      const error = err instanceof Error ? err : new Error('Failed to fetch documents');
      setError(error);
      onError?.(error);
    } finally {
      setIsLoading(false);
    }
  }, [apiClient, page, perPage, search, sortBy, sortOrder, onSuccess, onError]);

  // Fetch on mount or when params change
  useEffect(() => {
    if (immediate) {
      fetchDocuments();
    }
  }, [immediate, fetchDocuments]);

  // Reset to page 1 when search or sort changes
  useEffect(() => {
    setPage(1);
  }, [search, sortBy, sortOrder]);

  // Set search handler
  const handleSetSearch = useCallback((query: string) => {
    setSearch(query);
  }, []);

  // Set sort handler
  const handleSetSort = useCallback((field: string, order?: 'asc' | 'desc') => {
    setSortBy(field);
    if (order) {
      setSortOrder(order);
    }
  }, []);

  // Page navigation
  const nextPage = useCallback(() => {
    if (page < totalPages) {
      setPage((p) => p + 1);
    }
  }, [page, totalPages]);

  const prevPage = useCallback(() => {
    if (page > 1) {
      setPage((p) => p - 1);
    }
  }, [page]);

  return {
    documents,
    pagination,
    isLoading,
    error,
    search,
    sortBy,
    sortOrder,
    setPage,
    setSearch: handleSetSearch,
    setSort: handleSetSort,
    refetch: fetchDocuments,
    nextPage,
    prevPage,
  };
}

export default usePdfDocuments;

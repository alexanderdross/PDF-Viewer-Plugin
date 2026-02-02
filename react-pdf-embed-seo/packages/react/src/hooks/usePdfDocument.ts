/**
 * usePdfDocument Hook
 * Fetch and manage a single PDF document
 */

import { useState, useEffect, useCallback } from 'react';
import type { PdfDocument } from '@pdf-embed-seo/core';
import { usePdfContext } from '../components/PdfProvider/PdfContext';

/**
 * usePdfDocument options
 */
export interface UsePdfDocumentOptions {
  /** Custom API endpoint */
  apiEndpoint?: string;
  /** Whether to fetch immediately */
  immediate?: boolean;
  /** Callback on successful fetch */
  onSuccess?: (document: PdfDocument) => void;
  /** Callback on error */
  onError?: (error: Error) => void;
}

/**
 * usePdfDocument return value
 */
export interface UsePdfDocumentResult {
  /** The PDF document data */
  document: PdfDocument | null;
  /** Whether the document is loading */
  isLoading: boolean;
  /** Error if any */
  error: Error | null;
  /** Refetch the document */
  refetch: () => Promise<void>;
}

/**
 * usePdfDocument Hook
 *
 * @example
 * ```tsx
 * function PdfPage({ id }) {
 *   const { document, isLoading, error } = usePdfDocument(id);
 *
 *   if (isLoading) return <Loading />;
 *   if (error) return <Error error={error} />;
 *   if (!document) return <NotFound />;
 *
 *   return <PdfViewer src={document} />;
 * }
 * ```
 */
export function usePdfDocument(
  id: string | number | undefined,
  options: UsePdfDocumentOptions = {}
): UsePdfDocumentResult {
  const { immediate = true, onSuccess, onError } = options;
  const { apiClient } = usePdfContext();

  const [document, setDocument] = useState<PdfDocument | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const fetchDocument = useCallback(async () => {
    if (!id || !apiClient) return;

    setIsLoading(true);
    setError(null);

    try {
      const doc = await apiClient.getDocument(id);
      setDocument(doc);
      onSuccess?.(doc);
    } catch (err) {
      const error = err instanceof Error ? err : new Error('Failed to fetch document');
      setError(error);
      onError?.(error);
    } finally {
      setIsLoading(false);
    }
  }, [id, apiClient, onSuccess, onError]);

  // Fetch on mount or when id changes
  useEffect(() => {
    if (immediate && id) {
      fetchDocument();
    }
  }, [immediate, id, fetchDocument]);

  return {
    document,
    isLoading,
    error,
    refetch: fetchDocument,
  };
}

/**
 * usePdfDocumentBySlug Hook
 * Fetch document by slug instead of ID
 */
export function usePdfDocumentBySlug(
  slug: string | undefined,
  options: UsePdfDocumentOptions = {}
): UsePdfDocumentResult {
  const { immediate = true, onSuccess, onError } = options;
  const { apiClient } = usePdfContext();

  const [document, setDocument] = useState<PdfDocument | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const fetchDocument = useCallback(async () => {
    if (!slug || !apiClient) return;

    setIsLoading(true);
    setError(null);

    try {
      const doc = await apiClient.getDocumentBySlug(slug);
      setDocument(doc);
      onSuccess?.(doc);
    } catch (err) {
      const error = err instanceof Error ? err : new Error('Failed to fetch document');
      setError(error);
      onError?.(error);
    } finally {
      setIsLoading(false);
    }
  }, [slug, apiClient, onSuccess, onError]);

  useEffect(() => {
    if (immediate && slug) {
      fetchDocument();
    }
  }, [immediate, slug, fetchDocument]);

  return {
    document,
    isLoading,
    error,
    refetch: fetchDocument,
  };
}

export default usePdfDocument;

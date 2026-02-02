/**
 * useReadingProgress Hook
 * Track and restore reading progress for PDF documents
 */

import { useState, useCallback, useEffect, useRef } from 'react';
import type { PdfDocument, ReadingProgress } from '@pdf-embed-seo/core';
import { usePdfContext } from '@pdf-embed-seo/react';

/**
 * useReadingProgress options
 */
export interface UseReadingProgressOptions {
  /** Auto-save interval in ms */
  autoSaveInterval?: number;
  /** Use local storage as fallback */
  useLocalStorage?: boolean;
  /** Storage key prefix */
  storagePrefix?: string;
}

/**
 * useReadingProgress return value
 */
export interface UseReadingProgressResult {
  /** Current progress */
  progress: ReadingProgress | null;
  /** Percentage complete (0-100) */
  percentComplete: number;
  /** Whether progress was restored */
  wasRestored: boolean;
  /** Save current progress */
  saveProgress: (data: { page: number; scroll?: number; zoom?: number }) => Promise<void>;
  /** Clear saved progress */
  clearProgress: () => void;
  /** Loading state */
  isLoading: boolean;
}

/**
 * useReadingProgress Hook
 *
 * @example
 * ```tsx
 * function PdfWithProgress({ document }) {
 *   const {
 *     progress,
 *     percentComplete,
 *     saveProgress,
 *   } = useReadingProgress(document);
 *
 *   return (
 *     <>
 *       <PdfProgressBar progress={percentComplete} />
 *       <PdfViewer
 *         src={document}
 *         initialPage={progress?.page || 1}
 *         onPageChange={(page) => saveProgress({ page })}
 *       />
 *     </>
 *   );
 * }
 * ```
 */
export function useReadingProgress(
  document: PdfDocument | null | undefined,
  options: UseReadingProgressOptions = {}
): UseReadingProgressResult {
  const {
    autoSaveInterval = 5000,
    useLocalStorage = true,
    storagePrefix = 'pdf_progress_',
  } = options;

  const { apiClient, isPremium } = usePdfContext();

  const [progress, setProgress] = useState<ReadingProgress | null>(null);
  const [wasRestored, setWasRestored] = useState(false);
  const [isLoading, setIsLoading] = useState(true);

  const storageKey = document ? `${storagePrefix}${document.id}` : '';
  const pendingProgress = useRef<{ page: number; scroll?: number; zoom?: number } | null>(null);
  const saveTimeoutRef = useRef<NodeJS.Timeout | null>(null);

  // Calculate percent complete
  const percentComplete = progress && document?.pageCount
    ? Math.round((progress.page / document.pageCount) * 100)
    : 0;

  // Load progress on mount
  useEffect(() => {
    if (!document) {
      setIsLoading(false);
      return;
    }

    async function loadProgress() {
      setIsLoading(true);

      try {
        // Try API first if premium
        if (apiClient && isPremium) {
          const response = await apiClient.getProgress(document!.id);
          if (response.progress) {
            setProgress(response.progress);
            setWasRestored(true);
            setIsLoading(false);
            return;
          }
        }

        // Fall back to local storage
        if (useLocalStorage) {
          const stored = localStorage.getItem(storageKey);
          if (stored) {
            const parsed = JSON.parse(stored) as ReadingProgress;
            setProgress(parsed);
            setWasRestored(true);
          }
        }
      } catch (err) {
        console.error('Failed to load reading progress:', err);
      } finally {
        setIsLoading(false);
      }
    }

    loadProgress();
  }, [document, apiClient, isPremium, useLocalStorage, storageKey]);

  // Save progress
  const saveProgress = useCallback(
    async (data: { page: number; scroll?: number; zoom?: number }) => {
      if (!document) return;

      // Update pending progress
      pendingProgress.current = data;

      // Create progress object
      const newProgress: ReadingProgress = {
        documentId: document.id,
        page: data.page,
        scroll: data.scroll,
        zoom: data.zoom,
        lastRead: new Date().toISOString(),
        percentComplete: document.pageCount
          ? Math.round((data.page / document.pageCount) * 100)
          : undefined,
      };

      setProgress(newProgress);

      // Save to local storage immediately
      if (useLocalStorage) {
        localStorage.setItem(storageKey, JSON.stringify(newProgress));
      }

      // Debounce API save
      if (saveTimeoutRef.current) {
        clearTimeout(saveTimeoutRef.current);
      }

      if (apiClient && isPremium) {
        saveTimeoutRef.current = setTimeout(async () => {
          try {
            await apiClient.saveProgress(document.id, data);
          } catch (err) {
            console.error('Failed to save progress to API:', err);
          }
        }, autoSaveInterval);
      }
    },
    [document, apiClient, isPremium, useLocalStorage, storageKey, autoSaveInterval]
  );

  // Clear progress
  const clearProgress = useCallback(() => {
    setProgress(null);
    setWasRestored(false);

    if (useLocalStorage) {
      localStorage.removeItem(storageKey);
    }
  }, [useLocalStorage, storageKey]);

  // Cleanup on unmount
  useEffect(() => {
    return () => {
      if (saveTimeoutRef.current) {
        clearTimeout(saveTimeoutRef.current);
      }
    };
  }, []);

  return {
    progress,
    percentComplete,
    wasRestored,
    saveProgress,
    clearProgress,
    isLoading,
  };
}

export default useReadingProgress;

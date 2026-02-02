/**
 * usePdfViewer Hook
 * Manage PDF viewer state
 */

import { useState, useCallback, useRef, RefObject } from 'react';

/**
 * usePdfViewer options
 */
export interface UsePdfViewerOptions {
  /** Initial page */
  initialPage?: number;
  /** Initial zoom */
  initialZoom?: number;
  /** Minimum zoom */
  minZoom?: number;
  /** Maximum zoom */
  maxZoom?: number;
  /** Zoom step multiplier */
  zoomStep?: number;
}

/**
 * usePdfViewer return value
 */
export interface UsePdfViewerResult {
  /** Ref to attach to viewer container */
  viewerRef: RefObject<HTMLDivElement>;
  /** Current page number */
  currentPage: number;
  /** Total pages */
  totalPages: number;
  /** Current zoom level */
  zoom: number;
  /** Whether viewer is in fullscreen */
  isFullscreen: boolean;
  /** Set current page */
  setPage: (page: number) => void;
  /** Go to next page */
  nextPage: () => void;
  /** Go to previous page */
  prevPage: () => void;
  /** Set zoom level */
  setZoom: (zoom: number) => void;
  /** Zoom in */
  zoomIn: () => void;
  /** Zoom out */
  zoomOut: () => void;
  /** Reset zoom */
  resetZoom: () => void;
  /** Toggle fullscreen */
  toggleFullscreen: () => void;
  /** Set total pages (usually set by viewer on load) */
  setTotalPages: (pages: number) => void;
}

/**
 * usePdfViewer Hook
 *
 * @example
 * ```tsx
 * function CustomViewer({ src }) {
 *   const {
 *     viewerRef,
 *     currentPage,
 *     totalPages,
 *     zoom,
 *     nextPage,
 *     prevPage,
 *     zoomIn,
 *     zoomOut,
 *   } = usePdfViewer({ initialPage: 1, initialZoom: 1 });
 *
 *   return (
 *     <div ref={viewerRef}>
 *       <PdfViewer src={src} />
 *       <button onClick={prevPage}>Prev</button>
 *       <span>{currentPage} / {totalPages}</span>
 *       <button onClick={nextPage}>Next</button>
 *       <button onClick={zoomIn}>+</button>
 *       <button onClick={zoomOut}>-</button>
 *     </div>
 *   );
 * }
 * ```
 */
export function usePdfViewer(options: UsePdfViewerOptions = {}): UsePdfViewerResult {
  const {
    initialPage = 1,
    initialZoom = 1,
    minZoom = 0.25,
    maxZoom = 5,
    zoomStep = 1.25,
  } = options;

  const viewerRef = useRef<HTMLDivElement>(null);

  // State
  const [currentPage, setCurrentPage] = useState(initialPage);
  const [totalPages, setTotalPages] = useState(0);
  const [zoom, setZoomState] = useState(initialZoom);
  const [isFullscreen, setIsFullscreen] = useState(false);

  // Set page with bounds checking
  const setPage = useCallback(
    (page: number) => {
      const newPage = Math.max(1, Math.min(page, totalPages || page));
      setCurrentPage(newPage);
    },
    [totalPages]
  );

  // Page navigation
  const nextPage = useCallback(() => {
    if (currentPage < totalPages) {
      setCurrentPage((p) => p + 1);
    }
  }, [currentPage, totalPages]);

  const prevPage = useCallback(() => {
    if (currentPage > 1) {
      setCurrentPage((p) => p - 1);
    }
  }, [currentPage]);

  // Set zoom with bounds checking
  const setZoom = useCallback(
    (newZoom: number) => {
      const clampedZoom = Math.max(minZoom, Math.min(maxZoom, newZoom));
      setZoomState(clampedZoom);
    },
    [minZoom, maxZoom]
  );

  // Zoom controls
  const zoomIn = useCallback(() => {
    setZoom(zoom * zoomStep);
  }, [zoom, zoomStep, setZoom]);

  const zoomOut = useCallback(() => {
    setZoom(zoom / zoomStep);
  }, [zoom, zoomStep, setZoom]);

  const resetZoom = useCallback(() => {
    setZoomState(1);
  }, []);

  // Fullscreen toggle
  const toggleFullscreen = useCallback(() => {
    const container = viewerRef.current;
    if (!container) return;

    if (!isFullscreen) {
      if (container.requestFullscreen) {
        container.requestFullscreen();
      }
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      }
    }
    setIsFullscreen(!isFullscreen);
  }, [isFullscreen]);

  return {
    viewerRef,
    currentPage,
    totalPages,
    zoom,
    isFullscreen,
    setPage,
    nextPage,
    prevPage,
    setZoom,
    zoomIn,
    zoomOut,
    resetZoom,
    toggleFullscreen,
    setTotalPages,
  };
}

export default usePdfViewer;

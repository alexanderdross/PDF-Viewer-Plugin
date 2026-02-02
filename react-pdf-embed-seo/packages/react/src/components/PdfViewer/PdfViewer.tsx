/**
 * PdfViewer Component
 * Main PDF viewer using Mozilla PDF.js
 */

'use client';

import React, { useRef, useEffect, useState, useCallback, forwardRef } from 'react';
import type { PdfDocument, PdfDocumentInfo, ViewerTheme } from '@pdf-embed-seo/core';
import { loadPdfDocument, getPdfInfo, CSS_CLASSES } from '@pdf-embed-seo/core';
import { usePdfContext } from '../PdfProvider/PdfContext';
import { PdfToolbar } from './PdfToolbar';
import { PdfPageNav } from './PdfPageNav';
import { PdfZoomControls } from './PdfZoomControls';

/**
 * PdfViewer props
 */
export interface PdfViewerProps {
  /** PDF source - URL string or PdfDocument object */
  src: string | PdfDocument;
  /** Viewer width */
  width?: string | number;
  /** Viewer height */
  height?: string | number;
  /** Allow download */
  allowDownload?: boolean;
  /** Allow print */
  allowPrint?: boolean;
  /** Show toolbar */
  showToolbar?: boolean;
  /** Show page navigation */
  showPageNav?: boolean;
  /** Show zoom controls */
  showZoom?: boolean;
  /** Theme override */
  theme?: ViewerTheme;
  /** Initial page number */
  initialPage?: number;
  /** Initial zoom level */
  initialZoom?: number | 'auto' | 'page-fit' | 'page-width';
  /** Callback when document loads */
  onDocumentLoad?: (info: PdfDocumentInfo) => void;
  /** Callback when page changes */
  onPageChange?: (page: number) => void;
  /** Callback when zoom changes */
  onZoomChange?: (zoom: number) => void;
  /** Callback on error */
  onError?: (error: Error) => void;
  /** Enable search (premium) */
  enableSearch?: boolean;
  /** Enable bookmarks (premium) */
  enableBookmarks?: boolean;
  /** Enable progress tracking (premium) */
  enableProgress?: boolean;
  /** Additional CSS class */
  className?: string;
  /** Inline styles */
  style?: React.CSSProperties;
}

/**
 * PdfViewer Component
 *
 * @example
 * ```tsx
 * <PdfViewer
 *   src={document}
 *   height="600px"
 *   allowDownload={false}
 *   theme="dark"
 *   onPageChange={(page) => console.log(`Page: ${page}`)}
 * />
 * ```
 */
export const PdfViewer = forwardRef<HTMLDivElement, PdfViewerProps>(
  function PdfViewer(
    {
      src,
      width = '100%',
      height = '800px',
      allowDownload = true,
      allowPrint = true,
      showToolbar = true,
      showPageNav = true,
      showZoom = true,
      theme: themeProp,
      initialPage = 1,
      initialZoom = 'auto',
      onDocumentLoad,
      onPageChange,
      onZoomChange,
      onError,
      enableSearch = false,
      enableBookmarks = false,
      enableProgress = false,
      className = '',
      style = {},
    },
    ref
  ) {
    // Get context values
    const { theme: contextTheme, isPremium, apiClient } = usePdfContext();

    // Refs
    const containerRef = useRef<HTMLDivElement>(null);
    const canvasRef = useRef<HTMLCanvasElement>(null);
    const pdfDocRef = useRef<unknown>(null);

    // State
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<Error | null>(null);
    const [currentPage, setCurrentPage] = useState(initialPage);
    const [totalPages, setTotalPages] = useState(0);
    const [zoom, setZoom] = useState(typeof initialZoom === 'number' ? initialZoom : 1);
    const [isFullscreen, setIsFullscreen] = useState(false);

    // Determine effective theme
    const effectiveTheme = themeProp || contextTheme;
    const resolvedTheme =
      effectiveTheme === 'system'
        ? typeof window !== 'undefined' &&
          window.matchMedia('(prefers-color-scheme: dark)').matches
          ? 'dark'
          : 'light'
        : effectiveTheme;

    // Get PDF URL
    const pdfUrl = typeof src === 'string' ? src : src.pdfUrl || '';

    // Check if premium features are allowed
    const canUseSearch = enableSearch && isPremium;
    const canUseBookmarks = enableBookmarks && isPremium;
    const canUseProgress = enableProgress && isPremium;

    // Load PDF document
    useEffect(() => {
      if (!pdfUrl) {
        setError(new Error('No PDF source provided'));
        setIsLoading(false);
        return;
      }

      let mounted = true;

      async function loadPdf() {
        try {
          setIsLoading(true);
          setError(null);

          const doc = await loadPdfDocument(pdfUrl);
          if (!mounted) {
            doc.destroy?.();
            return;
          }

          pdfDocRef.current = doc;
          const info = await getPdfInfo(doc);

          setTotalPages(info.numPages);
          setCurrentPage(Math.min(initialPage, info.numPages));

          // Track view
          if (apiClient && typeof src !== 'string') {
            apiClient.trackView(src.id).catch(() => {});
          }

          onDocumentLoad?.(info);
          setIsLoading(false);

          // Render first page
          await renderPage(currentPage);
        } catch (err) {
          if (!mounted) return;
          const error = err instanceof Error ? err : new Error('Failed to load PDF');
          setError(error);
          onError?.(error);
          setIsLoading(false);
        }
      }

      loadPdf();

      return () => {
        mounted = false;
        const doc = pdfDocRef.current as { destroy?: () => void } | null;
        doc?.destroy?.();
      };
    }, [pdfUrl]);

    // Render a page
    const renderPage = useCallback(
      async (pageNum: number) => {
        const doc = pdfDocRef.current as {
          getPage: (num: number) => Promise<unknown>;
        } | null;
        const canvas = canvasRef.current;
        if (!doc || !canvas) return;

        try {
          const page = (await doc.getPage(pageNum)) as {
            getViewport: (params: { scale: number }) => {
              width: number;
              height: number;
              scale: number;
            };
            render: (params: {
              canvasContext: CanvasRenderingContext2D;
              viewport: unknown;
            }) => { promise: Promise<void> };
          };
          const viewport = page.getViewport({ scale: zoom });

          const context = canvas.getContext('2d');
          if (!context) return;

          canvas.width = viewport.width;
          canvas.height = viewport.height;

          await page.render({
            canvasContext: context,
            viewport,
          }).promise;
        } catch (err) {
          console.error('Error rendering page:', err);
        }
      },
      [zoom]
    );

    // Re-render on page or zoom change
    useEffect(() => {
      if (!isLoading && pdfDocRef.current) {
        renderPage(currentPage);
      }
    }, [currentPage, zoom, isLoading, renderPage]);

    // Page navigation handlers
    const goToPage = useCallback(
      (page: number) => {
        const newPage = Math.max(1, Math.min(page, totalPages));
        setCurrentPage(newPage);
        onPageChange?.(newPage);
      },
      [totalPages, onPageChange]
    );

    const nextPage = useCallback(() => {
      if (currentPage < totalPages) {
        goToPage(currentPage + 1);
      }
    }, [currentPage, totalPages, goToPage]);

    const prevPage = useCallback(() => {
      if (currentPage > 1) {
        goToPage(currentPage - 1);
      }
    }, [currentPage, goToPage]);

    // Zoom handlers
    const handleZoomChange = useCallback(
      (newZoom: number) => {
        setZoom(newZoom);
        onZoomChange?.(newZoom);
      },
      [onZoomChange]
    );

    const zoomIn = useCallback(() => {
      handleZoomChange(Math.min(zoom * 1.25, 5));
    }, [zoom, handleZoomChange]);

    const zoomOut = useCallback(() => {
      handleZoomChange(Math.max(zoom / 1.25, 0.25));
    }, [zoom, handleZoomChange]);

    // Fullscreen toggle
    const toggleFullscreen = useCallback(() => {
      const container = containerRef.current;
      if (!container) return;

      if (!isFullscreen) {
        container.requestFullscreen?.();
      } else {
        document.exitFullscreen?.();
      }
      setIsFullscreen(!isFullscreen);
    }, [isFullscreen]);

    // Download handler
    const handleDownload = useCallback(() => {
      if (!allowDownload) return;
      const url = typeof src === 'string' ? src : src.pdfUrl;
      if (!url) return;

      const link = document.createElement('a');
      link.href = url;
      link.download = typeof src === 'string' ? 'document.pdf' : `${src.slug}.pdf`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      // Track download
      if (apiClient && typeof src !== 'string' && isPremium) {
        apiClient.trackDownload?.(src.id).catch(() => {});
      }
    }, [src, allowDownload, apiClient, isPremium]);

    // Print handler
    const handlePrint = useCallback(() => {
      if (!allowPrint) return;
      window.print();
    }, [allowPrint]);

    // CSS classes
    const themeClass =
      resolvedTheme === 'dark'
        ? CSS_CLASSES.viewer.themeDark
        : CSS_CLASSES.viewer.themeLight;

    // Styles
    const containerStyle: React.CSSProperties = {
      width: typeof width === 'number' ? `${width}px` : width,
      height: typeof height === 'number' ? `${height}px` : height,
      ...style,
    };

    return (
      <div
        ref={(node) => {
          (containerRef as React.MutableRefObject<HTMLDivElement | null>).current = node;
          if (typeof ref === 'function') {
            ref(node);
          } else if (ref) {
            ref.current = node;
          }
        }}
        className={`${CSS_CLASSES.viewer.wrapper} ${themeClass} ${className}`}
        style={containerStyle}
      >
        {showToolbar && (
          <PdfToolbar
            allowDownload={allowDownload}
            allowPrint={allowPrint}
            isFullscreen={isFullscreen}
            onDownload={handleDownload}
            onPrint={handlePrint}
            onFullscreen={toggleFullscreen}
            enableSearch={canUseSearch}
            enableBookmarks={canUseBookmarks}
          />
        )}

        <div className={CSS_CLASSES.viewer.container}>
          {isLoading && (
            <div className="pdf-viewer-loading">
              <div className="pdf-viewer-spinner" />
              <span>Loading PDF...</span>
            </div>
          )}

          {error && (
            <div className="pdf-viewer-error">
              <span>Error: {error.message}</span>
            </div>
          )}

          {!isLoading && !error && (
            <canvas
              ref={canvasRef}
              className={CSS_CLASSES.viewer.canvas}
            />
          )}
        </div>

        <div className={CSS_CLASSES.viewer.controls}>
          {showPageNav && (
            <PdfPageNav
              currentPage={currentPage}
              totalPages={totalPages}
              onPageChange={goToPage}
              onPrevPage={prevPage}
              onNextPage={nextPage}
            />
          )}

          {showZoom && (
            <PdfZoomControls
              zoom={zoom}
              onZoomChange={handleZoomChange}
              onZoomIn={zoomIn}
              onZoomOut={zoomOut}
            />
          )}
        </div>
      </div>
    );
  }
);

export default PdfViewer;

/**
 * PdfToolbar Component
 * Toolbar with download, print, fullscreen, and premium controls
 */

'use client';

import React from 'react';
import { CSS_CLASSES } from '@pdf-embed-seo/core';

/**
 * PdfToolbar props
 */
export interface PdfToolbarProps {
  /** Allow download */
  allowDownload?: boolean;
  /** Allow print */
  allowPrint?: boolean;
  /** Is fullscreen active */
  isFullscreen?: boolean;
  /** Download handler */
  onDownload?: () => void;
  /** Print handler */
  onPrint?: () => void;
  /** Fullscreen toggle handler */
  onFullscreen?: () => void;
  /** Enable search (premium) */
  enableSearch?: boolean;
  /** Enable bookmarks (premium) */
  enableBookmarks?: boolean;
  /** Search handler (premium) */
  onSearch?: () => void;
  /** Bookmarks handler (premium) */
  onBookmarks?: () => void;
}

/**
 * PdfToolbar Component
 */
export function PdfToolbar({
  allowDownload = true,
  allowPrint = true,
  isFullscreen = false,
  onDownload,
  onPrint,
  onFullscreen,
  enableSearch = false,
  enableBookmarks = false,
  onSearch,
  onBookmarks,
}: PdfToolbarProps): React.ReactElement {
  return (
    <div className={CSS_CLASSES.viewer.toolbar}>
      <div className="pdf-toolbar-left">
        {enableSearch && (
          <button
            type="button"
            className="pdf-toolbar-button"
            onClick={onSearch}
            title="Search in document"
            aria-label="Search in document"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
            >
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.3-4.3" />
            </svg>
          </button>
        )}

        {enableBookmarks && (
          <button
            type="button"
            className="pdf-toolbar-button"
            onClick={onBookmarks}
            title="Bookmarks"
            aria-label="Show bookmarks"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
            >
              <path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z" />
            </svg>
          </button>
        )}
      </div>

      <div className="pdf-toolbar-right">
        {allowDownload && (
          <button
            type="button"
            className="pdf-toolbar-button pdf-download-button"
            onClick={onDownload}
            title="Download PDF"
            aria-label="Download PDF"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
            >
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
              <polyline points="7 10 12 15 17 10" />
              <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
          </button>
        )}

        {allowPrint && (
          <button
            type="button"
            className="pdf-toolbar-button"
            onClick={onPrint}
            title="Print PDF"
            aria-label="Print PDF"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
            >
              <polyline points="6 9 6 2 18 2 18 9" />
              <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
              <rect x="6" y="14" width="12" height="8" />
            </svg>
          </button>
        )}

        <button
          type="button"
          className="pdf-toolbar-button"
          onClick={onFullscreen}
          title={isFullscreen ? 'Exit fullscreen' : 'Fullscreen'}
          aria-label={isFullscreen ? 'Exit fullscreen' : 'Enter fullscreen'}
        >
          {isFullscreen ? (
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
            >
              <path d="M8 3v3a2 2 0 0 1-2 2H3" />
              <path d="M21 8h-3a2 2 0 0 1-2-2V3" />
              <path d="M3 16h3a2 2 0 0 1 2 2v3" />
              <path d="M16 21v-3a2 2 0 0 1 2-2h3" />
            </svg>
          ) : (
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
            >
              <path d="M8 3H5a2 2 0 0 0-2 2v3" />
              <path d="M21 8V5a2 2 0 0 0-2-2h-3" />
              <path d="M3 16v3a2 2 0 0 0 2 2h3" />
              <path d="M16 21h3a2 2 0 0 0 2-2v-3" />
            </svg>
          )}
        </button>
      </div>
    </div>
  );
}

export default PdfToolbar;

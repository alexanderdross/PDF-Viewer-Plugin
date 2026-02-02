/**
 * PdfPageNav Component
 * Page navigation controls for the PDF viewer
 */

'use client';

import React, { useState, useCallback } from 'react';

/**
 * PdfPageNav props
 */
export interface PdfPageNavProps {
  /** Current page number */
  currentPage: number;
  /** Total pages */
  totalPages: number;
  /** Page change handler */
  onPageChange: (page: number) => void;
  /** Previous page handler */
  onPrevPage: () => void;
  /** Next page handler */
  onNextPage: () => void;
}

/**
 * PdfPageNav Component
 */
export function PdfPageNav({
  currentPage,
  totalPages,
  onPageChange,
  onPrevPage,
  onNextPage,
}: PdfPageNavProps): React.ReactElement {
  const [inputValue, setInputValue] = useState(currentPage.toString());

  // Update input when page changes externally
  React.useEffect(() => {
    setInputValue(currentPage.toString());
  }, [currentPage]);

  // Handle input change
  const handleInputChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      setInputValue(e.target.value);
    },
    []
  );

  // Handle input blur or enter
  const handleInputSubmit = useCallback(() => {
    const page = parseInt(inputValue, 10);
    if (!isNaN(page) && page >= 1 && page <= totalPages) {
      onPageChange(page);
    } else {
      setInputValue(currentPage.toString());
    }
  }, [inputValue, totalPages, currentPage, onPageChange]);

  // Handle keydown
  const handleKeyDown = useCallback(
    (e: React.KeyboardEvent<HTMLInputElement>) => {
      if (e.key === 'Enter') {
        handleInputSubmit();
      }
    },
    [handleInputSubmit]
  );

  return (
    <div className="pdf-page-nav">
      <button
        type="button"
        className="pdf-nav-button pdf-nav-prev"
        onClick={onPrevPage}
        disabled={currentPage <= 1}
        title="Previous page"
        aria-label="Go to previous page"
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
          <polyline points="15 18 9 12 15 6" />
        </svg>
      </button>

      <div className="pdf-page-input-wrapper">
        <input
          type="text"
          className="pdf-page-input"
          value={inputValue}
          onChange={handleInputChange}
          onBlur={handleInputSubmit}
          onKeyDown={handleKeyDown}
          aria-label="Current page"
          style={{ width: `${Math.max(2, inputValue.length)}ch` }}
        />
        <span className="pdf-page-separator">/</span>
        <span className="pdf-total-pages">{totalPages}</span>
      </div>

      <button
        type="button"
        className="pdf-nav-button pdf-nav-next"
        onClick={onNextPage}
        disabled={currentPage >= totalPages}
        title="Next page"
        aria-label="Go to next page"
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
          <polyline points="9 18 15 12 9 6" />
        </svg>
      </button>
    </div>
  );
}

export default PdfPageNav;

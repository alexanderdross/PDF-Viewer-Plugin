/**
 * PdfSearchBar Component
 * Search bar for text search within PDF documents
 */

'use client';

import React, { useState, useCallback, useRef, useEffect } from 'react';
import { CSS_CLASSES } from '@pdf-embed-seo/core';

/**
 * Search result
 */
export interface PdfSearchResult {
  pageIndex: number;
  matchIndex: number;
  text: string;
}

/**
 * PdfSearchBar props
 */
export interface PdfSearchBarProps {
  /** Search handler */
  onSearch: (query: string) => void;
  /** Navigate to result */
  onNavigate?: (result: PdfSearchResult) => void;
  /** Clear search */
  onClear?: () => void;
  /** Search results */
  results?: PdfSearchResult[];
  /** Current result index */
  currentIndex?: number;
  /** Loading state */
  isSearching?: boolean;
  /** Placeholder text */
  placeholder?: string;
  /** Additional CSS class */
  className?: string;
}

/**
 * PdfSearchBar Component
 */
export function PdfSearchBar({
  onSearch,
  onNavigate,
  onClear,
  results = [],
  currentIndex = 0,
  isSearching = false,
  placeholder = 'Search in document...',
  className = '',
}: PdfSearchBarProps): React.ReactElement {
  const [query, setQuery] = useState('');
  const inputRef = useRef<HTMLInputElement>(null);

  const handleChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      const value = e.target.value;
      setQuery(value);
      onSearch(value);
    },
    [onSearch]
  );

  const handleClear = useCallback(() => {
    setQuery('');
    onClear?.();
    inputRef.current?.focus();
  }, [onClear]);

  const handlePrev = useCallback(() => {
    if (results.length > 0 && currentIndex > 0) {
      onNavigate?.(results[currentIndex - 1]);
    }
  }, [results, currentIndex, onNavigate]);

  const handleNext = useCallback(() => {
    if (results.length > 0 && currentIndex < results.length - 1) {
      onNavigate?.(results[currentIndex + 1]);
    }
  }, [results, currentIndex, onNavigate]);

  // Keyboard shortcuts
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Enter' && e.shiftKey) {
        handlePrev();
      } else if (e.key === 'Enter') {
        handleNext();
      } else if (e.key === 'Escape') {
        handleClear();
      }
    };

    const input = inputRef.current;
    input?.addEventListener('keydown', handleKeyDown);
    return () => input?.removeEventListener('keydown', handleKeyDown);
  }, [handlePrev, handleNext, handleClear]);

  return (
    <div className={`${CSS_CLASSES.premium.searchBar} ${className}`}>
      <div className="pdf-search-input-wrapper">
        <svg
          className="pdf-search-icon"
          xmlns="http://www.w3.org/2000/svg"
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
        >
          <circle cx="11" cy="11" r="8" />
          <path d="m21 21-4.3-4.3" />
        </svg>

        <input
          ref={inputRef}
          type="search"
          value={query}
          onChange={handleChange}
          placeholder={placeholder}
          className="pdf-search-input"
          aria-label="Search in document"
        />

        {query && (
          <button
            type="button"
            onClick={handleClear}
            className="pdf-search-clear"
            aria-label="Clear search"
          >
            ×
          </button>
        )}
      </div>

      {query && (
        <div className="pdf-search-results-info">
          {isSearching ? (
            <span className="pdf-search-loading">Searching...</span>
          ) : results.length > 0 ? (
            <>
              <span className="pdf-search-count">
                {currentIndex + 1} of {results.length}
              </span>
              <div className="pdf-search-nav">
                <button
                  onClick={handlePrev}
                  disabled={currentIndex === 0}
                  className="pdf-search-nav-button"
                  aria-label="Previous result"
                >
                  ↑
                </button>
                <button
                  onClick={handleNext}
                  disabled={currentIndex >= results.length - 1}
                  className="pdf-search-nav-button"
                  aria-label="Next result"
                >
                  ↓
                </button>
              </div>
            </>
          ) : (
            <span className="pdf-search-no-results">No results</span>
          )}
        </div>
      )}
    </div>
  );
}

export default PdfSearchBar;

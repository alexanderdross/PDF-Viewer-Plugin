/**
 * PdfBookmarkList Component
 * Display PDF document bookmarks/outline
 */

'use client';

import React, { useState, useCallback } from 'react';
import type { TocItem } from '@pdf-embed-seo/core';
import { CSS_CLASSES } from '@pdf-embed-seo/core';

/**
 * PdfBookmarkList props
 */
export interface PdfBookmarkListProps {
  /** Bookmark/TOC items */
  items: TocItem[];
  /** Navigate to page handler */
  onNavigate: (page: number) => void;
  /** Current page */
  currentPage?: number;
  /** Additional CSS class */
  className?: string;
}

/**
 * Recursive bookmark item component
 */
function BookmarkItem({
  item,
  onNavigate,
  currentPage,
  level = 0,
}: {
  item: TocItem;
  onNavigate: (page: number) => void;
  currentPage?: number;
  level?: number;
}) {
  const [isExpanded, setIsExpanded] = useState(level < 2);
  const hasChildren = item.children && item.children.length > 0;
  const isActive = currentPage === item.page;

  const handleClick = useCallback(() => {
    onNavigate(item.page);
  }, [item.page, onNavigate]);

  const handleToggle = useCallback((e: React.MouseEvent) => {
    e.stopPropagation();
    setIsExpanded((prev) => !prev);
  }, []);

  return (
    <li className={`pdf-bookmark-item pdf-bookmark-level-${level}`}>
      <div
        className={`pdf-bookmark-content ${isActive ? 'pdf-bookmark-active' : ''}`}
        onClick={handleClick}
        role="button"
        tabIndex={0}
        onKeyDown={(e) => e.key === 'Enter' && handleClick()}
      >
        {hasChildren && (
          <button
            className="pdf-bookmark-toggle"
            onClick={handleToggle}
            aria-expanded={isExpanded}
            aria-label={isExpanded ? 'Collapse' : 'Expand'}
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="12"
              height="12"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              style={{ transform: isExpanded ? 'rotate(90deg)' : 'rotate(0deg)' }}
            >
              <polyline points="9 18 15 12 9 6" />
            </svg>
          </button>
        )}

        <span className="pdf-bookmark-title">{item.title}</span>
        <span className="pdf-bookmark-page">{item.page}</span>
      </div>

      {hasChildren && isExpanded && (
        <ul className="pdf-bookmark-children">
          {item.children!.map((child, index) => (
            <BookmarkItem
              key={index}
              item={child}
              onNavigate={onNavigate}
              currentPage={currentPage}
              level={level + 1}
            />
          ))}
        </ul>
      )}
    </li>
  );
}

/**
 * PdfBookmarkList Component
 */
export function PdfBookmarkList({
  items,
  onNavigate,
  currentPage,
  className = '',
}: PdfBookmarkListProps): React.ReactElement {
  if (items.length === 0) {
    return (
      <div className={`${CSS_CLASSES.premium.bookmarks} pdf-bookmarks-empty ${className}`}>
        <p>No bookmarks available</p>
      </div>
    );
  }

  return (
    <nav
      className={`${CSS_CLASSES.premium.bookmarks} ${className}`}
      aria-label="Document bookmarks"
    >
      <ul className="pdf-bookmark-list">
        {items.map((item, index) => (
          <BookmarkItem
            key={index}
            item={item}
            onNavigate={onNavigate}
            currentPage={currentPage}
          />
        ))}
      </ul>
    </nav>
  );
}

export default PdfBookmarkList;

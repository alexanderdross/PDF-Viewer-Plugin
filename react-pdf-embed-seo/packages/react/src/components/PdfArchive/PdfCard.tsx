/**
 * PdfCard Component
 * Card component for displaying PDF document in archive
 */

'use client';

import React from 'react';
import type { PdfDocument } from '@pdf-embed-seo/core';
import { CSS_CLASSES } from '@pdf-embed-seo/core';

/**
 * PdfCard props
 */
export interface PdfCardProps {
  /** PDF document data */
  document: PdfDocument;
  /** Show thumbnail */
  showThumbnail?: boolean;
  /** Show view count */
  showViewCount?: boolean;
  /** Show excerpt */
  showExcerpt?: boolean;
  /** Show date */
  showDate?: boolean;
  /** Click handler */
  onClick?: () => void;
  /** Render as link */
  href?: string;
  /** Additional CSS class */
  className?: string;
}

/**
 * Format date for display
 */
function formatDate(dateString: string): string {
  const date = new Date(dateString);
  return date.toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}

/**
 * Format view count
 */
function formatViews(views: number): string {
  if (views >= 1000000) {
    return `${(views / 1000000).toFixed(1)}M`;
  }
  if (views >= 1000) {
    return `${(views / 1000).toFixed(1)}K`;
  }
  return views.toString();
}

/**
 * PdfCard Component
 */
export function PdfCard({
  document,
  showThumbnail = true,
  showViewCount = true,
  showExcerpt = true,
  showDate = true,
  onClick,
  href,
  className = '',
}: PdfCardProps): React.ReactElement {
  const cardContent = (
    <>
      {showThumbnail && (
        <div className={CSS_CLASSES.archive.thumbnail}>
          {document.thumbnail ? (
            <img
              src={document.thumbnail}
              alt={`${document.title} thumbnail`}
              loading="lazy"
            />
          ) : (
            <div className="pdf-thumbnail-placeholder">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="48"
                height="48"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="1.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <line x1="10" y1="9" x2="8" y2="9" />
              </svg>
              <span>PDF</span>
            </div>
          )}
        </div>
      )}

      <div className="pdf-card-content">
        <h3 className={CSS_CLASSES.archive.title}>{document.title}</h3>

        {showExcerpt && document.excerpt && (
          <p className={CSS_CLASSES.archive.excerpt}>{document.excerpt}</p>
        )}

        <div className={CSS_CLASSES.archive.meta}>
          {showDate && (
            <span className="pdf-meta-date">{formatDate(document.date)}</span>
          )}

          {showViewCount && (
            <span className="pdf-meta-views">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
              {formatViews(document.views)}
            </span>
          )}

          {document.pageCount && (
            <span className="pdf-meta-pages">{document.pageCount} pages</span>
          )}
        </div>
      </div>
    </>
  );

  const cardClass = `${CSS_CLASSES.archive.card} ${className}`;

  // Render as link if href provided
  if (href) {
    return (
      <a href={href} className={cardClass} onClick={onClick}>
        {cardContent}
      </a>
    );
  }

  // Render as link to document URL
  if (document.url) {
    return (
      <a href={document.url} className={cardClass} onClick={onClick}>
        {cardContent}
      </a>
    );
  }

  // Render as button if onClick provided
  if (onClick) {
    return (
      <button type="button" className={cardClass} onClick={onClick}>
        {cardContent}
      </button>
    );
  }

  // Render as div
  return <div className={cardClass}>{cardContent}</div>;
}

export default PdfCard;

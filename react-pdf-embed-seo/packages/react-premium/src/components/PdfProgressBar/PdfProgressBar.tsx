/**
 * PdfProgressBar Component
 * Reading progress indicator for PDF documents
 */

'use client';

import React from 'react';
import { CSS_CLASSES } from '@pdf-embed-seo/core';

/**
 * PdfProgressBar props
 */
export interface PdfProgressBarProps {
  /** Progress percentage (0-100) */
  progress: number;
  /** Current page */
  currentPage?: number;
  /** Total pages */
  totalPages?: number;
  /** Show percentage text */
  showPercentage?: boolean;
  /** Show page indicator */
  showPages?: boolean;
  /** Position */
  position?: 'top' | 'bottom';
  /** Additional CSS class */
  className?: string;
}

/**
 * PdfProgressBar Component
 *
 * @example
 * ```tsx
 * <PdfProgressBar
 *   progress={45}
 *   currentPage={5}
 *   totalPages={11}
 *   showPercentage
 * />
 * ```
 */
export function PdfProgressBar({
  progress,
  currentPage,
  totalPages,
  showPercentage = true,
  showPages = true,
  position = 'top',
  className = '',
}: PdfProgressBarProps): React.ReactElement {
  const clampedProgress = Math.max(0, Math.min(100, progress));

  return (
    <div
      className={`${CSS_CLASSES.premium.progressBar} pdf-progress-${position} ${className}`}
      role="progressbar"
      aria-valuenow={clampedProgress}
      aria-valuemin={0}
      aria-valuemax={100}
      aria-label="Reading progress"
    >
      <div className="pdf-progress-track">
        <div
          className="pdf-progress-fill"
          style={{ width: `${clampedProgress}%` }}
        />
      </div>

      <div className="pdf-progress-info">
        {showPercentage && (
          <span className="pdf-progress-percentage">
            {Math.round(clampedProgress)}% complete
          </span>
        )}

        {showPages && currentPage && totalPages && (
          <span className="pdf-progress-pages">
            Page {currentPage} of {totalPages}
          </span>
        )}
      </div>
    </div>
  );
}

export default PdfProgressBar;

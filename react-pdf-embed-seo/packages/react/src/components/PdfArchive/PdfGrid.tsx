/**
 * PdfGrid Component
 * Grid layout for archive items
 */

'use client';

import React from 'react';
import { CSS_CLASSES } from '@pdf-embed-seo/core';

/**
 * PdfGrid props
 */
export interface PdfGridProps {
  /** Number of columns */
  columns?: 1 | 2 | 3 | 4;
  /** Grid gap */
  gap?: string;
  /** Children (card elements) */
  children: React.ReactNode;
  /** Additional CSS class */
  className?: string;
}

/**
 * PdfGrid Component
 */
export function PdfGrid({
  columns = 3,
  gap = '1.5rem',
  children,
  className = '',
}: PdfGridProps): React.ReactElement {
  const gridStyle: React.CSSProperties = {
    display: 'grid',
    gridTemplateColumns: `repeat(${columns}, 1fr)`,
    gap,
  };

  return (
    <div
      className={`${CSS_CLASSES.archive.grid} pdf-grid-${columns}-cols ${className}`}
      style={gridStyle}
    >
      {children}
    </div>
  );
}

export default PdfGrid;

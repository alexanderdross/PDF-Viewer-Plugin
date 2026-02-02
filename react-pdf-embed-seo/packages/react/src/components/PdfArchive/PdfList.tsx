/**
 * PdfList Component
 * List layout for archive items
 */

'use client';

import React from 'react';
import { CSS_CLASSES } from '@pdf-embed-seo/core';

/**
 * PdfList props
 */
export interface PdfListProps {
  /** List gap */
  gap?: string;
  /** Children (card elements) */
  children: React.ReactNode;
  /** Additional CSS class */
  className?: string;
}

/**
 * PdfList Component
 */
export function PdfList({
  gap = '1rem',
  children,
  className = '',
}: PdfListProps): React.ReactElement {
  const listStyle: React.CSSProperties = {
    display: 'flex',
    flexDirection: 'column',
    gap,
  };

  return (
    <div className={`${CSS_CLASSES.archive.list} ${className}`} style={listStyle}>
      {children}
    </div>
  );
}

export default PdfList;

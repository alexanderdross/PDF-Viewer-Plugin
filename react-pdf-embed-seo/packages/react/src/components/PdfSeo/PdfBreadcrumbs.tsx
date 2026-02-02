/**
 * PdfBreadcrumbs Component
 * Accessible breadcrumb navigation with Schema.org markup
 */

'use client';

import React from 'react';
import type { PdfDocument } from '@pdf-embed-seo/core';
import {
  generateDocumentBreadcrumbs,
  generateBreadcrumbSchema,
  CSS_CLASSES,
} from '@pdf-embed-seo/core';
import { usePdfContext } from '../PdfProvider/PdfContext';

/**
 * PdfBreadcrumbs props
 */
export interface PdfBreadcrumbsProps {
  /** PDF document data */
  document: PdfDocument;
  /** Custom home label */
  homeLabel?: string;
  /** Custom archive label */
  archiveLabel?: string;
  /** Separator character */
  separator?: string;
  /** Include Schema.org JSON-LD */
  includeSchema?: boolean;
  /** Additional CSS class */
  className?: string;
}

/**
 * PdfBreadcrumbs Component
 *
 * @example
 * ```tsx
 * <PdfBreadcrumbs
 *   document={document}
 *   homeLabel="Home"
 *   archiveLabel="Documents"
 *   separator="/"
 * />
 * ```
 */
export function PdfBreadcrumbs({
  document,
  homeLabel = 'Home',
  archiveLabel = 'PDF Documents',
  separator = '/',
  includeSchema = true,
  className = '',
}: PdfBreadcrumbsProps): React.ReactElement {
  const { siteUrl, siteName } = usePdfContext();

  // Generate breadcrumb items
  const breadcrumbs = generateDocumentBreadcrumbs(document, {
    siteUrl,
    siteName,
    archiveTitle: archiveLabel,
  });

  // Override labels
  if (breadcrumbs.length > 0) {
    breadcrumbs[0].name = homeLabel;
  }
  if (breadcrumbs.length > 1) {
    breadcrumbs[1].name = archiveLabel;
  }

  // Generate schema
  const schema = generateBreadcrumbSchema(breadcrumbs, { siteUrl, siteName });

  return (
    <>
      {includeSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify(schema, null, 0),
          }}
        />
      )}

      <nav
        aria-label="Breadcrumb"
        className={`${CSS_CLASSES.breadcrumbs.wrapper} ${className}`}
      >
        <ol className="pdf-breadcrumbs-list">
          {breadcrumbs.map((item, index) => {
            const isLast = index === breadcrumbs.length - 1;

            return (
              <li
                key={index}
                className={`${CSS_CLASSES.breadcrumbs.item} ${
                  isLast ? CSS_CLASSES.breadcrumbs.current : ''
                }`}
              >
                {index > 0 && (
                  <span
                    className={CSS_CLASSES.breadcrumbs.separator}
                    aria-hidden="true"
                  >
                    {separator}
                  </span>
                )}

                {isLast ? (
                  <span aria-current="page">{item.name}</span>
                ) : (
                  <a href={item.url}>{item.name}</a>
                )}
              </li>
            );
          })}
        </ol>
      </nav>
    </>
  );
}

export default PdfBreadcrumbs;

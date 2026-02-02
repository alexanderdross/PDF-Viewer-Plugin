/**
 * PdfJsonLd Component
 * Schema.org JSON-LD markup for PDF documents
 */

import React from 'react';
import type { PdfDocument } from '@pdf-embed-seo/core';
import {
  generateDigitalDocumentSchema,
  generateBreadcrumbSchema,
  generateDocumentBreadcrumbs,
  generateFaqSchema,
  mergeSchemas,
} from '@pdf-embed-seo/core';
import { usePdfContext } from '../PdfProvider/PdfContext';

/**
 * PdfJsonLd props
 */
export interface PdfJsonLdProps {
  /** PDF document data */
  document: PdfDocument;
  /** Schema type (defaults to DigitalDocument) */
  type?: 'DigitalDocument' | 'CollectionPage';
  /** Include breadcrumbs schema */
  includeBreadcrumbs?: boolean;
  /** Include speakable schema */
  includeSpeakable?: boolean;
  /** Include FAQ schema (premium) */
  includeFaq?: boolean;
  /** Include Table of Contents schema (premium) */
  includeTableOfContents?: boolean;
  /** Custom organization name */
  organizationName?: string;
  /** Custom organization URL */
  organizationUrl?: string;
  /** Custom organization logo */
  organizationLogo?: string;
}

/**
 * PdfJsonLd Component
 *
 * @example
 * ```tsx
 * export default function PdfPage({ document }) {
 *   return (
 *     <>
 *       <PdfJsonLd document={document} includeBreadcrumbs />
 *       <PdfViewer src={document} />
 *     </>
 *   );
 * }
 * ```
 */
export function PdfJsonLd({
  document,
  type = 'DigitalDocument',
  includeBreadcrumbs = false,
  includeSpeakable = true,
  includeFaq = false,
  includeTableOfContents = false,
  organizationName,
  organizationUrl,
  organizationLogo,
}: PdfJsonLdProps): React.ReactElement {
  const { siteUrl, siteName, isPremium } = usePdfContext();

  const schemaOptions = {
    siteUrl,
    siteName,
    organizationName: organizationName || siteName,
    organizationUrl: organizationUrl || siteUrl,
    organizationLogo,
  };

  // Generate schemas
  const schemas: Record<string, unknown>[] = [];

  // Main document schema
  if (type === 'DigitalDocument') {
    schemas.push(generateDigitalDocumentSchema(document, schemaOptions));
  }

  // Breadcrumbs schema
  if (includeBreadcrumbs) {
    const breadcrumbs = generateDocumentBreadcrumbs(document, schemaOptions);
    schemas.push(generateBreadcrumbSchema(breadcrumbs, schemaOptions));
  }

  // FAQ schema (premium)
  if (includeFaq && isPremium && document.faqItems && document.faqItems.length > 0) {
    schemas.push(generateFaqSchema(document.faqItems));
  }

  // Merge all schemas into a graph
  const mergedSchema = schemas.length > 1 ? mergeSchemas(schemas) : schemas[0];

  return (
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{
        __html: JSON.stringify(mergedSchema, null, 0),
      }}
    />
  );
}

export default PdfJsonLd;

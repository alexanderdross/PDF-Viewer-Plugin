/**
 * usePdfSeo Hook
 * Generate SEO metadata for PDF documents
 */

import { useMemo } from 'react';
import type { PdfDocument } from '@pdf-embed-seo/core';
import {
  generateDocumentMeta,
  generateDigitalDocumentSchema,
  generateBreadcrumbSchema,
  generateDocumentBreadcrumbs,
  toNextMetadata,
  MetaTags,
  BreadcrumbItem,
} from '@pdf-embed-seo/core';
import { usePdfContext } from '../components/PdfProvider/PdfContext';

/**
 * usePdfSeo options
 */
export interface UsePdfSeoOptions {
  /** Twitter handle */
  twitterHandle?: string;
  /** Default image if document has no thumbnail */
  defaultImage?: string;
  /** Locale for OpenGraph */
  locale?: string;
  /** Include breadcrumbs */
  includeBreadcrumbs?: boolean;
  /** Custom archive label */
  archiveLabel?: string;
  /** Organization name for schema */
  organizationName?: string;
}

/**
 * usePdfSeo return value
 */
export interface UsePdfSeoResult {
  /** Meta tags object */
  metaTags: MetaTags;
  /** Next.js Metadata object */
  nextMetadata: Record<string, unknown>;
  /** JSON-LD schema */
  jsonLd: Record<string, unknown>;
  /** Breadcrumb items */
  breadcrumbs: BreadcrumbItem[];
  /** Breadcrumbs JSON-LD schema */
  breadcrumbsSchema: Record<string, unknown>;
}

/**
 * usePdfSeo Hook
 *
 * @example
 * ```tsx
 * // For custom meta tag handling
 * function PdfPage({ document }) {
 *   const { metaTags, jsonLd, breadcrumbs } = usePdfSeo(document);
 *
 *   return (
 *     <>
 *       <Head>
 *         <title>{metaTags.title}</title>
 *         <meta name="description" content={metaTags.description} />
 *       </Head>
 *       <script
 *         type="application/ld+json"
 *         dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
 *       />
 *       <Breadcrumbs items={breadcrumbs} />
 *       <PdfViewer src={document} />
 *     </>
 *   );
 * }
 * ```
 */
export function usePdfSeo(
  document: PdfDocument | null | undefined,
  options: UsePdfSeoOptions = {}
): UsePdfSeoResult | null {
  const { siteUrl, siteName } = usePdfContext();

  const {
    twitterHandle,
    defaultImage,
    locale = 'en_US',
    includeBreadcrumbs = true,
    archiveLabel = 'PDF Documents',
    organizationName,
  } = options;

  return useMemo(() => {
    if (!document) {
      return null as unknown as UsePdfSeoResult;
    }

    // Meta options
    const metaOptions = {
      siteUrl,
      siteName,
      twitterHandle,
      defaultImage,
      locale,
    };

    // Schema options
    const schemaOptions = {
      siteUrl,
      siteName,
      organizationName: organizationName || siteName,
    };

    // Generate meta tags
    const metaTags = generateDocumentMeta(document, metaOptions);

    // Generate Next.js Metadata
    const nextMetadata = toNextMetadata(metaTags);

    // Generate JSON-LD
    const jsonLd = generateDigitalDocumentSchema(document, schemaOptions);

    // Generate breadcrumbs
    const breadcrumbs = includeBreadcrumbs
      ? generateDocumentBreadcrumbs(document, {
          ...schemaOptions,
          archiveTitle: archiveLabel,
        })
      : [];

    // Generate breadcrumbs schema
    const breadcrumbsSchema = includeBreadcrumbs
      ? generateBreadcrumbSchema(breadcrumbs, schemaOptions)
      : {};

    return {
      metaTags,
      nextMetadata,
      jsonLd,
      breadcrumbs,
      breadcrumbsSchema,
    };
  }, [
    document,
    siteUrl,
    siteName,
    twitterHandle,
    defaultImage,
    locale,
    includeBreadcrumbs,
    archiveLabel,
    organizationName,
  ]);
}

export default usePdfSeo;

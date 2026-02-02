/**
 * Next.js Metadata generation utilities
 * For use with App Router generateMetadata
 */

import type { PdfDocument } from '@pdf-embed-seo/core';
import { generateDocumentMeta, generateArchiveMeta, toNextMetadata } from '@pdf-embed-seo/core';

/**
 * Metadata options
 */
export interface PdfMetadataOptions {
  /** Site URL */
  siteUrl: string;
  /** Site name */
  siteName?: string;
  /** Twitter handle */
  twitterHandle?: string;
  /** Default image */
  defaultImage?: string;
  /** Locale */
  locale?: string;
}

/**
 * Generate Next.js Metadata for a PDF document
 *
 * @example
 * ```tsx
 * // app/pdf/[slug]/page.tsx
 * import { generatePdfMetadata } from '@pdf-embed-seo/react/nextjs';
 *
 * export async function generateMetadata({ params }) {
 *   const document = await getPdfDocument(params.slug);
 *   return generatePdfMetadata(document, {
 *     siteUrl: process.env.NEXT_PUBLIC_SITE_URL,
 *   });
 * }
 * ```
 */
export function generatePdfMetadata(
  document: PdfDocument,
  options: PdfMetadataOptions
): Record<string, unknown> {
  const meta = generateDocumentMeta(document, {
    siteUrl: options.siteUrl,
    siteName: options.siteName,
    twitterHandle: options.twitterHandle,
    defaultImage: options.defaultImage,
    locale: options.locale,
  });

  return toNextMetadata(meta);
}

/**
 * Generate Next.js Metadata for the archive page
 *
 * @example
 * ```tsx
 * // app/pdf/page.tsx
 * import { generateArchiveMetadata } from '@pdf-embed-seo/react/nextjs';
 *
 * export const metadata = generateArchiveMetadata({
 *   siteUrl: process.env.NEXT_PUBLIC_SITE_URL,
 *   title: 'PDF Documents',
 *   description: 'Browse our collection of PDF documents.',
 * });
 * ```
 */
export function generateArchiveMetadata(
  options: PdfMetadataOptions & {
    title?: string;
    description?: string;
    archivePath?: string;
  }
): Record<string, unknown> {
  const meta = generateArchiveMeta({
    siteUrl: options.siteUrl,
    siteName: options.siteName,
    twitterHandle: options.twitterHandle,
    defaultImage: options.defaultImage,
    locale: options.locale,
    title: options.title,
    description: options.description,
    archivePath: options.archivePath,
  });

  return toNextMetadata(meta);
}

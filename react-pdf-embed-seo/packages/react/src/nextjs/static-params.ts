/**
 * Next.js Static Params generation utilities
 * For use with App Router generateStaticParams
 */

import type { PdfDocument } from '@pdf-embed-seo/core';

/**
 * Params shape for PDF document pages
 */
export interface PdfDocumentParams {
  slug: string;
}

/**
 * Generate static params for PDF document pages
 *
 * @example
 * ```tsx
 * // app/pdf/[slug]/page.tsx
 * import { generatePdfStaticParams } from '@pdf-embed-seo/react/nextjs';
 *
 * export async function generateStaticParams() {
 *   const documents = await getAllPdfDocuments();
 *   return generatePdfStaticParams(documents);
 * }
 * ```
 */
export function generatePdfStaticParams(documents: PdfDocument[]): PdfDocumentParams[] {
  return documents.map((doc) => ({
    slug: doc.slug,
  }));
}

/**
 * Generate static params with category prefix
 *
 * @example
 * ```tsx
 * // app/pdf/[category]/[slug]/page.tsx
 * export async function generateStaticParams() {
 *   const documents = await getAllPdfDocuments();
 *   return generatePdfCategoryStaticParams(documents);
 * }
 * ```
 */
export function generatePdfCategoryStaticParams(
  documents: PdfDocument[]
): { category: string; slug: string }[] {
  const params: { category: string; slug: string }[] = [];

  documents.forEach((doc) => {
    if (doc.categories && doc.categories.length > 0) {
      doc.categories.forEach((cat) => {
        params.push({
          category: cat.slug,
          slug: doc.slug,
        });
      });
    } else {
      // Documents without category go under 'uncategorized'
      params.push({
        category: 'uncategorized',
        slug: doc.slug,
      });
    }
  });

  return params;
}

/**
 * Generate static params for pagination
 *
 * @example
 * ```tsx
 * // app/pdf/page/[page]/page.tsx
 * export async function generateStaticParams() {
 *   const { totalPages } = await getPdfDocumentCount();
 *   return generatePdfPaginationParams(totalPages);
 * }
 * ```
 */
export function generatePdfPaginationParams(
  totalPages: number
): { page: string }[] {
  return Array.from({ length: totalPages }, (_, i) => ({
    page: (i + 1).toString(),
  }));
}

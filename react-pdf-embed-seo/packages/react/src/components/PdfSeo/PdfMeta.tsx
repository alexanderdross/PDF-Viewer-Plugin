/**
 * PdfMeta Component
 * OpenGraph and Twitter Card meta tags for PDF documents
 * For use with Next.js Pages Router (uses next/head)
 */

'use client';

import React from 'react';
import type { PdfDocument } from '@pdf-embed-seo/core';
import { generateDocumentMeta, toHtmlMetaTags } from '@pdf-embed-seo/core';
import { usePdfContext } from '../PdfProvider/PdfContext';

/**
 * PdfMeta props
 */
export interface PdfMetaProps {
  /** PDF document data */
  document: PdfDocument;
  /** Twitter handle (optional) */
  twitterHandle?: string;
  /** Default image for OG (if document has no thumbnail) */
  defaultImage?: string;
  /** Locale for OpenGraph */
  locale?: string;
}

/**
 * PdfMeta Component - Renders meta tags inline
 * For Next.js App Router, use the generatePdfMetadata helper instead
 *
 * @example
 * ```tsx
 * // Pages Router
 * import Head from 'next/head';
 *
 * export default function PdfPage({ document }) {
 *   return (
 *     <>
 *       <Head>
 *         <PdfMeta document={document} />
 *       </Head>
 *       <PdfViewer src={document} />
 *     </>
 *   );
 * }
 * ```
 */
export function PdfMeta({
  document,
  twitterHandle,
  defaultImage,
  locale = 'en_US',
}: PdfMetaProps): React.ReactElement {
  const { siteUrl, siteName } = usePdfContext();

  const meta = generateDocumentMeta(document, {
    siteUrl,
    siteName,
    twitterHandle,
    defaultImage,
    locale,
  });

  const metaTags = toHtmlMetaTags(meta);

  // Return fragments with meta tags
  return (
    <>
      {metaTags.map((tag, index) => (
        <React.Fragment key={index}>
          {/* Parse and render meta tags */}
          {tag.startsWith('<title>') ? (
            <title>{document.title}</title>
          ) : (
            <meta
              {...parseMetaTag(tag)}
            />
          )}
        </React.Fragment>
      ))}
    </>
  );
}

/**
 * Parse meta tag string to props
 */
function parseMetaTag(tag: string): Record<string, string> {
  const props: Record<string, string> = {};

  // Extract attributes using regex
  const attrRegex = /(\w+(?:-\w+)?)="([^"]*)"/g;
  let match;

  while ((match = attrRegex.exec(tag)) !== null) {
    const [, name, value] = match;
    // Convert property to React format
    if (name === 'property') {
      props.property = value;
    } else if (name === 'name') {
      props.name = value;
    } else if (name === 'content') {
      props.content = value;
    } else if (name === 'href') {
      props.href = value;
    }
  }

  return props;
}

export default PdfMeta;

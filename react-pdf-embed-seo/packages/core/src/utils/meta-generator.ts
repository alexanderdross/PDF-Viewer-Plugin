/**
 * Meta tag generator for PDF documents
 */

import type { PdfDocument } from '../types/document';

/**
 * Meta tag options
 */
export interface MetaOptions {
  siteUrl: string;
  siteName?: string;
  twitterHandle?: string;
  locale?: string;
  defaultImage?: string;
}

/**
 * Generated meta tags
 */
export interface MetaTags {
  title: string;
  description: string;
  canonical: string;
  openGraph: OpenGraphMeta;
  twitter: TwitterMeta;
  robots?: string;
}

/**
 * OpenGraph meta tags
 */
export interface OpenGraphMeta {
  title: string;
  description: string;
  url: string;
  siteName: string;
  type: string;
  locale: string;
  image?: string;
  imageWidth?: number;
  imageHeight?: number;
  imageAlt?: string;
}

/**
 * Twitter Card meta tags
 */
export interface TwitterMeta {
  card: 'summary' | 'summary_large_image';
  title: string;
  description: string;
  image?: string;
  imageAlt?: string;
  site?: string;
}

/**
 * Generate meta tags for a single PDF document
 */
export function generateDocumentMeta(document: PdfDocument, options: MetaOptions): MetaTags {
  const description = truncateDescription(document.excerpt || document.description || '', 160);
  const url = document.url || `${options.siteUrl}/pdf/${document.slug}/`;
  const image = document.thumbnail || options.defaultImage;

  return {
    title: document.title,
    description,
    canonical: url,
    openGraph: {
      title: document.title,
      description,
      url,
      siteName: options.siteName || 'PDF Documents',
      type: 'article',
      locale: options.locale || 'en_US',
      ...(image && {
        image,
        imageWidth: 1200,
        imageHeight: 630,
        imageAlt: `${document.title} - PDF Document`,
      }),
    },
    twitter: {
      card: image ? 'summary_large_image' : 'summary',
      title: document.title,
      description,
      ...(image && {
        image,
        imageAlt: `${document.title} - PDF Document`,
      }),
      ...(options.twitterHandle && { site: options.twitterHandle }),
    },
    robots: 'index, follow',
  };
}

/**
 * Generate meta tags for the archive page
 */
export function generateArchiveMeta(
  options: MetaOptions & {
    title?: string;
    description?: string;
    archivePath?: string;
  }
): MetaTags {
  const title = options.title || 'PDF Documents';
  const description = options.description || 'Browse our collection of PDF documents.';
  const url = `${options.siteUrl}${options.archivePath || '/pdf/'}`;

  return {
    title,
    description,
    canonical: url,
    openGraph: {
      title,
      description,
      url,
      siteName: options.siteName || 'PDF Documents',
      type: 'website',
      locale: options.locale || 'en_US',
      ...(options.defaultImage && {
        image: options.defaultImage,
        imageWidth: 1200,
        imageHeight: 630,
        imageAlt: title,
      }),
    },
    twitter: {
      card: options.defaultImage ? 'summary_large_image' : 'summary',
      title,
      description,
      ...(options.defaultImage && {
        image: options.defaultImage,
        imageAlt: title,
      }),
      ...(options.twitterHandle && { site: options.twitterHandle }),
    },
    robots: 'index, follow',
  };
}

/**
 * Convert MetaTags to Next.js Metadata format
 */
export function toNextMetadata(meta: MetaTags): Record<string, unknown> {
  return {
    title: meta.title,
    description: meta.description,
    alternates: {
      canonical: meta.canonical,
    },
    openGraph: {
      title: meta.openGraph.title,
      description: meta.openGraph.description,
      url: meta.openGraph.url,
      siteName: meta.openGraph.siteName,
      type: meta.openGraph.type,
      locale: meta.openGraph.locale,
      ...(meta.openGraph.image && {
        images: [
          {
            url: meta.openGraph.image,
            width: meta.openGraph.imageWidth,
            height: meta.openGraph.imageHeight,
            alt: meta.openGraph.imageAlt,
          },
        ],
      }),
    },
    twitter: {
      card: meta.twitter.card,
      title: meta.twitter.title,
      description: meta.twitter.description,
      ...(meta.twitter.image && {
        images: [meta.twitter.image],
      }),
      ...(meta.twitter.site && { site: meta.twitter.site }),
    },
    robots: meta.robots,
  };
}

/**
 * Convert MetaTags to HTML meta tag strings (for Pages Router)
 */
export function toHtmlMetaTags(meta: MetaTags): string[] {
  const tags: string[] = [
    `<title>${escapeHtml(meta.title)}</title>`,
    `<meta name="description" content="${escapeHtml(meta.description)}" />`,
    `<link rel="canonical" href="${meta.canonical}" />`,
    // OpenGraph
    `<meta property="og:title" content="${escapeHtml(meta.openGraph.title)}" />`,
    `<meta property="og:description" content="${escapeHtml(meta.openGraph.description)}" />`,
    `<meta property="og:url" content="${meta.openGraph.url}" />`,
    `<meta property="og:site_name" content="${escapeHtml(meta.openGraph.siteName)}" />`,
    `<meta property="og:type" content="${meta.openGraph.type}" />`,
    `<meta property="og:locale" content="${meta.openGraph.locale}" />`,
    // Twitter
    `<meta name="twitter:card" content="${meta.twitter.card}" />`,
    `<meta name="twitter:title" content="${escapeHtml(meta.twitter.title)}" />`,
    `<meta name="twitter:description" content="${escapeHtml(meta.twitter.description)}" />`,
  ];

  if (meta.openGraph.image) {
    tags.push(`<meta property="og:image" content="${meta.openGraph.image}" />`);
    if (meta.openGraph.imageWidth) {
      tags.push(`<meta property="og:image:width" content="${meta.openGraph.imageWidth}" />`);
    }
    if (meta.openGraph.imageHeight) {
      tags.push(`<meta property="og:image:height" content="${meta.openGraph.imageHeight}" />`);
    }
    if (meta.openGraph.imageAlt) {
      tags.push(`<meta property="og:image:alt" content="${escapeHtml(meta.openGraph.imageAlt)}" />`);
    }
  }

  if (meta.twitter.image) {
    tags.push(`<meta name="twitter:image" content="${meta.twitter.image}" />`);
  }

  if (meta.twitter.site) {
    tags.push(`<meta name="twitter:site" content="${meta.twitter.site}" />`);
  }

  if (meta.robots) {
    tags.push(`<meta name="robots" content="${meta.robots}" />`);
  }

  return tags;
}

/**
 * Truncate description to specified length
 */
function truncateDescription(text: string, maxLength: number): string {
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength - 3).trim() + '...';
}

/**
 * Escape HTML entities
 */
function escapeHtml(text: string): string {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

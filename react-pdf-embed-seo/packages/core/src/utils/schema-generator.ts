/**
 * Schema.org JSON-LD generator for PDF documents
 */

import type { PdfDocument, FaqItem, TocItem } from '../types/document';

/**
 * Schema.org types used by this module
 */
export type SchemaType =
  | 'DigitalDocument'
  | 'CollectionPage'
  | 'BreadcrumbList'
  | 'FAQPage'
  | 'WebPage';

/**
 * Base schema options
 */
export interface SchemaOptions {
  siteUrl: string;
  siteName?: string;
  organizationName?: string;
  organizationUrl?: string;
  organizationLogo?: string;
}

/**
 * Breadcrumb item
 */
export interface BreadcrumbItem {
  name: string;
  url: string;
}

/**
 * Generate DigitalDocument schema for a PDF
 */
export function generateDigitalDocumentSchema(
  document: PdfDocument,
  options: SchemaOptions
): Record<string, unknown> {
  const schema: Record<string, unknown> = {
    '@context': 'https://schema.org',
    '@type': 'DigitalDocument',
    '@id': `${options.siteUrl}/pdf/${document.slug}/#document`,
    name: document.title,
    url: document.url || `${options.siteUrl}/pdf/${document.slug}/`,
    datePublished: document.date,
    dateModified: document.modified,
    description: document.excerpt || document.description,
    identifier: document.id.toString(),
    encodingFormat: 'application/pdf',
    inLanguage: document.language || 'en',
    accessMode: ['textual', 'visual'],
    accessibilityFeature: ['tableOfContents', 'readingOrder', 'alternativeText'],
    accessibilityHazard: 'none',
  };

  // Add thumbnail/image
  if (document.thumbnail) {
    schema.image = document.thumbnail;
    schema.thumbnailUrl = document.thumbnail;
  }

  // Add file info
  if (document.fileSize) {
    schema.contentSize = formatFileSize(document.fileSize);
  }

  if (document.pageCount) {
    schema.numberOfPages = document.pageCount;
  }

  // Add author
  if (document.author) {
    schema.author = {
      '@type': 'Person',
      name: document.author,
    };
  }

  // Add publisher/organization
  if (options.organizationName) {
    schema.publisher = {
      '@type': 'Organization',
      name: options.organizationName,
      url: options.organizationUrl || options.siteUrl,
      ...(options.organizationLogo && { logo: options.organizationLogo }),
    };
  }

  // Add potential actions
  const potentialActions: Array<{ '@type': string; target: string }> = [
    {
      '@type': 'ReadAction',
      target: document.url || `${options.siteUrl}/pdf/${document.slug}/`,
    },
  ];

  if (document.allowDownload) {
    potentialActions.push({
      '@type': 'DownloadAction',
      target: document.pdfUrl || `${options.siteUrl}/pdf/${document.slug}/download/`,
    });
  }

  schema.potentialAction = potentialActions;

  // Premium: Add reading time
  if (document.readingTime) {
    schema.timeRequired = `PT${document.readingTime}M`;
  }

  // Premium: Add difficulty level
  if (document.difficultyLevel) {
    schema.educationalLevel = document.difficultyLevel;
  }

  // Premium: Add speakable
  if (document.aiSummary || document.keyPoints) {
    schema.speakable = {
      '@type': 'SpeakableSpecification',
      cssSelector: ['.pdf-summary', '.pdf-key-points', 'h1'],
    };
  }

  return schema;
}

/**
 * Generate CollectionPage schema for archive
 */
export function generateCollectionPageSchema(
  documents: PdfDocument[],
  options: SchemaOptions & {
    title?: string;
    description?: string;
    archivePath?: string;
  }
): Record<string, unknown> {
  const archiveUrl = `${options.siteUrl}${options.archivePath || '/pdf/'}`;

  return {
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    '@id': `${archiveUrl}#collection`,
    name: options.title || 'PDF Documents',
    description: options.description || 'Browse our collection of PDF documents.',
    url: archiveUrl,
    mainEntity: {
      '@type': 'ItemList',
      numberOfItems: documents.length,
      itemListElement: documents.map((doc, index) => ({
        '@type': 'ListItem',
        position: index + 1,
        item: {
          '@type': 'DigitalDocument',
          '@id': `${options.siteUrl}/pdf/${doc.slug}/#document`,
          name: doc.title,
          url: doc.url || `${options.siteUrl}/pdf/${doc.slug}/`,
          ...(doc.thumbnail && { image: doc.thumbnail }),
        },
      })),
    },
    isPartOf: {
      '@type': 'WebSite',
      '@id': `${options.siteUrl}/#website`,
      name: options.siteName || options.siteUrl,
      url: options.siteUrl,
    },
  };
}

/**
 * Generate BreadcrumbList schema
 */
export function generateBreadcrumbSchema(
  items: BreadcrumbItem[],
  options: SchemaOptions
): Record<string, unknown> {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((item, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      name: item.name,
      item: item.url.startsWith('http') ? item.url : `${options.siteUrl}${item.url}`,
    })),
  };
}

/**
 * Generate FAQPage schema (premium)
 */
export function generateFaqSchema(faqItems: FaqItem[]): Record<string, unknown> {
  return {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqItems.map((faq) => ({
      '@type': 'Question',
      name: faq.question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: faq.answer,
      },
    })),
  };
}

/**
 * Generate document breadcrumbs
 */
export function generateDocumentBreadcrumbs(
  document: PdfDocument,
  options: SchemaOptions & { archiveTitle?: string }
): BreadcrumbItem[] {
  return [
    { name: 'Home', url: options.siteUrl },
    {
      name: options.archiveTitle || 'PDF Documents',
      url: `${options.siteUrl}/pdf/`,
    },
    {
      name: document.title,
      url: document.url || `${options.siteUrl}/pdf/${document.slug}/`,
    },
  ];
}

/**
 * Format file size for schema
 */
function formatFileSize(bytes: number): string {
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

/**
 * Merge multiple schemas into a graph
 */
export function mergeSchemas(schemas: Record<string, unknown>[]): Record<string, unknown> {
  return {
    '@context': 'https://schema.org',
    '@graph': schemas.map((schema) => {
      // Remove @context from nested schemas
      const { '@context': _, ...rest } = schema;
      return rest;
    }),
  };
}

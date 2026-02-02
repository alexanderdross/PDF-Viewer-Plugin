/**
 * PDF Sitemap generation for Next.js
 */

import type { PdfDocument } from '@pdf-embed-seo/core';

/**
 * Sitemap entry
 */
export interface SitemapEntry {
  url: string;
  lastModified: string;
  changeFrequency?: 'always' | 'hourly' | 'daily' | 'weekly' | 'monthly' | 'yearly' | 'never';
  priority?: number;
}

/**
 * Generate PDF sitemap entries
 */
export function generatePdfSitemapEntries(
  documents: PdfDocument[],
  options: { siteUrl: string }
): SitemapEntry[] {
  return documents.map((doc) => ({
    url: doc.url || `${options.siteUrl}/pdf/${doc.slug}/`,
    lastModified: doc.modified || doc.date,
    changeFrequency: 'monthly' as const,
    priority: 0.7,
  }));
}

/**
 * Generate sitemap XML string
 */
export function generateSitemapXml(entries: SitemapEntry[]): string {
  const urlElements = entries
    .map(
      (entry) => `
  <url>
    <loc>${escapeXml(entry.url)}</loc>
    <lastmod>${entry.lastModified}</lastmod>
    ${entry.changeFrequency ? `<changefreq>${entry.changeFrequency}</changefreq>` : ''}
    ${entry.priority !== undefined ? `<priority>${entry.priority}</priority>` : ''}
  </url>`
    )
    .join('');

  return `<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="/pdf/sitemap.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${urlElements}
</urlset>`;
}

/**
 * Generate PDF sitemap as Next.js Response
 *
 * @example
 * ```tsx
 * // app/pdf/sitemap.xml/route.ts
 * import { generatePdfSitemap } from '@pdf-embed-seo/react-premium/nextjs';
 *
 * export async function GET() {
 *   const documents = await getAllPdfDocuments();
 *   return generatePdfSitemap(documents, {
 *     siteUrl: process.env.NEXT_PUBLIC_SITE_URL,
 *   });
 * }
 * ```
 */
export function generatePdfSitemap(
  documents: PdfDocument[],
  options: { siteUrl: string }
): Response {
  const entries = generatePdfSitemapEntries(documents, options);
  const xml = generateSitemapXml(entries);

  return new Response(xml, {
    headers: {
      'Content-Type': 'application/xml',
      'Cache-Control': 'public, max-age=3600, s-maxage=3600',
    },
  });
}

/**
 * Generate sitemap XSL stylesheet
 */
export function generateSitemapXsl(): string {
  return `<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9">

  <xsl:output method="html" encoding="UTF-8" indent="yes"/>

  <xsl:template match="/">
    <html>
      <head>
        <title>PDF Sitemap</title>
        <style>
          body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; padding: 40px; }
          h1 { color: #333; }
          table { border-collapse: collapse; width: 100%; margin-top: 20px; }
          th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
          th { background: #f5f5f5; }
          tr:hover { background: #fafafa; }
          a { color: #0066cc; text-decoration: none; }
          a:hover { text-decoration: underline; }
        </style>
      </head>
      <body>
        <h1>PDF Document Sitemap</h1>
        <p>This sitemap contains <xsl:value-of select="count(sitemap:urlset/sitemap:url)"/> PDF documents.</p>
        <table>
          <thead>
            <tr>
              <th>URL</th>
              <th>Last Modified</th>
              <th>Priority</th>
            </tr>
          </thead>
          <tbody>
            <xsl:for-each select="sitemap:urlset/sitemap:url">
              <tr>
                <td><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a></td>
                <td><xsl:value-of select="sitemap:lastmod"/></td>
                <td><xsl:value-of select="sitemap:priority"/></td>
              </tr>
            </xsl:for-each>
          </tbody>
        </table>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>`;
}

/**
 * Generate XSL route handler
 */
export function generateSitemapXslResponse(): Response {
  return new Response(generateSitemapXsl(), {
    headers: {
      'Content-Type': 'application/xslt+xml',
      'Cache-Control': 'public, max-age=86400',
    },
  });
}

/**
 * Escape XML special characters
 */
function escapeXml(str: string): string {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}

/**
 * Next.js Route Handler utilities
 * For creating API routes for PDF operations
 */

import type { PdfDocument } from '@pdf-embed-seo/core';
import { PdfApiClient, createWordPressClient, createDrupalClient } from '@pdf-embed-seo/core';

/**
 * Route handler options
 */
export interface RouteHandlerOptions {
  /** Backend adapter type */
  adapter: 'wordpress' | 'drupal' | 'custom';
  /** Backend API URL */
  apiUrl: string;
  /** Custom API client (for 'custom' adapter) */
  client?: PdfApiClient;
  /** Enable caching */
  cache?: boolean;
  /** Cache revalidation time in seconds */
  revalidate?: number;
}

/**
 * Create API client based on adapter type
 */
function createClient(options: RouteHandlerOptions): PdfApiClient {
  if (options.client) {
    return options.client;
  }

  switch (options.adapter) {
    case 'wordpress':
      return createWordPressClient({ baseUrl: options.apiUrl });
    case 'drupal':
      return createDrupalClient({ baseUrl: options.apiUrl });
    default:
      throw new Error(`Unknown adapter: ${options.adapter}`);
  }
}

/**
 * Create route handlers for PDF documents
 *
 * @example
 * ```tsx
 * // app/api/pdf/[id]/route.ts
 * import { createPdfRouteHandler } from '@pdf-embed-seo/react/nextjs';
 *
 * export const { GET, POST } = createPdfRouteHandler({
 *   adapter: 'wordpress',
 *   apiUrl: process.env.WP_API_URL,
 * });
 * ```
 */
export function createPdfRouteHandler(options: RouteHandlerOptions) {
  const client = createClient(options);

  return {
    /**
     * GET handler - Fetch document by ID
     */
    async GET(
      request: Request,
      { params }: { params: { id: string } }
    ): Promise<Response> {
      try {
        const document = await client.getDocument(params.id);

        return Response.json(
          { document },
          {
            headers: options.cache
              ? {
                  'Cache-Control': `public, s-maxage=${options.revalidate || 3600}, stale-while-revalidate`,
                }
              : {},
          }
        );
      } catch (error) {
        const message = error instanceof Error ? error.message : 'Unknown error';
        return Response.json(
          { error: message },
          { status: 404 }
        );
      }
    },

    /**
     * POST handler - Track view
     */
    async POST(
      request: Request,
      { params }: { params: { id: string } }
    ): Promise<Response> {
      try {
        const result = await client.trackView(params.id);
        return Response.json(result);
      } catch (error) {
        const message = error instanceof Error ? error.message : 'Unknown error';
        return Response.json(
          { error: message },
          { status: 500 }
        );
      }
    },
  };
}

/**
 * Create route handlers for document list
 *
 * @example
 * ```tsx
 * // app/api/pdf/route.ts
 * import { createPdfListRouteHandler } from '@pdf-embed-seo/react/nextjs';
 *
 * export const { GET } = createPdfListRouteHandler({
 *   adapter: 'wordpress',
 *   apiUrl: process.env.WP_API_URL,
 * });
 * ```
 */
export function createPdfListRouteHandler(options: RouteHandlerOptions) {
  const client = createClient(options);

  return {
    /**
     * GET handler - Fetch document list
     */
    async GET(request: Request): Promise<Response> {
      const { searchParams } = new URL(request.url);

      try {
        const result = await client.getDocuments({
          page: parseInt(searchParams.get('page') || '1'),
          perPage: parseInt(searchParams.get('per_page') || '10'),
          search: searchParams.get('search') || undefined,
          orderby: (searchParams.get('orderby') as 'date' | 'title' | 'views') || 'date',
          order: (searchParams.get('order') as 'asc' | 'desc') || 'desc',
        });

        return Response.json(result, {
          headers: options.cache
            ? {
                'Cache-Control': `public, s-maxage=${options.revalidate || 60}, stale-while-revalidate`,
              }
            : {},
        });
      } catch (error) {
        const message = error instanceof Error ? error.message : 'Unknown error';
        return Response.json(
          { error: message },
          { status: 500 }
        );
      }
    },
  };
}

/**
 * Create PDF data route handler (for secure PDF URLs)
 *
 * @example
 * ```tsx
 * // app/api/pdf/[id]/data/route.ts
 * import { createPdfDataRouteHandler } from '@pdf-embed-seo/react/nextjs';
 *
 * export const { GET } = createPdfDataRouteHandler({
 *   adapter: 'wordpress',
 *   apiUrl: process.env.WP_API_URL,
 * });
 * ```
 */
export function createPdfDataRouteHandler(options: RouteHandlerOptions) {
  const client = createClient(options);

  return {
    /**
     * GET handler - Get PDF data/URL
     */
    async GET(
      request: Request,
      { params }: { params: { id: string } }
    ): Promise<Response> {
      try {
        const data = await client.getDocumentData(params.id);

        return Response.json(data, {
          headers: {
            // Short cache for data endpoints
            'Cache-Control': 'private, max-age=60',
          },
        });
      } catch (error) {
        const message = error instanceof Error ? error.message : 'Unknown error';
        return Response.json(
          { error: message },
          { status: 404 }
        );
      }
    },
  };
}

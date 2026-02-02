/**
 * Next.js utilities for @pdf-embed-seo/react
 */

export { generatePdfMetadata, generateArchiveMetadata, type PdfMetadataOptions } from './metadata';
export {
  generatePdfStaticParams,
  generatePdfCategoryStaticParams,
  generatePdfPaginationParams,
  type PdfDocumentParams,
} from './static-params';
export {
  createPdfRouteHandler,
  createPdfListRouteHandler,
  createPdfDataRouteHandler,
  type RouteHandlerOptions,
} from './route-handlers';

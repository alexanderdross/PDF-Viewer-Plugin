/**
 * PDF.js loader utility
 * Handles dynamic loading and configuration of PDF.js
 */

import type { PdfDocumentInfo } from '../types/document';

// PDF.js types (minimal)
interface PDFDocumentProxy {
  numPages: number;
  getMetadata(): Promise<{ info: Record<string, unknown> }>;
  getPage(pageNumber: number): Promise<PDFPageProxy>;
  destroy(): void;
}

interface PDFPageProxy {
  getViewport(params: { scale: number; rotation?: number }): PDFPageViewport;
  render(params: { canvasContext: CanvasRenderingContext2D; viewport: PDFPageViewport }): {
    promise: Promise<void>;
  };
}

interface PDFPageViewport {
  width: number;
  height: number;
  scale: number;
}

interface PDFJSLib {
  getDocument(params: { url: string } | { data: ArrayBuffer }): {
    promise: Promise<PDFDocumentProxy>;
  };
  GlobalWorkerOptions: {
    workerSrc: string;
  };
}

/**
 * PDF.js loader configuration
 */
export interface PdfJsConfig {
  /** Custom worker URL */
  workerUrl?: string;
  /** Use CDN fallback */
  useCdn?: boolean;
  /** PDF.js version for CDN */
  version?: string;
}

// Global PDF.js reference
let pdfjsLib: PDFJSLib | null = null;
let loadPromise: Promise<PDFJSLib> | null = null;

/**
 * Get the default PDF.js CDN worker URL
 */
export function getDefaultWorkerUrl(version = '4.0.379'): string {
  return `https://cdnjs.cloudflare.com/ajax/libs/pdf.js/${version}/pdf.worker.min.js`;
}

/**
 * Load PDF.js library
 */
export async function loadPdfJs(config: PdfJsConfig = {}): Promise<PDFJSLib> {
  // Return cached instance
  if (pdfjsLib) return pdfjsLib;

  // Return existing load promise
  if (loadPromise) return loadPromise;

  loadPromise = (async () => {
    // Dynamic import of pdfjs-dist
    const pdfjs = await import('pdfjs-dist');

    // Configure worker
    const workerUrl = config.workerUrl || getDefaultWorkerUrl(config.version);
    pdfjs.GlobalWorkerOptions.workerSrc = workerUrl;

    pdfjsLib = pdfjs as unknown as PDFJSLib;
    return pdfjsLib;
  })();

  return loadPromise;
}

/**
 * Load a PDF document
 */
export async function loadPdfDocument(
  source: string | ArrayBuffer,
  config?: PdfJsConfig
): Promise<PDFDocumentProxy> {
  const pdfjs = await loadPdfJs(config);

  const params = typeof source === 'string' ? { url: source } : { data: source };

  return pdfjs.getDocument(params).promise;
}

/**
 * Get document info from PDF
 */
export async function getPdfInfo(doc: PDFDocumentProxy): Promise<PdfDocumentInfo> {
  const metadata = await doc.getMetadata();
  const info = metadata.info || {};

  return {
    numPages: doc.numPages,
    title: info.Title as string | undefined,
    author: info.Author as string | undefined,
    subject: info.Subject as string | undefined,
    keywords: info.Keywords as string | undefined,
    creator: info.Creator as string | undefined,
    producer: info.Producer as string | undefined,
    creationDate: parseDate(info.CreationDate as string | undefined),
    modificationDate: parseDate(info.ModDate as string | undefined),
  };
}

/**
 * Render a PDF page to canvas
 */
export async function renderPage(
  doc: PDFDocumentProxy,
  pageNumber: number,
  canvas: HTMLCanvasElement,
  scale = 1.0
): Promise<void> {
  const page = await doc.getPage(pageNumber);
  const viewport = page.getViewport({ scale });

  canvas.width = viewport.width;
  canvas.height = viewport.height;

  const context = canvas.getContext('2d');
  if (!context) {
    throw new Error('Could not get canvas 2D context');
  }

  await page.render({
    canvasContext: context,
    viewport,
  }).promise;
}

/**
 * Generate thumbnail from first page
 */
export async function generateThumbnail(
  source: string | ArrayBuffer,
  options: {
    width?: number;
    height?: number;
    format?: 'image/png' | 'image/jpeg';
    quality?: number;
  } = {}
): Promise<string> {
  const { width = 200, height = 280, format = 'image/png', quality = 0.8 } = options;

  const doc = await loadPdfDocument(source);
  const page = await doc.getPage(1);

  // Calculate scale to fit dimensions
  const viewport = page.getViewport({ scale: 1 });
  const scale = Math.min(width / viewport.width, height / viewport.height);
  const scaledViewport = page.getViewport({ scale });

  // Create canvas
  const canvas = document.createElement('canvas');
  canvas.width = scaledViewport.width;
  canvas.height = scaledViewport.height;

  const context = canvas.getContext('2d');
  if (!context) {
    doc.destroy();
    throw new Error('Could not get canvas 2D context');
  }

  // Render page
  await page.render({
    canvasContext: context,
    viewport: scaledViewport,
  }).promise;

  // Convert to data URL
  const dataUrl = canvas.toDataURL(format, quality);

  // Cleanup
  doc.destroy();

  return dataUrl;
}

/**
 * Parse PDF date string
 */
function parseDate(dateString?: string): Date | undefined {
  if (!dateString) return undefined;

  // PDF date format: D:YYYYMMDDHHmmSSOHH'mm'
  const match = dateString.match(
    /D:(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})([+-Z])(\d{2})'?(\d{2})?'?/
  );

  if (!match) return undefined;

  const [, year, month, day, hour, minute, second, tzSign, tzHour, tzMinute = '00'] = match;

  const date = new Date(
    Date.UTC(
      parseInt(year),
      parseInt(month) - 1,
      parseInt(day),
      parseInt(hour),
      parseInt(minute),
      parseInt(second)
    )
  );

  // Apply timezone offset
  if (tzSign !== 'Z') {
    const offset = (parseInt(tzHour) * 60 + parseInt(tzMinute)) * (tzSign === '-' ? -1 : 1);
    date.setMinutes(date.getMinutes() - offset);
  }

  return date;
}

/**
 * Check if PDF.js is loaded
 */
export function isPdfJsLoaded(): boolean {
  return pdfjsLib !== null;
}

/**
 * Reset PDF.js loader (for testing)
 */
export function resetPdfJsLoader(): void {
  pdfjsLib = null;
  loadPromise = null;
}

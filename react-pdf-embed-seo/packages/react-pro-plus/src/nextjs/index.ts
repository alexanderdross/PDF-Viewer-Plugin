/**
 * @pdf-embed-seo/react-pro-plus - Next.js Exports
 *
 * Server-safe re-exports of Pro+ components for Next.js App Router
 */

// Re-export all components - they use 'use client' internally
export {
  PdfAnnotations,
  PdfAnnotationToolbar,
  PdfVersionHistory,
  PdfAdvancedAnalytics,
  PdfHeatmap,
  PdfComplianceConsent,
  PdfAuditLog,
  PdfWebhookConfig,
  PdfWhiteLabel,
  PdfTwoFactorAuth,
} from '../components';

// Re-export all hooks
export {
  useAnnotations,
  useVersions,
  useAdvancedAnalytics,
  useHeatmap,
  useCompliance,
  useAuditLog,
  useWebhooks,
  useWhiteLabel,
  useTwoFactorAuth,
  useProPlusLicense,
} from '../hooks';

// Re-export context
export { ProPlusProvider, useProPlusContext } from '../context/ProPlusContext';

// Re-export types
export type * from '../types';

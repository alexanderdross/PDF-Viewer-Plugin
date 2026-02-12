/**
 * @pdf-embed-seo/react-pro-plus
 *
 * Pro+ Enterprise React components for PDF Embed SEO
 * Provides advanced analytics, annotations, versioning, webhooks, and compliance features.
 *
 * @license Commercial
 * @version 1.3.0
 */

// Components
export { PdfAnnotations } from './components/PdfAnnotations';
export { PdfAnnotationToolbar } from './components/PdfAnnotationToolbar';
export { PdfVersionHistory } from './components/PdfVersionHistory';
export { PdfAdvancedAnalytics } from './components/PdfAdvancedAnalytics';
export { PdfHeatmap } from './components/PdfHeatmap';
export { PdfComplianceConsent } from './components/PdfComplianceConsent';
export { PdfAuditLog } from './components/PdfAuditLog';
export { PdfWebhookConfig } from './components/PdfWebhookConfig';
export { PdfWhiteLabel } from './components/PdfWhiteLabel';
export { PdfTwoFactorAuth } from './components/PdfTwoFactorAuth';

// Hooks
export { useAnnotations } from './hooks/useAnnotations';
export { useVersions } from './hooks/useVersions';
export { useAdvancedAnalytics } from './hooks/useAdvancedAnalytics';
export { useHeatmap } from './hooks/useHeatmap';
export { useCompliance } from './hooks/useCompliance';
export { useAuditLog } from './hooks/useAuditLog';
export { useWebhooks } from './hooks/useWebhooks';
export { useWhiteLabel } from './hooks/useWhiteLabel';
export { useTwoFactorAuth } from './hooks/useTwoFactorAuth';
export { useProPlusLicense } from './hooks/useProPlusLicense';

// Types
export type {
  Annotation,
  AnnotationType,
  AnnotationToolbarProps,
  DocumentVersion,
  VersionHistoryProps,
  AdvancedAnalyticsData,
  HeatmapData,
  HeatmapPoint,
  ComplianceMode,
  ConsentRecord,
  AuditLogEntry,
  AuditAction,
  WebhookConfig,
  WebhookEvent,
  WebhookDelivery,
  WhiteLabelConfig,
  TwoFactorConfig,
  ProPlusLicenseStatus,
} from './types';

// Context
export { ProPlusProvider, useProPlusContext } from './context/ProPlusContext';

// Utilities
export { validateProPlusLicense } from './utils/license';
export { generateWebhookSignature, verifyWebhookSignature } from './utils/webhook';
export { anonymizeIp } from './utils/compliance';

/**
 * Pro+ Enterprise type definitions
 */

// Annotation Types
export type AnnotationType =
  | 'highlight'
  | 'underline'
  | 'strikethrough'
  | 'text_note'
  | 'sticky_note'
  | 'freehand'
  | 'rectangle'
  | 'circle'
  | 'arrow'
  | 'line';

export interface Annotation {
  id: string;
  documentId: string | number;
  page: number;
  type: AnnotationType;
  x: number;
  y: number;
  width?: number;
  height?: number;
  color?: string;
  opacity?: number;
  content?: string;
  pathData?: { x: number; y: number }[];
  authorId: number;
  authorName?: string;
  createdAt: string;
  updatedAt: string;
}

export interface AnnotationToolbarProps {
  documentId: string | number;
  onAnnotationCreate?: (annotation: Annotation) => void;
  onAnnotationUpdate?: (annotation: Annotation) => void;
  onAnnotationDelete?: (annotationId: string) => void;
  allowedTypes?: AnnotationType[];
  defaultColor?: string;
  disabled?: boolean;
}

// Version Types
export interface DocumentVersion {
  id: number;
  documentId: string | number;
  versionNumber: string;
  fileUrl: string;
  fileSize: number;
  checksum?: string;
  changelog?: string;
  authorId: number;
  authorName?: string;
  isCurrent: boolean;
  createdAt: string;
}

export interface VersionHistoryProps {
  documentId: string | number;
  onVersionSelect?: (version: DocumentVersion) => void;
  onVersionRestore?: (version: DocumentVersion) => void;
  showChangelog?: boolean;
  showRestore?: boolean;
}

// Analytics Types
export interface AdvancedAnalyticsData {
  overview: {
    totalViews: number;
    uniqueVisitors: number;
    avgTimeOnPage: number;
    engagementScore: number;
    bounceRate: number;
    completionRate: number;
  };
  deviceStats: {
    desktop: number;
    mobile: number;
    tablet: number;
  };
  geoData: {
    country: string;
    countryCode: string;
    views: number;
  }[];
  pageStats: {
    page: number;
    views: number;
    avgTime: number;
  }[];
  period: string;
}

export interface HeatmapData {
  documentId: string | number;
  page: number;
  points: HeatmapPoint[];
}

export interface HeatmapPoint {
  x: number;
  y: number;
  intensity: number;
  timestamp?: string;
}

// Compliance Types
export type ComplianceMode = 'gdpr' | 'hipaa' | 'both' | 'none';

export interface ConsentRecord {
  id?: number;
  userId?: number;
  sessionId?: string;
  consentType: 'analytics' | 'tracking' | 'marketing' | 'functional' | 'necessary';
  consented: boolean;
  ipAddress?: string;
  consentVersion?: string;
  createdAt: string;
}

export interface ComplianceConsentProps {
  mode: ComplianceMode;
  onConsent?: (consents: ConsentRecord[]) => void;
  privacyPolicyUrl?: string;
  cookiePolicyUrl?: string;
  position?: 'top' | 'bottom' | 'floating';
  theme?: 'light' | 'dark';
}

// Audit Log Types
export type AuditAction =
  | 'document_viewed'
  | 'document_downloaded'
  | 'document_printed'
  | 'password_attempt_success'
  | 'password_attempt_failed'
  | 'settings_changed'
  | 'document_created'
  | 'document_updated'
  | 'document_deleted'
  | 'annotation_added'
  | 'annotation_deleted'
  | 'version_created'
  | 'version_restored';

export interface AuditLogEntry {
  id: number;
  timestamp: string;
  userId?: number;
  userEmail?: string;
  action: AuditAction;
  objectType?: string;
  objectId?: number;
  ipAddress?: string;
  userAgent?: string;
  details?: Record<string, unknown>;
}

export interface AuditLogProps {
  documentId?: string | number;
  userId?: number;
  actions?: AuditAction[];
  dateFrom?: string;
  dateTo?: string;
  perPage?: number;
}

// Webhook Types
export type WebhookEvent =
  | 'document.viewed'
  | 'document.downloaded'
  | 'document.printed'
  | 'password.success'
  | 'password.failed'
  | 'annotation.created'
  | 'annotation.deleted'
  | 'version.created'
  | 'progress.updated';

export interface WebhookConfig {
  id?: number;
  name: string;
  url: string;
  secret?: string;
  events: WebhookEvent[];
  active: boolean;
  createdAt?: string;
}

export interface WebhookDelivery {
  id: number;
  webhookId: number;
  event: WebhookEvent;
  payload: string;
  responseCode?: number;
  responseBody?: string;
  attempts: number;
  status: 'pending' | 'delivered' | 'failed';
  createdAt: string;
  deliveredAt?: string;
}

export interface WebhookConfigProps {
  webhooks: WebhookConfig[];
  onWebhookCreate?: (webhook: WebhookConfig) => void;
  onWebhookUpdate?: (webhook: WebhookConfig) => void;
  onWebhookDelete?: (webhookId: number) => void;
  onWebhookTest?: (webhookId: number) => void;
}

// White Label Types
export interface WhiteLabelConfig {
  customBranding: boolean;
  hidePoweredBy: boolean;
  customLogoUrl?: string;
  customCss?: string;
  brandColors?: {
    primary?: string;
    secondary?: string;
    accent?: string;
    background?: string;
    text?: string;
  };
  customText?: {
    loading?: string;
    error?: string;
    passwordPrompt?: string;
  };
  watermark?: {
    enabled: boolean;
    text?: string;
    imageUrl?: string;
    opacity?: number;
    position?: 'center' | 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right';
    rotation?: number;
  };
}

// Two-Factor Auth Types
export interface TwoFactorConfig {
  enabled: boolean;
  secret?: string;
  qrCodeUrl?: string;
  recoveryCodes?: string[];
  verified: boolean;
}

export interface TwoFactorAuthProps {
  onEnable?: (config: TwoFactorConfig) => void;
  onDisable?: () => void;
  onVerify?: (code: string) => Promise<boolean>;
}

// License Types
export type ProPlusLicenseStatus = 'valid' | 'invalid' | 'expired' | 'inactive' | 'grace_period';

export interface ProPlusLicense {
  key: string;
  status: ProPlusLicenseStatus;
  expiresAt?: string;
  features: string[];
}

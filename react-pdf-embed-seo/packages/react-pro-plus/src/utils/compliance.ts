/**
 * Compliance utilities
 */

/**
 * Anonymize an IP address for GDPR compliance
 * IPv4: Zeros out the last octet
 * IPv6: Zeros out the last segment
 */
export function anonymizeIp(ip: string): string {
  if (!ip) {
    return '';
  }

  // IPv4: 192.168.1.100 -> 192.168.1.0
  if (ip.includes('.') && !ip.includes(':')) {
    const parts = ip.split('.');
    if (parts.length === 4) {
      parts[3] = '0';
      return parts.join('.');
    }
    return ip;
  }

  // IPv6: 2001:0db8:85a3:0000:0000:8a2e:0370:7334 -> 2001:0db8:85a3:0000:0000:8a2e:0370:0000
  if (ip.includes(':')) {
    const parts = ip.split(':');
    if (parts.length > 0) {
      parts[parts.length - 1] = '0000';
      return parts.join(':');
    }
    return ip;
  }

  return ip;
}

/**
 * Check if IP anonymization is needed based on region
 */
export function shouldAnonymizeIp(region: string): boolean {
  const gdprRegions = [
    'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
    'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
    'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB', 'UK',
    'IS', 'LI', 'NO', // EEA
  ];

  return gdprRegions.includes(region.toUpperCase());
}

/**
 * Generate a consent cookie value
 */
export function generateConsentCookie(consents: Record<string, boolean>): string {
  const consentData = {
    consents,
    timestamp: Date.now(),
    version: '1.0',
  };

  return btoa(JSON.stringify(consentData));
}

/**
 * Parse a consent cookie value
 */
export function parseConsentCookie(cookie: string): {
  consents: Record<string, boolean>;
  timestamp: number;
  version: string;
} | null {
  try {
    const decoded = atob(cookie);
    return JSON.parse(decoded);
  } catch {
    return null;
  }
}

/**
 * Check if consent is required for an action
 */
export function requiresConsent(
  action: 'analytics' | 'marketing' | 'functional' | 'necessary',
  complianceMode: 'gdpr' | 'ccpa' | 'hipaa' | 'none'
): boolean {
  if (complianceMode === 'none') {
    return false;
  }

  if (action === 'necessary') {
    return false;
  }

  if (complianceMode === 'gdpr') {
    return true;
  }

  if (complianceMode === 'ccpa') {
    return action === 'marketing';
  }

  if (complianceMode === 'hipaa') {
    return action === 'analytics' || action === 'marketing';
  }

  return false;
}

/**
 * Get data retention period in days based on compliance mode
 */
export function getRetentionPeriod(
  dataType: 'analytics' | 'audit_log' | 'consent' | 'heatmap',
  complianceMode: 'gdpr' | 'ccpa' | 'hipaa' | 'none'
): number {
  const retentionPeriods: Record<string, Record<string, number>> = {
    gdpr: {
      analytics: 365,
      audit_log: 730,
      consent: 1825, // 5 years
      heatmap: 90,
    },
    ccpa: {
      analytics: 365,
      audit_log: 730,
      consent: 365,
      heatmap: 90,
    },
    hipaa: {
      analytics: 2190, // 6 years
      audit_log: 2190,
      consent: 2190,
      heatmap: 365,
    },
    none: {
      analytics: 365,
      audit_log: 365,
      consent: 365,
      heatmap: 90,
    },
  };

  return retentionPeriods[complianceMode]?.[dataType] || 365;
}

/**
 * Generate data export in compliance-friendly format
 */
export function formatDataExport(
  data: Record<string, unknown>,
  format: 'json' | 'csv'
): string {
  if (format === 'csv') {
    return objectToCsv(data);
  }

  return JSON.stringify(data, null, 2);
}

/**
 * Convert object to CSV format
 */
function objectToCsv(data: Record<string, unknown>): string {
  const lines: string[] = [];

  for (const [key, value] of Object.entries(data)) {
    if (Array.isArray(value) && value.length > 0) {
      // Handle arrays of objects
      const headers = Object.keys(value[0] as Record<string, unknown>);
      lines.push(`\n## ${key}`);
      lines.push(headers.join(','));

      for (const item of value) {
        const row = headers.map(h => {
          const val = (item as Record<string, unknown>)[h];
          const strVal = String(val ?? '');
          return strVal.includes(',') ? `"${strVal}"` : strVal;
        });
        lines.push(row.join(','));
      }
    } else if (typeof value === 'object' && value !== null) {
      lines.push(`\n## ${key}`);
      for (const [subKey, subValue] of Object.entries(value)) {
        lines.push(`${subKey},${String(subValue ?? '')}`);
      }
    } else {
      lines.push(`${key},${String(value ?? '')}`);
    }
  }

  return lines.join('\n');
}

export default {
  anonymizeIp,
  shouldAnonymizeIp,
  generateConsentCookie,
  parseConsentCookie,
  requiresConsent,
  getRetentionPeriod,
  formatDataExport,
};

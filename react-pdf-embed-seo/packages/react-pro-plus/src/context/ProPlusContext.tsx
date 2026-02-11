/**
 * ProPlusContext
 * Context provider for Pro+ Enterprise features
 */

import React, { createContext, useContext, useState, useEffect, useCallback, useMemo } from 'react';
import type {
  ProPlusLicense,
  ProPlusLicenseStatus,
  WhiteLabelConfig,
  ComplianceMode,
} from '../types';
import { validateProPlusLicense } from '../utils/license';

interface ProPlusContextValue {
  // License
  license: ProPlusLicense | null;
  isLicenseValid: boolean;
  licenseStatus: ProPlusLicenseStatus;

  // Features
  features: {
    annotations: boolean;
    versioning: boolean;
    advancedAnalytics: boolean;
    heatmaps: boolean;
    webhooks: boolean;
    compliance: boolean;
    whiteLabel: boolean;
    twoFactor: boolean;
    auditLog: boolean;
  };

  // White Label
  whiteLabel: WhiteLabelConfig | null;

  // Compliance
  complianceMode: ComplianceMode;

  // API
  apiBaseUrl: string;

  // Methods
  setLicenseKey: (key: string) => Promise<boolean>;
  refreshLicense: () => Promise<void>;
}

interface ProPlusProviderProps {
  children: React.ReactNode;
  apiBaseUrl?: string;
  licenseKey?: string;
  complianceMode?: ComplianceMode;
}

const defaultFeatures = {
  annotations: false,
  versioning: false,
  advancedAnalytics: false,
  heatmaps: false,
  webhooks: false,
  compliance: false,
  whiteLabel: false,
  twoFactor: false,
  auditLog: false,
};

const ProPlusContext = createContext<ProPlusContextValue | null>(null);

export const ProPlusProvider: React.FC<ProPlusProviderProps> = ({
  children,
  apiBaseUrl = '/wp-json/pdf-embed-seo/v1',
  licenseKey: initialLicenseKey,
  complianceMode = 'none',
}) => {
  const [license, setLicense] = useState<ProPlusLicense | null>(null);
  const [whiteLabel, setWhiteLabel] = useState<WhiteLabelConfig | null>(null);
  const [loading, setLoading] = useState(true);

  const fetchLicenseStatus = useCallback(async () => {
    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/license`);

      if (!response.ok) {
        setLicense(null);
        return;
      }

      const data = await response.json();
      setLicense({
        key: data.key || '',
        status: data.status || 'inactive',
        expiresAt: data.expires_at,
        features: data.features || [],
      });
    } catch (err) {
      console.error('Failed to fetch license status:', err);
      setLicense(null);
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const fetchWhiteLabel = useCallback(async () => {
    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/white-label`);

      if (response.ok) {
        const data = await response.json();
        setWhiteLabel(data);
      }
    } catch (err) {
      console.error('Failed to fetch white label config:', err);
    }
  }, [apiBaseUrl]);

  const setLicenseKey = useCallback(async (key: string): Promise<boolean> => {
    // Validate format first
    const validationResult = validateProPlusLicense(key);
    if (!validationResult.isValid) {
      return false;
    }

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/license`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ license_key: key }),
      });

      if (!response.ok) {
        return false;
      }

      const data = await response.json();
      setLicense({
        key: data.key || key,
        status: data.status || 'valid',
        expiresAt: data.expires_at,
        features: data.features || [],
      });

      return data.status === 'valid' || data.status === 'grace_period';
    } catch (err) {
      console.error('Failed to activate license:', err);
      return false;
    }
  }, [apiBaseUrl]);

  const refreshLicense = useCallback(async () => {
    await fetchLicenseStatus();
  }, [fetchLicenseStatus]);

  // Initialize on mount
  useEffect(() => {
    if (initialLicenseKey) {
      setLicenseKey(initialLicenseKey);
    } else {
      fetchLicenseStatus();
    }
    fetchWhiteLabel();
  }, [initialLicenseKey, setLicenseKey, fetchLicenseStatus, fetchWhiteLabel]);

  const isLicenseValid = useMemo(() => {
    return license?.status === 'valid' || license?.status === 'grace_period';
  }, [license]);

  const licenseStatus = useMemo((): ProPlusLicenseStatus => {
    return license?.status || 'inactive';
  }, [license]);

  const features = useMemo(() => {
    if (!isLicenseValid) {
      return defaultFeatures;
    }

    const licenseFeatures = license?.features || [];

    return {
      annotations: licenseFeatures.includes('annotations') || licenseFeatures.includes('*'),
      versioning: licenseFeatures.includes('versioning') || licenseFeatures.includes('*'),
      advancedAnalytics: licenseFeatures.includes('advanced_analytics') || licenseFeatures.includes('*'),
      heatmaps: licenseFeatures.includes('heatmaps') || licenseFeatures.includes('*'),
      webhooks: licenseFeatures.includes('webhooks') || licenseFeatures.includes('*'),
      compliance: licenseFeatures.includes('compliance') || licenseFeatures.includes('*'),
      whiteLabel: licenseFeatures.includes('white_label') || licenseFeatures.includes('*'),
      twoFactor: licenseFeatures.includes('2fa') || licenseFeatures.includes('*'),
      auditLog: licenseFeatures.includes('audit_log') || licenseFeatures.includes('*'),
    };
  }, [isLicenseValid, license]);

  const value = useMemo<ProPlusContextValue>(() => ({
    license,
    isLicenseValid,
    licenseStatus,
    features,
    whiteLabel,
    complianceMode,
    apiBaseUrl,
    setLicenseKey,
    refreshLicense,
  }), [
    license,
    isLicenseValid,
    licenseStatus,
    features,
    whiteLabel,
    complianceMode,
    apiBaseUrl,
    setLicenseKey,
    refreshLicense,
  ]);

  return (
    <ProPlusContext.Provider value={value}>
      {children}
    </ProPlusContext.Provider>
  );
};

export function useProPlusContext(): ProPlusContextValue {
  const context = useContext(ProPlusContext);

  if (!context) {
    throw new Error('useProPlusContext must be used within a ProPlusProvider');
  }

  return context;
}

export { ProPlusContext };
export default ProPlusProvider;

/**
 * useTwoFactorAuth Hook
 * Manages two-factor authentication setup and verification
 */

import { useState, useEffect, useCallback } from 'react';
import type { TwoFactorConfig } from '../types';

interface UseTwoFactorAuthOptions {
  apiBaseUrl?: string;
}

interface UseTwoFactorAuthReturn {
  config: TwoFactorConfig | null;
  loading: boolean;
  error: Error | null;
  enable: () => Promise<TwoFactorConfig | null>;
  disable: () => Promise<boolean>;
  verify: (code: string) => Promise<boolean>;
  generateRecoveryCodes: () => Promise<string[]>;
  isRequired: (documentId: string | number) => Promise<boolean>;
  hasPassed: (documentId: string | number) => boolean;
  markPassed: (documentId: string | number) => void;
}

export function useTwoFactorAuth(options: UseTwoFactorAuthOptions = {}): UseTwoFactorAuthReturn {
  const { apiBaseUrl = '/wp-json/pdf-embed-seo/v1' } = options;

  const [config, setConfig] = useState<TwoFactorConfig | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);
  const [passedDocuments, setPassedDocuments] = useState<Set<string | number>>(new Set());

  const fetchConfig = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/2fa/status`);

      if (!response.ok) {
        setConfig({ enabled: false, verified: false });
        return;
      }

      const data = await response.json();
      setConfig({
        enabled: data.enabled || false,
        verified: data.verified || false,
        qrCodeUrl: data.qr_code_url,
        secret: data.secret,
      });
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      setConfig({ enabled: false, verified: false });
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const enable = useCallback(async (): Promise<TwoFactorConfig | null> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/2fa/enable`, {
        method: 'POST',
      });

      if (!response.ok) {
        throw new Error('Failed to enable 2FA');
      }

      const data = await response.json();
      const newConfig: TwoFactorConfig = {
        enabled: true,
        verified: false,
        secret: data.secret,
        qrCodeUrl: data.qr_code_url,
      };
      setConfig(newConfig);
      return newConfig;
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return null;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const disable = useCallback(async (): Promise<boolean> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/2fa/disable`, {
        method: 'POST',
      });

      if (!response.ok) {
        throw new Error('Failed to disable 2FA');
      }

      setConfig({ enabled: false, verified: false });
      return true;
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return false;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const verify = useCallback(async (code: string): Promise<boolean> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/2fa/verify`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ code }),
      });

      if (!response.ok) {
        return false;
      }

      const data = await response.json();

      if (data.success) {
        setConfig(prev => prev ? { ...prev, verified: true } : null);
        return true;
      }

      return false;
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return false;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const generateRecoveryCodes = useCallback(async (): Promise<string[]> => {
    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/2fa/recovery-codes`, {
        method: 'POST',
      });

      if (!response.ok) {
        throw new Error('Failed to generate recovery codes');
      }

      const data = await response.json();
      const codes = data.codes || [];

      setConfig(prev => prev ? { ...prev, recoveryCodes: codes } : null);
      return codes;
    } catch (err) {
      console.error('Failed to generate recovery codes:', err);
      return [];
    }
  }, [apiBaseUrl]);

  const isRequired = useCallback(async (documentId: string | number): Promise<boolean> => {
    try {
      const response = await fetch(
        `${apiBaseUrl}/pro-plus/2fa/required/${documentId}`
      );

      if (!response.ok) {
        return false;
      }

      const data = await response.json();
      return data.required === true;
    } catch {
      return false;
    }
  }, [apiBaseUrl]);

  const hasPassed = useCallback((documentId: string | number): boolean => {
    return passedDocuments.has(documentId);
  }, [passedDocuments]);

  const markPassed = useCallback((documentId: string | number): void => {
    setPassedDocuments(prev => new Set(prev).add(documentId));

    // Also store in sessionStorage for persistence within session
    const passed = JSON.parse(sessionStorage.getItem('pdf_2fa_passed') || '[]');
    if (!passed.includes(documentId)) {
      passed.push(documentId);
      sessionStorage.setItem('pdf_2fa_passed', JSON.stringify(passed));
    }
  }, []);

  // Initialize from sessionStorage
  useEffect(() => {
    const passed = JSON.parse(sessionStorage.getItem('pdf_2fa_passed') || '[]');
    setPassedDocuments(new Set(passed));
  }, []);

  useEffect(() => {
    fetchConfig();
  }, [fetchConfig]);

  return {
    config,
    loading,
    error,
    enable,
    disable,
    verify,
    generateRecoveryCodes,
    isRequired,
    hasPassed,
    markPassed,
  };
}

export default useTwoFactorAuth;

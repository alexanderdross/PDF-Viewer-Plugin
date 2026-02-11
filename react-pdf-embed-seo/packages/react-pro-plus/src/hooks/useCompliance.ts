/**
 * useCompliance Hook
 * Manages GDPR/HIPAA compliance features
 */

import { useState, useCallback } from 'react';
import type { ComplianceMode, ConsentRecord } from '../types';

interface UseComplianceOptions {
  mode?: ComplianceMode;
  apiBaseUrl?: string;
}

interface UseComplianceReturn {
  mode: ComplianceMode;
  hasConsent: (type: ConsentRecord['consentType']) => Promise<boolean>;
  recordConsent: (
    type: ConsentRecord['consentType'],
    consented: boolean
  ) => Promise<ConsentRecord | null>;
  withdrawConsent: (type: ConsentRecord['consentType']) => Promise<boolean>;
  getConsentHistory: () => Promise<ConsentRecord[]>;
  exportUserData: () => Promise<Record<string, unknown> | null>;
  deleteUserData: () => Promise<boolean>;
  loading: boolean;
  error: Error | null;
}

export function useCompliance(options: UseComplianceOptions = {}): UseComplianceReturn {
  const { mode = 'gdpr', apiBaseUrl = '/wp-json/pdf-embed-seo/v1' } = options;

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const hasConsent = useCallback(async (type: ConsentRecord['consentType']): Promise<boolean> => {
    // Check localStorage first for quick access
    const localConsent = localStorage.getItem(`pdf_consent_${type}`);
    if (localConsent !== null) {
      return localConsent === 'true';
    }

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/compliance/consent/${type}`);
      if (!response.ok) return false;

      const data = await response.json();
      return data.consented === true;
    } catch {
      return false;
    }
  }, [apiBaseUrl]);

  const recordConsent = useCallback(async (
    type: ConsentRecord['consentType'],
    consented: boolean
  ): Promise<ConsentRecord | null> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/compliance/consent`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          consent_type: type,
          consented,
        }),
      });

      if (!response.ok) {
        throw new Error('Failed to record consent');
      }

      const record = await response.json();

      // Store in localStorage for quick access
      localStorage.setItem(`pdf_consent_${type}`, String(consented));

      return {
        id: record.id,
        consentType: type,
        consented,
        createdAt: record.created_at || new Date().toISOString(),
      };
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return null;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const withdrawConsent = useCallback(async (type: ConsentRecord['consentType']): Promise<boolean> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/compliance/consent/${type}`, {
        method: 'DELETE',
      });

      if (!response.ok) {
        throw new Error('Failed to withdraw consent');
      }

      localStorage.removeItem(`pdf_consent_${type}`);
      return true;
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return false;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const getConsentHistory = useCallback(async (): Promise<ConsentRecord[]> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/compliance/consent/history`);

      if (!response.ok) {
        throw new Error('Failed to fetch consent history');
      }

      const data = await response.json();
      return data.records || [];
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return [];
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const exportUserData = useCallback(async (): Promise<Record<string, unknown> | null> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/compliance/export`);

      if (!response.ok) {
        throw new Error('Failed to export user data');
      }

      return await response.json();
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return null;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const deleteUserData = useCallback(async (): Promise<boolean> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/compliance/delete`, {
        method: 'DELETE',
      });

      if (!response.ok) {
        throw new Error('Failed to delete user data');
      }

      // Clear all local consent data
      const keys = Object.keys(localStorage).filter(key => key.startsWith('pdf_consent_'));
      keys.forEach(key => localStorage.removeItem(key));

      return true;
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return false;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  return {
    mode,
    hasConsent,
    recordConsent,
    withdrawConsent,
    getConsentHistory,
    exportUserData,
    deleteUserData,
    loading,
    error,
  };
}

export default useCompliance;

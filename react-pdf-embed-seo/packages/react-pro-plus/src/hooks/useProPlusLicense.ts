import { useState, useCallback, useEffect } from 'react';
import type { ProPlusLicense, ProPlusLicenseStatus } from '../types';

interface UseProPlusLicenseReturn {
  license: ProPlusLicense | null;
  status: ProPlusLicenseStatus;
  isValid: boolean;
  loading: boolean;
  error: Error | null;
  validateLicense: (key: string) => Promise<boolean>;
  checkLicense: () => Promise<void>;
}

const LICENSE_PATTERNS = {
  proPlus: /^PDF\$PRO\+#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}-[A-Z0-9]{4}![A-Z0-9]{4}$/i,
  unlimited: /^PDF\$UNLIMITED#[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i,
  dev: /^PDF\$DEV#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i,
};

export function useProPlusLicense(): UseProPlusLicenseReturn {
  const [license, setLicense] = useState<ProPlusLicense | null>(null);
  const [status, setStatus] = useState<ProPlusLicenseStatus>('inactive');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const isValid = status === 'valid' || status === 'grace_period';

  const validateLicense = useCallback(async (key: string): Promise<boolean> => {
    // Check format locally first
    const isValidFormat =
      LICENSE_PATTERNS.proPlus.test(key) ||
      LICENSE_PATTERNS.unlimited.test(key) ||
      LICENSE_PATTERNS.dev.test(key);

    if (!isValidFormat) {
      setStatus('invalid');
      return false;
    }

    setLoading(true);
    setError(null);

    try {
      const response = await fetch('/api/pdf-embed-seo/v1/license/validate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ key }),
      });

      if (!response.ok) {
        throw new Error('License validation failed');
      }

      const data = await response.json();
      setLicense(data.license);
      setStatus(data.license.status);

      return data.license.status === 'valid';
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      setStatus('invalid');
      return false;
    } finally {
      setLoading(false);
    }
  }, []);

  const checkLicense = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch('/api/pdf-embed-seo/v1/license/status');

      if (!response.ok) {
        throw new Error('Failed to check license');
      }

      const data = await response.json();
      setLicense(data.license);
      setStatus(data.license?.status || 'inactive');
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      setStatus('inactive');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    checkLicense();
  }, [checkLicense]);

  return {
    license,
    status,
    isValid,
    loading,
    error,
    validateLicense,
    checkLicense,
  };
}

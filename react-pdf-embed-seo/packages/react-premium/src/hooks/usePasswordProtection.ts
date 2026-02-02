/**
 * usePasswordProtection Hook
 * Manage password protection for PDF documents
 */

import { useState, useCallback, useEffect } from 'react';
import type { PdfDocument } from '@pdf-embed-seo/core';
import { usePdfContext } from '@pdf-embed-seo/react';

/**
 * usePasswordProtection options
 */
export interface UsePasswordProtectionOptions {
  /** Session storage key prefix */
  storagePrefix?: string;
  /** Session duration in seconds */
  sessionDuration?: number;
}

/**
 * usePasswordProtection return value
 */
export interface UsePasswordProtectionResult {
  /** Whether document requires password */
  isProtected: boolean;
  /** Whether password has been verified */
  isUnlocked: boolean;
  /** Verify password */
  verifyPassword: (password: string) => Promise<boolean>;
  /** Lock document (clear session) */
  lock: () => void;
  /** Loading state */
  isVerifying: boolean;
  /** Error message */
  error: string | null;
}

/**
 * usePasswordProtection Hook
 *
 * @example
 * ```tsx
 * function ProtectedPdf({ document }) {
 *   const { isProtected, isUnlocked, verifyPassword, error } = usePasswordProtection(document);
 *
 *   if (isProtected && !isUnlocked) {
 *     return (
 *       <PdfPasswordModal
 *         isOpen
 *         onSubmit={verifyPassword}
 *         error={error}
 *       />
 *     );
 *   }
 *
 *   return <PdfViewer src={document} />;
 * }
 * ```
 */
export function usePasswordProtection(
  document: PdfDocument | null | undefined,
  options: UsePasswordProtectionOptions = {}
): UsePasswordProtectionResult {
  const { storagePrefix = 'pdf_unlock_', sessionDuration = 3600 } = options;
  const { apiClient } = usePdfContext();

  const [isUnlocked, setIsUnlocked] = useState(false);
  const [isVerifying, setIsVerifying] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const isProtected = document?.passwordProtected ?? false;
  const storageKey = document ? `${storagePrefix}${document.id}` : '';

  // Check session storage on mount
  useEffect(() => {
    if (!isProtected || !storageKey) return;

    const stored = sessionStorage.getItem(storageKey);
    if (stored) {
      try {
        const { expiresAt } = JSON.parse(stored);
        if (new Date(expiresAt) > new Date()) {
          setIsUnlocked(true);
        } else {
          sessionStorage.removeItem(storageKey);
        }
      } catch {
        sessionStorage.removeItem(storageKey);
      }
    }
  }, [isProtected, storageKey]);

  // Verify password
  const verifyPassword = useCallback(
    async (password: string): Promise<boolean> => {
      if (!document || !apiClient) return false;

      setIsVerifying(true);
      setError(null);

      try {
        const response = await apiClient.verifyPassword(document.id, password);

        if (response.success) {
          // Store session
          const expiresAt = new Date(Date.now() + sessionDuration * 1000).toISOString();
          sessionStorage.setItem(
            storageKey,
            JSON.stringify({ expiresAt, token: response.accessToken })
          );
          setIsUnlocked(true);
          return true;
        } else {
          setError(response.message || 'Incorrect password');
          return false;
        }
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Verification failed');
        return false;
      } finally {
        setIsVerifying(false);
      }
    },
    [document, apiClient, storageKey, sessionDuration]
  );

  // Lock document
  const lock = useCallback(() => {
    if (storageKey) {
      sessionStorage.removeItem(storageKey);
    }
    setIsUnlocked(false);
  }, [storageKey]);

  return {
    isProtected,
    isUnlocked,
    verifyPassword,
    lock,
    isVerifying,
    error,
  };
}

export default usePasswordProtection;

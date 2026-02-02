/**
 * PdfPasswordModal Component
 * Modal for password-protected PDF documents
 */

'use client';

import React, { useState, useCallback, FormEvent } from 'react';
import { CSS_CLASSES } from '@pdf-embed-seo/core';

/**
 * PdfPasswordModal props
 */
export interface PdfPasswordModalProps {
  /** Whether modal is open */
  isOpen: boolean;
  /** Document title (for display) */
  documentTitle?: string;
  /** Submit handler */
  onSubmit: (password: string) => Promise<boolean>;
  /** Cancel handler */
  onCancel?: () => void;
  /** Error message */
  error?: string;
  /** Loading state */
  isLoading?: boolean;
  /** Custom title */
  title?: string;
  /** Custom submit button text */
  submitText?: string;
  /** Custom cancel button text */
  cancelText?: string;
  /** Additional CSS class */
  className?: string;
}

/**
 * PdfPasswordModal Component
 *
 * @example
 * ```tsx
 * <PdfPasswordModal
 *   isOpen={isPasswordRequired}
 *   documentTitle={document.title}
 *   onSubmit={async (password) => {
 *     const result = await verifyPassword(document.id, password);
 *     return result.success;
 *   }}
 *   onCancel={() => router.back()}
 * />
 * ```
 */
export function PdfPasswordModal({
  isOpen,
  documentTitle,
  onSubmit,
  onCancel,
  error: externalError,
  isLoading: externalLoading,
  title = 'Password Required',
  submitText = 'Unlock',
  cancelText = 'Cancel',
  className = '',
}: PdfPasswordModalProps): React.ReactElement | null {
  const [password, setPassword] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  // Use external state if provided
  const loading = externalLoading !== undefined ? externalLoading : isLoading;
  const errorMessage = externalError || error;

  const handleSubmit = useCallback(
    async (e: FormEvent) => {
      e.preventDefault();

      if (!password.trim()) {
        setError('Please enter a password');
        return;
      }

      setIsLoading(true);
      setError(null);

      try {
        const success = await onSubmit(password);
        if (!success) {
          setError('Incorrect password. Please try again.');
        }
      } catch (err) {
        setError(err instanceof Error ? err.message : 'An error occurred');
      } finally {
        setIsLoading(false);
      }
    },
    [password, onSubmit]
  );

  const handlePasswordChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      setPassword(e.target.value);
      if (error) setError(null);
    },
    [error]
  );

  if (!isOpen) return null;

  return (
    <div className={`${CSS_CLASSES.premium.passwordModal} pdf-modal-overlay ${className}`}>
      <div className="pdf-modal-backdrop" onClick={onCancel} />

      <div className="pdf-modal-content" role="dialog" aria-modal="true" aria-labelledby="password-modal-title">
        <h2 id="password-modal-title" className="pdf-modal-title">
          {title}
        </h2>

        {documentTitle && (
          <p className="pdf-modal-document-title">
            Document: <strong>{documentTitle}</strong>
          </p>
        )}

        <p className="pdf-modal-description">
          This document is password protected. Please enter the password to view it.
        </p>

        <form onSubmit={handleSubmit} className="pdf-password-form">
          <div className="pdf-form-group">
            <label htmlFor="pdf-password" className="pdf-form-label">
              Password
            </label>
            <input
              type="password"
              id="pdf-password"
              value={password}
              onChange={handlePasswordChange}
              placeholder="Enter password"
              className="pdf-form-input"
              autoFocus
              autoComplete="current-password"
              disabled={loading}
            />
          </div>

          {errorMessage && (
            <div className="pdf-form-error" role="alert">
              {errorMessage}
            </div>
          )}

          <div className="pdf-modal-actions">
            {onCancel && (
              <button
                type="button"
                onClick={onCancel}
                className="pdf-button pdf-button-secondary"
                disabled={loading}
              >
                {cancelText}
              </button>
            )}
            <button
              type="submit"
              className="pdf-button pdf-button-primary"
              disabled={loading || !password.trim()}
            >
              {loading ? (
                <span className="pdf-button-loading">
                  <span className="pdf-spinner-small" />
                  Verifying...
                </span>
              ) : (
                submitText
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default PdfPasswordModal;

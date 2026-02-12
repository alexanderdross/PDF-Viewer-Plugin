/**
 * PdfTwoFactorAuth Component
 * Two-factor authentication setup and verification
 */

import React, { useState, useEffect } from 'react';
import { useTwoFactorAuth } from '../hooks/useTwoFactorAuth';
import type { TwoFactorAuthProps, TwoFactorConfig } from '../types';

export const PdfTwoFactorAuth: React.FC<TwoFactorAuthProps> = ({
  onEnable,
  onDisable,
  onVerify,
}) => {
  const { config, loading, enable, disable, verify, generateRecoveryCodes } = useTwoFactorAuth();
  const [step, setStep] = useState<'setup' | 'verify' | 'recovery' | 'done'>('setup');
  const [code, setCode] = useState('');
  const [error, setError] = useState('');
  const [recoveryCodes, setRecoveryCodes] = useState<string[]>([]);

  useEffect(() => {
    if (config?.verified) {
      setStep('done');
    }
  }, [config]);

  const handleEnable = async () => {
    setError('');
    const result = await enable();
    if (result) {
      setStep('verify');
      onEnable?.(result);
    } else {
      setError('Failed to enable 2FA. Please try again.');
    }
  };

  const handleVerify = async () => {
    if (code.length !== 6) {
      setError('Please enter a 6-digit code');
      return;
    }

    setError('');
    const success = await verify(code);

    if (success) {
      if (onVerify) {
        const verified = await onVerify(code);
        if (verified) {
          const codes = await generateRecoveryCodes();
          setRecoveryCodes(codes);
          setStep('recovery');
        } else {
          setError('Verification failed. Please try again.');
        }
      } else {
        const codes = await generateRecoveryCodes();
        setRecoveryCodes(codes);
        setStep('recovery');
      }
    } else {
      setError('Invalid code. Please try again.');
    }

    setCode('');
  };

  const handleDisable = async () => {
    if (!confirm('Are you sure you want to disable two-factor authentication?')) return;

    setError('');
    const success = await disable();

    if (success) {
      setStep('setup');
      onDisable?.();
    } else {
      setError('Failed to disable 2FA. Please try again.');
    }
  };

  const copyRecoveryCodes = () => {
    navigator.clipboard.writeText(recoveryCodes.join('\n'));
  };

  if (loading) {
    return (
      <div className="pdf-2fa pdf-2fa-loading">
        <div className="pdf-2fa-spinner" />
        <span>Loading...</span>
      </div>
    );
  }

  return (
    <div className="pdf-2fa">
      <h3>Two-Factor Authentication</h3>

      {error && <div className="pdf-2fa-error">{error}</div>}

      {step === 'setup' && (
        <div className="pdf-2fa-setup">
          <p>Add an extra layer of security to your account by enabling two-factor authentication.</p>
          <button className="pdf-2fa-btn pdf-2fa-btn-primary" onClick={handleEnable}>
            Enable 2FA
          </button>
        </div>
      )}

      {step === 'verify' && config && (
        <div className="pdf-2fa-verify">
          <p>Scan this QR code with your authenticator app:</p>

          {config.qrCodeUrl && (
            <div className="pdf-2fa-qr">
              <img src={config.qrCodeUrl} alt="2FA QR Code" />
            </div>
          )}

          <p className="pdf-2fa-secret">
            Or enter this secret manually: <code>{config.secret}</code>
          </p>

          <div className="pdf-2fa-code-input">
            <label>Enter the 6-digit code from your app:</label>
            <input
              type="text"
              value={code}
              onChange={(e) => setCode(e.target.value.replace(/\D/g, '').slice(0, 6))}
              placeholder="000000"
              maxLength={6}
              autoFocus
            />
            <button
              className="pdf-2fa-btn pdf-2fa-btn-primary"
              onClick={handleVerify}
              disabled={code.length !== 6}
            >
              Verify
            </button>
          </div>
        </div>
      )}

      {step === 'recovery' && (
        <div className="pdf-2fa-recovery">
          <h4>Save Your Recovery Codes</h4>
          <p>Store these codes in a safe place. You can use them to access your account if you lose your authenticator device.</p>

          <div className="pdf-2fa-recovery-codes">
            {recoveryCodes.map((code, index) => (
              <code key={index}>{code}</code>
            ))}
          </div>

          <div className="pdf-2fa-recovery-actions">
            <button className="pdf-2fa-btn" onClick={copyRecoveryCodes}>
              Copy Codes
            </button>
            <button className="pdf-2fa-btn pdf-2fa-btn-primary" onClick={() => setStep('done')}>
              I've Saved My Codes
            </button>
          </div>
        </div>
      )}

      {step === 'done' && (
        <div className="pdf-2fa-done">
          <div className="pdf-2fa-success">
            <span className="pdf-2fa-check">✓</span>
            Two-factor authentication is enabled
          </div>

          <button className="pdf-2fa-btn pdf-2fa-btn-danger" onClick={handleDisable}>
            Disable 2FA
          </button>
        </div>
      )}

      <style>{`
        .pdf-2fa {
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
          max-width: 400px;
          padding: 20px;
          background: #fff;
          border-radius: 8px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pdf-2fa h3 {
          margin: 0 0 16px;
          font-size: 18px;
        }
        .pdf-2fa h4 {
          margin: 0 0 12px;
          font-size: 16px;
        }
        .pdf-2fa p {
          margin: 0 0 16px;
          color: #666;
          font-size: 14px;
        }
        .pdf-2fa-loading {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
          padding: 40px;
        }
        .pdf-2fa-spinner {
          width: 24px;
          height: 24px;
          border: 3px solid #ddd;
          border-top-color: #2196f3;
          border-radius: 50%;
          animation: spin 1s linear infinite;
        }
        @keyframes spin {
          to { transform: rotate(360deg); }
        }
        .pdf-2fa-error {
          padding: 12px;
          background: #ffebee;
          color: #c62828;
          border-radius: 4px;
          margin-bottom: 16px;
          font-size: 14px;
        }
        .pdf-2fa-btn {
          padding: 10px 20px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background: #fff;
          font-size: 14px;
          cursor: pointer;
          transition: all 0.2s ease;
        }
        .pdf-2fa-btn:hover {
          background: #f5f5f5;
        }
        .pdf-2fa-btn-primary {
          background: #2196f3;
          color: #fff;
          border-color: #2196f3;
        }
        .pdf-2fa-btn-primary:hover {
          background: #1976d2;
        }
        .pdf-2fa-btn-primary:disabled {
          opacity: 0.5;
          cursor: not-allowed;
        }
        .pdf-2fa-btn-danger {
          background: #f44336;
          color: #fff;
          border-color: #f44336;
        }
        .pdf-2fa-btn-danger:hover {
          background: #d32f2f;
        }
        .pdf-2fa-qr {
          text-align: center;
          margin: 16px 0;
        }
        .pdf-2fa-qr img {
          max-width: 200px;
          border: 4px solid #fff;
          box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .pdf-2fa-secret {
          text-align: center;
          background: #f5f5f5;
          padding: 12px;
          border-radius: 4px;
        }
        .pdf-2fa-secret code {
          font-family: monospace;
          font-weight: bold;
          font-size: 14px;
        }
        .pdf-2fa-code-input {
          margin-top: 16px;
        }
        .pdf-2fa-code-input label {
          display: block;
          margin-bottom: 8px;
          font-size: 14px;
        }
        .pdf-2fa-code-input input {
          width: 100%;
          padding: 12px;
          border: 1px solid #ddd;
          border-radius: 4px;
          font-size: 24px;
          text-align: center;
          letter-spacing: 8px;
          font-family: monospace;
          margin-bottom: 12px;
        }
        .pdf-2fa-code-input input:focus {
          border-color: #2196f3;
          outline: none;
        }
        .pdf-2fa-recovery-codes {
          display: grid;
          grid-template-columns: repeat(2, 1fr);
          gap: 8px;
          padding: 16px;
          background: #f9f9f9;
          border-radius: 4px;
          margin-bottom: 16px;
        }
        .pdf-2fa-recovery-codes code {
          font-family: monospace;
          font-size: 13px;
          padding: 4px 8px;
          background: #fff;
          border-radius: 4px;
          text-align: center;
        }
        .pdf-2fa-recovery-actions {
          display: flex;
          gap: 8px;
          justify-content: flex-end;
        }
        .pdf-2fa-success {
          display: flex;
          align-items: center;
          gap: 12px;
          padding: 16px;
          background: #e8f5e9;
          border-radius: 4px;
          margin-bottom: 16px;
          color: #2e7d32;
        }
        .pdf-2fa-check {
          width: 24px;
          height: 24px;
          background: #4caf50;
          color: #fff;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 14px;
        }
      `}</style>
    </div>
  );
};

export default PdfTwoFactorAuth;

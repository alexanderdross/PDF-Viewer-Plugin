/**
 * PdfComplianceConsent Component
 * GDPR/HIPAA compliance consent banner
 */

import React, { useState, useCallback, useEffect } from 'react';
import { useCompliance } from '../hooks/useCompliance';
import type { ComplianceConsentProps, ConsentRecord } from '../types';

export const PdfComplianceConsent: React.FC<ComplianceConsentProps> = ({
  mode,
  onConsent,
  privacyPolicyUrl,
  cookiePolicyUrl,
  position = 'bottom',
  theme = 'light',
}) => {
  const { hasConsent, recordConsent, loading } = useCompliance();
  const [showBanner, setShowBanner] = useState(false);
  const [showDetails, setShowDetails] = useState(false);
  const [consents, setConsents] = useState({
    necessary: true,
    functional: false,
    analytics: false,
    marketing: false,
  });

  useEffect(() => {
    // Check if consent already given
    const checkConsent = async () => {
      const hasExisting = await hasConsent('necessary');
      setShowBanner(!hasExisting);
    };
    checkConsent();
  }, [hasConsent]);

  const handleAcceptAll = useCallback(async () => {
    const allConsents = {
      necessary: true,
      functional: true,
      analytics: true,
      marketing: true,
    };

    const records: ConsentRecord[] = [];
    for (const [type, consented] of Object.entries(allConsents)) {
      const record = await recordConsent(type as ConsentRecord['consentType'], consented);
      if (record) records.push(record);
    }

    onConsent?.(records);
    setShowBanner(false);
  }, [recordConsent, onConsent]);

  const handleAcceptSelected = useCallback(async () => {
    const records: ConsentRecord[] = [];
    for (const [type, consented] of Object.entries(consents)) {
      const record = await recordConsent(type as ConsentRecord['consentType'], consented);
      if (record) records.push(record);
    }

    onConsent?.(records);
    setShowBanner(false);
  }, [consents, recordConsent, onConsent]);

  const handleDeclineAll = useCallback(async () => {
    const record = await recordConsent('necessary', true);
    onConsent?.(record ? [record] : []);
    setShowBanner(false);
  }, [recordConsent, onConsent]);

  const toggleConsent = (type: keyof typeof consents) => {
    if (type === 'necessary') return; // Necessary cannot be disabled
    setConsents(prev => ({ ...prev, [type]: !prev[type] }));
  };

  if (!showBanner || mode === 'none') return null;

  const positionClasses = {
    top: 'pdf-consent-top',
    bottom: 'pdf-consent-bottom',
    floating: 'pdf-consent-floating',
  };

  return (
    <div className={`pdf-consent-banner ${positionClasses[position]} ${theme}`}>
      <div className="pdf-consent-content">
        <div className="pdf-consent-text">
          <h3>Privacy & Cookies</h3>
          <p>
            We use cookies and similar technologies to enhance your experience.
            {mode === 'gdpr' && ' Under GDPR, we need your consent to process your data.'}
            {mode === 'hipaa' && ' We comply with HIPAA regulations for health information protection.'}
          </p>
          {(privacyPolicyUrl || cookiePolicyUrl) && (
            <p className="pdf-consent-links">
              {privacyPolicyUrl && (
                <a href={privacyPolicyUrl} target="_blank" rel="noopener noreferrer">
                  Privacy Policy
                </a>
              )}
              {privacyPolicyUrl && cookiePolicyUrl && ' | '}
              {cookiePolicyUrl && (
                <a href={cookiePolicyUrl} target="_blank" rel="noopener noreferrer">
                  Cookie Policy
                </a>
              )}
            </p>
          )}
        </div>

        {showDetails && (
          <div className="pdf-consent-details">
            <div className="pdf-consent-option">
              <label>
                <input
                  type="checkbox"
                  checked={consents.necessary}
                  disabled
                />
                <span className="pdf-consent-option-label">
                  <strong>Necessary</strong>
                  <small>Required for the website to function properly</small>
                </span>
              </label>
            </div>
            <div className="pdf-consent-option">
              <label>
                <input
                  type="checkbox"
                  checked={consents.functional}
                  onChange={() => toggleConsent('functional')}
                />
                <span className="pdf-consent-option-label">
                  <strong>Functional</strong>
                  <small>Enhanced functionality like preferences</small>
                </span>
              </label>
            </div>
            <div className="pdf-consent-option">
              <label>
                <input
                  type="checkbox"
                  checked={consents.analytics}
                  onChange={() => toggleConsent('analytics')}
                />
                <span className="pdf-consent-option-label">
                  <strong>Analytics</strong>
                  <small>Help us improve by tracking usage</small>
                </span>
              </label>
            </div>
            <div className="pdf-consent-option">
              <label>
                <input
                  type="checkbox"
                  checked={consents.marketing}
                  onChange={() => toggleConsent('marketing')}
                />
                <span className="pdf-consent-option-label">
                  <strong>Marketing</strong>
                  <small>Personalized content and ads</small>
                </span>
              </label>
            </div>
          </div>
        )}

        <div className="pdf-consent-actions">
          <button
            className="pdf-consent-btn pdf-consent-btn-decline"
            onClick={handleDeclineAll}
            disabled={loading}
          >
            Decline All
          </button>
          <button
            className="pdf-consent-btn pdf-consent-btn-customize"
            onClick={() => setShowDetails(!showDetails)}
          >
            {showDetails ? 'Hide Details' : 'Customize'}
          </button>
          {showDetails ? (
            <button
              className="pdf-consent-btn pdf-consent-btn-accept"
              onClick={handleAcceptSelected}
              disabled={loading}
            >
              Accept Selected
            </button>
          ) : (
            <button
              className="pdf-consent-btn pdf-consent-btn-accept"
              onClick={handleAcceptAll}
              disabled={loading}
            >
              Accept All
            </button>
          )}
        </div>
      </div>

      <style>{`
        .pdf-consent-banner {
          position: fixed;
          left: 0;
          right: 0;
          z-index: 10000;
          padding: 16px;
          background: #fff;
          box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .pdf-consent-banner.dark {
          background: #1a1a1a;
          color: #fff;
        }
        .pdf-consent-top {
          top: 0;
          box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .pdf-consent-bottom {
          bottom: 0;
        }
        .pdf-consent-floating {
          bottom: 20px;
          left: 20px;
          right: 20px;
          border-radius: 8px;
        }
        .pdf-consent-content {
          max-width: 1200px;
          margin: 0 auto;
        }
        .pdf-consent-text h3 {
          margin: 0 0 8px;
          font-size: 16px;
          font-weight: 600;
        }
        .pdf-consent-text p {
          margin: 0 0 8px;
          font-size: 14px;
          color: #666;
        }
        .pdf-consent-banner.dark .pdf-consent-text p {
          color: #aaa;
        }
        .pdf-consent-links a {
          color: #2196f3;
          text-decoration: none;
        }
        .pdf-consent-links a:hover {
          text-decoration: underline;
        }
        .pdf-consent-details {
          margin: 16px 0;
          padding: 12px;
          background: #f5f5f5;
          border-radius: 8px;
        }
        .pdf-consent-banner.dark .pdf-consent-details {
          background: #333;
        }
        .pdf-consent-option {
          margin: 8px 0;
        }
        .pdf-consent-option label {
          display: flex;
          align-items: flex-start;
          gap: 10px;
          cursor: pointer;
        }
        .pdf-consent-option input[type="checkbox"] {
          margin-top: 2px;
          width: 18px;
          height: 18px;
        }
        .pdf-consent-option-label {
          flex: 1;
        }
        .pdf-consent-option-label strong {
          display: block;
          font-size: 14px;
        }
        .pdf-consent-option-label small {
          display: block;
          font-size: 12px;
          color: #888;
        }
        .pdf-consent-actions {
          display: flex;
          gap: 8px;
          flex-wrap: wrap;
          justify-content: flex-end;
        }
        .pdf-consent-btn {
          padding: 10px 20px;
          border: none;
          border-radius: 4px;
          font-size: 14px;
          cursor: pointer;
          transition: all 0.2s ease;
        }
        .pdf-consent-btn:disabled {
          opacity: 0.5;
          cursor: not-allowed;
        }
        .pdf-consent-btn-decline {
          background: #f0f0f0;
          color: #666;
        }
        .pdf-consent-btn-decline:hover:not(:disabled) {
          background: #e0e0e0;
        }
        .pdf-consent-btn-customize {
          background: transparent;
          border: 1px solid #ddd;
          color: #333;
        }
        .pdf-consent-banner.dark .pdf-consent-btn-customize {
          border-color: #555;
          color: #fff;
        }
        .pdf-consent-btn-customize:hover:not(:disabled) {
          background: rgba(0,0,0,0.05);
        }
        .pdf-consent-btn-accept {
          background: #4caf50;
          color: #fff;
        }
        .pdf-consent-btn-accept:hover:not(:disabled) {
          background: #43a047;
        }
      `}</style>
    </div>
  );
};

export default PdfComplianceConsent;

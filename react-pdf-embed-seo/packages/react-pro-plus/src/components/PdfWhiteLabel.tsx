/**
 * PdfWhiteLabel Component
 * Wrapper component that applies white label branding
 */

import React, { useEffect } from 'react';
import { useWhiteLabel } from '../hooks/useWhiteLabel';
import type { WhiteLabelConfig } from '../types';

export interface PdfWhiteLabelProps {
  config?: WhiteLabelConfig;
  children: React.ReactNode;
}

export const PdfWhiteLabel: React.FC<PdfWhiteLabelProps> = ({
  config: propConfig,
  children,
}) => {
  const { config: hookConfig } = useWhiteLabel();
  const config = propConfig || hookConfig;

  useEffect(() => {
    if (!config?.customCss) return;

    const styleId = 'pdf-white-label-custom-css';
    let styleElement = document.getElementById(styleId);

    if (!styleElement) {
      styleElement = document.createElement('style');
      styleElement.id = styleId;
      document.head.appendChild(styleElement);
    }

    styleElement.textContent = config.customCss;

    return () => {
      const el = document.getElementById(styleId);
      if (el) el.remove();
    };
  }, [config?.customCss]);

  const brandColors = config?.brandColors || {};

  return (
    <div
      className="pdf-white-label-wrapper"
      style={{
        '--pdf-wl-primary': brandColors.primary || '#0073aa',
        '--pdf-wl-secondary': brandColors.secondary || '#23282d',
        '--pdf-wl-accent': brandColors.accent || '#00a0d2',
        '--pdf-wl-background': brandColors.background || '#ffffff',
        '--pdf-wl-text': brandColors.text || '#333333',
      } as React.CSSProperties}
    >
      {config?.customBranding && config?.customLogoUrl && (
        <div className="pdf-white-label-logo">
          <img
            src={config.customLogoUrl}
            alt="Logo"
            className="pdf-white-label-logo-img"
          />
        </div>
      )}

      {children}

      {config?.watermark?.enabled && (
        <div
          className="pdf-white-label-watermark"
          style={{
            opacity: config.watermark.opacity || 0.1,
            transform: `rotate(${config.watermark.rotation || -45}deg)`,
          }}
        >
          {config.watermark.imageUrl ? (
            <img src={config.watermark.imageUrl} alt="" />
          ) : (
            <span>{config.watermark.text}</span>
          )}
        </div>
      )}

      {!config?.hidePoweredBy && (
        <div className="pdf-white-label-powered-by">
          Powered by PDF Embed SEO
        </div>
      )}

      <style>{`
        .pdf-white-label-wrapper {
          position: relative;
          --pdf-wl-primary: #0073aa;
          --pdf-wl-secondary: #23282d;
          --pdf-wl-accent: #00a0d2;
          --pdf-wl-background: #ffffff;
          --pdf-wl-text: #333333;
        }
        .pdf-white-label-wrapper *:not(.pdf-white-label-powered-by) {
          font-family: inherit;
        }
        .pdf-white-label-wrapper a {
          color: var(--pdf-wl-primary);
        }
        .pdf-white-label-wrapper button {
          background-color: var(--pdf-wl-primary);
        }
        .pdf-white-label-logo {
          padding: 12px;
          text-align: center;
        }
        .pdf-white-label-logo-img {
          max-height: 48px;
          max-width: 200px;
          object-fit: contain;
        }
        .pdf-white-label-watermark {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%) rotate(-45deg);
          pointer-events: none;
          z-index: 1000;
          font-size: 48px;
          color: var(--pdf-wl-text);
          white-space: nowrap;
          user-select: none;
        }
        .pdf-white-label-watermark img {
          max-width: 300px;
          max-height: 100px;
        }
        .pdf-white-label-powered-by {
          text-align: center;
          padding: 8px;
          font-size: 11px;
          color: #888;
        }
      `}</style>
    </div>
  );
};

export default PdfWhiteLabel;

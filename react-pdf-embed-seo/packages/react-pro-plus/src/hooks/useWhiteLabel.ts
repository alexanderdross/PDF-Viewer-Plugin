/**
 * useWhiteLabel Hook
 * Manages white label branding configuration
 */

import { useState, useEffect, useCallback, useContext, createContext } from 'react';
import type { WhiteLabelConfig } from '../types';

interface UseWhiteLabelOptions {
  apiBaseUrl?: string;
}

interface UseWhiteLabelReturn {
  config: WhiteLabelConfig | null;
  loading: boolean;
  error: Error | null;
  updateConfig: (config: Partial<WhiteLabelConfig>) => Promise<boolean>;
  getCssVariables: () => string;
  refresh: () => Promise<void>;
}

const defaultConfig: WhiteLabelConfig = {
  customBranding: false,
  hidePoweredBy: false,
  brandColors: {
    primary: '#0073aa',
    secondary: '#23282d',
    accent: '#00a0d2',
    background: '#ffffff',
    text: '#333333',
  },
};

export function useWhiteLabel(options: UseWhiteLabelOptions = {}): UseWhiteLabelReturn {
  const { apiBaseUrl = '/wp-json/pdf-embed-seo/v1' } = options;

  const [config, setConfig] = useState<WhiteLabelConfig | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const fetchConfig = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/white-label`);

      if (!response.ok) {
        // Use defaults if no config exists
        setConfig(defaultConfig);
        return;
      }

      const data = await response.json();
      setConfig({
        ...defaultConfig,
        ...data,
        brandColors: {
          ...defaultConfig.brandColors,
          ...data.brandColors,
        },
      });
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      setConfig(defaultConfig);
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const updateConfig = useCallback(async (
    updates: Partial<WhiteLabelConfig>
  ): Promise<boolean> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/white-label`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(updates),
      });

      if (!response.ok) {
        throw new Error('Failed to update white label config');
      }

      const updatedConfig = await response.json();
      setConfig(prev => ({
        ...prev,
        ...updatedConfig,
        brandColors: {
          ...prev?.brandColors,
          ...updatedConfig.brandColors,
        },
      }));
      return true;
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return false;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const getCssVariables = useCallback((): string => {
    if (!config) return '';

    const colors = config.brandColors || {};
    let css = ':root {\n';
    css += `  --pdf-wl-primary: ${colors.primary || '#0073aa'};\n`;
    css += `  --pdf-wl-secondary: ${colors.secondary || '#23282d'};\n`;
    css += `  --pdf-wl-accent: ${colors.accent || '#00a0d2'};\n`;
    css += `  --pdf-wl-background: ${colors.background || '#ffffff'};\n`;
    css += `  --pdf-wl-text: ${colors.text || '#333333'};\n`;
    css += '}\n';

    if (config.customCss) {
      css += `\n/* Custom CSS */\n${config.customCss}\n`;
    }

    return css;
  }, [config]);

  const refresh = useCallback(async () => {
    await fetchConfig();
  }, [fetchConfig]);

  useEffect(() => {
    fetchConfig();
  }, [fetchConfig]);

  return {
    config,
    loading,
    error,
    updateConfig,
    getCssVariables,
    refresh,
  };
}

export default useWhiteLabel;

/**
 * PDF Provider Component
 * Provides global configuration and API client to child components
 */

'use client';

import React, { useState, useMemo, useEffect, ReactNode } from 'react';
import { PdfContext, PdfContextValue } from './PdfContext';
import type { PdfProviderConfig, ViewerTheme } from '@pdf-embed-seo/core';
import {
  createApiClient,
  createWordPressClient,
  createDrupalClient,
  createStandaloneClient,
  PdfApiClient,
  DEFAULT_SETTINGS,
} from '@pdf-embed-seo/core';

/**
 * PdfProvider props
 */
export interface PdfProviderProps {
  /** Child components */
  children: ReactNode;
  /** Provider configuration */
  config?: Partial<PdfProviderConfig>;
  /** Initial theme */
  initialTheme?: ViewerTheme;
  /** Whether premium features are enabled */
  premium?: boolean;
  /** Custom API client (overrides mode-based client) */
  apiClient?: PdfApiClient;
}

/**
 * PdfProvider Component
 *
 * @example
 * ```tsx
 * <PdfProvider
 *   config={{
 *     mode: 'wordpress',
 *     apiUrl: 'https://example.com/wp-json/pdf-embed-seo/v1',
 *     siteUrl: 'https://example.com',
 *   }}
 * >
 *   <App />
 * </PdfProvider>
 * ```
 */
export function PdfProvider({
  children,
  config = {},
  initialTheme,
  premium = false,
  apiClient: customApiClient,
}: PdfProviderProps): React.ReactElement {
  // Merge config with defaults
  const fullConfig: PdfProviderConfig = {
    mode: 'standalone',
    theme: 'light',
    siteUrl: '',
    siteName: 'PDF Documents',
    ...config,
  };

  // Theme state
  const [theme, setTheme] = useState<ViewerTheme>(
    initialTheme || fullConfig.theme || 'light'
  );

  // Handle system theme preference
  useEffect(() => {
    if (theme !== 'system') return;

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const handleChange = () => {
      // Theme state stays 'system', actual rendering handles preference
    };

    mediaQuery.addEventListener('change', handleChange);
    return () => mediaQuery.removeEventListener('change', handleChange);
  }, [theme]);

  // Create or use provided API client
  const apiClient = useMemo(() => {
    if (customApiClient) return customApiClient;

    const apiUrl = fullConfig.apiUrl || '';

    switch (fullConfig.mode) {
      case 'wordpress':
        return createWordPressClient({ baseUrl: apiUrl });
      case 'drupal':
        return createDrupalClient({ baseUrl: apiUrl });
      case 'standalone':
      default:
        return createStandaloneClient([], fullConfig.siteUrl);
    }
  }, [customApiClient, fullConfig.mode, fullConfig.apiUrl, fullConfig.siteUrl]);

  // Context value
  const contextValue: PdfContextValue = useMemo(
    () => ({
      config: fullConfig,
      apiClient: apiClient as PdfApiClient,
      theme,
      setTheme,
      isPremium: premium,
      siteUrl: fullConfig.siteUrl || '',
      siteName: fullConfig.siteName || DEFAULT_SETTINGS.siteName,
    }),
    [fullConfig, apiClient, theme, premium]
  );

  return (
    <PdfContext.Provider value={contextValue}>
      {children}
    </PdfContext.Provider>
  );
}

export default PdfProvider;

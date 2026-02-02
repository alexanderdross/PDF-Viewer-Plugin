/**
 * PDF Provider Context
 */

import { createContext, useContext } from 'react';
import type { PdfProviderConfig, ViewerTheme } from '@pdf-embed-seo/core';
import type { PdfApiClient } from '@pdf-embed-seo/core';

/**
 * Context value interface
 */
export interface PdfContextValue {
  /** Provider configuration */
  config: PdfProviderConfig;
  /** API client instance */
  apiClient: PdfApiClient | null;
  /** Current theme */
  theme: ViewerTheme;
  /** Set theme */
  setTheme: (theme: ViewerTheme) => void;
  /** Whether premium features are enabled */
  isPremium: boolean;
  /** Site URL for SEO */
  siteUrl: string;
  /** Site name for SEO */
  siteName: string;
}

/**
 * Default context value
 */
const defaultContextValue: PdfContextValue = {
  config: {
    mode: 'standalone',
    theme: 'light',
    siteUrl: '',
    siteName: 'PDF Documents',
  },
  apiClient: null,
  theme: 'light',
  setTheme: () => {},
  isPremium: false,
  siteUrl: '',
  siteName: 'PDF Documents',
};

/**
 * PDF Context
 */
export const PdfContext = createContext<PdfContextValue>(defaultContextValue);

/**
 * Hook to access PDF context
 */
export function usePdfContext(): PdfContextValue {
  const context = useContext(PdfContext);
  if (!context) {
    throw new Error('usePdfContext must be used within a PdfProvider');
  }
  return context;
}

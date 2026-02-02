/**
 * usePdfTheme Hook
 * Manage PDF viewer theme
 */

import { useCallback, useEffect, useState } from 'react';
import type { ViewerTheme } from '@pdf-embed-seo/core';
import { usePdfContext } from '../components/PdfProvider/PdfContext';

/**
 * usePdfTheme return value
 */
export interface UsePdfThemeResult {
  /** Current theme setting */
  theme: ViewerTheme;
  /** Resolved theme (light or dark, never system) */
  resolvedTheme: 'light' | 'dark';
  /** Set theme */
  setTheme: (theme: ViewerTheme) => void;
  /** Toggle between light and dark */
  toggleTheme: () => void;
  /** Whether system prefers dark mode */
  systemPrefersDark: boolean;
}

/**
 * usePdfTheme Hook
 *
 * @example
 * ```tsx
 * function ThemeToggle() {
 *   const { theme, resolvedTheme, toggleTheme } = usePdfTheme();
 *
 *   return (
 *     <button onClick={toggleTheme}>
 *       Current: {resolvedTheme}
 *     </button>
 *   );
 * }
 * ```
 */
export function usePdfTheme(): UsePdfThemeResult {
  const { theme, setTheme } = usePdfContext();

  // Track system preference
  const [systemPrefersDark, setSystemPrefersDark] = useState(false);

  // Listen for system preference changes
  useEffect(() => {
    if (typeof window === 'undefined') return;

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    setSystemPrefersDark(mediaQuery.matches);

    const handleChange = (e: MediaQueryListEvent) => {
      setSystemPrefersDark(e.matches);
    };

    mediaQuery.addEventListener('change', handleChange);
    return () => mediaQuery.removeEventListener('change', handleChange);
  }, []);

  // Resolve theme to actual value
  const resolvedTheme: 'light' | 'dark' =
    theme === 'system' ? (systemPrefersDark ? 'dark' : 'light') : theme;

  // Toggle between light and dark
  const toggleTheme = useCallback(() => {
    if (theme === 'system') {
      // When in system mode, toggle to opposite of current system preference
      setTheme(systemPrefersDark ? 'light' : 'dark');
    } else {
      setTheme(theme === 'light' ? 'dark' : 'light');
    }
  }, [theme, systemPrefersDark, setTheme]);

  return {
    theme,
    resolvedTheme,
    setTheme,
    toggleTheme,
    systemPrefersDark,
  };
}

export default usePdfTheme;

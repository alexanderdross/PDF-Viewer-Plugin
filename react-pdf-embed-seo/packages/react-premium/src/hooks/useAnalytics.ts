/**
 * useAnalytics Hook
 * Track and fetch PDF analytics
 */

import { useState, useCallback, useEffect } from 'react';
import type { DocumentAnalytics } from '@pdf-embed-seo/core';
import { usePdfContext } from '@pdf-embed-seo/react';
import type { AnalyticsSummary } from '../components/PdfAnalytics/PdfAnalyticsDashboard';

/**
 * Analytics period
 */
export type AnalyticsPeriod = '7days' | '30days' | '90days' | '12months' | 'all';

/**
 * useAnalytics options
 */
export interface UseAnalyticsOptions {
  /** Initial period */
  initialPeriod?: AnalyticsPeriod;
  /** Auto-refresh interval in ms (0 to disable) */
  refreshInterval?: number;
}

/**
 * useAnalytics return value
 */
export interface UseAnalyticsResult {
  /** Analytics summary */
  analytics: AnalyticsSummary | null;
  /** Current period */
  period: AnalyticsPeriod;
  /** Set period */
  setPeriod: (period: AnalyticsPeriod) => void;
  /** Refresh data */
  refresh: () => Promise<void>;
  /** Loading state */
  isLoading: boolean;
  /** Error */
  error: Error | null;
}

/**
 * useAnalytics Hook
 *
 * @example
 * ```tsx
 * function AnalyticsPage() {
 *   const {
 *     analytics,
 *     period,
 *     setPeriod,
 *     isLoading,
 *   } = useAnalytics({ initialPeriod: '30days' });
 *
 *   return (
 *     <PdfAnalyticsDashboard
 *       data={analytics}
 *       period={period}
 *       onPeriodChange={setPeriod}
 *     />
 *   );
 * }
 * ```
 */
export function useAnalytics(options: UseAnalyticsOptions = {}): UseAnalyticsResult {
  const { initialPeriod = '30days', refreshInterval = 0 } = options;
  const { apiClient, isPremium } = usePdfContext();

  const [analytics, setAnalytics] = useState<AnalyticsSummary | null>(null);
  const [period, setPeriod] = useState<AnalyticsPeriod>(initialPeriod);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  // Fetch analytics
  const fetchAnalytics = useCallback(async () => {
    if (!apiClient || !isPremium) {
      setIsLoading(false);
      return;
    }

    setIsLoading(true);
    setError(null);

    try {
      const response = await apiClient.getAnalytics(period);
      setAnalytics({
        totalViews: response.totalViews,
        uniqueVisitors: response.uniqueVisitors,
        totalDocuments: response.totalDocuments,
        totalDownloads: response.totalDownloads,
        topDocuments: response.topDocuments,
        viewsByDay: response.viewsByDay,
      });
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Failed to fetch analytics'));
    } finally {
      setIsLoading(false);
    }
  }, [apiClient, isPremium, period]);

  // Fetch on mount and period change
  useEffect(() => {
    fetchAnalytics();
  }, [fetchAnalytics]);

  // Auto-refresh
  useEffect(() => {
    if (refreshInterval <= 0) return;

    const interval = setInterval(fetchAnalytics, refreshInterval);
    return () => clearInterval(interval);
  }, [fetchAnalytics, refreshInterval]);

  return {
    analytics,
    period,
    setPeriod,
    refresh: fetchAnalytics,
    isLoading,
    error,
  };
}

export default useAnalytics;

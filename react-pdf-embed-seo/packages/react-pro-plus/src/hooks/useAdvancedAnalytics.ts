import { useState, useCallback } from 'react';
import type { AdvancedAnalyticsData } from '../types';

type AnalyticsPeriod = 'today' | 'yesterday' | '7days' | '30days' | '90days' | 'year' | 'all';

interface UseAdvancedAnalyticsOptions {
  documentId?: string | number;
  period?: AnalyticsPeriod;
}

interface UseAdvancedAnalyticsReturn {
  analytics: AdvancedAnalyticsData | null;
  loading: boolean;
  error: Error | null;
  loadAnalytics: (period?: AnalyticsPeriod) => Promise<void>;
  exportAnalytics: (format: 'csv' | 'json') => Promise<Blob>;
  trackEvent: (eventType: string, data?: Record<string, unknown>) => Promise<void>;
}

export function useAdvancedAnalytics(
  options: UseAdvancedAnalyticsOptions = {}
): UseAdvancedAnalyticsReturn {
  const { documentId, period: initialPeriod = '30days' } = options;
  const [analytics, setAnalytics] = useState<AdvancedAnalyticsData | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const loadAnalytics = useCallback(
    async (period: AnalyticsPeriod = initialPeriod) => {
      setLoading(true);
      setError(null);

      try {
        const params = new URLSearchParams({ period });
        if (documentId) {
          params.append('document_id', String(documentId));
        }

        const response = await fetch(
          `/api/pdf-embed-seo/v1/analytics/advanced?${params}`
        );

        if (!response.ok) {
          throw new Error('Failed to load analytics');
        }

        const data = await response.json();
        setAnalytics(data);
      } catch (err) {
        setError(err instanceof Error ? err : new Error('Unknown error'));
      } finally {
        setLoading(false);
      }
    },
    [documentId, initialPeriod]
  );

  const exportAnalytics = useCallback(
    async (format: 'csv' | 'json'): Promise<Blob> => {
      const params = new URLSearchParams({ format });
      if (documentId) {
        params.append('document_id', String(documentId));
      }

      const response = await fetch(
        `/api/pdf-embed-seo/v1/analytics/export?${params}`
      );

      if (!response.ok) {
        throw new Error('Failed to export analytics');
      }

      return response.blob();
    },
    [documentId]
  );

  const trackEvent = useCallback(
    async (eventType: string, data?: Record<string, unknown>) => {
      try {
        await fetch('/api/pdf-embed-seo/v1/analytics/track', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            event: eventType,
            documentId,
            data,
            timestamp: new Date().toISOString(),
          }),
        });
      } catch (err) {
        console.error('Failed to track event:', err);
      }
    },
    [documentId]
  );

  return {
    analytics,
    loading,
    error,
    loadAnalytics,
    exportAnalytics,
    trackEvent,
  };
}

/**
 * useHeatmap Hook
 * Fetches and manages heatmap data for PDF pages
 */

import { useState, useEffect, useCallback } from 'react';
import type { HeatmapData, HeatmapPoint } from '../types';

interface UseHeatmapOptions {
  period?: '7d' | '30d' | '90d';
  enabled?: boolean;
}

interface UseHeatmapReturn {
  heatmapData: HeatmapData | null;
  loading: boolean;
  error: Error | null;
  trackInteraction: (point: Omit<HeatmapPoint, 'intensity'>) => Promise<void>;
  refresh: () => Promise<void>;
}

export function useHeatmap(
  documentId: string | number,
  page: number,
  options: UseHeatmapOptions = {}
): UseHeatmapReturn {
  const { period = '30d', enabled = true } = options;

  const [heatmapData, setHeatmapData] = useState<HeatmapData | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const fetchHeatmap = useCallback(async () => {
    if (!enabled || !documentId) return;

    setLoading(true);
    setError(null);

    try {
      // This would be replaced with actual API call
      const response = await fetch(
        `/wp-json/pdf-embed-seo/v1/pro-plus/heatmaps/${documentId}/${page}?period=${period}`
      );

      if (!response.ok) {
        throw new Error('Failed to fetch heatmap data');
      }

      const data = await response.json();
      setHeatmapData({
        documentId,
        page,
        points: data.points || [],
      });
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
    } finally {
      setLoading(false);
    }
  }, [documentId, page, period, enabled]);

  const trackInteraction = useCallback(async (point: Omit<HeatmapPoint, 'intensity'>) => {
    if (!documentId) return;

    try {
      await fetch(`/wp-json/pdf-embed-seo/v1/pro-plus/heatmaps/${documentId}/${page}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          x: point.x,
          y: point.y,
          timestamp: point.timestamp || new Date().toISOString(),
        }),
      });
    } catch (err) {
      console.error('Failed to track heatmap interaction:', err);
    }
  }, [documentId, page]);

  const refresh = useCallback(async () => {
    await fetchHeatmap();
  }, [fetchHeatmap]);

  useEffect(() => {
    fetchHeatmap();
  }, [fetchHeatmap]);

  return {
    heatmapData,
    loading,
    error,
    trackInteraction,
    refresh,
  };
}

export default useHeatmap;

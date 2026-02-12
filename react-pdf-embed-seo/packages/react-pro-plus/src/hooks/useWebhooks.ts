/**
 * useWebhooks Hook
 * Manages webhook configurations
 */

import { useState, useEffect, useCallback } from 'react';
import type { WebhookConfig, WebhookDelivery } from '../types';

interface UseWebhooksOptions {
  apiBaseUrl?: string;
}

interface UseWebhooksReturn {
  webhooks: WebhookConfig[];
  loading: boolean;
  error: Error | null;
  createWebhook: (webhook: Omit<WebhookConfig, 'id'>) => Promise<WebhookConfig | null>;
  updateWebhook: (id: number, webhook: Partial<WebhookConfig>) => Promise<WebhookConfig | null>;
  deleteWebhook: (id: number) => Promise<boolean>;
  testWebhook: (id: number) => Promise<{ success: boolean; message: string }>;
  getDeliveries: (id: number) => Promise<WebhookDelivery[]>;
  refresh: () => Promise<void>;
}

export function useWebhooks(options: UseWebhooksOptions = {}): UseWebhooksReturn {
  const { apiBaseUrl = '/wp-json/pdf-embed-seo/v1' } = options;

  const [webhooks, setWebhooks] = useState<WebhookConfig[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const fetchWebhooks = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/webhooks`);

      if (!response.ok) {
        throw new Error('Failed to fetch webhooks');
      }

      const data = await response.json();
      setWebhooks(data.webhooks || []);
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      setWebhooks([]);
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const createWebhook = useCallback(async (
    webhook: Omit<WebhookConfig, 'id'>
  ): Promise<WebhookConfig | null> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/webhooks`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(webhook),
      });

      if (!response.ok) {
        throw new Error('Failed to create webhook');
      }

      const newWebhook = await response.json();
      setWebhooks(prev => [...prev, newWebhook]);
      return newWebhook;
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return null;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const updateWebhook = useCallback(async (
    id: number,
    webhook: Partial<WebhookConfig>
  ): Promise<WebhookConfig | null> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/webhooks/${id}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(webhook),
      });

      if (!response.ok) {
        throw new Error('Failed to update webhook');
      }

      const updatedWebhook = await response.json();
      setWebhooks(prev => prev.map(w => w.id === id ? updatedWebhook : w));
      return updatedWebhook;
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return null;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const deleteWebhook = useCallback(async (id: number): Promise<boolean> => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/webhooks/${id}`, {
        method: 'DELETE',
      });

      if (!response.ok) {
        throw new Error('Failed to delete webhook');
      }

      setWebhooks(prev => prev.filter(w => w.id !== id));
      return true;
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      return false;
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl]);

  const testWebhook = useCallback(async (
    id: number
  ): Promise<{ success: boolean; message: string }> => {
    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/webhooks/${id}/test`, {
        method: 'POST',
      });

      if (!response.ok) {
        return { success: false, message: 'Test request failed' };
      }

      const result = await response.json();
      return {
        success: result.success === true,
        message: result.message || (result.success ? 'Test successful' : 'Test failed'),
      };
    } catch (err) {
      return {
        success: false,
        message: err instanceof Error ? err.message : 'Unknown error',
      };
    }
  }, [apiBaseUrl]);

  const getDeliveries = useCallback(async (id: number): Promise<WebhookDelivery[]> => {
    try {
      const response = await fetch(`${apiBaseUrl}/pro-plus/webhooks/${id}/deliveries`);

      if (!response.ok) {
        throw new Error('Failed to fetch deliveries');
      }

      const data = await response.json();
      return data.deliveries || [];
    } catch (err) {
      console.error('Failed to fetch deliveries:', err);
      return [];
    }
  }, [apiBaseUrl]);

  const refresh = useCallback(async () => {
    await fetchWebhooks();
  }, [fetchWebhooks]);

  useEffect(() => {
    fetchWebhooks();
  }, [fetchWebhooks]);

  return {
    webhooks,
    loading,
    error,
    createWebhook,
    updateWebhook,
    deleteWebhook,
    testWebhook,
    getDeliveries,
    refresh,
  };
}

export default useWebhooks;

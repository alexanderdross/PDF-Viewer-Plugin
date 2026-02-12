/**
 * useAuditLog Hook
 * Fetches and manages audit log entries
 */

import { useState, useEffect, useCallback } from 'react';
import type { AuditLogEntry, AuditAction } from '../types';

interface UseAuditLogOptions {
  documentId?: string | number;
  userId?: number;
  actions?: AuditAction[];
  dateFrom?: string;
  dateTo?: string;
  page?: number;
  perPage?: number;
  apiBaseUrl?: string;
}

interface UseAuditLogReturn {
  entries: AuditLogEntry[];
  loading: boolean;
  error: Error | null;
  totalPages: number;
  totalEntries: number;
  refresh: () => Promise<void>;
  exportLog: (format: 'csv' | 'json') => Promise<string | null>;
}

export function useAuditLog(options: UseAuditLogOptions = {}): UseAuditLogReturn {
  const {
    documentId,
    userId,
    actions,
    dateFrom,
    dateTo,
    page = 1,
    perPage = 20,
    apiBaseUrl = '/wp-json/pdf-embed-seo/v1',
  } = options;

  const [entries, setEntries] = useState<AuditLogEntry[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);
  const [totalPages, setTotalPages] = useState(0);
  const [totalEntries, setTotalEntries] = useState(0);

  const fetchEntries = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      const params = new URLSearchParams();
      params.set('page', String(page));
      params.set('per_page', String(perPage));

      if (documentId) params.set('document_id', String(documentId));
      if (userId) params.set('user_id', String(userId));
      if (actions?.length) params.set('actions', actions.join(','));
      if (dateFrom) params.set('date_from', dateFrom);
      if (dateTo) params.set('date_to', dateTo);

      const response = await fetch(`${apiBaseUrl}/pro-plus/audit-log?${params}`);

      if (!response.ok) {
        throw new Error('Failed to fetch audit log');
      }

      const data = await response.json();

      setEntries(data.entries || []);
      setTotalPages(data.total_pages || 1);
      setTotalEntries(data.total || 0);
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      setEntries([]);
    } finally {
      setLoading(false);
    }
  }, [apiBaseUrl, documentId, userId, actions, dateFrom, dateTo, page, perPage]);

  const refresh = useCallback(async () => {
    await fetchEntries();
  }, [fetchEntries]);

  const exportLog = useCallback(async (format: 'csv' | 'json'): Promise<string | null> => {
    try {
      const params = new URLSearchParams();
      params.set('format', format);

      if (documentId) params.set('document_id', String(documentId));
      if (userId) params.set('user_id', String(userId));
      if (actions?.length) params.set('actions', actions.join(','));
      if (dateFrom) params.set('date_from', dateFrom);
      if (dateTo) params.set('date_to', dateTo);

      const response = await fetch(`${apiBaseUrl}/pro-plus/audit-log/export?${params}`);

      if (!response.ok) {
        throw new Error('Failed to export audit log');
      }

      return await response.text();
    } catch (err) {
      console.error('Export failed:', err);
      return null;
    }
  }, [apiBaseUrl, documentId, userId, actions, dateFrom, dateTo]);

  useEffect(() => {
    fetchEntries();
  }, [fetchEntries]);

  return {
    entries,
    loading,
    error,
    totalPages,
    totalEntries,
    refresh,
    exportLog,
  };
}

export default useAuditLog;

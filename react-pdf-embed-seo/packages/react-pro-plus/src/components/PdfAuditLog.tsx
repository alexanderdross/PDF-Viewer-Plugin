/**
 * PdfAuditLog Component
 * Displays audit log entries for compliance tracking
 */

import React, { useState, useMemo } from 'react';
import { useAuditLog } from '../hooks/useAuditLog';
import type { AuditLogProps, AuditLogEntry, AuditAction } from '../types';

const ACTION_LABELS: Record<AuditAction, string> = {
  document_viewed: 'Document Viewed',
  document_downloaded: 'Document Downloaded',
  document_printed: 'Document Printed',
  password_attempt_success: 'Password Success',
  password_attempt_failed: 'Password Failed',
  settings_changed: 'Settings Changed',
  document_created: 'Document Created',
  document_updated: 'Document Updated',
  document_deleted: 'Document Deleted',
  annotation_added: 'Annotation Added',
  annotation_deleted: 'Annotation Deleted',
  version_created: 'Version Created',
  version_restored: 'Version Restored',
};

const ACTION_COLORS: Record<string, string> = {
  document_viewed: '#2196f3',
  document_downloaded: '#4caf50',
  document_printed: '#9c27b0',
  password_attempt_success: '#4caf50',
  password_attempt_failed: '#f44336',
  settings_changed: '#ff9800',
  document_created: '#4caf50',
  document_updated: '#2196f3',
  document_deleted: '#f44336',
  annotation_added: '#00bcd4',
  annotation_deleted: '#e91e63',
  version_created: '#673ab7',
  version_restored: '#009688',
};

export const PdfAuditLog: React.FC<AuditLogProps> = ({
  documentId,
  userId,
  actions,
  dateFrom,
  dateTo,
  perPage = 20,
}) => {
  const [page, setPage] = useState(1);
  const [filterAction, setFilterAction] = useState<AuditAction | ''>('');

  const { entries, loading, error, totalPages, exportLog } = useAuditLog({
    documentId,
    userId,
    actions: filterAction ? [filterAction] : actions,
    dateFrom,
    dateTo,
    page,
    perPage,
  });

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    }).format(date);
  };

  const handleExport = async (format: 'csv' | 'json') => {
    const data = await exportLog(format);
    if (data) {
      const blob = new Blob([data], {
        type: format === 'csv' ? 'text/csv' : 'application/json',
      });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `audit-log.${format}`;
      a.click();
      URL.revokeObjectURL(url);
    }
  };

  if (loading && entries.length === 0) {
    return (
      <div className="pdf-audit-log pdf-audit-log-loading">
        <div className="pdf-audit-log-spinner" />
        <span>Loading audit log...</span>
      </div>
    );
  }

  if (error) {
    return (
      <div className="pdf-audit-log pdf-audit-log-error">
        <span>Failed to load audit log: {error.message}</span>
      </div>
    );
  }

  return (
    <div className="pdf-audit-log">
      <div className="pdf-audit-log-header">
        <h3>Audit Log</h3>
        <div className="pdf-audit-log-controls">
          <select
            value={filterAction}
            onChange={(e) => setFilterAction(e.target.value as AuditAction | '')}
            className="pdf-audit-log-filter"
          >
            <option value="">All Actions</option>
            {Object.entries(ACTION_LABELS).map(([action, label]) => (
              <option key={action} value={action}>{label}</option>
            ))}
          </select>
          <div className="pdf-audit-log-export">
            <button onClick={() => handleExport('csv')}>Export CSV</button>
            <button onClick={() => handleExport('json')}>Export JSON</button>
          </div>
        </div>
      </div>

      <div className="pdf-audit-log-table-container">
        <table className="pdf-audit-log-table">
          <thead>
            <tr>
              <th>Timestamp</th>
              <th>Action</th>
              <th>User</th>
              <th>IP Address</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            {entries.map((entry) => (
              <tr key={entry.id}>
                <td className="pdf-audit-log-timestamp">
                  {formatDate(entry.timestamp)}
                </td>
                <td>
                  <span
                    className="pdf-audit-log-action"
                    style={{
                      backgroundColor: ACTION_COLORS[entry.action] || '#666',
                    }}
                  >
                    {ACTION_LABELS[entry.action] || entry.action}
                  </span>
                </td>
                <td className="pdf-audit-log-user">
                  {entry.userEmail || `User #${entry.userId}` || 'Anonymous'}
                </td>
                <td className="pdf-audit-log-ip">
                  {entry.ipAddress || '-'}
                </td>
                <td className="pdf-audit-log-details">
                  {entry.details ? (
                    <details>
                      <summary>View details</summary>
                      <pre>{JSON.stringify(entry.details, null, 2)}</pre>
                    </details>
                  ) : '-'}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {entries.length === 0 && (
        <div className="pdf-audit-log-empty">
          No audit log entries found
        </div>
      )}

      {totalPages > 1 && (
        <div className="pdf-audit-log-pagination">
          <button
            onClick={() => setPage(p => Math.max(1, p - 1))}
            disabled={page === 1 || loading}
          >
            Previous
          </button>
          <span>Page {page} of {totalPages}</span>
          <button
            onClick={() => setPage(p => Math.min(totalPages, p + 1))}
            disabled={page === totalPages || loading}
          >
            Next
          </button>
        </div>
      )}

      <style>{`
        .pdf-audit-log {
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
          background: #fff;
          border-radius: 8px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pdf-audit-log-loading,
        .pdf-audit-log-error {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
          padding: 40px;
          color: #666;
        }
        .pdf-audit-log-spinner {
          width: 24px;
          height: 24px;
          border: 3px solid #ddd;
          border-top-color: #2196f3;
          border-radius: 50%;
          animation: spin 1s linear infinite;
        }
        @keyframes spin {
          to { transform: rotate(360deg); }
        }
        .pdf-audit-log-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 16px;
          border-bottom: 1px solid #eee;
        }
        .pdf-audit-log-header h3 {
          margin: 0;
          font-size: 16px;
          font-weight: 600;
        }
        .pdf-audit-log-controls {
          display: flex;
          gap: 12px;
          align-items: center;
        }
        .pdf-audit-log-filter {
          padding: 6px 10px;
          border: 1px solid #ddd;
          border-radius: 4px;
          font-size: 13px;
        }
        .pdf-audit-log-export {
          display: flex;
          gap: 4px;
        }
        .pdf-audit-log-export button {
          padding: 6px 10px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background: #fff;
          font-size: 12px;
          cursor: pointer;
        }
        .pdf-audit-log-export button:hover {
          background: #f5f5f5;
        }
        .pdf-audit-log-table-container {
          overflow-x: auto;
        }
        .pdf-audit-log-table {
          width: 100%;
          border-collapse: collapse;
          font-size: 13px;
        }
        .pdf-audit-log-table th,
        .pdf-audit-log-table td {
          padding: 12px;
          text-align: left;
          border-bottom: 1px solid #eee;
        }
        .pdf-audit-log-table th {
          background: #f9f9f9;
          font-weight: 600;
          color: #666;
        }
        .pdf-audit-log-timestamp {
          white-space: nowrap;
          color: #666;
        }
        .pdf-audit-log-action {
          display: inline-block;
          padding: 2px 8px;
          border-radius: 4px;
          color: #fff;
          font-size: 11px;
          font-weight: 500;
        }
        .pdf-audit-log-user {
          color: #333;
        }
        .pdf-audit-log-ip {
          font-family: monospace;
          color: #888;
        }
        .pdf-audit-log-details details {
          cursor: pointer;
        }
        .pdf-audit-log-details summary {
          color: #2196f3;
          font-size: 12px;
        }
        .pdf-audit-log-details pre {
          margin: 8px 0 0;
          padding: 8px;
          background: #f5f5f5;
          border-radius: 4px;
          font-size: 11px;
          overflow-x: auto;
        }
        .pdf-audit-log-empty {
          padding: 40px;
          text-align: center;
          color: #888;
        }
        .pdf-audit-log-pagination {
          display: flex;
          justify-content: center;
          align-items: center;
          gap: 16px;
          padding: 16px;
          border-top: 1px solid #eee;
        }
        .pdf-audit-log-pagination button {
          padding: 6px 12px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background: #fff;
          cursor: pointer;
        }
        .pdf-audit-log-pagination button:hover:not(:disabled) {
          background: #f5f5f5;
        }
        .pdf-audit-log-pagination button:disabled {
          opacity: 0.5;
          cursor: not-allowed;
        }
        .pdf-audit-log-pagination span {
          font-size: 13px;
          color: #666;
        }
      `}</style>
    </div>
  );
};

export default PdfAuditLog;

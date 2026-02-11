/**
 * PdfVersionHistory Component
 * Displays document version history with restore capabilities
 */

import React, { useMemo, useState } from 'react';
import { useVersions } from '../hooks/useVersions';
import type { DocumentVersion, VersionHistoryProps } from '../types';

function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function formatDate(dateString: string): string {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
}

function getRelativeTime(dateString: string): string {
  const date = new Date(dateString);
  const now = new Date();
  const diff = now.getTime() - date.getTime();

  const seconds = Math.floor(diff / 1000);
  const minutes = Math.floor(seconds / 60);
  const hours = Math.floor(minutes / 60);
  const days = Math.floor(hours / 24);

  if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;
  if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
  if (minutes > 0) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
  return 'Just now';
}

export const PdfVersionHistory: React.FC<VersionHistoryProps> = ({
  documentId,
  onVersionSelect,
  onVersionRestore,
  showChangelog = true,
  showRestore = true,
}) => {
  const { versions, loading, error, restoreVersion } = useVersions({ documentId });
  const [isRestoring, setIsRestoring] = useState(false);

  const sortedVersions = useMemo(() => {
    return [...versions].sort((a, b) =>
      new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime()
    );
  }, [versions]);

  const handleRestore = async (version: DocumentVersion) => {
    if (version.isCurrent || isRestoring) return;

    setIsRestoring(true);
    try {
      await restoreVersion(version.id);
      onVersionRestore?.(version);
    } finally {
      setIsRestoring(false);
    }
  };

  const handleSelect = (version: DocumentVersion) => {
    onVersionSelect?.(version);
  };

  if (loading) {
    return (
      <div className="pdf-version-history pdf-version-history-loading">
        <div className="pdf-version-history-spinner" />
        <span>Loading versions...</span>
      </div>
    );
  }

  if (error) {
    return (
      <div className="pdf-version-history pdf-version-history-error">
        <span>Failed to load versions: {error.message}</span>
      </div>
    );
  }

  if (versions.length === 0) {
    return (
      <div className="pdf-version-history pdf-version-history-empty">
        <span>No version history available</span>
      </div>
    );
  }

  return (
    <div className="pdf-version-history">
      <h3 className="pdf-version-history-title">Version History</h3>

      <div className="pdf-version-history-list">
        {sortedVersions.map((version, index) => (
          <div
            key={version.id}
            className={`pdf-version-item ${version.isCurrent ? 'current' : ''}`}
            onClick={() => handleSelect(version)}
            role="button"
            tabIndex={0}
            onKeyPress={(e) => e.key === 'Enter' && handleSelect(version)}
          >
            <div className="pdf-version-item-header">
              <div className="pdf-version-item-info">
                <span className="pdf-version-number">
                  v{version.versionNumber}
                  {version.isCurrent && (
                    <span className="pdf-version-current-badge">Current</span>
                  )}
                </span>
                <span className="pdf-version-date" title={formatDate(version.createdAt)}>
                  {getRelativeTime(version.createdAt)}
                </span>
              </div>

              <div className="pdf-version-item-meta">
                <span className="pdf-version-size">{formatFileSize(version.fileSize)}</span>
                {version.authorName && (
                  <span className="pdf-version-author">by {version.authorName}</span>
                )}
              </div>
            </div>

            {showChangelog && version.changelog && (
              <div className="pdf-version-changelog">
                <p>{version.changelog}</p>
              </div>
            )}

            <div className="pdf-version-item-actions">
              <button
                className="pdf-version-action pdf-version-preview"
                onClick={(e) => {
                  e.stopPropagation();
                  window.open(version.fileUrl, '_blank');
                }}
                title="Preview version"
              >
                👁️ Preview
              </button>

              {showRestore && !version.isCurrent && (
                <button
                  className="pdf-version-action pdf-version-restore"
                  onClick={(e) => {
                    e.stopPropagation();
                    handleRestore(version);
                  }}
                  disabled={isRestoring}
                  title="Restore this version"
                >
                  {isRestoring ? '...' : '↩️ Restore'}
                </button>
              )}
            </div>

            {index < sortedVersions.length - 1 && (
              <div className="pdf-version-timeline-connector" />
            )}
          </div>
        ))}
      </div>

      <style>{`
        .pdf-version-history {
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
          max-width: 400px;
        }
        .pdf-version-history-loading,
        .pdf-version-history-error,
        .pdf-version-history-empty {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
          padding: 20px;
          color: #666;
        }
        .pdf-version-history-error {
          color: #e74c3c;
        }
        .pdf-version-history-spinner {
          width: 20px;
          height: 20px;
          border: 2px solid #ddd;
          border-top-color: #333;
          border-radius: 50%;
          animation: spin 1s linear infinite;
        }
        @keyframes spin {
          to { transform: rotate(360deg); }
        }
        .pdf-version-history-title {
          margin: 0 0 16px;
          font-size: 16px;
          font-weight: 600;
          color: #333;
        }
        .pdf-version-history-list {
          display: flex;
          flex-direction: column;
          gap: 12px;
        }
        .pdf-version-item {
          position: relative;
          padding: 12px;
          background: #f9f9f9;
          border: 1px solid #e0e0e0;
          border-radius: 8px;
          cursor: pointer;
          transition: all 0.2s ease;
        }
        .pdf-version-item:hover {
          background: #fff;
          border-color: #bbb;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pdf-version-item.current {
          background: #e3f2fd;
          border-color: #2196f3;
        }
        .pdf-version-item-header {
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
          margin-bottom: 8px;
        }
        .pdf-version-item-info {
          display: flex;
          flex-direction: column;
          gap: 2px;
        }
        .pdf-version-number {
          font-weight: 600;
          font-size: 14px;
          color: #333;
          display: flex;
          align-items: center;
          gap: 8px;
        }
        .pdf-version-current-badge {
          font-size: 10px;
          font-weight: 500;
          background: #2196f3;
          color: #fff;
          padding: 2px 6px;
          border-radius: 4px;
        }
        .pdf-version-date {
          font-size: 12px;
          color: #888;
        }
        .pdf-version-item-meta {
          text-align: right;
          font-size: 12px;
          color: #666;
        }
        .pdf-version-size {
          display: block;
        }
        .pdf-version-author {
          display: block;
          font-style: italic;
        }
        .pdf-version-changelog {
          margin: 8px 0;
          padding: 8px;
          background: #fff;
          border-radius: 4px;
          font-size: 13px;
          color: #555;
        }
        .pdf-version-changelog p {
          margin: 0;
        }
        .pdf-version-item-actions {
          display: flex;
          gap: 8px;
          margin-top: 8px;
        }
        .pdf-version-action {
          padding: 4px 8px;
          font-size: 12px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background: #fff;
          cursor: pointer;
          transition: all 0.2s ease;
        }
        .pdf-version-action:hover:not(:disabled) {
          background: #f0f0f0;
        }
        .pdf-version-action:disabled {
          opacity: 0.5;
          cursor: not-allowed;
        }
        .pdf-version-restore {
          border-color: #4caf50;
          color: #4caf50;
        }
        .pdf-version-restore:hover:not(:disabled) {
          background: #e8f5e9;
        }
        .pdf-version-timeline-connector {
          position: absolute;
          left: 24px;
          bottom: -12px;
          width: 2px;
          height: 12px;
          background: #ddd;
        }
      `}</style>
    </div>
  );
};

export default PdfVersionHistory;

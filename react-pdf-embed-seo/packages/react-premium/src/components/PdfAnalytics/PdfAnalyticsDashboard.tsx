/**
 * PdfAnalyticsDashboard Component
 * Analytics dashboard for PDF documents
 */

'use client';

import React, { useState, useEffect } from 'react';
import type { DocumentAnalytics } from '@pdf-embed-seo/core';
import { CSS_CLASSES } from '@pdf-embed-seo/core';
import { usePdfContext } from '@pdf-embed-seo/react';

/**
 * PdfAnalyticsDashboard props
 */
export interface PdfAnalyticsDashboardProps {
  /** Time period filter */
  period?: '7days' | '30days' | '90days' | '12months' | 'all';
  /** Show export button */
  showExport?: boolean;
  /** Custom data (override API fetch) */
  data?: AnalyticsSummary;
  /** Additional CSS class */
  className?: string;
}

/**
 * Analytics summary data
 */
export interface AnalyticsSummary {
  totalViews: number;
  uniqueVisitors: number;
  totalDocuments: number;
  totalDownloads: number;
  topDocuments: DocumentAnalytics[];
  viewsByDay: { date: string; views: number }[];
}

/**
 * Format number with abbreviation
 */
function formatNumber(num: number): string {
  if (num >= 1000000) return `${(num / 1000000).toFixed(1)}M`;
  if (num >= 1000) return `${(num / 1000).toFixed(1)}K`;
  return num.toString();
}

/**
 * PdfAnalyticsDashboard Component
 *
 * @example
 * ```tsx
 * <PdfAnalyticsDashboard
 *   period="30days"
 *   showExport
 * />
 * ```
 */
export function PdfAnalyticsDashboard({
  period = '30days',
  showExport = true,
  data: propData,
  className = '',
}: PdfAnalyticsDashboardProps): React.ReactElement {
  const { apiClient } = usePdfContext();

  const [selectedPeriod, setSelectedPeriod] = useState(period);
  const [data, setData] = useState<AnalyticsSummary | null>(propData || null);
  const [isLoading, setIsLoading] = useState(!propData);
  const [error, setError] = useState<Error | null>(null);

  // Fetch analytics data
  useEffect(() => {
    if (propData) {
      setData(propData);
      return;
    }

    if (!apiClient) return;

    async function fetchAnalytics() {
      setIsLoading(true);
      setError(null);

      try {
        const response = await apiClient.getAnalytics(selectedPeriod);
        setData({
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
    }

    fetchAnalytics();
  }, [apiClient, selectedPeriod, propData]);

  const handleExport = async (format: 'csv' | 'json') => {
    // Export functionality would be implemented here
    console.log(`Exporting as ${format}`);
  };

  if (isLoading) {
    return (
      <div className={`${CSS_CLASSES.premium.analytics} ${className}`}>
        <div className="pdf-analytics-loading">
          <div className="pdf-spinner" />
          Loading analytics...
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className={`${CSS_CLASSES.premium.analytics} ${className}`}>
        <div className="pdf-analytics-error">
          Error: {error.message}
        </div>
      </div>
    );
  }

  if (!data) return null;

  return (
    <div className={`${CSS_CLASSES.premium.analytics} ${className}`}>
      {/* Header */}
      <div className="pdf-analytics-header">
        <h2>PDF Analytics</h2>

        <div className="pdf-analytics-controls">
          <select
            value={selectedPeriod}
            onChange={(e) => setSelectedPeriod(e.target.value as typeof selectedPeriod)}
            className="pdf-period-select"
          >
            <option value="7days">Last 7 days</option>
            <option value="30days">Last 30 days</option>
            <option value="90days">Last 90 days</option>
            <option value="12months">Last 12 months</option>
            <option value="all">All time</option>
          </select>

          {showExport && (
            <div className="pdf-export-buttons">
              <button onClick={() => handleExport('csv')} className="pdf-button-small">
                Export CSV
              </button>
              <button onClick={() => handleExport('json')} className="pdf-button-small">
                Export JSON
              </button>
            </div>
          )}
        </div>
      </div>

      {/* Stats cards */}
      <div className="pdf-analytics-stats">
        <div className="pdf-stat-card">
          <span className="pdf-stat-value">{formatNumber(data.totalViews)}</span>
          <span className="pdf-stat-label">Total Views</span>
        </div>
        <div className="pdf-stat-card">
          <span className="pdf-stat-value">{formatNumber(data.uniqueVisitors)}</span>
          <span className="pdf-stat-label">Unique Visitors</span>
        </div>
        <div className="pdf-stat-card">
          <span className="pdf-stat-value">{formatNumber(data.totalDownloads)}</span>
          <span className="pdf-stat-label">Downloads</span>
        </div>
        <div className="pdf-stat-card">
          <span className="pdf-stat-value">{data.totalDocuments}</span>
          <span className="pdf-stat-label">Documents</span>
        </div>
      </div>

      {/* Top documents */}
      <div className="pdf-analytics-section">
        <h3>Top Documents</h3>
        <table className="pdf-analytics-table">
          <thead>
            <tr>
              <th>Document</th>
              <th>Views</th>
              <th>Unique</th>
              <th>Downloads</th>
            </tr>
          </thead>
          <tbody>
            {data.topDocuments.map((doc) => (
              <tr key={doc.documentId}>
                <td>{doc.title}</td>
                <td>{formatNumber(doc.views)}</td>
                <td>{formatNumber(doc.uniqueViews)}</td>
                <td>{formatNumber(doc.downloads)}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default PdfAnalyticsDashboard;

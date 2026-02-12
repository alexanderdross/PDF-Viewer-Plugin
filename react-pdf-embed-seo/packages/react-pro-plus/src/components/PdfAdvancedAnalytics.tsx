/**
 * PdfAdvancedAnalytics Component
 * Displays advanced analytics dashboard for PDF documents
 */

import React, { useMemo } from 'react';
import { useAdvancedAnalytics } from '../hooks/useAdvancedAnalytics';
import type { AdvancedAnalyticsData } from '../types';

export interface PdfAdvancedAnalyticsProps {
  documentId?: string | number;
  period?: '7d' | '30d' | '90d' | '1y';
  showHeatmap?: boolean;
  showDeviceStats?: boolean;
  showGeoData?: boolean;
  onExport?: (data: AdvancedAnalyticsData) => void;
}

export const PdfAdvancedAnalytics: React.FC<PdfAdvancedAnalyticsProps> = ({
  documentId,
  period = '30d',
  showHeatmap = true,
  showDeviceStats = true,
  showGeoData = true,
  onExport,
}) => {
  const { analytics, loading, error, loadAnalytics } = useAdvancedAnalytics({ documentId });

  const engagementLevel = useMemo(() => {
    if (!analytics) return 'N/A';
    const score = analytics.overview.engagementScore;
    if (score >= 80) return 'Excellent';
    if (score >= 60) return 'Good';
    if (score >= 40) return 'Average';
    if (score >= 20) return 'Low';
    return 'Very Low';
  }, [analytics]);

  const formatDuration = (seconds: number): string => {
    if (seconds < 60) return `${Math.round(seconds)}s`;
    if (seconds < 3600) return `${Math.round(seconds / 60)}m`;
    return `${Math.round(seconds / 3600)}h ${Math.round((seconds % 3600) / 60)}m`;
  };

  const formatPercentage = (value: number): string => {
    return `${Math.round(value * 100)}%`;
  };

  if (loading) {
    return (
      <div className="pdf-analytics pdf-analytics-loading">
        <div className="pdf-analytics-spinner" />
        <span>Loading analytics...</span>
      </div>
    );
  }

  if (error) {
    return (
      <div className="pdf-analytics pdf-analytics-error">
        <span>Failed to load analytics: {error.message}</span>
        <button onClick={() => loadAnalytics()}>Retry</button>
      </div>
    );
  }

  if (!analytics) {
    return (
      <div className="pdf-analytics pdf-analytics-empty">
        <span>No analytics data available</span>
      </div>
    );
  }

  return (
    <div className="pdf-analytics">
      <div className="pdf-analytics-header">
        <h3>Analytics Overview</h3>
        <div className="pdf-analytics-actions">
          <select
            value={period}
            onChange={(e) => {/* Handle period change */}}
            className="pdf-analytics-period-select"
          >
            <option value="7d">Last 7 days</option>
            <option value="30d">Last 30 days</option>
            <option value="90d">Last 90 days</option>
            <option value="1y">Last year</option>
          </select>
          {onExport && (
            <button
              onClick={() => onExport(analytics)}
              className="pdf-analytics-export-btn"
            >
              Export
            </button>
          )}
        </div>
      </div>

      <div className="pdf-analytics-stats-grid">
        <div className="pdf-analytics-stat-card">
          <span className="pdf-analytics-stat-label">Total Views</span>
          <span className="pdf-analytics-stat-value">{analytics.overview.totalViews.toLocaleString()}</span>
        </div>
        <div className="pdf-analytics-stat-card">
          <span className="pdf-analytics-stat-label">Unique Visitors</span>
          <span className="pdf-analytics-stat-value">{analytics.overview.uniqueVisitors.toLocaleString()}</span>
        </div>
        <div className="pdf-analytics-stat-card">
          <span className="pdf-analytics-stat-label">Avg. Time</span>
          <span className="pdf-analytics-stat-value">{formatDuration(analytics.overview.avgTimeOnPage)}</span>
        </div>
        <div className="pdf-analytics-stat-card">
          <span className="pdf-analytics-stat-label">Engagement Score</span>
          <span className="pdf-analytics-stat-value">{analytics.overview.engagementScore}</span>
          <span className="pdf-analytics-stat-sublabel">{engagementLevel}</span>
        </div>
        <div className="pdf-analytics-stat-card">
          <span className="pdf-analytics-stat-label">Bounce Rate</span>
          <span className="pdf-analytics-stat-value">{formatPercentage(analytics.overview.bounceRate)}</span>
        </div>
        <div className="pdf-analytics-stat-card">
          <span className="pdf-analytics-stat-label">Completion Rate</span>
          <span className="pdf-analytics-stat-value">{formatPercentage(analytics.overview.completionRate)}</span>
        </div>
      </div>

      {showDeviceStats && (
        <div className="pdf-analytics-section">
          <h4>Device Breakdown</h4>
          <div className="pdf-analytics-device-chart">
            {Object.entries(analytics.deviceStats).map(([device, count]) => {
              const total = Object.values(analytics.deviceStats).reduce((a, b) => a + b, 0);
              const percentage = total > 0 ? (count / total) * 100 : 0;
              return (
                <div key={device} className="pdf-analytics-device-bar">
                  <span className="pdf-analytics-device-label">{device}</span>
                  <div className="pdf-analytics-device-bar-container">
                    <div
                      className="pdf-analytics-device-bar-fill"
                      style={{ width: `${percentage}%` }}
                    />
                  </div>
                  <span className="pdf-analytics-device-value">{Math.round(percentage)}%</span>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {showGeoData && analytics.geoData.length > 0 && (
        <div className="pdf-analytics-section">
          <h4>Geographic Distribution</h4>
          <div className="pdf-analytics-geo-list">
            {analytics.geoData.slice(0, 10).map((geo) => (
              <div key={geo.countryCode} className="pdf-analytics-geo-item">
                <span className="pdf-analytics-geo-country">
                  {geo.country}
                </span>
                <span className="pdf-analytics-geo-views">{geo.views.toLocaleString()} views</span>
              </div>
            ))}
          </div>
        </div>
      )}

      {analytics.pageStats.length > 0 && (
        <div className="pdf-analytics-section">
          <h4>Page Performance</h4>
          <table className="pdf-analytics-page-table">
            <thead>
              <tr>
                <th>Page</th>
                <th>Views</th>
                <th>Avg. Time</th>
              </tr>
            </thead>
            <tbody>
              {analytics.pageStats.slice(0, 10).map((page) => (
                <tr key={page.page}>
                  <td>Page {page.page}</td>
                  <td>{page.views.toLocaleString()}</td>
                  <td>{formatDuration(page.avgTime)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      <style>{`
        .pdf-analytics {
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
          padding: 20px;
          background: #fff;
          border-radius: 8px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pdf-analytics-loading,
        .pdf-analytics-error,
        .pdf-analytics-empty {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
          padding: 40px;
          color: #666;
        }
        .pdf-analytics-spinner {
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
        .pdf-analytics-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
        }
        .pdf-analytics-header h3 {
          margin: 0;
          font-size: 18px;
          font-weight: 600;
        }
        .pdf-analytics-actions {
          display: flex;
          gap: 8px;
        }
        .pdf-analytics-period-select {
          padding: 6px 10px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background: #fff;
          font-size: 14px;
        }
        .pdf-analytics-export-btn {
          padding: 6px 12px;
          border: 1px solid #2196f3;
          border-radius: 4px;
          background: #2196f3;
          color: #fff;
          font-size: 14px;
          cursor: pointer;
        }
        .pdf-analytics-export-btn:hover {
          background: #1976d2;
        }
        .pdf-analytics-stats-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
          gap: 12px;
          margin-bottom: 24px;
        }
        .pdf-analytics-stat-card {
          padding: 16px;
          background: #f5f5f5;
          border-radius: 8px;
          text-align: center;
        }
        .pdf-analytics-stat-label {
          display: block;
          font-size: 12px;
          color: #666;
          margin-bottom: 4px;
        }
        .pdf-analytics-stat-value {
          display: block;
          font-size: 24px;
          font-weight: 600;
          color: #333;
        }
        .pdf-analytics-stat-sublabel {
          display: block;
          font-size: 11px;
          color: #888;
          margin-top: 2px;
        }
        .pdf-analytics-section {
          margin-top: 24px;
        }
        .pdf-analytics-section h4 {
          margin: 0 0 12px;
          font-size: 14px;
          font-weight: 600;
          color: #333;
        }
        .pdf-analytics-device-chart {
          display: flex;
          flex-direction: column;
          gap: 8px;
        }
        .pdf-analytics-device-bar {
          display: flex;
          align-items: center;
          gap: 8px;
        }
        .pdf-analytics-device-label {
          width: 60px;
          font-size: 13px;
          text-transform: capitalize;
        }
        .pdf-analytics-device-bar-container {
          flex: 1;
          height: 20px;
          background: #eee;
          border-radius: 4px;
          overflow: hidden;
        }
        .pdf-analytics-device-bar-fill {
          height: 100%;
          background: linear-gradient(90deg, #2196f3, #21cbf3);
          border-radius: 4px;
          transition: width 0.3s ease;
        }
        .pdf-analytics-device-value {
          width: 40px;
          text-align: right;
          font-size: 13px;
          font-weight: 500;
        }
        .pdf-analytics-geo-list {
          display: flex;
          flex-direction: column;
          gap: 4px;
        }
        .pdf-analytics-geo-item {
          display: flex;
          justify-content: space-between;
          padding: 8px;
          background: #f9f9f9;
          border-radius: 4px;
        }
        .pdf-analytics-geo-country {
          font-size: 13px;
        }
        .pdf-analytics-geo-views {
          font-size: 13px;
          color: #666;
        }
        .pdf-analytics-page-table {
          width: 100%;
          border-collapse: collapse;
          font-size: 13px;
        }
        .pdf-analytics-page-table th,
        .pdf-analytics-page-table td {
          padding: 8px;
          text-align: left;
          border-bottom: 1px solid #eee;
        }
        .pdf-analytics-page-table th {
          font-weight: 600;
          color: #666;
        }
      `}</style>
    </div>
  );
};

export default PdfAdvancedAnalytics;

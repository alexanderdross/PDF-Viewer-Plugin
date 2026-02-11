/**
 * PdfHeatmap Component
 * Visualizes user interaction heatmaps on PDF pages
 */

import React, { useEffect, useRef, useMemo } from 'react';
import { useHeatmap } from '../hooks/useHeatmap';
import type { HeatmapData, HeatmapPoint } from '../types';

export interface PdfHeatmapProps {
  documentId: string | number;
  page: number;
  width: number;
  height: number;
  opacity?: number;
  colorScheme?: 'warm' | 'cool' | 'grayscale';
  blurRadius?: number;
  showLegend?: boolean;
  enabled?: boolean;
}

const COLOR_SCHEMES = {
  warm: [
    { stop: 0, color: 'rgba(0, 0, 255, 0)' },
    { stop: 0.25, color: 'rgba(0, 255, 255, 0.5)' },
    { stop: 0.5, color: 'rgba(0, 255, 0, 0.7)' },
    { stop: 0.75, color: 'rgba(255, 255, 0, 0.8)' },
    { stop: 1, color: 'rgba(255, 0, 0, 1)' },
  ],
  cool: [
    { stop: 0, color: 'rgba(0, 0, 128, 0)' },
    { stop: 0.25, color: 'rgba(0, 128, 255, 0.5)' },
    { stop: 0.5, color: 'rgba(100, 149, 237, 0.7)' },
    { stop: 0.75, color: 'rgba(138, 43, 226, 0.8)' },
    { stop: 1, color: 'rgba(255, 0, 255, 1)' },
  ],
  grayscale: [
    { stop: 0, color: 'rgba(0, 0, 0, 0)' },
    { stop: 0.5, color: 'rgba(128, 128, 128, 0.5)' },
    { stop: 1, color: 'rgba(255, 255, 255, 1)' },
  ],
};

export const PdfHeatmap: React.FC<PdfHeatmapProps> = ({
  documentId,
  page,
  width,
  height,
  opacity = 0.6,
  colorScheme = 'warm',
  blurRadius = 15,
  showLegend = true,
  enabled = true,
}) => {
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const { heatmapData, loading, error } = useHeatmap(documentId, page);

  const colorStops = useMemo(() => COLOR_SCHEMES[colorScheme], [colorScheme]);

  useEffect(() => {
    if (!enabled || !canvasRef.current || !heatmapData?.points.length) return;

    const canvas = canvasRef.current;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Clear canvas
    ctx.clearRect(0, 0, width, height);

    // Create gradient for points
    const createGradient = (x: number, y: number, radius: number, intensity: number) => {
      const gradient = ctx.createRadialGradient(x, y, 0, x, y, radius);
      const alpha = intensity * opacity;

      colorStops.forEach(({ stop, color }) => {
        // Adjust alpha based on intensity
        const adjustedColor = color.replace(
          /rgba?\(([^)]+),\s*([0-9.]+)\)/,
          (_, rgb, a) => `rgba(${rgb}, ${parseFloat(a) * alpha})`
        );
        gradient.addColorStop(stop, adjustedColor);
      });

      return gradient;
    };

    // Draw heatmap points
    heatmapData.points.forEach((point: HeatmapPoint) => {
      const x = point.x * width;
      const y = point.y * height;
      const radius = blurRadius * (1 + point.intensity * 0.5);

      ctx.beginPath();
      ctx.arc(x, y, radius, 0, Math.PI * 2);
      ctx.fillStyle = createGradient(x, y, radius, point.intensity);
      ctx.fill();
    });

    // Apply blur effect
    if (blurRadius > 0) {
      ctx.filter = `blur(${blurRadius}px)`;
      ctx.globalCompositeOperation = 'source-over';
      ctx.drawImage(canvas, 0, 0);
      ctx.filter = 'none';
    }
  }, [heatmapData, width, height, opacity, colorStops, blurRadius, enabled]);

  if (!enabled) return null;

  if (loading) {
    return (
      <div className="pdf-heatmap pdf-heatmap-loading">
        <span>Loading heatmap...</span>
      </div>
    );
  }

  if (error) {
    return (
      <div className="pdf-heatmap pdf-heatmap-error">
        <span>Failed to load heatmap</span>
      </div>
    );
  }

  return (
    <div className="pdf-heatmap" style={{ position: 'relative' }}>
      <canvas
        ref={canvasRef}
        width={width}
        height={height}
        style={{
          position: 'absolute',
          top: 0,
          left: 0,
          pointerEvents: 'none',
          opacity: opacity,
        }}
      />

      {showLegend && (
        <div className="pdf-heatmap-legend">
          <span className="pdf-heatmap-legend-label">Low</span>
          <div
            className="pdf-heatmap-legend-gradient"
            style={{
              background: `linear-gradient(to right, ${colorStops.map(s => s.color).join(', ')})`,
            }}
          />
          <span className="pdf-heatmap-legend-label">High</span>
        </div>
      )}

      <style>{`
        .pdf-heatmap {
          position: relative;
        }
        .pdf-heatmap-loading,
        .pdf-heatmap-error {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          padding: 8px 16px;
          background: rgba(0,0,0,0.7);
          color: #fff;
          border-radius: 4px;
          font-size: 12px;
        }
        .pdf-heatmap-legend {
          position: absolute;
          bottom: 10px;
          right: 10px;
          display: flex;
          align-items: center;
          gap: 8px;
          padding: 6px 10px;
          background: rgba(255,255,255,0.9);
          border-radius: 4px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pdf-heatmap-legend-label {
          font-size: 10px;
          color: #666;
        }
        .pdf-heatmap-legend-gradient {
          width: 60px;
          height: 8px;
          border-radius: 4px;
        }
      `}</style>
    </div>
  );
};

export default PdfHeatmap;

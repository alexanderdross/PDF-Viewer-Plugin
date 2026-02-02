/**
 * PdfZoomControls Component
 * Zoom controls for the PDF viewer
 */

'use client';

import React, { useCallback } from 'react';

/**
 * Zoom preset values
 */
const ZOOM_PRESETS = [0.5, 0.75, 1, 1.25, 1.5, 2, 3];

/**
 * PdfZoomControls props
 */
export interface PdfZoomControlsProps {
  /** Current zoom level */
  zoom: number;
  /** Zoom change handler */
  onZoomChange: (zoom: number) => void;
  /** Zoom in handler */
  onZoomIn: () => void;
  /** Zoom out handler */
  onZoomOut: () => void;
  /** Minimum zoom */
  minZoom?: number;
  /** Maximum zoom */
  maxZoom?: number;
}

/**
 * PdfZoomControls Component
 */
export function PdfZoomControls({
  zoom,
  onZoomChange,
  onZoomIn,
  onZoomOut,
  minZoom = 0.25,
  maxZoom = 5,
}: PdfZoomControlsProps): React.ReactElement {
  // Format zoom as percentage
  const zoomPercent = Math.round(zoom * 100);

  // Handle preset selection
  const handlePresetChange = useCallback(
    (e: React.ChangeEvent<HTMLSelectElement>) => {
      const value = parseFloat(e.target.value);
      onZoomChange(value);
    },
    [onZoomChange]
  );

  return (
    <div className="pdf-zoom-controls">
      <button
        type="button"
        className="pdf-zoom-button pdf-zoom-out"
        onClick={onZoomOut}
        disabled={zoom <= minZoom}
        title="Zoom out"
        aria-label="Zoom out"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
          <line x1="8" y1="11" x2="14" y2="11" />
        </svg>
      </button>

      <select
        className="pdf-zoom-select"
        value={zoom}
        onChange={handlePresetChange}
        aria-label="Zoom level"
      >
        {ZOOM_PRESETS.map((preset) => (
          <option key={preset} value={preset}>
            {Math.round(preset * 100)}%
          </option>
        ))}
        {!ZOOM_PRESETS.includes(zoom) && (
          <option value={zoom}>{zoomPercent}%</option>
        )}
      </select>

      <button
        type="button"
        className="pdf-zoom-button pdf-zoom-in"
        onClick={onZoomIn}
        disabled={zoom >= maxZoom}
        title="Zoom in"
        aria-label="Zoom in"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
          <line x1="11" y1="8" x2="11" y2="14" />
          <line x1="8" y1="11" x2="14" y2="11" />
        </svg>
      </button>
    </div>
  );
}

export default PdfZoomControls;

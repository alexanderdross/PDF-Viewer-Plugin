/**
 * PdfAnnotationToolbar Component
 * Toolbar for creating PDF annotations
 */

import React, { useState, useCallback } from 'react';
import type { AnnotationType, AnnotationToolbarProps } from '../types';

interface ToolConfig {
  type: AnnotationType;
  icon: string;
  label: string;
  cursor: string;
}

const TOOLS: ToolConfig[] = [
  { type: 'highlight', icon: '🖍️', label: 'Highlight', cursor: 'text' },
  { type: 'underline', icon: '___', label: 'Underline', cursor: 'text' },
  { type: 'strikethrough', icon: '---', label: 'Strikethrough', cursor: 'text' },
  { type: 'text_note', icon: '📝', label: 'Text Note', cursor: 'crosshair' },
  { type: 'sticky_note', icon: '📌', label: 'Sticky Note', cursor: 'crosshair' },
  { type: 'rectangle', icon: '⬜', label: 'Rectangle', cursor: 'crosshair' },
  { type: 'circle', icon: '⭕', label: 'Circle', cursor: 'crosshair' },
  { type: 'freehand', icon: '✏️', label: 'Freehand', cursor: 'crosshair' },
  { type: 'arrow', icon: '➡️', label: 'Arrow', cursor: 'crosshair' },
  { type: 'line', icon: '—', label: 'Line', cursor: 'crosshair' },
];

const DEFAULT_COLORS = [
  '#ffff00', // Yellow
  '#ff6b6b', // Red
  '#4ecdc4', // Teal
  '#45b7d1', // Blue
  '#96ceb4', // Green
  '#ffeaa7', // Light Yellow
  '#dfe6e9', // Light Gray
  '#000000', // Black
];

export const PdfAnnotationToolbar: React.FC<AnnotationToolbarProps> = ({
  documentId,
  onAnnotationCreate,
  onAnnotationUpdate,
  onAnnotationDelete,
  allowedTypes,
  defaultColor = '#ffff00',
  disabled = false,
}) => {
  const [selectedTool, setSelectedTool] = useState<AnnotationType | null>(null);
  const [selectedColor, setSelectedColor] = useState(defaultColor);
  const [showColorPicker, setShowColorPicker] = useState(false);

  const availableTools = allowedTypes
    ? TOOLS.filter(tool => allowedTypes.includes(tool.type))
    : TOOLS;

  const handleToolSelect = useCallback((type: AnnotationType) => {
    setSelectedTool(prev => prev === type ? null : type);
  }, []);

  const handleColorSelect = useCallback((color: string) => {
    setSelectedColor(color);
    setShowColorPicker(false);
  }, []);

  const handleClear = useCallback(() => {
    setSelectedTool(null);
  }, []);

  return (
    <div className={`pdf-annotation-toolbar ${disabled ? 'disabled' : ''}`}>
      <div className="pdf-annotation-toolbar-tools">
        {availableTools.map(tool => (
          <button
            key={tool.type}
            className={`pdf-annotation-tool ${selectedTool === tool.type ? 'active' : ''}`}
            onClick={() => handleToolSelect(tool.type)}
            disabled={disabled}
            title={tool.label}
            aria-label={tool.label}
            aria-pressed={selectedTool === tool.type}
          >
            <span className="tool-icon">{tool.icon}</span>
          </button>
        ))}
      </div>

      <div className="pdf-annotation-toolbar-divider" />

      <div className="pdf-annotation-toolbar-color">
        <button
          className="pdf-annotation-color-button"
          onClick={() => setShowColorPicker(!showColorPicker)}
          disabled={disabled}
          style={{ backgroundColor: selectedColor }}
          aria-label="Select color"
        />

        {showColorPicker && (
          <div className="pdf-annotation-color-picker">
            {DEFAULT_COLORS.map(color => (
              <button
                key={color}
                className={`pdf-annotation-color-option ${color === selectedColor ? 'active' : ''}`}
                style={{ backgroundColor: color }}
                onClick={() => handleColorSelect(color)}
                aria-label={`Select ${color}`}
              />
            ))}
            <input
              type="color"
              value={selectedColor}
              onChange={(e) => handleColorSelect(e.target.value)}
              className="pdf-annotation-color-custom"
            />
          </div>
        )}
      </div>

      <div className="pdf-annotation-toolbar-divider" />

      <div className="pdf-annotation-toolbar-actions">
        <button
          className="pdf-annotation-action"
          onClick={handleClear}
          disabled={disabled || !selectedTool}
          title="Clear selection"
        >
          ✕
        </button>
      </div>

      <style>{`
        .pdf-annotation-toolbar {
          display: flex;
          align-items: center;
          gap: 8px;
          padding: 8px;
          background: #fff;
          border: 1px solid #ddd;
          border-radius: 8px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pdf-annotation-toolbar.disabled {
          opacity: 0.5;
          pointer-events: none;
        }
        .pdf-annotation-toolbar-tools {
          display: flex;
          gap: 4px;
        }
        .pdf-annotation-tool {
          width: 36px;
          height: 36px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background: #fff;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.2s ease;
        }
        .pdf-annotation-tool:hover {
          background: #f0f0f0;
          border-color: #bbb;
        }
        .pdf-annotation-tool.active {
          background: #e3f2fd;
          border-color: #2196f3;
        }
        .pdf-annotation-tool:disabled {
          opacity: 0.5;
          cursor: not-allowed;
        }
        .tool-icon {
          font-size: 16px;
        }
        .pdf-annotation-toolbar-divider {
          width: 1px;
          height: 24px;
          background: #ddd;
        }
        .pdf-annotation-toolbar-color {
          position: relative;
        }
        .pdf-annotation-color-button {
          width: 32px;
          height: 32px;
          border: 2px solid #ddd;
          border-radius: 4px;
          cursor: pointer;
        }
        .pdf-annotation-color-picker {
          position: absolute;
          top: 100%;
          left: 0;
          background: #fff;
          border: 1px solid #ddd;
          border-radius: 8px;
          padding: 8px;
          display: grid;
          grid-template-columns: repeat(4, 1fr);
          gap: 4px;
          z-index: 100;
          box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .pdf-annotation-color-option {
          width: 24px;
          height: 24px;
          border: 2px solid transparent;
          border-radius: 4px;
          cursor: pointer;
        }
        .pdf-annotation-color-option.active {
          border-color: #333;
        }
        .pdf-annotation-color-custom {
          grid-column: span 4;
          width: 100%;
          height: 28px;
          border: none;
          cursor: pointer;
        }
        .pdf-annotation-action {
          width: 32px;
          height: 32px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background: #fff;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .pdf-annotation-action:hover:not(:disabled) {
          background: #f0f0f0;
        }
        .pdf-annotation-action:disabled {
          opacity: 0.5;
          cursor: not-allowed;
        }
      `}</style>
    </div>
  );
};

export default PdfAnnotationToolbar;

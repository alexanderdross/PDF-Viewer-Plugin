/**
 * PdfAnnotations Component
 * Displays and manages PDF annotations
 */

import React, { useEffect, useRef, useCallback } from 'react';
import { useAnnotations } from '../hooks/useAnnotations';
import type { Annotation, AnnotationType } from '../types';

export interface PdfAnnotationsProps {
  documentId: string | number;
  page: number;
  scale?: number;
  onAnnotationClick?: (annotation: Annotation) => void;
  onAnnotationCreate?: (annotation: Annotation) => void;
  onAnnotationUpdate?: (annotation: Annotation) => void;
  onAnnotationDelete?: (id: string) => void;
  readonly?: boolean;
  highlightColor?: string;
  showAuthor?: boolean;
}

export const PdfAnnotations: React.FC<PdfAnnotationsProps> = ({
  documentId,
  page,
  scale = 1,
  onAnnotationClick,
  onAnnotationCreate,
  onAnnotationUpdate,
  onAnnotationDelete,
  readonly = false,
  highlightColor = '#ffff00',
  showAuthor = true,
}) => {
  const containerRef = useRef<HTMLDivElement>(null);
  const { annotations, loading, createAnnotation, updateAnnotation, deleteAnnotation } = useAnnotations({ documentId });

  const handleAnnotationClick = useCallback((annotation: Annotation) => {
    onAnnotationClick?.(annotation);
  }, [onAnnotationClick]);

  const handleCreate = useCallback(async (data: Partial<Annotation>) => {
    const newAnnotation = await createAnnotation(data);
    if (newAnnotation) {
      onAnnotationCreate?.(newAnnotation);
    }
    return newAnnotation;
  }, [createAnnotation, onAnnotationCreate]);

  const handleUpdate = useCallback(async (id: string, data: Partial<Annotation>) => {
    const updated = await updateAnnotation(id, data);
    if (updated) {
      onAnnotationUpdate?.(updated);
    }
    return updated;
  }, [updateAnnotation, onAnnotationUpdate]);

  const handleDelete = useCallback(async (id: string) => {
    await deleteAnnotation(id);
    onAnnotationDelete?.(id);
  }, [deleteAnnotation, onAnnotationDelete]);

  const getAnnotationStyle = (annotation: Annotation): React.CSSProperties => {
    const baseStyle: React.CSSProperties = {
      position: 'absolute',
      left: `${annotation.x * scale}px`,
      top: `${annotation.y * scale}px`,
      width: annotation.width ? `${annotation.width * scale}px` : 'auto',
      height: annotation.height ? `${annotation.height * scale}px` : 'auto',
      cursor: readonly ? 'pointer' : 'move',
      pointerEvents: 'auto',
    };

    switch (annotation.type) {
      case 'highlight':
        return {
          ...baseStyle,
          backgroundColor: annotation.color || highlightColor,
          opacity: annotation.opacity || 0.4,
          mixBlendMode: 'multiply',
        };
      case 'underline':
        return {
          ...baseStyle,
          borderBottom: `2px solid ${annotation.color || '#000'}`,
          height: '2px',
        };
      case 'strikethrough':
        return {
          ...baseStyle,
          borderBottom: `2px solid ${annotation.color || '#ff0000'}`,
          transform: 'translateY(-50%)',
        };
      case 'text_note':
      case 'sticky_note':
        return {
          ...baseStyle,
          backgroundColor: annotation.color || '#ffffa0',
          border: '1px solid #e0e000',
          padding: '4px',
          fontSize: `${12 * scale}px`,
          borderRadius: '4px',
          boxShadow: '2px 2px 4px rgba(0,0,0,0.2)',
        };
      case 'rectangle':
        return {
          ...baseStyle,
          border: `2px solid ${annotation.color || '#0000ff'}`,
          backgroundColor: 'transparent',
        };
      case 'circle':
        return {
          ...baseStyle,
          border: `2px solid ${annotation.color || '#0000ff'}`,
          backgroundColor: 'transparent',
          borderRadius: '50%',
        };
      default:
        return baseStyle;
    }
  };

  const renderAnnotation = (annotation: Annotation) => {
    const style = getAnnotationStyle(annotation);

    return (
      <div
        key={annotation.id}
        className={`pdf-annotation pdf-annotation-${annotation.type}`}
        style={style}
        onClick={() => handleAnnotationClick(annotation)}
        data-annotation-id={annotation.id}
        data-annotation-type={annotation.type}
        role="button"
        tabIndex={0}
        onKeyPress={(e) => e.key === 'Enter' && handleAnnotationClick(annotation)}
      >
        {(annotation.type === 'text_note' || annotation.type === 'sticky_note') && (
          <div className="pdf-annotation-content">
            {annotation.content}
            {showAuthor && annotation.authorName && (
              <div className="pdf-annotation-author">
                — {annotation.authorName}
              </div>
            )}
          </div>
        )}

        {!readonly && (
          <button
            className="pdf-annotation-delete"
            onClick={(e) => {
              e.stopPropagation();
              handleDelete(annotation.id);
            }}
            aria-label="Delete annotation"
          >
            ×
          </button>
        )}
      </div>
    );
  };

  if (loading) {
    return <div className="pdf-annotations-loading">Loading annotations...</div>;
  }

  return (
    <div
      ref={containerRef}
      className="pdf-annotations-layer"
      style={{
        position: 'absolute',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        pointerEvents: 'none',
        zIndex: 10,
      }}
    >
      {annotations.map(renderAnnotation)}

      <style>{`
        .pdf-annotation {
          transition: opacity 0.2s ease;
        }
        .pdf-annotation:hover {
          opacity: 1 !important;
        }
        .pdf-annotation-delete {
          position: absolute;
          top: -8px;
          right: -8px;
          width: 16px;
          height: 16px;
          border-radius: 50%;
          background: #ff4444;
          color: white;
          border: none;
          cursor: pointer;
          font-size: 12px;
          line-height: 1;
          display: none;
          align-items: center;
          justify-content: center;
        }
        .pdf-annotation:hover .pdf-annotation-delete {
          display: flex;
        }
        .pdf-annotation-content {
          max-width: 200px;
          word-wrap: break-word;
        }
        .pdf-annotation-author {
          font-size: 10px;
          color: #666;
          margin-top: 4px;
        }
      `}</style>
    </div>
  );
};

export default PdfAnnotations;

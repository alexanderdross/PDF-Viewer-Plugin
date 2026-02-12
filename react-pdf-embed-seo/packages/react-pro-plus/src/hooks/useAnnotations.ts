import { useState, useCallback } from 'react';
import type { Annotation, AnnotationType } from '../types';

interface UseAnnotationsOptions {
  documentId: string | number;
  onSave?: (annotation: Annotation) => Promise<void>;
  onDelete?: (annotationId: string) => Promise<void>;
}

interface UseAnnotationsReturn {
  annotations: Annotation[];
  loading: boolean;
  error: Error | null;
  createAnnotation: (data: Partial<Annotation>) => Promise<Annotation>;
  updateAnnotation: (annotationId: string, data: Partial<Annotation>) => Promise<Annotation>;
  deleteAnnotation: (annotationId: string) => Promise<void>;
  loadAnnotations: () => Promise<void>;
}

export function useAnnotations(options: UseAnnotationsOptions): UseAnnotationsReturn {
  const { documentId, onSave, onDelete } = options;
  const [annotations, setAnnotations] = useState<Annotation[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const loadAnnotations = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      // API call to load annotations
      const response = await fetch(
        `/api/pdf-embed-seo/v1/documents/${documentId}/annotations`
      );

      if (!response.ok) {
        throw new Error('Failed to load annotations');
      }

      const data = await response.json();
      setAnnotations(data.annotations || []);
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
    } finally {
      setLoading(false);
    }
  }, [documentId]);

  const createAnnotation = useCallback(
    async (data: Partial<Annotation>): Promise<Annotation> => {
      const annotation: Annotation = {
        id: crypto.randomUUID(),
        documentId,
        page: data.page || 1,
        type: data.type || 'highlight',
        x: data.x || 0,
        y: data.y || 0,
        width: data.width,
        height: data.height,
        color: data.color || '#ffff00',
        opacity: data.opacity || 1,
        content: data.content,
        pathData: data.pathData,
        authorId: data.authorId || 0,
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
      };

      if (onSave) {
        await onSave(annotation);
      }

      setAnnotations((prev) => [...prev, annotation]);
      return annotation;
    },
    [documentId, onSave]
  );

  const updateAnnotation = useCallback(
    async (annotationId: string, data: Partial<Annotation>): Promise<Annotation> => {
      const updated = annotations.find((a) => a.id === annotationId);
      if (!updated) {
        throw new Error('Annotation not found');
      }

      const updatedAnnotation: Annotation = {
        ...updated,
        ...data,
        updatedAt: new Date().toISOString(),
      };

      if (onSave) {
        await onSave(updatedAnnotation);
      }

      setAnnotations((prev) =>
        prev.map((a) => (a.id === annotationId ? updatedAnnotation : a))
      );

      return updatedAnnotation;
    },
    [annotations, onSave]
  );

  const deleteAnnotation = useCallback(
    async (annotationId: string): Promise<void> => {
      if (onDelete) {
        await onDelete(annotationId);
      }

      setAnnotations((prev) => prev.filter((a) => a.id !== annotationId));
    },
    [onDelete]
  );

  return {
    annotations,
    loading,
    error,
    createAnnotation,
    updateAnnotation,
    deleteAnnotation,
    loadAnnotations,
  };
}

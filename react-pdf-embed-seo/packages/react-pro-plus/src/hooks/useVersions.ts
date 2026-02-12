import { useState, useCallback } from 'react';
import type { DocumentVersion } from '../types';

interface UseVersionsOptions {
  documentId: string | number;
}

interface UseVersionsReturn {
  versions: DocumentVersion[];
  currentVersion: DocumentVersion | null;
  loading: boolean;
  error: Error | null;
  loadVersions: () => Promise<void>;
  restoreVersion: (versionId: number) => Promise<void>;
  createVersion: (file: File, changelog?: string) => Promise<DocumentVersion>;
}

export function useVersions(options: UseVersionsOptions): UseVersionsReturn {
  const { documentId } = options;
  const [versions, setVersions] = useState<DocumentVersion[]>([]);
  const [currentVersion, setCurrentVersion] = useState<DocumentVersion | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const loadVersions = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(
        `/api/pdf-embed-seo/v1/documents/${documentId}/versions`
      );

      if (!response.ok) {
        throw new Error('Failed to load versions');
      }

      const data = await response.json();
      setVersions(data.versions || []);
      setCurrentVersion(data.versions?.find((v: DocumentVersion) => v.isCurrent) || null);
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
    } finally {
      setLoading(false);
    }
  }, [documentId]);

  const restoreVersion = useCallback(
    async (versionId: number) => {
      setLoading(true);
      setError(null);

      try {
        const response = await fetch(
          `/api/pdf-embed-seo/v1/documents/${documentId}/versions/${versionId}/restore`,
          { method: 'POST' }
        );

        if (!response.ok) {
          throw new Error('Failed to restore version');
        }

        await loadVersions();
      } catch (err) {
        setError(err instanceof Error ? err : new Error('Unknown error'));
        throw err;
      } finally {
        setLoading(false);
      }
    },
    [documentId, loadVersions]
  );

  const createVersion = useCallback(
    async (file: File, changelog?: string): Promise<DocumentVersion> => {
      setLoading(true);
      setError(null);

      try {
        const formData = new FormData();
        formData.append('file', file);
        if (changelog) {
          formData.append('changelog', changelog);
        }

        const response = await fetch(
          `/api/pdf-embed-seo/v1/documents/${documentId}/versions`,
          {
            method: 'POST',
            body: formData,
          }
        );

        if (!response.ok) {
          throw new Error('Failed to create version');
        }

        const data = await response.json();
        await loadVersions();

        return data.version;
      } catch (err) {
        setError(err instanceof Error ? err : new Error('Unknown error'));
        throw err;
      } finally {
        setLoading(false);
      }
    },
    [documentId, loadVersions]
  );

  return {
    versions,
    currentVersion,
    loading,
    error,
    loadVersions,
    restoreVersion,
    createVersion,
  };
}

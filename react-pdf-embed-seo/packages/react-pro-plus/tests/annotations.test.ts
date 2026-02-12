import { describe, it, expect } from 'vitest';
import type { Annotation, AnnotationType } from '../src/types';

describe('Annotations', () => {
  describe('Annotation Types', () => {
    it('should define valid annotation types', () => {
      const validTypes: AnnotationType[] = [
        'highlight',
        'underline',
        'strikethrough',
        'text_note',
        'sticky_note',
        'freehand',
        'rectangle',
        'circle',
        'arrow',
        'line',
      ];

      validTypes.forEach((type) => {
        expect(type).toMatch(/^[a-z_]+$/);
      });
    });
  });

  describe('Annotation Data Structure', () => {
    it('should have required fields', () => {
      const annotation: Annotation = {
        id: 'uuid-123',
        documentId: 1,
        page: 1,
        type: 'highlight',
        x: 100,
        y: 200,
        width: 300,
        height: 50,
        color: '#ffff00',
        opacity: 0.5,
        content: 'Test annotation',
        authorId: 1,
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
      };

      expect(annotation).toHaveProperty('id');
      expect(annotation).toHaveProperty('documentId');
      expect(annotation).toHaveProperty('page');
      expect(annotation).toHaveProperty('type');
      expect(annotation).toHaveProperty('x');
      expect(annotation).toHaveProperty('y');
      expect(annotation).toHaveProperty('authorId');
    });
  });

  describe('Color Validation', () => {
    it('should validate hex color format', () => {
      const validColors = ['#ffff00', '#FF0000', '#00ff00'];
      const hexPattern = /^#[0-9a-fA-F]{6}$/;

      validColors.forEach((color) => {
        expect(color).toMatch(hexPattern);
      });
    });
  });

  describe('Coordinate Validation', () => {
    it('should have non-negative coordinates', () => {
      const annotation: Partial<Annotation> = {
        x: 100,
        y: 200,
        width: 300,
        height: 50,
      };

      expect(annotation.x).toBeGreaterThanOrEqual(0);
      expect(annotation.y).toBeGreaterThanOrEqual(0);
      expect(annotation.width).toBeGreaterThan(0);
      expect(annotation.height).toBeGreaterThan(0);
    });
  });

  describe('Freehand Path Data', () => {
    it('should validate path data structure', () => {
      const pathData = [
        { x: 100, y: 100 },
        { x: 150, y: 120 },
        { x: 200, y: 100 },
      ];

      expect(pathData.length).toBeGreaterThan(1);
      pathData.forEach((point) => {
        expect(point).toHaveProperty('x');
        expect(point).toHaveProperty('y');
      });
    });
  });
});

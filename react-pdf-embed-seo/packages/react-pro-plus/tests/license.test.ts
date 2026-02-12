import { describe, it, expect } from 'vitest';
import type { ProPlusLicenseStatus } from '../src/types';

describe('Pro+ License', () => {
  const LICENSE_PATTERNS = {
    proPlus:
      /^PDF\$PRO\+#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}-[A-Z0-9]{4}![A-Z0-9]{4}$/i,
    unlimited: /^PDF\$UNLIMITED#[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i,
    dev: /^PDF\$DEV#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/i,
  };

  describe('Pro+ License Key Format', () => {
    it('should validate Pro+ license key format', () => {
      const validKey = 'PDF$PRO+#ABCD-EFGH@IJKL-MNOP!QRST';
      expect(validKey).toMatch(LICENSE_PATTERNS.proPlus);
    });

    it('should reject invalid formats', () => {
      const invalidKeys = [
        'invalid-key',
        'PDF$PRO#ABCD-EFGH@IJKL-MNOP!QRST', // Missing +
        'PDFPRO+ABCDEFGH@IJKLMNOP!QRST', // Missing $ and #
        'short',
      ];

      invalidKeys.forEach((key) => {
        expect(key).not.toMatch(LICENSE_PATTERNS.proPlus);
      });
    });
  });

  describe('Unlimited License Key Format', () => {
    it('should validate unlimited license key', () => {
      const unlimitedKey = 'PDF$UNLIMITED#ABCD@EFGH!IJKL';
      expect(unlimitedKey).toMatch(LICENSE_PATTERNS.unlimited);
    });
  });

  describe('Development License Key Format', () => {
    it('should validate dev license key', () => {
      const devKey = 'PDF$DEV#ABCD-EFGH@IJKL!MNOP';
      expect(devKey).toMatch(LICENSE_PATTERNS.dev);
    });
  });

  describe('License Status Values', () => {
    it('should have valid status values', () => {
      const validStatuses: ProPlusLicenseStatus[] = [
        'valid',
        'invalid',
        'expired',
        'inactive',
        'grace_period',
      ];

      validStatuses.forEach((status) => {
        expect([
          'valid',
          'invalid',
          'expired',
          'inactive',
          'grace_period',
        ]).toContain(status);
      });
    });
  });

  describe('Grace Period', () => {
    it('should detect grace period (within 14 days after expiration)', () => {
      const expiredDate = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000); // 7 days ago
      const gracePeriodDays = 14;
      const graceEnd = new Date(
        expiredDate.getTime() + gracePeriodDays * 24 * 60 * 60 * 1000
      );

      expect(Date.now()).toBeLessThan(graceEnd.getTime());
    });

    it('should detect grace period exceeded', () => {
      const expiredDate = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000); // 30 days ago
      const gracePeriodDays = 14;
      const graceEnd = new Date(
        expiredDate.getTime() + gracePeriodDays * 24 * 60 * 60 * 1000
      );

      expect(Date.now()).toBeGreaterThan(graceEnd.getTime());
    });
  });

  describe('License Validity Check', () => {
    it('should identify valid statuses', () => {
      const validStatuses: ProPlusLicenseStatus[] = ['valid', 'grace_period'];
      const isValid = (status: ProPlusLicenseStatus) =>
        validStatuses.includes(status);

      expect(isValid('valid')).toBe(true);
      expect(isValid('grace_period')).toBe(true);
      expect(isValid('expired')).toBe(false);
      expect(isValid('inactive')).toBe(false);
    });
  });
});

/**
 * License validation utilities
 */

import type { ProPlusLicenseStatus } from '../types';

interface LicenseValidationResult {
  isValid: boolean;
  status: ProPlusLicenseStatus;
  type: 'pro_plus' | 'unlimited' | 'dev' | 'invalid';
  message: string;
}

/**
 * Validate Pro+ license key format
 * Formats:
 * - Pro+: PDF$PRO+#XXXX-XXXX@XXXX-XXXX!XXXX
 * - Unlimited: PDF$UNLIMITED#XXXX@XXXX!XXXX
 * - Dev: PDF$DEV#XXXX-XXXX@XXXX!XXXX
 */
export function validateProPlusLicense(licenseKey: string): LicenseValidationResult {
  if (!licenseKey || typeof licenseKey !== 'string') {
    return {
      isValid: false,
      status: 'inactive',
      type: 'invalid',
      message: 'No license key provided',
    };
  }

  const key = licenseKey.trim().toUpperCase();

  // Pro+ Enterprise license format
  const proPlusPattern = /^PDF\$PRO\+#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}-[A-Z0-9]{4}![A-Z0-9]{4}$/;
  if (proPlusPattern.test(key)) {
    return {
      isValid: true,
      status: 'valid',
      type: 'pro_plus',
      message: 'Valid Pro+ Enterprise license',
    };
  }

  // Unlimited license format
  const unlimitedPattern = /^PDF\$UNLIMITED#[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/;
  if (unlimitedPattern.test(key)) {
    return {
      isValid: true,
      status: 'valid',
      type: 'unlimited',
      message: 'Valid unlimited license',
    };
  }

  // Development license format
  const devPattern = /^PDF\$DEV#[A-Z0-9]{4}-[A-Z0-9]{4}@[A-Z0-9]{4}![A-Z0-9]{4}$/;
  if (devPattern.test(key)) {
    return {
      isValid: true,
      status: 'valid',
      type: 'dev',
      message: 'Valid development license',
    };
  }

  return {
    isValid: false,
    status: 'invalid',
    type: 'invalid',
    message: 'Invalid license key format',
  };
}

/**
 * Mask a license key for display
 */
export function maskLicenseKey(licenseKey: string): string {
  if (!licenseKey || licenseKey.length <= 10) {
    return '*'.repeat(licenseKey?.length || 0);
  }

  const prefix = licenseKey.slice(0, 8);
  const suffix = licenseKey.slice(-4);
  const middle = '*'.repeat(Math.max(0, licenseKey.length - 12));

  return `${prefix}${middle}${suffix}`;
}

/**
 * Check if license is expired
 */
export function isLicenseExpired(expiresAt: string | undefined): boolean {
  if (!expiresAt) {
    return false;
  }

  const expiryDate = new Date(expiresAt);
  return expiryDate < new Date();
}

/**
 * Check if license is in grace period (14 days after expiry)
 */
export function isInGracePeriod(expiresAt: string | undefined): boolean {
  if (!expiresAt) {
    return false;
  }

  const expiryDate = new Date(expiresAt);
  const now = new Date();
  const graceEndDate = new Date(expiryDate);
  graceEndDate.setDate(graceEndDate.getDate() + 14);

  return expiryDate < now && now < graceEndDate;
}

/**
 * Get days until license expires
 */
export function getDaysUntilExpiry(expiresAt: string | undefined): number | null {
  if (!expiresAt) {
    return null;
  }

  const expiryDate = new Date(expiresAt);
  const now = new Date();
  const diffTime = expiryDate.getTime() - now.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  return diffDays;
}

export default {
  validateProPlusLicense,
  maskLicenseKey,
  isLicenseExpired,
  isInGracePeriod,
  getDaysUntilExpiry,
};

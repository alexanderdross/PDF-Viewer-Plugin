# Changelog v1.3.0

**Release Date:** 2026-02-11
**Platforms:** WordPress, Drupal, React/Next.js
**Tiers:** Free, Pro, Pro+ Enterprise

---

## Overview

This release introduces comprehensive QA, UAT, and Unit tests across all modules (Free, Pro, and Pro+ Enterprise) for WordPress, Drupal, and React/Next.js platforms. The naming convention has been updated from "Premium" to "Pro" for consistency.

---

## New Features

### Pro+ Enterprise Module (v1.3.0)

#### WordPress Pro+ (`/pdf-embed-seo-optimize/pro-plus/`)
- **Complete Test Suite**: 9 unit test files covering all Pro+ features
  - `test-pro-plus-license.php` - License validation tests
  - `test-pro-plus-advanced-analytics.php` - Heatmaps, engagement scoring
  - `test-pro-plus-security.php` - 2FA, IP whitelisting, audit logs
  - `test-pro-plus-webhooks.php` - Webhook delivery and signatures
  - `test-pro-plus-versioning.php` - Document versioning
  - `test-pro-plus-annotations.php` - PDF annotations
  - `test-pro-plus-compliance.php` - GDPR/HIPAA compliance
  - `test-pro-plus-white-label.php` - White label branding
  - `test-pro-plus-rest-api.php` - Pro+ REST API endpoints

#### Drupal Pro+ (`/drupal-pdf-embed-seo/modules/pdf_embed_seo_pro_plus/`)
- **New Module Created**: Full Pro+ Enterprise module for Drupal
- **Database Schema**: Tables for versions, annotations, audit logs, webhooks, consents
- **Services**:
  - `VersionManager` - Document versioning
  - `AnnotationManager` - PDF annotations
  - `AuditLogger` - Audit trail logging
  - `WebhookDispatcher` - Webhook delivery
  - `AdvancedAnalytics` - Advanced analytics
  - `ComplianceManager` - GDPR/HIPAA compliance
  - `TwoFactorAuth` - 2FA authentication
  - `WhiteLabel` - Branding customization
  - `LicenseValidator` - License validation
- **Unit Tests**: 6 test files
  - `VersionManagerTest.php`
  - `AnnotationManagerTest.php`
  - `WebhookDispatcherTest.php`
  - `AuditLoggerTest.php`
  - `ComplianceManagerTest.php`
  - `LicenseValidatorTest.php`

#### React Pro+ (`/react-pdf-embed-seo/packages/react-pro-plus/`)
- **New Package Created**: `@pdf-embed-seo/react-pro-plus` v1.3.0
- **Components**:
  - `PdfAnnotations` - Annotation display
  - `PdfAnnotationToolbar` - Annotation tools
  - `PdfVersionHistory` - Version management
  - `PdfAdvancedAnalytics` - Analytics dashboard
  - `PdfHeatmap` - Heatmap visualization
  - `PdfComplianceConsent` - Consent banner
  - `PdfAuditLog` - Audit log viewer
  - `PdfWebhookConfig` - Webhook management
  - `PdfWhiteLabel` - White label wrapper
  - `PdfTwoFactorAuth` - 2FA setup
- **Hooks**:
  - `useAnnotations` - Annotation management
  - `useVersions` - Version management
  - `useAdvancedAnalytics` - Analytics data
  - `useHeatmap` - Heatmap data
  - `useCompliance` - Compliance features
  - `useAuditLog` - Audit log access
  - `useWebhooks` - Webhook management
  - `useWhiteLabel` - White label config
  - `useTwoFactorAuth` - 2FA management
  - `useProPlusLicense` - License validation
- **Unit Tests**: 5 test files
  - `annotations.test.ts`
  - `versions.test.ts`
  - `license.test.ts`
  - `webhooks.test.ts`
  - `compliance.test.ts`

---

## Test Coverage

### Comprehensive QA Test Plan
- **Location**: `/tests/qa/QA-TEST-PLAN-COMPLETE.md`
- **Coverage**: 200+ test cases across all platforms and tiers
- **Sections**:
  - WordPress Free/Pro/Pro+ tests
  - Drupal Free/Pro/Pro+ tests
  - React/Next.js Free/Pro/Pro+ tests
  - Cross-platform integration tests
  - Performance tests
  - Security tests
  - Accessibility tests

### Comprehensive UAT Test Plan
- **Location**: `/tests/qa/UAT-TEST-PLAN-COMPLETE.md`
- **Coverage**: Complete user acceptance scenarios
- **User Journeys**:
  - Content Creator publishing documents
  - Visitor researching products
  - Enterprise Admin managing secure documents
  - Developer implementing React integration

---

## Naming Convention Update

The "Premium" tier has been renamed to "Pro" for consistency:

| Old Name | New Name |
|----------|----------|
| Premium | Pro |
| Pro Plus | Pro+ Enterprise |

### Package Names
- `@pdf-embed-seo/react-premium` → `@pdf-embed-seo/react-pro`
- `@pdf-embed-seo/react-pro-plus` (new)

---

## Test Statistics

| Platform | Module | Unit Tests | QA Tests | UAT Scenarios |
|----------|--------|------------|----------|---------------|
| WordPress | Free | 7 | 35 | 5 |
| WordPress | Pro | 7 | 45 | 6 |
| WordPress | Pro+ | 9 | 50 | 8 |
| Drupal | Free | 9 | 30 | 3 |
| Drupal | Pro | 3 | 25 | 4 |
| Drupal | Pro+ | 6 | 40 | 6 |
| React | Free | 4 | 25 | 4 |
| React | Pro | 4 | 30 | 4 |
| React | Pro+ | 5 | 35 | 4 |
| **Total** | | **54** | **315** | **44** |

---

## File Changes Summary

### New Files Created

```
pdf-embed-seo-optimize/pro-plus/tests/
├── bootstrap.php
├── class-pro-plus-test-case.php
└── unit/
    ├── test-pro-plus-license.php
    ├── test-pro-plus-advanced-analytics.php
    ├── test-pro-plus-security.php
    ├── test-pro-plus-webhooks.php
    ├── test-pro-plus-versioning.php
    ├── test-pro-plus-annotations.php
    ├── test-pro-plus-compliance.php
    ├── test-pro-plus-white-label.php
    └── test-pro-plus-rest-api.php

drupal-pdf-embed-seo/modules/pdf_embed_seo_pro_plus/
├── pdf_embed_seo_pro_plus.info.yml
├── pdf_embed_seo_pro_plus.module
├── pdf_embed_seo_pro_plus.install
├── pdf_embed_seo_pro_plus.routing.yml
├── pdf_embed_seo_pro_plus.permissions.yml
├── pdf_embed_seo_pro_plus.services.yml
├── config/
│   ├── install/pdf_embed_seo_pro_plus.settings.yml
│   └── schema/pdf_embed_seo_pro_plus.schema.yml
└── tests/src/Unit/
    ├── VersionManagerTest.php
    ├── AnnotationManagerTest.php
    ├── WebhookDispatcherTest.php
    ├── AuditLoggerTest.php
    ├── ComplianceManagerTest.php
    └── LicenseValidatorTest.php

react-pdf-embed-seo/packages/react-pro-plus/
├── package.json
├── tsconfig.json
├── tsup.config.ts
├── src/
│   ├── index.ts
│   ├── types/index.ts
│   └── hooks/
│       ├── useAnnotations.ts
│       ├── useVersions.ts
│       ├── useAdvancedAnalytics.ts
│       └── useProPlusLicense.ts
└── tests/
    ├── annotations.test.ts
    ├── versions.test.ts
    ├── license.test.ts
    ├── webhooks.test.ts
    └── compliance.test.ts

tests/qa/
├── QA-TEST-PLAN-COMPLETE.md
└── UAT-TEST-PLAN-COMPLETE.md
```

---

## Breaking Changes

None. This release is fully backward compatible.

---

## Migration Guide

No migration required. Simply update to v1.3.0.

---

## Credits

Made with care by [Dross:Media](https://dross.net/media/)

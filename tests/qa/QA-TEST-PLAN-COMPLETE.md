# PDF Embed & SEO Optimize - Complete QA Test Plan

**Version:** 1.3.0
**Last Updated:** 2026-02-11
**Platforms:** WordPress, Drupal, React/Next.js
**Tiers:** Free, Pro, Pro+ Enterprise

---

## Table of Contents

1. [Test Environment Requirements](#1-test-environment-requirements)
2. [WordPress Free Module Tests](#2-wordpress-free-module-tests)
3. [WordPress Pro Module Tests](#3-wordpress-pro-module-tests)
4. [WordPress Pro+ Enterprise Tests](#4-wordpress-pro-enterprise-tests)
5. [Drupal Free Module Tests](#5-drupal-free-module-tests)
6. [Drupal Pro Module Tests](#6-drupal-pro-module-tests)
7. [Drupal Pro+ Enterprise Tests](#7-drupal-pro-enterprise-tests)
8. [React/Next.js Free Package Tests](#8-reactnextjs-free-package-tests)
9. [React/Next.js Pro Package Tests](#9-reactnextjs-pro-package-tests)
10. [React/Next.js Pro+ Package Tests](#10-reactnextjs-pro-package-tests)
11. [Cross-Platform Integration Tests](#11-cross-platform-integration-tests)
12. [Performance Tests](#12-performance-tests)
13. [Security Tests](#13-security-tests)
14. [Accessibility Tests](#14-accessibility-tests)

---

## 1. Test Environment Requirements

### WordPress

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| WordPress | 5.8+ | 6.4+ |
| PHP | 7.4+ | 8.2+ |
| MySQL | 5.7+ | 8.0+ |
| Memory | 128MB | 256MB |

### Drupal

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| Drupal | 10.0 | 11.0 |
| PHP | 8.1+ | 8.3+ |
| Database | MySQL 5.7+ / PostgreSQL 12+ | MySQL 8.0+ |
| Memory | 128MB | 256MB |

### React/Next.js

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| Node.js | 18+ | 20+ |
| React | 18+ | 19+ |
| Next.js | 13+ | 15+ |
| TypeScript | 5.0+ | 5.3+ |

### Browsers to Test

- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)
- Mobile Safari (iOS 15+)
- Chrome Mobile (Android 10+)

---

## 2. WordPress Free Module Tests

### 2.1 Installation & Activation

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-001 | Fresh Installation | Upload and activate plugin | Plugin activates without errors | |
| WPF-002 | Database Tables | Check database after activation | pdf_document CPT exists | |
| WPF-003 | Uninstall Cleanup | Deactivate and delete plugin | All data removed (if option enabled) | |
| WPF-004 | Multisite Activation | Network activate on multisite | Works on all subsites | |

### 2.2 Custom Post Type

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-010 | Create PDF Document | Add New > PDF Document | Post created successfully | |
| WPF-011 | Upload PDF File | Use meta box to upload PDF | File attached to document | |
| WPF-012 | Set Title & Slug | Enter title, verify slug | Clean URL /pdf/slug/ created | |
| WPF-013 | Publish Document | Click Publish | Document visible on frontend | |
| WPF-014 | Edit Document | Modify and update | Changes saved | |
| WPF-015 | Delete Document | Trash and delete | Document removed | |
| WPF-016 | Quick Edit | Use Quick Edit | Fields update correctly | |

### 2.3 PDF Viewer

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-020 | Load PDF | View single PDF document | PDF renders in viewer | |
| WPF-021 | Page Navigation | Use prev/next buttons | Pages change correctly | |
| WPF-022 | Go to Page | Enter page number | Jumps to specified page | |
| WPF-023 | Zoom In/Out | Click zoom controls | PDF scales correctly | |
| WPF-024 | Fit Width/Page | Use fit buttons | PDF fits appropriately | |
| WPF-025 | Fullscreen | Click fullscreen | Viewer goes fullscreen | |
| WPF-026 | Print Button | Click print (when allowed) | Print dialog opens | |
| WPF-027 | Download Button | Click download (when allowed) | PDF downloads | |
| WPF-028 | Dark Theme | Toggle theme | Viewer switches to dark | |
| WPF-029 | Responsive | Resize window | Viewer adapts | |

### 2.4 Archive Page

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-030 | Archive Load | Visit /pdf/ | Archive page displays | |
| WPF-031 | Pagination | Navigate pages | Pagination works | |
| WPF-032 | Grid View | Switch to grid | Documents show in grid | |
| WPF-033 | List View | Switch to list | Documents show in list | |
| WPF-034 | Custom Heading | Set custom heading | Heading displays | |
| WPF-035 | Custom Colors | Set font/background colors | Colors applied | |
| WPF-036 | Content Alignment | Set alignment | Content aligns correctly | |

### 2.5 Shortcodes

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-040 | [pdf_viewer id="X"] | Add to page | PDF embeds correctly | |
| WPF-041 | Width/Height Params | Set custom dimensions | Viewer resizes | |
| WPF-042 | [pdf_viewer_sitemap] | Add to page | List of PDFs displays | |
| WPF-043 | Sitemap Ordering | Set orderby/order | Correct ordering | |
| WPF-044 | Sitemap Limit | Set limit param | Only X documents show | |

### 2.6 REST API

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-050 | GET /documents | API request | List of documents returned | |
| WPF-051 | GET /documents/{id} | API request | Single document data | |
| WPF-052 | GET /documents/{id}/data | API request | Secure PDF URL returned | |
| WPF-053 | POST /documents/{id}/view | Track view | View count incremented | |
| WPF-054 | GET /settings | API request | Public settings returned | |
| WPF-055 | Pagination | ?page=2&per_page=10 | Correct page returned | |
| WPF-056 | Search | ?search=keyword | Filtered results | |

### 2.7 SEO & Schema

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-060 | DigitalDocument Schema | View page source | Schema.org markup present | |
| WPF-061 | CollectionPage Schema | View archive source | Collection schema present | |
| WPF-062 | BreadcrumbList | View page source | Breadcrumb schema present | |
| WPF-063 | Yoast Integration | Check with Yoast active | PDF documents in Yoast sitemap | |
| WPF-064 | Open Graph Tags | View page source | OG meta tags present | |
| WPF-065 | Twitter Cards | View page source | Twitter card meta present | |

---

## 3. WordPress Pro Module Tests

### 3.1 License Validation

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-001 | Valid License | Enter valid key | License activated | |
| WPP-002 | Invalid License | Enter invalid key | Error message shown | |
| WPP-003 | Expired License | Test with expired key | Grace period warning | |
| WPP-004 | License Page | Visit License settings | Page renders correctly | |

### 3.2 Analytics Dashboard

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-010 | Dashboard Load | Visit Analytics page | Dashboard displays | |
| WPP-011 | View Tracking | View several PDFs | Views recorded | |
| WPP-012 | Time Periods | Filter by 7/30/90 days | Data filters correctly | |
| WPP-013 | Export CSV | Click export | CSV downloads | |
| WPP-014 | Export JSON | Click export JSON | JSON downloads | |
| WPP-015 | Popular Documents | Check top docs list | Sorted by views | |

### 3.3 Password Protection

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-020 | Enable Password | Set password on PDF | Password required to view | |
| WPP-021 | Correct Password | Enter correct password | PDF unlocks | |
| WPP-022 | Wrong Password | Enter wrong password | Error shown, locked | |
| WPP-023 | Session Persistence | Refresh after unlock | Stays unlocked | |
| WPP-024 | Rate Limiting | 5+ failed attempts | Account locked 15min | |

### 3.4 Reading Progress

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-030 | Save Progress | Read to page 5, close | Progress saved | |
| WPP-031 | Resume Reading | Return to PDF | Prompts to resume | |
| WPP-032 | Progress Bar | Enable progress bar | Bar shows completion % | |
| WPP-033 | Progress API | GET /documents/{id}/progress | Returns saved progress | |

### 3.5 Taxonomies

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-040 | Add Category | Create PDF category | Category created | |
| WPP-041 | Add Tags | Add tags to PDF | Tags assigned | |
| WPP-042 | Filter by Category | View category archive | Filtered results | |
| WPP-043 | Filter by Tag | View tag archive | Filtered results | |

### 3.6 XML Sitemap

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-050 | Sitemap URL | Visit /pdf/sitemap.xml | Sitemap displays | |
| WPP-051 | Sitemap Entries | Check entries | All PDFs listed | |
| WPP-052 | XSL Stylesheet | View sitemap | Styled with XSL | |
| WPP-053 | Legacy Redirect | Visit /pdf-sitemap.xml | 301 to new URL | |
| WPP-054 | Yoast Integration | Check with Yoast | Redirects to Yoast sitemap | |

### 3.7 Pro REST API Extensions

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-060 | GET /analytics | API request (admin) | Analytics data returned | |
| WPP-061 | GET /analytics/documents | API request | Per-doc analytics | |
| WPP-062 | GET/POST /progress | Save/load progress | Progress persists | |
| WPP-063 | POST /verify-password | Verify password | Returns unlock token | |
| WPP-064 | POST /expiring-link | Create link (admin) | Expiring URL returned | |
| WPP-065 | GET /categories | API request | Categories list | |
| WPP-066 | GET /tags | API request | Tags list | |

---

## 4. WordPress Pro+ Enterprise Tests

### 4.1 Pro+ License

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPE-001 | Valid Pro+ Key | Enter PDF$PRO+#... format | Pro+ activated | |
| WPE-002 | Requires Pro | Deactivate Pro license | Pro+ disabled with notice | |
| WPE-003 | Grace Period | Expired Pro+ key | 14-day grace period | |

### 4.2 Advanced Analytics

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPE-010 | Heatmap Tracking | Enable heatmaps | Click positions recorded | |
| WPE-011 | Engagement Score | View PDF | Score calculated | |
| WPE-012 | Geographic Tracking | View from different IPs | Location data recorded | |
| WPE-013 | Device Analytics | View on different devices | Device types recorded | |
| WPE-014 | Scroll Depth | Scroll through PDF | Depth percentages tracked | |
| WPE-015 | Time on Page | View PDF for 2 min | Time recorded per page | |

### 4.3 Security Features

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPE-020 | 2FA Enable | Enable 2FA for admin | QR code displays | |
| WPE-021 | 2FA Login | Login with TOTP code | Access granted | |
| WPE-022 | IP Whitelist | Add IP to whitelist | Only whitelisted IPs access | |
| WPE-023 | Audit Log | Perform various actions | All actions logged | |
| WPE-024 | Brute Force | 5+ failed attempts | IP blocked | |
| WPE-025 | Log Retention | Set 90-day retention | Old logs purged | |

### 4.4 Webhooks

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPE-030 | Create Webhook | Add webhook URL | Webhook saved | |
| WPE-031 | View Event | View a PDF | Webhook fires | |
| WPE-032 | Download Event | Download PDF | Webhook fires | |
| WPE-033 | Signature Validation | Check X-PDF-Signature | HMAC validates | |
| WPE-034 | Retry Logic | Webhook fails | Retries with backoff | |
| WPE-035 | Delivery Log | Check webhook log | Deliveries recorded | |

### 4.5 Document Versioning

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPE-040 | Upload New Version | Replace PDF file | Version created | |
| WPE-041 | Version History | View version list | All versions shown | |
| WPE-042 | Restore Version | Restore old version | Old version becomes current | |
| WPE-043 | Version Limit | Exceed keep_versions | Oldest versions deleted | |
| WPE-044 | Changelog | Add changelog text | Changelog saved | |
| WPE-045 | Checksum | Upload file | MD5/SHA256 computed | |

### 4.6 Annotations

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPE-050 | Add Highlight | Select text, highlight | Highlight saved | |
| WPE-051 | Add Note | Click to add note | Sticky note appears | |
| WPE-052 | Freehand Draw | Draw on PDF | Drawing saved | |
| WPE-053 | Edit Annotation | Modify annotation | Changes saved | |
| WPE-054 | Delete Annotation | Remove annotation | Annotation deleted | |
| WPE-055 | User Permissions | Enable user annotations | Users can annotate | |

### 4.7 Compliance (GDPR/HIPAA)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPE-060 | GDPR Mode | Enable GDPR mode | IP anonymization active | |
| WPE-061 | Consent Banner | Enable consent | Banner displays | |
| WPE-062 | Data Export | Request user data | JSON export generated | |
| WPE-063 | Data Deletion | Request deletion | User data purged | |
| WPE-064 | Retention Policy | Set 365 days | Old data auto-deleted | |
| WPE-065 | HIPAA Mode | Enable HIPAA mode | Enhanced logging | |

### 4.8 White Label

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPE-070 | Custom Logo | Upload logo | Logo shows in viewer | |
| WPE-071 | Hide Powered By | Enable option | Branding removed | |
| WPE-072 | Custom CSS | Add custom styles | Styles applied | |
| WPE-073 | Custom Colors | Set brand colors | Colors applied | |
| WPE-074 | Custom Text | Set loading text | Text displays | |

---

## 5. Drupal Free Module Tests

### 5.1 Module Installation

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DF-001 | Enable Module | drush en pdf_embed_seo | Module enables | |
| DF-002 | Entity Creation | Check entity definitions | PdfDocument entity exists | |
| DF-003 | Routes | Check routing | All routes registered | |
| DF-004 | Permissions | Check permissions | All permissions defined | |

### 5.2 PDF Document Entity

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DF-010 | Create Document | Add PDF document | Entity saved | |
| DF-011 | Upload PDF | Attach PDF file | File stored | |
| DF-012 | View Document | Visit /pdf/{slug} | PDF renders | |
| DF-013 | Edit Document | Modify and save | Changes saved | |
| DF-014 | Delete Document | Delete entity | Entity removed | |

### 5.3 Block Plugin

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DF-020 | Place Block | Add PDF Viewer block | Block appears | |
| DF-021 | Select Document | Choose PDF in block config | PDF embeds | |
| DF-022 | Block Settings | Configure width/height | Viewer resizes | |

### 5.4 REST API

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DF-030 | GET /api/pdf-embed-seo/v1/documents | Request | Documents list | |
| DF-031 | GET /documents/{id} | Request | Document data | |
| DF-032 | CSRF Token | POST with token | Request succeeds | |
| DF-033 | Rate Limiting | Rapid requests | Rate limited | |

---

## 6. Drupal Pro Module Tests

### 6.1 Premium Submodule

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DPP-001 | Enable Premium | drush en pdf_embed_seo_premium | Module enables | |
| DPP-002 | License Page | Visit settings | License form displays | |
| DPP-003 | Valid License | Enter valid key | Premium active | |

### 6.2 Analytics

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DPP-010 | Dashboard | Visit analytics page | Dashboard loads | |
| DPP-011 | View Tracking | View PDFs | Views recorded | |
| DPP-012 | IP Anonymization | Check stored IPs | Last octet is 0 | |

### 6.3 Password Protection

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DPP-020 | Enable Password | Set password | Password required | |
| DPP-021 | Verify Password | Enter correct password | Access granted | |
| DPP-022 | Rate Limiting | 5+ failures | Blocked 15 min | |

### 6.4 Services

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DPP-030 | RateLimiter Service | Inject and test | Rate limiting works | |
| DPP-031 | AccessTokenStorage | Create/validate token | Token persists | |
| DPP-032 | ProgressTracker | Save/load progress | Progress works | |

---

## 7. Drupal Pro+ Enterprise Tests

### 7.1 Pro+ Module

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DPE-001 | Enable Pro+ | drush en pdf_embed_seo_pro_plus | Module enables | |
| DPE-002 | Requires Premium | Disable premium | Pro+ disabled | |

(Additional Pro+ tests mirror WordPress Pro+ section)

---

## 8. React/Next.js Free Package Tests

### 8.1 Package Installation

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RF-001 | npm install | npm install @pdf-embed-seo/react | Package installs | |
| RF-002 | Import Components | import { PdfViewer } | No errors | |
| RF-003 | TypeScript Types | Use with TS | Types work | |

### 8.2 PdfProvider

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RF-010 | Configure Provider | Wrap app with PdfProvider | Context available | |
| RF-011 | WordPress Backend | Set backendType: 'wordpress' | API connects | |
| RF-012 | Drupal Backend | Set backendType: 'drupal' | API connects | |

### 8.3 PdfViewer Component

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RF-020 | Render Viewer | <PdfViewer documentId={1} /> | PDF renders | |
| RF-021 | Custom Height | height="600px" | Viewer resizes | |
| RF-022 | Theme Prop | theme="dark" | Dark theme applies | |
| RF-023 | Permissions | allowDownload={false} | Download hidden | |

### 8.4 Hooks

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RF-030 | usePdf | usePdf(documentId) | Document data returned | |
| RF-031 | usePdfList | usePdfList({ perPage: 10 }) | Documents array | |
| RF-032 | usePdfViewer | usePdfViewer(id) | Viewer state | |

### 8.5 PdfArchive Component

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RF-040 | Render Archive | <PdfArchive /> | Archive displays | |
| RF-041 | Grid Mode | displayMode="grid" | Grid layout | |
| RF-042 | Pagination | showPagination={true} | Pagination works | |

### 8.6 PdfSeo Component

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RF-050 | Schema Output | <PdfSeo document={doc} /> | JSON-LD in head | |
| RF-051 | Meta Tags | Check head | OG/Twitter meta | |

---

## 9. React/Next.js Pro Package Tests

### 9.1 Pro Package

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RPP-001 | npm install | npm install @pdf-embed-seo/react-pro | Package installs | |
| RPP-002 | License Config | Configure license in provider | Pro features enabled | |

### 9.2 Pro Components

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RPP-010 | PdfPasswordModal | Render with protected PDF | Modal appears | |
| RPP-011 | PdfProgressBar | Render with viewer | Progress shows | |
| RPP-012 | PdfSearch | Render search | Search works | |
| RPP-013 | PdfBookmarks | Render bookmarks | Bookmarks navigate | |

### 9.3 Pro Hooks

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RPP-020 | useAnalytics | useAnalytics(id) | Tracking works | |
| RPP-021 | usePasswordProtection | usePasswordProtection(id) | Password flow | |
| RPP-022 | useReadingProgress | useReadingProgress(id) | Progress saves | |

---

## 10. React/Next.js Pro+ Package Tests

### 10.1 Pro+ Package

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RPE-001 | npm install | npm install @pdf-embed-seo/react-pro-plus | Package installs | |
| RPE-002 | License Config | Configure Pro+ license | Pro+ features enabled | |

### 10.2 Pro+ Components

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| RPE-010 | PdfAnnotations | Render annotations | Annotation tools appear | |
| RPE-011 | PdfVersions | Render version list | Versions display | |
| RPE-012 | PdfAdvancedAnalytics | Render analytics | Heatmaps/engagement show | |
| RPE-013 | PdfCompliance | Render consent | Consent banner appears | |

---

## 11. Cross-Platform Integration Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| XP-001 | React + WordPress | Configure React with WP API | Data flows correctly | |
| XP-002 | React + Drupal | Configure React with Drupal API | Data flows correctly | |
| XP-003 | API Compatibility | Compare WP/Drupal responses | Same structure | |
| XP-004 | Progress Sync | Save progress in React, view in WP | Progress matches | |

---

## 12. Performance Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| PERF-001 | Large PDF (100MB) | Load 100MB PDF | Loads within 10s | |
| PERF-002 | Many Pages (500+) | Load 500-page PDF | Navigation smooth | |
| PERF-003 | Concurrent Users | 100 simultaneous viewers | No crashes | |
| PERF-004 | API Response Time | Measure /documents | < 200ms | |
| PERF-005 | Memory Usage | Monitor memory | No leaks | |

---

## 13. Security Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| SEC-001 | XSS Prevention | Try XSS in inputs | Scripts blocked | |
| SEC-002 | SQL Injection | Try SQL in parameters | Query escaped | |
| SEC-003 | CSRF Protection | POST without token | Request rejected | |
| SEC-004 | Direct PDF Access | Try direct URL | Access denied | |
| SEC-005 | Permission Check | Access admin as subscriber | Access denied | |
| SEC-006 | Password Hash | Check stored passwords | Hashed with bcrypt | |

---

## 14. Accessibility Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| A11Y-001 | Keyboard Navigation | Tab through viewer | All controls reachable | |
| A11Y-002 | Screen Reader | Test with VoiceOver/NVDA | Content announced | |
| A11Y-003 | Color Contrast | Check with contrast tool | WCAG AA compliant | |
| A11Y-004 | Focus Indicators | Tab through | Visible focus rings | |
| A11Y-005 | ARIA Labels | Check elements | Proper ARIA labels | |

---

## Test Execution Log

| Date | Tester | Platform | Version | Tests Passed | Tests Failed | Notes |
|------|--------|----------|---------|--------------|--------------|-------|
| | | | | | | |

---

## Sign-Off

| Role | Name | Signature | Date |
|------|------|-----------|------|
| QA Lead | | | |
| Dev Lead | | | |
| Product Owner | | | |

# UAT/QA Test Plan: PDF Embed & SEO Optimize (WordPress Premium Version)

**Version:** 1.2.5
**Last Updated:** 2026-01-28
**Test Environment:** WordPress 6.4+, PHP 8.0+

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [All Free Version Tests](#all-free-version-tests)
3. [License Management](#3-license-management)
4. [PDF Categories & Tags](#4-pdf-categories--tags)
5. [Password Protection](#5-password-protection)
6. [Role Restrictions](#6-role-restrictions)
7. [Advanced Analytics](#7-advanced-analytics)
8. [Text Search in Viewer](#8-text-search-in-viewer)
9. [Bookmarks Panel](#9-bookmarks-panel)
10. [Reading Progress](#10-reading-progress)
11. [PDF Sitemap](#11-pdf-sitemap)
12. [Bulk Import](#12-bulk-import)
13. [Premium Admin Pages](#13-premium-admin-pages)
14. [Premium Settings](#14-premium-settings)
15. [Privacy & External Requests](#15-privacy--external-requests)

---

## Prerequisites

Before running premium tests:
1. Complete ALL tests from `UAT-TEST-PLAN-FREE.md`
2. Ensure premium add-on is installed and activated
3. Have a valid license key ready (or use "valid" for testing)

---

## All Free Version Tests

**IMPORTANT:** All tests from the Free version test plan must PASS before proceeding with premium tests.

| Section | Status |
|---------|--------|
| Installation Testing | [ ] Pass [ ] Fail |
| Activation Testing | [ ] Pass [ ] Fail |
| Custom Post Type Testing | [ ] Pass [ ] Fail |
| PDF Upload & Management | [ ] Pass [ ] Fail |
| PDF Viewer Frontend | [ ] Pass [ ] Fail |
| Archive Page Testing | [ ] Pass [ ] Fail |
| Shortcode Testing | [ ] Pass [ ] Fail |
| Gutenberg Block Testing | [ ] Pass [ ] Fail |
| Admin Settings Testing | [ ] Pass [ ] Fail |
| SEO Integration Testing | [ ] Pass [ ] Fail |
| Security Testing | [ ] Pass [ ] Fail |
| Accessibility Testing | [ ] Pass [ ] Fail |
| Performance Testing | [ ] Pass [ ] Fail |

---

## 3. License Management

### 3.1 License Page Access

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| LIC-001 | License Page Exists | 1. Go to PDF Documents > License | Page loads | [ ] Pass [ ] Fail |
| LIC-002 | License Form Display | 1. Check license page | Input field and save button | [ ] Pass [ ] Fail |
| LIC-003 | License Status Display | 1. Check license status box | Shows Active/Inactive | [ ] Pass [ ] Fail |

### 3.2 License Activation

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| LIC-004 | Enter Valid License | 1. Enter valid key<br>2. Save | Status shows "Active" | [ ] Pass [ ] Fail |
| LIC-005 | Enter Invalid License | 1. Enter invalid key<br>2. Save | Status shows "Inactive" | [ ] Pass [ ] Fail |
| LIC-006 | Empty License | 1. Clear license field<br>2. Save | Status shows "Inactive" | [ ] Pass [ ] Fail |
| LIC-007 | License Persists | 1. Enter key<br>2. Navigate away<br>3. Return | Key still saved | [ ] Pass [ ] Fail |

### 3.3 License Page UI

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| LIC-008 | Credit Link | 1. Scroll to bottom | "made with ♥ by Dross:Media" | [ ] Pass [ ] Fail |
| LIC-009 | Credit Link A11Y | 1. Inspect link | aria-label, title present | [ ] Pass [ ] Fail |
| LIC-010 | Password Field | 1. Check license input | Type="password" for security | [ ] Pass [ ] Fail |

---

## 4. PDF Categories & Tags

### 4.1 Taxonomy Registration

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| CAT-001 | Enable Categories | 1. Go to Premium Settings<br>2. Enable PDF Categories<br>3. Save | Categories enabled | [ ] Pass [ ] Fail |
| CAT-002 | Categories in Menu | 1. Enable categories<br>2. Check sidebar | "PDF Categories" submenu | [ ] Pass [ ] Fail |
| CAT-003 | Tags in Menu | 1. Enable categories<br>2. Check sidebar | "PDF Tags" submenu | [ ] Pass [ ] Fail |

### 4.2 Category Management

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| CAT-004 | Create Category | 1. Go to PDF Categories<br>2. Add new category | Category created | [ ] Pass [ ] Fail |
| CAT-005 | Edit Category | 1. Edit existing category<br>2. Save | Changes saved | [ ] Pass [ ] Fail |
| CAT-006 | Delete Category | 1. Delete category | Category removed | [ ] Pass [ ] Fail |
| CAT-007 | Assign to PDF | 1. Edit PDF<br>2. Select category<br>3. Save | Category assigned | [ ] Pass [ ] Fail |
| CAT-008 | Multiple Categories | 1. Assign 3 categories<br>2. Save | All categories saved | [ ] Pass [ ] Fail |

### 4.3 Category Archives

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| CAT-009 | Category Archive Page | 1. Visit /pdf-category/slug/ | PDFs in category listed | [ ] Pass [ ] Fail |
| CAT-010 | Tag Archive Page | 1. Visit /pdf-tag/slug/ | PDFs with tag listed | [ ] Pass [ ] Fail |
| CAT-011 | Empty Category | 1. Visit empty category | "No PDFs found" message | [ ] Pass [ ] Fail |

### 4.4 Disable Categories

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| CAT-012 | Disable Feature | 1. Disable PDF Categories<br>2. Save | Menu items hidden | [ ] Pass [ ] Fail |
| CAT-013 | Data Preserved | 1. Disable<br>2. Re-enable | Categories still exist | [ ] Pass [ ] Fail |

---

## 5. Password Protection

### 5.1 Enable Feature

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PWD-001 | Enable in Settings | 1. Go to Premium Settings<br>2. Enable Password Protection<br>3. Save | Feature enabled | [ ] Pass [ ] Fail |
| PWD-002 | Meta Box Appears | 1. Edit PDF<br>2. Check sidebar | Password field visible | [ ] Pass [ ] Fail |

### 5.2 Set Password

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PWD-003 | Set Password | 1. Enter password<br>2. Save PDF | Password saved | [ ] Pass [ ] Fail |
| PWD-004 | Clear Password | 1. Clear password field<br>2. Save | Password removed | [ ] Pass [ ] Fail |
| PWD-005 | Password Hidden | 1. Check password field | Type="password" | [ ] Pass [ ] Fail |

### 5.3 Frontend Protection

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PWD-006 | Password Form Shows | 1. Visit protected PDF | Password form displayed | [ ] Pass [ ] Fail |
| PWD-007 | Correct Password | 1. Enter correct password<br>2. Submit | PDF displays | [ ] Pass [ ] Fail |
| PWD-008 | Wrong Password | 1. Enter wrong password<br>2. Submit | Error message shown | [ ] Pass [ ] Fail |
| PWD-009 | Empty Password | 1. Submit empty form | Validation error | [ ] Pass [ ] Fail |
| PWD-010 | Session Remember | 1. Enter password<br>2. Refresh page | Still unlocked | [ ] Pass [ ] Fail |

### 5.4 Admin Bypass

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PWD-011 | Admin Sees PDF | 1. Log in as admin<br>2. Visit protected PDF | PDF shows (no password) | [ ] Pass [ ] Fail |
| PWD-012 | Logged Out User | 1. Log out<br>2. Visit protected PDF | Password required | [ ] Pass [ ] Fail |

---

## 6. Role Restrictions

### 6.1 Enable Feature

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ROL-001 | Enable in Settings | 1. Go to Premium Settings<br>2. Enable Role Restrictions<br>3. Save | Feature enabled | [ ] Pass [ ] Fail |
| ROL-002 | Meta Box Appears | 1. Edit PDF | Role restriction options visible | [ ] Pass [ ] Fail |

### 6.2 Configure Restrictions

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ROL-003 | Restrict to Admin | 1. Select Administrator only<br>2. Save | Restriction saved | [ ] Pass [ ] Fail |
| ROL-004 | Multiple Roles | 1. Select multiple roles<br>2. Save | All roles saved | [ ] Pass [ ] Fail |
| ROL-005 | Logged-In Users | 1. Select "Logged-in users"<br>2. Save | Restriction saved | [ ] Pass [ ] Fail |

### 6.3 Frontend Enforcement

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ROL-006 | Allowed Role Access | 1. Login as allowed role<br>2. Visit PDF | PDF displays | [ ] Pass [ ] Fail |
| ROL-007 | Denied Role Access | 1. Login as denied role<br>2. Visit PDF | Access denied message | [ ] Pass [ ] Fail |
| ROL-008 | Anonymous Access | 1. Log out<br>2. Visit restricted PDF | Access denied message | [ ] Pass [ ] Fail |
| ROL-009 | Login Link | 1. View denied message | Login link provided | [ ] Pass [ ] Fail |

---

## 7. Advanced Analytics

### 7.1 Enable Feature

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ANA-001 | Enable in Settings | 1. Go to Premium Settings<br>2. Enable Advanced Analytics<br>3. Save | Feature enabled | [ ] Pass [ ] Fail |
| ANA-002 | Analytics Menu | 1. Check admin menu | "Analytics" submenu appears | [ ] Pass [ ] Fail |

### 7.2 Data Collection

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ANA-003 | Track Page View | 1. View PDF on frontend<br>2. Check analytics | View recorded | [ ] Pass [ ] Fail |
| ANA-004 | Track Download | 1. Download PDF<br>2. Check analytics | Download recorded | [ ] Pass [ ] Fail |
| ANA-005 | Track Print | 1. Print PDF<br>2. Check analytics | Print recorded | [ ] Pass [ ] Fail |
| ANA-006 | Page Time Tracking | 1. View PDF for 30 sec<br>2. Check analytics | Time recorded | [ ] Pass [ ] Fail |

### 7.3 Analytics Dashboard

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ANA-007 | Dashboard Loads | 1. Go to PDF Documents > Analytics | Page loads | [ ] Pass [ ] Fail |
| ANA-008 | View Count Display | 1. Check total views | Count shown | [ ] Pass [ ] Fail |
| ANA-009 | Top PDFs List | 1. Check popular PDFs | List displayed | [ ] Pass [ ] Fail |
| ANA-010 | Date Range Filter | 1. Select date range<br>2. Apply | Data filtered | [ ] Pass [ ] Fail |
| ANA-011 | Export Data | 1. Click export<br>2. Download | CSV downloads | [ ] Pass [ ] Fail |
| ANA-012 | Credit Link | 1. Scroll to bottom | Dross:Media credit visible | [ ] Pass [ ] Fail |

### 7.4 Per-PDF Analytics

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ANA-013 | Individual Stats | 1. Edit PDF<br>2. Check analytics meta box | Stats displayed | [ ] Pass [ ] Fail |
| ANA-014 | Page Views Graph | 1. Check graph | Views over time shown | [ ] Pass [ ] Fail |

---

## 8. Text Search in Viewer

### 8.1 Enable Feature

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SRC-001 | Enable in Settings | 1. Go to Premium Settings<br>2. Enable Text Search<br>3. Save | Feature enabled | [ ] Pass [ ] Fail |
| SRC-002 | Search Button Appears | 1. View PDF on frontend | Search icon visible | [ ] Pass [ ] Fail |

### 8.2 Search Functionality

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SRC-003 | Open Search Panel | 1. Click search icon | Search panel opens | [ ] Pass [ ] Fail |
| SRC-004 | Basic Search | 1. Enter search term<br>2. Press Enter | Results highlighted | [ ] Pass [ ] Fail |
| SRC-005 | Case Insensitive | 1. Search "TEST"<br>2. Check matches | Finds "test", "Test", etc. | [ ] Pass [ ] Fail |
| SRC-006 | Navigate Results | 1. Search term<br>2. Click next/prev | Navigates between matches | [ ] Pass [ ] Fail |
| SRC-007 | Result Count | 1. Search term | Shows "X of Y matches" | [ ] Pass [ ] Fail |
| SRC-008 | No Results | 1. Search non-existent term | "No matches found" | [ ] Pass [ ] Fail |
| SRC-009 | Clear Search | 1. Click clear/close | Highlights removed | [ ] Pass [ ] Fail |
| SRC-010 | Keyboard Shortcut | 1. Press Ctrl+F | Search panel opens | [ ] Pass [ ] Fail |

---

## 9. Bookmarks Panel

### 9.1 Enable Feature

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| BMK-001 | Enable in Settings | 1. Go to Premium Settings<br>2. Enable Bookmarks Panel<br>3. Save | Feature enabled | [ ] Pass [ ] Fail |
| BMK-002 | Bookmark Icon | 1. View PDF with bookmarks | Bookmark icon visible | [ ] Pass [ ] Fail |

### 9.2 Bookmark Navigation

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| BMK-003 | Open Panel | 1. Click bookmark icon | Panel slides open | [ ] Pass [ ] Fail |
| BMK-004 | Bookmarks Listed | 1. Open panel | PDF bookmarks shown | [ ] Pass [ ] Fail |
| BMK-005 | Click Bookmark | 1. Click bookmark item | Navigates to section | [ ] Pass [ ] Fail |
| BMK-006 | Nested Bookmarks | 1. Check hierarchical PDF | Nested structure shown | [ ] Pass [ ] Fail |
| BMK-007 | Close Panel | 1. Click close<br>2. Click outside | Panel closes | [ ] Pass [ ] Fail |
| BMK-008 | No Bookmarks | 1. View PDF without bookmarks | "No bookmarks" message | [ ] Pass [ ] Fail |

---

## 10. Reading Progress

### 10.1 Enable Feature

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRG-001 | Enable in Settings | 1. Go to Premium Settings<br>2. Enable Reading Progress<br>3. Save | Feature enabled | [ ] Pass [ ] Fail |

### 10.2 Progress Tracking

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRG-002 | Progress Bar Shows | 1. View PDF | Progress bar visible | [ ] Pass [ ] Fail |
| PRG-003 | Progress Updates | 1. Scroll through PDF | Bar updates | [ ] Pass [ ] Fail |
| PRG-004 | Progress Saved | 1. Scroll to page 5<br>2. Leave page<br>3. Return | Resumes at page 5 | [ ] Pass [ ] Fail |
| PRG-005 | Different PDFs | 1. Read PDF A to page 3<br>2. Read PDF B to page 7<br>3. Return to each | Progress separate | [ ] Pass [ ] Fail |
| PRG-006 | Reset Progress | 1. Go to page 1<br>2. Stay | Progress resets | [ ] Pass [ ] Fail |

### 10.3 Storage

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRG-007 | LocalStorage Used | 1. Check browser storage | Progress in localStorage | [ ] Pass [ ] Fail |
| PRG-008 | Clear Storage | 1. Clear browser data<br>2. Visit PDF | Progress reset | [ ] Pass [ ] Fail |

---

## 11. PDF Sitemap

### 11.1 Enable Feature

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SMP-001 | Enable in Settings | 1. Go to Premium Settings<br>2. Enable PDF Sitemap<br>3. Save | Feature enabled | [ ] Pass [ ] Fail |

### 11.2 Sitemap Generation

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SMP-002 | Sitemap Accessible | 1. Visit /pdf/sitemap.xml | XML sitemap displays (or redirects to Yoast) | [ ] Pass [ ] Fail |
| SMP-002a | Legacy URL Redirect | 1. Visit /pdf-sitemap.xml | 301 redirect to /pdf/sitemap.xml | [ ] Pass [ ] Fail |
| SMP-003 | PDFs Listed | 1. Check sitemap content | All published PDFs included | [ ] Pass [ ] Fail |
| SMP-004 | Correct URLs | 1. Check <loc> tags | Clean URLs (/pdf/slug/) | [ ] Pass [ ] Fail |
| SMP-005 | Last Modified | 1. Check <lastmod> tags | Dates present | [ ] Pass [ ] Fail |
| SMP-006 | Priority Values | 1. Check <priority> tags | Valid values (0.1-1.0) | [ ] Pass [ ] Fail |
| SMP-007 | Change Frequency | 1. Check <changefreq> tags | Valid values | [ ] Pass [ ] Fail |

### 11.3 Robots.txt

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SMP-008 | Sitemap in Robots | 1. Visit /robots.txt | PDF sitemap referenced | [ ] Pass [ ] Fail |

### 11.4 Search Engine Ping

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SMP-009 | Ping Disabled Default | 1. Check default settings | Ping disabled | [ ] Pass [ ] Fail |
| SMP-010 | Enable Ping | 1. Enable Ping Search Engines<br>2. Save<br>3. Publish PDF | Ping sent (check logs) | [ ] Pass [ ] Fail |
| SMP-011 | Rate Limiting | 1. Publish 3 PDFs quickly | Only one ping/hour | [ ] Pass [ ] Fail |

### 11.5 SEO Plugin Integration

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SMP-012 | Yoast Index | 1. Install Yoast<br>2. Check sitemap index | PDF sitemap included | [ ] Pass [ ] Fail |
| SMP-013 | WP Core Sitemap | 1. Check /wp-sitemap.xml | PDF documents included | [ ] Pass [ ] Fail |
| SMP-014 | Yoast Redirect | 1. Install Yoast SEO<br>2. Visit /pdf/sitemap.xml | 302 redirect to /pdf_document-sitemap.xml | [ ] Pass [ ] Fail |
| SMP-015 | Yoast Disabled Fallback | 1. Disable Yoast SEO<br>2. Visit /pdf/sitemap.xml | Custom sitemap renders directly | [ ] Pass [ ] Fail |
| SMP-016 | Yoast PDF Noindex | 1. Install Yoast<br>2. Set pdf_document to noindex<br>3. Visit /pdf/sitemap.xml | Custom sitemap renders (no redirect) | [ ] Pass [ ] Fail |

---

## 12. Bulk Import

### 12.1 Access

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| BLK-001 | Menu Access | 1. Check admin menu | "Bulk Import" submenu | [ ] Pass [ ] Fail |
| BLK-002 | Page Loads | 1. Go to Bulk Import | Page loads without error | [ ] Pass [ ] Fail |

### 12.2 Import Process

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| BLK-003 | Select PDFs | 1. Open Media Library<br>2. Select multiple PDFs | PDFs selected | [ ] Pass [ ] Fail |
| BLK-004 | Import PDFs | 1. Select PDFs<br>2. Click Import | Documents created | [ ] Pass [ ] Fail |
| BLK-005 | Title Generation | 1. Import PDF<br>2. Check title | Uses filename as title | [ ] Pass [ ] Fail |
| BLK-006 | Skip Duplicates | 1. Import same PDF twice | Duplicate skipped | [ ] Pass [ ] Fail |
| BLK-007 | Progress Display | 1. Import 10 PDFs | Progress shown | [ ] Pass [ ] Fail |
| BLK-008 | Success Message | 1. Complete import | Success count shown | [ ] Pass [ ] Fail |

### 12.3 Bulk Import UI

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| BLK-009 | Credit Link | 1. Scroll to bottom | Dross:Media credit visible | [ ] Pass [ ] Fail |
| BLK-010 | Credit A11Y | 1. Inspect credit link | aria-label, title present | [ ] Pass [ ] Fail |

---

## 13. Premium Admin Pages

### 13.1 Premium Settings Tab

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PAD-001 | Premium Tab Exists | 1. Go to Settings<br>2. Check tabs | "Premium" tab visible | [ ] Pass [ ] Fail |
| PAD-002 | Toggle All Features | 1. Check all premium toggles | All toggle correctly | [ ] Pass [ ] Fail |
| PAD-003 | Settings Persist | 1. Change settings<br>2. Save<br>3. Refresh | Settings persisted | [ ] Pass [ ] Fail |

### 13.2 Premium Badge

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PAD-004 | Badge Display | 1. Go to PDF Documents | "PREMIUM" badge visible | [ ] Pass [ ] Fail |
| PAD-005 | Badge Styling | 1. Inspect badge | Gradient purple styling | [ ] Pass [ ] Fail |

### 13.3 Plugin Row

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PAD-006 | License Link | 1. Go to Plugins page | "License" link present | [ ] Pass [ ] Fail |

---

## 14. Premium Settings

### 14.1 Feature Toggles

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PST-001 | Enable Categories | Toggle, save, verify | Feature enabled | [ ] Pass [ ] Fail |
| PST-002 | Enable Password | Toggle, save, verify | Feature enabled | [ ] Pass [ ] Fail |
| PST-003 | Enable Roles | Toggle, save, verify | Feature enabled | [ ] Pass [ ] Fail |
| PST-004 | Enable Analytics | Toggle, save, verify | Feature enabled | [ ] Pass [ ] Fail |
| PST-005 | Enable Search | Toggle, save, verify | Feature enabled | [ ] Pass [ ] Fail |
| PST-006 | Enable Bookmarks | Toggle, save, verify | Feature enabled | [ ] Pass [ ] Fail |
| PST-007 | Enable Progress | Toggle, save, verify | Feature enabled | [ ] Pass [ ] Fail |
| PST-008 | Enable Sitemap | Toggle, save, verify | Feature enabled | [ ] Pass [ ] Fail |
| PST-009 | Enable Sitemap Ping | Toggle, save, verify | Feature enabled | [ ] Pass [ ] Fail |

### 14.2 Archive Page Redirect

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PST-010 | Redirect Settings Visible | 1. Go to Premium Settings<br>2. Check Archive Redirect section | Section visible | [ ] Pass [ ] Fail |
| PST-011 | Enable Redirect Toggle | 1. Enable Archive Redirect<br>2. Save | Setting saved | [ ] Pass [ ] Fail |
| PST-012 | Redirect Type 301 | 1. Set redirect type to 301<br>2. Set URL<br>3. Visit /pdf/<br>4. Check HTTP code | 301 redirect | [ ] Pass [ ] Fail |
| PST-013 | Redirect Type 302 | 1. Set redirect type to 302<br>2. Visit /pdf/<br>3. Check HTTP code | 302 redirect | [ ] Pass [ ] Fail |
| PST-014 | Redirect URL | 1. Set custom redirect URL<br>2. Visit /pdf/ | Redirects to custom URL | [ ] Pass [ ] Fail |
| PST-015 | Redirect Disabled | 1. Disable redirect<br>2. Visit /pdf/ | Archive page displays normally | [ ] Pass [ ] Fail |
| PST-016 | License Required | 1. Invalidate license<br>2. Enable redirect<br>3. Visit /pdf/ | Redirect disabled (free fallback) | [ ] Pass [ ] Fail |
| PST-017 | URL Validation | 1. Enter invalid URL<br>2. Save | Error shown | [ ] Pass [ ] Fail |

### 14.2 Default States

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PST-010 | Default All Enabled | 1. Fresh install<br>2. Check defaults | All features ON by default | [ ] Pass [ ] Fail |
| PST-011 | Sitemap Ping Default | 1. Fresh install<br>2. Check ping setting | Ping OFF by default | [ ] Pass [ ] Fail |

---

## 15. Download Tracking (v1.2.5)

### 15.1 Download Counter

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| DWN-001 | Download Count Display | 1. Edit PDF in admin<br>2. Check meta box | Download count visible | [ ] Pass [ ] Fail |
| DWN-002 | Download Tracking | 1. Download a PDF<br>2. Check admin | Download count increments | [ ] Pass [ ] Fail |
| DWN-003 | Separate from Views | 1. View PDF<br>2. Download PDF<br>3. Check counts | View and download counts separate | [ ] Pass [ ] Fail |
| DWN-004 | Download Analytics | 1. View Analytics dashboard | Download stats shown | [ ] Pass [ ] Fail |

### 15.2 Download REST API

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| DWN-005 | POST /download | 1. POST to /documents/{id}/download | Returns success, count increments | [ ] Pass [ ] Fail |
| DWN-006 | Download Response | 1. Check response data | Includes download_count, timestamp | [ ] Pass [ ] Fail |

---

## 16. Expiring Access Links (v1.2.5)

### 16.1 Generate Links

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| EXP-001 | Generate Link Button | 1. Edit PDF<br>2. Check Expiring Links section | Generate button visible | [ ] Pass [ ] Fail |
| EXP-002 | Generate Link | 1. Click Generate<br>2. Check response | Token URL returned | [ ] Pass [ ] Fail |
| EXP-003 | Custom Expiration | 1. Set expiration to 1 hour<br>2. Generate link | Link expires in 1 hour | [ ] Pass [ ] Fail |
| EXP-004 | Max Uses Setting | 1. Set max uses to 5<br>2. Generate link | Link limited to 5 uses | [ ] Pass [ ] Fail |
| EXP-005 | Admin Only | 1. Try as non-admin | Access denied | [ ] Pass [ ] Fail |

### 16.2 Validate Links

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| EXP-006 | Valid Link Access | 1. Use valid expiring link | PDF accessible | [ ] Pass [ ] Fail |
| EXP-007 | Expired Link | 1. Wait for expiration<br>2. Try link | Access denied, shows expired | [ ] Pass [ ] Fail |
| EXP-008 | Max Uses Exceeded | 1. Use link max times<br>2. Try again | Access denied, shows limit reached | [ ] Pass [ ] Fail |
| EXP-009 | Invalid Token | 1. Use fake token | Access denied, invalid token | [ ] Pass [ ] Fail |
| EXP-010 | Usage Counter | 1. Use link<br>2. Check admin | Usage count increments | [ ] Pass [ ] Fail |

### 16.3 Expiring Links REST API

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| EXP-011 | POST /expiring-link | 1. POST as admin | Returns token and URL | [ ] Pass [ ] Fail |
| EXP-012 | GET /expiring-link/{token} | 1. GET with valid token | Returns PDF data | [ ] Pass [ ] Fail |
| EXP-013 | Unauthorized Generate | 1. POST as non-admin | 403 Forbidden | [ ] Pass [ ] Fail |

---

## 17. GEO/AEO/LLM Schema Optimization (v1.2.5)

### 17.1 AI Summary & Key Points

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| GEO-001 | AI Summary Field | 1. Edit PDF<br>2. Check AI Optimization section | TL;DR field visible | [ ] Pass [ ] Fail |
| GEO-002 | Key Points Field | 1. Check AI Optimization | Key Takeaways field visible | [ ] Pass [ ] Fail |
| GEO-003 | Summary Schema | 1. Add summary<br>2. View source | abstract property in schema | [ ] Pass [ ] Fail |

### 17.2 FAQ Schema

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| GEO-004 | FAQ Fields | 1. Edit PDF<br>2. Add FAQ items | FAQ Q&A fields work | [ ] Pass [ ] Fail |
| GEO-005 | FAQPage Schema | 1. Add FAQs<br>2. View source | FAQPage schema present | [ ] Pass [ ] Fail |

### 17.3 Reading Metadata

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| GEO-006 | Reading Time | 1. Set reading time<br>2. View source | timeRequired in schema | [ ] Pass [ ] Fail |
| GEO-007 | Difficulty Level | 1. Set difficulty<br>2. View source | educationalLevel in schema | [ ] Pass [ ] Fail |
| GEO-008 | Target Audience | 1. Set audience<br>2. View source | audience in schema | [ ] Pass [ ] Fail |

---

## 18. Privacy & External Requests

### 15.1 No Unwanted External Calls

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRV-001 | Admin Pages | 1. Open Network tab<br>2. Navigate admin pages | No external requests | [ ] Pass [ ] Fail |
| PRV-002 | Frontend Viewer | 1. Open Network tab<br>2. View PDF | No external requests | [ ] Pass [ ] Fail |
| PRV-003 | PDF.js Local | 1. Check script sources | PDF.js from local domain | [ ] Pass [ ] Fail |
| PRV-004 | Styles Local | 1. Check stylesheet sources | All CSS from local domain | [ ] Pass [ ] Fail |

### 15.2 Opt-In External Calls

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRV-005 | Ping Disabled | 1. Disable sitemap ping<br>2. Publish PDF<br>3. Check network | No Google/Bing requests | [ ] Pass [ ] Fail |
| PRV-006 | Ping Enabled | 1. Enable sitemap ping<br>2. Publish PDF<br>3. Check logs | Ping sent to Google/Bing | [ ] Pass [ ] Fail |
| PRV-007 | Ping Notification | 1. Check ping setting | "sends request to external servers" note | [ ] Pass [ ] Fail |

---

## Test Sign-Off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| QA Tester | | | |
| Developer | | | |
| Product Owner | | | |

---

## Defect Log

| Defect ID | Test ID | Description | Severity | Status |
|-----------|---------|-------------|----------|--------|
| | | | | |

---

## Notes

- Complete FREE version tests first
- Test feature combinations (password + role restriction)
- Test with various PDF types (bookmarked, large, image-heavy)
- Verify database cleanup when features disabled
- Test multisite compatibility if applicable

# UAT/QA Test Plan: PDF Embed & SEO Optimize (WordPress Free Version)

**Version:** 1.1.0
**Last Updated:** 2026-01-27
**Test Environment:** WordPress 6.4+, PHP 8.0+

---

## Table of Contents

1. [Installation Testing](#1-installation-testing)
2. [Activation Testing](#2-activation-testing)
3. [Custom Post Type Testing](#3-custom-post-type-testing)
4. [PDF Upload & Management](#4-pdf-upload--management)
5. [PDF Viewer Frontend](#5-pdf-viewer-frontend)
6. [Archive Page Testing](#6-archive-page-testing)
7. [Shortcode Testing](#7-shortcode-testing)
8. [Gutenberg Block Testing](#8-gutenberg-block-testing)
9. [Admin Settings Testing](#9-admin-settings-testing)
10. [SEO Integration Testing](#10-seo-integration-testing)
11. [Security Testing](#11-security-testing)
12. [Accessibility Testing](#12-accessibility-testing)
13. [Performance Testing](#13-performance-testing)
14. [Uninstall Testing](#14-uninstall-testing)

---

## 1. Installation Testing

### 1.1 Fresh Installation

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| INS-001 | Upload via Admin | 1. Go to Plugins > Add New > Upload Plugin<br>2. Select plugin ZIP<br>3. Click Install Now | Plugin installs without errors | [ ] Pass [ ] Fail |
| INS-002 | FTP Installation | 1. Upload plugin folder to /wp-content/plugins/<br>2. Go to Plugins page | Plugin appears in list | [ ] Pass [ ] Fail |
| INS-003 | WordPress.org Install | 1. Search "PDF Embed SEO Optimize"<br>2. Click Install | Plugin installs from repository | [ ] Pass [ ] Fail |
| INS-004 | PHP Version Check | 1. Test on PHP 7.4<br>2. Test on PHP 8.0+<br>3. Test on PHP 8.2+ | Works on all supported versions | [ ] Pass [ ] Fail |
| INS-005 | WordPress Version Check | 1. Test on WP 5.8<br>2. Test on WP 6.0+<br>3. Test on WP 6.4+ | Works on all supported versions | [ ] Pass [ ] Fail |

### 1.2 Upgrade Installation

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| INS-006 | Version Upgrade | 1. Install older version<br>2. Upload new version<br>3. Confirm upgrade | Upgrades without data loss | [ ] Pass [ ] Fail |
| INS-007 | Database Migration | 1. Upgrade plugin<br>2. Check existing PDFs | All PDFs and settings preserved | [ ] Pass [ ] Fail |

---

## 2. Activation Testing

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ACT-001 | Plugin Activation | 1. Go to Plugins page<br>2. Click Activate | Plugin activates, no errors | [ ] Pass [ ] Fail |
| ACT-002 | Rewrite Rules Flushed | 1. Activate plugin<br>2. Check permalink structure | /pdf/ URLs work immediately | [ ] Pass [ ] Fail |
| ACT-003 | Default Options Created | 1. Activate plugin<br>2. Check wp_options table | Default settings created | [ ] Pass [ ] Fail |
| ACT-004 | Admin Menu Appears | 1. Activate plugin<br>2. Check admin sidebar | "PDF Documents" menu visible | [ ] Pass [ ] Fail |
| ACT-005 | No PHP Errors | 1. Enable WP_DEBUG<br>2. Activate plugin | No warnings/errors in log | [ ] Pass [ ] Fail |

---

## 3. Custom Post Type Testing

### 3.1 Post Type Registration

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| CPT-001 | Post Type Exists | 1. Check admin menu<br>2. Visit edit.php?post_type=pdf_document | Post type accessible | [ ] Pass [ ] Fail |
| CPT-002 | Supports Title | 1. Add new PDF Document<br>2. Check title field | Title field available | [ ] Pass [ ] Fail |
| CPT-003 | Supports Editor | 1. Add new PDF Document<br>2. Check content editor | Editor available | [ ] Pass [ ] Fail |
| CPT-004 | Supports Thumbnail | 1. Add new PDF Document<br>2. Check featured image | Featured image meta box present | [ ] Pass [ ] Fail |
| CPT-005 | Supports Excerpt | 1. Add new PDF Document<br>2. Check excerpt field | Excerpt field available | [ ] Pass [ ] Fail |

### 3.2 PDF Document Creation

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| CPT-006 | Create New PDF | 1. Click Add New<br>2. Enter title<br>3. Upload PDF<br>4. Publish | PDF document created | [ ] Pass [ ] Fail |
| CPT-007 | Edit PDF | 1. Open existing PDF<br>2. Change title<br>3. Update | Changes saved | [ ] Pass [ ] Fail |
| CPT-008 | Delete PDF | 1. Select PDF<br>2. Move to Trash | PDF moved to trash | [ ] Pass [ ] Fail |
| CPT-009 | Restore PDF | 1. Go to Trash<br>2. Restore PDF | PDF restored | [ ] Pass [ ] Fail |
| CPT-010 | Permanently Delete | 1. Go to Trash<br>2. Delete permanently | PDF and meta removed | [ ] Pass [ ] Fail |

---

## 4. PDF Upload & Management

### 4.1 File Upload

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| UPL-001 | Upload Valid PDF | 1. Click Select PDF<br>2. Upload .pdf file<br>3. Select | PDF attached to document | [ ] Pass [ ] Fail |
| UPL-002 | Upload Large PDF (>10MB) | 1. Upload large PDF<br>2. Wait for upload | Handles large files | [ ] Pass [ ] Fail |
| UPL-003 | Upload Non-PDF | 1. Try uploading .doc file | Rejected with error message | [ ] Pass [ ] Fail |
| UPL-004 | Select from Library | 1. Open Media Library<br>2. Select existing PDF | PDF attached | [ ] Pass [ ] Fail |
| UPL-005 | Replace PDF | 1. Open existing document<br>2. Select different PDF | PDF replaced | [ ] Pass [ ] Fail |

### 4.2 Meta Box Settings

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| UPL-006 | Allow Download Toggle | 1. Enable download<br>2. Save<br>3. Check frontend | Download button appears/hidden | [ ] Pass [ ] Fail |
| UPL-007 | Allow Print Toggle | 1. Enable print<br>2. Save<br>3. Check frontend | Print button appears/hidden | [ ] Pass [ ] Fail |
| UPL-008 | View Count Display | 1. View PDF on frontend<br>2. Check admin | View count incremented | [ ] Pass [ ] Fail |
| UPL-009 | PDF Preview | 1. Click preview in admin | PDF preview shows | [ ] Pass [ ] Fail |

---

## 5. PDF Viewer Frontend

### 5.1 Viewer Display

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| VWR-001 | PDF Loads | 1. Visit PDF URL<br>2. Wait for load | PDF renders correctly | [ ] Pass [ ] Fail |
| VWR-002 | Page Navigation | 1. Load multi-page PDF<br>2. Click next/prev | Pages navigate | [ ] Pass [ ] Fail |
| VWR-003 | Page Number Display | 1. Load PDF<br>2. Check page indicator | Shows "Page X of Y" | [ ] Pass [ ] Fail |
| VWR-004 | Go to Page | 1. Enter page number<br>2. Press Enter | Jumps to page | [ ] Pass [ ] Fail |
| VWR-005 | Zoom In/Out | 1. Click zoom controls | PDF zooms correctly | [ ] Pass [ ] Fail |
| VWR-006 | Fit to Width | 1. Click fit width | PDF fits container | [ ] Pass [ ] Fail |
| VWR-007 | Fit to Page | 1. Click fit page | Full page visible | [ ] Pass [ ] Fail |
| VWR-008 | Fullscreen Mode | 1. Click fullscreen<br>2. Press Escape | Enters/exits fullscreen | [ ] Pass [ ] Fail |

### 5.2 Viewer Controls

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| VWR-009 | Download Button (Enabled) | 1. Enable download<br>2. Click download | PDF downloads | [ ] Pass [ ] Fail |
| VWR-010 | Download Button (Disabled) | 1. Disable download<br>2. Check frontend | No download button | [ ] Pass [ ] Fail |
| VWR-011 | Print Button (Enabled) | 1. Enable print<br>2. Click print | Print dialog opens | [ ] Pass [ ] Fail |
| VWR-012 | Print Button (Disabled) | 1. Disable print<br>2. Check frontend | No print button | [ ] Pass [ ] Fail |
| VWR-013 | Context Menu Disabled | 1. Right-click on PDF | Context menu blocked | [ ] Pass [ ] Fail |

### 5.3 Responsive Design

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| VWR-014 | Desktop Display | 1. View on desktop (1920px) | Full controls visible | [ ] Pass [ ] Fail |
| VWR-015 | Tablet Display | 1. View on tablet (768px) | Controls adapt | [ ] Pass [ ] Fail |
| VWR-016 | Mobile Display | 1. View on mobile (375px) | Touch-friendly controls | [ ] Pass [ ] Fail |
| VWR-017 | Touch Gestures | 1. Pinch to zoom on mobile | Zoom works with touch | [ ] Pass [ ] Fail |

---

## 6. Archive Page Testing

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ARC-001 | Archive Page Loads | 1. Visit /pdf/ | Archive page displays | [ ] Pass [ ] Fail |
| ARC-002 | PDF List Display | 1. Create multiple PDFs<br>2. Visit archive | All PDFs listed | [ ] Pass [ ] Fail |
| ARC-003 | Thumbnails Display | 1. Add featured images<br>2. Check archive | Thumbnails visible | [ ] Pass [ ] Fail |
| ARC-004 | Pagination | 1. Create 20+ PDFs<br>2. Check pagination | Pagination works | [ ] Pass [ ] Fail |
| ARC-005 | Click to View | 1. Click PDF title | Navigates to viewer | [ ] Pass [ ] Fail |
| ARC-006 | Empty State | 1. Delete all PDFs<br>2. Visit archive | Shows "No PDFs found" | [ ] Pass [ ] Fail |

---

## 7. Shortcode Testing

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SHC-001 | Basic Shortcode | 1. Add [pdf_viewer id="123"]<br>2. View page | PDF embeds | [ ] Pass [ ] Fail |
| SHC-002 | Width Attribute | 1. Add width="500"<br>2. View page | Width applied | [ ] Pass [ ] Fail |
| SHC-003 | Height Attribute | 1. Add height="600"<br>2. View page | Height applied | [ ] Pass [ ] Fail |
| SHC-004 | Download Override | 1. Add download="false"<br>2. View page | Download disabled | [ ] Pass [ ] Fail |
| SHC-005 | Print Override | 1. Add print="false"<br>2. View page | Print disabled | [ ] Pass [ ] Fail |
| SHC-006 | Invalid ID | 1. Use non-existent ID<br>2. View page | Error message shown | [ ] Pass [ ] Fail |
| SHC-007 | Multiple Shortcodes | 1. Add 3 shortcodes<br>2. View page | All PDFs render | [ ] Pass [ ] Fail |

---

## 8. Gutenberg Block Testing

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| BLK-001 | Block Available | 1. Open block inserter<br>2. Search "PDF" | Block appears | [ ] Pass [ ] Fail |
| BLK-002 | Insert Block | 1. Insert PDF block<br>2. Select PDF | Block renders | [ ] Pass [ ] Fail |
| BLK-003 | Block Preview | 1. Insert block<br>2. Check editor | Preview visible | [ ] Pass [ ] Fail |
| BLK-004 | Block Settings | 1. Select block<br>2. Check sidebar | Settings panel shows | [ ] Pass [ ] Fail |
| BLK-005 | Save & View | 1. Publish post<br>2. View frontend | PDF displays correctly | [ ] Pass [ ] Fail |

---

## 9. Admin Settings Testing

### 9.1 Settings Page

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SET-001 | Settings Page Loads | 1. Go to PDF Documents > Settings | Page loads without error | [ ] Pass [ ] Fail |
| SET-002 | Default Download Setting | 1. Change default download<br>2. Save | Setting persists | [ ] Pass [ ] Fail |
| SET-003 | Default Print Setting | 1. Change default print<br>2. Save | Setting persists | [ ] Pass [ ] Fail |
| SET-004 | Theme Selection | 1. Select dark theme<br>2. Save<br>3. View PDF | Theme applied | [ ] Pass [ ] Fail |
| SET-005 | Credit Link Visible | 1. Scroll to bottom | "made with ♥ by Dross:Media" visible | [ ] Pass [ ] Fail |
| SET-006 | Credit Link Accessible | 1. Inspect credit link | aria-label and title present | [ ] Pass [ ] Fail |

### 9.2 Docs Page

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| DOC-001 | Docs Page Loads | 1. Go to PDF Documents > Documentation | Page loads | [ ] Pass [ ] Fail |
| DOC-002 | Shortcode Examples | 1. Check shortcode section | Examples displayed | [ ] Pass [ ] Fail |
| DOC-003 | Credit Link Visible | 1. Scroll to bottom | Credit link present | [ ] Pass [ ] Fail |

---

## 10. SEO Integration Testing

### 10.1 Basic SEO

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEO-001 | Clean URLs | 1. Create PDF "Test Doc"<br>2. Check URL | URL is /pdf/test-doc/ | [ ] Pass [ ] Fail |
| SEO-002 | Title Tag | 1. View PDF page source<br>2. Check <title> | Contains PDF title | [ ] Pass [ ] Fail |
| SEO-003 | Meta Description | 1. Add excerpt<br>2. View source | Meta description present | [ ] Pass [ ] Fail |
| SEO-004 | Canonical URL | 1. View source | Canonical tag present | [ ] Pass [ ] Fail |

### 10.2 Yoast SEO Integration

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEO-005 | Yoast Meta Box | 1. Install Yoast<br>2. Edit PDF | Yoast meta box visible | [ ] Pass [ ] Fail |
| SEO-006 | Custom Title | 1. Set Yoast title<br>2. View source | Custom title used | [ ] Pass [ ] Fail |
| SEO-007 | Custom Description | 1. Set Yoast description<br>2. View source | Custom description used | [ ] Pass [ ] Fail |
| SEO-008 | OpenGraph Tags | 1. View source | OG tags present | [ ] Pass [ ] Fail |
| SEO-009 | Twitter Cards | 1. View source | Twitter card tags present | [ ] Pass [ ] Fail |
| SEO-010 | Sitemap Inclusion | 1. View Yoast sitemap | PDF URLs included | [ ] Pass [ ] Fail |

### 10.3 Schema.org

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEO-011 | Schema Markup | 1. View source<br>2. Check JSON-LD | DigitalDocument schema | [ ] Pass [ ] Fail |
| SEO-012 | Schema Validation | 1. Use Google Rich Results Test | No errors | [ ] Pass [ ] Fail |

---

## 11. Security Testing

### 11.1 Access Control

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEC-001 | Admin-Only Settings | 1. Log in as Subscriber<br>2. Try settings URL | Access denied | [ ] Pass [ ] Fail |
| SEC-002 | Nonce Verification | 1. Submit form without nonce | Rejected | [ ] Pass [ ] Fail |
| SEC-003 | Capability Check | 1. Try editing PDF as Subscriber | Access denied | [ ] Pass [ ] Fail |

### 11.2 Data Sanitization

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEC-004 | XSS in Title | 1. Enter <script>alert(1)</script><br>2. Save | Script escaped | [ ] Pass [ ] Fail |
| SEC-005 | SQL Injection | 1. Enter ' OR 1=1 in search<br>2. Submit | Query safe | [ ] Pass [ ] Fail |
| SEC-006 | File Type Validation | 1. Try uploading PHP file | Rejected | [ ] Pass [ ] Fail |

### 11.3 Privacy

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEC-007 | No External Requests | 1. Monitor network<br>2. Load plugin pages | No external calls | [ ] Pass [ ] Fail |
| SEC-008 | PDF.js Self-Hosted | 1. Check script sources | PDF.js loaded locally | [ ] Pass [ ] Fail |

---

## 12. Accessibility Testing

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| A11Y-001 | Keyboard Navigation | 1. Tab through viewer<br>2. Use Enter/Space | All controls accessible | [ ] Pass [ ] Fail |
| A11Y-002 | Screen Reader | 1. Use NVDA/VoiceOver<br>2. Navigate viewer | Content announced | [ ] Pass [ ] Fail |
| A11Y-003 | ARIA Labels | 1. Inspect buttons | aria-label present | [ ] Pass [ ] Fail |
| A11Y-004 | Focus Indicators | 1. Tab through controls | Focus visible | [ ] Pass [ ] Fail |
| A11Y-005 | Color Contrast | 1. Use contrast checker | Meets WCAG AA | [ ] Pass [ ] Fail |
| A11Y-006 | Credit Link A11Y | 1. Inspect Dross:Media link | aria-label, title, screen-reader-text | [ ] Pass [ ] Fail |

---

## 13. Performance Testing

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRF-001 | Page Load Time | 1. Load PDF page<br>2. Check load time | Under 3 seconds | [ ] Pass [ ] Fail |
| PRF-002 | Scripts Loaded | 1. Check non-PDF pages | PDF.js NOT loaded | [ ] Pass [ ] Fail |
| PRF-003 | Memory Usage | 1. Load large PDF<br>2. Monitor memory | No memory leaks | [ ] Pass [ ] Fail |
| PRF-004 | Multiple PDFs | 1. Embed 5 PDFs<br>2. Check performance | Page remains responsive | [ ] Pass [ ] Fail |

---

## 14. Uninstall Testing

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| UNI-001 | Deactivate Plugin | 1. Deactivate plugin | PDFs still accessible in DB | [ ] Pass [ ] Fail |
| UNI-002 | Reactivate Plugin | 1. Reactivate plugin | All data preserved | [ ] Pass [ ] Fail |
| UNI-003 | Delete Plugin | 1. Delete plugin<br>2. Check database | Options removed | [ ] Pass [ ] Fail |
| UNI-004 | PDFs Preserved | 1. Delete plugin<br>2. Check posts | PDF posts remain (user choice) | [ ] Pass [ ] Fail |

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

- Test on clean WordPress installation
- Test with popular themes (Twenty Twenty-Four, Astra, GeneratePress)
- Test with common plugins (Yoast SEO, WooCommerce, Elementor)
- Document any browser-specific issues

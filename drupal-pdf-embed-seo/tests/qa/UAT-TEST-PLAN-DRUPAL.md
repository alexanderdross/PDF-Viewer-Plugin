# UAT/QA Test Plan: PDF Embed & SEO Optimize (Drupal Module)

**Version:** 1.2.11
**Last Updated:** 2026-02-10
**Test Environment:** Drupal 10.x/11.x, PHP 8.1+

---

## Table of Contents

1. [Installation Testing](#1-installation-testing)
2. [Module Configuration](#2-module-configuration)
3. [PDF Document Entity](#3-pdf-document-entity)
4. [PDF Viewer Frontend](#4-pdf-viewer-frontend)
5. [Archive/Listing Page](#5-archivelisting-page)
6. [Block Plugin Testing](#6-block-plugin-testing)
7. [Media Library Integration](#7-media-library-integration)
8. [Settings Form](#8-settings-form)
9. [SEO Features](#9-seo-features)
10. [Premium Features](#10-premium-features)
11. [Security Testing](#11-security-testing)
12. [Accessibility Testing](#12-accessibility-testing)
13. [Performance Testing](#13-performance-testing)
14. [Uninstall Testing](#14-uninstall-testing)

---

## 1. Installation Testing

### 1.1 Module Installation

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| INS-001 | Composer Install | 1. Run composer require<br>2. Check modules folder | Module files present | [ ] Pass [ ] Fail |
| INS-002 | Manual Install | 1. Upload to modules/custom/<br>2. Check Extend page | Module listed | [ ] Pass [ ] Fail |
| INS-003 | Enable Module | 1. Go to Extend<br>2. Enable pdf_embed_seo | Module enables | [ ] Pass [ ] Fail |
| INS-004 | Dependencies Check | 1. Check module dependencies | No missing dependencies | [ ] Pass [ ] Fail |
| INS-005 | PHP Version | 1. Test on PHP 8.1<br>2. Test on PHP 8.2<br>3. Test on PHP 8.3 | Works on all versions | [ ] Pass [ ] Fail |
| INS-006 | Drupal 10 Compat | 1. Install on Drupal 10.x | Works correctly | [ ] Pass [ ] Fail |
| INS-007 | Drupal 11 Compat | 1. Install on Drupal 11.x | Works correctly | [ ] Pass [ ] Fail |
| INS-008 | Media Module Dep | 1. Check dependencies | Media module required | [ ] Pass [ ] Fail |

### 1.2 Database Schema

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| INS-009 | Tables Created | 1. Enable module<br>2. Check database | pdf_document table exists | [ ] Pass [ ] Fail |
| INS-010 | Analytics Table | 1. Check database | pdf_analytics table exists | [ ] Pass [ ] Fail |
| INS-011 | Field Tables | 1. Check database | Field storage tables created | [ ] Pass [ ] Fail |
| INS-012 | Access Tokens Table | 1. Enable premium<br>2. Run update hook | pdf_embed_seo_access_tokens exists | [ ] Pass [ ] Fail |
| INS-013 | Rate Limit Table | 1. Enable premium<br>2. Run update hook | pdf_embed_seo_rate_limit exists | [ ] Pass [ ] Fail |

### 1.3 Permissions

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| INS-014 | Permissions Exist | 1. Go to People > Permissions | PDF permissions listed | [ ] Pass [ ] Fail |
| INS-015 | Admin Permissions | 1. Check admin user | Has all PDF permissions | [ ] Pass [ ] Fail |

---

## 2. Module Configuration

### 2.1 Config Sync

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| CFG-001 | Default Config | 1. Enable module<br>2. Export config | Default config exported | [ ] Pass [ ] Fail |
| CFG-002 | Config Import | 1. Modify config<br>2. Import | Config applied | [ ] Pass [ ] Fail |

### 2.2 Routing

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| CFG-003 | Admin Routes | 1. Visit /admin/content/pdf | Page loads | [ ] Pass [ ] Fail |
| CFG-004 | Settings Route | 1. Visit /admin/config/media/pdf-embed-seo | Settings page loads | [ ] Pass [ ] Fail |
| CFG-005 | Viewer Route | 1. Visit /pdf/{id} | PDF viewer loads | [ ] Pass [ ] Fail |
| CFG-006 | Archive Route | 1. Visit /pdfs | Archive page loads | [ ] Pass [ ] Fail |

---

## 3. PDF Document Entity

### 3.1 Entity Type

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ENT-001 | Entity Exists | 1. Check Entity types | pdf_document registered | [ ] Pass [ ] Fail |
| ENT-002 | Admin Listing | 1. Go to Content > PDF Documents | Listing page works | [ ] Pass [ ] Fail |
| ENT-003 | Add Form | 1. Click Add PDF Document | Form displays | [ ] Pass [ ] Fail |

### 3.2 Entity Fields

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ENT-004 | Title Field | 1. Check add form | Title field present | [ ] Pass [ ] Fail |
| ENT-005 | PDF File Field | 1. Check add form | PDF upload field present | [ ] Pass [ ] Fail |
| ENT-006 | Description Field | 1. Check add form | Description textarea present | [ ] Pass [ ] Fail |
| ENT-007 | Thumbnail Field | 1. Check add form | Image upload present | [ ] Pass [ ] Fail |
| ENT-008 | Allow Download | 1. Check add form | Checkbox present | [ ] Pass [ ] Fail |
| ENT-009 | Allow Print | 1. Check add form | Checkbox present | [ ] Pass [ ] Fail |
| ENT-010 | View Count Computed | 1. View PDF<br>2. Check entity | View count reads from analytics | [ ] Pass [ ] Fail |

### 3.3 CRUD Operations

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ENT-011 | Create Document | 1. Fill form<br>2. Upload PDF<br>3. Save | Document created | [ ] Pass [ ] Fail |
| ENT-012 | Edit Document | 1. Open document<br>2. Change title<br>3. Save | Changes saved | [ ] Pass [ ] Fail |
| ENT-013 | Delete Document | 1. Click delete<br>2. Confirm | Document deleted | [ ] Pass [ ] Fail |
| ENT-014 | View Document | 1. Click view | PDF viewer displays | [ ] Pass [ ] Fail |

### 3.4 File Upload

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ENT-015 | Upload PDF | 1. Select PDF file<br>2. Upload | File uploads | [ ] Pass [ ] Fail |
| ENT-016 | Large File | 1. Upload 50MB PDF | Handles large files | [ ] Pass [ ] Fail |
| ENT-017 | Invalid File | 1. Try uploading .doc | Rejected | [ ] Pass [ ] Fail |
| ENT-018 | Replace PDF | 1. Remove file<br>2. Upload new | File replaced | [ ] Pass [ ] Fail |

---

## 4. PDF Viewer Frontend

### 4.1 Viewer Display

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| VWR-001 | PDF Loads | 1. Visit /pdf/{id} | PDF renders | [ ] Pass [ ] Fail |
| VWR-002 | PDF.js Loaded | 1. Check page source | PDF.js scripts included | [ ] Pass [ ] Fail |
| VWR-003 | Canvas Renders | 1. Check viewer | Canvas element with PDF | [ ] Pass [ ] Fail |

### 4.2 Navigation Controls

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| VWR-004 | Next Page | 1. Click next | Goes to next page | [ ] Pass [ ] Fail |
| VWR-005 | Previous Page | 1. Click previous | Goes to previous page | [ ] Pass [ ] Fail |
| VWR-006 | Page Counter | 1. Load multi-page PDF | Shows "X of Y" | [ ] Pass [ ] Fail |
| VWR-007 | Go to Page | 1. Enter page number<br>2. Submit | Jumps to page | [ ] Pass [ ] Fail |

### 4.3 Zoom Controls

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| VWR-008 | Zoom In | 1. Click zoom in | PDF enlarges | [ ] Pass [ ] Fail |
| VWR-009 | Zoom Out | 1. Click zoom out | PDF shrinks | [ ] Pass [ ] Fail |
| VWR-010 | Fit Width | 1. Click fit width | Fits container width | [ ] Pass [ ] Fail |
| VWR-011 | Fit Page | 1. Click fit page | Full page visible | [ ] Pass [ ] Fail |
| VWR-012 | Fullscreen | 1. Click fullscreen | Enters fullscreen | [ ] Pass [ ] Fail |

### 4.4 Download/Print Controls

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| VWR-013 | Download Enabled | 1. Enable download<br>2. View PDF | Download button visible | [ ] Pass [ ] Fail |
| VWR-014 | Download Disabled | 1. Disable download<br>2. View PDF | No download button | [ ] Pass [ ] Fail |
| VWR-015 | Download Works | 1. Click download | PDF downloads | [ ] Pass [ ] Fail |
| VWR-016 | Print Enabled | 1. Enable print<br>2. View PDF | Print button visible | [ ] Pass [ ] Fail |
| VWR-017 | Print Disabled | 1. Disable print<br>2. View PDF | No print button | [ ] Pass [ ] Fail |
| VWR-018 | Print Works | 1. Click print | Print dialog opens | [ ] Pass [ ] Fail |

### 4.5 Responsive Design

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| VWR-019 | Desktop | 1. View on 1920px | Full controls | [ ] Pass [ ] Fail |
| VWR-020 | Tablet | 1. View on 768px | Adapted layout | [ ] Pass [ ] Fail |
| VWR-021 | Mobile | 1. View on 375px | Mobile-friendly | [ ] Pass [ ] Fail |

### 4.6 Theme Support

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| VWR-022 | Light Theme | 1. Set light theme<br>2. View PDF | Light styling applied | [ ] Pass [ ] Fail |
| VWR-023 | Dark Theme | 1. Set dark theme<br>2. View PDF | Dark styling applied | [ ] Pass [ ] Fail |

---

## 5. Archive/Listing Page

### 5.1 Archive Display Options

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ARC-001 | Page Loads | 1. Visit /pdf | Page displays | [ ] Pass [ ] Fail |
| ARC-002 | PDFs Listed | 1. Create PDFs<br>2. Visit archive | PDFs shown | [ ] Pass [ ] Fail |
| ARC-003 | Thumbnails | 1. Add thumbnails<br>2. Visit archive | Thumbnails display | [ ] Pass [ ] Fail |
| ARC-004 | Titles Linked | 1. Click title | Goes to viewer | [ ] Pass [ ] Fail |
| ARC-005 | Descriptions | 1. Add descriptions<br>2. Visit archive | Descriptions shown | [ ] Pass [ ] Fail |
| ARC-006 | Pagination | 1. Create 20+ PDFs | Pager works | [ ] Pass [ ] Fail |
| ARC-007 | Empty State | 1. Delete all PDFs | "No PDFs" message | [ ] Pass [ ] Fail |
| ARC-008 | Grid View Display | 1. Set display style to Grid<br>2. Visit archive | Cards with thumbnails shown | [ ] Pass [ ] Fail |
| ARC-009 | List View Display | 1. Set display style to List<br>2. Visit archive | Simple list with icons shown | [ ] Pass [ ] Fail |
| ARC-010 | Show Description Toggle | 1. Enable "Show descriptions"<br>2. Visit archive | Descriptions visible | [ ] Pass [ ] Fail |
| ARC-011 | Hide Description Toggle | 1. Disable "Show descriptions"<br>2. Visit archive | Descriptions hidden | [ ] Pass [ ] Fail |
| ARC-012 | Show View Count Toggle | 1. Enable "Show view counts"<br>2. Visit archive | View counts visible | [ ] Pass [ ] Fail |
| ARC-013 | Hide View Count Toggle | 1. Disable "Show view counts"<br>2. Visit archive | View counts hidden | [ ] Pass [ ] Fail |

### 5.2 Archive Breadcrumbs

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ARC-014 | Breadcrumb Navigation | 1. Visit archive<br>2. Check breadcrumb | Home > PDF Documents shown | [ ] Pass [ ] Fail |
| ARC-015 | Breadcrumb Schema | 1. View archive source<br>2. Check JSON-LD | BreadcrumbList schema present | [ ] Pass [ ] Fail |
| ARC-016 | Breadcrumb Links Work | 1. Click Home in breadcrumb | Navigates to homepage | [ ] Pass [ ] Fail |
| ARC-017 | Current Page Indicator | 1. Check archive breadcrumb | aria-current="page" on current | [ ] Pass [ ] Fail |

### 5.3 Single PDF Breadcrumbs

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ARC-018 | Single PDF Breadcrumb | 1. Visit single PDF<br>2. Check breadcrumb | Home > PDF Documents > Title | [ ] Pass [ ] Fail |
| ARC-019 | Single PDF Schema | 1. View single PDF source<br>2. Check JSON-LD | BreadcrumbList with 3 items | [ ] Pass [ ] Fail |
| ARC-020 | Archive Link Works | 1. Click PDF Documents in breadcrumb | Navigates to archive | [ ] Pass [ ] Fail |
| ARC-021 | Breadcrumb A11Y | 1. Inspect breadcrumb nav | aria-label="Breadcrumb" present | [ ] Pass [ ] Fail |

### 5.4 Archive Accessibility

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| ARC-022 | List View Title Attr | 1. Set list view<br>2. Hover over links | Title attributes shown | [ ] Pass [ ] Fail |
| ARC-023 | Grid View Aria Labels | 1. Set grid view<br>2. Inspect card links | aria-label on links | [ ] Pass [ ] Fail |
| ARC-024 | View PDF Button A11Y | 1. Inspect "View PDF" button | aria-label includes title | [ ] Pass [ ] Fail |
| ARC-025 | Focus Styles | 1. Tab through archive | Focus indicators visible | [ ] Pass [ ] Fail |

---

## 6. Block Plugin Testing

### 6.1 Block Availability

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| BLK-001 | Block Exists | 1. Go to Block layout<br>2. Search "PDF" | Block available | [ ] Pass [ ] Fail |
| BLK-002 | Place Block | 1. Add block to region | Block placed | [ ] Pass [ ] Fail |

### 6.2 Block Configuration

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| BLK-003 | PDF Selection | 1. Configure block<br>2. Select PDF | PDF selected | [ ] Pass [ ] Fail |
| BLK-004 | Width Setting | 1. Set width | Width applied | [ ] Pass [ ] Fail |
| BLK-005 | Height Setting | 1. Set height | Height applied | [ ] Pass [ ] Fail |

### 6.3 Block Display

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| BLK-006 | Block Renders | 1. View page with block | PDF viewer in block | [ ] Pass [ ] Fail |
| BLK-007 | Multiple Blocks | 1. Place 3 blocks | All render | [ ] Pass [ ] Fail |

---

## 7. Media Library Integration

### 7.1 Media Source Plugin (v1.2.11)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| MED-001 | Media Source Available | 1. Go to Admin > Structure > Media types<br>2. Add media type | "PDF Document" source listed | [ ] Pass [ ] Fail |
| MED-002 | Create Media Type | 1. Create media type with PDF source<br>2. Save | Media type created | [ ] Pass [ ] Fail |
| MED-003 | File Field Created | 1. Check new media type fields | PDF file field exists | [ ] Pass [ ] Fail |
| MED-004 | File Extensions | 1. Check file field settings | Only PDF extension allowed | [ ] Pass [ ] Fail |
| MED-005 | Max File Size | 1. Check file field settings | 50 MB max file size | [ ] Pass [ ] Fail |

### 7.2 Media Entity Operations

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| MED-006 | Add PDF Media | 1. Add new PDF media<br>2. Upload PDF file | Media entity created | [ ] Pass [ ] Fail |
| MED-007 | Default Name | 1. Upload PDF without title | Uses filename as name | [ ] Pass [ ] Fail |
| MED-008 | Metadata Retrieved | 1. Create media<br>2. Check metadata | File name, size, MIME type shown | [ ] Pass [ ] Fail |
| MED-009 | Thumbnail Display | 1. Check media listing | PDF icon thumbnail shown | [ ] Pass [ ] Fail |
| MED-010 | Edit Media | 1. Edit PDF media<br>2. Update title | Changes saved | [ ] Pass [ ] Fail |
| MED-011 | Delete Media | 1. Delete PDF media | Media deleted | [ ] Pass [ ] Fail |

### 7.3 Media Field Formatter (v1.2.11)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| MED-012 | Formatter Available | 1. Add file field to content type<br>2. Check display settings | "PDF Viewer (SEO Optimized)" listed | [ ] Pass [ ] Fail |
| MED-013 | Formatter Renders | 1. Apply formatter to file field<br>2. View content | PDF viewer displays | [ ] Pass [ ] Fail |
| MED-014 | Width Setting | 1. Configure formatter width<br>2. View content | Width applied | [ ] Pass [ ] Fail |
| MED-015 | Height Setting | 1. Configure formatter height<br>2. View content | Height applied | [ ] Pass [ ] Fail |
| MED-016 | Download Toggle | 1. Enable download in formatter<br>2. View content | Download button visible | [ ] Pass [ ] Fail |
| MED-017 | Print Toggle | 1. Enable print in formatter<br>2. View content | Print button visible | [ ] Pass [ ] Fail |
| MED-018 | Non-PDF Skip | 1. Upload non-PDF file<br>2. View content | File skipped, no errors | [ ] Pass [ ] Fail |
| MED-019 | Dark Theme | 1. Set global dark theme<br>2. View formatter | Dark theme applied | [ ] Pass [ ] Fail |

### 7.4 Media Library Widget

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| MED-020 | Media Library Browse | 1. Add media field<br>2. Open media library | PDF media visible | [ ] Pass [ ] Fail |
| MED-021 | Select Existing | 1. Select existing PDF media | Media added to field | [ ] Pass [ ] Fail |
| MED-022 | Upload New | 1. Upload new PDF via library | New media created | [ ] Pass [ ] Fail |
| MED-023 | Multiple Selection | 1. Select multiple PDFs | All selected | [ ] Pass [ ] Fail |

---

## 8. Settings Form

### 8.1 Form Access

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SET-001 | Page Loads | 1. Visit settings URL | Form displays | [ ] Pass [ ] Fail |
| SET-002 | Permission Check | 1. Login as non-admin<br>2. Try settings | Access denied | [ ] Pass [ ] Fail |

### 8.2 Settings Fields

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SET-003 | Default Download | 1. Change setting<br>2. Save | Setting saved | [ ] Pass [ ] Fail |
| SET-004 | Default Print | 1. Change setting<br>2. Save | Setting saved | [ ] Pass [ ] Fail |
| SET-005 | Theme Selection | 1. Select dark<br>2. Save | Theme saved | [ ] Pass [ ] Fail |
| SET-006 | View Tracking | 1. Toggle setting<br>2. Save | Setting saved | [ ] Pass [ ] Fail |

### 8.3 Form UI

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SET-007 | Credit Link | 1. Scroll to bottom | "made with by Dross:Media" | [ ] Pass [ ] Fail |
| SET-008 | Credit A11Y | 1. Inspect credit link | aria-label, title present | [ ] Pass [ ] Fail |
| SET-009 | Save Message | 1. Save settings | Success message shown | [ ] Pass [ ] Fail |

---

## 9. SEO Features

### 9.1 URL Structure

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEO-001 | Clean URLs | 1. Create PDF<br>2. Check URL | Clean path /pdf/123 | [ ] Pass [ ] Fail |
| SEO-002 | Path Alias | 1. Set path alias<br>2. Check URL | Alias works | [ ] Pass [ ] Fail |

### 9.2 Meta Tags

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEO-003 | Page Title | 1. View PDF<br>2. Check <title> | PDF title in title tag | [ ] Pass [ ] Fail |
| SEO-004 | Meta Description | 1. Add description<br>2. Check source | Meta tag present | [ ] Pass [ ] Fail |

### 9.3 Schema.org

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEO-005 | Schema Markup | 1. View source | JSON-LD present | [ ] Pass [ ] Fail |
| SEO-006 | Schema Type | 1. Check JSON-LD | DigitalDocument type | [ ] Pass [ ] Fail |

---

## 10. Premium Features

### 10.1 Password Protection

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-001 | Password Field | 1. Edit PDF | Password field present | [ ] Pass [ ] Fail |
| PRM-002 | Set Password | 1. Enter password<br>2. Save | Password saved | [ ] Pass [ ] Fail |
| PRM-003 | Protection Works | 1. Visit protected PDF | Password form shows | [ ] Pass [ ] Fail |
| PRM-004 | Correct Password | 1. Enter correct password | PDF displays | [ ] Pass [ ] Fail |
| PRM-005 | Wrong Password | 1. Enter wrong password | Error shown | [ ] Pass [ ] Fail |
| PRM-006 | Session Cache | 1. Unlock PDF<br>2. Refresh page | Remains unlocked | [ ] Pass [ ] Fail |

### 10.2 Rate Limiting (v1.2.11)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-007 | Rate Limit Service | 1. Check service container | pdf_embed_seo.rate_limiter exists | [ ] Pass [ ] Fail |
| PRM-008 | 5 Failed Attempts | 1. Enter wrong password 5 times | Allowed | [ ] Pass [ ] Fail |
| PRM-009 | 6th Attempt Blocked | 1. Enter wrong password 6th time | HTTP 429 returned | [ ] Pass [ ] Fail |
| PRM-010 | Retry After Header | 1. Check blocked response | retry_after field present | [ ] Pass [ ] Fail |
| PRM-011 | Block Duration | 1. Wait 15 minutes<br>2. Try again | Access restored | [ ] Pass [ ] Fail |
| PRM-012 | Per-Document Limit | 1. Block on doc A<br>2. Try doc B | Doc B not blocked | [ ] Pass [ ] Fail |
| PRM-013 | Successful Login Reset | 1. Enter correct password | Attempt counter reset | [ ] Pass [ ] Fail |
| PRM-014 | Cron Cleanup | 1. Run cron | Old rate limit records deleted | [ ] Pass [ ] Fail |

### 10.3 Analytics

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-015 | Dashboard Access | 1. Visit analytics page | Dashboard loads | [ ] Pass [ ] Fail |
| PRM-016 | View Tracking | 1. View PDF<br>2. Check analytics | View recorded | [ ] Pass [ ] Fail |
| PRM-017 | Download Track | 1. Download PDF<br>2. Check analytics | Download recorded | [ ] Pass [ ] Fail |
| PRM-018 | Credit Link | 1. Check dashboard bottom | Dross:Media credit | [ ] Pass [ ] Fail |

### 10.4 Text Search

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-019 | Search Button | 1. View PDF | Search icon visible | [ ] Pass [ ] Fail |
| PRM-020 | Search Works | 1. Enter term<br>2. Search | Results highlighted | [ ] Pass [ ] Fail |

### 10.5 Bookmarks

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-021 | Bookmarks Panel | 1. View bookmarked PDF | Bookmark icon visible | [ ] Pass [ ] Fail |
| PRM-022 | Navigate Bookmarks | 1. Click bookmark | Goes to section | [ ] Pass [ ] Fail |

### 10.6 Reading Progress

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-023 | Progress Bar | 1. View PDF | Progress bar visible | [ ] Pass [ ] Fail |
| PRM-024 | Progress Saves | 1. Scroll to page 5<br>2. Return | Resumes at page 5 | [ ] Pass [ ] Fail |

### 10.7 Archive Page Redirect (Premium)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-025 | Redirect Settings | 1. Go to Premium Settings<br>2. Check Archive Redirect section | Section visible | [ ] Pass [ ] Fail |
| PRM-026 | Enable Redirect | 1. Enable Archive Redirect<br>2. Save | Setting saved | [ ] Pass [ ] Fail |
| PRM-027 | Redirect Type 301 | 1. Set redirect type to 301<br>2. Set URL<br>3. Visit /pdf<br>4. Check HTTP code | 301 redirect | [ ] Pass [ ] Fail |
| PRM-028 | Redirect Type 302 | 1. Set redirect type to 302<br>2. Visit /pdf<br>3. Check HTTP code | 302 redirect | [ ] Pass [ ] Fail |
| PRM-029 | Redirect URL | 1. Set custom redirect URL<br>2. Visit /pdf | Redirects to custom URL | [ ] Pass [ ] Fail |
| PRM-030 | Redirect Disabled | 1. Disable redirect<br>2. Visit /pdf | Archive page displays normally | [ ] Pass [ ] Fail |
| PRM-031 | License Required | 1. Invalidate license<br>2. Enable redirect<br>3. Visit /pdf | Redirect disabled (free fallback) | [ ] Pass [ ] Fail |

### 10.8 Download Tracking (v1.2.5)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-032 | Download Count Display | 1. Edit PDF<br>2. Check statistics | Download count visible | [ ] Pass [ ] Fail |
| PRM-033 | Download Tracking | 1. Download a PDF<br>2. Check admin | Download count increments | [ ] Pass [ ] Fail |
| PRM-034 | Download API | 1. POST to /download endpoint | Returns success | [ ] Pass [ ] Fail |
| PRM-035 | Download Analytics | 1. View Analytics | Downloads shown separately | [ ] Pass [ ] Fail |

### 10.9 Expiring Access Links (v1.2.5)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-036 | Generate Link | 1. As admin, generate link | Token URL returned | [ ] Pass [ ] Fail |
| PRM-037 | Custom Expiration | 1. Set 1 hour expiration | Link expires correctly | [ ] Pass [ ] Fail |
| PRM-038 | Max Uses | 1. Set max uses to 5 | Limit enforced | [ ] Pass [ ] Fail |
| PRM-039 | Valid Link Access | 1. Use valid link | PDF accessible | [ ] Pass [ ] Fail |
| PRM-040 | Expired Link | 1. Use expired link | Access denied | [ ] Pass [ ] Fail |
| PRM-041 | Max Uses Exceeded | 1. Exceed max uses | Access denied | [ ] Pass [ ] Fail |

### 10.10 Access Token Storage (v1.2.11)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-042 | Token Service | 1. Check service container | pdf_embed_seo.access_token_storage exists | [ ] Pass [ ] Fail |
| PRM-043 | Database Storage | 1. Generate token<br>2. Check database | Token in pdf_embed_seo_access_tokens | [ ] Pass [ ] Fail |
| PRM-044 | Token Validation | 1. Generate token<br>2. Validate | Returns valid | [ ] Pass [ ] Fail |
| PRM-045 | Use Count Increment | 1. Validate token<br>2. Check use_count | Incremented | [ ] Pass [ ] Fail |
| PRM-046 | Expiry Check | 1. Wait for expiry<br>2. Validate | Returns invalid | [ ] Pass [ ] Fail |
| PRM-047 | Max Uses Check | 1. Exceed max uses<br>2. Validate | Returns invalid | [ ] Pass [ ] Fail |
| PRM-048 | Cron Token Cleanup | 1. Run cron | Expired tokens deleted | [ ] Pass [ ] Fail |
| PRM-049 | State API Fallback | 1. Remove table<br>2. Generate token | Falls back to State API | [ ] Pass [ ] Fail |

### 10.11 Schema Optimization (v1.2.5)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-050 | AI Summary | 1. Add TL;DR<br>2. View source | abstract in schema | [ ] Pass [ ] Fail |
| PRM-051 | FAQ Schema | 1. Add FAQ items<br>2. View source | FAQPage schema | [ ] Pass [ ] Fail |
| PRM-052 | Reading Time | 1. Set reading time | timeRequired in schema | [ ] Pass [ ] Fail |
| PRM-053 | Difficulty Level | 1. Set difficulty | educationalLevel in schema | [ ] Pass [ ] Fail |

### 10.12 Role-Based Access Control (v1.2.5)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-054 | Role Restriction Field | 1. Edit PDF<br>2. Check access section | Role selection visible | [ ] Pass [ ] Fail |
| PRM-055 | Restrict to Admin | 1. Restrict to admin<br>2. View as anon | Access denied | [ ] Pass [ ] Fail |
| PRM-056 | Multiple Roles | 1. Allow multiple roles | Correct access | [ ] Pass [ ] Fail |
| PRM-057 | Logged-In Only | 1. Require login<br>2. Test | Login required | [ ] Pass [ ] Fail |

### 10.13 Bulk Import (v1.2.5)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-058 | Bulk Import Page | 1. Visit bulk import | Page loads | [ ] Pass [ ] Fail |
| PRM-059 | Import Multiple PDFs | 1. Select PDFs<br>2. Import | Documents created | [ ] Pass [ ] Fail |
| PRM-060 | Skip Duplicates | 1. Import same PDF twice | Duplicate skipped | [ ] Pass [ ] Fail |
| PRM-061 | Progress Display | 1. Import 10 PDFs | Progress shown | [ ] Pass [ ] Fail |

### 10.14 Viewer Enhancements (v1.2.5)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRM-062 | Text Search | 1. Enable search<br>2. Use Ctrl+F | Search works | [ ] Pass [ ] Fail |
| PRM-063 | Search Results | 1. Search term | Results highlighted | [ ] Pass [ ] Fail |
| PRM-064 | Bookmarks Panel | 1. View PDF with bookmarks | Bookmarks visible | [ ] Pass [ ] Fail |
| PRM-065 | Navigate Bookmarks | 1. Click bookmark | Navigates to section | [ ] Pass [ ] Fail |

---

## 11. Security Testing

### 11.1 Access Control

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEC-001 | Anonymous View | 1. Log out<br>2. View published PDF | Can view | [ ] Pass [ ] Fail |
| SEC-002 | Anonymous Edit | 1. Log out<br>2. Try edit URL | Access denied | [ ] Pass [ ] Fail |
| SEC-003 | CSRF Protection | 1. Submit form without token | Rejected | [ ] Pass [ ] Fail |

### 11.2 CSRF Token Protection (v1.2.11)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEC-004 | Track View CSRF | 1. POST to track_view without token | Request rejected | [ ] Pass [ ] Fail |
| SEC-005 | Track Download CSRF | 1. POST to track_download without token | Request rejected | [ ] Pass [ ] Fail |
| SEC-006 | Save Progress CSRF | 1. POST to progress without token | Request rejected | [ ] Pass [ ] Fail |
| SEC-007 | Verify Password CSRF | 1. POST to verify-password without token | Request rejected | [ ] Pass [ ] Fail |
| SEC-008 | Valid CSRF Token | 1. POST with valid X-CSRF-Token | Request accepted | [ ] Pass [ ] Fail |

### 11.3 Input Validation

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEC-009 | XSS in Title | 1. Enter <script><br>2. Save | Script escaped | [ ] Pass [ ] Fail |
| SEC-010 | File Validation | 1. Try upload .php | Rejected | [ ] Pass [ ] Fail |

### 11.4 Privacy

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEC-011 | No External Calls | 1. Monitor network | No external requests | [ ] Pass [ ] Fail |
| SEC-012 | PDF.js Local | 1. Check script sources | All local | [ ] Pass [ ] Fail |
| SEC-013 | IP Anonymization | 1. Check analytics record | IP anonymized (last octet 0) | [ ] Pass [ ] Fail |

### 11.5 Session Security (v1.2.11)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| SEC-014 | Session Cache Context | 1. Unlock protected PDF<br>2. Check cache tags | Session context applied | [ ] Pass [ ] Fail |
| SEC-015 | Different Session | 1. Unlock in browser A<br>2. Check in browser B | Browser B sees password form | [ ] Pass [ ] Fail |
| SEC-016 | Password Form No Cache | 1. Check password form cache | max-age: 0 | [ ] Pass [ ] Fail |

---

## 12. Accessibility Testing

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| A11Y-001 | Keyboard Nav | 1. Tab through viewer | All accessible | [ ] Pass [ ] Fail |
| A11Y-002 | Screen Reader | 1. Use screen reader | Content announced | [ ] Pass [ ] Fail |
| A11Y-003 | Focus Visible | 1. Tab through | Focus indicators | [ ] Pass [ ] Fail |
| A11Y-004 | ARIA Labels | 1. Inspect controls | Labels present | [ ] Pass [ ] Fail |
| A11Y-005 | Credit Link A11Y | 1. Inspect Dross:Media link | aria-label, title | [ ] Pass [ ] Fail |

---

## 13. Performance Testing

### 13.1 Page Load Performance

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRF-001 | Page Load | 1. Load PDF viewer | Under 3 seconds | [ ] Pass [ ] Fail |
| PRF-002 | Large PDF | 1. Load 100-page PDF | Handles without crash | [ ] Pass [ ] Fail |
| PRF-003 | Cache Working | 1. Check cache headers | Proper caching | [ ] Pass [ ] Fail |
| PRF-004 | Library Attach | 1. Check non-PDF pages | PDF.js not loaded | [ ] Pass [ ] Fail |

### 13.2 Computed View Count (v1.2.11)

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRF-005 | No Entity Save | 1. View PDF<br>2. Check database queries | No entity save | [ ] Pass [ ] Fail |
| PRF-006 | View Count Computed | 1. Track view in analytics<br>2. Check entity view_count | Reads from analytics table | [ ] Pass [ ] Fail |
| PRF-007 | Cache Not Invalidated | 1. View PDF<br>2. Check cache tags | No cache invalidation | [ ] Pass [ ] Fail |
| PRF-008 | Fallback Query | 1. Query with pdf_id<br>2. Query with pdf_document_id | Both work | [ ] Pass [ ] Fail |

### 13.3 Cron Performance

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| PRF-009 | Token Cleanup | 1. Create 1000 tokens<br>2. Run cron | Expired tokens deleted | [ ] Pass [ ] Fail |
| PRF-010 | Rate Limit Cleanup | 1. Create 1000 records<br>2. Run cron | Old records deleted | [ ] Pass [ ] Fail |
| PRF-011 | Analytics Retention | 1. Check analytics cleanup | Old data deleted per setting | [ ] Pass [ ] Fail |

---

## 14. Uninstall Testing

| Test ID | Test Case | Steps | Expected Result | Status |
|---------|-----------|-------|-----------------|--------|
| UNI-001 | Disable Module | 1. Disable module | No errors | [ ] Pass [ ] Fail |
| UNI-002 | Enable Again | 1. Re-enable module | Data preserved | [ ] Pass [ ] Fail |
| UNI-003 | Uninstall | 1. Uninstall module | Tables removed | [ ] Pass [ ] Fail |
| UNI-004 | Config Removed | 1. Check config | Module config removed | [ ] Pass [ ] Fail |

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

- Test on clean Drupal installation
- Test with common themes (Olivero, Claro admin theme)
- Test with common modules (Pathauto, Metatag, Token)
- Test Drush commands if implemented
- Check Views integration if applicable
- Test cron jobs for analytics aggregation
- Ensure Media module is enabled before testing Media Library features
- v1.2.11 requires update hook 9001 for new database tables

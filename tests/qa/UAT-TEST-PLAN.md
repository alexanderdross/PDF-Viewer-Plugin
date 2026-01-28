# User Acceptance Testing (UAT) Plan

## PDF Embed & SEO Optimize - Version 1.2.3

**Date:** 2025-01-28
**Modules:** WP Free, WP Premium, Drupal Free, Drupal Premium

---

## Overview

This UAT plan validates that the PDF Embed & SEO Optimize plugin meets business requirements and user expectations across all four modules.

---

## Test Participants

| Role | Responsibility |
|------|----------------|
| Site Administrator | Tests admin functionality, settings, analytics |
| Content Editor | Tests PDF creation, editing, publishing |
| End User (Guest) | Tests viewing, downloading, navigation |
| End User (Logged-in) | Tests progress tracking, protected PDFs |
| Developer | Tests API endpoints, hooks, integration |

---

## User Stories & Acceptance Criteria

### WordPress Free Module

#### US-WPF-001: Add PDF Documents
**As a** content editor
**I want to** upload and publish PDF documents
**So that** visitors can view them on my website

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can navigate to PDF Documents > Add New | ☐ | ☐ | |
| 2 | Can enter title and description | ☐ | ☐ | |
| 3 | Can upload PDF file via media uploader | ☐ | ☐ | |
| 4 | Can set featured image | ☐ | ☐ | |
| 5 | Can toggle download permission | ☐ | ☐ | |
| 6 | Can toggle print permission | ☐ | ☐ | |
| 7 | Can publish document | ☐ | ☐ | |
| 8 | Document appears in list after publishing | ☐ | ☐ | |

---

#### US-WPF-002: View PDF Documents
**As a** website visitor
**I want to** view PDF documents in the browser
**So that** I can read them without downloading

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can access PDF at /pdf/document-slug/ | ☐ | ☐ | |
| 2 | PDF viewer loads within 5 seconds | ☐ | ☐ | |
| 3 | Can navigate pages using controls | ☐ | ☐ | |
| 4 | Can zoom in and out | ☐ | ☐ | |
| 5 | Can enter full screen mode | ☐ | ☐ | |
| 6 | Viewer works on mobile devices | ☐ | ☐ | |
| 7 | Viewer works on tablets | ☐ | ☐ | |

---

#### US-WPF-003: Browse PDF Archive
**As a** website visitor
**I want to** browse all available PDFs
**So that** I can find documents I'm interested in

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can access archive at /pdf/ | ☐ | ☐ | |
| 2 | All published PDFs are listed | ☐ | ☐ | |
| 3 | Can see thumbnails for each PDF | ☐ | ☐ | |
| 4 | Can see title and description | ☐ | ☐ | |
| 5 | Can click to view a PDF | ☐ | ☐ | |
| 6 | Pagination works correctly | ☐ | ☐ | |
| 7 | Can search for specific PDFs | ☐ | ☐ | |

---

#### US-WPF-004: Control Print/Download
**As a** site administrator
**I want to** control print and download permissions per PDF
**So that** I can protect sensitive documents

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can disable download on a PDF | ☐ | ☐ | |
| 2 | Download button hidden when disabled | ☐ | ☐ | |
| 3 | Can disable print on a PDF | ☐ | ☐ | |
| 4 | Print button hidden when disabled | ☐ | ☐ | |
| 5 | Settings apply immediately after save | ☐ | ☐ | |

---

#### US-WPF-005: Embed PDFs in Pages
**As a** content editor
**I want to** embed PDF viewers in posts and pages
**So that** I can display PDFs inline with other content

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can find "PDF Viewer" block in editor | ☐ | ☐ | |
| 2 | Can select a PDF from dropdown | ☐ | ☐ | |
| 3 | Block preview shows in editor | ☐ | ☐ | |
| 4 | Can adjust viewer height | ☐ | ☐ | |
| 5 | Published page shows embedded viewer | ☐ | ☐ | |
| 6 | Shortcode [pdf_viewer id="X"] works | ☐ | ☐ | |

---

#### US-WPF-006: SEO Optimization
**As a** site administrator
**I want** PDFs to be SEO-optimized
**So that** they appear in search results

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Each PDF has unique, clean URL | ☐ | ☐ | |
| 2 | Schema.org markup present in page source | ☐ | ☐ | |
| 3 | OpenGraph meta tags present | ☐ | ☐ | |
| 4 | Twitter Card meta tags present | ☐ | ☐ | |
| 5 | Yoast SEO fields are editable | ☐ | ☐ | |

---

#### US-WPF-007: GEO/AEO/LLM Optimization
**As a** site administrator
**I want** PDFs optimized for AI and voice search
**So that** they appear in voice assistants and AI-generated results

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | SpeakableSpecification present in schema | ☐ | ☐ | |
| 2 | potentialAction (ReadAction) present | ☐ | ☐ | |
| 3 | accessMode and accessibilityFeature present | ☐ | ☐ | |
| 4 | fileFormat shows application/pdf | ☐ | ☐ | |
| 5 | inLanguage matches site language | ☐ | ☐ | |
| 6 | publisher schema includes site name | ☐ | ☐ | |

---

#### US-WPF-008: Social Sharing Without Yoast
**As a** site administrator without Yoast SEO
**I want** proper social sharing meta tags
**So that** PDFs display correctly when shared on social media

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | og:title present without Yoast | ☐ | ☐ | |
| 2 | og:description present without Yoast | ☐ | ☐ | |
| 3 | og:image uses featured image | ☐ | ☐ | |
| 4 | twitter:card present without Yoast | ☐ | ☐ | |
| 5 | Archive page has og:type=website | ☐ | ☐ | |
| 6 | PDF preview displays on Facebook share | ☐ | ☐ | |
| 7 | PDF preview displays on Twitter share | ☐ | ☐ | |

---

### WordPress Premium Module

#### US-WPP-001: View Analytics
**As a** site administrator
**I want to** see PDF view statistics
**So that** I can understand which documents are popular

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can access Analytics from admin menu | ☐ | ☐ | |
| 2 | Dashboard shows total views | ☐ | ☐ | |
| 3 | Can see popular documents chart | ☐ | ☐ | |
| 4 | Can see recent views log | ☐ | ☐ | |
| 5 | Can filter by time period | ☐ | ☐ | |
| 6 | Can export data as CSV | ☐ | ☐ | |
| 7 | Can export data as JSON | ☐ | ☐ | |

---

#### US-WPP-002: Password Protect PDFs
**As a** content editor
**I want to** password protect sensitive PDFs
**So that** only authorized users can view them

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can enable password protection on PDF | ☐ | ☐ | |
| 2 | Can set a password | ☐ | ☐ | |
| 3 | Visitors see password prompt | ☐ | ☐ | |
| 4 | Correct password grants access | ☐ | ☐ | |
| 5 | Wrong password shows error | ☐ | ☐ | |
| 6 | Access persists during session | ☐ | ☐ | |
| 7 | Too many wrong attempts locks out | ☐ | ☐ | |

---

#### US-WPP-003: Resume Reading
**As a** website visitor
**I want to** resume reading where I left off
**So that** I don't have to find my place again

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Progress saves automatically | ☐ | ☐ | |
| 2 | Returning shows "Resume reading?" prompt | ☐ | ☐ | |
| 3 | Clicking "Yes" jumps to saved page | ☐ | ☐ | |
| 4 | Clicking "No" starts from beginning | ☐ | ☐ | |
| 5 | Works for guest users | ☐ | ☐ | |
| 6 | Works for logged-in users | ☐ | ☐ | |

---

#### US-WPP-004: XML Sitemap
**As a** site administrator
**I want** a dedicated PDF sitemap
**So that** search engines can find all my PDFs

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Sitemap accessible at /pdf/sitemap.xml | ☐ | ☐ | |
| 2 | All published PDFs are listed | ☐ | ☐ | |
| 3 | Draft PDFs are not listed | ☐ | ☐ | |
| 4 | Sitemap has styled view in browser | ☐ | ☐ | |
| 5 | Can submit to Google Search Console | ☐ | ☐ | |

---

#### US-WPP-005: Organize with Categories
**As a** content editor
**I want to** organize PDFs into categories and tags
**So that** visitors can find related documents

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can create PDF categories | ☐ | ☐ | |
| 2 | Can create PDF tags | ☐ | ☐ | |
| 3 | Can assign categories to PDFs | ☐ | ☐ | |
| 4 | Can assign tags to PDFs | ☐ | ☐ | |
| 5 | Category archive pages work | ☐ | ☐ | |
| 6 | Tag archive pages work | ☐ | ☐ | |

---

### Drupal Free Module

#### US-DRF-001: Add PDF Documents
**As a** content editor
**I want to** create PDF documents in Drupal
**So that** visitors can view them on my site

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can navigate to Content > PDF Documents | ☐ | ☐ | |
| 2 | Can click Add PDF Document | ☐ | ☐ | |
| 3 | Can fill in title and description | ☐ | ☐ | |
| 4 | Can upload PDF file | ☐ | ☐ | |
| 5 | Can upload thumbnail image | ☐ | ☐ | |
| 6 | Can set download/print permissions | ☐ | ☐ | |
| 7 | Can save and publish | ☐ | ☐ | |

---

#### US-DRF-002: View PDFs
**As a** website visitor
**I want to** view PDFs in the browser
**So that** I can read without downloading

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can access PDF at /pdf/slug | ☐ | ☐ | |
| 2 | PDF viewer loads correctly | ☐ | ☐ | |
| 3 | Navigation controls work | ☐ | ☐ | |
| 4 | Zoom controls work | ☐ | ☐ | |
| 5 | Full screen works | ☐ | ☐ | |
| 6 | Responsive on mobile | ☐ | ☐ | |

---

#### US-DRF-003: Browse Archive
**As a** visitor
**I want to** browse all PDFs
**So that** I can find interesting documents

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Archive available at /pdf | ☐ | ☐ | |
| 2 | All published PDFs listed | ☐ | ☐ | |
| 3 | Pagination works | ☐ | ☐ | |
| 4 | Search/filter works | ☐ | ☐ | |

---

### Drupal Premium Module

#### US-DRP-001: View Analytics
**As a** site administrator
**I want to** see PDF statistics
**So that** I understand document engagement

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can access Reports > PDF Analytics | ☐ | ☐ | |
| 2 | Dashboard shows statistics | ☐ | ☐ | |
| 3 | Can see popular documents | ☐ | ☐ | |
| 4 | Can export data | ☐ | ☐ | |

---

#### US-DRP-002: Password Protection
**As a** content editor
**I want to** protect PDFs with passwords
**So that** only authorized users can access

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can enable password on PDF | ☐ | ☐ | |
| 2 | Password form displays | ☐ | ☐ | |
| 3 | Correct password works | ☐ | ☐ | |
| 4 | Wrong password denied | ☐ | ☐ | |

---

#### US-DRP-003: Reading Progress
**As a** visitor
**I want to** resume where I left off
**So that** I can continue reading

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Progress saves automatically | ☐ | ☐ | |
| 2 | Resume prompt on return | ☐ | ☐ | |
| 3 | Resume works correctly | ☐ | ☐ | |

---

### API Testing

#### US-API-001: External Integration
**As a** developer
**I want to** access PDF data via REST API
**So that** I can build custom integrations

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | GET /documents returns list | ☐ | ☐ | |
| 2 | Response includes all fields | ☐ | ☐ | |
| 3 | Pagination works | ☐ | ☐ | |
| 4 | Search filter works | ☐ | ☐ | |
| 5 | GET /documents/{id} returns single | ☐ | ☐ | |
| 6 | POST /documents/{id}/view tracks | ☐ | ☐ | |
| 7 | GET /settings returns config | ☐ | ☐ | |
| 8 | API works identically on WP and Drupal | ☐ | ☐ | |

---

## Test Scenarios

### Scenario 1: New User Onboarding (WordPress)
1. Install plugin
2. Activate plugin
3. Go to PDF Documents
4. Add first PDF
5. View PDF on frontend
6. Embed in a page using block
7. Check archive page

**Expected:** All steps complete without errors

---

### Scenario 2: Premium Upgrade Path
1. Use free version normally
2. Click "Get Premium" link
3. Purchase and download premium
4. Upload premium folder
5. Enter license key
6. Verify premium features appear
7. Test password protection
8. Test analytics dashboard

**Expected:** Smooth upgrade experience

---

### Scenario 3: Content Manager Workflow
1. Create 5 PDF documents
2. Assign categories to each
3. Set some as password protected
4. Publish all
5. Check archive filtering
6. Check category pages
7. View analytics after some views

**Expected:** All management features work

---

### Scenario 4: End User Experience
1. Visit site as guest
2. Browse archive
3. Open a PDF
4. Navigate through pages
5. Leave and return later
6. Resume reading (premium)
7. Access password protected PDF

**Expected:** Smooth viewing experience

---

## Sign-Off

### Test Summary

| Module | Total Tests | Passed | Failed | Blocked |
|--------|-------------|--------|--------|---------|
| WP Free | | | | |
| WP Premium | | | | |
| Drupal Free | | | | |
| Drupal Premium | | | | |

### Defects Found

| ID | Description | Severity | Status |
|----|-------------|----------|--------|
| | | | |

### Approval

| Role | Name | Date | Approved |
|------|------|------|----------|
| Product Owner | | | ☐ |
| QA Manager | | | ☐ |
| Dev Lead | | | ☐ |

---

**UAT Complete:** ☐ Yes ☐ No

**Comments:**

---

*PDF Embed & SEO Optimize v1.2.3 - UAT Test Plan*

# User Acceptance Testing (UAT) Plan

## PDF Embed & SEO Optimize - Version 1.2.8

**Date:** 2026-02-02
**Updated:** 2026-02-04 (Grid/List Styling Enhancements)
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

#### US-WPP-006: Track Downloads (v1.2.5)
**As a** site administrator
**I want to** track PDF downloads separately from views
**So that** I can understand document engagement better

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Download count visible in admin | ☐ | ☐ | |
| 2 | Downloads tracked separately from views | ☐ | ☐ | |
| 3 | Download analytics in dashboard | ☐ | ☐ | |
| 4 | Download event fires on button click | ☐ | ☐ | |
| 5 | API endpoint POST /download works | ☐ | ☐ | |

---

#### US-WPP-007: Expiring Access Links (v1.2.5)
**As a** site administrator
**I want to** generate time-limited access links
**So that** I can share PDFs temporarily with specific people

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can generate expiring link from admin | ☐ | ☐ | |
| 2 | Can set custom expiration time | ☐ | ☐ | |
| 3 | Can set maximum uses | ☐ | ☐ | |
| 4 | Valid link grants access to PDF | ☐ | ☐ | |
| 5 | Expired link shows error message | ☐ | ☐ | |
| 6 | Max uses exceeded shows error | ☐ | ☐ | |
| 7 | Only admins can generate links | ☐ | ☐ | |

---

#### US-WPP-008: GEO/AEO Schema Optimization (v1.2.5)
**As a** site administrator
**I want to** add AI-optimized metadata to PDFs
**So that** they appear in AI search results and voice assistants

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can add AI Summary (TL;DR) | ☐ | ☐ | |
| 2 | Can add Key Points/Takeaways | ☐ | ☐ | |
| 3 | Can add FAQ items | ☐ | ☐ | |
| 4 | FAQPage schema generated | ☐ | ☐ | |
| 5 | Can set reading time | ☐ | ☐ | |
| 6 | Can set difficulty level | ☐ | ☐ | |
| 7 | Can set target audience | ☐ | ☐ | |

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

#### US-DRP-004: Download Tracking (v1.2.5)
**As a** site administrator
**I want to** track PDF downloads
**So that** I can measure document engagement

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Download count tracked | ☐ | ☐ | |
| 2 | Separate from view count | ☐ | ☐ | |
| 3 | Visible in analytics | ☐ | ☐ | |

---

#### US-DRP-005: Expiring Access Links (v1.2.5)
**As a** site administrator
**I want to** create temporary access links
**So that** I can share PDFs with time limits

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can generate expiring link | ☐ | ☐ | |
| 2 | Link expires after set time | ☐ | ☐ | |
| 3 | Max uses enforced | ☐ | ☐ | |
| 4 | Invalid links rejected | ☐ | ☐ | |

---

#### US-DRP-006: Role-Based Access (v1.2.5)
**As a** site administrator
**I want to** restrict PDF access by user role
**So that** only authorized users can view certain documents

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can select allowed roles | ☐ | ☐ | |
| 2 | Unauthorized users denied | ☐ | ☐ | |
| 3 | Login prompt shown | ☐ | ☐ | |

---

#### US-DRP-007: Bulk Import (v1.2.5)
**As a** content editor
**I want to** import multiple PDFs at once
**So that** I can quickly add many documents

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Bulk import page accessible | ☐ | ☐ | |
| 2 | Can select multiple PDFs | ☐ | ☐ | |
| 3 | Documents created correctly | ☐ | ☐ | |
| 4 | Duplicates handled | ☐ | ☐ | |

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

---

## Version 1.2.7 User Stories

### US-SEC-001: Secure Password Storage (Drupal)
**As a** site administrator
**I want** PDF passwords to be stored securely hashed
**So that** user data is protected if the database is compromised

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Password is hashed when saving PDF document | ☐ | ☐ | |
| 2 | Stored password starts with $ (hash format) | ☐ | ☐ | |
| 3 | Cannot see plain text password in database | ☐ | ☐ | |
| 4 | Password verification still works correctly | ☐ | ☐ | |
| 5 | Existing plain text passwords still work | ☐ | ☐ | |

---

### US-SEC-002: XSS Prevention in PDF Titles (Drupal)
**As a** website visitor
**I want** PDF titles to be safely displayed
**So that** malicious scripts cannot execute on my browser

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Title with <script> tags displays as text | ☐ | ☐ | |
| 2 | Title with HTML event handlers is escaped | ☐ | ☐ | |
| 3 | Normal titles display correctly unchanged | ☐ | ☐ | |
| 4 | Unicode characters in titles preserved | ☐ | ☐ | |

---

### US-DEV-001: Hook Migration
**As a** developer with custom code
**I want** clear documentation on hook changes
**So that** I can update my integration code

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | New hook pdf_embed_seo_optimize_settings_saved works | ☐ | ☐ | |
| 2 | Hook receives $post_id and $settings array | ☐ | ☐ | |
| 3 | Documentation shows new hook name | ☐ | ☐ | |
| 4 | Migration instructions provided | ☐ | ☐ | |
| 5 | Thumbnail generation triggered on save | ☐ | ☐ | |

---

### US-QA-001: WordPress Plugin Check Compliance
**As a** plugin developer
**I want** the plugin to pass WordPress Plugin Check
**So that** it can be listed on WordPress.org

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | No SQL escaping warnings in Plugin Check | ☐ | ☐ | |
| 2 | No hook naming warnings | ☐ | ☐ | |
| 3 | No unescaped output warnings | ☐ | ☐ | |
| 4 | All critical issues resolved | ☐ | ☐ | |

---

## Version 1.2.7 User Stories (New)

### US-FW-001: Full-Width PDF Archive (WordPress)
**As a** website visitor
**I want** the PDF archive page to display full-width
**So that** I can see the PDF listings without distracting sidebars

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | No widget area visible on /pdf/ archive | ☐ | ☐ | |
| 2 | Content area spans full width | ☐ | ☐ | |
| 3 | Grid view displays correctly without sidebar | ☐ | ☐ | |
| 4 | List view displays correctly without sidebar | ☐ | ☐ | |
| 5 | Header and footer still visible | ☐ | ☐ | |

---

### US-FW-002: Full-Width PDF Single Page (WordPress)
**As a** website visitor
**I want** individual PDF pages to display full-width
**So that** I can view PDFs with maximum screen space

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | No widget area visible on /pdf/slug/ | ☐ | ☐ | |
| 2 | PDF viewer has maximum width available | ☐ | ☐ | |
| 3 | Works with different WordPress themes | ☐ | ☐ | |
| 4 | Responsive on mobile devices | ☐ | ☐ | |

---

### US-FW-003: Full-Width PDF Pages (Drupal)
**As a** website visitor
**I want** Drupal PDF pages to display full-width
**So that** I have optimal viewing experience

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | No sidebar regions on /pdf archive | ☐ | ☐ | |
| 2 | No sidebar regions on /pdf/slug single page | ☐ | ☐ | |
| 3 | Body has .page-pdf class for theming | ☐ | ☐ | |
| 4 | Works with Bartik theme | ☐ | ☐ | |
| 5 | Works with Olivero theme | ☐ | ☐ | |

---

### US-DEV-002: Custom Page Template (Drupal)
**As a** theme developer
**I want** page template suggestions for PDF pages
**So that** I can create custom full-width layouts

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | page--pdf.html.twig suggestion available | ☐ | ☐ | |
| 2 | page--pdf--archive.html.twig suggestion available | ☐ | ☐ | |
| 3 | page--pdf--document.html.twig suggestion available | ☐ | ☐ | |
| 4 | Custom template overrides work correctly | ☐ | ☐ | |

---

---

### US-STYLE-001: Customize Archive Page Heading
**As a** site administrator
**I want to** customize the archive page heading
**So that** I can match my site's branding and terminology

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can find "Archive Page Heading" in Settings | ☐ | ☐ | |
| 2 | Default placeholder shows "PDF Documents" | ☐ | ☐ | |
| 3 | Custom heading displays on /pdf/ archive | ☐ | ☐ | |
| 4 | Breadcrumb updates with custom heading | ☐ | ☐ | |
| 5 | Schema.org breadcrumb updates | ☐ | ☐ | |
| 6 | Empty field uses default "PDF Documents" | ☐ | ☐ | |

---

### US-STYLE-002: Style Archive Page Header
**As a** site administrator
**I want to** customize the archive page styling
**So that** it matches my website design

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can set heading alignment (left/center/right) | ☐ | ☐ | |
| 2 | Center alignment is default | ☐ | ☐ | |
| 3 | Can set custom font color | ☐ | ☐ | |
| 4 | Can use theme default color | ☐ | ☐ | |
| 5 | Can set custom background color | ☐ | ☐ | |
| 6 | Background adds padding automatically | ☐ | ☐ | |

---

### US-CACHE-001: PDF Viewer on Cached Pages
**As a** website visitor
**I want** PDF viewers to work on cached pages
**So that** I can view PDFs even when pages are served from cache

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | PDF loads on first visit | ☐ | ☐ | |
| 2 | PDF loads on cached page (same session) | ☐ | ☐ | |
| 3 | PDF loads on cached page (different day) | ☐ | ☐ | |
| 4 | No "Security check failed" error | ☐ | ☐ | |
| 5 | Works with page caching plugin enabled | ☐ | ☐ | |
| 6 | Works with CDN caching enabled | ☐ | ☐ | |

---

## Version 1.2.8 User Stories

### US-GRID-001: Grid View Styling (WordPress & Drupal)
**As a** site administrator
**I want to** style the grid view cards with custom colors
**So that** the PDF archive matches my website design

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can set font color for grid card titles | ☐ | ☐ | |
| 2 | Font color applies to card excerpts | ☐ | ☐ | |
| 3 | Font color applies to card meta (date, views) | ☐ | ☐ | |
| 4 | Can set background color for individual cards | ☐ | ☐ | |
| 5 | Content alignment applies to card content | ☐ | ☐ | |
| 6 | Styling works on WordPress | ☐ | ☐ | |
| 7 | Styling works on Drupal | ☐ | ☐ | |

---

### US-GRID-002: List View Styling (WordPress & Drupal)
**As a** site administrator
**I want to** style the list view with custom colors
**So that** it matches my website design when using list display

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Can set font color for list item titles | ☐ | ☐ | |
| 2 | Font color applies to list links | ☐ | ☐ | |
| 3 | Can set background color for list container | ☐ | ☐ | |
| 4 | Content alignment applies to list items | ☐ | ☐ | |
| 5 | Styling works on WordPress | ☐ | ☐ | |
| 6 | Styling works on Drupal | ☐ | ☐ | |

---

### US-GRID-003: Display Style Independence (WordPress & Drupal)
**As a** site administrator
**I want** styling settings to work with both grid and list views
**So that** I can switch display styles without losing my color settings

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Font color persists when switching grid to list | ☐ | ☐ | |
| 2 | Font color persists when switching list to grid | ☐ | ☐ | |
| 3 | Background color applies correctly to grid cards | ☐ | ☐ | |
| 4 | Background color applies correctly to list container | ☐ | ☐ | |
| 5 | Alignment works correctly in grid view | ☐ | ☐ | |
| 6 | Alignment works correctly in list view | ☐ | ☐ | |

---

### US-GRID-004: Layout Width Setting (WordPress & Drupal)
**As a** site administrator
**I want to** choose between boxed and full-width layouts
**So that** the archive page fits my site's layout style

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Boxed layout constrains content width | ☐ | ☐ | |
| 2 | Full-width layout spans entire page | ☐ | ☐ | |
| 3 | Layout setting works with grid view | ☐ | ☐ | |
| 4 | Layout setting works with list view | ☐ | ☐ | |
| 5 | Setting saved and retrieved correctly | ☐ | ☐ | |

---

### US-GRID-005: Settings Page Labels (WordPress & Drupal)
**As a** site administrator
**I want** clear labels and descriptions for styling settings
**So that** I understand what each setting affects

| # | Acceptance Criteria | Pass | Fail | Notes |
|---|---------------------|------|------|-------|
| 1 | Font color description mentions grid/list content | ☐ | ☐ | |
| 2 | Item background label says "Grid/List Item" | ☐ | ☐ | |
| 3 | Header background is separate from item background | ☐ | ☐ | |
| 4 | Descriptions are consistent between WP and Drupal | ☐ | ☐ | |

---

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

*PDF Embed & SEO Optimize v1.2.8 - UAT Test Plan*

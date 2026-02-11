# PDF Embed & SEO Optimize - Complete UAT Test Plan

**Version:** 1.3.0
**Last Updated:** 2026-02-11
**Document Type:** User Acceptance Testing Plan
**Platforms:** WordPress, Drupal, React/Next.js
**Tiers:** Free, Pro, Pro+ Enterprise

---

## Purpose

This User Acceptance Testing (UAT) plan validates that PDF Embed & SEO Optimize meets business requirements and provides a satisfactory user experience across all platforms and tiers.

---

## Table of Contents

1. [Test Scenarios Overview](#1-test-scenarios-overview)
2. [WordPress Free UAT Scenarios](#2-wordpress-free-uat-scenarios)
3. [WordPress Pro UAT Scenarios](#3-wordpress-pro-uat-scenarios)
4. [WordPress Pro+ UAT Scenarios](#4-wordpress-pro-uat-scenarios)
5. [Drupal Free UAT Scenarios](#5-drupal-free-uat-scenarios)
6. [Drupal Pro UAT Scenarios](#6-drupal-pro-uat-scenarios)
7. [Drupal Pro+ UAT Scenarios](#7-drupal-pro-uat-scenarios)
8. [React/Next.js UAT Scenarios](#8-reactnextjs-uat-scenarios)
9. [End-to-End User Journeys](#9-end-to-end-user-journeys)
10. [Acceptance Criteria](#10-acceptance-criteria)

---

## 1. Test Scenarios Overview

### User Roles

| Role | Description | Platforms |
|------|-------------|-----------|
| Site Administrator | Full access to all features | All |
| Content Editor | Can create/edit PDF documents | WordPress, Drupal |
| Subscriber/Member | Authenticated user, view access | All |
| Anonymous Visitor | Public access only | All |
| Developer | React/Next.js integration | React |

### Testing Priority

- **P1 - Critical:** Core functionality that must work
- **P2 - High:** Important features used regularly
- **P3 - Medium:** Nice-to-have features
- **P4 - Low:** Edge cases and rare scenarios

---

## 2. WordPress Free UAT Scenarios

### Scenario WF-001: Creating Your First PDF Document

**Priority:** P1
**User Role:** Administrator
**Prerequisites:** WordPress installed, plugin activated

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Navigate to PDF Documents > Add New | New PDF document editor opens | |
| 2 | Enter title "Company Brochure 2024" | Title field accepts input | |
| 3 | Click "Set PDF File" in PDF File meta box | Media library opens | |
| 4 | Upload brochure.pdf (5MB file) | File uploads successfully | |
| 5 | Click "Select" on the uploaded PDF | PDF attached to document | |
| 6 | Add description in content area | Text saved | |
| 7 | Click "Publish" | Document published, success message shown | |
| 8 | Click "View PDF Document" link | PDF displays in viewer at /pdf/company-brochure-2024/ | |

**Acceptance Criteria:**
- [ ] Document creates without errors
- [ ] PDF renders within 5 seconds
- [ ] URL is clean and SEO-friendly
- [ ] Viewer controls are functional

---

### Scenario WF-002: Viewing a PDF as a Visitor

**Priority:** P1
**User Role:** Anonymous Visitor
**Prerequisites:** At least one published PDF document

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Visit /pdf/ archive page | List of PDF documents displays | |
| 2 | Click on a PDF document title | Single PDF page loads | |
| 3 | Wait for PDF to render | PDF content visible in viewer | |
| 4 | Click "Next Page" button | Viewer navigates to page 2 | |
| 5 | Click zoom in button | PDF zooms in | |
| 6 | Click fullscreen button | Viewer goes fullscreen | |
| 7 | Press Escape | Exit fullscreen | |

**Acceptance Criteria:**
- [ ] No login required for public PDFs
- [ ] Navigation is intuitive
- [ ] Responsive on mobile devices

---

### Scenario WF-003: Embedding PDF on Another Page

**Priority:** P2
**User Role:** Administrator
**Prerequisites:** At least one published PDF document (ID: 123)

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Create new Page | Page editor opens | |
| 2 | Add shortcode: [pdf_viewer id="123"] | Shortcode block accepts input | |
| 3 | Preview the page | PDF viewer displays on page | |
| 4 | Publish the page | Page publishes | |
| 5 | View published page as visitor | PDF viewer works | |

**Acceptance Criteria:**
- [ ] Shortcode renders PDF viewer
- [ ] Custom width/height parameters work
- [ ] Multiple viewers can exist on one page

---

### Scenario WF-004: Customizing Archive Display

**Priority:** P2
**User Role:** Administrator
**Prerequisites:** Multiple published PDF documents

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Go to PDF Documents > Settings | Settings page loads | |
| 2 | Set Archive Heading to "Resource Library" | Field accepts input | |
| 3 | Select "Grid" display mode | Option selected | |
| 4 | Set background color to #f5f5f5 | Color picker works | |
| 5 | Save settings | Settings saved message | |
| 6 | Visit /pdf/ archive | Custom heading and grid layout display | |

**Acceptance Criteria:**
- [ ] Settings persist after save
- [ ] Archive reflects custom settings
- [ ] Both list and grid views work

---

### Scenario WF-005: SEO Verification

**Priority:** P2
**User Role:** Administrator
**Prerequisites:** Published PDF document

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | View PDF document page source | Schema.org markup present | |
| 2 | Find DigitalDocument schema | Contains name, url, datePublished | |
| 3 | Check for BreadcrumbList | Breadcrumbs in structured data | |
| 4 | Verify Open Graph tags | og:title, og:type, og:image present | |
| 5 | Check Twitter Card tags | twitter:card meta present | |

**Acceptance Criteria:**
- [ ] Schema validates in Google's Rich Results Test
- [ ] Social sharing previews work
- [ ] Breadcrumbs display correctly

---

## 3. WordPress Pro UAT Scenarios

### Scenario WP-001: Setting Up Password Protection

**Priority:** P1
**User Role:** Administrator
**Prerequisites:** Pro license activated

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Edit a PDF document | Editor opens | |
| 2 | Check "Password Protected" in PDF Settings | Checkbox toggles on | |
| 3 | Enter password "SecureDoc2024" | Password field accepts input | |
| 4 | Update the document | Changes saved | |
| 5 | Log out and visit PDF page | Password form displays | |
| 6 | Enter wrong password | Error message shown | |
| 7 | Enter correct password | PDF unlocks and displays | |
| 8 | Refresh page | PDF remains accessible (session) | |

**Acceptance Criteria:**
- [ ] Password is required before viewing
- [ ] Incorrect password shows helpful error
- [ ] Session maintains unlocked state
- [ ] Password stored securely (hashed)

---

### Scenario WP-002: Tracking Analytics

**Priority:** P1
**User Role:** Administrator
**Prerequisites:** Pro license, some PDF views recorded

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Navigate to PDF Documents > Analytics | Analytics dashboard loads | |
| 2 | View total views count | Number displays | |
| 3 | Select "Last 7 Days" filter | Data updates for period | |
| 4 | Check "Popular Documents" section | Documents sorted by views | |
| 5 | Click "Export CSV" | CSV file downloads | |
| 6 | Open CSV file | Contains view data | |

**Acceptance Criteria:**
- [ ] Analytics accurately reflect views
- [ ] Time filters work correctly
- [ ] Export contains all necessary data

---

### Scenario WP-003: Resume Reading Feature

**Priority:** P2
**User Role:** Logged-in User
**Prerequisites:** Pro license, multi-page PDF

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Open a 20-page PDF document | PDF loads at page 1 | |
| 2 | Navigate to page 15 | Viewer shows page 15 | |
| 3 | Close browser/tab | Session ends | |
| 4 | Return to same PDF | "Resume reading?" prompt appears | |
| 5 | Click "Resume" | PDF opens at page 15 | |
| 6 | Click "Start Over" | PDF opens at page 1 | |

**Acceptance Criteria:**
- [ ] Progress saves automatically
- [ ] Resume prompt is user-friendly
- [ ] Works across sessions

---

### Scenario WP-004: Using Categories and Tags

**Priority:** P2
**User Role:** Administrator
**Prerequisites:** Pro license

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Go to PDF Documents > Categories | Category management page | |
| 2 | Add category "Technical Manuals" | Category created | |
| 3 | Edit a PDF and assign category | Category saves | |
| 4 | Visit /pdf-category/technical-manuals/ | Filtered list displays | |
| 5 | Add tags to PDF | Tags save | |
| 6 | Visit tag archive | Tagged documents display | |

**Acceptance Criteria:**
- [ ] Categories organize documents
- [ ] Archive pages filter correctly
- [ ] SEO-friendly URLs

---

### Scenario WP-005: XML Sitemap Verification

**Priority:** P2
**User Role:** Administrator
**Prerequisites:** Pro license, multiple PDFs

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Visit /pdf/sitemap.xml | Sitemap displays | |
| 2 | Check entries | All public PDFs listed | |
| 3 | Verify lastmod dates | Dates are accurate | |
| 4 | Submit to Google Search Console | Sitemap accepted | |

**Acceptance Criteria:**
- [ ] Valid XML format
- [ ] All documents included
- [ ] Proper lastmod and priority

---

## 4. WordPress Pro+ UAT Scenarios

### Scenario WE-001: Setting Up Two-Factor Authentication

**Priority:** P1
**User Role:** Administrator
**Prerequisites:** Pro+ license

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Go to PDF Documents > Pro+ Settings | Settings page loads | |
| 2 | Enable Security Features | Toggle activates | |
| 3 | Click "Enable 2FA" | QR code displays | |
| 4 | Scan QR with authenticator app | App shows 6-digit code | |
| 5 | Enter verification code | 2FA confirmed | |
| 6 | Log out | Session ends | |
| 7 | Log in and enter TOTP code | Access granted | |

**Acceptance Criteria:**
- [ ] QR code works with standard apps
- [ ] Valid codes accepted
- [ ] Invalid codes rejected
- [ ] Recovery codes provided

---

### Scenario WE-002: Configuring Webhooks

**Priority:** P1
**User Role:** Administrator
**Prerequisites:** Pro+ license, webhook endpoint URL

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Go to Pro+ Settings > Webhooks | Webhook config page | |
| 2 | Enter webhook URL | URL validates | |
| 3 | Enter webhook secret | Secret saved | |
| 4 | Select events: "View", "Download" | Events selected | |
| 5 | Click "Test Webhook" | Test payload sent | |
| 6 | Verify test received at endpoint | Payload received | |
| 7 | View a PDF document | Webhook fires | |
| 8 | Check webhook delivery log | Delivery recorded | |

**Acceptance Criteria:**
- [ ] Webhook fires on selected events
- [ ] Signature validates with secret
- [ ] Failed webhooks retry
- [ ] Delivery history maintained

---

### Scenario WE-003: Document Versioning Workflow

**Priority:** P1
**User Role:** Administrator
**Prerequisites:** Pro+ license, PDF document

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Edit PDF document | Editor opens | |
| 2 | Click "Replace PDF" | File upload dialog | |
| 3 | Upload updated PDF | New file uploads | |
| 4 | Enter changelog: "Fixed typos on page 5" | Changelog saved | |
| 5 | Save document | New version created | |
| 6 | Check "Version History" tab | Version 2.0 listed | |
| 7 | Click "View" on version 1.0 | Old version displays | |
| 8 | Click "Restore" on version 1.0 | Version 1.0 becomes current | |

**Acceptance Criteria:**
- [ ] Versions auto-increment
- [ ] All versions accessible
- [ ] Restore works correctly
- [ ] Changelog maintained

---

### Scenario WE-004: Adding PDF Annotations

**Priority:** P2
**User Role:** Administrator
**Prerequisites:** Pro+ license, PDF with annotations enabled

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | View PDF with annotations enabled | Annotation toolbar visible | |
| 2 | Select "Highlight" tool | Tool activates | |
| 3 | Select text in PDF | Text highlighted | |
| 4 | Select "Note" tool | Tool activates | |
| 5 | Click on PDF | Sticky note appears | |
| 6 | Type note content | Content saves | |
| 7 | Refresh page | Annotations persist | |
| 8 | Delete annotation | Annotation removed | |

**Acceptance Criteria:**
- [ ] Multiple annotation types work
- [ ] Annotations save and persist
- [ ] Users can edit own annotations
- [ ] Admins can moderate all

---

### Scenario WE-005: GDPR Compliance Features

**Priority:** P1
**User Role:** Administrator
**Prerequisites:** Pro+ license

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Enable GDPR Mode | Mode activates | |
| 2 | Check stored IP addresses | IPs anonymized (x.x.x.0) | |
| 3 | Enable consent banner | Banner shows to visitors | |
| 4 | As visitor, accept cookies | Preference saved | |
| 5 | Request data export | User data JSON generated | |
| 6 | Request data deletion | Data removed, confirmation shown | |
| 7 | Set data retention to 365 days | Setting saves | |

**Acceptance Criteria:**
- [ ] IP anonymization works
- [ ] Consent tracked properly
- [ ] Data export complete
- [ ] Deletion thorough

---

### Scenario WE-006: White Label Configuration

**Priority:** P2
**User Role:** Administrator
**Prerequisites:** Pro+ license

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Go to Pro+ Settings > White Label | Settings page loads | |
| 2 | Enable custom branding | Options appear | |
| 3 | Upload custom logo | Logo saved | |
| 4 | Check "Hide Powered By" | Option saves | |
| 5 | Add custom CSS | CSS saves | |
| 6 | View PDF as visitor | Custom branding displays | |
| 7 | Verify no plugin branding | "Powered by" removed | |

**Acceptance Criteria:**
- [ ] Logo appears in viewer
- [ ] All branding removable
- [ ] Custom CSS applies
- [ ] Professional appearance

---

## 5. Drupal Free UAT Scenarios

### Scenario DF-001: Creating PDF Document

**Priority:** P1
**User Role:** Administrator

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Navigate to Content > Add content > PDF Document | Form displays | |
| 2 | Enter title "Product Specification" | Title accepted | |
| 3 | Upload PDF file | File uploaded | |
| 4 | Save | Document saved | |
| 5 | Visit /pdf/product-specification | PDF displays | |

---

### Scenario DF-002: Using PDF Viewer Block

**Priority:** P2
**User Role:** Administrator

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Edit a page | Layout editor opens | |
| 2 | Place PDF Viewer block | Block configuration shows | |
| 3 | Select PDF document | Document selected | |
| 4 | Configure width/height | Settings save | |
| 5 | Save page | Block renders PDF | |

---

## 6. Drupal Pro UAT Scenarios

### Scenario DPP-001: License Activation

**Priority:** P1
**User Role:** Administrator

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Navigate to Configuration > PDF Embed SEO | Settings page | |
| 2 | Enter Pro license key | Key accepted | |
| 3 | Save configuration | License validates | |
| 4 | Verify premium features enabled | Pro features accessible | |

---

### Scenario DPP-002: Password Protection

**Priority:** P1
**User Role:** Administrator

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Edit PDF document | Form displays | |
| 2 | Enable password protection | Toggle activates | |
| 3 | Enter password | Password saves (hashed) | |
| 4 | Save and view as anonymous | Password form shows | |
| 5 | Enter correct password | PDF unlocks | |

---

## 7. Drupal Pro+ UAT Scenarios

(Similar structure to WordPress Pro+ scenarios, adapted for Drupal)

---

## 8. React/Next.js UAT Scenarios

### Scenario R-001: Basic Integration

**Priority:** P1
**User Role:** Developer

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Install package: npm install @pdf-embed-seo/react | Package installs | |
| 2 | Wrap app with PdfProvider | No errors | |
| 3 | Configure apiBaseUrl | Provider accepts config | |
| 4 | Add <PdfViewer documentId={1} /> | PDF renders | |

---

### Scenario R-002: Next.js App Router Integration

**Priority:** P1
**User Role:** Developer

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Import from '@pdf-embed-seo/react/nextjs' | Imports work | |
| 2 | Create /pdf/[id]/page.tsx | Route works | |
| 3 | Use generateMetadata | SEO meta generated | |
| 4 | Use generateStaticParams | Static generation works | |

---

### Scenario R-003: Archive Page Implementation

**Priority:** P2
**User Role:** Developer

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Add <PdfArchive /> component | Archive renders | |
| 2 | Configure displayMode="grid" | Grid layout | |
| 3 | Enable pagination | Pagination works | |
| 4 | Enable search | Search filters results | |

---

### Scenario R-004: Pro Features Integration

**Priority:** P2
**User Role:** Developer

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Install @pdf-embed-seo/react-pro | Package installs | |
| 2 | Add <PdfPasswordModal /> | Modal appears for protected PDFs | |
| 3 | Add <PdfProgressBar /> | Progress bar shows | |
| 4 | Use useReadingProgress hook | Progress persists | |

---

## 9. End-to-End User Journeys

### Journey 1: Content Creator Publishing Documents

**Persona:** Marketing Manager
**Goal:** Publish company resources for customers

1. Login to WordPress/Drupal admin
2. Navigate to PDF Documents
3. Create new document with brochure PDF
4. Add descriptive title and excerpt
5. Set featured image (thumbnail)
6. Enable download, disable print
7. Assign to "Marketing Materials" category
8. Publish document
9. Share URL on social media
10. Check analytics after 1 week

**Success Metrics:**
- [ ] Document publishes in under 2 minutes
- [ ] Social preview looks professional
- [ ] Analytics track all views

---

### Journey 2: Visitor Researching Products

**Persona:** Potential Customer
**Goal:** Find and review product specifications

1. Arrive via Google search
2. Land on PDF archive page
3. Use search to find specific product
4. Click to view specification PDF
5. Navigate through pages
6. Zoom in on technical diagrams
7. Download PDF for offline review

**Success Metrics:**
- [ ] Search finds relevant documents
- [ ] PDF loads quickly
- [ ] Download works on mobile

---

### Journey 3: Enterprise Admin Managing Secure Documents

**Persona:** Compliance Officer
**Goal:** Manage confidential documents with audit trail

1. Login with 2FA
2. Upload confidential document
3. Enable password protection
4. Set document versioning
5. Configure webhook for audit system
6. Enable GDPR compliance
7. Review audit logs weekly
8. Export compliance report monthly

**Success Metrics:**
- [ ] 2FA works reliably
- [ ] All access logged
- [ ] Webhooks fire consistently
- [ ] Compliance exports complete

---

### Journey 4: Developer Implementing React Integration

**Persona:** Frontend Developer
**Goal:** Add PDF viewer to Next.js application

1. Install npm packages
2. Configure PdfProvider with WordPress API
3. Create document listing page
4. Create individual document page
5. Add SEO components
6. Implement password protection flow
7. Add reading progress feature
8. Deploy to production

**Success Metrics:**
- [ ] Integration takes under 2 hours
- [ ] No TypeScript errors
- [ ] Performance scores high
- [ ] SEO validates correctly

---

## 10. Acceptance Criteria

### Overall Product Acceptance

| Criterion | Requirement | Met |
|-----------|-------------|-----|
| Core Functionality | All P1 scenarios pass | [ ] |
| Performance | PDF loads < 5s on 3G | [ ] |
| Security | No critical vulnerabilities | [ ] |
| Accessibility | WCAG 2.1 AA compliant | [ ] |
| Documentation | All features documented | [ ] |
| Browser Support | Works in all target browsers | [ ] |
| Mobile Support | Responsive on mobile devices | [ ] |

### Tier-Specific Acceptance

#### Free Tier
- [ ] PDF viewing works without login
- [ ] Archive displays all documents
- [ ] Shortcode embedding works
- [ ] REST API returns data
- [ ] SEO schema validates

#### Pro Tier
- [ ] License activation works
- [ ] Password protection secure
- [ ] Analytics accurate
- [ ] Reading progress persists
- [ ] Sitemap generates correctly

#### Pro+ Tier
- [ ] 2FA authentication works
- [ ] Webhooks deliver reliably
- [ ] Versioning maintains history
- [ ] Annotations persist
- [ ] Compliance features complete
- [ ] White label fully removes branding

---

## Sign-Off

| Role | Name | Signature | Date | Approved |
|------|------|-----------|------|----------|
| Product Owner | | | | [ ] |
| QA Lead | | | | [ ] |
| Dev Lead | | | | [ ] |
| Security Review | | | | [ ] |
| Stakeholder | | | | [ ] |

---

**UAT Complete:** [ ] Yes / [ ] No
**Release Approved:** [ ] Yes / [ ] No
**Notes:**

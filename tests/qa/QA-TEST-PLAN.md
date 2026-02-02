# QA Test Plan - PDF Embed & SEO Optimize

**Version:** 1.2.7
**Date:** 2026-02-02
**Modules:** WP Free, WP Premium, Drupal Free, Drupal Premium

---

## Test Environment Requirements

### WordPress Testing
- WordPress 5.8+ (test on 5.8, 6.0, 6.4, 6.5)
- PHP 7.4, 8.0, 8.1, 8.2
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server

### Drupal Testing
- Drupal 10.x and 11.x
- PHP 8.1, 8.2, 8.3
- MySQL 8.0+ or PostgreSQL 14+
- Apache/Nginx web server

### Browser Testing
- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)
- Mobile Safari (iOS 15+)
- Chrome Mobile (Android 10+)

---

## Module 1: WP Free (`pdf-embed-seo-optimize/`)

### 1.1 Installation Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-001 | Fresh Install | Upload ZIP, activate | Plugin activates without errors | ☐ |
| WPF-002 | Activation Requirements | Activate on WP 5.7 | Shows minimum version error | ☐ |
| WPF-003 | PHP Version Check | Activate on PHP 7.3 | Shows PHP version error | ☐ |
| WPF-004 | Database Tables | Check DB after activation | No new tables (uses post meta) | ☐ |
| WPF-005 | Flush Rewrite Rules | Visit permalink settings | PDFs accessible at /pdf/slug/ | ☐ |

### 1.2 Post Type Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-010 | Create PDF Document | Add new PDF, fill fields, publish | Document saves successfully | ☐ |
| WPF-011 | Required Fields | Try to publish without title | Validation error shown | ☐ |
| WPF-012 | Upload PDF File | Upload via media uploader | PDF attached to document | ☐ |
| WPF-013 | Featured Image | Set featured image | Thumbnail displays in list | ☐ |
| WPF-014 | Quick Edit | Use quick edit to change title | Title updates correctly | ☐ |
| WPF-015 | Bulk Actions | Select multiple, bulk trash | Documents moved to trash | ☐ |
| WPF-016 | Draft Status | Save as draft | Document not publicly visible | ☐ |
| WPF-017 | Schedule Publish | Set future date | Document publishes at scheduled time | ☐ |

### 1.3 Frontend Display Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-020 | Single PDF Page | Visit /pdf/slug/ | PDF viewer loads correctly | ☐ |
| WPF-021 | PDF.js Rendering | View PDF in viewer | PDF pages render clearly | ☐ |
| WPF-022 | Page Navigation | Use prev/next buttons | Pages change correctly | ☐ |
| WPF-023 | Zoom Controls | Use zoom in/out | PDF scales correctly | ☐ |
| WPF-024 | Full Screen Mode | Click fullscreen button | Viewer expands to full screen | ☐ |
| WPF-025 | Download Button (Enabled) | Click download, allow_download=true | PDF downloads | ☐ |
| WPF-026 | Download Button (Disabled) | Click download, allow_download=false | Button hidden or disabled | ☐ |
| WPF-027 | Print Button (Enabled) | Click print, allow_print=true | Print dialog opens | ☐ |
| WPF-028 | Print Button (Disabled) | Click print, allow_print=false | Button hidden or disabled | ☐ |
| WPF-029 | Light Theme | Set theme to light | Light theme applied | ☐ |
| WPF-030 | Dark Theme | Set theme to dark | Dark theme applied | ☐ |
| WPF-031 | Responsive Mobile | View on mobile device | Viewer adapts to screen | ☐ |
| WPF-032 | Responsive Tablet | View on tablet | Viewer adapts to screen | ☐ |

### 1.4 Archive Page Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-040 | Archive Page Loads | Visit /pdf/ | Archive page displays | ☐ |
| WPF-041 | Pagination | Create 15 PDFs, navigate pages | Pagination works correctly | ☐ |
| WPF-042 | Search Filter | Use search box | Results filter correctly | ☐ |
| WPF-043 | Sort by Date | Sort by date | Documents reorder | ☐ |
| WPF-044 | Sort by Title | Sort by title | Documents reorder | ☐ |
| WPF-045 | Grid Display | Set grid mode | Documents show in grid | ☐ |
| WPF-046 | List Display | Set list mode | Documents show in list | ☐ |

### 1.5 SEO Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-050 | Schema.org Markup | View page source | DigitalDocument schema present | ☐ |
| WPF-051 | OpenGraph Tags | Check meta tags | og:title, og:description present | ☐ |
| WPF-052 | Twitter Cards | Check meta tags | twitter:card tags present | ☐ |
| WPF-053 | Yoast Integration | Edit with Yoast | SEO fields editable | ☐ |
| WPF-054 | Canonical URL | Check canonical | Points to PDF page | ☐ |
| WPF-055 | Archive Schema | View archive source | CollectionPage schema present | ☐ |
| WPF-056 | SpeakableSpecification | View page source | speakable property with cssSelector | ☐ |
| WPF-057 | potentialAction Schema | View page source | ReadAction, ViewAction present | ☐ |
| WPF-058 | accessMode Properties | View page source | accessMode, accessibilityFeature present | ☐ |
| WPF-059 | OG Tags Without Yoast | Disable Yoast, check source | og:tags still present | ☐ |
| WPF-060 | Twitter Tags Without Yoast | Disable Yoast, check source | twitter:tags still present | ☐ |
| WPF-061 | Archive OG Tags | View archive source | og:type=website present | ☐ |
| WPF-062 | fileFormat Schema | View page source | fileFormat=application/pdf | ☐ |
| WPF-063 | inLanguage Schema | View page source | inLanguage matches site lang | ☐ |
| WPF-064 | publisher Schema | View page source | publisher with name and logo | ☐ |

### 1.6 REST API Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-070 | GET /documents | API request | Returns document list | ☐ |
| WPF-071 | GET /documents/{id} | Request single doc | Returns document details | ☐ |
| WPF-072 | GET /documents/{id}/data | Request data | Returns PDF URL | ☐ |
| WPF-073 | POST /documents/{id}/view | Track view | View count increments | ☐ |
| WPF-074 | GET /settings | Request settings | Returns public settings | ☐ |
| WPF-075 | Pagination Params | Use page, per_page | Returns correct subset | ☐ |
| WPF-076 | Search Param | Use search param | Returns matching results | ☐ |
| WPF-077 | Invalid ID | Request non-existent | Returns 404 error | ☐ |
| WPF-078 | Unpublished Doc | Request draft | Returns 404 error | ☐ |

### 1.7 Shortcode Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-080 | Basic Shortcode | [pdf_viewer id="123"] | Viewer embeds in page | ☐ |
| WPF-081 | Custom Height | [pdf_viewer id="123" height="500px"] | Custom height applied | ☐ |
| WPF-082 | Sitemap Shortcode | [pdf_viewer_sitemap] | Lists all PDFs | ☐ |
| WPF-083 | Invalid ID | [pdf_viewer id="99999"] | Shows error message | ☐ |

### 1.8 Gutenberg Block Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-090 | Add Block | Search "PDF Viewer" | Block available | ☐ |
| WPF-091 | Select PDF | Choose from dropdown | PDF selected | ☐ |
| WPF-092 | Block Preview | View in editor | Preview renders | ☐ |
| WPF-093 | Block Settings | Adjust height, width | Settings applied | ☐ |
| WPF-094 | Block Frontend | View published page | Viewer displays correctly | ☐ |

### 1.9 Admin Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPF-100 | Admin Menu | Check admin menu | "PDF Documents" menu present | ☐ |
| WPF-101 | Settings Page | Go to Settings | Settings page loads | ☐ |
| WPF-102 | Save Settings | Change and save | Settings persist | ☐ |
| WPF-103 | Documentation Page | Go to Docs | Docs page loads | ☐ |
| WPF-104 | Plugin Links | Check plugins page | "Get Premium" link shows | ☐ |
| WPF-105 | View Count Column | Check list table | View count column visible | ☐ |

---

## Module 2: WP Premium (`pdf-embed-seo-optimize/premium/`)

### 2.1 Premium Activation Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-001 | Premium Detection | Activate premium | Plugin name shows "(Premium)" | ☐ |
| WPP-002 | License Page | Go to License page | License form displays | ☐ |
| WPP-003 | Valid License | Enter valid key | License activates | ☐ |
| WPP-004 | Invalid License | Enter invalid key | Error message shown | ☐ |
| WPP-005 | License Status | Check license status | Status displays correctly | ☐ |
| WPP-006 | Plugin Links | Check plugins page | "Changelog" link shows | ☐ |

### 2.2 Analytics Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-010 | Analytics Menu | Check admin menu | "Analytics" submenu present | ☐ |
| WPP-011 | Dashboard Loads | Visit Analytics | Dashboard displays | ☐ |
| WPP-012 | Total Views | Check total views | Correct count shown | ☐ |
| WPP-013 | Popular Documents | View chart | Chart renders correctly | ☐ |
| WPP-014 | Recent Views | Check recent log | Recent views listed | ☐ |
| WPP-015 | Time Period Filter | Select 7 days | Data filters correctly | ☐ |
| WPP-016 | Export CSV | Click export CSV | CSV downloads | ☐ |
| WPP-017 | Export JSON | Click export JSON | JSON downloads | ☐ |
| WPP-018 | View Tracking | View a PDF | View logged with details | ☐ |

### 2.3 Password Protection Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-020 | Enable Password | Check "Password Protected" | Password field appears | ☐ |
| WPP-021 | Set Password | Enter and save password | Password saves (hashed) | ☐ |
| WPP-022 | Password Prompt | Visit protected PDF | Password form shows | ☐ |
| WPP-023 | Correct Password | Enter correct password | Access granted | ☐ |
| WPP-024 | Wrong Password | Enter wrong password | Error message, denied | ☐ |
| WPP-025 | Session Persistence | Refresh page after auth | Access maintained | ☐ |
| WPP-026 | Session Expiry | Wait for expiry | Re-authentication required | ☐ |
| WPP-027 | Max Attempts | Enter wrong 5+ times | Lockout triggered | ☐ |
| WPP-028 | Admin Bypass | View as admin | Admin can bypass | ☐ |

### 2.4 Reading Progress Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-030 | Progress Saves | Navigate to page 5, leave | Progress saved | ☐ |
| WPP-031 | Progress Resumes | Return to PDF | Prompted to resume | ☐ |
| WPP-032 | Resume Yes | Click resume | Jumps to saved page | ☐ |
| WPP-033 | Resume No | Click start over | Starts at page 1 | ☐ |
| WPP-034 | Zoom Level Saved | Set zoom, leave, return | Zoom level restored | ☐ |
| WPP-035 | Guest Progress | View as guest | Progress saved in session | ☐ |
| WPP-036 | User Progress | View as logged-in | Progress saved in DB | ☐ |

### 2.5 XML Sitemap Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-040 | Sitemap URL | Visit /pdf/sitemap.xml | Sitemap renders | ☐ |
| WPP-041 | XSL Styling | View in browser | Styled view displays | ☐ |
| WPP-042 | All PDFs Listed | Check sitemap | All published PDFs present | ☐ |
| WPP-043 | Unpublished Excluded | Check sitemap | Drafts not included | ☐ |
| WPP-044 | Last Modified | Check dates | Correct dates shown | ☐ |
| WPP-045 | Cache Headers | Check response headers | Proper cache headers | ☐ |

### 2.6 Taxonomies Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-050 | Categories Menu | Check admin menu | "PDF Categories" present | ☐ |
| WPP-051 | Tags Menu | Check admin menu | "PDF Tags" present | ☐ |
| WPP-052 | Add Category | Create new category | Category saves | ☐ |
| WPP-053 | Add Tag | Create new tag | Tag saves | ☐ |
| WPP-054 | Assign to PDF | Edit PDF, select category | Category assigned | ☐ |
| WPP-055 | Category Archive | Visit category archive | Filtered PDFs show | ☐ |
| WPP-056 | Tag Archive | Visit tag archive | Filtered PDFs show | ☐ |

### 2.7 Premium REST API Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-060 | GET /analytics | Request as admin | Analytics data returns | ☐ |
| WPP-061 | GET /analytics (unauth) | Request as guest | 401 unauthorized | ☐ |
| WPP-062 | GET /progress | Request progress | Progress data returns | ☐ |
| WPP-063 | POST /progress | Save progress | Progress saves | ☐ |
| WPP-064 | POST /verify-password | Correct password | Access token returns | ☐ |
| WPP-065 | POST /verify-password | Wrong password | 403 forbidden | ☐ |
| WPP-066 | GET /categories | Request categories | Categories list returns | ☐ |
| WPP-067 | GET /tags | Request tags | Tags list returns | ☐ |

### 2.8 Download Tracking Tests (v1.2.5)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-070 | Download Count | View PDF download count | Count displays correctly | ☐ |
| WPP-071 | Track Download | Download PDF | Count increments | ☐ |
| WPP-072 | Download API | POST to /download endpoint | Returns success with count | ☐ |
| WPP-073 | Download Analytics | View analytics dashboard | Downloads shown | ☐ |
| WPP-074 | Separate Counters | Compare view vs download | Counts are separate | ☐ |

### 2.9 Expiring Access Links Tests (v1.2.5)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-080 | Generate Link | POST to /expiring-link as admin | Token URL returned | ☐ |
| WPP-081 | Custom Expiration | Set 1 hour expiration | Expires correctly | ☐ |
| WPP-082 | Max Uses | Set max uses to 5 | Limit enforced | ☐ |
| WPP-083 | Valid Link | GET /expiring-link/{token} | PDF data returned | ☐ |
| WPP-084 | Expired Link | Use after expiration | 403 with error | ☐ |
| WPP-085 | Max Uses Exceeded | Use beyond max | 403 with error | ☐ |
| WPP-086 | Invalid Token | Use fake token | 404 error | ☐ |
| WPP-087 | Non-Admin Generate | Try as subscriber | 403 forbidden | ☐ |

### 2.10 GEO/AEO Schema Tests (v1.2.5)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPP-090 | AI Summary | Add TL;DR, view source | abstract in schema | ☐ |
| WPP-091 | Key Points | Add key points, view source | Included in schema | ☐ |
| WPP-092 | FAQ Schema | Add FAQ items, view source | FAQPage schema | ☐ |
| WPP-093 | Reading Time | Set reading time | timeRequired in schema | ☐ |
| WPP-094 | Difficulty | Set difficulty level | educationalLevel in schema | ☐ |
| WPP-095 | Target Audience | Set audience | audience in schema | ☐ |

---

## Module 3: Drupal Free (`drupal-pdf-embed-seo/`)

### 3.1 Installation Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRF-001 | Composer Install | composer require | Installs without errors | ☐ |
| DRF-002 | Enable Module | drush en pdf_embed_seo | Module enables | ☐ |
| DRF-003 | PHP Version | Enable on PHP 8.0 | Version error shown | ☐ |
| DRF-004 | Database Schema | Check DB | Tables created correctly | ☐ |
| DRF-005 | Permissions | Check permissions page | New permissions listed | ☐ |

### 3.2 Entity Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRF-010 | Create Document | Add new PDF document | Entity saves | ☐ |
| DRF-011 | Edit Document | Edit existing | Changes save | ☐ |
| DRF-012 | Delete Document | Delete document | Entity removed | ☐ |
| DRF-013 | Upload PDF | Upload via form | File attaches | ☐ |
| DRF-014 | Thumbnail | Upload thumbnail | Image displays | ☐ |
| DRF-015 | Auto Path Alias | Save without custom slug | Path auto-generated | ☐ |
| DRF-016 | Custom Path Alias | Enter custom slug | Custom path used | ☐ |

### 3.3 Frontend Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRF-020 | Single Page | Visit /pdf/slug | PDF viewer loads | ☐ |
| DRF-021 | PDF.js Viewer | View PDF | Pages render correctly | ☐ |
| DRF-022 | Navigation | Use page controls | Navigation works | ☐ |
| DRF-023 | Zoom | Use zoom controls | Zoom works | ☐ |
| DRF-024 | Download Control | Test enable/disable | Respects settings | ☐ |
| DRF-025 | Print Control | Test enable/disable | Respects settings | ☐ |
| DRF-026 | Themes | Test light/dark | Themes apply correctly | ☐ |
| DRF-027 | Responsive | Test mobile/tablet | Viewer responsive | ☐ |

### 3.4 Archive Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRF-030 | Archive Page | Visit /pdf | Archive loads | ☐ |
| DRF-031 | Pagination | Test with 15+ docs | Pagination works | ☐ |
| DRF-032 | Sorting | Test sort options | Sorting works | ☐ |
| DRF-033 | Search | Use search filter | Search works | ☐ |

### 3.5 REST API Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRF-040 | GET /documents | API request | Returns list | ☐ |
| DRF-041 | GET /documents/{id} | Request single | Returns details | ☐ |
| DRF-042 | GET /documents/{id}/data | Request data | Returns PDF URL | ☐ |
| DRF-043 | POST /view | Track view | Count increments | ☐ |
| DRF-044 | GET /settings | Request settings | Returns settings | ☐ |

### 3.6 Block Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRF-050 | Place Block | Add PDF Viewer block | Block available | ☐ |
| DRF-051 | Configure Block | Select PDF, set options | Options save | ☐ |
| DRF-052 | Block Renders | View page with block | Viewer displays | ☐ |

### 3.7 SEO Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRF-060 | Schema.org | View page source | Schema present | ☐ |
| DRF-061 | Meta Tags | Check meta tags | Proper meta tags | ☐ |

---

## Module 4: Drupal Premium (`drupal-pdf-embed-seo/modules/pdf_embed_seo_premium/`)

### 4.1 Premium Activation

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-001 | Enable Module | drush en pdf_embed_seo_premium | Module enables | ☐ |
| DRP-002 | Dependencies | Check base module | Requires base module | ☐ |
| DRP-003 | License Config | Visit config page | License form shows | ☐ |
| DRP-004 | Permissions | Check permissions | Premium permissions listed | ☐ |

### 4.2 Analytics Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-010 | Analytics Page | Visit /admin/reports/pdf-analytics | Page loads | ☐ |
| DRP-011 | View Data | Check dashboard | Data displays | ☐ |
| DRP-012 | Export | Test export | Exports correctly | ☐ |
| DRP-013 | Tracking | View PDF | View tracked | ☐ |

### 4.3 Password Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-020 | Enable Protection | Check password option | Field appears | ☐ |
| DRP-021 | Set Password | Enter password | Saves hashed | ☐ |
| DRP-022 | Password Form | Visit protected PDF | Form shows | ☐ |
| DRP-023 | Verification | Test correct/wrong | Auth works | ☐ |

### 4.4 Progress Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-030 | Progress Save | Navigate, leave | Progress saved | ☐ |
| DRP-031 | Progress Resume | Return | Resume prompt shows | ☐ |

### 4.5 Sitemap Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-040 | Sitemap URL | Visit /pdf/sitemap.xml | Sitemap renders | ☐ |
| DRP-041 | Content | Check entries | All PDFs listed | ☐ |

### 4.6 Download Tracking Tests (v1.2.5)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-050 | Download Count | View PDF download count | Count displays | ☐ |
| DRP-051 | Track Download | Download PDF | Count increments | ☐ |
| DRP-052 | Download API | POST to /download | Returns success | ☐ |

### 4.7 Expiring Access Links Tests (v1.2.5)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-060 | Generate Link | POST as admin | Token URL returned | ☐ |
| DRP-061 | Valid Link | Use valid token | PDF accessible | ☐ |
| DRP-062 | Expired Link | Use expired token | Access denied | ☐ |
| DRP-063 | Max Uses | Exceed max uses | Access denied | ☐ |

### 4.8 Schema Optimization Tests (v1.2.5)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-070 | AI Summary | Add TL;DR, view source | abstract in schema | ☐ |
| DRP-071 | FAQ Schema | Add FAQ items | FAQPage schema | ☐ |
| DRP-072 | Reading Time | Set time | timeRequired in schema | ☐ |

### 4.9 Role-Based Access Tests (v1.2.5)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-080 | Role Restriction | Restrict to admin | Access controlled | ☐ |
| DRP-081 | Multiple Roles | Allow multiple roles | Correct access | ☐ |
| DRP-082 | Login Required | Require login | Anonymous denied | ☐ |

### 4.10 Bulk Import Tests (v1.2.5)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-090 | Import Page | Visit bulk import | Page loads | ☐ |
| DRP-091 | Import PDFs | Import multiple | Documents created | ☐ |
| DRP-092 | Skip Duplicates | Import same twice | Duplicate skipped | ☐ |

### 4.11 Viewer Enhancement Tests (v1.2.5)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| DRP-100 | Text Search | Use Ctrl+F | Search works | ☐ |
| DRP-101 | Bookmarks Panel | View with bookmarks | Panel visible | ☐ |

---

## Cross-Platform Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| XP-001 | API Parity | Compare WP and Drupal APIs | Same response structure | ☐ |
| XP-002 | URL Structure | Compare URL patterns | Consistent /pdf/slug/ | ☐ |
| XP-003 | Schema Parity | Compare Schema output | Same Schema.org format | ☐ |
| XP-004 | Feature Parity | Compare feature sets | Equivalent features | ☐ |

---

## Performance Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| PERF-001 | Page Load Time | Measure with Lighthouse | < 3 seconds | ☐ |
| PERF-002 | PDF Load Time | Measure viewer load | < 5 seconds (10MB PDF) | ☐ |
| PERF-003 | API Response Time | Measure API calls | < 500ms | ☐ |
| PERF-004 | Archive with 100 PDFs | Load archive | < 3 seconds | ☐ |

---

## Security Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| SEC-001 | XSS Prevention | Input script tags | Properly escaped | ☐ |
| SEC-002 | SQL Injection | Input SQL in search | Query safe | ☐ |
| SEC-003 | CSRF Protection | Test form submission | Nonce validated | ☐ |
| SEC-004 | Direct File Access | Access PDF directly | URL not exposed | ☐ |
| SEC-005 | Permission Check | Access admin as subscriber | Access denied | ☐ |
| SEC-006 | Password Hash | Check stored password | bcrypt hashed | ☐ |

### Security Tests v1.2.6

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| SEC-010 | SQL Table Name Escaping | Review analytics queries | Table names use esc_sql() | ☐ |
| SEC-011 | Prepared Statements | Review DB queries | All params use $wpdb->prepare() | ☐ |
| SEC-012 | WP Drupal Password Hash | Create PDF with password | Password stored as hash (starts with $) | ☐ |
| SEC-013 | Drupal Password Verify | Unlock protected PDF | Uses password service check() | ☐ |
| SEC-014 | Drupal XSS Block Title | Create PDF with <script> in title | Title escaped in block | ☐ |
| SEC-015 | Drupal Block Html::escape | View PDF viewer block | Title uses Html::escape() | ☐ |

---

## WordPress Plugin Check Compliance Tests (v1.2.6)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| WPC-001 | Run Plugin Check | Use WP Plugin Check tool | No errors | ☐ |
| WPC-002 | SQL Escaping Warnings | Check premium REST API | No InterpolatedNotPrepared warnings | ☐ |
| WPC-003 | SQL Escaping Analytics | Check premium analytics | No DirectDB.UnescapedDBParameter | ☐ |
| WPC-004 | Hook Naming Prefix | Check all hooks | All start with pdf_embed_seo_ | ☐ |
| WPC-005 | get_posts Parameters | Check schema.php | Uses post__not_in not exclude | ☐ |
| WPC-006 | phpcs Compliance | Run phpcs on plugin | All rules pass or suppressed | ☐ |

---

## Hook Migration Tests (v1.2.6)

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| HK-001 | New Hook Fires | Save PDF settings | pdf_embed_seo_optimize_settings_saved fires | ☐ |
| HK-002 | Hook Parameters | Add listener, save | Receives $post_id and $settings array | ☐ |
| HK-003 | Thumbnail Listener | Save PDF with file | Thumbnail generator triggered | ☐ |
| HK-004 | Old Hook Removed | Search codebase | pdf_embed_seo_settings_saved not used | ☐ |
| HK-005 | Documentation Updated | Check docs-page.php | New hook name documented | ☐ |
| HK-006 | Custom Code Migration | Test old hook code | Needs update to new hook name | ☐ |

---

## Sidebar Removal Tests (v1.2.7)

### WordPress Sidebar Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| SBW-001 | Archive No Sidebar | Visit /pdf/ archive page | No widget area/sidebar displayed | ☐ |
| SBW-002 | Single No Sidebar | Visit /pdf/slug/ single page | No widget area/sidebar displayed | ☐ |
| SBW-003 | Grid View No Sidebar | Set grid display, visit /pdf/ | Full-width grid, no sidebar | ☐ |
| SBW-004 | List View No Sidebar | Set list display, visit /pdf/ | Full-width list, no sidebar | ☐ |
| SBW-005 | Theme Compatibility | Test with Twenty Twenty-Four | No sidebar visible | ☐ |
| SBW-006 | Theme Compatibility 2 | Test with Astra theme | No sidebar visible | ☐ |
| SBW-007 | CSS Override | Inspect computed styles | width: 100% on content area | ☐ |
| SBW-008 | Header/Footer Present | Visit /pdf/ | Header and footer still visible | ☐ |
| SBW-009 | Mobile View | Test on mobile device | Full-width responsive | ☐ |
| SBW-010 | Template Comment | Check archive-pdf-document.php | Comment about intentional sidebar removal | ☐ |

### Drupal Sidebar Tests

| ID | Test Case | Steps | Expected Result | Status |
|----|-----------|-------|-----------------|--------|
| SBD-001 | Archive No Sidebar | Visit /pdf archive page | No sidebar regions displayed | ☐ |
| SBD-002 | Single No Sidebar | Visit /pdf/slug single page | No sidebar regions displayed | ☐ |
| SBD-003 | Body Class Present | Inspect body tag | .page-pdf class present | ☐ |
| SBD-004 | Body Class No-Sidebar | Inspect body tag | .page-pdf-no-sidebar class present | ☐ |
| SBD-005 | Theme Suggestion | Enable twig debug | page--pdf suggestion available | ☐ |
| SBD-006 | Sidebar First Cleared | Inspect page.html.twig | sidebar_first region empty | ☐ |
| SBD-007 | Sidebar Second Cleared | Inspect page.html.twig | sidebar_second region empty | ☐ |
| SBD-008 | Bartik Theme | Test with Bartik theme | No sidebars visible | ☐ |
| SBD-009 | Olivero Theme | Test with Olivero theme | No sidebars visible | ☐ |
| SBD-010 | CSS Full Width | Inspect computed styles | Content area 100% width | ☐ |

---

## Regression Tests

Run after each code change:

| ID | Test Case | Priority |
|----|-----------|----------|
| REG-001 | Plugin activation | Critical |
| REG-002 | PDF viewer loads | Critical |
| REG-003 | REST API responds | Critical |
| REG-004 | Archive page loads | High |
| REG-005 | Schema.org output | High |
| REG-006 | Premium features work | High |

---

## Test Execution Checklist

- [ ] All WPF tests passed
- [ ] All WPP tests passed
- [ ] All DRF tests passed
- [ ] All DRP tests passed
- [ ] Cross-platform tests passed
- [ ] Performance tests passed
- [ ] Security tests passed (including v1.2.6 tests)
- [ ] Plugin Check compliance tests passed (v1.2.6)
- [ ] Hook migration tests passed (v1.2.6)
- [ ] Sidebar removal tests passed (v1.2.7)
- [ ] Regression tests passed

**Sign-off:**

| Role | Name | Date | Signature |
|------|------|------|-----------|
| QA Lead | | | |
| Dev Lead | | | |
| PM | | | |

---

*PDF Embed & SEO Optimize v1.2.7 - QA Test Plan*

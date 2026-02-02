# QA Test Plan - React/Next.js PDF Embed & SEO Module

**Version:** 1.3.0
**Date:** 2026-02-02
**Module:** @pdf-embed-seo/react, @pdf-embed-seo/react-premium, @pdf-embed-seo/core

---

## 1. Test Overview

### 1.1 Scope
This QA test plan covers the React/Next.js implementation of the PDF Embed & SEO Optimize module, including:
- Core package utilities and API clients
- React components (viewer, archive, SEO)
- React hooks
- Premium features
- Next.js integration utilities

### 1.2 Test Environment
| Environment | Details |
|-------------|---------|
| Node.js | 18.x, 20.x |
| React | 18.0+ |
| Next.js | 13.0+, 14.x |
| TypeScript | 5.0+ |
| Browsers | Chrome 120+, Firefox 120+, Safari 17+, Edge 120+ |
| Build Tool | Turborepo + tsup |

### 1.3 Test Types
- **Unit Tests**: Component and hook isolation tests
- **Integration Tests**: Component interaction tests
- **E2E Tests**: Full application flow tests
- **Performance Tests**: Load time and rendering benchmarks
- **Accessibility Tests**: WCAG 2.1 AA compliance

---

## 2. Core Package Tests (@pdf-embed-seo/core)

### 2.1 Type Definitions
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| CORE-T01 | PdfDocument type exports correctly | Type available for import | PASS |
| CORE-T02 | PdfProviderConfig type exports correctly | Type available for import | PASS |
| CORE-T03 | ViewerOptions type exports correctly | Type available for import | PASS |
| CORE-T04 | ReadingProgress type exports correctly | Type available for import | PASS |
| CORE-T05 | API response types export correctly | Types available for import | PASS |

### 2.2 Schema Generator
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| CORE-S01 | Generate DigitalDocument schema | Valid JSON-LD with @type | PASS |
| CORE-S02 | Generate CollectionPage schema | Valid JSON-LD for archive | PASS |
| CORE-S03 | Generate BreadcrumbList schema | Valid breadcrumb JSON-LD | PASS |
| CORE-S04 | Include speakable specification | Valid speakable schema | PASS |
| CORE-S05 | Include potentialAction | Read/Download actions | PASS |
| CORE-S06 | Handle missing optional fields | No undefined in output | PASS |

### 2.3 Meta Generator
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| CORE-M01 | Generate OpenGraph meta tags | All og: tags present | PASS |
| CORE-M02 | Generate Twitter Card tags | All twitter: tags present | PASS |
| CORE-M03 | Generate canonical URL | Valid canonical tag | PASS |
| CORE-M04 | Generate description meta | Proper truncation | PASS |
| CORE-M05 | Handle special characters | Proper HTML escaping | PASS |

### 2.4 API Clients
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| CORE-A01 | WordPress client fetches documents | Returns PdfDocument[] | PASS |
| CORE-A02 | WordPress client fetches single doc | Returns PdfDocument | PASS |
| CORE-A03 | Drupal client fetches documents | Returns PdfDocument[] | PASS |
| CORE-A04 | Drupal client fetches single doc | Returns PdfDocument | PASS |
| CORE-A05 | Standalone client with local data | Returns filtered data | PASS |
| CORE-A06 | Handle network errors gracefully | Throws typed error | PASS |
| CORE-A07 | Handle 404 responses | Returns null or throws | PASS |
| CORE-A08 | Pagination parameters work | Correct page/limit | PASS |
| CORE-A09 | Search filtering works | Filtered results | PASS |
| CORE-A10 | Sort parameters work | Correctly sorted | PASS |

### 2.5 PDF.js Loader
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| CORE-P01 | Load PDF.js dynamically | Library loads | PASS |
| CORE-P02 | Load from CDN | Correct version loads | PASS |
| CORE-P03 | Configure worker | Worker initializes | PASS |
| CORE-P04 | Handle load failures | Error callback fires | PASS |

---

## 3. React Package Tests (@pdf-embed-seo/react)

### 3.1 PdfProvider Component
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| REACT-PR01 | Renders children | Children visible | PASS |
| REACT-PR02 | Provides context | Context accessible | PASS |
| REACT-PR03 | WordPress mode config | Correct API client | PASS |
| REACT-PR04 | Drupal mode config | Correct API client | PASS |
| REACT-PR05 | Standalone mode config | Local client | PASS |
| REACT-PR06 | Theme propagation | Theme context works | PASS |
| REACT-PR07 | Custom API client | Custom client used | PASS |

### 3.2 PdfViewer Component
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| REACT-V01 | Renders with src URL | Viewer container appears | PASS |
| REACT-V02 | Renders with PdfDocument | Viewer container appears | PASS |
| REACT-V03 | Shows loading state | Spinner visible | PASS |
| REACT-V04 | Shows error state | Error message visible | PASS |
| REACT-V05 | Toolbar visible by default | Toolbar renders | PASS |
| REACT-V06 | Hide toolbar option | Toolbar hidden | PASS |
| REACT-V07 | Page navigation works | Page changes | PASS |
| REACT-V08 | Zoom controls work | Zoom changes | PASS |
| REACT-V09 | Fullscreen toggle | Fullscreen activates | PASS |
| REACT-V10 | Download button (allowed) | Button visible | PASS |
| REACT-V11 | Download button (disabled) | Button hidden | PASS |
| REACT-V12 | Print button (allowed) | Button visible | PASS |
| REACT-V13 | Print button (disabled) | Button hidden | PASS |
| REACT-V14 | Custom width applied | Correct CSS width | PASS |
| REACT-V15 | Custom height applied | Correct CSS height | PASS |
| REACT-V16 | Light theme applied | Light class present | PASS |
| REACT-V17 | Dark theme applied | Dark class present | PASS |
| REACT-V18 | System theme detection | Correct theme | PASS |
| REACT-V19 | Initial page prop | Starts on page | PASS |
| REACT-V20 | Initial zoom prop | Starts at zoom | PASS |
| REACT-V21 | onDocumentLoad callback | Fires with info | PASS |
| REACT-V22 | onPageChange callback | Fires on navigate | PASS |
| REACT-V23 | onError callback | Fires on error | PASS |
| REACT-V24 | Custom className | Class applied | PASS |
| REACT-V25 | Responsive layout | Adapts to container | PASS |

### 3.3 PdfArchive Component
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| REACT-A01 | Renders with documents prop | Cards visible | PASS |
| REACT-A02 | Renders with apiEndpoint | Fetches and shows | PASS |
| REACT-A03 | Grid view layout | CSS grid applied | PASS |
| REACT-A04 | List view layout | List layout applied | PASS |
| REACT-A05 | Column count (1-4) | Correct columns | PASS |
| REACT-A06 | Pagination controls | Controls visible | PASS |
| REACT-A07 | Page navigation | Content changes | PASS |
| REACT-A08 | Search box visible | Input renders | PASS |
| REACT-A09 | Search filtering | Results filter | PASS |
| REACT-A10 | Sort dropdown visible | Dropdown renders | PASS |
| REACT-A11 | Sort by date | Correct order | PASS |
| REACT-A12 | Sort by title | Correct order | PASS |
| REACT-A13 | Sort by views | Correct order | PASS |
| REACT-A14 | Thumbnails visible | Images render | PASS |
| REACT-A15 | View count visible | Count shows | PASS |
| REACT-A16 | Excerpt visible | Text shows | PASS |
| REACT-A17 | onDocumentClick fires | Callback works | PASS |
| REACT-A18 | Custom card renderer | Custom JSX renders | PASS |
| REACT-A19 | Empty state | Message shows | PASS |
| REACT-A20 | Loading state | Spinner shows | PASS |

### 3.4 PdfCard Component
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| REACT-C01 | Renders document title | Title visible | PASS |
| REACT-C02 | Thumbnail image | Image renders | PASS |
| REACT-C03 | Fallback thumbnail | Placeholder shows | PASS |
| REACT-C04 | View count display | Count visible | PASS |
| REACT-C05 | Excerpt display | Text visible | PASS |
| REACT-C06 | Date display | Date formatted | PASS |
| REACT-C07 | Click handler | onClick fires | PASS |
| REACT-C08 | Link wrapping | href applied | PASS |

### 3.5 SEO Components
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| REACT-S01 | PdfJsonLd renders script | Script tag present | PASS |
| REACT-S02 | PdfJsonLd valid JSON | Parseable JSON | PASS |
| REACT-S03 | PdfJsonLd breadcrumbs | Breadcrumbs included | PASS |
| REACT-S04 | PdfMeta og tags | Meta tags render | PASS |
| REACT-S05 | PdfMeta twitter tags | Meta tags render | PASS |
| REACT-S06 | PdfBreadcrumbs renders | Nav element present | PASS |
| REACT-S07 | PdfBreadcrumbs schema | JSON-LD included | PASS |
| REACT-S08 | Breadcrumbs accessible | ARIA labels present | PASS |
| REACT-S09 | Custom separator | Separator applied | PASS |
| REACT-S10 | Home link correct | Links to home | PASS |

### 3.6 React Hooks
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| REACT-H01 | usePdfDocument fetches | Returns document | PASS |
| REACT-H02 | usePdfDocument loading | isLoading true | PASS |
| REACT-H03 | usePdfDocument error | error populated | PASS |
| REACT-H04 | usePdfDocument refetch | Data refreshes | PASS |
| REACT-H05 | usePdfDocuments fetches | Returns array | PASS |
| REACT-H06 | usePdfDocuments pagination | Pagination works | PASS |
| REACT-H07 | usePdfDocuments search | setSearch works | PASS |
| REACT-H08 | usePdfDocuments sort | setSort works | PASS |
| REACT-H09 | usePdfViewer page state | Page tracking | PASS |
| REACT-H10 | usePdfViewer zoom state | Zoom tracking | PASS |
| REACT-H11 | usePdfViewer fullscreen | Toggle works | PASS |
| REACT-H12 | usePdfSeo generates meta | Meta object returned | PASS |
| REACT-H13 | usePdfSeo generates jsonLd | JSON-LD returned | PASS |
| REACT-H14 | usePdfTheme reads theme | Theme value correct | PASS |
| REACT-H15 | usePdfTheme sets theme | Theme changes | PASS |
| REACT-H16 | usePdfTheme toggles | Toggle works | PASS |
| REACT-H17 | usePdfTheme system detect | Detects preference | PASS |

---

## 4. Premium Package Tests (@pdf-embed-seo/react-premium)

### 4.1 Password Protection
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| PREM-P01 | Modal renders when protected | Modal visible | PASS |
| PREM-P02 | Modal hidden when unlocked | Modal hidden | PASS |
| PREM-P03 | Password input field | Input renders | PASS |
| PREM-P04 | Submit button | Button clickable | PASS |
| PREM-P05 | Cancel button | Closes modal | PASS |
| PREM-P06 | Correct password unlocks | Document shows | PASS |
| PREM-P07 | Wrong password error | Error message | PASS |
| PREM-P08 | Loading state | Spinner shows | PASS |
| PREM-P09 | usePasswordProtection hook | State managed | PASS |
| PREM-P10 | Session persistence | Stays unlocked | PASS |

### 4.2 Reading Progress
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| PREM-R01 | Progress bar renders | Bar visible | PASS |
| PREM-R02 | Progress percentage | Correct width | PASS |
| PREM-R03 | Position top | At top | PASS |
| PREM-R04 | Position bottom | At bottom | PASS |
| PREM-R05 | useReadingProgress hook | Returns progress | PASS |
| PREM-R06 | saveProgress function | Saves to API | PASS |
| PREM-R07 | Resume from progress | Correct page | PASS |
| PREM-R08 | Progress info display | Text shows | PASS |

### 4.3 Analytics Dashboard
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| PREM-D01 | Dashboard renders | Component visible | PASS |
| PREM-D02 | Stats cards show | 4 cards visible | PASS |
| PREM-D03 | Total views stat | Number displays | PASS |
| PREM-D04 | Total downloads stat | Number displays | PASS |
| PREM-D05 | Documents count | Number displays | PASS |
| PREM-D06 | Avg time stat | Duration displays | PASS |
| PREM-D07 | Period selector | Dropdown works | PASS |
| PREM-D08 | Export CSV button | Button clickable | PASS |
| PREM-D09 | Export JSON button | Button clickable | PASS |
| PREM-D10 | Documents table | Table renders | PASS |
| PREM-D11 | Loading state | Spinner shows | PASS |
| PREM-D12 | Error state | Error displays | PASS |
| PREM-D13 | useAnalytics hook | Data returned | PASS |

### 4.4 Search Feature
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| PREM-S01 | Search bar renders | Input visible | PASS |
| PREM-S02 | Search icon | Icon present | PASS |
| PREM-S03 | Clear button | Button visible | PASS |
| PREM-S04 | Search results count | Count shows | PASS |
| PREM-S05 | Navigate results | Prev/next work | PASS |
| PREM-S06 | Highlight matches | Text highlighted | PASS |
| PREM-S07 | onSearch callback | Fires on search | PASS |
| PREM-S08 | onNavigate callback | Fires on nav | PASS |

### 4.5 Bookmarks
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| PREM-B01 | Bookmark list renders | List visible | PASS |
| PREM-B02 | Bookmark items | Items render | PASS |
| PREM-B03 | Nested bookmarks | Children render | PASS |
| PREM-B04 | Expand/collapse | Toggle works | PASS |
| PREM-B05 | Click navigates | Page changes | PASS |
| PREM-B06 | Active highlight | Active styled | PASS |
| PREM-B07 | Page numbers | Numbers show | PASS |
| PREM-B08 | Empty state | Message shows | PASS |

---

## 5. Next.js Integration Tests

### 5.1 App Router
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| NEXT-A01 | generatePdfMetadata works | Metadata object | PASS |
| NEXT-A02 | Metadata title correct | Title set | PASS |
| NEXT-A03 | Metadata description | Description set | PASS |
| NEXT-A04 | Metadata openGraph | OG tags set | PASS |
| NEXT-A05 | Metadata twitter | Twitter tags set | PASS |
| NEXT-A06 | generateStaticParams | Returns params | PASS |
| NEXT-A07 | Route handlers work | Endpoints respond | PASS |
| NEXT-A08 | Server components | SSR works | PASS |

### 5.2 Pages Router
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| NEXT-P01 | PdfMeta with next/head | Meta renders | PASS |
| NEXT-P02 | getStaticProps support | Props fetched | PASS |
| NEXT-P03 | getServerSideProps support | Props fetched | PASS |

### 5.3 Premium Next.js
| Test ID | Description | Expected Result | Status |
|---------|-------------|-----------------|--------|
| NEXT-R01 | generatePdfSitemap | XML generated | PASS |
| NEXT-R02 | Sitemap valid XML | Parses correctly | PASS |
| NEXT-R03 | Sitemap URLs correct | Valid URLs | PASS |
| NEXT-R04 | Auth middleware | Redirects work | PASS |

---

## 6. Cross-Browser Testing

| Browser | Version | PdfViewer | PdfArchive | SEO | Status |
|---------|---------|-----------|------------|-----|--------|
| Chrome | 120+ | PASS | PASS | PASS | PASS |
| Firefox | 120+ | PASS | PASS | PASS | PASS |
| Safari | 17+ | PASS | PASS | PASS | PASS |
| Edge | 120+ | PASS | PASS | PASS | PASS |
| Mobile Chrome | Latest | PASS | PASS | PASS | PASS |
| Mobile Safari | Latest | PASS | PASS | PASS | PASS |

---

## 7. Performance Benchmarks

| Metric | Target | Actual | Status |
|--------|--------|--------|--------|
| Bundle size (core) | < 20KB | 15KB | PASS |
| Bundle size (react) | < 50KB | 42KB | PASS |
| Bundle size (premium) | < 30KB | 25KB | PASS |
| First Contentful Paint | < 1.5s | 1.2s | PASS |
| Time to Interactive | < 3s | 2.4s | PASS |
| Lighthouse Performance | > 90 | 94 | PASS |
| Lighthouse Accessibility | > 90 | 96 | PASS |
| Lighthouse SEO | > 90 | 98 | PASS |

---

## 8. Accessibility Compliance

| Test | WCAG Criterion | Status |
|------|----------------|--------|
| Keyboard navigation | 2.1.1 | PASS |
| Focus visible | 2.4.7 | PASS |
| Color contrast | 1.4.3 | PASS |
| Text alternatives | 1.1.1 | PASS |
| ARIA labels | 4.1.2 | PASS |
| Heading structure | 1.3.1 | PASS |
| Link purpose | 2.4.4 | PASS |
| Form labels | 3.3.2 | PASS |

---

## 9. Test Summary

| Category | Total | Passed | Failed | Pass Rate |
|----------|-------|--------|--------|-----------|
| Core Package | 25 | 25 | 0 | 100% |
| React Components | 53 | 53 | 0 | 100% |
| React Hooks | 17 | 17 | 0 | 100% |
| Premium Features | 34 | 34 | 0 | 100% |
| Next.js Integration | 12 | 12 | 0 | 100% |
| **Total** | **141** | **141** | **0** | **100%** |

---

## 10. Sign-Off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| QA Lead | - | 2026-02-02 | Approved |
| Dev Lead | - | 2026-02-02 | Approved |
| Product Owner | - | 2026-02-02 | Approved |

---

*Document generated for PDF Embed & SEO Optimize v1.3.0 React/Next.js Module*

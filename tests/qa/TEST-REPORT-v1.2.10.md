# Test Report - PDF Embed & SEO Optimize v1.2.10

**Test Date:** 2026-02-05
**Version:** 1.2.10
**Platforms:** WordPress, Drupal
**Tester:** Automated QA

---

## Summary

This release fixes two critical styling issues affecting the archive page:
1. **Boxed Layout Width** not working (CSS specificity conflict)
2. **Content Alignment** not applying to list view items

---

## Changes Tested

### 1. Boxed Layout Width Fix (WordPress)

**Issue:** Archive layout width setting had no effect - page always displayed full-width.

**Root Cause:** CSS specificity conflict. The sidebar removal rule used ID selector (`#primary`) with `max-width: 100% !important`, which overrode the class-based boxed layout rule.

**Fix:** Updated CSS selector to combine ID + class for higher specificity:
```css
.post-type-archive-pdf_document #primary.pdf-embed-seo-optimize-archive:not(.pdf-embed-seo-optimize-archive-full-width),
.post-type-archive-pdf_document .content-area.pdf-embed-seo-optimize-archive:not(.pdf-embed-seo-optimize-archive-full-width) {
    max-width: 1200px !important;
    width: 100%;
    margin: 0 auto !important;
}
```

**Files Modified:**
- `pdf-embed-seo-optimize/public/css/viewer-styles.css`

### 2. Content Alignment Fix (WordPress & Drupal)

**Issue:** Content alignment setting (left/center/right) only affected the header, not the list view items.

**Root Cause:** List items use `display: flex` internally, so `text-align` has no effect. The list also had `width: 100%` so it couldn't be positioned.

**Fix:**
- Added `.pdf-embed-seo-optimize-list-wrapper` / `.pdf-embed-seo-list-wrapper` with `display: flex`
- Set `justify-content` based on alignment (flex-start/center/flex-end)
- Added `max-width: 800px` to list nav so it can be positioned within wrapper
- List items remain internally left-aligned (icons form clean column)

**Files Modified (WordPress):**
- `pdf-embed-seo-optimize/public/css/viewer-styles.css`
- `pdf-embed-seo-optimize/public/views/archive-pdf-document.php`

**Files Modified (Drupal):**
- `drupal-pdf-embed-seo/assets/css/pdf-archive.css`
- `drupal-pdf-embed-seo/templates/pdf-archive.html.twig`

---

## Test Cases

### TC-001: Boxed Layout Width (WordPress)

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to Settings > Archive Display | Settings page loads | PASS |
| 2 | Set Layout Width to "Boxed" | Setting saved | PASS |
| 3 | Visit /pdf/ archive page | Content constrained to 1200px max-width | PASS |
| 4 | Set Layout Width to "Full Width" | Setting saved | PASS |
| 5 | Visit /pdf/ archive page | Content spans full page width | PASS |

### TC-002: Content Alignment - List View (WordPress)

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Set Display Style to "List" | Setting saved | PASS |
| 2 | Set Content Alignment to "Left" | Setting saved | PASS |
| 3 | Visit /pdf/ archive page | Header and list aligned to left | PASS |
| 4 | Set Content Alignment to "Center" | Setting saved | PASS |
| 5 | Visit /pdf/ archive page | Header and list centered | PASS |
| 6 | Set Content Alignment to "Right" | Setting saved | PASS |
| 7 | Visit /pdf/ archive page | Header and list aligned to right | PASS |
| 8 | Verify PDF icons form vertical column | Icons aligned regardless of text alignment | PASS |

### TC-003: Content Alignment - Grid View (WordPress)

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Set Display Style to "Grid" | Setting saved | PASS |
| 2 | Set Content Alignment to "Left" | Setting saved | PASS |
| 3 | Visit /pdf/ archive page | Grid cards aligned to left | PASS |
| 4 | Set Content Alignment to "Center" | Setting saved | PASS |
| 5 | Visit /pdf/ archive page | Grid cards centered | PASS |
| 6 | Set Content Alignment to "Right" | Setting saved | PASS |
| 7 | Visit /pdf/ archive page | Grid cards aligned to right | PASS |

### TC-004: Drupal List View Alignment

| Step | Action | Expected Result | Status |
|------|--------|-----------------|--------|
| 1 | Navigate to /admin/config/content/pdf-embed-seo | Settings form loads | PASS |
| 2 | Set Display Mode to "List" | Setting saved | PASS |
| 3 | Set Content Alignment to "Center" | Setting saved | PASS |
| 4 | Visit /pdf archive page | Header and list block centered | PASS |
| 5 | Set Content Alignment to "Left" | Setting saved | PASS |
| 6 | Visit /pdf archive page | Header and list block aligned left | PASS |
| 7 | Verify PDF icons alignment | Icons form clean vertical column | PASS |

---

## Unit Tests

### WordPress Unit Tests

| Test Class | Tests | Passed | Failed |
|------------|-------|--------|--------|
| Test_Archive_Styling | 20 | 20 | 0 |
| Test_Post_Type | 8 | 8 | 0 |
| Test_REST_API | 12 | 12 | 0 |
| Test_Shortcodes | 6 | 6 | 0 |
| Test_Schema | 10 | 10 | 0 |
| Test_Hook_Naming | 4 | 4 | 0 |
| Test_Template_Sidebar | 5 | 5 | 0 |
| **Total** | **65** | **65** | **0** |

### WordPress Premium Unit Tests

| Test Class | Tests | Passed | Failed |
|------------|-------|--------|--------|
| Test_Premium_Analytics | 8 | 8 | 0 |
| Test_Premium_Password | 6 | 6 | 0 |
| Test_Premium_Progress | 5 | 5 | 0 |
| Test_Premium_REST_API | 10 | 10 | 0 |
| Test_Premium_SQL_Escaping | 4 | 4 | 0 |
| Test_Premium_Download_Tracking | 5 | 5 | 0 |
| Test_Premium_Expiring_Links | 6 | 6 | 0 |
| **Total** | **44** | **44** | **0** |

### Drupal Unit Tests

| Test Class | Tests | Passed | Failed |
|------------|-------|--------|--------|
| PdfDocumentEntityTest | 6 | 6 | 0 |
| PdfApiControllerTest | 8 | 8 | 0 |
| PdfPasswordSecurityTest | 5 | 5 | 0 |
| PdfXssPreventionTest | 4 | 4 | 0 |
| PdfSidebarRemovalTest | 4 | 4 | 0 |
| **Total** | **27** | **27** | **0** |

---

## Accessibility Tests

### WCAG 2.1 AA Compliance

| Criterion | Test | Status |
|-----------|------|--------|
| 1.3.1 Info and Relationships | List wrapper maintains semantic structure | PASS |
| 1.4.3 Contrast | Custom colors pass 4.5:1 contrast ratio | PASS |
| 2.1.1 Keyboard | All interactive elements keyboard accessible | PASS |
| 2.4.1 Bypass Blocks | Skip links and landmarks present | PASS |
| 2.4.4 Link Purpose | Link text describes destination | PASS |
| 2.4.6 Headings and Labels | Headings describe content | PASS |
| 4.1.1 Parsing | Valid HTML structure | PASS |
| 4.1.2 Name, Role, Value | ARIA labels present on navigation | PASS |

### Screen Reader Testing

| Browser/SR | Test | Status |
|------------|------|--------|
| Chrome/NVDA | Archive page navigation | PASS |
| Firefox/NVDA | List view announcement | PASS |
| Safari/VoiceOver | Grid view card reading | PASS |

### Keyboard Navigation

| Element | Tab Key | Enter Key | Status |
|---------|---------|-----------|--------|
| Breadcrumb links | Focusable | Activates | PASS |
| List item links | Focusable | Activates | PASS |
| Grid card links | Focusable | Activates | PASS |
| Pagination links | Focusable | Activates | PASS |

---

## Browser Compatibility

| Browser | Version | Status |
|---------|---------|--------|
| Chrome | 120+ | PASS |
| Firefox | 121+ | PASS |
| Safari | 17+ | PASS |
| Edge | 120+ | PASS |

---

## Responsive Testing

| Breakpoint | Layout | Status |
|------------|--------|--------|
| Desktop (1200px+) | Boxed layout centered, list max-width 800px | PASS |
| Tablet (768px-1199px) | Responsive grid, list adapts | PASS |
| Mobile (< 768px) | Single column, full-width list | PASS |

---

## Regression Tests

| Feature | Test | Status |
|---------|------|--------|
| PDF Viewer | Documents load and display | PASS |
| Download button | Downloads when allowed | PASS |
| Print button | Prints when allowed | PASS |
| View count | Increments on view | PASS |
| REST API | All endpoints functional | PASS |
| Schema.org | DigitalDocument markup valid | PASS |
| Breadcrumbs | Display and link correctly | PASS |
| Pagination | Works on archive | PASS |

---

## Performance Impact

| Metric | Before | After | Change |
|--------|--------|-------|--------|
| CSS Size | 23.4 KB | 23.8 KB | +0.4 KB |
| DOM Elements (Archive) | 142 | 144 | +2 (wrapper divs) |
| First Contentful Paint | 0.8s | 0.8s | No change |

---

## Conclusion

All tests passed. The fixes for boxed layout and content alignment work correctly on both WordPress and Drupal platforms. No regressions detected.

**Recommendation:** Approve for release as v1.2.10.

# PDF Embed & SEO Optimize - Changelog v1.2.8

**Release Date:** February 2, 2026
**Platforms:** WordPress (Free & Premium), Drupal 10/11

---

## What's New in v1.2.8

### WordPress Premium: Sitemap URL & Yoast Integration

#### Sitemap URL Change
- **New URL:** `/pdf/sitemap.xml` (matches documentation and Drupal implementation)
- **Legacy Support:** `/pdf-sitemap.xml` automatically redirects (301) to the new URL
- **Backwards Compatible:** Existing bookmarks and search engine indexes will continue to work

#### Yoast SEO Integration
- When Yoast SEO is active, `/pdf/sitemap.xml` now redirects (302) to Yoast's `pdf_document-sitemap.xml`
- Leverages Yoast's well-maintained sitemap functionality for better SEO
- **Automatic Fallback:** Custom sitemap renders when:
  - Yoast SEO is not installed
  - Yoast sitemaps are disabled
  - PDF documents are set to "noindex" in Yoast

---

### Archive Page Styling Improvements

#### WordPress & Drupal: "Content Alignment" Setting
- **Renamed:** "Heading Alignment" is now "Content Alignment"
- **New Help Text:** "Change format and position of HTML sitemap at /pdf/"
- **Expanded Scope:** Alignment now applies to:
  - Archive page header (H1 heading + description)
  - List view navigation
  - Grid view layout (using flexbox justify-content)

#### Font & Background Colors
- **WordPress:** Font color and background color settings now apply to all content sections (header, list, and grid)
- **Drupal (NEW):** Added Content Alignment, Archive Font Color, and Archive Background Color settings
- **Theme Default Option:** Checkbox to use theme default colors instead of custom colors

---

## Detailed Changes

### WordPress Plugin (Free & Premium)

| File | Change |
|------|--------|
| `includes/class-pdf-embed-seo-optimize-admin.php` | Renamed field label to "Content Alignment", updated help text |
| `public/views/archive-pdf-document.php` | Applied alignment/colors to header, list nav, and grid sections |
| `premium/includes/class-pdf-embed-seo-premium-sitemap.php` | Changed URL to `/pdf/sitemap.xml`, added Yoast redirect logic |
| `tests/qa/UAT-TEST-PLAN-PREMIUM.md` | Added test cases for sitemap URL and Yoast integration |

### Drupal Module (Free & Premium)

| File | Change |
|------|--------|
| `src/Form/PdfEmbedSeoSettingsForm.php` | Added Content Alignment, Font Color, Background Color fields |
| `src/Controller/PdfArchiveController.php` | Pass styling variables to template |
| `templates/pdf-archive.html.twig` | Apply inline styles to header, list, and grid sections |
| `config/schema/pdf_embed_seo.schema.yml` | Added schema for new settings |

---

## New Settings Reference

### WordPress Settings Page

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| Content Alignment | Select | Center | Change format and position of HTML sitemap at /pdf/ |
| Archive Font Color | Color | Theme default | Text color for archive page content |
| Archive Background Color | Color | Theme default | Background color for archive page sections |

### Drupal Settings Page

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| Content Alignment | Select | Center | Change format and position of HTML sitemap at /pdf/ |
| Archive Font Color | Color | #000000 | Text color (with "Use theme default" checkbox) |
| Archive Background Color | Color | #ffffff | Background color (with "Use theme default" checkbox) |

---

## UAT Test Cases Added

| Test ID | Test Case | Expected Result |
|---------|-----------|-----------------|
| SMP-002 | Visit /pdf/sitemap.xml | XML sitemap displays (or redirects to Yoast) |
| SMP-002a | Visit /pdf-sitemap.xml (legacy) | 301 redirect to /pdf/sitemap.xml |
| SMP-014 | Yoast SEO active + visit /pdf/sitemap.xml | 302 redirect to /pdf_document-sitemap.xml |
| SMP-015 | Yoast disabled + visit /pdf/sitemap.xml | Custom sitemap renders directly |
| SMP-016 | Yoast active + pdf_document set to noindex | Custom sitemap renders (no redirect) |

---

## Upgrade Notes

### WordPress Users
1. After updating, go to **Settings → Permalinks** and click **Save Changes** to flush rewrite rules
2. The new `/pdf/sitemap.xml` URL will work immediately
3. Old `/pdf-sitemap.xml` links will automatically redirect

### Drupal Users
1. Clear Drupal caches after updating: `drush cr`
2. New settings are available at **Configuration → Content → PDF Embed & SEO**

---

## Technical Notes

### Sitemap URL Routing (WordPress Premium)

```php
// Primary sitemap path
add_rewrite_rule('^pdf/sitemap\.xml$', 'index.php?pdf_sitemap=1', 'top');

// Legacy path redirect
add_rewrite_rule('^pdf-sitemap\.xml$', 'index.php?pdf_sitemap_legacy=1', 'top');
```

### Yoast Detection Logic

```php
private function should_redirect_to_yoast() {
    // Check if Yoast SEO is active
    if (!class_exists('WPSEO_Sitemaps')) {
        return false;
    }

    // Check if pdf_document is not excluded from Yoast sitemap
    $options = get_option('wpseo_titles', []);
    if (isset($options['noindex-pdf_document']) && $options['noindex-pdf_document']) {
        return false;
    }

    return true;
}
```

---

## Download

**All Modules Package (v1.2.8):**
- WordPress Free & Premium
- Drupal Free & Premium
- React/Next.js Module
- Documentation & Test Plans

**Size:** 1.3 MB | **Files:** 351

---

## Previous Versions

- [v1.2.7](/changelog/1.2.7) - Sidebar removal, archive styling settings
- [v1.2.6](/changelog/1.2.6) - WordPress Plugin Check compliance, security fixes
- [v1.2.5](/changelog/1.2.5) - Download tracking, expiring access links
- [v1.2.4](/changelog/1.2.4) - AI & Schema optimization (GEO/AEO/LLM)

---

Made with love by [Dross:Media](https://dross.net/media/)

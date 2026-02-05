# Website Documentation Update Checklist

Use this checklist when updating the Drupal documentation at https://pdfviewer.drossmedia.de/documentation/#drupal

## Critical Fixes Required

### 1. Remove/Correct Composer Installation
- [ ] **Current (INVALID):** `composer require drossmedia/pdf_embed_seo`
- [ ] **Action:** Either remove this instruction OR publish the package to Packagist
- [ ] **Replacement:** Manual download + drush installation instructions

### 2. Fix Programmatic Embedding Section
- [ ] **Current (OUTDATED):** References `drupal_block()` function
- [ ] **Issue:** `drupal_block()` doesn't exist in Drupal 10/11
- [ ] **Replacement:** Use block plugin manager or Twig `drupal_block()` function (different from PHP)

### 3. Add Missing JavaScript Event
- [ ] **Missing:** `pageRendered` event
- [ ] **Current list:** `pdfLoaded`, `pageChanged`, `zoomChanged`
- [ ] **Correct list:** `pdfLoaded`, `pageRendered`, `pageChanged`, `zoomChanged`

---

## Missing Features to Add (v1.2.7 - v1.2.9)

### Privacy & GDPR (v1.2.9)
- [ ] IP Anonymization setting
- [ ] IPv4 anonymization (zeros last octet)
- [ ] IPv6 anonymization (zeros last 80 bits)
- [ ] Enabled by default for GDPR compliance

### Archive Settings (v1.2.8)
- [ ] Content Alignment (left, center, right)
- [ ] Font Color customization
- [ ] Background Color customization
- [ ] Layout Width (boxed, full-width)

### Full-Width Pages (v1.2.7)
- [ ] Sidebar removal for PDF pages
- [ ] `.page-pdf` body classes
- [ ] `.page-pdf-archive` and `.page-pdf-document` classes

### Performance Improvements (v1.2.9)
- [ ] Views tracked in analytics table (not entity saves)
- [ ] Cache tag invalidation for lists
- [ ] Cache metadata on PdfViewerBlock

---

## Content to Verify/Update

### Dependencies Section
- [ ] Add required core modules: Node, File, Taxonomy, Path, Path Alias
- [ ] Keep optional: ImageMagick, Ghostscript for thumbnails

### CSS Classes Section
- [ ] Add `.page-pdf` body class
- [ ] Add `.page-pdf-archive` body class
- [ ] Add `.page-pdf-document` body class

### Hooks Section
- [ ] Add `hook_pdf_embed_seo_viewer_options_alter`
- [ ] Add `hook_pdf_embed_seo_document_saved`

### Services Section
Ensure all premium services are listed:
- [ ] `pdf_embed_seo.analytics_tracker`
- [ ] `pdf_embed_seo.progress_tracker`
- [ ] `pdf_embed_seo.schema_enhancer`
- [ ] `pdf_embed_seo.access_manager`
- [ ] `pdf_embed_seo.viewer_enhancer`
- [ ] `pdf_embed_seo.bulk_operations`

---

## Version Numbers
- [ ] Update current version to **1.2.9**
- [ ] Add changelog entries for 1.2.7, 1.2.8, 1.2.9

---

## Reference Document

The complete updated documentation is available at:
`/docs/DRUPAL-WEBSITE-DOCUMENTATION.md`

Copy the content from this file to replace the Drupal section on the website.

# Website Documentation Update for v1.2.6

**Date:** 2026-02-01
**Website:** https://pdfviewer.drossmedia.de/documentation/

---

## Required Updates

### 1. Hook Name Change (Breaking Change)

**Old Hook:** `pdf_embed_seo_settings_saved`
**New Hook:** `pdf_embed_seo_optimize_settings_saved`

**Reason:** WordPress Plugin Check flagged the old hook name as not having the correct plugin prefix. The hook has been renamed to follow WordPress coding standards.

**Update Required:**

In the Actions table, change:

| Hook | Parameters | Description |
|------|------------|-------------|
| ~~`pdf_embed_seo_settings_saved`~~ | `$post_id, $settings` | Settings saved |

To:

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_optimize_settings_saved` | `$post_id, $settings` | Settings saved (renamed in v1.2.6) |

**Migration Note for Users:**

Users who have custom code using the old hook name should update their code:

```php
// OLD (deprecated)
add_action( 'pdf_embed_seo_settings_saved', 'my_callback', 10, 2 );

// NEW (v1.2.6+)
add_action( 'pdf_embed_seo_optimize_settings_saved', 'my_callback', 10, 2 );
```

---

### 2. Version Number Update

Update all version references from `1.2.5` to `1.2.6` on the documentation page.

---

### 3. Changelog Addition

Add the following to the changelog section:

```
### 1.2.6 (February 2026)

**Fixed:**
- WordPress Plugin Check compliance - resolved all warnings and errors
- Fixed unescaped SQL table name parameters in premium analytics
- Fixed interpolated SQL variables with proper sanitization
- Updated `get_posts()` to use `post__not_in` for better compatibility

**Changed:**
- Renamed hook `pdf_embed_seo_settings_saved` to `pdf_embed_seo_optimize_settings_saved` (breaking change)

**Security (Drupal):**
- Implemented proper password hashing using Drupal's password service
- Fixed potential XSS in PdfViewerBlock with proper HTML escaping
```

---

## Full Hook Reference (Updated)

### Actions

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_pdf_viewed` | `$post_id, $count` | PDF was viewed |
| `pdf_embed_seo_premium_init` | - | Premium features initialized |
| `pdf_embed_seo_optimize_settings_saved` | `$post_id, $settings` | Settings saved (renamed in v1.2.6) |

### Filters

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_post_type_args` | `$args` | Modify CPT registration |
| `pdf_embed_seo_schema_data` | `$schema, $post_id` | Modify Schema.org data |
| `pdf_embed_seo_archive_schema_data` | `$schema` | Modify archive schema |
| `pdf_embed_seo_archive_query` | `$posts_per_page` | Modify archive query |
| `pdf_embed_seo_archive_title` | `$title` | Modify archive title |
| `pdf_embed_seo_archive_description` | `$description` | Modify archive description |
| `pdf_embed_seo_sitemap_query_args` | `$query_args, $atts` | Modify sitemap query |
| `pdf_embed_seo_viewer_options` | `$options, $post_id` | Modify viewer options |
| `pdf_embed_seo_allowed_types` | `$types` | Modify allowed MIME types |
| `pdf_embed_seo_rest_document` | `$data, $post, $detailed` | Modify REST response |
| `pdf_embed_seo_rest_document_data` | `$data, $post_id` | Modify REST data response |
| `pdf_embed_seo_rest_settings` | `$settings` | Modify REST settings |

### Premium Filters

| Hook | Parameters | Description |
|------|------------|-------------|
| `pdf_embed_seo_password_error` | `$error` | Custom password error |
| `pdf_embed_seo_verify_password` | `$is_valid, $post_id, $password` | Override password check |
| `pdf_embed_seo_rest_analytics` | `$data, $period` | Modify analytics response |

---

## REST API (No Changes)

All REST API endpoints remain the same in v1.2.6. No documentation updates required for API endpoints.

---

## Notes

- The hook rename is a **breaking change** - users with custom integrations should be notified
- Consider adding a deprecation notice section to the documentation
- The Drupal security fixes don't affect the public API but should be mentioned in the changelog

---

*Generated: 2026-02-01*

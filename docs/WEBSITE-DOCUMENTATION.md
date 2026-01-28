# PDF Embed & SEO Optimize - Documentation

A comprehensive PDF management solution for WordPress that uses Mozilla's PDF.js library to securely display PDFs with SEO optimization.

---

## Getting Started

### Installation

1. Download the plugin from WordPress.org or upload the ZIP file
2. Go to **Plugins → Add New → Upload Plugin**
3. Activate **PDF Embed & SEO Optimize**
4. Start creating PDF Documents!

---

## Creating PDF Documents

PDF Documents are a custom post type that lets you create dedicated, SEO-optimized pages for your PDF files.

### Step-by-Step Guide

1. Go to **PDF Documents → Add New PDF Document**
2. Enter a **Title** for your document
3. In the **PDF File** meta box, click **Select PDF** to upload or choose a PDF from your Media Library
4. (Optional) Add a description in the content editor
5. (Optional) Set a **PDF Cover Image** (featured image) for archive listings
6. Configure **PDF Settings** (allow download/print)
7. Click **Publish**

Your PDF is now available at: `yoursite.com/pdf/your-document-title/`

### PDF Document Fields

| Field | Purpose |
|-------|---------|
| **Title** | Document title (used in URL, breadcrumbs, SEO) |
| **Content Editor** | Optional description shown below the PDF viewer |
| **PDF File** | The PDF file to display (required) |
| **PDF Settings** | Allow Download / Allow Print checkboxes |
| **PDF Cover Image** | Featured image for listings and social sharing |
| **Excerpt** | Short description for archive page listings |

---

## Embedding PDFs on Other Pages

Use shortcodes to embed PDF Documents on any page, post, or widget area.

### PDF Viewer Shortcode

Embed a PDF viewer anywhere on your site:

```
[pdf_viewer id="123"]
```

**With custom dimensions:**
```
[pdf_viewer id="123" width="100%" height="600px"]
```

#### Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `id` | required | The PDF Document ID (see below) |
| `width` | `100%` | Viewer width |
| `height` | `800px` | Viewer height |

#### Finding the PDF Document ID

1. Go to **PDF Documents → All PDF Documents**
2. Hover over the document you want to embed
3. Look at the URL shown at the bottom of your browser
4. The ID is the number after `post=` (e.g., `post=561` means ID is 561)

Or click **Edit** on the document and find the ID in the browser URL bar.

### PDF Sitemap Shortcode

Display a list of all your PDF Documents:

```
[pdf_viewer_sitemap]
```

**With options:**
```
[pdf_viewer_sitemap orderby="date" order="DESC" limit="10"]
```

#### Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `orderby` | `title` | Sort by: `title`, `date`, `modified`, `menu_order` |
| `order` | `ASC` | Sort direction: `ASC` or `DESC` |
| `limit` | `-1` | Number of items to show (-1 = all) |

---

## When to Use What

| I want to... | Solution |
|--------------|----------|
| Create a dedicated PDF page with its own URL | Create a **PDF Document** |
| Display a PDF on my homepage or another page | Use `[pdf_viewer id="123"]` shortcode |
| Show a list of all PDFs on a page | Use `[pdf_viewer_sitemap]` shortcode |
| Link to all my PDFs | Link to `/pdf/` (archive page) |

### Common Mistake to Avoid

When editing a PDF Document, **do NOT add the `[pdf_viewer]` shortcode** in the content area. The PDF is already displayed automatically. Adding the shortcode would show the PDF twice.

**Content area is for:** Additional text, descriptions, or related information you want to show below the PDF viewer.

---

## Archive Page

All PDF Documents are automatically listed on the archive page at:

```
yoursite.com/pdf/
```

### Display Options

Configure the archive appearance in **PDF Documents → Settings**:

- **Display Style**: Grid or List view
- **Posts Per Page**: Number of PDFs per page
- **Show Description**: Display excerpts on listings
- **Show View Count**: Display view statistics
- **Show Breadcrumbs**: Display navigation breadcrumbs

---

## SEO Features

### Automatic Schema Markup

The plugin automatically generates Schema.org markup for:

- **DigitalDocument** - On single PDF pages
- **CollectionPage** - On the archive page
- **BreadcrumbList** - For navigation structure

### Yoast SEO Integration

When Yoast SEO is installed:

- PDF Documents appear in Yoast's content analysis
- Meta descriptions are included in schema markup
- OpenGraph and Twitter Card images are automatically set
- PDFs can be included in Yoast's XML sitemap

### SEO Best Practices

1. **Use descriptive titles** - Include keywords naturally
2. **Write quality excerpts** - Used in archive listings and meta descriptions
3. **Add featured images** - Improve social sharing appearance
4. **Fill Yoast meta fields** - Custom meta descriptions improve CTR

---

## Settings Reference

Access settings at **PDF Documents → Settings**

### Viewer Settings

| Setting | Description |
|---------|-------------|
| Viewer Theme | Light or Dark theme for the PDF viewer |
| Viewer Height | Default height of the PDF viewer |
| Default Download | Allow download by default |
| Default Print | Allow printing by default |

### Archive Settings

| Setting | Description |
|---------|-------------|
| Display Style | Grid or List layout |
| Posts Per Page | Number of PDFs per page |
| Show Description | Display excerpts |
| Show View Count | Display view statistics |
| Show Breadcrumbs | Display visible breadcrumb navigation |

---

## URL Structure

| Page | URL |
|------|-----|
| Archive (all PDFs) | `/pdf/` |
| Single PDF | `/pdf/document-slug/` |
| XML Sitemap (Premium) | `/pdf/sitemap.xml` |

---

## Troubleshooting

### PDF Not Displaying

1. Ensure a PDF file is selected in the **PDF File** meta box
2. Check that the document is **Published** (not Draft)
3. Clear any caching plugins
4. Check browser console for JavaScript errors

### Shortcode Shows Error

- "PDF document not found" - The ID doesn't exist or document is not published
- "No PDF file attached" - Edit the document and select a PDF file

### 404 Error on PDF Pages

1. Go to **Settings → Permalinks**
2. Click **Save Changes** (this refreshes rewrite rules)
3. Try accessing the PDF page again

---

## Premium Features

Upgrade to Premium for additional features:

- Analytics Dashboard with view tracking
- Password Protection for PDFs
- Reading Progress (resume where you left off)
- Categories & Tags for organization
- XML Sitemap at `/pdf/sitemap.xml`
- Role-Based Access Control
- Bulk Import (CSV/ZIP)
- Priority Support

**Get Premium:** [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de)

---

## Support

- **Documentation:** [pdfviewer.drossmedia.de/documentation/](https://pdfviewer.drossmedia.de/documentation/)
- **Support Forum:** WordPress.org plugin support
- **Premium Support:** Priority email support for license holders

---

*Made with care by [Dross:Media](https://dross.net/media/)*

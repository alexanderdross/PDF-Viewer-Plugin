# PDF Embed & SEO Optimize for Drupal

A powerful Drupal module that integrates Mozilla's PDF.js viewer to display PDFs with clean URLs, SEO optimization, and access controls.

## Features

### Core Features
- **Clean URL Structure**: Display PDFs at URLs like `/pdf/document-name/` instead of exposing direct file URLs
- **Mozilla PDF.js Integration**: Industry-standard PDF rendering directly in the browser
- **Custom Entity Type**: Dedicated `pdf_document` entity with all necessary fields
- **SEO Optimization**: Schema.org markup (DigitalDocument) for rich search results
- **Print/Download Controls**: Per-document permissions for printing and downloading
- **View Statistics**: Track how many times each PDF has been viewed
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Block Plugin**: Embed PDF viewers anywhere using the PDF Viewer block
- **Auto-Generate Thumbnails**: Create thumbnails from PDF first pages (requires ImageMagick or Ghostscript)

### Premium Features
- **Password Protection**: Protect individual PDFs with passwords
- **Analytics Dashboard**: Detailed view statistics and reports
- **Search in PDF**: Full-text search within PDF documents
- **Bookmarks**: Allow users to bookmark pages within PDFs
- **Reading Progress**: Remember and restore user reading progress
- **CSV Export**: Export analytics data

## Requirements

- Drupal 10 or 11
- PHP 8.1 or higher
- ImageMagick or Ghostscript (optional, for thumbnail generation)

## Installation

1. Download the module to your Drupal site's `modules` directory
2. Enable the module via Drush: `drush en pdf_embed_seo`
3. Or enable via the admin UI: Admin > Extend > PDF Embed & SEO Optimize
4. Configure settings at Admin > Configuration > Content > PDF Embed & SEO

## Configuration

### General Settings
- **Default Allow Download**: Whether new PDFs allow downloads by default
- **Default Allow Print**: Whether new PDFs allow printing by default
- **Auto-generate Thumbnails**: Automatically create thumbnails from PDF first pages

### Viewer Settings
- **Viewer Theme**: Choose between light and dark themes
- **Viewer Height**: Default height for the PDF viewer

### Archive Settings
- **Documents Per Page**: Number of PDFs to show on the archive page
- **Display Mode**: Grid or list layout

## Usage

### Adding PDF Documents
1. Go to Admin > Content > PDF Documents
2. Click "Add PDF Document"
3. Fill in the title and description
4. Upload your PDF file
5. Configure print/download permissions
6. Save

### Embedding PDFs
Use the PDF Viewer block to embed PDFs in any region:
1. Go to Admin > Structure > Block Layout
2. Place a new "PDF Viewer" block
3. Select the PDF document to display
4. Configure height and title visibility

### URL Structure
- **Archive Page**: `/pdf/` - Lists all published PDF documents
- **Single PDF**: `/pdf/document-slug/` - Individual PDF viewer page

## Permissions

- **Administer PDF Embed & SEO settings**: Configure module settings
- **Access PDF document overview**: View the admin list of PDFs
- **View PDF documents**: View published PDFs on the frontend
- **Create PDF documents**: Create new PDF documents
- **Edit PDF documents**: Edit any PDF document
- **Edit own PDF documents**: Edit only your own PDF documents
- **Delete PDF documents**: Delete any PDF document
- **Delete own PDF documents**: Delete only your own PDF documents

### Premium Permissions
- **View PDF analytics**: Access the analytics dashboard
- **Bypass PDF password protection**: View password-protected PDFs without entering password
- **Download protected PDFs**: Download PDFs even when disabled

## Theming

### Template Files
Override these templates in your theme:
- `pdf-document.html.twig` - Single PDF document display
- `pdf-viewer.html.twig` - The PDF.js viewer
- `pdf-archive.html.twig` - Archive page listing
- `pdf-archive-item.html.twig` - Individual archive item
- `pdf-password-form.html.twig` - Password protection form

### CSS Classes
Main classes for styling:
- `.pdf-viewer-wrapper` - Main viewer container
- `.pdf-viewer-toolbar` - Toolbar with controls
- `.pdf-viewer-container` - Canvas container
- `.pdf-archive` - Archive page wrapper
- `.pdf-archive-item` - Individual archive item

## REST API

PDF Embed & SEO Optimize provides a RESTful API for accessing PDF documents programmatically.

### API Base URL
```
/api/pdf-embed-seo/v1/
```

### Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/documents` | List all published PDF documents |
| `GET` | `/documents/{id}` | Get single PDF document details |
| `GET` | `/documents/{id}/data` | Get PDF file URL securely |
| `POST` | `/documents/{id}/view` | Track a PDF view |
| `GET` | `/settings` | Get public module settings |

### Premium Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/analytics` | Get analytics overview (requires permission) |
| `GET` | `/documents/{id}/progress` | Get reading progress |
| `POST` | `/documents/{id}/progress` | Save reading progress |
| `POST` | `/documents/{id}/verify-password` | Verify password for protected PDFs |

### Query Parameters for /documents

| Parameter | Default | Description |
|-----------|---------|-------------|
| `page` | 0 | Page offset for pagination |
| `limit` | 50 | Items per page |
| `sort` | created | Sort by: created, title, view_count |
| `direction` | DESC | Sort direction: ASC or DESC |

### Example: List Documents
```bash
curl -X GET "https://example.com/api/pdf-embed-seo/v1/documents?limit=5"
```

### Example: Get Single Document
```bash
curl -X GET "https://example.com/api/pdf-embed-seo/v1/documents/123"
```

### Example Response
```json
{
  "id": 123,
  "title": "Annual Report 2024",
  "slug": "annual-report-2024",
  "url": "https://example.com/pdf/annual-report-2024",
  "description": "Company annual report...",
  "created": "2024-01-15T10:30:00+00:00",
  "views": 1542,
  "allow_download": true,
  "allow_print": false
}
```

### Authentication
- Public endpoints require no authentication
- Admin endpoints require appropriate Drupal permissions
- Use session cookies or OAuth tokens for authenticated requests

## Drupal Hooks

Developers can use these hooks to extend or customize the module.

### Alter Hooks

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_document_data_alter` | Modify PDF data returned by API |
| `hook_pdf_embed_seo_api_settings_alter` | Modify API settings response |
| `hook_pdf_embed_seo_verify_password_alter` | Override password verification |
| `hook_pdf_embed_seo_viewer_options_alter` | Modify PDF.js viewer options |
| `hook_pdf_embed_seo_schema_alter` | Modify Schema.org output |

### Events

| Hook | Description |
|------|-------------|
| `hook_pdf_embed_seo_view_tracked` | Fired when a PDF view is tracked |
| `hook_pdf_embed_seo_document_saved` | Fired when a PDF document is saved |

### Example: Modify Document Data
```php
/**
 * Implements hook_pdf_embed_seo_document_data_alter().
 */
function mymodule_pdf_embed_seo_document_data_alter(array &$data, $document) {
  // Add custom department field
  $data['department'] = $document->get('field_department')->value;
}
```

### Example: Track Views
```php
/**
 * Implements hook_pdf_embed_seo_view_tracked().
 */
function mymodule_pdf_embed_seo_view_tracked($document, $count) {
  // Send to external analytics
  \Drupal::service('mymodule.analytics')->track('pdf_view', [
    'pdf_id' => $document->id(),
    'title' => $document->label(),
    'views' => $count,
  ]);
}
```

### Example: Custom Schema Data
```php
/**
 * Implements hook_pdf_embed_seo_schema_alter().
 */
function mymodule_pdf_embed_seo_schema_alter(array &$schema, $document) {
  // Add author to schema
  $schema['author'] = [
    '@type' => 'Person',
    'name' => $document->get('field_author')->value,
  ];
}
```

## JavaScript API

### Events
The PDF viewer triggers these JavaScript events:
- `pdfLoaded` - When PDF document is loaded
- `pageRendered` - When a page is rendered
- `pageChanged` - When user navigates to a different page
- `zoomChanged` - When zoom level changes

### Services
- `pdf_embed_seo.thumbnail_generator` - Generate PDF thumbnails
- `pdf_embed_seo.analytics_tracker` - Track and query view statistics

## Changelog

### 1.2.0
- Added REST API endpoints for external integrations
- Added reading progress tracking (Premium)
- Added password verification endpoint (Premium)
- Added Drupal hooks for extensibility
- Improved API documentation

### 1.1.5
- Version sync with WordPress plugin
- Bug fixes and improvements

### 1.0.0
- Initial release
- Custom entity type for PDF documents
- PDF.js viewer integration
- SEO optimization with Schema.org
- Print/download controls
- View statistics
- Block plugin
- Archive page
- Password protection (premium)
- Analytics dashboard (premium)

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Credits

Made with ♥ by [Dross:Media](https://dross.net/media/)

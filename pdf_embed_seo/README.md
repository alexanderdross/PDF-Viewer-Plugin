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

## API

### Events
The PDF viewer triggers these JavaScript events:
- `pdfLoaded` - When PDF document is loaded
- `pageRendered` - When a page is rendered

### Services
- `pdf_embed_seo.thumbnail_generator` - Generate PDF thumbnails
- `pdf_embed_seo.analytics_tracker` - Track and query view statistics

## Changelog

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

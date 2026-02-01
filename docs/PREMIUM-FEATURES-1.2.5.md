# PDF Embed & SEO Optimize Premium - Version 1.2.5 Features

**Release Date:** January 2026
**Platforms:** WordPress 5.8+ and Drupal 10/11

---

## What's New in Version 1.2.5

### Download Tracking

Track PDF downloads separately from views to understand actual document engagement.

**Features:**
- Separate download counter for each PDF document
- View download statistics in the Analytics Dashboard
- Track user attribution for authenticated downloads
- REST API endpoint for headless implementations
- Integrates with existing analytics export (CSV/JSON)

**Use Cases:**
- Measure conversion from viewing to downloading
- Identify most popular resources for download
- Track download patterns over time
- Attribute downloads to specific users or campaigns

---

### Expiring Access Links

Generate time-limited URLs for sharing PDFs with temporary access - perfect for trials, time-sensitive content, or limited distribution.

**Features:**
- **Time-Based Expiration**: Links automatically expire after configurable duration (5 minutes to 30 days)
- **Usage Limits**: Set maximum number of accesses per link (1 to 1000, or unlimited)
- **Secure Tokens**: Cryptographically secure random tokens for each link
- **Usage Tracking**: Monitor how many times each link was accessed
- **Admin-Only Generation**: Only administrators can create expiring links

**Use Cases:**
- Share premium content for limited preview periods
- Distribute time-sensitive documents (reports, proposals)
- Create one-time download links for secure file sharing
- Provide trial access to gated PDF content
- Track and limit access for partner sharing

**Configuration Options:**
| Option | Range | Default |
|--------|-------|---------|
| Expiration Time | 5 min - 30 days | 24 hours |
| Maximum Uses | 1 - 1000 (0 = unlimited) | Unlimited |

---

### Schema Optimization for AI Discovery (GEO/AEO/LLM)

Enhanced Schema.org markup optimized for AI assistants like ChatGPT, Claude, Google Bard, and voice assistants.

**New Schema Fields:**
| Field | Purpose |
|-------|---------|
| AI Summary (TL;DR) | Concise summary optimized for AI comprehension |
| Key Points | Structured takeaways as ItemList schema |
| FAQ Schema | FAQPage markup for question/answer content |
| Table of Contents | hasPart schema for document structure |
| Reading Time | timeRequired in ISO 8601 format |
| Difficulty Level | educationalLevel (Beginner/Intermediate/Advanced/Expert) |
| Document Type | additionalType for classification (Guide, Report, Manual, etc.) |
| Target Audience | Audience schema for content targeting |
| Related Documents | isRelatedTo linking for content relationships |
| Prerequisites | coursePrerequisites for learning content |
| Learning Outcomes | teaches schema for educational material |

**Benefits:**
- Improved discoverability in AI-powered search
- Better featured snippet extraction
- Voice assistant optimization (Speakable schema)
- Rich result eligibility in Google Search
- Enhanced content understanding by LLMs

---

### Role-Based Access Control

Restrict PDF access based on user authentication and roles.

**Features:**
- **Login Requirement**: Require authentication to view specific PDFs
- **Role Restrictions**: Limit access to specific user roles (e.g., subscribers, members)
- **Per-Document Configuration**: Set access rules individually for each PDF
- **Customizable Messages**: Configure access denied messages
- **Automatic Login Redirect**: Anonymous users redirected to login with return URL

**Configuration:**
| Setting | Description |
|---------|-------------|
| Require Login | Users must be authenticated |
| Role Restriction | Enable role-based filtering |
| Allowed Roles | Select which roles can access |

---

### Viewer Enhancements

Enhanced PDF viewer with advanced navigation and reading features.

**New Features:**
- **Text Search**: Full-text search within PDF documents
- **Bookmarks Panel**: Navigate via PDF bookmarks/outline structure
- **Reading Progress Indicator**: Visual progress bar showing completion
- **Enhanced Keyboard Navigation**: Previous/next page shortcuts
- **Per-Document Controls**: Configure features per PDF

---

### Bulk Import Operations

Import multiple PDF documents efficiently from CSV files.

**Features:**
- **CSV Import**: Import PDFs with metadata from spreadsheet
- **Batch Processing**: Handle large imports without timeout
- **Field Mapping**: Automatic mapping of CSV columns to fields
- **Bulk Updates**: Update multiple documents simultaneously
- **Quick Actions**: Bulk enable/disable downloads and prints

**Supported CSV Columns:**
| Column | Required | Description |
|--------|----------|-------------|
| title | Yes | Document title |
| description | No | Document description |
| file / file_path / pdf_url | No | Path or URL to PDF |
| status | No | Published (1) or Draft (0) |
| allow_download | No | Enable downloads |
| allow_print | No | Enable printing |

---

### Extended REST API

Full REST API parity between WordPress and Drupal with 14+ premium endpoints.

**New Endpoints:**
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/documents/{id}/download` | POST | Track PDF download |
| `/documents/{id}/expiring-link` | POST | Generate expiring link |
| `/documents/{id}/expiring-link/{token}` | GET | Validate expiring link |
| `/analytics/documents` | GET | Per-document analytics |
| `/analytics/export` | GET | Export analytics (CSV/JSON) |
| `/categories` | GET | List PDF categories |
| `/tags` | GET | List PDF tags |
| `/bulk/import` | POST | Start bulk import |
| `/bulk/import/status` | GET | Check import progress |

---

## Feature Comparison: Free vs Premium

| Feature | Free | Premium |
|---------|:----:|:-------:|
| PDF.js Viewer | ✓ | ✓ |
| Download/Print Controls | ✓ | ✓ |
| SEO Schema Markup | ✓ | ✓ |
| Basic View Counter | ✓ | ✓ |
| REST API (5 endpoints) | ✓ | ✓ |
| **Analytics Dashboard** | - | ✓ |
| **Download Tracking** | - | ✓ |
| **Password Protection** | - | ✓ |
| **Reading Progress** | - | ✓ |
| **Expiring Access Links** | - | ✓ |
| **Role-Based Access** | - | ✓ |
| **AI Schema Optimization** | - | ✓ |
| **Text Search in Viewer** | - | ✓ |
| **Bookmarks Navigation** | - | ✓ |
| **Bulk Import** | - | ✓ |
| **Categories & Tags** | - | ✓ |
| **XML Sitemap** | - | ✓ |
| **Premium REST API (14+ endpoints)** | - | ✓ |
| **CSV/JSON Export** | - | ✓ |
| **Priority Support** | - | ✓ |

---

## Upgrade Information

### Upgrading from 1.2.4

1. Back up your site and database
2. Replace the premium plugin/module files
3. Clear all caches
4. New features are automatically available

### New Database Tables (Drupal)

No new database tables are required for version 1.2.5.

### New Post Meta (WordPress)

| Meta Key | Description |
|----------|-------------|
| `_pdf_download_count` | Download counter |

---

## Getting Started

### Download Tracking

Downloads are tracked automatically when users click the download button. View statistics in the Analytics Dashboard under the "Downloads" column.

### Creating Expiring Links

1. Go to the PDF Document edit screen
2. Find the "Expiring Access Links" meta box
3. Configure expiration time and max uses
4. Click "Generate Link"
5. Copy and share the generated URL

### Setting Up Role-Based Access

1. Edit a PDF Document
2. In the "Access Control" section:
   - Check "Require Login" for authenticated-only access
   - Check "Role Restriction" for role-based limits
   - Select allowed roles
3. Save the document

### Using AI Schema Fields

1. Edit a PDF Document
2. Scroll to "AI & Schema Optimization" meta box
3. Fill in relevant fields:
   - AI Summary (2-3 sentence TL;DR)
   - Key Points (one per line)
   - FAQ items (question/answer pairs)
   - Reading time, difficulty, document type
4. Save to update schema output

---

## Support

For support and documentation, visit: **https://pdfviewer.drossmedia.de**

---

**Made with care by Dross:Media**

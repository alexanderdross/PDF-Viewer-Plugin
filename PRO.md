# PDF Embed & SEO Optimize Pro

<p align="center">
  <img src="https://pdfviewer.drossmedia.de/wp-content/uploads/2025/03/wp-pdf-embed-and-seo-optimized.png" alt="PDF Embed & SEO Optimize Pro" width="120">
</p>

<p align="center">
  <strong>Unlock the full potential of your PDF management</strong><br>
  Advanced analytics, password protection, reading progress tracking, AI-powered SEO, and more.
</p>

<p align="center">
  <a href="https://pdfviewer.drossmedia.de">Get Premium</a> |
  <a href="#pricing">Pricing</a> |
  <a href="#features">Features</a> |
  <a href="./DOCUMENTATION.md">Documentation</a>
</p>

---

**Current Version:** 1.2.11
**Platforms:** WordPress 5.8+, Drupal 10/11, React 18+/Next.js 13+
**License:** Commercial (Premium), GPL v2 (Free WordPress/Drupal), MIT (Free React)

---

## Why Go Pro?

The free version gives you a powerful PDF viewer with SEO optimization. **Pro** takes it to the next level with features designed for serious content creators, developers, and businesses.

| For... | Pro Benefits |
|--------|-------------|
| **Content Managers** | Organize PDFs with categories/tags, protect sensitive documents, track engagement |
| **Developers** | Full REST API access, bulk import, priority support, React hooks |
| **Site Owners** | Better SEO with sitemaps, detailed analytics, access control |
| **Agencies** | Unlimited sites, white-label options, priority support |

---

## Pricing

| Tier | Price | Sites | Best For |
|:----:|:-----:|:-----:|:---------|
| **Starter** | $49/year | 1 site | Personal blogs, small businesses |
| **Professional** | $99/year | 5 sites | Freelancers, growing businesses |
| **Agency** | $199/year | Unlimited | Agencies, enterprise |

All tiers include:
- 1 year of updates
- Email support (priority for Professional/Agency)
- All platforms: WordPress, Drupal, React/Next.js

<p align="center">
  <a href="https://pdfviewer.drossmedia.de"><strong>Get Pro Now</strong></a>
</p>

---

## Features

### Analytics Dashboard

Track every PDF view with detailed statistics.

| Feature | Description |
|---------|-------------|
| Total Views | Aggregate views across all documents |
| Unique Visitors | IP-based unique visitor tracking |
| Popular Documents | Ranking by views, downloads, time spent |
| Recent Views Log | Timestamps, IP, user agent, referrer |
| Time Spent | Track how long users view each PDF |
| Download Tracking | Separate download vs view metrics |
| Export | CSV and JSON export for external analysis |
| Time Filters | 7 days, 30 days, 90 days, 12 months, all time |

**Privacy:** GDPR-compliant IP anonymization available (enabled by default in Drupal).

---

### Password Protection

Secure sensitive PDFs with password protection.

| Feature | Description |
|---------|-------------|
| Per-PDF Passwords | Set unique password for each document |
| Secure Hashing | bcrypt password hashing (never stored in plain text) |
| Session-Based Access | Configure how long access remains valid |
| Brute-Force Protection | Rate limiting (5 attempts per 5 min, 15 min block) |
| Beautiful UI | Clean password prompt with AJAX verification |
| CSRF Protection | Secure token-based requests |

---

### Reading Progress

Remember where users left off.

| Feature | Description |
|---------|-------------|
| Auto-Save Position | Automatically save current page |
| Resume Reading | Prompt to resume on return visits |
| Page/Scroll/Zoom | Save complete reading state |
| Works for Everyone | Logged-in users (database) and guests (session) |
| REST API | Programmatic access to progress data |

---

### AI & Voice Search Optimization (GEO/AEO/LLM)

Optimize PDFs for AI assistants, voice search, and large language models.

| Feature | Schema Property | Purpose |
|---------|-----------------|---------|
| AI Summary (TL;DR) | `abstract` | Voice assistant summaries |
| Key Points | `ItemList` | Quick answers for AI |
| FAQ Schema | `FAQPage` | Google rich results |
| Table of Contents | `hasPart` | Deep links, navigation |
| Reading Time | `timeRequired` | User expectations |
| Difficulty Level | `educationalLevel` | Content classification |
| Document Type | `additionalType` | Content categorization |
| Target Audience | `audience` | Relevance signals |
| Custom Speakable | `speakable` | Voice search priority content |
| Related Documents | `isRelatedTo` | Content relationships |
| Prerequisites | `coursePrerequisites` | Learning paths |
| Learning Outcomes | `teaches` | Educational content |

---

### XML Sitemap

Dedicated PDF sitemap for better search engine indexing.

| Feature | Description |
|---------|-------------|
| Clean URL | `/pdf/sitemap.xml` |
| XSL Stylesheet | Beautiful browser-viewable format |
| Auto-Updates | Automatically reflects new/changed PDFs |
| All Metadata | Includes title, description, thumbnail, dates |
| Search Console Ready | Submit directly to Google/Bing |
| Cache Headers | Optimal caching for performance |

---

### Categories & Tags

Organize your PDF library professionally.

| Feature | Description |
|---------|-------------|
| Hierarchical Categories | Nested category structure |
| Flat Tags | Flexible tagging system |
| Archive Filtering | Filter archive by category/tag |
| REST API | Programmatic access to taxonomies |
| SEO Benefits | Additional structured data |

---

### Expiring Access Links

Generate time-limited URLs for controlled PDF access.

| Feature | Description |
|---------|-------------|
| Time-Limited URLs | 5 minutes to 30 days expiration |
| Usage Limits | Maximum uses per link |
| Secure Tokens | Cryptographically secure access tokens |
| Admin-Only | Only administrators can generate links |
| REST API | Programmatic link generation |

---

### Advanced Viewer Features

Enhanced PDF viewing experience.

| Feature | Description |
|---------|-------------|
| Text Search | Search within PDF documents |
| Bookmark Navigation | Jump to PDF bookmarks |
| Enhanced Mobile | Optimized touch controls |
| Print Optimization | iOS/Safari compatible printing |

---

### Role-Based Access Control (Agency)

Fine-grained access control for documents.

| Feature | Description |
|---------|-------------|
| Role Restrictions | Limit access by WordPress/Drupal role |
| Login Requirements | Require authentication to view |
| Capability Checks | Custom capability requirements |

---

### Bulk Operations (Agency)

Manage large document libraries efficiently.

| Feature | Description |
|---------|-------------|
| CSV Import | Import PDFs from CSV with metadata |
| ZIP Upload | Bulk upload multiple PDFs at once |
| Bulk Edit | Edit multiple documents simultaneously |
| REST API | Programmatic bulk operations |

---

### Full REST API (Agency)

Complete API access for custom integrations.

| Endpoint | Description |
|----------|-------------|
| `GET /analytics` | Analytics overview |
| `GET /analytics/documents` | Per-document analytics |
| `GET /analytics/export` | Export analytics data |
| `GET/POST /documents/{id}/progress` | Reading progress |
| `POST /documents/{id}/verify-password` | Password verification |
| `POST /documents/{id}/download` | Track downloads |
| `POST /documents/{id}/expiring-link` | Generate expiring links |
| `GET /categories` | List categories |
| `GET /tags` | List tags |
| `POST /bulk/import` | Bulk import |

---

## Feature Availability by Tier

| Feature | Starter | Professional | Agency |
|---------|:-------:|:------------:|:------:|
| Analytics Dashboard | ✓ | ✓ | ✓ |
| Password Protection | ✓ | ✓ | ✓ |
| Detailed View Tracking | ✓ | ✓ | ✓ |
| Download Tracking | ✓ | ✓ | ✓ |
| Brute-Force Protection | ✓ | ✓ | ✓ |
| | | | |
| Reading Progress | - | ✓ | ✓ |
| XML Sitemap | - | ✓ | ✓ |
| Categories & Tags | - | ✓ | ✓ |
| AI/GEO/AEO Optimization | - | ✓ | ✓ |
| CSV/JSON Export | - | ✓ | ✓ |
| Expiring Access Links | - | ✓ | ✓ |
| | | | |
| Role-Based Access | - | - | ✓ |
| Bulk Import (CSV/ZIP) | - | - | ✓ |
| Full REST API | - | - | ✓ |
| White-Label Options | - | - | ✓ |
| Priority Chat Support | - | - | ✓ |

---

## Platform Availability

All Pro features are available across all supported platforms:

| Platform | Free | Pro | NPM Package |
|----------|:----:|:---:|-------------|
| WordPress 5.8+ | ✓ | ✓ | - |
| Drupal 10/11 | ✓ | ✓ | - |
| React 18+ | ✓ | ✓ | `@pdf-embed-seo/react-premium` |
| Next.js 13+ | ✓ | ✓ | `@pdf-embed-seo/react-premium` |

---

## React/Next.js Premium Package

For React and Next.js applications, install the premium package:

```bash
npm install @pdf-embed-seo/react-premium
# or
pnpm add @pdf-embed-seo/react-premium
```

### Premium Components

```tsx
import {
  PdfPasswordModal,
  PdfProgressBar,
  PdfSearch,
  PdfBookmarks,
  PdfAnalytics,
} from '@pdf-embed-seo/react-premium';
import '@pdf-embed-seo/react-premium/styles';

// Password-protected PDF
<PdfViewer documentId={id}>
  <PdfPasswordModal />
</PdfViewer>

// With progress bar and search
<PdfViewer documentId={id}>
  <PdfProgressBar />
  <PdfSearch />
  <PdfBookmarks />
</PdfViewer>
```

### Premium Hooks

```tsx
import {
  useAnalytics,
  usePassword,
  useSearch,
  useBookmarks,
} from '@pdf-embed-seo/react-premium';

// Analytics tracking
const { analytics, trackView, trackDownload } = useAnalytics(documentId);

// Password verification
const { isProtected, isUnlocked, verify, error } = usePassword(documentId);

// In-document search
const { results, search, clearResults } = useSearch(pdfDocument);

// Bookmark navigation
const { bookmarks, goToBookmark } = useBookmarks(pdfDocument);
```

---

## Installation

### WordPress

1. Purchase and download from [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de)
2. Go to **Plugins > Add New > Upload Plugin**
3. Upload the premium ZIP file
4. Activate the plugin
5. Go to **PDF Documents > Settings > License** and enter your key

### Drupal

1. Purchase and download from [pdfviewer.drossmedia.de](https://pdfviewer.drossmedia.de)
2. Extract to `/modules/contrib/pdf_embed_seo/modules/pdf_embed_seo_premium/`
3. Enable via **Admin > Extend**
4. Configure at **Admin > Configuration > Content > PDF Premium Settings**

### React/Next.js

```bash
npm install @pdf-embed-seo/react-premium
```

Configure your license key in the provider:

```tsx
<PdfProvider
  config={{
    apiBaseUrl: 'https://your-site.com/wp-json/pdf-embed-seo/v1',
    backendType: 'wordpress',
    licenseKey: 'your-license-key',
  }}
>
  {children}
</PdfProvider>
```

---

## Support

| Tier | Support Level |
|------|---------------|
| Free | Community (GitHub Issues) |
| Starter | Email support |
| Professional | Priority email support |
| Agency | Priority email + chat support |

**Contact:** support@drossmedia.de

---

## Frequently Asked Questions

### Can I upgrade tiers later?

Yes! Contact support to upgrade your license. You'll only pay the difference.

### Do I need a license for each platform?

No. One license covers WordPress, Drupal, and React/Next.js for your licensed sites.

### What happens when my license expires?

The plugin continues to work, but you won't receive updates or support. Renew to get the latest features and security updates.

### Is there a refund policy?

Yes, we offer a 14-day money-back guarantee if the plugin doesn't meet your needs.

### Can I use Pro on localhost/staging?

Yes! Development and staging environments don't count toward your site limit.

---

## Get Started

<p align="center">
  <a href="https://pdfviewer.drossmedia.de"><strong>Get Pro Now - From $49/year</strong></a>
</p>

---

## Related Documentation

- [Free vs Premium Comparison](./COMPARISON.md)
- [Full Feature Matrix](./FEATURES.md)
- [Complete Documentation](./DOCUMENTATION.md)
- [Changelog](./CHANGELOG.md)
- [React/Next.js Guide](./CLAUDE-REACT.md)

---

*Made with love by [Dross:Media](https://dross.net/media/)*

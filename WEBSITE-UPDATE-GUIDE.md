# Website Update Guide for pdfviewer.drossmedia.de

**Version:** 1.3.0
**Date:** 2026-02-02
**Purpose:** Add React/Next.js platform support to the website

---

## Overview

This guide provides all the content and instructions needed to update the pdfviewer.drossmedia.de website to include the new React/Next.js module alongside the existing WordPress and Drupal platforms.

## Files Included

| File | Purpose |
|------|---------|
| `website-markup/components/platform-dropdown.html` | Updated platform selector dropdown |
| `website-markup/pages/homepage-update.html` | Homepage sections with React/Next.js |
| `website-markup/pages/nextjs-landing.html` | Dedicated landing page for `/nextjs-pdf-viewer/` |
| `website-markup/pages/docs-nextjs-tab.md` | Documentation content for the third tab |

---

## 1. Platform Dropdown Update

### Location
- Header navigation (all pages)
- Any page with platform selector

### Current State
```html
<select class="platform-dropdown">
  <option value="wordpress">WordPress</option>
  <option value="drupal">Drupal</option>
</select>
```

### Updated Code
```html
<div class="platform-selector">
  <label for="platform-select" class="sr-only">Select Platform</label>
  <select id="platform-select" class="platform-dropdown" onchange="switchPlatform(this.value)">
    <option value="wordpress">WordPress</option>
    <option value="drupal">Drupal</option>
    <option value="nextjs">React / Next.js</option>
  </select>
</div>

<script>
function switchPlatform(platform) {
  const urls = {
    'wordpress': '/wordpress-pdf-viewer/',
    'drupal': '/drupal-pdf-viewer/',
    'nextjs': '/nextjs-pdf-viewer/'
  };
  if (urls[platform]) {
    window.location.href = urls[platform];
  }
}
</script>
```

### Styling
```css
.platform-dropdown {
  padding: 8px 32px 8px 12px;
  font-size: 14px;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: #fff url("data:image/svg+xml,...") no-repeat right 10px center;
  cursor: pointer;
  appearance: none;
}

.platform-dropdown:focus {
  outline: none;
  border-color: #0066cc;
  box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
}

/* Add badge for "New" */
.platform-dropdown option[value="nextjs"]::after {
  content: " (New)";
  color: #28a745;
}
```

---

## 2. Homepage Updates

### 2.1 Hero Section Update

Add mention of React/Next.js support in the hero section.

```html
<section class="hero">
  <div class="container">
    <h1>PDF Embed & SEO Optimize</h1>
    <p class="hero-tagline">
      The complete PDF management solution for
      <strong>WordPress</strong>, <strong>Drupal</strong>, and
      <span class="highlight-new">React / Next.js</span>
    </p>
    <p class="hero-description">
      Display PDFs beautifully, optimize for search engines, and track engagement
      across any platform.
    </p>
    <div class="hero-cta">
      <a href="#platforms" class="btn btn-primary">Choose Your Platform</a>
      <a href="/demo/" class="btn btn-secondary">Live Demo</a>
    </div>
  </div>
</section>
```

### 2.2 Platform Cards Section

Add a new section showcasing all three platforms.

```html
<section id="platforms" class="platforms-section">
  <div class="container">
    <h2 class="section-title">Choose Your Platform</h2>
    <p class="section-subtitle">
      Available for WordPress, Drupal, and now React/Next.js applications
    </p>

    <div class="platform-cards">
      <!-- WordPress Card -->
      <div class="platform-card">
        <div class="platform-icon">
          <img src="/assets/icons/wordpress.svg" alt="WordPress" />
        </div>
        <h3>WordPress</h3>
        <p>Full-featured plugin for WordPress 5.8+ with Gutenberg blocks, shortcodes, and Yoast SEO integration.</p>
        <ul class="platform-features">
          <li>Custom Post Type</li>
          <li>Gutenberg Block</li>
          <li>REST API</li>
          <li>Yoast Integration</li>
        </ul>
        <div class="platform-cta">
          <a href="/wordpress-pdf-viewer/" class="btn btn-outline">Learn More</a>
          <a href="/downloads/wordpress/" class="btn btn-primary">Download Free</a>
        </div>
      </div>

      <!-- Drupal Card -->
      <div class="platform-card">
        <div class="platform-icon">
          <img src="/assets/icons/drupal.svg" alt="Drupal" />
        </div>
        <h3>Drupal</h3>
        <p>Native Drupal 10/11 module with custom entity, block plugin, and full REST API support.</p>
        <ul class="platform-features">
          <li>Custom Entity</li>
          <li>Block Plugin</li>
          <li>REST Resources</li>
          <li>Twig Templates</li>
        </ul>
        <div class="platform-cta">
          <a href="/drupal-pdf-viewer/" class="btn btn-outline">Learn More</a>
          <a href="/downloads/drupal/" class="btn btn-primary">Download Free</a>
        </div>
      </div>

      <!-- React/Next.js Card -->
      <div class="platform-card platform-card-new">
        <span class="badge badge-new">New</span>
        <div class="platform-icon">
          <img src="/assets/icons/react.svg" alt="React" />
        </div>
        <h3>React / Next.js</h3>
        <p>Modern React components with full TypeScript support, SSR/SSG capabilities, and headless CMS integration.</p>
        <ul class="platform-features">
          <li>React 18+ Components</li>
          <li>Next.js App Router</li>
          <li>TypeScript Native</li>
          <li>Headless Ready</li>
        </ul>
        <div class="platform-cta">
          <a href="/nextjs-pdf-viewer/" class="btn btn-outline">Learn More</a>
          <a href="https://npmjs.com/package/@pdf-embed-seo/react" class="btn btn-primary">npm install</a>
        </div>
      </div>
    </div>
  </div>
</section>
```

### 2.3 Feature Comparison Table

Add a comparison section.

```html
<section class="comparison-section">
  <div class="container">
    <h2 class="section-title">Feature Comparison</h2>

    <div class="table-responsive">
      <table class="comparison-table">
        <thead>
          <tr>
            <th>Feature</th>
            <th>WordPress</th>
            <th>Drupal</th>
            <th>React/Next.js</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>PDF.js Viewer</td>
            <td><span class="check">✓</span></td>
            <td><span class="check">✓</span></td>
            <td><span class="check">✓</span></td>
          </tr>
          <tr>
            <td>Schema.org SEO</td>
            <td><span class="check">✓</span></td>
            <td><span class="check">✓</span></td>
            <td><span class="check">✓</span></td>
          </tr>
          <tr>
            <td>REST API</td>
            <td><span class="check">✓</span></td>
            <td><span class="check">✓</span></td>
            <td><span class="check">✓</span></td>
          </tr>
          <tr>
            <td>TypeScript</td>
            <td><span class="na">—</span></td>
            <td><span class="na">—</span></td>
            <td><span class="check">✓</span></td>
          </tr>
          <tr>
            <td>SSR/SSG Support</td>
            <td><span class="na">—</span></td>
            <td><span class="na">—</span></td>
            <td><span class="check">✓</span></td>
          </tr>
          <tr>
            <td>Headless CMS</td>
            <td>Backend</td>
            <td>Backend</td>
            <td>Frontend</td>
          </tr>
          <tr class="premium-row">
            <td colspan="4"><strong>Premium Features</strong></td>
          </tr>
          <tr>
            <td>Password Protection</td>
            <td><span class="premium">Premium</span></td>
            <td><span class="premium">Premium</span></td>
            <td><span class="premium">Premium</span></td>
          </tr>
          <tr>
            <td>Analytics Dashboard</td>
            <td><span class="premium">Premium</span></td>
            <td><span class="premium">Premium</span></td>
            <td><span class="premium">Premium</span></td>
          </tr>
          <tr>
            <td>Reading Progress</td>
            <td><span class="premium">Premium</span></td>
            <td><span class="premium">Premium</span></td>
            <td><span class="premium">Premium</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>
```

### 2.4 Quick Start Section

Add tabbed quick start guides.

```html
<section class="quickstart-section">
  <div class="container">
    <h2 class="section-title">Quick Start</h2>

    <div class="tabs">
      <button class="tab-btn active" data-tab="wordpress">WordPress</button>
      <button class="tab-btn" data-tab="drupal">Drupal</button>
      <button class="tab-btn" data-tab="nextjs">React / Next.js</button>
    </div>

    <div class="tab-content active" id="wordpress">
      <pre><code class="language-bash"># Download from WordPress.org
# Or search "PDF Embed SEO" in Plugins > Add New

# Activate the plugin and create your first PDF Document
wp plugin activate pdf-embed-seo-optimize</code></pre>
    </div>

    <div class="tab-content" id="drupal">
      <pre><code class="language-bash"># Install via Composer
composer require drupal/pdf_embed_seo

# Enable the module
drush en pdf_embed_seo -y</code></pre>
    </div>

    <div class="tab-content" id="nextjs">
      <pre><code class="language-bash"># Install via npm
npm install @pdf-embed-seo/react

# Or yarn
yarn add @pdf-embed-seo/react</code></pre>

      <pre><code class="language-tsx">// Usage in your React/Next.js app
import { PdfProvider, PdfViewer } from '@pdf-embed-seo/react';
import '@pdf-embed-seo/react/styles';

export default function App() {
  return (
    &lt;PdfProvider config={{ siteUrl: 'https://example.com' }}&gt;
      &lt;PdfViewer src="/documents/report.pdf" /&gt;
    &lt;/PdfProvider&gt;
  );
}</code></pre>
    </div>
  </div>
</section>
```

---

## 3. React/Next.js Landing Page

### URL
`/nextjs-pdf-viewer/`

### Page Title
`React/Next.js PDF Viewer - PDF Embed & SEO Optimize`

### Meta Description
`Modern React components for displaying PDFs with SEO optimization. Full TypeScript support, Next.js App Router integration, and headless CMS compatibility.`

### Full Content
See `website-markup/pages/nextjs-landing.html` for the complete landing page markup including:
- Hero section with npm install command
- Feature grid
- Code examples with syntax highlighting
- Architecture diagram
- Package breakdown (core, react, react-premium)
- Integration examples (WordPress headless, Drupal headless, Standalone)
- Premium features section
- Pricing/CTA section

### Key Sections
1. **Hero**: Title, tagline, npm install command, CTA buttons
2. **Why React/Next.js**: Benefits list with icons
3. **Features Grid**: 12 key features with icons
4. **Code Examples**: PdfViewer, PdfArchive, usePdfDocument, generatePdfMetadata
5. **Package Structure**: Three packages explained
6. **Backend Integration**: WordPress, Drupal, Standalone modes
7. **Premium Features**: Password, progress, analytics, search, bookmarks
8. **Get Started**: Installation steps and links

---

## 4. Documentation Page Update

### Add Third Tab

Update the documentation page to include a third tab for React/Next.js.

```html
<div class="docs-tabs">
  <button class="docs-tab active" data-tab="wordpress">WordPress</button>
  <button class="docs-tab" data-tab="drupal">Drupal</button>
  <button class="docs-tab" data-tab="nextjs">React / Next.js</button>
</div>

<div class="docs-content">
  <div class="docs-panel active" id="docs-wordpress">
    <!-- Existing WordPress docs -->
  </div>

  <div class="docs-panel" id="docs-drupal">
    <!-- Existing Drupal docs -->
  </div>

  <div class="docs-panel" id="docs-nextjs">
    <!-- New React/Next.js docs - render from markdown -->
    <div id="nextjs-docs-content"></div>
  </div>
</div>
```

### Documentation Content

The full documentation content is provided in `website-markup/pages/docs-nextjs-tab.md` (520+ lines) and includes:

1. **Table of Contents**
2. **Installation** - npm/yarn/pnpm commands
3. **Quick Start** - Basic setup with PdfProvider
4. **Components**
   - PdfViewer with all props
   - PdfArchive with all props
   - PdfCard
   - PdfJsonLd
   - PdfBreadcrumbs
5. **Hooks**
   - usePdfDocument
   - usePdfDocumentBySlug
   - usePdfDocuments
   - usePdfViewer
   - usePdfSeo
   - usePdfTheme
6. **Next.js Integration**
   - App Router (generateMetadata, generateStaticParams, Route Handlers)
   - Pages Router (PdfMeta with next/head)
7. **Backend Integration**
   - WordPress (Headless)
   - Drupal (Headless)
   - Standalone (No Backend)
8. **Premium Features**
   - Password Protection
   - Reading Progress
   - Analytics Dashboard
   - Sitemap Generation
9. **API Reference**
   - TypeScript interfaces
   - CSS classes

---

## 5. Styling Updates

### New CSS Variables

```css
:root {
  /* Existing colors */
  --color-wordpress: #21759b;
  --color-drupal: #0678be;

  /* New React/Next.js colors */
  --color-react: #61dafb;
  --color-nextjs: #000000;
  --color-typescript: #3178c6;

  /* Badge colors */
  --color-new-badge: #28a745;
  --color-premium-badge: #ffc107;
}
```

### Platform Card Styles

```css
.platform-card-new {
  position: relative;
  border: 2px solid var(--color-react);
}

.platform-card-new .badge-new {
  position: absolute;
  top: -10px;
  right: 20px;
  background: var(--color-new-badge);
  color: white;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.highlight-new {
  background: linear-gradient(120deg, var(--color-react), var(--color-nextjs));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 600;
}
```

### Tab Styles for Docs

```css
.docs-tabs {
  display: flex;
  gap: 0;
  border-bottom: 2px solid #e0e0e0;
  margin-bottom: 24px;
}

.docs-tab {
  padding: 12px 24px;
  background: none;
  border: none;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  cursor: pointer;
  font-size: 16px;
  color: #666;
  transition: all 0.2s;
}

.docs-tab:hover {
  color: #333;
}

.docs-tab.active {
  color: #0066cc;
  border-bottom-color: #0066cc;
  font-weight: 600;
}

.docs-panel {
  display: none;
}

.docs-panel.active {
  display: block;
}
```

---

## 6. Navigation Updates

### Main Navigation

Add link to React/Next.js in main navigation.

```html
<nav class="main-nav">
  <ul>
    <li><a href="/">Home</a></li>
    <li class="dropdown">
      <a href="#">Platforms</a>
      <ul class="dropdown-menu">
        <li><a href="/wordpress-pdf-viewer/">WordPress</a></li>
        <li><a href="/drupal-pdf-viewer/">Drupal</a></li>
        <li><a href="/nextjs-pdf-viewer/">React / Next.js <span class="nav-badge">New</span></a></li>
      </ul>
    </li>
    <li><a href="/documentation/">Documentation</a></li>
    <li><a href="/pricing/">Pricing</a></li>
    <li><a href="/demo/">Demo</a></li>
  </ul>
</nav>
```

### Footer Navigation

```html
<div class="footer-column">
  <h4>Platforms</h4>
  <ul>
    <li><a href="/wordpress-pdf-viewer/">WordPress Plugin</a></li>
    <li><a href="/drupal-pdf-viewer/">Drupal Module</a></li>
    <li><a href="/nextjs-pdf-viewer/">React / Next.js</a></li>
  </ul>
</div>
```

---

## 7. SEO Updates

### New Pages to Add to Sitemap

```xml
<url>
  <loc>https://pdfviewer.drossmedia.de/nextjs-pdf-viewer/</loc>
  <lastmod>2026-02-02</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.9</priority>
</url>
```

### Schema.org for Landing Page

```json
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "PDF Embed & SEO Optimize for React/Next.js",
  "applicationCategory": "DeveloperApplication",
  "operatingSystem": "Any",
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "USD"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.8",
    "ratingCount": "120"
  }
}
```

---

## 8. Implementation Checklist

### Phase 1: Core Updates
- [ ] Update platform dropdown on all pages
- [ ] Add React/Next.js to main navigation
- [ ] Add React/Next.js to footer

### Phase 2: Homepage
- [ ] Update hero section tagline
- [ ] Add platform cards section
- [ ] Add feature comparison table
- [ ] Add quick start tabs

### Phase 3: Landing Page
- [ ] Create `/nextjs-pdf-viewer/` page
- [ ] Add all content sections
- [ ] Add code examples with syntax highlighting
- [ ] Add pricing/CTA section

### Phase 4: Documentation
- [ ] Add third tab to docs page
- [ ] Render markdown content
- [ ] Add code copy buttons
- [ ] Test all code examples

### Phase 5: SEO & Testing
- [ ] Update sitemap.xml
- [ ] Add Schema.org markup
- [ ] Test all internal links
- [ ] Cross-browser testing
- [ ] Mobile responsiveness check

---

## 9. Assets Needed

### Icons (SVG)
- `react.svg` - React logo
- `nextjs.svg` - Next.js logo (or use text)
- `typescript.svg` - TypeScript logo
- `npm.svg` - npm logo

### Screenshots
- React component in action
- PdfViewer with toolbar
- PdfArchive grid view
- Analytics dashboard
- Code editor with TypeScript

---

## Support

For questions about implementing these updates:
- **Documentation:** [pdfviewer.drossmedia.de/documentation](https://pdfviewer.drossmedia.de/documentation)
- **GitHub Issues:** [github.com/drossmedia/pdf-embed-seo-optimize/issues](https://github.com/drossmedia/pdf-embed-seo-optimize/issues)
- **Email:** support@drossmedia.de

---

*Made with care by [Dross:Media](https://dross.net/media/)*

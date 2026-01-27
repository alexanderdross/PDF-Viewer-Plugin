<?php

namespace Drupal\pdf_embed_seo_premium\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for PDF XML Sitemap (Premium).
 */
class PdfSitemapController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a PdfSitemapController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Generate XML sitemap for PDF documents.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   XML sitemap response.
   */
  public function sitemap() {
    $storage = $this->entityTypeManager->getStorage('pdf_document');

    // Query all published PDF documents.
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('status', 1)
      ->sort('changed', 'DESC');

    $ids = $query->execute();
    $documents = $storage->loadMultiple($ids);

    // Build XML sitemap.
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<?xml-stylesheet type="text/xsl" href="' . Url::fromRoute('pdf_embed_seo_premium.sitemap_style', [], ['absolute' => TRUE])->toString() . '"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    $xml .= '        xmlns:pdf="http://www.google.com/schemas/sitemap-pdf/1.0">' . "\n";

    // Add archive page.
    $archive_url = Url::fromRoute('pdf_embed_seo.archive', [], ['absolute' => TRUE])->toString();
    $xml .= "  <url>\n";
    $xml .= "    <loc>" . htmlspecialchars($archive_url) . "</loc>\n";
    $xml .= "    <changefreq>weekly</changefreq>\n";
    $xml .= "    <priority>0.8</priority>\n";
    $xml .= "  </url>\n";

    // Add individual PDF documents.
    foreach ($documents as $document) {
      $url = $document->toUrl('canonical', ['absolute' => TRUE])->toString();
      $lastmod = date('c', $document->getChangedTime());

      $xml .= "  <url>\n";
      $xml .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";
      $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
      $xml .= "    <changefreq>monthly</changefreq>\n";
      $xml .= "    <priority>0.6</priority>\n";

      // Add PDF-specific metadata.
      $xml .= "    <pdf:pdf>\n";
      $xml .= "      <pdf:title>" . htmlspecialchars($document->label()) . "</pdf:title>\n";

      if ($document->hasField('description') && !$document->get('description')->isEmpty()) {
        $description = strip_tags($document->get('description')->value);
        $xml .= "      <pdf:description>" . htmlspecialchars(substr($description, 0, 200)) . "</pdf:description>\n";
      }

      // Add thumbnail if available.
      if ($document->hasField('thumbnail') && !$document->get('thumbnail')->isEmpty()) {
        $thumbnail = $document->get('thumbnail')->entity;
        if ($thumbnail) {
          $thumb_url = \Drupal::service('file_url_generator')->generateAbsoluteString($thumbnail->getFileUri());
          $xml .= "      <pdf:thumbnail>" . htmlspecialchars($thumb_url) . "</pdf:thumbnail>\n";
        }
      }

      $xml .= "    </pdf:pdf>\n";
      $xml .= "  </url>\n";
    }

    $xml .= "</urlset>\n";

    $response = new Response($xml);
    $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
    $response->headers->set('Cache-Control', 'public, max-age=3600');

    return $response;
  }

  /**
   * Serve XSL stylesheet for sitemap.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   XSL stylesheet response.
   */
  public function sitemapStyle() {
    $xsl = '<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:pdf="http://www.google.com/schemas/sitemap-pdf/1.0">
  <xsl:output method="html" encoding="UTF-8"/>

  <xsl:template match="/">
    <html>
      <head>
        <title>PDF Documents Sitemap</title>
        <style>
          body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
          }
          .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
          }
          header {
            background: linear-gradient(135deg, #0073aa 0%, #005a87 100%);
            color: #fff;
            padding: 30px;
          }
          header h1 {
            margin: 0 0 10px;
            font-size: 24px;
          }
          header p {
            margin: 0;
            opacity: 0.9;
          }
          .stats {
            display: flex;
            gap: 30px;
            margin-top: 20px;
          }
          .stat {
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 6px;
          }
          .stat-value {
            font-size: 24px;
            font-weight: bold;
          }
          .stat-label {
            font-size: 12px;
            opacity: 0.8;
          }
          table {
            width: 100%;
            border-collapse: collapse;
          }
          th {
            background: #f8f9fa;
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e9ecef;
          }
          td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
          }
          tr:hover {
            background: #f8f9fa;
          }
          a {
            color: #0073aa;
            text-decoration: none;
          }
          a:hover {
            text-decoration: underline;
          }
          .url {
            word-break: break-all;
            max-width: 400px;
          }
          .lastmod {
            color: #666;
            font-size: 13px;
          }
          .priority {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
          }
          .priority-high {
            background: #d4edda;
            color: #155724;
          }
          .priority-medium {
            background: #fff3cd;
            color: #856404;
          }
          .priority-low {
            background: #e2e3e5;
            color: #383d41;
          }
          .pdf-title {
            font-weight: 500;
            margin-bottom: 4px;
          }
          .pdf-desc {
            font-size: 13px;
            color: #666;
          }
          footer {
            padding: 20px 30px;
            background: #f8f9fa;
            text-align: center;
            font-size: 13px;
            color: #666;
          }
        </style>
      </head>
      <body>
        <div class="container">
          <header>
            <h1>PDF Documents Sitemap</h1>
            <p>This sitemap contains all published PDF documents on this website.</p>
            <div class="stats">
              <div class="stat">
                <div class="stat-value"><xsl:value-of select="count(sitemap:urlset/sitemap:url)"/></div>
                <div class="stat-label">Total URLs</div>
              </div>
            </div>
          </header>
          <table>
            <thead>
              <tr>
                <th>URL / Document</th>
                <th>Last Modified</th>
                <th>Change Freq</th>
                <th>Priority</th>
              </tr>
            </thead>
            <tbody>
              <xsl:for-each select="sitemap:urlset/sitemap:url">
                <tr>
                  <td>
                    <div class="url">
                      <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                    </div>
                    <xsl:if test="pdf:pdf/pdf:title">
                      <div class="pdf-title"><xsl:value-of select="pdf:pdf/pdf:title"/></div>
                    </xsl:if>
                    <xsl:if test="pdf:pdf/pdf:description">
                      <div class="pdf-desc"><xsl:value-of select="pdf:pdf/pdf:description"/></div>
                    </xsl:if>
                  </td>
                  <td class="lastmod">
                    <xsl:value-of select="substring(sitemap:lastmod, 1, 10)"/>
                  </td>
                  <td><xsl:value-of select="sitemap:changefreq"/></td>
                  <td>
                    <xsl:choose>
                      <xsl:when test="sitemap:priority &gt;= 0.7">
                        <span class="priority priority-high"><xsl:value-of select="sitemap:priority"/></span>
                      </xsl:when>
                      <xsl:when test="sitemap:priority &gt;= 0.5">
                        <span class="priority priority-medium"><xsl:value-of select="sitemap:priority"/></span>
                      </xsl:when>
                      <xsl:otherwise>
                        <span class="priority priority-low"><xsl:value-of select="sitemap:priority"/></span>
                      </xsl:otherwise>
                    </xsl:choose>
                  </td>
                </tr>
              </xsl:for-each>
            </tbody>
          </table>
          <footer>
            Generated by PDF Embed &amp; SEO Optimize Premium
          </footer>
        </div>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>';

    $response = new Response($xsl);
    $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
    $response->headers->set('Cache-Control', 'public, max-age=86400');

    return $response;
  }

}

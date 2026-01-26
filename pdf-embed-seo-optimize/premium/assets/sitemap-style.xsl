<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<xsl:output method="html" encoding="UTF-8" indent="yes"/>

<xsl:template match="/">
<html>
<head>
    <title>PDF Sitemap</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1d2327;
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        .intro {
            color: #646970;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f0f0f1;
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
            color: #1d2327;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f1;
        }
        tr:hover td {
            background: #f9f9f9;
        }
        a {
            color: #2271b1;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .priority-high {
            color: #00a32a;
            font-weight: 600;
        }
        .priority-medium {
            color: #dba617;
        }
        .priority-low {
            color: #646970;
        }
        .thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .count {
            color: #646970;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PDF Document Sitemap</h1>
        <p class="intro">This is a sitemap of all PDF documents available on this website. Search engines use this file to discover and index PDF content.</p>
        <p class="count">Total PDFs: <strong><xsl:value-of select="count(sitemap:urlset/sitemap:url)"/></strong></p>
        <table>
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Last Modified</th>
                    <th>Change Freq</th>
                    <th>Priority</th>
                </tr>
            </thead>
            <tbody>
                <xsl:for-each select="sitemap:urlset/sitemap:url">
                    <tr>
                        <td>
                            <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                        </td>
                        <td><xsl:value-of select="substring(sitemap:lastmod, 1, 10)"/></td>
                        <td><xsl:value-of select="sitemap:changefreq"/></td>
                        <td>
                            <xsl:choose>
                                <xsl:when test="sitemap:priority >= 0.8">
                                    <span class="priority-high"><xsl:value-of select="sitemap:priority"/></span>
                                </xsl:when>
                                <xsl:when test="sitemap:priority >= 0.5">
                                    <span class="priority-medium"><xsl:value-of select="sitemap:priority"/></span>
                                </xsl:when>
                                <xsl:otherwise>
                                    <span class="priority-low"><xsl:value-of select="sitemap:priority"/></span>
                                </xsl:otherwise>
                            </xsl:choose>
                        </td>
                    </tr>
                </xsl:for-each>
            </tbody>
        </table>
    </div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>

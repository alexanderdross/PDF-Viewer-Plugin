/**
 * @file
 * PDF Analytics tracking for premium features.
 */

(function (Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.pdfAnalytics = {
    attach: function (context, settings) {
      var pdfSettings = drupalSettings.pdfEmbedSeo || {};
      if (!pdfSettings.premium || !pdfSettings.premium.enableAnalytics) {
        return;
      }

      var documentId = pdfSettings.documentId || 0;
      var startTime = Date.now();
      var tracked = false;

      function trackView() {
        if (tracked || !documentId) {
          return;
        }

        tracked = true;
        var token = drupalSettings.pdfEmbedSeo.csrfToken || '';

        fetch(Drupal.url('api/pdf-embed-seo/v1/documents/' + documentId + '/view'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': token
          },
          body: JSON.stringify({
            referrer: document.referrer,
            user_agent: navigator.userAgent
          })
        }).catch(function (error) {
          console.error('Error tracking view:', error);
        });
      }

      function trackTimeSpent() {
        if (!documentId) {
          return;
        }

        var timeSpent = Math.round((Date.now() - startTime) / 1000);
        var token = drupalSettings.pdfEmbedSeo.csrfToken || '';

        // Use sendBeacon for reliability on page unload
        if (navigator.sendBeacon) {
          var data = new FormData();
          data.append('time_spent', timeSpent.toString());
          navigator.sendBeacon(
            Drupal.url('api/pdf-embed-seo/v1/documents/' + documentId + '/analytics/time'),
            data
          );
        } else {
          fetch(Drupal.url('api/pdf-embed-seo/v1/documents/' + documentId + '/analytics/time'), {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-Token': token
            },
            body: JSON.stringify({
              time_spent: timeSpent
            }),
            keepalive: true
          });
        }
      }

      function trackDownload() {
        if (!documentId) {
          return;
        }

        var token = drupalSettings.pdfEmbedSeo.csrfToken || '';

        fetch(Drupal.url('api/pdf-embed-seo/v1/documents/' + documentId + '/download'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': token
          }
        }).catch(function (error) {
          console.error('Error tracking download:', error);
        });
      }

      // Track view when PDF is ready
      document.addEventListener('pdf:ready', trackView);

      // Track time spent on page unload
      window.addEventListener('beforeunload', trackTimeSpent);
      window.addEventListener('pagehide', trackTimeSpent);

      // Track downloads
      document.addEventListener('click', function (e) {
        if (e.target.matches('.pdf-download-btn, [data-pdf-download]')) {
          trackDownload();
        }
      });

      // Fallback view tracking
      setTimeout(trackView, 3000);
    }
  };

})(Drupal, drupalSettings);

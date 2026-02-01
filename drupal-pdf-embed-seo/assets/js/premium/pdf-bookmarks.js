/**
 * @file
 * PDF Bookmarks functionality for premium features.
 */

(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.pdfBookmarks = {
    attach: function (context, settings) {
      var elements = once('pdf-bookmarks', '.pdf-viewer-wrapper', context);
      if (elements.length === 0) {
        return;
      }

      var pdfSettings = drupalSettings.pdfEmbedSeo || {};
      if (!pdfSettings.premium || !pdfSettings.premium.enableBookmarks) {
        return;
      }

      var bookmarksLoaded = false;

      function initBookmarks() {
        var bookmarksToggle = document.querySelector('.pdf-bookmarks-toggle');
        var bookmarksClose = document.querySelector('.pdf-bookmarks-close');

        // Create sidebar if not exists
        if (!document.querySelector('.pdf-bookmarks-sidebar')) {
          var sidebar = document.createElement('div');
          sidebar.className = 'pdf-bookmarks-sidebar';
          sidebar.style.display = 'none';
          sidebar.innerHTML =
            '<div class="pdf-bookmarks-header">' +
              '<h4>' + Drupal.t('Bookmarks') + '</h4>' +
              '<button type="button" class="pdf-bookmarks-close" aria-label="' + Drupal.t('Close') + '">&times;</button>' +
            '</div>' +
            '<div class="pdf-bookmarks-content">' +
              '<ul class="pdf-bookmarks-list"></ul>' +
              '<p class="pdf-no-bookmarks" style="display:none;">' + Drupal.t('No bookmarks available') + '</p>' +
            '</div>';
          document.body.appendChild(sidebar);
        }

        if (bookmarksToggle) {
          bookmarksToggle.addEventListener('click', function (e) {
            e.preventDefault();
            toggleBookmarks();
          });
        }

        document.addEventListener('click', function (e) {
          if (e.target.matches('.pdf-bookmarks-close')) {
            e.preventDefault();
            closeBookmarks();
          }
          if (e.target.matches('.pdf-bookmarks-list a')) {
            e.preventDefault();
            var dest = e.target.getAttribute('data-dest');
            navigateToBookmark(dest);
          }
        });
      }

      function toggleBookmarks() {
        var sidebar = document.querySelector('.pdf-bookmarks-sidebar');
        if (sidebar) {
          if (sidebar.style.display === 'none') {
            sidebar.style.display = 'flex';
            if (!bookmarksLoaded) {
              loadBookmarks();
            }
          } else {
            closeBookmarks();
          }
        }
      }

      function closeBookmarks() {
        var sidebar = document.querySelector('.pdf-bookmarks-sidebar');
        if (sidebar) {
          sidebar.style.display = 'none';
        }
      }

      function loadBookmarks() {
        if (typeof window.pdfViewerInstance !== 'undefined' && window.pdfViewerInstance.pdfDoc) {
          window.pdfViewerInstance.pdfDoc.getOutline().then(function (outline) {
            var list = document.querySelector('.pdf-bookmarks-list');
            var noBookmarks = document.querySelector('.pdf-no-bookmarks');

            if (!outline || outline.length === 0) {
              if (list) list.style.display = 'none';
              if (noBookmarks) noBookmarks.style.display = 'block';
            } else {
              var html = renderBookmarks(outline);
              if (list) {
                list.innerHTML = html;
                list.style.display = 'block';
              }
              if (noBookmarks) noBookmarks.style.display = 'none';
            }
            bookmarksLoaded = true;
          });
        }
      }

      function renderBookmarks(items, level) {
        level = level || 0;
        var html = '';

        items.forEach(function (item) {
          html += '<li>';
          html += '<a href="#" data-dest="' + encodeURIComponent(JSON.stringify(item.dest)) + '" style="padding-left:' + (level * 15 + 10) + 'px;">';
          html += escapeHtml(item.title);
          html += '</a>';

          if (item.items && item.items.length > 0) {
            html += '<ul>' + renderBookmarks(item.items, level + 1) + '</ul>';
          }

          html += '</li>';
        });

        return html;
      }

      function navigateToBookmark(destJson) {
        try {
          var dest = JSON.parse(decodeURIComponent(destJson));

          if (typeof window.pdfViewerInstance !== 'undefined' && window.pdfViewerInstance.pdfDoc) {
            if (typeof dest === 'string') {
              window.pdfViewerInstance.pdfDoc.getDestination(dest).then(function (destArray) {
                goToDestination(destArray);
              });
            } else if (Array.isArray(dest)) {
              goToDestination(dest);
            }
          }
        } catch (e) {
          console.error('Error navigating to bookmark:', e);
        }
      }

      function goToDestination(destArray) {
        if (!destArray || !destArray[0]) {
          return;
        }

        if (typeof window.pdfViewerInstance !== 'undefined' && window.pdfViewerInstance.pdfDoc) {
          window.pdfViewerInstance.pdfDoc.getPageIndex(destArray[0]).then(function (pageIndex) {
            window.pdfViewerInstance.goToPage(pageIndex + 1);
            closeBookmarks();
          });
        }
      }

      function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      }

      // Initialize when PDF is ready
      document.addEventListener('pdf:ready', initBookmarks);
      // Fallback initialization
      setTimeout(initBookmarks, 2000);
    }
  };

})(Drupal, drupalSettings, once);

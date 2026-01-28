/**
 * @file
 * PDF Search functionality for premium features.
 */

(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.pdfSearch = {
    attach: function (context, settings) {
      var elements = once('pdf-search', '.pdf-viewer-wrapper', context);
      if (elements.length === 0) {
        return;
      }

      var pdfSettings = drupalSettings.pdfEmbedSeo || {};
      if (!pdfSettings.premium || !pdfSettings.premium.enableSearch) {
        return;
      }

      var searchMatches = [];
      var currentMatch = 0;

      // Initialize search UI
      function initSearch() {
        var searchToggle = document.querySelector('.pdf-search-toggle');
        var searchPanel = document.querySelector('.pdf-search-panel');
        var searchInput = document.querySelector('.pdf-search-input');
        var searchClose = document.querySelector('.pdf-search-close');
        var searchPrev = document.querySelector('.pdf-search-prev');
        var searchNext = document.querySelector('.pdf-search-next');

        if (searchToggle) {
          searchToggle.addEventListener('click', function (e) {
            e.preventDefault();
            toggleSearch();
          });
        }

        if (searchClose) {
          searchClose.addEventListener('click', function (e) {
            e.preventDefault();
            closeSearch();
          });
        }

        if (searchInput) {
          var debounceTimer;
          searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
              performSearch(searchInput.value);
            }, 300);
          });

          searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
              e.preventDefault();
              navigateSearch(e.shiftKey ? -1 : 1);
            } else if (e.key === 'Escape') {
              closeSearch();
            }
          });
        }

        if (searchPrev) {
          searchPrev.addEventListener('click', function () {
            navigateSearch(-1);
          });
        }

        if (searchNext) {
          searchNext.addEventListener('click', function () {
            navigateSearch(1);
          });
        }

        // Keyboard shortcut Ctrl+F
        document.addEventListener('keydown', function (e) {
          if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            var viewerWrapper = document.querySelector('.pdf-viewer-wrapper');
            if (viewerWrapper && viewerWrapper.contains(document.activeElement)) {
              e.preventDefault();
              toggleSearch();
            }
          }
        });
      }

      function toggleSearch() {
        var panel = document.querySelector('.pdf-search-panel');
        if (panel) {
          if (panel.style.display === 'none' || !panel.style.display) {
            panel.style.display = 'flex';
            var input = panel.querySelector('.pdf-search-input');
            if (input) {
              input.focus();
            }
          } else {
            closeSearch();
          }
        }
      }

      function closeSearch() {
        var panel = document.querySelector('.pdf-search-panel');
        if (panel) {
          panel.style.display = 'none';
        }
        clearSearchHighlights();
      }

      function performSearch(query) {
        if (!query || query.length < 2) {
          clearSearchHighlights();
          updateSearchResults(0);
          return;
        }

        searchMatches = [];
        currentMatch = 0;

        // Access PDF.js document if available
        if (typeof window.pdfViewerInstance !== 'undefined' && window.pdfViewerInstance.pdfDoc) {
          var pdfDoc = window.pdfViewerInstance.pdfDoc;
          var numPages = pdfDoc.numPages;
          var searchPromises = [];

          for (var i = 1; i <= numPages; i++) {
            searchPromises.push(searchPage(pdfDoc, i, query));
          }

          Promise.all(searchPromises).then(function (results) {
            results.forEach(function (pageMatches, index) {
              pageMatches.forEach(function (match) {
                searchMatches.push({
                  page: index + 1,
                  match: match
                });
              });
            });

            updateSearchResults(searchMatches.length);
            if (searchMatches.length > 0) {
              highlightCurrentMatch();
            }
          });
        }
      }

      function searchPage(pdfDoc, pageNum, query) {
        return new Promise(function (resolve) {
          pdfDoc.getPage(pageNum).then(function (page) {
            page.getTextContent().then(function (textContent) {
              var text = textContent.items.map(function (item) {
                return item.str;
              }).join(' ');

              var matches = [];
              var regex = new RegExp(query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
              var match;

              while ((match = regex.exec(text)) !== null) {
                matches.push({
                  index: match.index,
                  text: match[0]
                });
              }

              resolve(matches);
            });
          }).catch(function () {
            resolve([]);
          });
        });
      }

      function updateSearchResults(count) {
        var resultsEl = document.querySelector('.pdf-search-results');
        if (resultsEl) {
          if (count === 0) {
            resultsEl.textContent = Drupal.t('No results');
          } else {
            resultsEl.textContent = Drupal.t('@count matches found', { '@count': count });
          }
        }
      }

      function navigateSearch(direction) {
        if (searchMatches.length === 0) {
          return;
        }

        currentMatch += direction;
        if (currentMatch >= searchMatches.length) {
          currentMatch = 0;
        } else if (currentMatch < 0) {
          currentMatch = searchMatches.length - 1;
        }

        highlightCurrentMatch();
      }

      function highlightCurrentMatch() {
        var match = searchMatches[currentMatch];
        if (match && typeof window.pdfViewerInstance !== 'undefined') {
          window.pdfViewerInstance.goToPage(match.page);
        }
      }

      function clearSearchHighlights() {
        var highlights = document.querySelectorAll('.pdf-search-highlight');
        highlights.forEach(function (el) {
          el.classList.remove('pdf-search-highlight', 'current');
        });
      }

      // Initialize when PDF is ready
      document.addEventListener('pdf:ready', initSearch);
      // Fallback initialization
      setTimeout(initSearch, 2000);
    }
  };

})(Drupal, drupalSettings, once);

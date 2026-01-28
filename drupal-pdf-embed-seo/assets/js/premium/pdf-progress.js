/**
 * @file
 * PDF Reading Progress tracking for premium features.
 */

(function (Drupal, drupalSettings, once) {
  'use strict';

  Drupal.behaviors.pdfProgress = {
    attach: function (context, settings) {
      var elements = once('pdf-progress', '.pdf-viewer-wrapper', context);
      if (elements.length === 0) {
        return;
      }

      var pdfSettings = drupalSettings.pdfEmbedSeo || {};
      if (!pdfSettings.premium || !pdfSettings.premium.enableProgress) {
        return;
      }

      var currentPage = 1;
      var savedPage = 0;
      var documentId = pdfSettings.documentId || 0;

      function initProgress() {
        // Track page changes
        document.addEventListener('pdf:pagechange', function (e) {
          currentPage = e.detail.page || 1;
          saveReadingProgress();
        });

        // Save progress periodically
        setInterval(saveReadingProgress, 30000);

        // Check for existing progress
        checkReadingProgress();
      }

      function saveReadingProgress() {
        if (currentPage < 2 || !documentId) {
          return;
        }

        // Check if user is logged in
        if (drupalSettings.user && drupalSettings.user.uid > 0) {
          // Save via AJAX for logged-in users
          var token = drupalSettings.pdfEmbedSeo.csrfToken || '';
          fetch(Drupal.url('api/pdf-embed-seo/v1/documents/' + documentId + '/progress'), {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-Token': token
            },
            body: JSON.stringify({
              page_number: currentPage
            })
          }).catch(function (error) {
            console.error('Error saving progress:', error);
          });
        } else {
          // Save to localStorage for guests
          try {
            localStorage.setItem('pdf_progress_' + documentId, currentPage.toString());
          } catch (e) {
            // localStorage might be disabled
          }
        }
      }

      function checkReadingProgress() {
        if (!documentId) {
          return;
        }

        if (drupalSettings.user && drupalSettings.user.uid > 0) {
          // Check via AJAX for logged-in users
          fetch(Drupal.url('api/pdf-embed-seo/v1/documents/' + documentId + '/progress'))
            .then(function (response) {
              return response.json();
            })
            .then(function (data) {
              if (data.page_number && data.page_number > 1) {
                showProgressPrompt(data.page_number);
              }
            })
            .catch(function (error) {
              console.error('Error checking progress:', error);
            });
        } else {
          // Check localStorage for guests
          try {
            var stored = localStorage.getItem('pdf_progress_' + documentId);
            if (stored && parseInt(stored, 10) > 1) {
              showProgressPrompt(parseInt(stored, 10));
            }
          } catch (e) {
            // localStorage might be disabled
          }
        }
      }

      function showProgressPrompt(pageNum) {
        savedPage = pageNum;
        var message = Drupal.t('You were on page @page. Resume reading?', { '@page': pageNum });

        var prompt = document.createElement('div');
        prompt.className = 'pdf-progress-prompt';
        prompt.innerHTML =
          '<div class="pdf-progress-prompt-content">' +
            '<p class="pdf-progress-message">' + message + '</p>' +
            '<div class="pdf-progress-buttons">' +
              '<button type="button" class="pdf-progress-resume button button--primary">' + Drupal.t('Resume') + '</button>' +
              '<button type="button" class="pdf-progress-start-over button">' + Drupal.t('Start Over') + '</button>' +
            '</div>' +
          '</div>';

        document.body.appendChild(prompt);

        prompt.querySelector('.pdf-progress-resume').addEventListener('click', function () {
          resumeReading();
        });

        prompt.querySelector('.pdf-progress-start-over').addEventListener('click', function () {
          startOver();
        });
      }

      function resumeReading() {
        if (savedPage && typeof window.pdfViewerInstance !== 'undefined') {
          window.pdfViewerInstance.goToPage(savedPage);
        }
        removePrompt();
      }

      function startOver() {
        removePrompt();
        // Clear saved progress
        if (drupalSettings.user && drupalSettings.user.uid > 0) {
          var token = drupalSettings.pdfEmbedSeo.csrfToken || '';
          fetch(Drupal.url('api/pdf-embed-seo/v1/documents/' + documentId + '/progress'), {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-Token': token
            },
            body: JSON.stringify({
              page_number: 1
            })
          });
        } else {
          try {
            localStorage.removeItem('pdf_progress_' + documentId);
          } catch (e) {
            // localStorage might be disabled
          }
        }
      }

      function removePrompt() {
        var prompt = document.querySelector('.pdf-progress-prompt');
        if (prompt) {
          prompt.remove();
        }
      }

      // Initialize when PDF is ready
      document.addEventListener('pdf:ready', initProgress);
      // Fallback initialization
      setTimeout(initProgress, 2000);
    }
  };

})(Drupal, drupalSettings, once);

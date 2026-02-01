/**
 * @file
 * PDF Password protection for premium features.
 */

(function (Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.pdfPassword = {
    attach: function (context, settings) {
      var forms = context.querySelectorAll('.pdf-password-form:not(.pdf-password-processed)');

      forms.forEach(function (form) {
        form.classList.add('pdf-password-processed');

        form.addEventListener('submit', function (e) {
          e.preventDefault();

          var passwordInput = form.querySelector('input[type="password"]');
          var errorEl = form.querySelector('.pdf-password-error');
          var submitBtn = form.querySelector('button[type="submit"]');
          var documentId = form.getAttribute('data-document-id');

          if (!passwordInput || !documentId) {
            return;
          }

          var password = passwordInput.value;
          if (!password) {
            showError(errorEl, Drupal.t('Please enter a password.'));
            return;
          }

          // Disable form during submission
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = Drupal.t('Verifying...');
          }

          var token = drupalSettings.pdfEmbedSeo ? drupalSettings.pdfEmbedSeo.csrfToken : '';

          fetch(Drupal.url('api/pdf-embed-seo/v1/documents/' + documentId + '/verify-password'), {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-Token': token
            },
            body: JSON.stringify({
              password: password
            })
          })
          .then(function (response) {
            return response.json();
          })
          .then(function (data) {
            if (data.success) {
              // Password verified, reload page to show PDF
              window.location.reload();
            } else {
              showError(errorEl, data.message || Drupal.t('Incorrect password. Please try again.'));
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = Drupal.t('Submit');
              }
            }
          })
          .catch(function (error) {
            console.error('Error verifying password:', error);
            showError(errorEl, Drupal.t('An error occurred. Please try again.'));
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = Drupal.t('Submit');
            }
          });
        });
      });

      function showError(errorEl, message) {
        if (errorEl) {
          errorEl.textContent = message;
          errorEl.style.display = 'block';
        }
      }
    }
  };

})(Drupal, drupalSettings);

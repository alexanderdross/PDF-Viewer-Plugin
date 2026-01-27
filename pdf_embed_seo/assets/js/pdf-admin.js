/**
 * @file
 * Admin JavaScript for PDF Embed & SEO Optimize.
 */

(function (Drupal, once) {
  'use strict';

  /**
   * Initialize admin functionality.
   */
  Drupal.behaviors.pdfEmbedSeoAdmin = {
    attach: function (context, settings) {
      // Auto-generate slug from title if empty.
      once('pdf-slug-generator', 'input[name="title[0][value]"]', context).forEach(function (titleInput) {
        const pathInput = document.querySelector('input[name="path[0][alias]"]');

        if (pathInput && !pathInput.value) {
          titleInput.addEventListener('blur', function () {
            if (!pathInput.value) {
              const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
              pathInput.value = '/pdf/' + slug;
            }
          });
        }
      });

      // Preview thumbnail generation.
      once('pdf-thumbnail-preview', 'input[name="files[pdf_file_0]"]', context).forEach(function (fileInput) {
        fileInput.addEventListener('change', function () {
          const thumbnailField = document.querySelector('.field--name-thumbnail');
          if (thumbnailField) {
            const helpText = thumbnailField.querySelector('.description');
            if (helpText) {
              helpText.innerHTML += '<br><em>' + Drupal.t('Thumbnail will be auto-generated after save.') + '</em>';
            }
          }
        });
      });
    }
  };

})(Drupal, once);

/**
 * Settings page JavaScript for PDF Embed & SEO Optimize.
 *
 * @package PDF_Embed_SEO
 */

/* global jQuery, wp, pdfEmbedSeoSettings */

(function($) {
	'use strict';

	/**
	 * Initialize the favicon upload functionality.
	 */
	function initFaviconUpload() {
		var $uploadBtn = $('.pdf-embed-seo-favicon-upload');
		var $removeBtn = $('.pdf-embed-seo-favicon-remove');
		var $urlInput = $('.pdf-embed-seo-favicon-url');
		var $previewContainer = $('.pdf-embed-seo-favicon-preview');
		var mediaFrame;

		// Upload button click handler.
		$uploadBtn.on('click', function(e) {
			e.preventDefault();

			// If media frame already exists, reopen it.
			if (mediaFrame) {
				mediaFrame.open();
				return;
			}

			// Create a new media frame.
			mediaFrame = wp.media({
				title: pdfEmbedSeoSettings.selectFavicon || 'Select Favicon',
				button: {
					text: pdfEmbedSeoSettings.useFavicon || 'Use this image'
				},
				library: {
					type: ['image', 'image/x-icon', 'image/vnd.microsoft.icon']
				},
				multiple: false
			});

			// When an image is selected, run a callback.
			mediaFrame.on('select', function() {
				var attachment = mediaFrame.state().get('selection').first().toJSON();
				var url = attachment.url;

				// Update the input field.
				$urlInput.val(url);

				// Update or create the preview.
				if ($previewContainer.length) {
					$previewContainer.find('img').attr('src', url);
				} else {
					$previewContainer = $('<div class="pdf-embed-seo-favicon-preview" style="margin-top: 10px;"><img src="' + url + '" alt="Favicon Preview" style="max-width: 32px; max-height: 32px; border: 1px solid #ddd; padding: 4px; background: #fff;"></div>');
					$urlInput.closest('.pdf-embed-seo-favicon-field').after($previewContainer);
				}

				// Show the remove button.
				$removeBtn.show();
			});

			// Open the modal.
			mediaFrame.open();
		});

		// Remove button click handler.
		$removeBtn.on('click', function(e) {
			e.preventDefault();

			// Clear the input field.
			$urlInput.val('');

			// Remove the preview.
			$previewContainer.remove();

			// Hide the remove button.
			$(this).hide();
		});

		// Update preview when URL is manually changed.
		$urlInput.on('change', function() {
			var url = $(this).val();

			if (url) {
				if ($previewContainer.length) {
					$previewContainer.find('img').attr('src', url);
				} else {
					$previewContainer = $('<div class="pdf-embed-seo-favicon-preview" style="margin-top: 10px;"><img src="' + url + '" alt="Favicon Preview" style="max-width: 32px; max-height: 32px; border: 1px solid #ddd; padding: 4px; background: #fff;"></div>');
					$urlInput.closest('.pdf-embed-seo-favicon-field').after($previewContainer);
				}
				$removeBtn.show();
			} else {
				$previewContainer.remove();
				$removeBtn.hide();
			}
		});
	}

	// Initialize on document ready.
	$(document).ready(function() {
		initFaviconUpload();
	});

})(jQuery);

/**
 * Admin scripts for PDF Embed & SEO Optimize.
 *
 * @package PDF_Viewer_2026
 */

(function($) {
    'use strict';

    /**
     * PDF File Upload Handler
     */
    var PDFFileUpload = {
        frame: null,

        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $(document).on('click', '.pdf-viewer-2026-upload-btn, .pdf-viewer-2026-change-btn', this.openMediaFrame.bind(this));
            $(document).on('click', '.pdf-viewer-2026-remove-btn', this.removeFile.bind(this));
        },

        openMediaFrame: function(e) {
            e.preventDefault();

            // If frame already exists, open it
            if (this.frame) {
                this.frame.open();
                return;
            }

            // Create media frame
            this.frame = wp.media({
                title: pdfViewer2026Admin.selectPdf,
                button: {
                    text: pdfViewer2026Admin.usePdf
                },
                library: {
                    type: 'application/pdf'
                },
                multiple: false
            });

            // Handle selection
            this.frame.on('select', this.handleSelection.bind(this));

            // Open frame
            this.frame.open();
        },

        handleSelection: function() {
            var attachment = this.frame.state().get('selection').first().toJSON();

            // Validate file type
            if (attachment.mime !== 'application/pdf') {
                alert('Please select a PDF file.');
                return;
            }

            // Update hidden field
            $('#pdf_file_id').val(attachment.id);

            // Update preview
            var fileName = attachment.filename || attachment.title;
            $('.pdf-viewer-2026-file-name').text(fileName);
            $('.pdf-viewer-2026-file-preview').show();

            // Update view PDF link
            var viewLink = $('.pdf-viewer-2026-file-preview .button');
            if (viewLink.length) {
                viewLink.attr('href', attachment.url);
            } else {
                var newLink = $('<a href="' + attachment.url + '" target="_blank" class="button button-small">View PDF</a>');
                $('.pdf-viewer-2026-file-info').after(newLink);
            }

            // Toggle buttons
            $('.pdf-viewer-2026-upload-btn').hide();
            $('.pdf-viewer-2026-change-btn, .pdf-viewer-2026-remove-btn').show();
        },

        removeFile: function(e) {
            e.preventDefault();

            // Clear hidden field
            $('#pdf_file_id').val('');

            // Hide preview
            $('.pdf-viewer-2026-file-preview').hide();
            $('.pdf-viewer-2026-file-name').text('');

            // Toggle buttons
            $('.pdf-viewer-2026-upload-btn').show();
            $('.pdf-viewer-2026-change-btn, .pdf-viewer-2026-remove-btn').hide();
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        PDFFileUpload.init();
    });

})(jQuery);

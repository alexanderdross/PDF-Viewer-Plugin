/**
 * PDF Embed & SEO Pro+ Viewer Scripts
 *
 * @package PDF_Embed_SEO_Optimize
 * @subpackage Pro_Plus
 * @since 1.3.0
 */

(function($) {
    'use strict';

    /**
     * Pro+ Viewer Controller
     */
    var ProPlusViewer = {
        settings: {},
        currentPage: 1,
        totalPages: 1,

        /**
         * Initialize
         */
        init: function() {
            if (typeof pdfProPlus === 'undefined') {
                return;
            }

            this.settings = pdfProPlus.settings || {};
            this.bindEvents();
            this.initToolbar();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Page change events from PDF.js
            $(document).on('pdf:pagechange', this.onPageChange.bind(this));

            // Toolbar button clicks
            $(document).on('click', '.pdf-pro-plus-tool', this.onToolClick.bind(this));
        },

        /**
         * Initialize toolbar extensions
         */
        initToolbar: function() {
            var $toolbar = $('.pdf-viewer-toolbar');

            if (!$toolbar.length) {
                return;
            }

            var $tools = $('<div class="pdf-pro-plus-tools"></div>');

            // Add annotation tool (if enabled)
            if (this.settings.enable_annotations && pdfAnnotations && pdfAnnotations.canAnnotate) {
                $tools.append(
                    '<button type="button" class="pdf-pro-plus-tool" data-tool="annotate" title="' + pdfProPlus.i18n.annotate + '">' +
                    '<span class="dashicons dashicons-edit"></span>' +
                    '</button>'
                );
            }

            // Add signature tool (if enabled)
            if (this.settings.enable_annotations && pdfAnnotations && pdfAnnotations.canSign) {
                $tools.append(
                    '<button type="button" class="pdf-pro-plus-tool" data-tool="signature" title="' + pdfProPlus.i18n.signature + '">' +
                    '<span class="dashicons dashicons-admin-users"></span>' +
                    '</button>'
                );
            }

            if ($tools.children().length) {
                $toolbar.append($tools);
            }
        },

        /**
         * Handle tool button click
         */
        onToolClick: function(e) {
            var $button = $(e.currentTarget);
            var tool = $button.data('tool');

            $button.toggleClass('active');

            switch (tool) {
                case 'annotate':
                    this.toggleAnnotationMode($button.hasClass('active'));
                    break;
                case 'signature':
                    this.openSignatureModal();
                    break;
            }
        },

        /**
         * Toggle annotation mode
         */
        toggleAnnotationMode: function(enabled) {
            var $layer = $('.pdf-annotations-layer');

            if (enabled) {
                $layer.addClass('editing');
            } else {
                $layer.removeClass('editing');
            }
        },

        /**
         * Open signature modal
         */
        openSignatureModal: function() {
            if (typeof PdfSignature !== 'undefined') {
                PdfSignature.open();
            }
        },

        /**
         * Handle page change
         */
        onPageChange: function(e, data) {
            this.currentPage = data.page || 1;
            this.totalPages = data.total || 1;

            // Trigger custom event for tracking
            $(document).trigger('pdf:proplus:pagechange', {
                page: this.currentPage,
                total: this.totalPages
            });
        }
    };

    /**
     * Signature Handler
     */
    var PdfSignature = {
        canvas: null,
        ctx: null,
        isDrawing: false,
        lastX: 0,
        lastY: 0,

        /**
         * Open signature modal
         */
        open: function() {
            var html = '<div class="pdf-signature-modal">' +
                '<div class="pdf-signature-box">' +
                '<h3>' + pdfProPlus.i18n.signature + '</h3>' +
                '<div class="pdf-signature-canvas-wrapper">' +
                '<canvas class="pdf-signature-canvas"></canvas>' +
                '</div>' +
                '<div class="pdf-signature-actions">' +
                '<button type="button" class="clear">' + pdfAnnotations.i18n.clearSign + '</button>' +
                '<button type="button" class="cancel">' + pdfAnnotations.i18n.cancel + '</button>' +
                '<button type="button" class="sign">' + pdfAnnotations.i18n.save + '</button>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('body').append(html);

            this.initCanvas();
            this.bindEvents();
        },

        /**
         * Initialize canvas
         */
        initCanvas: function() {
            this.canvas = document.querySelector('.pdf-signature-canvas');
            this.ctx = this.canvas.getContext('2d');

            // Set canvas size
            var wrapper = this.canvas.parentElement;
            this.canvas.width = wrapper.offsetWidth;
            this.canvas.height = 150;

            // Set drawing style
            this.ctx.strokeStyle = '#000';
            this.ctx.lineWidth = 2;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
        },

        /**
         * Bind signature events
         */
        bindEvents: function() {
            var self = this;

            // Mouse events
            $(this.canvas).on('mousedown', function(e) {
                self.isDrawing = true;
                self.lastX = e.offsetX;
                self.lastY = e.offsetY;
            });

            $(this.canvas).on('mousemove', function(e) {
                if (!self.isDrawing) return;
                self.draw(e.offsetX, e.offsetY);
            });

            $(this.canvas).on('mouseup mouseleave', function() {
                self.isDrawing = false;
            });

            // Touch events
            $(this.canvas).on('touchstart', function(e) {
                e.preventDefault();
                var touch = e.originalEvent.touches[0];
                var rect = self.canvas.getBoundingClientRect();
                self.isDrawing = true;
                self.lastX = touch.clientX - rect.left;
                self.lastY = touch.clientY - rect.top;
            });

            $(this.canvas).on('touchmove', function(e) {
                if (!self.isDrawing) return;
                e.preventDefault();
                var touch = e.originalEvent.touches[0];
                var rect = self.canvas.getBoundingClientRect();
                self.draw(touch.clientX - rect.left, touch.clientY - rect.top);
            });

            $(this.canvas).on('touchend', function() {
                self.isDrawing = false;
            });

            // Button events
            $('.pdf-signature-modal .clear').on('click', function() {
                self.clear();
            });

            $('.pdf-signature-modal .cancel').on('click', function() {
                self.close();
            });

            $('.pdf-signature-modal .sign').on('click', function() {
                self.save();
            });
        },

        /**
         * Draw on canvas
         */
        draw: function(x, y) {
            this.ctx.beginPath();
            this.ctx.moveTo(this.lastX, this.lastY);
            this.ctx.lineTo(x, y);
            this.ctx.stroke();
            this.lastX = x;
            this.lastY = y;
        },

        /**
         * Clear canvas
         */
        clear: function() {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        },

        /**
         * Save signature
         */
        save: function() {
            var self = this;
            var signatureData = this.canvas.toDataURL('image/png');

            // Check if canvas is empty
            var imageData = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
            var isEmpty = !imageData.data.some(function(channel) {
                return channel !== 0;
            });

            if (isEmpty) {
                alert('Please draw your signature');
                return;
            }

            $.ajax({
                url: pdfAnnotations.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pdf_save_signature',
                    nonce: pdfAnnotations.nonce,
                    post_id: pdfAnnotations.postId,
                    page_number: ProPlusViewer.currentPage,
                    signature_data: signatureData,
                    x_position: 50,
                    y_position: 80,
                    width: 200,
                    height: 80
                },
                success: function(response) {
                    if (response.success) {
                        self.close();
                        // Reload page to show signature
                        location.reload();
                    } else {
                        alert(response.data.message || 'Failed to save signature');
                    }
                },
                error: function() {
                    alert('Request failed');
                }
            });
        },

        /**
         * Close modal
         */
        close: function() {
            $('.pdf-signature-modal').remove();
        }
    };

    // Expose to window
    window.PdfSignature = PdfSignature;

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        ProPlusViewer.init();
    });

})(jQuery);

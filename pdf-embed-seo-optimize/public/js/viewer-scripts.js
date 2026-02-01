/**
 * PDF Viewer scripts for PDF Embed & SEO Optimize.
 *
 * Uses Mozilla's PDF.js library to render PDFs.
 *
 * @package PDF_Viewer_2026
 */

(function($) {
    'use strict';

    /**
     * PDF Viewer Class
     */
    var PDFViewer = function(container) {
        this.container = $(container);
        this.canvas = this.container.find('.pdf-embed-seo-optimize-canvas')[0];
        this.ctx = this.canvas.getContext('2d');
        this.loading = this.container.find('.pdf-embed-seo-optimize-loading');
        this.pageInput = this.container.find('.pdf-embed-seo-optimize-page-input');
        this.totalPages = this.container.find('.pdf-embed-seo-optimize-total-pages');
        this.zoomLevel = this.container.find('.pdf-embed-seo-optimize-zoom-level');

        this.pdfDoc = null;
        this.currentPage = 1;
        this.numPages = 0;
        this.scale = 1.0;
        this.minScale = 0.25;
        this.maxScale = 4.0;
        this.rendering = false;
        this.pendingPage = null;
        this.pdfUrl = null;
        this.pdfTitle = '';

        this.init();
    };

    PDFViewer.prototype = {
        /**
         * Initialize the viewer
         */
        init: function() {
            this.bindEvents();
            this.loadPDF();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            var self = this;

            // Navigation buttons
            this.container.on('click', '.pdf-embed-seo-optimize-prev', function() {
                self.prevPage();
            });

            this.container.on('click', '.pdf-embed-seo-optimize-next', function() {
                self.nextPage();
            });

            // Page input
            this.pageInput.on('change', function() {
                var page = parseInt($(this).val(), 10);
                if (page >= 1 && page <= self.numPages) {
                    self.goToPage(page);
                } else {
                    $(this).val(self.currentPage);
                }
            });

            // Zoom buttons
            this.container.on('click', '.pdf-embed-seo-optimize-zoom-in', function() {
                self.zoomIn();
            });

            this.container.on('click', '.pdf-embed-seo-optimize-zoom-out', function() {
                self.zoomOut();
            });

            // Download button
            this.container.on('click', '.pdf-embed-seo-optimize-download', function() {
                self.download();
            });

            // Print button
            this.container.on('click', '.pdf-embed-seo-optimize-print', function() {
                self.print();
            });

            // Fullscreen button
            this.container.on('click', '.pdf-embed-seo-optimize-fullscreen', function() {
                self.toggleFullscreen();
            });

            // Keyboard navigation
            $(document).on('keydown', function(e) {
                if (!self.container.is(':visible')) return;

                switch(e.key) {
                    case 'ArrowLeft':
                    case 'ArrowUp':
                        self.prevPage();
                        e.preventDefault();
                        break;
                    case 'ArrowRight':
                    case 'ArrowDown':
                        self.nextPage();
                        e.preventDefault();
                        break;
                }
            });

            // Handle window resize
            var resizeTimeout;
            $(window).on('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    if (self.pdfDoc) {
                        self.renderPage(self.currentPage);
                    }
                }, 250);
            });

            // Escape fullscreen
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && self.container.hasClass('is-fullscreen')) {
                    self.toggleFullscreen();
                }
            });
        },

        /**
         * Load PDF via AJAX
         */
        loadPDF: function() {
            var self = this;

            // Get post ID from shortcode container or use global
            var postId = this.container.closest('.pdf-embed-seo-optimize-shortcode').data('post-id') || pdfEmbedSeo.postId;

            if (!postId) {
                console.error('PDF Viewer: No post ID found');
                self.showError('No PDF document ID specified.');
                return;
            }

            console.log('PDF Viewer: Loading PDF for post ID:', postId);

            $.ajax({
                url: pdfEmbedSeo.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pdf_embed_seo_get_pdf',
                    nonce: pdfEmbedSeo.nonce,
                    post_id: postId
                },
                success: function(response) {
                    console.log('PDF Viewer: AJAX response:', response);
                    if (response.success) {
                        self.pdfUrl = response.data.url;
                        self.pdfTitle = response.data.title;
                        console.log('PDF Viewer: PDF URL:', self.pdfUrl);
                        self.initPDF();
                    } else {
                        console.error('PDF Viewer: AJAX error:', response.data.message);
                        self.showError(response.data.message || pdfEmbedSeo.strings.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('PDF Viewer: AJAX request failed:', status, error);
                    console.error('PDF Viewer: Response:', xhr.responseText);
                    self.showError(pdfEmbedSeo.strings.error + ' (AJAX failed: ' + status + ')');
                }
            });
        },

        /**
         * Initialize PDF.js with the PDF URL
         */
        initPDF: function() {
            var self = this;

            if (!this.pdfUrl) {
                console.error('PDF Viewer: No PDF URL provided');
                self.showError('No PDF file URL available.');
                return;
            }

            console.log('PDF Viewer: Initializing PDF.js with URL:', this.pdfUrl);

            // Check if pdfjsLib is available
            if (typeof pdfjsLib === 'undefined') {
                console.error('PDF Viewer: PDF.js library not loaded');
                self.showError('PDF.js library failed to load.');
                return;
            }

            // Load the PDF
            var loadingTask = pdfjsLib.getDocument(this.pdfUrl);

            loadingTask.promise.then(function(pdf) {
                console.log('PDF Viewer: PDF loaded successfully, pages:', pdf.numPages);
                self.pdfDoc = pdf;
                self.numPages = pdf.numPages;
                self.totalPages.text(self.numPages);
                self.pageInput.attr('max', self.numPages);

                // Render first page
                self.renderPage(1);

                // Update navigation buttons
                self.updateNavigation();

            }).catch(function(error) {
                console.error('PDF Viewer: PDF.js loading error:', error);
                var errorMsg = pdfEmbedSeo.strings.error;
                if (error.message) {
                    errorMsg += ' (' + error.message + ')';
                }
                // Check for common errors
                if (error.name === 'MissingPDFException') {
                    errorMsg = 'PDF file not found or inaccessible.';
                } else if (error.message && error.message.indexOf('CORS') !== -1) {
                    errorMsg = 'PDF blocked by browser security (CORS).';
                }
                self.showError(errorMsg);
            });
        },

        /**
         * Render a specific page
         */
        renderPage: function(num) {
            var self = this;

            if (this.rendering) {
                this.pendingPage = num;
                return;
            }

            this.rendering = true;
            this.currentPage = num;

            this.pdfDoc.getPage(num).then(function(page) {
                // Calculate scale to fit container width
                var containerWidth = self.container.find('.pdf-embed-seo-optimize-viewer').width() - 40;
                var viewport = page.getViewport({ scale: 1 });
                var defaultScale = containerWidth / viewport.width;

                // Apply user scale
                var finalScale = defaultScale * self.scale;
                viewport = page.getViewport({ scale: finalScale });

                // Set canvas dimensions
                self.canvas.height = viewport.height;
                self.canvas.width = viewport.width;

                // Render
                var renderContext = {
                    canvasContext: self.ctx,
                    viewport: viewport
                };

                var renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    self.rendering = false;
                    self.loading.hide();

                    // Update UI
                    self.pageInput.val(num);
                    self.updateNavigation();

                    // Render pending page if any
                    if (self.pendingPage !== null) {
                        var pending = self.pendingPage;
                        self.pendingPage = null;
                        self.renderPage(pending);
                    }
                });

            }).catch(function(error) {
                console.error('Page render error:', error);
                self.rendering = false;
                self.showError(pdfEmbedSeo.strings.error);
            });
        },

        /**
         * Go to previous page
         */
        prevPage: function() {
            if (this.currentPage > 1) {
                this.goToPage(this.currentPage - 1);
            }
        },

        /**
         * Go to next page
         */
        nextPage: function() {
            if (this.currentPage < this.numPages) {
                this.goToPage(this.currentPage + 1);
            }
        },

        /**
         * Go to specific page
         */
        goToPage: function(num) {
            if (num >= 1 && num <= this.numPages && num !== this.currentPage) {
                this.renderPage(num);
            }
        },

        /**
         * Update navigation button states
         */
        updateNavigation: function() {
            var prevBtn = this.container.find('.pdf-embed-seo-optimize-prev');
            var nextBtn = this.container.find('.pdf-embed-seo-optimize-next');

            prevBtn.prop('disabled', this.currentPage <= 1);
            nextBtn.prop('disabled', this.currentPage >= this.numPages);
        },

        /**
         * Zoom in
         */
        zoomIn: function() {
            if (this.scale < this.maxScale) {
                this.scale = Math.min(this.scale + 0.25, this.maxScale);
                this.updateZoom();
            }
        },

        /**
         * Zoom out
         */
        zoomOut: function() {
            if (this.scale > this.minScale) {
                this.scale = Math.max(this.scale - 0.25, this.minScale);
                this.updateZoom();
            }
        },

        /**
         * Update zoom display and re-render
         */
        updateZoom: function() {
            var percentage = Math.round(this.scale * 100);
            this.zoomLevel.text(percentage + '%');
            this.renderPage(this.currentPage);
        },

        /**
         * Download PDF
         */
        download: function() {
            if (!pdfEmbedSeo.allowDownload) {
                return;
            }

            var link = document.createElement('a');
            link.href = this.pdfUrl;
            link.download = this.pdfTitle + '.pdf';
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        /**
         * Print PDF
         */
        print: function() {
            if (!pdfEmbedSeo.allowPrint) {
                return;
            }

            // Open PDF in new window for printing
            var printWindow = window.open(this.pdfUrl, '_blank');
            if (printWindow) {
                printWindow.addEventListener('load', function() {
                    printWindow.print();
                });
            }
        },

        /**
         * Toggle fullscreen mode
         */
        toggleFullscreen: function() {
            this.container.toggleClass('is-fullscreen');

            var icon = this.container.find('.pdf-embed-seo-optimize-fullscreen .dashicons');

            if (this.container.hasClass('is-fullscreen')) {
                icon.removeClass('dashicons-fullscreen-alt').addClass('dashicons-fullscreen-exit-alt');
                $('body').css('overflow', 'hidden');
            } else {
                icon.removeClass('dashicons-fullscreen-exit-alt').addClass('dashicons-fullscreen-alt');
                $('body').css('overflow', '');
            }

            // Re-render to fit new dimensions
            if (this.pdfDoc) {
                var self = this;
                setTimeout(function() {
                    self.renderPage(self.currentPage);
                }, 100);
            }
        },

        /**
         * Show error message
         */
        showError: function(message) {
            this.loading.hide();
            this.container.find('.pdf-embed-seo-optimize-viewer').html(
                '<div class="pdf-embed-seo-optimize-error">' + message + '</div>'
            );
        }
    };

    /**
     * Initialize all PDF viewers on page load
     */
    $(document).ready(function() {
        $('.pdf-embed-seo-optimize-container').each(function() {
            new PDFViewer(this);
        });
    });

    // Expose for potential external use
    window.PDFViewer2026 = PDFViewer;

})(jQuery);

/**
 * PDF Embed & SEO Pro+ Tracking Scripts
 *
 * @package PDF_Embed_SEO_Optimize
 * @subpackage Pro_Plus
 * @since 1.3.0
 */

(function($) {
    'use strict';

    /**
     * Tracking Controller
     */
    var PdfTracking = {
        config: {},
        currentPage: 1,
        startTime: 0,
        scrollDepth: 0,
        interactions: 0,
        timeUpdateInterval: null,

        /**
         * Initialize tracking
         */
        init: function() {
            if (typeof pdfTracking === 'undefined') {
                return;
            }

            this.config = pdfTracking;
            this.startTime = Date.now();

            this.bindEvents();
            this.startTimeTracking();
            this.trackPageView(1);
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            var self = this;

            // Page change tracking
            $(document).on('pdf:pagechange', function(e, data) {
                self.trackPageView(data.page);
            });

            // Scroll depth tracking
            if (this.config.trackScroll) {
                $(document).on('pdf:scroll', function(e, data) {
                    self.trackScroll(data.depth);
                });
            }

            // Click tracking for heatmaps
            if (this.config.trackClicks) {
                $('.pdf-viewer-container').on('click', function(e) {
                    self.trackClick(e);
                });
            }

            // Interaction tracking
            $('.pdf-viewer-container').on('click keydown', function() {
                self.interactions++;
            });

            // Track on page unload
            $(window).on('beforeunload', function() {
                self.flush();
            });

            // Track on visibility change
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'hidden') {
                    self.flush();
                }
            });
        },

        /**
         * Start time tracking interval
         */
        startTimeTracking: function() {
            var self = this;

            if (!this.config.trackTime) {
                return;
            }

            // Send time update every 30 seconds
            this.timeUpdateInterval = setInterval(function() {
                self.trackTime();
            }, 30000);
        },

        /**
         * Track page view within PDF
         */
        trackPageView: function(pageNumber) {
            // Send previous page time first
            if (this.currentPage !== pageNumber) {
                this.trackTime();
                this.startTime = Date.now();
            }

            this.currentPage = pageNumber;

            this.sendTrackingData('pdf_track_page_view', {
                page_number: pageNumber,
                zoom_level: this.getZoomLevel(),
                screen_width: window.innerWidth,
                screen_height: window.innerHeight
            });
        },

        /**
         * Track scroll depth
         */
        trackScroll: function(depth) {
            if (depth <= this.scrollDepth) {
                return;
            }

            this.scrollDepth = depth;

            this.sendTrackingData('pdf_track_scroll', {
                page_number: this.currentPage,
                scroll_depth: depth
            });
        },

        /**
         * Track time on page
         */
        trackTime: function() {
            var seconds = Math.floor((Date.now() - this.startTime) / 1000);

            if (seconds < 1) {
                return;
            }

            this.sendTrackingData('pdf_track_time', {
                page_number: this.currentPage,
                seconds: seconds
            });

            // Reset timer
            this.startTime = Date.now();
        },

        /**
         * Track click for heatmap
         */
        trackClick: function(e) {
            var $container = $('.pdf-viewer-container');
            var offset = $container.offset();

            // Calculate relative position (0-100%)
            var x = ((e.pageX - offset.left) / $container.width()) * 100;
            var y = ((e.pageY - offset.top) / $container.height()) * 100;

            // Round to 2 decimal places
            x = Math.round(x * 100) / 100;
            y = Math.round(y * 100) / 100;

            this.sendTrackingData('pdf_track_click', {
                page_number: this.currentPage,
                x_position: x,
                y_position: y
            });
        },

        /**
         * Get current zoom level from PDF.js
         */
        getZoomLevel: function() {
            // Try to get zoom from PDF.js viewer
            if (typeof PDFViewerApplication !== 'undefined' && PDFViewerApplication.pdfViewer) {
                return PDFViewerApplication.pdfViewer.currentScale || 1;
            }
            return 1;
        },

        /**
         * Send tracking data to server
         */
        sendTrackingData: function(action, data) {
            var payload = $.extend({
                action: action,
                nonce: this.config.nonce,
                post_id: this.config.postId,
                session_id: this.config.sessionId
            }, data);

            // Use sendBeacon if available for reliability
            if (navigator.sendBeacon) {
                var formData = new FormData();
                $.each(payload, function(key, value) {
                    formData.append(key, value);
                });
                navigator.sendBeacon(this.config.ajaxUrl, formData);
            } else {
                $.ajax({
                    url: this.config.ajaxUrl,
                    type: 'POST',
                    data: payload,
                    async: true
                });
            }
        },

        /**
         * Flush all pending tracking data
         */
        flush: function() {
            if (this.timeUpdateInterval) {
                clearInterval(this.timeUpdateInterval);
            }

            this.trackTime();
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        PdfTracking.init();
    });

})(jQuery);

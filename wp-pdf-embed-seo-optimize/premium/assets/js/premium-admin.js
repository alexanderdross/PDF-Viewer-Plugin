/**
 * Premium Admin JavaScript
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

(function($) {
    'use strict';

    var PremiumAdmin = {
        init: function() {
            this.initToggles();
            this.initBulkImport();
            this.initAnalyticsCharts();
        },

        // Toggle switches
        initToggles: function() {
            $('.pdf-toggle input').on('change', function() {
                var $slider = $(this).siblings('.pdf-toggle-slider');
                if (this.checked) {
                    $slider.addClass('active');
                } else {
                    $slider.removeClass('active');
                }
            });
        },

        // Bulk Import functionality
        initBulkImport: function() {
            var $dropzone = $('.pdf-import-dropzone');
            var $fileInput = $('#pdf_files');

            if ($dropzone.length === 0) {
                return;
            }

            // Click to select files
            $dropzone.on('click', function() {
                $fileInput.trigger('click');
            });

            // Drag and drop
            $dropzone.on('dragover dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            $dropzone.on('dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            $dropzone.on('drop', function(e) {
                var files = e.originalEvent.dataTransfer.files;
                $fileInput[0].files = files;
                PremiumAdmin.updateFileList(files);
            });

            // File selection change
            $fileInput.on('change', function() {
                PremiumAdmin.updateFileList(this.files);
            });
        },

        updateFileList: function(files) {
            var $dropzone = $('.pdf-import-dropzone');
            var text = files.length + ' file(s) selected';

            $dropzone.find('p').text(text);
        },

        // Analytics Charts
        initAnalyticsCharts: function() {
            var $chartContainer = $('#pdf-analytics-chart');

            if ($chartContainer.length === 0 || typeof Chart === 'undefined') {
                return;
            }

            // Fetch chart data
            $.post(ajaxurl, {
                action: 'pdf_get_analytics_data',
                nonce: pdfAnalytics.nonce,
                days: 30
            }, function(response) {
                if (response.success) {
                    PremiumAdmin.renderChart($chartContainer[0], response.data.data);
                }
            });
        },

        renderChart: function(canvas, data) {
            var labels = data.map(function(item) {
                return item.date;
            });
            var values = data.map(function(item) {
                return item.views;
            });

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'PDF Views',
                        data: values,
                        borderColor: '#2271b1',
                        backgroundColor: 'rgba(34, 113, 177, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    };

    $(document).ready(function() {
        PremiumAdmin.init();
    });

})(jQuery);

/**
 * PDF Embed & SEO Pro+ Admin Scripts
 *
 * @package PDF_Embed_SEO_Optimize
 * @subpackage Pro_Plus
 * @since 1.3.0
 */

(function($) {
    'use strict';

    /**
     * Pro+ Admin Controller
     */
    var ProPlusAdmin = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initSettingsSections();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Settings sections toggle
            $(document).on('click', '.pdf-settings-section-header', this.toggleSection);

            // Version restore
            $(document).on('click', '.pdf-restore-version', this.restoreVersion);

            // Webhook test
            $(document).on('click', '.pdf-test-webhook', this.testWebhook);

            // Logo upload
            $(document).on('click', '.pdf-upload-logo', this.uploadLogo);

            // License activation
            $(document).on('submit', '#pdf-license-form', this.activateLicense);
        },

        /**
         * Initialize settings sections (collapsed state)
         */
        initSettingsSections: function() {
            var savedState = localStorage.getItem('pdf_pro_plus_sections');

            if (savedState) {
                var collapsed = JSON.parse(savedState);

                $.each(collapsed, function(id, isCollapsed) {
                    if (isCollapsed) {
                        $('#' + id).addClass('collapsed');
                    }
                });
            }
        },

        /**
         * Toggle settings section
         */
        toggleSection: function(e) {
            var $section = $(this).closest('.pdf-settings-section');
            var sectionId = $section.attr('id');

            $section.toggleClass('collapsed');

            // Save state
            var savedState = localStorage.getItem('pdf_pro_plus_sections');
            var collapsed = savedState ? JSON.parse(savedState) : {};

            collapsed[sectionId] = $section.hasClass('collapsed');
            localStorage.setItem('pdf_pro_plus_sections', JSON.stringify(collapsed));
        },

        /**
         * Restore version
         */
        restoreVersion: function(e) {
            e.preventDefault();

            var $button = $(this);
            var versionId = $button.data('version');

            if (!confirm(pdfProPlusAdmin.i18n.confirm)) {
                return;
            }

            $button.prop('disabled', true).text('...');

            $.ajax({
                url: pdfProPlusAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pdf_restore_version',
                    version_id: versionId,
                    nonce: pdfProPlusAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert(response.data.message || 'Error restoring version');
                        $button.prop('disabled', false).text('Restore');
                    }
                },
                error: function() {
                    alert('Request failed');
                    $button.prop('disabled', false).text('Restore');
                }
            });
        },

        /**
         * Test webhook
         */
        testWebhook: function(e) {
            e.preventDefault();

            var $button = $(this);
            var $result = $button.siblings('.pdf-webhook-test-result');

            $button.prop('disabled', true).text('Testing...');
            $result.remove();

            $.ajax({
                url: pdfProPlusAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pdf_test_webhook',
                    nonce: pdfProPlusAdmin.nonce
                },
                success: function(response) {
                    var message = response.data ? response.data.message : '';
                    var cssClass = response.success ? 'success' : 'error';

                    $button.after('<div class="pdf-webhook-test-result ' + cssClass + '">' + message + '</div>');
                    $button.prop('disabled', false).text('Test Webhook');
                },
                error: function() {
                    $button.after('<div class="pdf-webhook-test-result error">Request failed</div>');
                    $button.prop('disabled', false).text('Test Webhook');
                }
            });
        },

        /**
         * Upload logo via media library
         */
        uploadLogo: function(e) {
            e.preventDefault();

            var $button = $(this);
            var $input = $button.siblings('input[type="url"]');

            // Create media frame
            var frame = wp.media({
                title: 'Select Logo',
                button: {
                    text: 'Use this image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            // On select
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $input.val(attachment.url);
            });

            frame.open();
        },

        /**
         * Activate license
         */
        activateLicense: function(e) {
            e.preventDefault();

            var $form = $(this);
            var $button = $form.find('button[type="submit"]');
            var $input = $form.find('input[name="license_key"]');
            var licenseKey = $input.val().trim();

            if (!licenseKey) {
                alert('Please enter a license key');
                return;
            }

            $button.prop('disabled', true).text('Activating...');

            $.ajax({
                url: pdfProPlusAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'save',
                    option_page: 'pdf_embed_seo_pro_plus_license',
                    pdf_embed_seo_pro_plus_license_key: licenseKey,
                    _wpnonce: $form.find('input[name="_wpnonce"]').val()
                },
                success: function() {
                    window.location.reload();
                },
                error: function() {
                    alert('Failed to activate license');
                    $button.prop('disabled', false).text('Activate License');
                }
            });
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        ProPlusAdmin.init();
    });

})(jQuery);

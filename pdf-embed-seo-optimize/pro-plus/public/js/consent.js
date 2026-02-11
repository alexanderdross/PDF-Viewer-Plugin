/**
 * PDF Embed & SEO Pro+ Consent Scripts
 *
 * @package PDF_Embed_SEO_Optimize
 * @subpackage Pro_Plus
 * @since 1.3.0
 */

(function($) {
    'use strict';

    /**
     * Consent Controller
     */
    var PdfConsent = {
        config: {},

        /**
         * Initialize consent banner
         */
        init: function() {
            if (typeof pdfConsent === 'undefined') {
                return;
            }

            this.config = pdfConsent;
            this.checkConsent();
            this.bindEvents();
        },

        /**
         * Check if consent is needed
         */
        checkConsent: function() {
            // Check cookie
            if (this.getCookie('pdf_consent_analytics')) {
                return; // Already consented/declined
            }

            // Show banner
            setTimeout(function() {
                $('#pdf-consent-banner').fadeIn();
            }, 500);
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            var self = this;

            // Accept all
            $(document).on('click', '.pdf-consent-banner .consent-accept', function() {
                self.recordConsent('analytics', true);
                self.recordConsent('tracking', true);
                self.hideBanner();
            });

            // Decline
            $(document).on('click', '.pdf-consent-banner .consent-decline', function() {
                self.recordConsent('analytics', false);
                self.hideBanner();
            });

            // Settings
            $(document).on('click', '.pdf-consent-banner .consent-settings', function() {
                self.showSettings();
            });

            // Save settings
            $(document).on('click', '.pdf-consent-settings .save-settings', function() {
                self.saveSettings();
            });

            // Close settings
            $(document).on('click', '.pdf-consent-settings .close-settings', function() {
                self.hideSettings();
            });
        },

        /**
         * Record consent
         */
        recordConsent: function(type, given) {
            // Set cookie
            this.setCookie('pdf_consent_' + type, given ? 'yes' : 'no', 365);

            // Send to server
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pdf_record_consent',
                    nonce: this.config.nonce,
                    consent_type: type,
                    given: given ? 'true' : 'false'
                }
            });

            // Trigger event
            $(document).trigger('pdf:consent:' + (given ? 'accepted' : 'declined'), { type: type });
        },

        /**
         * Hide consent banner
         */
        hideBanner: function() {
            $('#pdf-consent-banner').fadeOut();
        },

        /**
         * Show settings modal
         */
        showSettings: function() {
            var html = '<div class="pdf-consent-settings">' +
                '<div class="consent-settings-box">' +
                '<h3>' + this.config.i18n.settings + '</h3>' +
                '<div class="consent-option">' +
                '<label>' +
                '<input type="checkbox" name="consent_essential" checked disabled>' +
                ' Essential (Required)' +
                '</label>' +
                '<p class="description">Required for the PDF viewer to function.</p>' +
                '</div>' +
                '<div class="consent-option">' +
                '<label>' +
                '<input type="checkbox" name="consent_analytics" checked>' +
                ' Analytics' +
                '</label>' +
                '<p class="description">Helps us understand how you use the document.</p>' +
                '</div>' +
                '<div class="consent-option">' +
                '<label>' +
                '<input type="checkbox" name="consent_tracking" checked>' +
                ' Progress Tracking' +
                '</label>' +
                '<p class="description">Saves your reading progress.</p>' +
                '</div>' +
                '<div class="consent-actions">' +
                '<button type="button" class="close-settings">' + this.config.i18n.decline + '</button>' +
                '<button type="button" class="save-settings">' + this.config.i18n.accept + '</button>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('body').append(html);
        },

        /**
         * Hide settings modal
         */
        hideSettings: function() {
            $('.pdf-consent-settings').remove();
        },

        /**
         * Save settings
         */
        saveSettings: function() {
            var analytics = $('input[name="consent_analytics"]').is(':checked');
            var tracking = $('input[name="consent_tracking"]').is(':checked');

            this.recordConsent('analytics', analytics);
            this.recordConsent('tracking', tracking);

            this.hideSettings();
            this.hideBanner();
        },

        /**
         * Get cookie value
         */
        getCookie: function(name) {
            var value = '; ' + document.cookie;
            var parts = value.split('; ' + name + '=');

            if (parts.length === 2) {
                return parts.pop().split(';').shift();
            }

            return null;
        },

        /**
         * Set cookie value
         */
        setCookie: function(name, value, days) {
            var expires = '';

            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }

            document.cookie = name + '=' + value + expires + '; path=/; SameSite=Lax';
        }
    };

    /**
     * Add settings modal styles
     */
    $('<style>')
        .text(
            '.pdf-consent-settings {' +
            '  position: fixed;' +
            '  top: 0;' +
            '  left: 0;' +
            '  right: 0;' +
            '  bottom: 0;' +
            '  background: rgba(0, 0, 0, 0.6);' +
            '  display: flex;' +
            '  align-items: center;' +
            '  justify-content: center;' +
            '  z-index: 100000;' +
            '}' +
            '.consent-settings-box {' +
            '  background: #fff;' +
            '  border-radius: 8px;' +
            '  padding: 30px;' +
            '  max-width: 400px;' +
            '  width: 90%;' +
            '}' +
            '.consent-settings-box h3 {' +
            '  margin: 0 0 20px 0;' +
            '}' +
            '.consent-option {' +
            '  padding: 15px 0;' +
            '  border-bottom: 1px solid #eee;' +
            '}' +
            '.consent-option:last-of-type {' +
            '  border-bottom: none;' +
            '}' +
            '.consent-option label {' +
            '  font-weight: bold;' +
            '}' +
            '.consent-option .description {' +
            '  margin: 5px 0 0 24px;' +
            '  color: #666;' +
            '  font-size: 13px;' +
            '}' +
            '.consent-actions {' +
            '  display: flex;' +
            '  gap: 10px;' +
            '  margin-top: 20px;' +
            '}' +
            '.consent-actions button {' +
            '  flex: 1;' +
            '  padding: 10px 20px;' +
            '  border: none;' +
            '  border-radius: 4px;' +
            '  cursor: pointer;' +
            '}' +
            '.consent-actions .save-settings {' +
            '  background: #27ae60;' +
            '  color: #fff;' +
            '}' +
            '.consent-actions .close-settings {' +
            '  background: #eee;' +
            '  color: #333;' +
            '}'
        )
        .appendTo('head');

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        PdfConsent.init();
    });

})(jQuery);

<?php
/**
 * Pro+ 2FA Verification Form
 *
 * @package    PDF_Embed_SEO_Optimize
 * @subpackage Pro_Plus
 * @since      1.3.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$post_id = get_the_ID();
$post    = get_post( $post_id );
?>

<div class="pdf-2fa-container">
    <div class="pdf-2fa-box">
        <div class="pdf-2fa-icon">
            <span class="dashicons dashicons-shield-alt"></span>
        </div>

        <h2><?php esc_html_e( 'Verification Required', 'pdf-embed-seo-optimize' ); ?></h2>

        <p class="pdf-2fa-description">
            <?php
            printf(
                /* translators: %s: Document title */
                esc_html__( 'This document "%s" requires verification to access. Please enter your email to receive a verification code.', 'pdf-embed-seo-optimize' ),
                '<strong>' . esc_html( $post->post_title ) . '</strong>'
            );
            ?>
        </p>

        <form id="pdf-2fa-form" class="pdf-2fa-form">
            <?php wp_nonce_field( 'pdf_2fa_nonce', 'pdf_2fa_nonce' ); ?>
            <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">

            <div class="pdf-2fa-step pdf-2fa-step-email active">
                <div class="pdf-2fa-field">
                    <label for="pdf_2fa_email"><?php esc_html_e( 'Email Address', 'pdf-embed-seo-optimize' ); ?></label>
                    <input type="email" id="pdf_2fa_email" name="email" required placeholder="<?php esc_attr_e( 'your@email.com', 'pdf-embed-seo-optimize' ); ?>">
                </div>

                <button type="button" class="pdf-2fa-send-code button button-primary">
                    <?php esc_html_e( 'Send Verification Code', 'pdf-embed-seo-optimize' ); ?>
                </button>
            </div>

            <div class="pdf-2fa-step pdf-2fa-step-code">
                <div class="pdf-2fa-field">
                    <label for="pdf_2fa_code"><?php esc_html_e( 'Verification Code', 'pdf-embed-seo-optimize' ); ?></label>
                    <input type="text" id="pdf_2fa_code" name="code" maxlength="6" pattern="[0-9]{6}" placeholder="000000" autocomplete="one-time-code">
                </div>

                <p class="pdf-2fa-hint">
                    <?php esc_html_e( 'Enter the 6-digit code sent to your email.', 'pdf-embed-seo-optimize' ); ?>
                </p>

                <button type="submit" class="pdf-2fa-verify button button-primary">
                    <?php esc_html_e( 'Verify & Access Document', 'pdf-embed-seo-optimize' ); ?>
                </button>

                <p class="pdf-2fa-resend">
                    <a href="#" class="pdf-2fa-resend-link"><?php esc_html_e( 'Didn\'t receive the code? Send again', 'pdf-embed-seo-optimize' ); ?></a>
                </p>
            </div>

            <div class="pdf-2fa-message"></div>
        </form>
    </div>
</div>

<style>
.pdf-2fa-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 400px;
    padding: 40px 20px;
}

.pdf-2fa-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 40px;
    max-width: 450px;
    width: 100%;
    text-align: center;
}

.pdf-2fa-icon {
    margin-bottom: 20px;
}

.pdf-2fa-icon .dashicons {
    font-size: 64px;
    width: 64px;
    height: 64px;
    color: #764ba2;
}

.pdf-2fa-box h2 {
    margin: 0 0 15px 0;
    color: #23282d;
}

.pdf-2fa-description {
    color: #666;
    margin-bottom: 30px;
}

.pdf-2fa-field {
    margin-bottom: 20px;
    text-align: left;
}

.pdf-2fa-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #23282d;
}

.pdf-2fa-field input {
    width: 100%;
    padding: 12px 15px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.pdf-2fa-field input:focus {
    border-color: #764ba2;
    outline: none;
    box-shadow: 0 0 0 2px rgba(118, 75, 162, 0.2);
}

#pdf_2fa_code {
    text-align: center;
    font-size: 24px;
    font-family: monospace;
    letter-spacing: 5px;
}

.pdf-2fa-form button {
    width: 100%;
    padding: 12px 20px;
    font-size: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: #fff;
    cursor: pointer;
    transition: opacity 0.2s;
}

.pdf-2fa-form button:hover {
    opacity: 0.9;
}

.pdf-2fa-form button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.pdf-2fa-hint {
    color: #666;
    font-size: 13px;
    margin-bottom: 20px;
}

.pdf-2fa-step {
    display: none;
}

.pdf-2fa-step.active {
    display: block;
}

.pdf-2fa-resend {
    margin-top: 15px;
}

.pdf-2fa-resend-link {
    color: #764ba2;
    text-decoration: none;
}

.pdf-2fa-resend-link:hover {
    text-decoration: underline;
}

.pdf-2fa-message {
    margin-top: 15px;
    padding: 10px;
    border-radius: 4px;
    display: none;
}

.pdf-2fa-message.success {
    display: block;
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.pdf-2fa-message.error {
    display: block;
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<script>
(function($) {
    'use strict';

    var $form = $('#pdf-2fa-form');
    var $message = $form.find('.pdf-2fa-message');
    var $stepEmail = $form.find('.pdf-2fa-step-email');
    var $stepCode = $form.find('.pdf-2fa-step-code');

    function showMessage(text, type) {
        $message.removeClass('success error').addClass(type).text(text).show();
    }

    function hideMessage() {
        $message.hide().removeClass('success error');
    }

    // Send code
    $form.on('click', '.pdf-2fa-send-code, .pdf-2fa-resend-link', function(e) {
        e.preventDefault();

        var $button = $form.find('.pdf-2fa-send-code');
        var email = $('#pdf_2fa_email').val();

        if (!email) {
            showMessage('<?php echo esc_js( __( 'Please enter your email address.', 'pdf-embed-seo-optimize' ) ); ?>', 'error');
            return;
        }

        hideMessage();
        $button.prop('disabled', true).text('<?php echo esc_js( __( 'Sending...', 'pdf-embed-seo-optimize' ) ); ?>');

        $.ajax({
            url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
            type: 'POST',
            data: {
                action: 'pdf_send_2fa',
                nonce: $('#pdf_2fa_nonce').val(),
                post_id: $form.find('input[name="post_id"]').val(),
                email: email
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data.message, 'success');
                    $stepEmail.removeClass('active');
                    $stepCode.addClass('active');
                    $('#pdf_2fa_code').focus();
                } else {
                    showMessage(response.data.message || '<?php echo esc_js( __( 'Failed to send code.', 'pdf-embed-seo-optimize' ) ); ?>', 'error');
                }
            },
            error: function() {
                showMessage('<?php echo esc_js( __( 'Request failed. Please try again.', 'pdf-embed-seo-optimize' ) ); ?>', 'error');
            },
            complete: function() {
                $button.prop('disabled', false).text('<?php echo esc_js( __( 'Send Verification Code', 'pdf-embed-seo-optimize' ) ); ?>');
            }
        });
    });

    // Verify code
    $form.on('submit', function(e) {
        e.preventDefault();

        var $button = $form.find('.pdf-2fa-verify');
        var code = $('#pdf_2fa_code').val();

        if (!code || code.length !== 6) {
            showMessage('<?php echo esc_js( __( 'Please enter a valid 6-digit code.', 'pdf-embed-seo-optimize' ) ); ?>', 'error');
            return;
        }

        hideMessage();
        $button.prop('disabled', true).text('<?php echo esc_js( __( 'Verifying...', 'pdf-embed-seo-optimize' ) ); ?>');

        $.ajax({
            url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
            type: 'POST',
            data: {
                action: 'pdf_verify_2fa',
                nonce: $('#pdf_2fa_nonce').val(),
                post_id: $form.find('input[name="post_id"]').val(),
                email: $('#pdf_2fa_email').val(),
                code: code
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data.message, 'success');
                    if (response.data.redirect) {
                        setTimeout(function() {
                            window.location.href = response.data.redirect;
                        }, 1000);
                    }
                } else {
                    showMessage(response.data.message || '<?php echo esc_js( __( 'Invalid code.', 'pdf-embed-seo-optimize' ) ); ?>', 'error');
                    $button.prop('disabled', false).text('<?php echo esc_js( __( 'Verify & Access Document', 'pdf-embed-seo-optimize' ) ); ?>');
                }
            },
            error: function() {
                showMessage('<?php echo esc_js( __( 'Request failed. Please try again.', 'pdf-embed-seo-optimize' ) ); ?>', 'error');
                $button.prop('disabled', false).text('<?php echo esc_js( __( 'Verify & Access Document', 'pdf-embed-seo-optimize' ) ); ?>');
            }
        });
    });

})(jQuery);
</script>

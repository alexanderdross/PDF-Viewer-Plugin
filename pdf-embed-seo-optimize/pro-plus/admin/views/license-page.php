<?php
/**
 * Pro+ License Page
 *
 * @package    PDF_Embed_SEO_Optimize
 * @subpackage Pro_Plus
 * @since      1.3.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$license_key    = get_option( 'pdf_embed_seo_pro_plus_license_key', '' );
$license_status = pdf_embed_seo_pro_plus()->get_license_status();
$expires        = get_option( 'pdf_embed_seo_pro_plus_license_expires', '' );

// Status labels and icons.
$status_info = array(
    'valid'        => array(
        'label' => __( 'License Active', 'pdf-embed-seo-optimize' ),
        'icon'  => '&#10004;',
        'class' => 'valid',
        'desc'  => __( 'Your Pro+ license is active. All enterprise features are enabled.', 'pdf-embed-seo-optimize' ),
    ),
    'grace_period' => array(
        'label' => __( 'Grace Period', 'pdf-embed-seo-optimize' ),
        'icon'  => '&#9888;',
        'class' => 'grace_period',
        'desc'  => __( 'Your license has expired but is in the 14-day grace period. Please renew to continue using Pro+ features.', 'pdf-embed-seo-optimize' ),
    ),
    'expired'      => array(
        'label' => __( 'License Expired', 'pdf-embed-seo-optimize' ),
        'icon'  => '&#10006;',
        'class' => 'expired',
        'desc'  => __( 'Your Pro+ license has expired. Please renew to reactivate enterprise features.', 'pdf-embed-seo-optimize' ),
    ),
    'invalid'      => array(
        'label' => __( 'Invalid License', 'pdf-embed-seo-optimize' ),
        'icon'  => '&#10006;',
        'class' => 'invalid',
        'desc'  => __( 'The license key you entered is invalid. Please check your license key and try again.', 'pdf-embed-seo-optimize' ),
    ),
    'inactive'     => array(
        'label' => __( 'No License', 'pdf-embed-seo-optimize' ),
        'icon'  => '&#9679;',
        'class' => 'inactive',
        'desc'  => __( 'Enter your Pro+ license key to activate enterprise features.', 'pdf-embed-seo-optimize' ),
    ),
);

$current_status = isset( $status_info[ $license_status ] ) ? $status_info[ $license_status ] : $status_info['inactive'];
?>

<div class="wrap pdf-license-page">
    <h1><?php esc_html_e( 'PDF Embed & SEO Pro+ License', 'pdf-embed-seo-optimize' ); ?></h1>

    <div class="pdf-license-box">
        <h2><?php esc_html_e( 'License Status', 'pdf-embed-seo-optimize' ); ?></h2>

        <div class="pdf-license-status <?php echo esc_attr( $current_status['class'] ); ?>">
            <span class="pdf-license-status-icon"><?php echo $current_status['icon']; ?></span>
            <div class="pdf-license-status-text">
                <h3><?php echo esc_html( $current_status['label'] ); ?></h3>
                <p><?php echo esc_html( $current_status['desc'] ); ?></p>
                <?php if ( $expires && in_array( $license_status, array( 'valid', 'grace_period' ), true ) ) : ?>
                    <p>
                        <strong><?php esc_html_e( 'Expires:', 'pdf-embed-seo-optimize' ); ?></strong>
                        <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $expires ) ) ); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <form method="post" action="options.php" id="pdf-license-form">
            <?php settings_fields( 'pdf_embed_seo_pro_plus_license' ); ?>

            <div class="pdf-license-input">
                <input type="text" name="pdf_embed_seo_pro_plus_license_key" id="pdf_embed_seo_pro_plus_license_key"
                       value="<?php echo esc_attr( $license_key ); ?>"
                       placeholder="PDF$PRO+#XXXX-XXXX@XXXX-XXXX!XXXX"
                       class="regular-text" />
                <button type="submit" class="button button-primary">
                    <?php echo $license_key ? esc_html__( 'Update License', 'pdf-embed-seo-optimize' ) : esc_html__( 'Activate License', 'pdf-embed-seo-optimize' ); ?>
                </button>
            </div>

            <p class="description">
                <?php
                printf(
                    /* translators: %s: Purchase URL */
                    esc_html__( 'Don\'t have a license? %s', 'pdf-embed-seo-optimize' ),
                    '<a href="https://pdfviewer.drossmedia.de/pro-plus" target="_blank">' . esc_html__( 'Purchase Pro+ License', 'pdf-embed-seo-optimize' ) . '</a>'
                );
                ?>
            </p>
        </form>
    </div>

    <div class="pdf-license-box">
        <h2><?php esc_html_e( 'Pro+ Features', 'pdf-embed-seo-optimize' ); ?></h2>

        <table class="widefat">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Feature', 'pdf-embed-seo-optimize' ); ?></th>
                    <th><?php esc_html_e( 'Description', 'pdf-embed-seo-optimize' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'pdf-embed-seo-optimize' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $features = array(
                    'advanced_analytics' => array(
                        'name' => __( 'Advanced Analytics', 'pdf-embed-seo-optimize' ),
                        'desc' => __( 'Heatmaps, engagement scoring, geographic & device tracking', 'pdf-embed-seo-optimize' ),
                    ),
                    'security' => array(
                        'name' => __( 'Security & 2FA', 'pdf-embed-seo-optimize' ),
                        'desc' => __( 'Two-factor authentication, IP whitelisting, audit logs', 'pdf-embed-seo-optimize' ),
                    ),
                    'webhooks' => array(
                        'name' => __( 'Webhooks', 'pdf-embed-seo-optimize' ),
                        'desc' => __( 'External integrations via webhook notifications', 'pdf-embed-seo-optimize' ),
                    ),
                    'white_label' => array(
                        'name' => __( 'White Label', 'pdf-embed-seo-optimize' ),
                        'desc' => __( 'Custom branding and attribution removal', 'pdf-embed-seo-optimize' ),
                    ),
                    'versioning' => array(
                        'name' => __( 'Document Versioning', 'pdf-embed-seo-optimize' ),
                        'desc' => __( 'Track and restore document versions', 'pdf-embed-seo-optimize' ),
                    ),
                    'annotations' => array(
                        'name' => __( 'Annotations & Signatures', 'pdf-embed-seo-optimize' ),
                        'desc' => __( 'User annotations and digital signatures', 'pdf-embed-seo-optimize' ),
                    ),
                    'compliance' => array(
                        'name' => __( 'Compliance', 'pdf-embed-seo-optimize' ),
                        'desc' => __( 'GDPR and HIPAA compliance tools', 'pdf-embed-seo-optimize' ),
                    ),
                );

                $pro_plus       = pdf_embed_seo_pro_plus();
                $is_valid       = $pro_plus->is_license_valid();

                foreach ( $features as $key => $feature ) :
                    $enabled = $is_valid && ! empty( $pro_plus->$key );
                ?>
                    <tr>
                        <td><strong><?php echo esc_html( $feature['name'] ); ?></strong></td>
                        <td><?php echo esc_html( $feature['desc'] ); ?></td>
                        <td>
                            <?php if ( $enabled ) : ?>
                                <span style="color: #46b450;">&#10004; <?php esc_html_e( 'Active', 'pdf-embed-seo-optimize' ); ?></span>
                            <?php elseif ( $is_valid ) : ?>
                                <span style="color: #ffb900;">&#9679; <?php esc_html_e( 'Disabled', 'pdf-embed-seo-optimize' ); ?></span>
                            <?php else : ?>
                                <span style="color: #999;">&#9679; <?php esc_html_e( 'Requires License', 'pdf-embed-seo-optimize' ); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ( $is_valid ) : ?>
            <p style="margin-top: 15px;">
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-pro-plus-settings' ) ); ?>" class="button">
                    <?php esc_html_e( 'Configure Pro+ Features', 'pdf-embed-seo-optimize' ); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>

    <div class="pdf-license-box">
        <h2><?php esc_html_e( 'License Information', 'pdf-embed-seo-optimize' ); ?></h2>

        <table class="widefat">
            <tbody>
                <tr>
                    <th><?php esc_html_e( 'Site URL', 'pdf-embed-seo-optimize' ); ?></th>
                    <td><?php echo esc_html( home_url() ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Pro+ Version', 'pdf-embed-seo-optimize' ); ?></th>
                    <td><?php echo esc_html( PDF_EMBED_SEO_PRO_PLUS_VERSION ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Premium Version', 'pdf-embed-seo-optimize' ); ?></th>
                    <td>
                        <?php
                        if ( defined( 'PDF_EMBED_SEO_PREMIUM_VERSION' ) ) {
                            echo esc_html( PDF_EMBED_SEO_PREMIUM_VERSION );
                        } else {
                            echo '<span style="color: #dc3232;">' . esc_html__( 'Not Installed', 'pdf-embed-seo-optimize' ) . '</span>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'PHP Version', 'pdf-embed-seo-optimize' ); ?></th>
                    <td><?php echo esc_html( PHP_VERSION ); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

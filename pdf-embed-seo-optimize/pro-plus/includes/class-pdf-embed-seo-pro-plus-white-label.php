<?php
/**
 * Pro+ White Label - Custom Branding & Attribution Control.
 *
 * @package    PDF_Embed_SEO_Optimize
 * @subpackage Pro_Plus
 * @since      1.3.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * White Label class.
 *
 * @since 1.3.0
 */
class PDF_Embed_SEO_Pro_Plus_White_Label {

    /**
     * Constructor.
     *
     * @since 1.3.0
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.3.0
     */
    private function init_hooks() {
        // Filter plugin branding.
        add_filter( 'pdf_embed_seo_branding', array( $this, 'filter_branding' ) );

        // Custom CSS.
        add_action( 'wp_head', array( $this, 'output_custom_css' ), 100 );
        add_action( 'admin_head', array( $this, 'output_custom_css' ), 100 );

        // Hide powered by.
        add_filter( 'pdf_embed_seo_show_powered_by', array( $this, 'filter_powered_by' ) );

        // Custom logo in viewer.
        add_filter( 'pdf_embed_seo_viewer_logo', array( $this, 'filter_viewer_logo' ) );

        // Admin footer branding.
        add_filter( 'admin_footer_text', array( $this, 'filter_admin_footer' ), 100 );

        // Plugin name filter.
        add_filter( 'all_plugins', array( $this, 'filter_plugin_name' ) );
    }

    /**
     * Filter branding settings.
     *
     * @since 1.3.0
     * @param array $branding Branding settings.
     * @return array
     */
    public function filter_branding( $branding ) {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( empty( $settings['custom_branding'] ) ) {
            return $branding;
        }

        $custom_branding = array(
            'name'        => get_option( 'pdf_pro_plus_brand_name', $branding['name'] ?? 'PDF Embed & SEO' ),
            'description' => get_option( 'pdf_pro_plus_brand_description', $branding['description'] ?? '' ),
            'logo_url'    => $settings['custom_logo_url'] ?: ( $branding['logo_url'] ?? '' ),
            'color'       => get_option( 'pdf_pro_plus_brand_color', $branding['color'] ?? '#764ba2' ),
        );

        return array_merge( $branding, $custom_branding );
    }

    /**
     * Output custom CSS.
     *
     * @since 1.3.0
     */
    public function output_custom_css() {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( empty( $settings['custom_css'] ) ) {
            return;
        }

        echo '<style id="pdf-pro-plus-custom-css">' . "\n";
        echo wp_strip_all_tags( $settings['custom_css'] );
        echo "\n</style>\n";
    }

    /**
     * Filter powered by display.
     *
     * @since 1.3.0
     * @param bool $show Whether to show powered by.
     * @return bool
     */
    public function filter_powered_by( $show ) {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( ! empty( $settings['hide_powered_by'] ) ) {
            return false;
        }

        return $show;
    }

    /**
     * Filter viewer logo.
     *
     * @since 1.3.0
     * @param string $logo_url Logo URL.
     * @return string
     */
    public function filter_viewer_logo( $logo_url ) {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( ! empty( $settings['custom_logo_url'] ) ) {
            return $settings['custom_logo_url'];
        }

        return $logo_url;
    }

    /**
     * Filter admin footer text.
     *
     * @since 1.3.0
     * @param string $text Footer text.
     * @return string
     */
    public function filter_admin_footer( $text ) {
        $screen = get_current_screen();

        if ( ! $screen || 'pdf_document' !== $screen->post_type ) {
            return $text;
        }

        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( ! empty( $settings['hide_powered_by'] ) ) {
            return $text;
        }

        $brand_name = get_option( 'pdf_pro_plus_brand_name', 'PDF Embed & SEO Pro+' );

        return sprintf(
            /* translators: %s: Brand name */
            esc_html__( 'Powered by %s', 'pdf-embed-seo-optimize' ),
            '<strong>' . esc_html( $brand_name ) . '</strong>'
        );
    }

    /**
     * Filter plugin name in plugins list.
     *
     * @since 1.3.0
     * @param array $plugins Plugins list.
     * @return array
     */
    public function filter_plugin_name( $plugins ) {
        $settings = pdf_embed_seo_pro_plus()->get_settings();

        if ( empty( $settings['custom_branding'] ) ) {
            return $plugins;
        }

        $brand_name = get_option( 'pdf_pro_plus_brand_name', '' );

        if ( empty( $brand_name ) ) {
            return $plugins;
        }

        // Find and update the plugin name.
        $plugin_file = 'pdf-embed-seo-optimize/pdf-embed-seo-optimize.php';

        if ( isset( $plugins[ $plugin_file ] ) ) {
            $plugins[ $plugin_file ]['Name'] = $brand_name;
        }

        return $plugins;
    }

    /**
     * Get white label settings form HTML.
     *
     * @since 1.3.0
     * @return string
     */
    public function get_settings_form() {
        $settings   = pdf_embed_seo_pro_plus()->get_settings();
        $brand_name = get_option( 'pdf_pro_plus_brand_name', '' );

        ob_start();
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="pdf_pro_plus_custom_branding"><?php esc_html_e( 'Enable Custom Branding', 'pdf-embed-seo-optimize' ); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[custom_branding]" id="pdf_pro_plus_custom_branding" value="1" <?php checked( $settings['custom_branding'] ); ?>>
                    <p class="description"><?php esc_html_e( 'Replace plugin branding with your own.', 'pdf-embed-seo-optimize' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="pdf_pro_plus_brand_name"><?php esc_html_e( 'Brand Name', 'pdf-embed-seo-optimize' ); ?></label>
                </th>
                <td>
                    <input type="text" name="pdf_pro_plus_brand_name" id="pdf_pro_plus_brand_name" value="<?php echo esc_attr( $brand_name ); ?>" class="regular-text">
                    <p class="description"><?php esc_html_e( 'Custom name to display instead of "PDF Embed & SEO".', 'pdf-embed-seo-optimize' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="pdf_pro_plus_hide_powered_by"><?php esc_html_e( 'Hide "Powered By"', 'pdf-embed-seo-optimize' ); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="pdf_embed_seo_pro_plus_settings[hide_powered_by]" id="pdf_pro_plus_hide_powered_by" value="1" <?php checked( $settings['hide_powered_by'] ); ?>>
                    <p class="description"><?php esc_html_e( 'Remove all plugin attribution from the viewer.', 'pdf-embed-seo-optimize' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="pdf_pro_plus_custom_logo_url"><?php esc_html_e( 'Custom Logo URL', 'pdf-embed-seo-optimize' ); ?></label>
                </th>
                <td>
                    <input type="url" name="pdf_embed_seo_pro_plus_settings[custom_logo_url]" id="pdf_pro_plus_custom_logo_url" value="<?php echo esc_url( $settings['custom_logo_url'] ); ?>" class="regular-text">
                    <button type="button" class="button pdf-upload-logo"><?php esc_html_e( 'Upload Logo', 'pdf-embed-seo-optimize' ); ?></button>
                    <p class="description"><?php esc_html_e( 'Logo to display in the PDF viewer toolbar.', 'pdf-embed-seo-optimize' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="pdf_pro_plus_custom_css"><?php esc_html_e( 'Custom CSS', 'pdf-embed-seo-optimize' ); ?></label>
                </th>
                <td>
                    <textarea name="pdf_embed_seo_pro_plus_settings[custom_css]" id="pdf_pro_plus_custom_css" rows="10" class="large-text code"><?php echo esc_textarea( $settings['custom_css'] ); ?></textarea>
                    <p class="description"><?php esc_html_e( 'Add custom CSS to style the PDF viewer and admin pages.', 'pdf-embed-seo-optimize' ); ?></p>
                </td>
            </tr>
        </table>
        <?php
        return ob_get_clean();
    }
}

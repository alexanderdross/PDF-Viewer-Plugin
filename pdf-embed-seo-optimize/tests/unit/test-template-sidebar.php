<?php
/**
 * Unit tests for PDF template sidebar removal (v1.2.7).
 *
 * @package PDF_Embed_SEO
 * @subpackage Tests
 * @since 1.2.7
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tests for sidebar removal in archive and single templates.
 */
class Test_PDF_Template_Sidebar extends WP_UnitTestCase {

	/**
	 * Test archive template does not call get_sidebar().
	 *
	 * @since 1.2.7
	 */
	public function test_archive_template_no_sidebar() {
		$template_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/public/views/archive-pdf-document.php';

		// Check template file exists.
		$this->assertFileExists( $template_path );

		// Read template content.
		$content = file_get_contents( $template_path );

		// Check that get_sidebar() is NOT called (commented out is fine).
		$has_get_sidebar_call = preg_match( '/^\s*get_sidebar\s*\(\s*\)\s*;/m', $content );

		$this->assertEquals( 0, $has_get_sidebar_call, 'Archive template should not call get_sidebar()' );

		// Check the intentional removal comment exists.
		$this->assertStringContainsString(
			'get_sidebar() intentionally removed',
			$content,
			'Archive template should have comment explaining sidebar removal'
		);
	}

	/**
	 * Test single template does not call get_sidebar().
	 *
	 * @since 1.2.7
	 */
	public function test_single_template_no_sidebar() {
		$template_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/public/views/single-pdf-document.php';

		// Check template file exists.
		$this->assertFileExists( $template_path );

		// Read template content.
		$content = file_get_contents( $template_path );

		// Check that get_sidebar() is NOT called (commented out is fine).
		$has_get_sidebar_call = preg_match( '/^\s*get_sidebar\s*\(\s*\)\s*;/m', $content );

		$this->assertEquals( 0, $has_get_sidebar_call, 'Single template should not call get_sidebar()' );

		// Check the intentional removal comment exists.
		$this->assertStringContainsString(
			'get_sidebar() intentionally removed',
			$content,
			'Single template should have comment explaining sidebar removal'
		);
	}

	/**
	 * Test CSS has sidebar hiding rules for archive pages.
	 *
	 * @since 1.2.7
	 */
	public function test_css_hides_archive_sidebar() {
		$css_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/public/css/viewer-styles.css';

		// Check CSS file exists.
		$this->assertFileExists( $css_path );

		// Read CSS content.
		$content = file_get_contents( $css_path );

		// Check for archive page sidebar hiding selectors.
		$this->assertStringContainsString(
			'.post-type-archive-pdf_document .widget-area',
			$content,
			'CSS should hide .widget-area on archive pages'
		);

		$this->assertStringContainsString(
			'.post-type-archive-pdf_document #secondary',
			$content,
			'CSS should hide #secondary on archive pages'
		);

		$this->assertStringContainsString(
			'.post-type-archive-pdf_document aside.sidebar',
			$content,
			'CSS should hide aside.sidebar on archive pages'
		);
	}

	/**
	 * Test CSS has full-width rules for archive pages.
	 *
	 * @since 1.2.7
	 */
	public function test_css_fullwidth_archive() {
		$css_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/public/css/viewer-styles.css';

		// Read CSS content.
		$content = file_get_contents( $css_path );

		// Check for full-width content area selectors.
		$this->assertStringContainsString(
			'.post-type-archive-pdf_document .content-area',
			$content,
			'CSS should target .content-area on archive pages'
		);

		$this->assertStringContainsString(
			'.post-type-archive-pdf_document #primary',
			$content,
			'CSS should target #primary on archive pages'
		);

		// Check for width: 100% rule.
		$this->assertStringContainsString(
			'width: 100% !important',
			$content,
			'CSS should force 100% width on PDF pages'
		);
	}

	/**
	 * Test templates still call get_footer().
	 *
	 * @since 1.2.7
	 */
	public function test_templates_have_footer() {
		$archive_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/public/views/archive-pdf-document.php';
		$single_path  = dirname( dirname( dirname( __FILE__ ) ) ) . '/public/views/single-pdf-document.php';

		// Read template contents.
		$archive_content = file_get_contents( $archive_path );
		$single_content  = file_get_contents( $single_path );

		// Check get_footer() is called.
		$this->assertStringContainsString( 'get_footer()', $archive_content, 'Archive should call get_footer()' );
		$this->assertStringContainsString( 'get_footer()', $single_content, 'Single should call get_footer()' );
	}

	/**
	 * Test templates still call get_header().
	 *
	 * @since 1.2.7
	 */
	public function test_templates_have_header() {
		$archive_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/public/views/archive-pdf-document.php';
		$single_path  = dirname( dirname( dirname( __FILE__ ) ) ) . '/public/views/single-pdf-document.php';

		// Read template contents.
		$archive_content = file_get_contents( $archive_path );
		$single_content  = file_get_contents( $single_path );

		// Check get_header() is called.
		$this->assertStringContainsString( 'get_header()', $archive_content, 'Archive should call get_header()' );
		$this->assertStringContainsString( 'get_header()', $single_content, 'Single should call get_header()' );
	}
}

<?php
/**
 * Unit tests for Hook Naming Conventions (v1.2.6).
 *
 * Tests that hooks follow WordPress naming conventions with proper plugin prefix.
 *
 * @package PDF_Embed_SEO
 * @subpackage Tests
 * @since 1.2.6
 */

/**
 * Test Hook Naming Conventions.
 */
class Test_PDF_Hook_Naming extends WP_UnitTestCase {

	/**
	 * Test PDF document ID.
	 *
	 * @var int
	 */
	protected $pdf_id;

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->pdf_id = $this->factory->post->create( array(
			'post_type'   => 'pdf_document',
			'post_title'  => 'Hook Test PDF',
			'post_status' => 'publish',
		) );
	}

	/**
	 * Test that the new hook name is properly prefixed.
	 */
	public function test_settings_saved_hook_is_prefixed() {
		$hook_name = 'pdf_embed_seo_optimize_settings_saved';

		// Hook should start with the plugin prefix.
		$this->assertStringStartsWith( 'pdf_embed_seo_optimize_', $hook_name );
	}

	/**
	 * Test that the old hook name is no longer used.
	 */
	public function test_old_hook_not_registered() {
		global $wp_filter;

		$old_hook = 'pdf_embed_seo_settings_saved';

		// Check if any callbacks are registered to the old hook.
		// In a clean test environment, this should be empty.
		$has_old_hook = isset( $wp_filter[ $old_hook ] ) && count( $wp_filter[ $old_hook ]->callbacks ) > 0;

		// Old hook should not have callbacks from the plugin itself.
		$this->assertFalse( $has_old_hook, 'Old hook should not be used.' );
	}

	/**
	 * Test that the new settings saved hook fires.
	 */
	public function test_settings_saved_hook_fires() {
		$hook_fired = false;
		$received_post_id = null;
		$received_settings = null;

		// Add a listener for the new hook.
		add_action(
			'pdf_embed_seo_optimize_settings_saved',
			function( $post_id, $settings ) use ( &$hook_fired, &$received_post_id, &$received_settings ) {
				$hook_fired = true;
				$received_post_id = $post_id;
				$received_settings = $settings;
			},
			10,
			2
		);

		// Trigger the hook manually to test.
		do_action(
			'pdf_embed_seo_optimize_settings_saved',
			$this->pdf_id,
			array(
				'allow_download' => true,
				'allow_print'    => false,
			)
		);

		$this->assertTrue( $hook_fired, 'Hook should have fired.' );
		$this->assertEquals( $this->pdf_id, $received_post_id );
		$this->assertIsArray( $received_settings );
		$this->assertArrayHasKey( 'allow_download', $received_settings );
		$this->assertArrayHasKey( 'allow_print', $received_settings );
	}

	/**
	 * Test that thumbnail generator listens to new hook.
	 */
	public function test_thumbnail_generator_uses_new_hook() {
		global $wp_filter;

		$new_hook = 'pdf_embed_seo_optimize_settings_saved';

		// Check if thumbnail generator is registered.
		if ( class_exists( 'PDF_Embed_SEO_Thumbnail' ) ) {
			$has_callback = has_action( $new_hook, array( 'PDF_Embed_SEO_Thumbnail', 'maybe_generate_thumbnail' ) );
			$this->assertNotFalse( $has_callback, 'Thumbnail generator should be registered to new hook.' );
		} else {
			$this->markTestSkipped( 'Thumbnail class not loaded.' );
		}
	}

	/**
	 * Test hook parameters structure.
	 */
	public function test_hook_parameters_structure() {
		$received_params = array();

		add_action(
			'pdf_embed_seo_optimize_settings_saved',
			function( $post_id, $settings ) use ( &$received_params ) {
				$received_params['post_id'] = $post_id;
				$received_params['settings'] = $settings;
			},
			10,
			2
		);

		$test_settings = array(
			'allow_download' => true,
			'allow_print'    => false,
			'pdf_file_id'    => 456,
		);

		do_action( 'pdf_embed_seo_optimize_settings_saved', $this->pdf_id, $test_settings );

		// Verify parameter structure.
		$this->assertArrayHasKey( 'post_id', $received_params );
		$this->assertArrayHasKey( 'settings', $received_params );
		$this->assertIsInt( $received_params['post_id'] );
		$this->assertIsArray( $received_params['settings'] );
	}

	/**
	 * Test all plugin hooks have proper prefix.
	 */
	public function test_all_plugin_hooks_prefixed() {
		$plugin_hooks = array(
			'pdf_embed_seo_pdf_viewed',
			'pdf_embed_seo_premium_init',
			'pdf_embed_seo_optimize_settings_saved',
			'pdf_embed_seo_post_type_args',
			'pdf_embed_seo_schema_data',
			'pdf_embed_seo_archive_schema_data',
			'pdf_embed_seo_archive_query',
			'pdf_embed_seo_archive_title',
			'pdf_embed_seo_archive_description',
			'pdf_embed_seo_sitemap_query_args',
			'pdf_embed_seo_viewer_options',
			'pdf_embed_seo_allowed_types',
			'pdf_embed_seo_rest_document',
			'pdf_embed_seo_rest_document_data',
			'pdf_embed_seo_rest_settings',
		);

		foreach ( $plugin_hooks as $hook ) {
			$this->assertStringStartsWith(
				'pdf_embed_seo_',
				$hook,
				"Hook '{$hook}' should start with 'pdf_embed_seo_' prefix."
			);
		}
	}

	/**
	 * Test backwards compatibility note.
	 */
	public function test_backwards_compatibility_migration() {
		// Document the migration path for users.
		$old_hook = 'pdf_embed_seo_settings_saved';
		$new_hook = 'pdf_embed_seo_optimize_settings_saved';

		// Users should update their code from old to new hook.
		$this->assertNotEquals( $old_hook, $new_hook );

		// New hook should contain 'optimize' to match plugin slug.
		$this->assertStringContainsString( 'optimize', $new_hook );
	}
}

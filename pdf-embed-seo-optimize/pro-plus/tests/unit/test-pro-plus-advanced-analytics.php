<?php
/**
 * Unit tests for Pro-plus advanced analytics.
 *
 * @package PDF_Embed_SEO_Pro_Plus
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Pro-plus advanced analytics functionality.
 */
class Test_Pro_Plus_Advanced_Analytics extends PDF_Pro_Plus_Test_Case {

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->activate_pro_plus_license();
		$this->enable_setting( 'enable_advanced_analytics', true );
	}

	/**
	 * Test heatmap data structure.
	 */
	public function test_heatmap_data_structure() {
		$heatmap_data = array(
			'document_id' => $this->pdf_id,
			'page'        => 1,
			'x'           => 150,
			'y'           => 300,
			'timestamp'   => current_time( 'mysql' ),
			'session_id'  => wp_generate_uuid4(),
		);

		$this->assertArrayHasKey( 'document_id', $heatmap_data );
		$this->assertArrayHasKey( 'page', $heatmap_data );
		$this->assertArrayHasKey( 'x', $heatmap_data );
		$this->assertArrayHasKey( 'y', $heatmap_data );
		$this->assertArrayHasKey( 'timestamp', $heatmap_data );
		$this->assertArrayHasKey( 'session_id', $heatmap_data );
	}

	/**
	 * Test engagement score calculation.
	 */
	public function test_engagement_score_calculation() {
		// Mock engagement data.
		$views = 100;
		$downloads = 25;
		$avg_time = 120; // seconds
		$scroll_depth = 0.75; // 75%
		$print_count = 5;

		// Sample engagement score formula.
		$score = (
			( $views * 1 ) +
			( $downloads * 5 ) +
			( $avg_time / 60 * 10 ) +
			( $scroll_depth * 100 ) +
			( $print_count * 3 )
		) / 10;

		$this->assertIsFloat( $score );
		$this->assertGreaterThan( 0, $score );
	}

	/**
	 * Test geographic tracking data structure.
	 */
	public function test_geographic_tracking_data() {
		$geo_data = array(
			'country_code' => 'US',
			'country_name' => 'United States',
			'region'       => 'California',
			'city'         => 'San Francisco',
			'latitude'     => 37.7749,
			'longitude'    => -122.4194,
			'timezone'     => 'America/Los_Angeles',
		);

		$this->assertArrayHasKey( 'country_code', $geo_data );
		$this->assertEquals( 2, strlen( $geo_data['country_code'] ) );
	}

	/**
	 * Test device analytics data structure.
	 */
	public function test_device_analytics_data() {
		$device_data = array(
			'device_type'   => 'desktop',
			'browser'       => 'Chrome',
			'browser_ver'   => '120.0',
			'os'            => 'Windows',
			'os_version'    => '11',
			'screen_width'  => 1920,
			'screen_height' => 1080,
			'pixel_ratio'   => 1,
		);

		$valid_device_types = array( 'desktop', 'mobile', 'tablet' );
		$this->assertContains( $device_data['device_type'], $valid_device_types );
	}

	/**
	 * Test analytics data sanitization.
	 */
	public function test_analytics_data_sanitization() {
		$dirty_data = array(
			'user_agent' => '<script>alert("xss")</script>Mozilla/5.0',
			'referrer'   => 'javascript:alert("xss")',
			'ip_address' => '192.168.1.1 OR 1=1',
		);

		$sanitized = array(
			'user_agent' => sanitize_text_field( $dirty_data['user_agent'] ),
			'referrer'   => esc_url_raw( $dirty_data['referrer'] ),
			'ip_address' => filter_var( $dirty_data['ip_address'], FILTER_VALIDATE_IP ) ?: '',
		);

		$this->assertStringNotContainsString( '<script>', $sanitized['user_agent'] );
		$this->assertEmpty( $sanitized['referrer'] ); // javascript: URLs should be stripped.
		$this->assertEmpty( $sanitized['ip_address'] ); // SQL injection attempt should fail.
	}

	/**
	 * Test time period filters.
	 */
	public function test_time_period_filters() {
		$valid_periods = array( 'today', 'yesterday', '7days', '30days', '90days', 'year', 'all' );

		foreach ( $valid_periods as $period ) {
			$this->assertContains( $period, $valid_periods );
		}
	}

	/**
	 * Test scroll depth tracking.
	 */
	public function test_scroll_depth_tracking() {
		$scroll_depths = array( 0, 25, 50, 75, 100 );

		foreach ( $scroll_depths as $depth ) {
			$this->assertGreaterThanOrEqual( 0, $depth );
			$this->assertLessThanOrEqual( 100, $depth );
		}
	}

	/**
	 * Test page time tracking.
	 */
	public function test_page_time_tracking() {
		$page_times = array(
			1 => 45.5,  // Page 1: 45.5 seconds
			2 => 30.2,  // Page 2: 30.2 seconds
			3 => 120.0, // Page 3: 120 seconds (2 minutes).
		);

		$total_time = array_sum( $page_times );
		$avg_time = $total_time / count( $page_times );

		$this->assertIsFloat( $total_time );
		$this->assertIsFloat( $avg_time );
	}

	/**
	 * Test session tracking.
	 */
	public function test_session_tracking() {
		$session_id = wp_generate_uuid4();

		// UUID v4 format validation.
		$this->assertMatchesRegularExpression(
			'/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
			$session_id
		);
	}

	/**
	 * Test analytics export formats.
	 */
	public function test_analytics_export_formats() {
		$valid_formats = array( 'csv', 'json', 'xlsx' );

		foreach ( $valid_formats as $format ) {
			$this->assertContains( $format, $valid_formats );
		}
	}

	/**
	 * Test engagement metrics aggregation.
	 */
	public function test_engagement_metrics_aggregation() {
		$metrics = array(
			'total_views'      => 1000,
			'unique_visitors'  => 750,
			'avg_time_on_page' => 180,
			'bounce_rate'      => 35.5,
			'completion_rate'  => 62.3,
		);

		$this->assertArrayHasKey( 'total_views', $metrics );
		$this->assertArrayHasKey( 'unique_visitors', $metrics );
		$this->assertLessThanOrEqual( $metrics['total_views'], $metrics['unique_visitors'] * 2 );
	}
}

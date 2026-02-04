<?php
/**
 * Unit tests for Premium Analytics.
 *
 * @package PDF_Embed_SEO_Premium
 * @subpackage Tests
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.DirectDatabaseQuery -- Test file validating database functionality.

/**
 * Test Premium Analytics functionality.
 */
class Test_PDF_Premium_Analytics extends WP_UnitTestCase {

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
			'post_title'  => 'Analytics Test PDF',
			'post_status' => 'publish',
		) );
	}

	/**
	 * Test analytics table exists.
	 */
	public function test_analytics_table_exists() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'pdf_analytics';

		$table_exists = $wpdb->get_var(
			$wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name )
		) === $table_name;

		// Table should exist if premium is installed.
		if ( class_exists( 'PDF_Embed_SEO_Premium_Analytics' ) ) {
			$this->assertTrue( $table_exists );
		} else {
			$this->markTestSkipped( 'Premium analytics not installed.' );
		}
	}

	/**
	 * Test tracking a view.
	 */
	public function test_track_view() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_Analytics' ) ) {
			$this->markTestSkipped( 'Premium analytics not installed.' );
		}

		$analytics = new PDF_Embed_SEO_Premium_Analytics();

		// Track a view.
		$result = $analytics->track_view( $this->pdf_id, array(
			'ip_address' => '127.0.0.1',
			'user_agent' => 'Test Browser',
			'referrer'   => 'https://example.com',
		) );

		$this->assertTrue( $result );
	}

	/**
	 * Test getting view statistics.
	 */
	public function test_get_view_stats() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_Analytics' ) ) {
			$this->markTestSkipped( 'Premium analytics not installed.' );
		}

		$analytics = new PDF_Embed_SEO_Premium_Analytics();

		// Track some views.
		for ( $i = 0; $i < 5; $i++ ) {
			$analytics->track_view( $this->pdf_id, array(
				'ip_address' => '127.0.0.' . $i,
			) );
		}

		$stats = $analytics->get_stats( $this->pdf_id );

		$this->assertArrayHasKey( 'total_views', $stats );
		$this->assertArrayHasKey( 'unique_visitors', $stats );
		$this->assertGreaterThanOrEqual( 5, $stats['total_views'] );
	}

	/**
	 * Test getting popular documents.
	 */
	public function test_get_popular_documents() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_Analytics' ) ) {
			$this->markTestSkipped( 'Premium analytics not installed.' );
		}

		$analytics = new PDF_Embed_SEO_Premium_Analytics();

		// Create more PDFs and track views.
		$pdf_ids = array( $this->pdf_id );
		for ( $i = 0; $i < 3; $i++ ) {
			$pdf_ids[] = $this->factory->post->create( array(
				'post_type'   => 'pdf_document',
				'post_status' => 'publish',
			) );
		}

		// Track views with varying counts.
		foreach ( $pdf_ids as $index => $id ) {
			for ( $j = 0; $j <= $index; $j++ ) {
				$analytics->track_view( $id );
			}
		}

		$popular = $analytics->get_popular_documents( 10, 30 );

		$this->assertIsArray( $popular );
	}

	/**
	 * Test filtering by date range.
	 */
	public function test_filter_by_date_range() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_Analytics' ) ) {
			$this->markTestSkipped( 'Premium analytics not installed.' );
		}

		$analytics = new PDF_Embed_SEO_Premium_Analytics();

		// Get stats for different periods.
		$stats_7days = $analytics->get_overview_stats( '7days' );
		$stats_30days = $analytics->get_overview_stats( '30days' );
		$stats_all = $analytics->get_overview_stats( 'all' );

		$this->assertArrayHasKey( 'total_views', $stats_7days );
		$this->assertArrayHasKey( 'total_views', $stats_30days );
		$this->assertArrayHasKey( 'total_views', $stats_all );
	}

	/**
	 * Test analytics export CSV.
	 */
	public function test_export_csv() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_Analytics' ) ) {
			$this->markTestSkipped( 'Premium analytics not installed.' );
		}

		$analytics = new PDF_Embed_SEO_Premium_Analytics();

		// Track some data.
		$analytics->track_view( $this->pdf_id );

		$csv = $analytics->export_csv( '30days' );

		$this->assertIsString( $csv );
		$this->assertStringContainsString( 'Document', $csv ); // Header.
	}

	/**
	 * Test analytics export JSON.
	 */
	public function test_export_json() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_Analytics' ) ) {
			$this->markTestSkipped( 'Premium analytics not installed.' );
		}

		$analytics = new PDF_Embed_SEO_Premium_Analytics();

		// Track some data.
		$analytics->track_view( $this->pdf_id );

		$json = $analytics->export_json( '30days' );
		$data = json_decode( $json, true );

		$this->assertIsArray( $data );
		$this->assertArrayHasKey( 'period', $data );
		$this->assertArrayHasKey( 'documents', $data );
	}
}

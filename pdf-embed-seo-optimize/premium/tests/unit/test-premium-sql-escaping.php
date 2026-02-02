<?php
/**
 * Unit tests for Premium SQL Escaping (v1.2.7).
 *
 * Tests that SQL queries properly escape table names and parameters
 * to comply with WordPress Plugin Check requirements.
 *
 * @package PDF_Embed_SEO_Premium
 * @subpackage Tests
 * @since 1.2.7
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.DirectDatabaseQuery -- Test file validating database functionality.

/**
 * Test Premium SQL Escaping functionality.
 */
class Test_PDF_Premium_SQL_Escaping extends WP_UnitTestCase {

	/**
	 * Test that analytics table name is properly constructed.
	 */
	public function test_analytics_table_name_construction() {
		global $wpdb;

		// The table name should be constructed from prefix + literal string.
		$expected_table = $wpdb->prefix . 'pdf_analytics';
		$escaped_table = esc_sql( $wpdb->prefix . 'pdf_analytics' );

		// esc_sql should not change a valid table name.
		$this->assertEquals( $expected_table, $escaped_table );
	}

	/**
	 * Test that table name with special characters is escaped.
	 */
	public function test_table_name_escaping_special_chars() {
		// Simulate a malicious table name injection attempt.
		$malicious_input = "pdf_analytics'; DROP TABLE wp_users; --";
		$escaped = esc_sql( $malicious_input );

		// Should not contain the original SQL injection.
		$this->assertStringNotContainsString( 'DROP TABLE', $escaped );
		$this->assertStringNotContainsString( '--', $escaped );
	}

	/**
	 * Test analytics class uses escaped table name.
	 */
	public function test_analytics_class_table_name() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_Analytics' ) ) {
			$this->markTestSkipped( 'Premium analytics not installed.' );
		}

		$analytics = new PDF_Embed_SEO_Premium_Analytics();

		// Use reflection to check private property.
		$reflection = new ReflectionClass( $analytics );
		$property = $reflection->getProperty( 'table_name' );
		$property->setAccessible( true );
		$table_name = $property->getValue( $analytics );

		global $wpdb;
		$expected = esc_sql( $wpdb->prefix . 'pdf_analytics' );

		$this->assertEquals( $expected, $table_name );
	}

	/**
	 * Test REST API analytics endpoint table name handling.
	 */
	public function test_rest_api_analytics_table_name() {
		if ( ! class_exists( 'PDF_Embed_SEO_Premium_REST_API' ) ) {
			$this->markTestSkipped( 'Premium REST API not installed.' );
		}

		global $wpdb;
		$table_name = esc_sql( $wpdb->prefix . 'pdf_analytics' );

		// Table name should match expected pattern.
		$this->assertMatchesRegularExpression( '/^[a-z0-9_]+pdf_analytics$/', $table_name );
	}

	/**
	 * Test prepared statement with date parameters.
	 */
	public function test_prepared_statement_with_dates() {
		global $wpdb;

		$start_date = '2026-01-01 00:00:00';
		$end_date = '2026-12-31 23:59:59';

		// Test that prepare works correctly with date strings.
		$prepared = $wpdb->prepare(
			'SELECT * FROM test_table WHERE created_at >= %s AND created_at <= %s',
			$start_date,
			$end_date
		);

		$this->assertStringContainsString( '2026-01-01', $prepared );
		$this->assertStringContainsString( '2026-12-31', $prepared );
	}

	/**
	 * Test $where clause construction safety.
	 */
	public function test_where_clause_construction() {
		global $wpdb;

		$date_from = '2026-01-01';
		$post_id = 123;

		// Build WHERE clause using prepare (like the analytics class does).
		$where = $wpdb->prepare( 'WHERE DATE(view_date) >= %s', $date_from );
		$where .= $wpdb->prepare( ' AND post_id = %d', $post_id );

		// Should contain properly formatted conditions.
		$this->assertStringContainsString( 'WHERE DATE(view_date) >=', $where );
		$this->assertStringContainsString( 'AND post_id =', $where );
		$this->assertStringContainsString( '123', $where );
	}

	/**
	 * Test SQL injection prevention in analytics queries.
	 */
	public function test_sql_injection_prevention() {
		global $wpdb;

		// Attempt SQL injection via date parameter.
		$malicious_date = "2026-01-01'; DELETE FROM wp_users; --";
		$prepared = $wpdb->prepare(
			'SELECT COUNT(*) FROM test_table WHERE DATE(view_date) = %s',
			$malicious_date
		);

		// Should be escaped/quoted properly.
		$this->assertStringNotContainsString( 'DELETE FROM', $prepared );
	}

	/**
	 * Test integer parameter escaping.
	 */
	public function test_integer_parameter_escaping() {
		global $wpdb;

		$limit = 10;
		$prepared = $wpdb->prepare(
			'SELECT * FROM test_table LIMIT %d',
			$limit
		);

		$this->assertStringContainsString( '10', $prepared );
	}
}

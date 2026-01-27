<?php
/**
 * Premium Analytics
 *
 * Provides detailed analytics and dashboard widget for PDF views.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium analytics class.
 */
class PDF_Embed_SEO_Premium_Analytics {

	/**
	 * Table name for analytics data.
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'pdf_analytics';

		// Create table on activation.
		add_action( 'admin_init', array( $this, 'maybe_create_table' ) );

		// Dashboard widget.
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );

		// Track views.
		add_action( 'pdf_embed_seo_pdf_viewed', array( $this, 'track_view' ) );

		// Admin page.
		add_action( 'admin_menu', array( $this, 'add_analytics_page' ) );

		// AJAX for chart data.
		add_action( 'wp_ajax_pdf_get_analytics_data', array( $this, 'ajax_get_analytics_data' ) );

		// Export functionality.
		add_action( 'admin_init', array( $this, 'handle_export' ) );
	}

	/**
	 * Create analytics table if needed.
	 *
	 * @return void
	 */
	public function maybe_create_table() {
		$version = get_option( 'pdf_analytics_db_version', '0' );

		if ( version_compare( $version, '1.0.0', '<' ) ) {
			$this->create_table();
			update_option( 'pdf_analytics_db_version', '1.0.0' );
		}
	}

	/**
	 * Create the analytics table.
	 *
	 * @return void
	 */
	private function create_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$this->table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			post_id bigint(20) unsigned NOT NULL,
			user_id bigint(20) unsigned DEFAULT 0,
			view_date datetime NOT NULL,
			ip_address varchar(45) DEFAULT '',
			user_agent text,
			referrer text,
			country varchar(2) DEFAULT '',
			page_number int(11) DEFAULT 1,
			time_spent int(11) DEFAULT 0,
			PRIMARY KEY (id),
			KEY post_id (post_id),
			KEY view_date (view_date),
			KEY user_id (user_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Track a PDF view.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function track_view( $post_id ) {
		global $wpdb;

		$user_id    = get_current_user_id();
		$ip_address = $this->get_user_ip();
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		$referrer   = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$this->table_name,
			array(
				'post_id'    => $post_id,
				'user_id'    => $user_id,
				'view_date'  => current_time( 'mysql' ),
				'ip_address' => $ip_address,
				'user_agent' => $user_agent,
				'referrer'   => $referrer,
			),
			array( '%d', '%d', '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Get user IP address.
	 *
	 * @return string
	 */
	private function get_user_ip() {
		$ip_keys = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		foreach ( $ip_keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
				// Handle comma-separated IPs.
				if ( strpos( $ip, ',' ) !== false ) {
					$ip = trim( explode( ',', $ip )[0] );
				}
				if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
					return $ip;
				}
			}
		}

		return '';
	}

	/**
	 * Add dashboard widget.
	 *
	 * @return void
	 */
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'pdf_analytics_widget',
			__( 'PDF Views Overview', 'wp-pdf-embed-seo-optimize' ),
			array( $this, 'render_dashboard_widget' )
		);
	}

	/**
	 * Render dashboard widget.
	 *
	 * @return void
	 */
	public function render_dashboard_widget() {
		$stats = $this->get_overview_stats();
		?>
		<div class="pdf-analytics-widget">
			<div class="pdf-stats-grid">
				<div class="pdf-stat-box">
					<span class="pdf-stat-number"><?php echo esc_html( number_format_i18n( $stats['total_views'] ) ); ?></span>
					<span class="pdf-stat-label"><?php esc_html_e( 'Total Views', 'wp-pdf-embed-seo-optimize' ); ?></span>
				</div>
				<div class="pdf-stat-box">
					<span class="pdf-stat-number"><?php echo esc_html( number_format_i18n( $stats['views_today'] ) ); ?></span>
					<span class="pdf-stat-label"><?php esc_html_e( 'Today', 'wp-pdf-embed-seo-optimize' ); ?></span>
				</div>
				<div class="pdf-stat-box">
					<span class="pdf-stat-number"><?php echo esc_html( number_format_i18n( $stats['views_week'] ) ); ?></span>
					<span class="pdf-stat-label"><?php esc_html_e( 'This Week', 'wp-pdf-embed-seo-optimize' ); ?></span>
				</div>
				<div class="pdf-stat-box">
					<span class="pdf-stat-number"><?php echo esc_html( number_format_i18n( $stats['views_month'] ) ); ?></span>
					<span class="pdf-stat-label"><?php esc_html_e( 'This Month', 'wp-pdf-embed-seo-optimize' ); ?></span>
				</div>
			</div>

			<h4><?php esc_html_e( 'Top PDFs (Last 30 Days)', 'wp-pdf-embed-seo-optimize' ); ?></h4>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Document', 'wp-pdf-embed-seo-optimize' ); ?></th>
						<th><?php esc_html_e( 'Views', 'wp-pdf-embed-seo-optimize' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$top_pdfs = $this->get_top_pdfs( 5, 30 );
					if ( empty( $top_pdfs ) ) :
						?>
						<tr>
							<td colspan="2"><?php esc_html_e( 'No data available yet.', 'wp-pdf-embed-seo-optimize' ); ?></td>
						</tr>
						<?php
					else :
						foreach ( $top_pdfs as $pdf ) :
							?>
							<tr>
								<td>
									<a href="<?php echo esc_url( get_edit_post_link( $pdf->post_id ) ); ?>">
										<?php echo esc_html( get_the_title( $pdf->post_id ) ); ?>
									</a>
								</td>
								<td><?php echo esc_html( number_format_i18n( $pdf->views ) ); ?></td>
							</tr>
							<?php
						endforeach;
					endif;
					?>
				</tbody>
			</table>

			<p class="pdf-widget-footer">
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pdf_document&page=pdf-analytics' ) ); ?>">
					<?php esc_html_e( 'View Full Analytics', 'wp-pdf-embed-seo-optimize' ); ?> &rarr;
				</a>
			</p>
		</div>

		<style>
			.pdf-stats-grid {
				display: grid;
				grid-template-columns: repeat(4, 1fr);
				gap: 10px;
				margin-bottom: 20px;
			}
			.pdf-stat-box {
				text-align: center;
				padding: 10px;
				background: #f0f0f1;
				border-radius: 4px;
			}
			.pdf-stat-number {
				display: block;
				font-size: 24px;
				font-weight: 600;
				color: #2271b1;
			}
			.pdf-stat-label {
				display: block;
				font-size: 11px;
				color: #646970;
				text-transform: uppercase;
			}
			.pdf-widget-footer {
				margin-top: 15px;
				text-align: right;
			}
		</style>
		<?php
	}

	/**
	 * Get overview statistics.
	 *
	 * @return array
	 */
	public function get_overview_stats() {
		global $wpdb;

		$today     = current_time( 'Y-m-d' );
		$week_ago  = gmdate( 'Y-m-d', strtotime( '-7 days' ) );
		$month_ago = gmdate( 'Y-m-d', strtotime( '-30 days' ) );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is safe class property.
		$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name}" );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is safe class property.
		$today_views = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} WHERE DATE(view_date) = %s",
				$today
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is safe class property.
		$week_views = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} WHERE DATE(view_date) >= %s",
				$week_ago
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is safe class property.
		$month_views = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} WHERE DATE(view_date) >= %s",
				$month_ago
			)
		);

		return array(
			'total_views' => (int) $total,
			'views_today' => (int) $today_views,
			'views_week'  => (int) $week_views,
			'views_month' => (int) $month_views,
		);
	}

	/**
	 * Get top PDFs by views.
	 *
	 * @param int $limit Number of PDFs to return.
	 * @param int $days  Number of days to look back.
	 * @return array
	 */
	public function get_top_pdfs( $limit = 10, $days = 30 ) {
		global $wpdb;

		$date_from = gmdate( 'Y-m-d', strtotime( "-{$days} days" ) );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is safe class property.
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_id, COUNT(*) as views
				FROM {$this->table_name}
				WHERE DATE(view_date) >= %s
				GROUP BY post_id
				ORDER BY views DESC
				LIMIT %d",
				$date_from,
				$limit
			)
		);
	}

	/**
	 * Get views over time.
	 *
	 * @param int    $days    Number of days to look back.
	 * @param int    $post_id Optional. Specific post ID.
	 * @return array
	 */
	public function get_views_over_time( $days = 30, $post_id = 0 ) {
		global $wpdb;

		$date_from = gmdate( 'Y-m-d', strtotime( "-{$days} days" ) );

		$where = $wpdb->prepare( 'WHERE DATE(view_date) >= %s', $date_from );
		if ( $post_id ) {
			$where .= $wpdb->prepare( ' AND post_id = %d', $post_id );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name and where clause are safely constructed.
		return $wpdb->get_results(
			"SELECT DATE(view_date) as date, COUNT(*) as views
			FROM {$this->table_name}
			{$where}
			GROUP BY DATE(view_date)
			ORDER BY date ASC"
		);
	}

	/**
	 * Add analytics admin page.
	 *
	 * @return void
	 */
	public function add_analytics_page() {
		add_submenu_page(
			'edit.php?post_type=pdf_document',
			__( 'PDF Analytics', 'wp-pdf-embed-seo-optimize' ),
			__( 'Analytics', 'wp-pdf-embed-seo-optimize' ),
			'manage_options',
			'pdf-analytics',
			array( $this, 'render_analytics_page' )
		);
	}

	/**
	 * Render analytics page.
	 *
	 * @return void
	 */
	public function render_analytics_page() {
		$stats    = $this->get_overview_stats();
		$top_pdfs = $this->get_top_pdfs( 20, 30 );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'PDF Analytics', 'wp-pdf-embed-seo-optimize' ); ?></h1>

			<div class="pdf-analytics-overview">
				<div class="pdf-stats-cards">
					<div class="pdf-stat-card">
						<span class="dashicons dashicons-visibility"></span>
						<div class="pdf-stat-content">
							<span class="pdf-stat-value"><?php echo esc_html( number_format_i18n( $stats['total_views'] ) ); ?></span>
							<span class="pdf-stat-title"><?php esc_html_e( 'Total Views', 'wp-pdf-embed-seo-optimize' ); ?></span>
						</div>
					</div>
					<div class="pdf-stat-card">
						<span class="dashicons dashicons-calendar-alt"></span>
						<div class="pdf-stat-content">
							<span class="pdf-stat-value"><?php echo esc_html( number_format_i18n( $stats['views_today'] ) ); ?></span>
							<span class="pdf-stat-title"><?php esc_html_e( 'Views Today', 'wp-pdf-embed-seo-optimize' ); ?></span>
						</div>
					</div>
					<div class="pdf-stat-card">
						<span class="dashicons dashicons-chart-bar"></span>
						<div class="pdf-stat-content">
							<span class="pdf-stat-value"><?php echo esc_html( number_format_i18n( $stats['views_week'] ) ); ?></span>
							<span class="pdf-stat-title"><?php esc_html_e( 'Views This Week', 'wp-pdf-embed-seo-optimize' ); ?></span>
						</div>
					</div>
					<div class="pdf-stat-card">
						<span class="dashicons dashicons-chart-area"></span>
						<div class="pdf-stat-content">
							<span class="pdf-stat-value"><?php echo esc_html( number_format_i18n( $stats['views_month'] ) ); ?></span>
							<span class="pdf-stat-title"><?php esc_html_e( 'Views This Month', 'wp-pdf-embed-seo-optimize' ); ?></span>
						</div>
					</div>
				</div>
			</div>

			<div class="pdf-analytics-section">
				<h2><?php esc_html_e( 'Top Performing PDFs (Last 30 Days)', 'wp-pdf-embed-seo-optimize' ); ?></h2>

				<form method="get" action="">
					<input type="hidden" name="post_type" value="pdf_document" />
					<input type="hidden" name="page" value="pdf-analytics" />
					<?php wp_nonce_field( 'pdf_export_analytics', 'pdf_export_nonce' ); ?>
					<button type="submit" name="export" value="csv" class="button">
						<?php esc_html_e( 'Export to CSV', 'wp-pdf-embed-seo-optimize' ); ?>
					</button>
				</form>

				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Document', 'wp-pdf-embed-seo-optimize' ); ?></th>
							<th><?php esc_html_e( 'Views', 'wp-pdf-embed-seo-optimize' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'wp-pdf-embed-seo-optimize' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( empty( $top_pdfs ) ) :
							?>
							<tr>
								<td colspan="3"><?php esc_html_e( 'No analytics data available yet.', 'wp-pdf-embed-seo-optimize' ); ?></td>
							</tr>
							<?php
						else :
							foreach ( $top_pdfs as $pdf ) :
								?>
								<tr>
									<td>
										<strong><?php echo esc_html( get_the_title( $pdf->post_id ) ); ?></strong>
									</td>
									<td><?php echo esc_html( number_format_i18n( $pdf->views ) ); ?></td>
									<td>
										<a href="<?php echo esc_url( get_permalink( $pdf->post_id ) ); ?>" target="_blank"><?php esc_html_e( 'View', 'wp-pdf-embed-seo-optimize' ); ?></a> |
										<a href="<?php echo esc_url( get_edit_post_link( $pdf->post_id ) ); ?>"><?php esc_html_e( 'Edit', 'wp-pdf-embed-seo-optimize' ); ?></a>
									</td>
								</tr>
								<?php
							endforeach;
						endif;
						?>
					</tbody>
				</table>
			</div>

			<p class="pdf-embed-seo-optimize-credit" style="text-align: center; margin-top: 30px; color: #666; font-size: 13px;">
				<?php
				printf(
					/* translators: %1$s: heart symbol, %2$s: Dross:Media link */
					esc_html__( 'made with %1$s by %2$s', 'wp-pdf-embed-seo-optimize' ),
					'<span style="color: #e25555;" aria-hidden="true">♥</span><span class="screen-reader-text">' . esc_html__( 'love', 'wp-pdf-embed-seo-optimize' ) . '</span>',
					'<a href="https://dross.net/media/" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr__( 'Visit Dross:Media website (opens in new tab)', 'wp-pdf-embed-seo-optimize' ) . '" title="' . esc_attr__( 'Visit Dross:Media website', 'wp-pdf-embed-seo-optimize' ) . '">Dross:Media</a>'
				);
				?>
			</p>
		</div>

		<style>
			.pdf-stats-cards {
				display: grid;
				grid-template-columns: repeat(4, 1fr);
				gap: 20px;
				margin: 20px 0;
			}
			.pdf-stat-card {
				background: #fff;
				border: 1px solid #c3c4c7;
				border-radius: 4px;
				padding: 20px;
				display: flex;
				align-items: center;
				gap: 15px;
			}
			.pdf-stat-card .dashicons {
				font-size: 40px;
				width: 40px;
				height: 40px;
				color: #2271b1;
			}
			.pdf-stat-value {
				display: block;
				font-size: 28px;
				font-weight: 600;
				line-height: 1.2;
			}
			.pdf-stat-title {
				display: block;
				color: #646970;
			}
			.pdf-analytics-section {
				background: #fff;
				border: 1px solid #c3c4c7;
				padding: 20px;
				margin-top: 20px;
			}
			.pdf-analytics-section h2 {
				margin-top: 0;
			}
			.pdf-analytics-section form {
				margin-bottom: 15px;
			}
		</style>
		<?php
	}

	/**
	 * Handle CSV export.
	 *
	 * @return void
	 */
	public function handle_export() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['page'] ) || 'pdf-analytics' !== $_GET['page'] ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['export'] ) || 'csv' !== $_GET['export'] ) {
			return;
		}

		if ( ! isset( $_GET['pdf_export_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['pdf_export_nonce'] ), 'pdf_export_analytics' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$top_pdfs = $this->get_top_pdfs( 1000, 30 );

		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment; filename="pdf-analytics-' . gmdate( 'Y-m-d' ) . '.csv"' );

		$output = fopen( 'php://output', 'w' );

		fputcsv( $output, array( 'Document', 'Post ID', 'Views (Last 30 Days)' ) );

		foreach ( $top_pdfs as $pdf ) {
			fputcsv(
				$output,
				array(
					get_the_title( $pdf->post_id ),
					$pdf->post_id,
					$pdf->views,
				)
			);
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Using php://output for CSV export, not file system.
		fclose( $output );
		exit;
	}

	/**
	 * AJAX handler for analytics data.
	 *
	 * @return void
	 */
	public function ajax_get_analytics_data() {
		check_ajax_referer( 'pdf_analytics_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'wp-pdf-embed-seo-optimize' ) ) );
		}

		$days    = isset( $_POST['days'] ) ? absint( $_POST['days'] ) : 30;
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		$data = $this->get_views_over_time( $days, $post_id );

		wp_send_json_success( array( 'data' => $data ) );
	}
}

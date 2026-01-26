<?php
/**
 * Uninstall script for PDF Embed & SEO Optimize.
 *
 * This file is executed when the plugin is deleted from WordPress.
 * It removes all plugin data from the database.
 *
 * @package PDF_Viewer_2026
 */

// Exit if not called by WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Clean up plugin data on uninstall.
 */
function pdf_viewer_2026_uninstall() {
	global $wpdb;

	// Delete plugin options.
	delete_option( 'pdf_viewer_2026_settings' );
	delete_option( 'pdf_viewer_2026_version' );

	// Delete all PDF document posts and their meta.
	$pdf_posts = get_posts(
		array(
			'post_type'      => 'pdf_document',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	foreach ( $pdf_posts as $post_id ) {
		// Delete post meta.
		delete_post_meta( $post_id, '_pdf_file_id' );
		delete_post_meta( $post_id, '_pdf_file_url' );
		delete_post_meta( $post_id, '_pdf_allow_download' );
		delete_post_meta( $post_id, '_pdf_allow_print' );
		delete_post_meta( $post_id, '_pdf_view_count' );

		// Delete the post.
		wp_delete_post( $post_id, true );
	}

	// Clean up any orphaned post meta.
	$wpdb->query(
		"DELETE FROM {$wpdb->postmeta} WHERE meta_key IN (
			'_pdf_file_id',
			'_pdf_file_url',
			'_pdf_allow_download',
			'_pdf_allow_print',
			'_pdf_view_count'
		)"
	);

	// Flush rewrite rules.
	flush_rewrite_rules();
}

// Run uninstall.
pdf_viewer_2026_uninstall();

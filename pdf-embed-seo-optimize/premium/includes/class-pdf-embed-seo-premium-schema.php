<?php
/**
 * Premium Schema Enhancements for GEO/AEO/LLM Optimization
 *
 * Adds advanced schema markup for AI assistants, voice search,
 * and generative engine optimization.
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.2.4
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Premium Schema class.
 */
class PDF_Embed_SEO_Premium_Schema {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add premium meta box for GEO/AEO settings.
		add_action( 'add_meta_boxes', array( $this, 'add_schema_meta_box' ) );

		// Save premium schema meta.
		add_action( 'save_post_pdf_document', array( $this, 'save_schema_meta' ), 10, 2 );

		// Enhance schema output.
		add_filter( 'pdf_embed_seo_schema_data', array( $this, 'enhance_schema' ), 10, 2 );

		// Add FAQ schema output.
		add_action( 'wp_head', array( $this, 'output_faq_schema' ), 15 );

		// Add premium speakable to WebPage schema.
		add_filter( 'pdf_embed_seo_webpage_schema', array( $this, 'enhance_webpage_schema' ), 10, 2 );
	}

	/**
	 * Add schema meta box.
	 *
	 * @return void
	 */
	public function add_schema_meta_box() {
		add_meta_box(
			'pdf_premium_schema',
			__( 'AI & Schema Optimization (Premium)', 'pdf-embed-seo-optimize' ),
			array( $this, 'render_schema_meta_box' ),
			'pdf_document',
			'normal',
			'default'
		);
	}

	/**
	 * Render the schema meta box.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_schema_meta_box( $post ) {
		wp_nonce_field( 'pdf_premium_schema_nonce', 'pdf_premium_schema_nonce' );

		// Get saved values.
		$ai_summary        = get_post_meta( $post->ID, '_pdf_ai_summary', true );
		$key_points        = get_post_meta( $post->ID, '_pdf_key_points', true );
		$reading_time      = get_post_meta( $post->ID, '_pdf_reading_time', true );
		$difficulty_level  = get_post_meta( $post->ID, '_pdf_difficulty_level', true );
		$target_audience   = get_post_meta( $post->ID, '_pdf_target_audience', true );
		$document_type     = get_post_meta( $post->ID, '_pdf_document_type', true );
		$faq_items         = get_post_meta( $post->ID, '_pdf_faq_items', true );
		$toc_items         = get_post_meta( $post->ID, '_pdf_toc_items', true );
		$custom_speakable  = get_post_meta( $post->ID, '_pdf_custom_speakable', true );
		$related_docs      = get_post_meta( $post->ID, '_pdf_related_documents', true );
		$prerequisites     = get_post_meta( $post->ID, '_pdf_prerequisites', true );
		$learning_outcomes = get_post_meta( $post->ID, '_pdf_learning_outcomes', true );

		// Default values.
		if ( empty( $faq_items ) || ! is_array( $faq_items ) ) {
			$faq_items = array();
		}
		if ( empty( $toc_items ) || ! is_array( $toc_items ) ) {
			$toc_items = array();
		}
		?>
		<style>
			.pdf-schema-section { margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #ddd; }
			.pdf-schema-section:last-child { border-bottom: none; }
			.pdf-schema-section h4 { margin: 0 0 10px; color: #1d2327; }
			.pdf-schema-row { margin-bottom: 15px; }
			.pdf-schema-row label { display: block; font-weight: 600; margin-bottom: 5px; }
			.pdf-schema-row .description { color: #646970; font-size: 13px; margin-top: 5px; }
			.pdf-faq-item, .pdf-toc-item { background: #f9f9f9; padding: 10px; margin-bottom: 10px; border-radius: 4px; }
			.pdf-faq-item input, .pdf-toc-item input { width: 100%; margin-bottom: 5px; }
			.pdf-faq-item textarea { width: 100%; height: 60px; }
			.pdf-remove-item { color: #b32d2e; cursor: pointer; font-size: 12px; }
			.pdf-add-item { margin-top: 10px; }
		</style>

		<div class="pdf-schema-sections">
			<!-- AI Summary Section -->
			<div class="pdf-schema-section">
				<h4><?php esc_html_e( 'AI Summary & Key Points', 'pdf-embed-seo-optimize' ); ?></h4>

				<div class="pdf-schema-row">
					<label for="pdf_ai_summary"><?php esc_html_e( 'AI Summary (TL;DR)', 'pdf-embed-seo-optimize' ); ?></label>
					<textarea id="pdf_ai_summary" name="pdf_ai_summary" rows="3" style="width:100%;"><?php echo esc_textarea( $ai_summary ); ?></textarea>
					<p class="description"><?php esc_html_e( 'A concise 1-2 sentence summary for AI assistants and voice search. Used in schema abstract property.', 'pdf-embed-seo-optimize' ); ?></p>
				</div>

				<div class="pdf-schema-row">
					<label for="pdf_key_points"><?php esc_html_e( 'Key Points / Takeaways', 'pdf-embed-seo-optimize' ); ?></label>
					<textarea id="pdf_key_points" name="pdf_key_points" rows="4" style="width:100%;"><?php echo esc_textarea( $key_points ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Enter key points, one per line. These will be included in schema for AI consumption.', 'pdf-embed-seo-optimize' ); ?></p>
				</div>
			</div>

			<!-- Document Metadata Section -->
			<div class="pdf-schema-section">
				<h4><?php esc_html_e( 'Document Metadata', 'pdf-embed-seo-optimize' ); ?></h4>

				<div class="pdf-schema-row" style="display: flex; gap: 20px;">
					<div style="flex: 1;">
						<label for="pdf_reading_time"><?php esc_html_e( 'Reading Time (minutes)', 'pdf-embed-seo-optimize' ); ?></label>
						<input type="number" id="pdf_reading_time" name="pdf_reading_time" value="<?php echo esc_attr( $reading_time ); ?>" min="1" max="999" style="width: 100px;">
					</div>

					<div style="flex: 1;">
						<label for="pdf_difficulty_level"><?php esc_html_e( 'Difficulty Level', 'pdf-embed-seo-optimize' ); ?></label>
						<select id="pdf_difficulty_level" name="pdf_difficulty_level">
							<option value=""><?php esc_html_e( '-- Select --', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="beginner" <?php selected( $difficulty_level, 'beginner' ); ?>><?php esc_html_e( 'Beginner', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="intermediate" <?php selected( $difficulty_level, 'intermediate' ); ?>><?php esc_html_e( 'Intermediate', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="advanced" <?php selected( $difficulty_level, 'advanced' ); ?>><?php esc_html_e( 'Advanced', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="expert" <?php selected( $difficulty_level, 'expert' ); ?>><?php esc_html_e( 'Expert', 'pdf-embed-seo-optimize' ); ?></option>
						</select>
					</div>

					<div style="flex: 1;">
						<label for="pdf_document_type"><?php esc_html_e( 'Document Type', 'pdf-embed-seo-optimize' ); ?></label>
						<select id="pdf_document_type" name="pdf_document_type">
							<option value=""><?php esc_html_e( '-- Select --', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="guide" <?php selected( $document_type, 'guide' ); ?>><?php esc_html_e( 'Guide / Tutorial', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="whitepaper" <?php selected( $document_type, 'whitepaper' ); ?>><?php esc_html_e( 'Whitepaper', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="report" <?php selected( $document_type, 'report' ); ?>><?php esc_html_e( 'Report', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="ebook" <?php selected( $document_type, 'ebook' ); ?>><?php esc_html_e( 'E-Book', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="manual" <?php selected( $document_type, 'manual' ); ?>><?php esc_html_e( 'Manual', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="brochure" <?php selected( $document_type, 'brochure' ); ?>><?php esc_html_e( 'Brochure', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="case-study" <?php selected( $document_type, 'case-study' ); ?>><?php esc_html_e( 'Case Study', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="datasheet" <?php selected( $document_type, 'datasheet' ); ?>><?php esc_html_e( 'Datasheet', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="presentation" <?php selected( $document_type, 'presentation' ); ?>><?php esc_html_e( 'Presentation', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="research" <?php selected( $document_type, 'research' ); ?>><?php esc_html_e( 'Research Paper', 'pdf-embed-seo-optimize' ); ?></option>
							<option value="form" <?php selected( $document_type, 'form' ); ?>><?php esc_html_e( 'Form / Template', 'pdf-embed-seo-optimize' ); ?></option>
						</select>
					</div>
				</div>

				<div class="pdf-schema-row">
					<label for="pdf_target_audience"><?php esc_html_e( 'Target Audience', 'pdf-embed-seo-optimize' ); ?></label>
					<input type="text" id="pdf_target_audience" name="pdf_target_audience" value="<?php echo esc_attr( $target_audience ); ?>" style="width: 100%;">
					<p class="description"><?php esc_html_e( 'Who is this document for? E.g., "Marketing professionals", "Small business owners", "IT administrators"', 'pdf-embed-seo-optimize' ); ?></p>
				</div>
			</div>

			<!-- FAQ Schema Section -->
			<div class="pdf-schema-section">
				<h4><?php esc_html_e( 'FAQ Schema (Question & Answers)', 'pdf-embed-seo-optimize' ); ?></h4>
				<p class="description"><?php esc_html_e( 'Add frequently asked questions related to this PDF. These will appear in Google FAQ rich results.', 'pdf-embed-seo-optimize' ); ?></p>

				<div id="pdf-faq-items">
					<?php
					if ( ! empty( $faq_items ) ) :
						foreach ( $faq_items as $index => $faq ) :
							?>
							<div class="pdf-faq-item">
								<input type="text" name="pdf_faq_items[<?php echo esc_attr( $index ); ?>][question]" placeholder="<?php esc_attr_e( 'Question', 'pdf-embed-seo-optimize' ); ?>" value="<?php echo esc_attr( $faq['question'] ?? '' ); ?>">
								<textarea name="pdf_faq_items[<?php echo esc_attr( $index ); ?>][answer]" placeholder="<?php esc_attr_e( 'Answer', 'pdf-embed-seo-optimize' ); ?>"><?php echo esc_textarea( $faq['answer'] ?? '' ); ?></textarea>
								<span class="pdf-remove-item" onclick="this.parentElement.remove();"><?php esc_html_e( 'Remove', 'pdf-embed-seo-optimize' ); ?></span>
							</div>
							<?php
						endforeach;
					endif;
					?>
				</div>
				<button type="button" class="button pdf-add-item" onclick="addFaqItem();"><?php esc_html_e( '+ Add FAQ Item', 'pdf-embed-seo-optimize' ); ?></button>
			</div>

			<!-- Table of Contents Section -->
			<div class="pdf-schema-section">
				<h4><?php esc_html_e( 'Table of Contents Schema', 'pdf-embed-seo-optimize' ); ?></h4>
				<p class="description"><?php esc_html_e( 'Add document sections/chapters. These create structured navigation for AI crawlers.', 'pdf-embed-seo-optimize' ); ?></p>

				<div id="pdf-toc-items">
					<?php
					if ( ! empty( $toc_items ) ) :
						foreach ( $toc_items as $index => $toc ) :
							?>
							<div class="pdf-toc-item">
								<input type="text" name="pdf_toc_items[<?php echo esc_attr( $index ); ?>][title]" placeholder="<?php esc_attr_e( 'Section Title', 'pdf-embed-seo-optimize' ); ?>" value="<?php echo esc_attr( $toc['title'] ?? '' ); ?>" style="width: 70%;">
								<input type="number" name="pdf_toc_items[<?php echo esc_attr( $index ); ?>][page]" placeholder="<?php esc_attr_e( 'Page #', 'pdf-embed-seo-optimize' ); ?>" value="<?php echo esc_attr( $toc['page'] ?? '' ); ?>" style="width: 25%;" min="1">
								<span class="pdf-remove-item" onclick="this.parentElement.remove();"><?php esc_html_e( 'Remove', 'pdf-embed-seo-optimize' ); ?></span>
							</div>
							<?php
						endforeach;
					endif;
					?>
				</div>
				<button type="button" class="button pdf-add-item" onclick="addTocItem();"><?php esc_html_e( '+ Add Section', 'pdf-embed-seo-optimize' ); ?></button>
			</div>

			<!-- Learning/Educational Section -->
			<div class="pdf-schema-section">
				<h4><?php esc_html_e( 'Educational Content (Optional)', 'pdf-embed-seo-optimize' ); ?></h4>

				<div class="pdf-schema-row">
					<label for="pdf_prerequisites"><?php esc_html_e( 'Prerequisites', 'pdf-embed-seo-optimize' ); ?></label>
					<textarea id="pdf_prerequisites" name="pdf_prerequisites" rows="2" style="width:100%;"><?php echo esc_textarea( $prerequisites ); ?></textarea>
					<p class="description"><?php esc_html_e( 'What should readers know before reading this? One item per line.', 'pdf-embed-seo-optimize' ); ?></p>
				</div>

				<div class="pdf-schema-row">
					<label for="pdf_learning_outcomes"><?php esc_html_e( 'Learning Outcomes', 'pdf-embed-seo-optimize' ); ?></label>
					<textarea id="pdf_learning_outcomes" name="pdf_learning_outcomes" rows="3" style="width:100%;"><?php echo esc_textarea( $learning_outcomes ); ?></textarea>
					<p class="description"><?php esc_html_e( 'What will readers learn? One outcome per line. E.g., "Understand the basics of...", "Be able to..."', 'pdf-embed-seo-optimize' ); ?></p>
				</div>
			</div>

			<!-- Custom Speakable Section -->
			<div class="pdf-schema-section">
				<h4><?php esc_html_e( 'Custom Speakable Content', 'pdf-embed-seo-optimize' ); ?></h4>

				<div class="pdf-schema-row">
					<label for="pdf_custom_speakable"><?php esc_html_e( 'Priority Content for Voice Search', 'pdf-embed-seo-optimize' ); ?></label>
					<textarea id="pdf_custom_speakable" name="pdf_custom_speakable" rows="3" style="width:100%;"><?php echo esc_textarea( $custom_speakable ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Enter content that voice assistants should read aloud. This takes priority over auto-detected content.', 'pdf-embed-seo-optimize' ); ?></p>
				</div>
			</div>

			<!-- Related Documents Section -->
			<div class="pdf-schema-section">
				<h4><?php esc_html_e( 'Related Documents', 'pdf-embed-seo-optimize' ); ?></h4>

				<div class="pdf-schema-row">
					<label for="pdf_related_documents"><?php esc_html_e( 'Related PDF Documents', 'pdf-embed-seo-optimize' ); ?></label>
					<?php
					// Get all PDF documents except the current one for related documents selection.
					// Limited to 100 posts and admin-only context to prevent performance issues.
					// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- Admin meta box with capped query (100 posts), single exclusion for UX.
					$all_pdfs = get_posts(
						array(
							'post_type'      => 'pdf_document',
							'posts_per_page' => 100,
							'post_status'    => 'publish',
							'post__not_in'   => array( $post->ID ),
							'orderby'        => 'title',
							'order'          => 'ASC',
						)
					);
					$selected_docs = is_array( $related_docs ) ? $related_docs : array();
					?>
					<select id="pdf_related_documents" name="pdf_related_documents[]" multiple style="width: 100%; height: 120px;">
						<?php foreach ( $all_pdfs as $pdf ) : ?>
							<option value="<?php echo esc_attr( $pdf->ID ); ?>" <?php echo in_array( $pdf->ID, $selected_docs, true ) ? 'selected' : ''; ?>>
								<?php echo esc_html( $pdf->post_title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Hold Ctrl/Cmd to select multiple. Creates isRelatedTo schema for AI understanding.', 'pdf-embed-seo-optimize' ); ?></p>
				</div>
			</div>
		</div>

		<script>
		var faqIndex = <?php echo count( $faq_items ); ?>;
		var tocIndex = <?php echo count( $toc_items ); ?>;

		function addFaqItem() {
			var container = document.getElementById('pdf-faq-items');
			var html = '<div class="pdf-faq-item">' +
				'<input type="text" name="pdf_faq_items[' + faqIndex + '][question]" placeholder="<?php echo esc_js( __( 'Question', 'pdf-embed-seo-optimize' ) ); ?>">' +
				'<textarea name="pdf_faq_items[' + faqIndex + '][answer]" placeholder="<?php echo esc_js( __( 'Answer', 'pdf-embed-seo-optimize' ) ); ?>"></textarea>' +
				'<span class="pdf-remove-item" onclick="this.parentElement.remove();"><?php echo esc_js( __( 'Remove', 'pdf-embed-seo-optimize' ) ); ?></span>' +
				'</div>';
			container.insertAdjacentHTML('beforeend', html);
			faqIndex++;
		}

		function addTocItem() {
			var container = document.getElementById('pdf-toc-items');
			var html = '<div class="pdf-toc-item">' +
				'<input type="text" name="pdf_toc_items[' + tocIndex + '][title]" placeholder="<?php echo esc_js( __( 'Section Title', 'pdf-embed-seo-optimize' ) ); ?>" style="width: 70%;">' +
				'<input type="number" name="pdf_toc_items[' + tocIndex + '][page]" placeholder="<?php echo esc_js( __( 'Page #', 'pdf-embed-seo-optimize' ) ); ?>" style="width: 25%;" min="1">' +
				'<span class="pdf-remove-item" onclick="this.parentElement.remove();"><?php echo esc_js( __( 'Remove', 'pdf-embed-seo-optimize' ) ); ?></span>' +
				'</div>';
			container.insertAdjacentHTML('beforeend', html);
			tocIndex++;
		}
		</script>
		<?php
	}

	/**
	 * Save schema meta data.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 * @return void
	 */
	public function save_schema_meta( $post_id, $post ) {
		// Verify nonce.
		if ( ! isset( $_POST['pdf_premium_schema_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pdf_premium_schema_nonce'] ) ), 'pdf_premium_schema_nonce' ) ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save text fields.
		$text_fields = array(
			'pdf_ai_summary',
			'pdf_key_points',
			'pdf_target_audience',
			'pdf_custom_speakable',
			'pdf_prerequisites',
			'pdf_learning_outcomes',
		);

		foreach ( $text_fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via sanitize_textarea_field.
				update_post_meta( $post_id, '_' . $field, sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}

		// Save select fields.
		if ( isset( $_POST['pdf_difficulty_level'] ) ) {
			update_post_meta( $post_id, '_pdf_difficulty_level', sanitize_text_field( wp_unslash( $_POST['pdf_difficulty_level'] ) ) );
		}
		if ( isset( $_POST['pdf_document_type'] ) ) {
			update_post_meta( $post_id, '_pdf_document_type', sanitize_text_field( wp_unslash( $_POST['pdf_document_type'] ) ) );
		}

		// Save numeric fields.
		if ( isset( $_POST['pdf_reading_time'] ) ) {
			update_post_meta( $post_id, '_pdf_reading_time', absint( $_POST['pdf_reading_time'] ) );
		}

		// Save FAQ items.
		if ( isset( $_POST['pdf_faq_items'] ) && is_array( $_POST['pdf_faq_items'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Array elements are sanitized individually below.
			$raw_faq_items = wp_unslash( $_POST['pdf_faq_items'] );
			$faq_items     = array();
			foreach ( $raw_faq_items as $faq ) {
				if ( ! empty( $faq['question'] ) && ! empty( $faq['answer'] ) ) {
					$faq_items[] = array(
						'question' => sanitize_text_field( $faq['question'] ),
						'answer'   => sanitize_textarea_field( $faq['answer'] ),
					);
				}
			}
			update_post_meta( $post_id, '_pdf_faq_items', $faq_items );
		} else {
			delete_post_meta( $post_id, '_pdf_faq_items' );
		}

		// Save TOC items.
		if ( isset( $_POST['pdf_toc_items'] ) && is_array( $_POST['pdf_toc_items'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Array elements are sanitized individually below.
			$raw_toc_items = wp_unslash( $_POST['pdf_toc_items'] );
			$toc_items     = array();
			foreach ( $raw_toc_items as $toc ) {
				if ( ! empty( $toc['title'] ) ) {
					$toc_items[] = array(
						'title' => sanitize_text_field( $toc['title'] ),
						'page'  => absint( $toc['page'] ?? 1 ),
					);
				}
			}
			update_post_meta( $post_id, '_pdf_toc_items', $toc_items );
		} else {
			delete_post_meta( $post_id, '_pdf_toc_items' );
		}

		// Save related documents.
		if ( isset( $_POST['pdf_related_documents'] ) && is_array( $_POST['pdf_related_documents'] ) ) {
			$related = array_map( 'absint', $_POST['pdf_related_documents'] );
			update_post_meta( $post_id, '_pdf_related_documents', $related );
		} else {
			delete_post_meta( $post_id, '_pdf_related_documents' );
		}
	}

	/**
	 * Enhance the DigitalDocument schema with premium data.
	 *
	 * @param array $schema  The existing schema.
	 * @param int   $post_id The post ID.
	 * @return array Enhanced schema.
	 */
	public function enhance_schema( $schema, $post_id ) {
		// AI Summary.
		$ai_summary = get_post_meta( $post_id, '_pdf_ai_summary', true );
		if ( ! empty( $ai_summary ) ) {
			$schema['abstract'] = wp_strip_all_tags( $ai_summary );
		}

		// Key Points as mentions.
		$key_points = get_post_meta( $post_id, '_pdf_key_points', true );
		if ( ! empty( $key_points ) ) {
			$points = array_filter( array_map( 'trim', explode( "\n", $key_points ) ) );
			if ( ! empty( $points ) ) {
				$schema['mainEntity'] = array(
					'@type'           => 'ItemList',
					'name'            => __( 'Key Points', 'pdf-embed-seo-optimize' ),
					'itemListElement' => array_map(
						function ( $point, $index ) {
							return array(
								'@type'    => 'ListItem',
								'position' => $index + 1,
								'name'     => $point,
							);
						},
						$points,
						array_keys( $points )
					),
				);
			}
		}

		// Reading time.
		$reading_time = get_post_meta( $post_id, '_pdf_reading_time', true );
		if ( ! empty( $reading_time ) ) {
			$schema['timeRequired'] = 'PT' . absint( $reading_time ) . 'M';
		}

		// Difficulty level as proficiency level.
		$difficulty = get_post_meta( $post_id, '_pdf_difficulty_level', true );
		if ( ! empty( $difficulty ) ) {
			$level_map = array(
				'beginner'     => 'Beginner',
				'intermediate' => 'Intermediate',
				'advanced'     => 'Advanced',
				'expert'       => 'Expert',
			);
			$schema['proficiencyLevel']         = $level_map[ $difficulty ] ?? $difficulty;
			$schema['educationalLevel']         = $level_map[ $difficulty ] ?? $difficulty;
			$schema['typicalAgeRange']          = $this->get_age_range_for_level( $difficulty );
			$schema['interactivityType']        = 'expositive';
			$schema['educationalUse']           = array( 'self study', 'reference' );
		}

		// Document type as additionalType.
		$doc_type = get_post_meta( $post_id, '_pdf_document_type', true );
		if ( ! empty( $doc_type ) ) {
			$type_map = array(
				'guide'        => 'https://schema.org/Guide',
				'whitepaper'   => 'https://schema.org/ScholarlyArticle',
				'report'       => 'https://schema.org/Report',
				'ebook'        => 'https://schema.org/Book',
				'manual'       => 'https://schema.org/TechArticle',
				'brochure'     => 'https://schema.org/AdvertiserContentArticle',
				'case-study'   => 'https://schema.org/ScholarlyArticle',
				'datasheet'    => 'https://schema.org/TechArticle',
				'presentation' => 'https://schema.org/PresentationDigitalDocument',
				'research'     => 'https://schema.org/ScholarlyArticle',
				'form'         => 'https://schema.org/DigitalDocument',
			);
			if ( isset( $type_map[ $doc_type ] ) ) {
				$schema['additionalType'] = $type_map[ $doc_type ];
			}
			$schema['learningResourceType'] = ucfirst( str_replace( '-', ' ', $doc_type ) );
		}

		// Target audience.
		$audience = get_post_meta( $post_id, '_pdf_target_audience', true );
		if ( ! empty( $audience ) ) {
			$schema['audience'] = array(
				'@type'        => 'Audience',
				'audienceType' => $audience,
			);
		}

		// Table of Contents as hasPart.
		$toc_items = get_post_meta( $post_id, '_pdf_toc_items', true );
		if ( ! empty( $toc_items ) && is_array( $toc_items ) ) {
			$parts = array();
			foreach ( $toc_items as $index => $toc ) {
				$parts[] = array(
					'@type'    => 'WebPageElement',
					'name'     => $toc['title'],
					'position' => $index + 1,
					'url'      => get_permalink( $post_id ) . '#page=' . $toc['page'],
				);
			}
			$schema['hasPart'] = $parts;
		}

		// Prerequisites as coursePrerequisites.
		$prereqs = get_post_meta( $post_id, '_pdf_prerequisites', true );
		if ( ! empty( $prereqs ) ) {
			$prereq_list = array_filter( array_map( 'trim', explode( "\n", $prereqs ) ) );
			if ( ! empty( $prereq_list ) ) {
				$schema['coursePrerequisites'] = $prereq_list;
			}
		}

		// Learning outcomes as teaches.
		$outcomes = get_post_meta( $post_id, '_pdf_learning_outcomes', true );
		if ( ! empty( $outcomes ) ) {
			$outcome_list = array_filter( array_map( 'trim', explode( "\n", $outcomes ) ) );
			if ( ! empty( $outcome_list ) ) {
				$schema['teaches'] = $outcome_list;
			}
		}

		// Related documents.
		$related = get_post_meta( $post_id, '_pdf_related_documents', true );
		if ( ! empty( $related ) && is_array( $related ) ) {
			$related_items = array();
			foreach ( $related as $rel_id ) {
				$rel_post = get_post( $rel_id );
				if ( $rel_post && 'publish' === $rel_post->post_status ) {
					$related_items[] = array(
						'@type' => 'DigitalDocument',
						'name'  => get_the_title( $rel_id ),
						'url'   => get_permalink( $rel_id ),
					);
				}
			}
			if ( ! empty( $related_items ) ) {
				$schema['isRelatedTo'] = $related_items;
			}
		}

		return $schema;
	}

	/**
	 * Output FAQ schema for PDF documents.
	 *
	 * @return void
	 */
	public function output_faq_schema() {
		if ( ! is_singular( 'pdf_document' ) ) {
			return;
		}

		$post_id   = get_the_ID();
		$faq_items = get_post_meta( $post_id, '_pdf_faq_items', true );

		if ( empty( $faq_items ) || ! is_array( $faq_items ) ) {
			return;
		}

		$faq_schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'@id'        => get_permalink( $post_id ) . '#faq',
			'mainEntity' => array(),
		);

		foreach ( $faq_items as $faq ) {
			if ( ! empty( $faq['question'] ) && ! empty( $faq['answer'] ) ) {
				$faq_schema['mainEntity'][] = array(
					'@type'          => 'Question',
					'name'           => $faq['question'],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $faq['answer'],
					),
				);
			}
		}

		if ( ! empty( $faq_schema['mainEntity'] ) ) {
			echo "\n<!-- PDF Embed & SEO Optimize Premium - FAQ Schema -->\n";
			echo '<script type="application/ld+json">' . "\n";
			echo wp_json_encode( $faq_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
			echo "\n</script>\n";
		}
	}

	/**
	 * Enhance WebPage schema with custom speakable.
	 *
	 * @param array $schema  The WebPage schema.
	 * @param int   $post_id The post ID.
	 * @return array Enhanced schema.
	 */
	public function enhance_webpage_schema( $schema, $post_id ) {
		$custom_speakable = get_post_meta( $post_id, '_pdf_custom_speakable', true );

		if ( ! empty( $custom_speakable ) ) {
			// Add custom speakable text alongside CSS selectors.
			$schema['speakable'] = array(
				'@type'       => 'SpeakableSpecification',
				'cssSelector' => array(
					'.pdf-embed-seo-optimize-title',
					'.pdf-embed-seo-optimize-excerpt',
					'.entry-title',
				),
				'xpath'       => array(
					"/html/head/meta[@name='description']/@content",
				),
			);
		}

		return $schema;
	}

	/**
	 * Get age range for difficulty level.
	 *
	 * @param string $level The difficulty level.
	 * @return string Age range.
	 */
	private function get_age_range_for_level( $level ) {
		$ranges = array(
			'beginner'     => '12-',
			'intermediate' => '16-',
			'advanced'     => '18-',
			'expert'       => '21-',
		);
		return $ranges[ $level ] ?? '18-';
	}
}

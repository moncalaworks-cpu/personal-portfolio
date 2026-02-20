<?php
/**
 * Post Editor Showcase
 *
 * Demonstrates WordPress page/post editing workflow:
 * - Edit screen loading and data retrieval
 * - Custom metaboxes (add_meta_box)
 * - Post saving with save_post hook
 * - Post metadata storage and retrieval
 * - Security (nonces, capabilities)
 * - Post revisions system
 *
 * @wordpress-plugin
 * Plugin Name: Post Editor Showcase
 * Version: 1.0.0
 * Description: Learn WordPress post editing workflow with custom metaboxes and metadata
 * Author: Learning Project
 * License: GPL v2 or later
 * Text Domain: post-editor-showcase
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register custom post type on init
add_action( 'init', 'pes_register_post_type' );

function pes_register_post_type() {
	register_post_type( 'pes_article', [
		'label'        => 'Editor Showcase Articles',
		'public'       => true,
		'show_ui'      => true,
		'show_in_menu' => true,
		'supports'     => [ 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ],
		'has_archive'  => true,
		'rewrite'      => [ 'slug' => 'articles' ],
	] );
}

// Add metaboxes on edit screen
add_action( 'add_meta_boxes', 'pes_add_metaboxes' );

function pes_add_metaboxes() {
	// Editor Metadata Metabox
	add_meta_box(
		'pes_editor_metadata',
		'Editor Metadata',
		'pes_render_editor_metadata_metabox',
		'pes_article',
		'normal',
		'high'
	);

	// Advanced Fields Metabox
	add_meta_box(
		'pes_advanced_fields',
		'Advanced Fields',
		'pes_render_advanced_fields_metabox',
		'pes_article',
		'normal',
		'high'
	);

	// Editor Info Metabox
	add_meta_box(
		'pes_editor_info',
		'Editor Information',
		'pes_render_editor_info_metabox',
		'pes_article',
		'side',
		'default'
	);
}

/**
 * Render Editor Metadata Metabox
 *
 * Demonstrates:
 * - Retrieving post metadata
 * - Displaying metabox UI
 * - Nonce field creation
 */
function pes_render_editor_metadata_metabox( $post ) {
	// Get current metadata
	$article_title = get_post_meta( $post->ID, 'article_title', true );
	$article_description = get_post_meta( $post->ID, 'article_description', true );
	$featured_image_url = get_post_meta( $post->ID, 'featured_image_url', true );

	// Create nonce for security verification
	wp_nonce_field( 'pes_save_metabox', 'pes_metabox_nonce' );

	?>
	<div class="pes-metabox">
		<p>
			<label for="pes_article_title"><strong>Article Title:</strong></label>
			<input
				type="text"
				id="pes_article_title"
				name="pes_article_title"
				value="<?php echo esc_attr( $article_title ); ?>"
				placeholder="Enter article title"
				class="widefat"
			/>
		</p>

		<p>
			<label for="pes_article_description"><strong>Article Description:</strong></label>
			<textarea
				id="pes_article_description"
				name="pes_article_description"
				rows="4"
				class="widefat"
				placeholder="Enter article description"
			><?php echo esc_textarea( $article_description ); ?></textarea>
		</p>

		<p>
			<label for="pes_featured_image_url"><strong>Featured Image URL:</strong></label>
			<input
				type="url"
				id="pes_featured_image_url"
				name="pes_featured_image_url"
				value="<?php echo esc_attr( $featured_image_url ); ?>"
				placeholder="https://example.com/image.jpg"
				class="widefat"
			/>
		</p>

		<p style="color: #666; font-size: 12px;">
			<em>This metabox demonstrates post metadata retrieval and storage.</em>
		</p>
	</div>
	<?php
}

/**
 * Render Advanced Fields Metabox
 *
 * Demonstrates:
 * - Multiple metadata fields
 * - Select dropdowns
 * - Priority and status fields
 */
function pes_render_advanced_fields_metabox( $post ) {
	$article_priority = get_post_meta( $post->ID, 'article_priority', true );
	$article_status = get_post_meta( $post->ID, 'article_status', true );
	$article_tags = get_post_meta( $post->ID, 'article_tags', true );

	wp_nonce_field( 'pes_save_metabox', 'pes_metabox_nonce' );

	?>
	<div class="pes-metabox">
		<p>
			<label for="pes_article_priority"><strong>Priority:</strong></label>
			<select id="pes_article_priority" name="pes_article_priority" class="widefat">
				<option value="">-- Select Priority --</option>
				<option value="low" <?php selected( $article_priority, 'low' ); ?>>Low</option>
				<option value="medium" <?php selected( $article_priority, 'medium' ); ?>>Medium</option>
				<option value="high" <?php selected( $article_priority, 'high' ); ?>>High</option>
			</select>
		</p>

		<p>
			<label for="pes_article_status"><strong>Status:</strong></label>
			<select id="pes_article_status" name="pes_article_status" class="widefat">
				<option value="">-- Select Status --</option>
				<option value="draft" <?php selected( $article_status, 'draft' ); ?>>Draft</option>
				<option value="review" <?php selected( $article_status, 'review' ); ?>>In Review</option>
				<option value="published" <?php selected( $article_status, 'published' ); ?>>Published</option>
			</select>
		</p>

		<p>
			<label for="pes_article_tags"><strong>Tags (comma-separated):</strong></label>
			<input
				type="text"
				id="pes_article_tags"
				name="pes_article_tags"
				value="<?php echo esc_attr( $article_tags ); ?>"
				placeholder="wordpress, php, editing"
				class="widefat"
			/>
		</p>

		<p style="color: #666; font-size: 12px;">
			<em>These fields demonstrate different input types and post metadata.</em>
		</p>
	</div>
	<?php
}

/**
 * Render Editor Info Metabox
 *
 * Demonstrates:
 * - Reading-only information
 * - Post revision data
 * - Editor details
 */
function pes_render_editor_info_metabox( $post ) {
	// Get post revisions
	$revisions = wp_get_post_revisions( $post->ID );
	$revision_count = count( $revisions );

	// Get post author
	$author = get_user_by( 'id', $post->post_author );
	$author_name = $author ? $author->display_name : 'Unknown';

	// Get created and modified dates
	$created_date = mysql2date( 'M d, Y H:i', $post->post_date );
	$modified_date = mysql2date( 'M d, Y H:i', $post->post_modified );

	?>
	<div class="pes-info-box">
		<p>
			<strong>Post ID:</strong> <?php echo esc_html( $post->ID ); ?>
		</p>

		<p>
			<strong>Author:</strong> <?php echo esc_html( $author_name ); ?>
		</p>

		<p>
			<strong>Created:</strong> <?php echo esc_html( $created_date ); ?>
		</p>

		<p>
			<strong>Last Modified:</strong> <?php echo esc_html( $modified_date ); ?>
		</p>

		<p>
			<strong>Revisions:</strong> <?php echo esc_html( $revision_count ); ?>
		</p>

		<p style="font-size: 12px; color: #666;">
			<em>This demonstrates reading post metadata and revisions.</em>
		</p>
	</div>
	<?php
}

/**
 * Save Post Data
 *
 * Demonstrates:
 * - save_post hook
 * - Nonce verification
 * - Capability checks
 * - Post metadata saving
 * - Avoiding infinite loops
 */
add_action( 'save_post_pes_article', 'pes_save_post_data', 10, 2 );

function pes_save_post_data( $post_id, $post ) {
	// Avoid infinite loops
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check capability - only allow editors and admins
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	// Verify nonce
	if ( ! isset( $_POST['pes_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['pes_metabox_nonce'], 'pes_save_metabox' ) ) {
		return;
	}

	// Save editor metadata
	if ( isset( $_POST['pes_article_title'] ) ) {
		$article_title = sanitize_text_field( $_POST['pes_article_title'] );
		update_post_meta( $post_id, 'article_title', $article_title );
	}

	if ( isset( $_POST['pes_article_description'] ) ) {
		$article_description = sanitize_textarea_field( $_POST['pes_article_description'] );
		update_post_meta( $post_id, 'article_description', $article_description );
	}

	if ( isset( $_POST['pes_featured_image_url'] ) ) {
		$featured_image_url = esc_url( $_POST['pes_featured_image_url'] );
		update_post_meta( $post_id, 'featured_image_url', $featured_image_url );
	}

	// Save advanced fields
	if ( isset( $_POST['pes_article_priority'] ) ) {
		$priority = sanitize_text_field( $_POST['pes_article_priority'] );
		// Validate that priority is one of allowed values
		if ( in_array( $priority, [ 'low', 'medium', 'high' ], true ) ) {
			update_post_meta( $post_id, 'article_priority', $priority );
		}
	}

	if ( isset( $_POST['pes_article_status'] ) ) {
		$status = sanitize_text_field( $_POST['pes_article_status'] );
		// Validate that status is one of allowed values
		if ( in_array( $status, [ 'draft', 'review', 'published' ], true ) ) {
			update_post_meta( $post_id, 'article_status', $status );
		}
	}

	if ( isset( $_POST['pes_article_tags'] ) ) {
		$tags = sanitize_text_field( $_POST['pes_article_tags'] );
		update_post_meta( $post_id, 'article_tags', $tags );
	}
}

/**
 * Enqueue Metabox Styles
 */
add_action( 'admin_enqueue_scripts', 'pes_enqueue_admin_styles' );

function pes_enqueue_admin_styles( $hook ) {
	// Only load on post edit screen
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	// Check if this is our custom post type
	global $post;
	if ( ! isset( $post ) || 'pes_article' !== $post->post_type ) {
		return;
	}

	wp_enqueue_style(
		'pes-admin-styles',
		plugin_dir_url( __FILE__ ) . 'css/editor-styles.css',
		[],
		'1.0.0'
	);
}

/**
 * Display Post Metadata on Frontend
 *
 * Demonstrates:
 * - Retrieving and displaying post metadata
 * - Integration with post content
 */
add_filter( 'the_content', 'pes_display_metadata_frontend' );

function pes_display_metadata_frontend( $content ) {
	// Only on pes_article posts, single view
	if ( ! is_singular( 'pes_article' ) || ! is_main_query() ) {
		return $content;
	}

	$post_id = get_the_ID();

	// Retrieve metadata
	$article_title = get_post_meta( $post_id, 'article_title', true );
	$article_priority = get_post_meta( $post_id, 'article_priority', true );
	$article_status = get_post_meta( $post_id, 'article_status', true );

	// Build metadata display
	$metadata_html = '';

	if ( $article_title || $article_priority || $article_status ) {
		$metadata_html .= '<div class="pes-metadata-display">';

		if ( $article_title ) {
			$metadata_html .= '<p><strong>Article Title:</strong> ' . esc_html( $article_title ) . '</p>';
		}

		if ( $article_priority ) {
			$metadata_html .= '<p><strong>Priority:</strong> ' . esc_html( ucfirst( $article_priority ) ) . '</p>';
		}

		if ( $article_status ) {
			$metadata_html .= '<p><strong>Status:</strong> ' . esc_html( ucfirst( $article_status ) ) . '</p>';
		}

		$metadata_html .= '</div>';
	}

	return $content . $metadata_html;
}

/**
 * Handle Plugin Activation
 *
 * Demonstrates:
 * - Creating sample posts for testing
 * - Plugin setup
 */
register_activation_hook( __FILE__, 'pes_plugin_activated' );

function pes_plugin_activated() {
	// Create sample article
	$sample_post = wp_insert_post( [
		'post_title'   => 'Sample Article',
		'post_type'    => 'pes_article',
		'post_status'  => 'publish',
		'post_content' => 'This is a sample article to demonstrate the editing workflow.',
	] );

	if ( $sample_post ) {
		// Add metadata to sample post
		add_post_meta( $sample_post, 'article_title', 'Learning WordPress Editor' );
		add_post_meta( $sample_post, 'article_description', 'Understanding the post editing workflow' );
		add_post_meta( $sample_post, 'article_priority', 'high' );
		add_post_meta( $sample_post, 'article_status', 'published' );
		add_post_meta( $sample_post, 'article_tags', 'wordpress, editing, metaboxes' );
	}
}

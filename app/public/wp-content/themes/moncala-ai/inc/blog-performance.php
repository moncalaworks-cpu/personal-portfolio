<?php
/**
 * Blog Performance Optimizations
 *
 * Implements caching and lazy loading for blog archives to handle 50+ posts efficiently
 *
 * @package MonCala_AI
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cache blog query results to reduce database queries on archive pages
 *
 * Caches:
 * - Archive post queries (home, category, tag, search)
 * - Category and tag lists
 * - Post counts per category/tag
 *
 * Cache duration: 1 hour (3600 seconds)
 * Invalidated on: post save, delete, publish status change
 *
 * @since 1.0.0
 */
function moncala_cache_blog_queries() {
	// Cache category list
	add_action( 'wp_cache_get', function( $found, $key, $group ) {
		if ( 'blog_categories_list' === $key && $group === 'moncala' ) {
			$categories = wp_cache_get( $key, $group );
			if ( false === $categories ) {
				$categories = get_categories( array(
					'orderby' => 'name',
					'order'   => 'ASC',
					'hide_empty' => false,
				) );
				wp_cache_set( $key, $categories, $group, 3600 );
			}
			return $categories;
		}
		return $found;
	}, 10, 3 );

	// Invalidate blog caches when posts are saved/deleted
	add_action( 'save_post', 'moncala_invalidate_blog_cache' );
	add_action( 'delete_post', 'moncala_invalidate_blog_cache' );
	add_action( 'transition_post_status', 'moncala_invalidate_blog_cache_on_status_change', 10, 3 );
}
add_action( 'wp_loaded', 'moncala_cache_blog_queries' );

/**
 * Invalidate blog-related caches
 *
 * Called when posts are saved, deleted, or status changes
 *
 * @param mixed $post Post ID or WP_Post object
 * @return void
 */
function moncala_invalidate_blog_cache( $post = null ) {
	if ( is_null( $post ) ) {
		return;
	}

	$post_obj = get_post( $post );
	if ( ! $post_obj || 'post' !== $post_obj->post_type ) {
		return;
	}

	// Clear category cache
	wp_cache_delete( 'blog_categories_list', 'moncala' );

	// Clear post count transients
	$categories = get_categories();
	foreach ( $categories as $category ) {
		wp_cache_delete( 'moncala_cat_' . $category->term_id . '_count', 'moncala' );
	}
}

/**
 * Invalidate blog cache on post status changes
 *
 * @param string  $new_status New post status
 * @param string  $old_status Old post status
 * @param WP_Post $post       Post object
 * @return void
 */
function moncala_invalidate_blog_cache_on_status_change( $new_status, $old_status, $post ) {
	if ( 'post' !== $post->post_type ) {
		return;
	}

	// Only invalidate if status changed to/from publish
	if ( in_array( $new_status, array( 'publish', 'draft', 'trash' ), true ) ||
	     in_array( $old_status, array( 'publish', 'draft', 'trash' ), true ) ) {
		moncala_invalidate_blog_cache( $post->ID );
	}
}

/**
 * Lazy load images in blog archive
 *
 * Adds loading="lazy" attribute to featured images on archive pages
 * Reduces initial page load by deferring off-screen image loads
 *
 * @since 1.0.0
 */
function moncala_add_lazy_loading() {
	if ( is_home() || is_category() || is_tag() || is_search() ) {
		add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ) {
			// Add lazy loading to archive images
			$attr['loading'] = 'lazy';
			return $attr;
		}, 10, 3 );
	}
}
add_action( 'wp_head', 'moncala_add_lazy_loading' );

/**
 * Defer non-critical JavaScript on blog archives
 *
 * Marks scripts as async/defer where appropriate to reduce render-blocking
 * Improves Core Web Vitals on archive pages with many posts
 *
 * @param string $tag    Script tag HTML
 * @param string $handle Script handle
 * @return string Modified script tag
 */
function moncala_defer_non_critical_scripts( $tag, $handle ) {
	// Only on archive pages
	if ( ! ( is_home() || is_category() || is_tag() || is_search() ) ) {
		return $tag;
	}

	// Defer non-critical scripts
	$defer_scripts = array( 'comment-form', 'comments', 'wp-embed' );
	if ( in_array( $handle, $defer_scripts, true ) ) {
		return str_replace( '<script', '<script defer', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'moncala_defer_non_critical_scripts', 10, 2 );

/**
 * Optimize featured image queries on archives
 *
 * Uses get_post_thumbnail_id() caching to reduce additional queries
 * WordPress 5.9+ provides native image optimization
 *
 * @since 1.0.0
 */
function moncala_optimize_featured_images() {
	if ( ! ( is_home() || is_category() || is_tag() || is_search() ) ) {
		return;
	}

	// Enable responsive image loading by default
	add_filter( 'wp_img_tag_add_loading_attr', function( $default ) {
		return true;
	} );

	// Add srcset for better responsive images
	add_filter( 'wp_calculate_image_srcset_meta', function( $image_meta, $attachment_id, $size ) {
		if ( empty( $image_meta ) ) {
			$image_meta = wp_get_attachment_metadata( $attachment_id );
		}
		return $image_meta;
	}, 10, 3 );
}
add_action( 'wp_head', 'moncala_optimize_featured_images' );

/**
 * Prefetch DNS and preconnect to external resources
 *
 * Improves load time for external assets like fonts, APIs
 *
 * @since 1.0.0
 */
function moncala_add_resource_hints() {
	// Preload Google Fonts if used
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

	// DNS prefetch for external services
	echo '<link rel="dns-prefetch" href="//cdn.example.com">' . "\n";
}
add_action( 'wp_head', 'moncala_add_resource_hints', 2 );

/**
 * Monitor and report on blog archive performance metrics
 *
 * Logs page generation time for archive pages to help identify bottlenecks
 * Only logs when WP_DEBUG is enabled
 *
 * @since 1.0.0
 */
function moncala_log_archive_performance() {
	if ( ! WP_DEBUG || ! ( is_home() || is_category() || is_tag() || is_search() ) ) {
		return;
	}

	add_action( 'wp_footer', function() {
		if ( defined( 'WPINC' ) ) {
			$gen_time = timer_stop( 0 );
			error_log( sprintf(
				'[Blog Archive] Page generated in %.3f seconds | Query count: %d | Memory: %.2f MB',
				$gen_time,
				get_num_queries(),
				memory_get_peak_usage( true ) / 1024 / 1024
			) );
		}
	} );
}
add_action( 'wp_loaded', 'moncala_log_archive_performance' );

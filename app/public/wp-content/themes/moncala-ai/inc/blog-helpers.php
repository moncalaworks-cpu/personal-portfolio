<?php
/**
 * Blog Helper Functions
 *
 * Utility functions for blog functionality
 *
 * @package MonCala_AI
 */

/**
 * Get estimated reading time for a post
 *
 * @param int $post_id Post ID
 * @return string Reading time estimate (e.g., "8 min read")
 */
function moncala_get_reading_time( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}

	// Count words in post content
	$word_count = str_word_count( strip_tags( $post->post_content ) );

	// Assume 200 words per minute reading speed
	$reading_time = ceil( $word_count / 200 );

	// Minimum 1 minute
	$reading_time = max( 1, $reading_time );

	return $reading_time . ' min read';
}

/**
 * Get related posts by category and tags
 *
 * @param int $post_id Post ID
 * @param int $num_posts Number of related posts to return (default: 3)
 * @return WP_Post[] Array of related posts
 */
function moncala_get_related_posts( $post_id = 0, $num_posts = 3 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return array();
	}

	// Get post categories and tags
	$categories = get_the_category( $post_id );
	$tags       = get_the_tags( $post_id );

	$category_ids = array();
	$tag_ids      = array();

	foreach ( $categories as $category ) {
		$category_ids[] = $category->term_id;
	}

	foreach ( $tags as $tag ) {
		$tag_ids[] = $tag->term_id;
	}

	// Query related posts
	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => $num_posts,
		'post__not_in'   => array( $post_id ),
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tax_query'      => array(
			'relation' => 'OR',
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $category_ids,
			),
			array(
				'taxonomy' => 'post_tag',
				'field'    => 'term_id',
				'terms'    => $tag_ids,
			),
		),
	);

	return get_posts( $args );
}

/**
 * Generate table of contents from post headings
 *
 * @param string $content Post content
 * @return array Array of headings with links
 */
function moncala_generate_table_of_contents( $content ) {
	$toc = array();

	// Find all H2 and H3 headings
	preg_match_all( '/<h([23])[^>]*>(.*?)<\/h\1>/i', $content, $matches, PREG_SET_ORDER );

	$counter = 0;
	foreach ( $matches as $match ) {
		$level  = intval( $match[1] );
		$text   = wp_strip_all_tags( $match[2] );
		$anchor = 'heading-' . $counter;

		$toc[] = array(
			'level'  => $level,
			'text'   => $text,
			'anchor' => $anchor,
		);

		$counter++;
	}

	return $toc;
}

/**
 * Add anchors to post headings for TOC navigation
 *
 * @param string $content Post content
 * @return string Content with anchors added to headings
 */
function moncala_add_heading_anchors( $content ) {
	$counter = 0;

	$content = preg_replace_callback(
		'/<h([23])([^>]*)>(.*?)<\/h\1>/i',
		function( $matches ) use ( &$counter ) {
			$level   = $matches[1];
			$attrs   = $matches[2];
			$text    = $matches[3];
			$anchor  = 'heading-' . $counter;
			$counter++;

			return sprintf(
				'<h%s%s id="%s">%s</h%s>',
				$level,
				$attrs,
				$anchor,
				$text,
				$level
			);
		},
		$content
	);

	return $content;
}

/**
 * Get social sharing URLs for a post
 *
 * @param int $post_id Post ID
 * @return array Array of social share URLs
 */
function moncala_get_social_share_urls( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return array();
	}

	$url   = get_permalink( $post_id );
	$title = get_the_title( $post_id );

	return array(
		'twitter'   => sprintf(
			'https://twitter.com/intent/tweet?url=%s&text=%s',
			urlencode( $url ),
			urlencode( $title )
		),
		'linkedin'  => sprintf(
			'https://www.linkedin.com/sharing/share-offsite/?url=%s',
			urlencode( $url )
		),
		'facebook'  => sprintf(
			'https://www.facebook.com/sharer/sharer.php?u=%s',
			urlencode( $url )
		),
		'copy_link' => $url,
	);
}

/**
 * Get blog page URL
 *
 * @return string Blog page URL
 */
function moncala_get_blog_url() {
	// Try to get blog page URL, fall back to /blog/
	$blog_page = get_page_by_path( 'blog' );
	if ( $blog_page ) {
		return get_permalink( $blog_page );
	}

	return home_url( '/blog/' );
}

/**
 * Check if current page is blog archive or single post
 *
 * @return bool True if on blog page or post
 */
function moncala_is_blog() {
	return ( is_home() || is_category() || is_tag() || is_search() || is_singular( 'post' ) );
}

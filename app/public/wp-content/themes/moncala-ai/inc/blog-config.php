<?php
/**
 * Blog Configuration
 *
 * Sets up blog-specific WordPress settings
 *
 * @package MonCala_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set posts per page for blog archive to 5
 */
function moncala_set_posts_per_page( $query ) {
	if ( is_admin() ) {
		return;
	}

	// Only modify main query on blog archives
	if ( $query->is_main_query() && ( is_home() || is_category() || is_tag() || is_search() ) ) {
		$query->set( 'posts_per_page', 5 );
	}
}
add_action( 'pre_get_posts', 'moncala_set_posts_per_page' );

/**
 * Enable comments on posts by default
 */
function moncala_enable_comments() {
	add_post_type_support( 'post', 'comments' );
}
add_action( 'init', 'moncala_enable_comments' );

/**
 * Add schema markup for blog posts
 */
function moncala_add_post_schema() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$post_id = get_the_ID();
	$post    = get_post( $post_id );

	$schema = array(
		'@context'       => 'https://schema.org',
		'@type'          => 'BlogPosting',
		'headline'       => get_the_title( $post_id ),
		'description'    => get_the_excerpt( $post_id ),
		'image'          => get_the_post_thumbnail_url( $post_id, 'large' ),
		'datePublished'  => get_the_date( 'c', $post_id ),
		'dateModified'   => get_post_modified_time( 'c', false, $post_id ),
		'author'         => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $post->post_author ),
		),
		'inLanguage'     => 'en-US',
		'wordCount'      => str_word_count( wp_strip_all_tags( $post->post_content ) ),
	);

	// Add publisher if blog name is set
	$blogname = get_bloginfo( 'name' );
	if ( $blogname ) {
		$schema['publisher'] = array(
			'@type' => 'Organization',
			'name'  => $blogname,
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => get_site_icon_url(),
			),
		);
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'moncala_add_post_schema' );

/**
 * Add breadcrumb schema markup
 */
function moncala_add_breadcrumb_schema() {
	if ( ! moncala_is_blog() ) {
		return;
	}

	$breadcrumbs = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => 'Home',
			'item'     => home_url(),
		),
		array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => 'Blog',
			'item'     => moncala_get_blog_url(),
		),
	);

	if ( is_singular( 'post' ) ) {
		$breadcrumbs[] = array(
			'@type'    => 'ListItem',
			'position' => 3,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $breadcrumbs,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'moncala_add_breadcrumb_schema' );

/**
 * Add open graph tags for social sharing
 */
function moncala_add_open_graph_tags() {
	if ( is_singular( 'post' ) ) {
		$post_id = get_the_ID();
		$title   = get_the_title( $post_id );
		$desc    = get_the_excerpt( $post_id );
		$image   = get_the_post_thumbnail_url( $post_id, 'large' );
		$url     = get_permalink( $post_id );

		echo '<meta property="og:type" content="article">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( wp_trim_words( $desc, 20, '...' ) ) . '">' . "\n";
		if ( $image ) {
			echo '<meta property="og:image" content="' . esc_attr( $image ) . '">' . "\n";
		}
		echo '<meta property="og:url" content="' . esc_attr( $url ) . '">' . "\n";

		// Twitter Card
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( wp_trim_words( $desc, 20, '...' ) ) . '">' . "\n";
		if ( $image ) {
			echo '<meta name="twitter:image" content="' . esc_attr( $image ) . '">' . "\n";
		}
	}
}
add_action( 'wp_head', 'moncala_add_open_graph_tags' );

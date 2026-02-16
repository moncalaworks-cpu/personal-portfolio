<?php
/**
 * MonCala AI Theme - SEO Functions
 *
 * Handles all SEO meta tags, Open Graph tags, Twitter cards, and schema.org markup.
 * Optimizes theme for search engines and social sharing.
 *
 * @package MonCala_AI
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add meta tags to head
 * Includes: description, robots, OG tags, Twitter cards
 *
 * @since 1.0.0
 */
function moncala_seo_meta_tags() {
	// Homepage meta description
	if ( is_home() || is_front_page() ) {
		$description = esc_attr( 'MonCala Works - Integrating AI into Legacy Codebases. AI consulting and legacy system modernization.' );
	} elseif ( is_single() || is_page() ) {
		// Get custom excerpt for posts/pages
		$description = get_the_excerpt();
		if ( empty( $description ) ) {
			$description = wp_trim_words( get_the_content(), 20 );
		}
		$description = esc_attr( wp_strip_all_tags( $description ) );
		// Ensure 120-160 characters
		if ( strlen( $description ) > 160 ) {
			$description = substr( $description, 0, 157 ) . '...';
		}
	} elseif ( is_archive() || is_category() ) {
		$description = esc_attr( 'Explore our blog posts about AI integration, legacy modernization, and technical topics.' );
	} else {
		$description = esc_attr( get_bloginfo( 'description' ) );
	}

	// Meta description tag
	if ( ! empty( $description ) ) {
		echo '<meta name="description" content="' . $description . '">' . "\n";
	}

	// Robots meta tag
	echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . "\n";

	// Open Graph tags for social sharing
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";

	if ( is_single() || is_page() ) {
		echo '<meta property="og:type" content="article">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '">' . "\n";
		echo '<meta property="og:description" content="' . $description . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_attr( get_permalink() ) . '">' . "\n";

		// Featured image for OG
		if ( has_post_thumbnail() ) {
			$thumb_url = get_the_post_thumbnail_url( null, 'moncala-featured' );
			echo '<meta property="og:image" content="' . esc_attr( $thumb_url ) . '">' . "\n";
			echo '<meta property="og:image:width" content="1200">' . "\n";
			echo '<meta property="og:image:height" content="600">' . "\n";
		}

		// Published/modified dates for articles
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
		echo '<meta property="article:author" content="' . esc_attr( get_the_author() ) . '">' . "\n";
	} else {
		echo '<meta property="og:type" content="website">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
		echo '<meta property="og:description" content="' . $description . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_attr( get_the_permalink() ? get_the_permalink() : home_url() ) . '">' . "\n";
	}

	// Twitter Card tags
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . $description . '">' . "\n";

	if ( is_single() || is_page() ) {
		if ( has_post_thumbnail() ) {
			$thumb_url = get_the_post_thumbnail_url( null, 'moncala-featured' );
			echo '<meta name="twitter:image" content="' . esc_attr( $thumb_url ) . '">' . "\n";
		}
	}

	// Canonical URL
	if ( is_singular() ) {
		echo '<link rel="canonical" href="' . esc_attr( get_permalink() ) . '">' . "\n";
	} elseif ( is_home() || is_front_page() ) {
		echo '<link rel="canonical" href="' . esc_attr( home_url() ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'moncala_seo_meta_tags', 5 );

/**
 * Add schema.org JSON-LD structured data
 * Includes: Article, Organization, BreadcrumbList
 *
 * @since 1.0.0
 */
function moncala_schema_org() {
	$schema = array();

	// Organization schema (for homepage and all pages)
	$organization_schema = array(
		'@context'       => 'https://schema.org',
		'@type'          => 'Organization',
		'name'           => get_bloginfo( 'name' ),
		'description'    => get_bloginfo( 'description' ),
		'url'            => home_url(),
		'logo'           => array(
			'@type' => 'ImageObject',
			'url'   => get_site_icon_url( 200 ),
			'width' => 200,
			'height' => 200,
		),
		'sameAs'         => array(
			'https://github.com/moncalaworks-cpu',
			'https://www.linkedin.com/company/moncala-works',
		),
		'contactPoint'   => array(
			'@type'       => 'ContactPoint',
			'contactType' => 'Customer Service',
			'email'       => get_option( 'admin_email' ),
		),
	);
	$schema[] = $organization_schema;

	// Article schema (for blog posts)
	if ( is_singular( 'post' ) && ! is_front_page() ) {
		$article_schema = array(
			'@context'       => 'https://schema.org',
			'@type'          => 'BlogPosting',
			'headline'       => get_the_title(),
			'description'    => wp_strip_all_tags( get_the_excerpt() ),
			'image'          => has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'moncala-featured' ) : '',
			'datePublished'  => get_the_date( 'c' ),
			'dateModified'   => get_the_modified_date( 'c' ),
			'author'         => array(
				'@type' => 'Person',
				'name'  => get_the_author(),
			),
			'publisher'      => array(
				'@type' => 'Organization',
				'name'  => get_bloginfo( 'name' ),
				'logo'  => array(
					'@type' => 'ImageObject',
					'url'   => get_site_icon_url( 200 ),
				),
			),
		);
		$schema[] = $article_schema;

		// BreadcrumbList schema for posts
		$breadcrumb_schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
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
					'item'     => home_url( '/blog' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 3,
					'name'     => get_the_title(),
					'item'     => get_permalink(),
				),
			),
		);
		$schema[] = $breadcrumb_schema;
	}

	// BreadcrumbList for category/archive pages
	if ( is_category() ) {
		$category = get_queried_object();
		$breadcrumb_schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
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
					'item'     => home_url( '/blog' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 3,
					'name'     => $category->name,
					'item'     => get_category_link( $category->term_id ),
				),
			),
		);
		$schema[] = $breadcrumb_schema;
	}

	// Output schema.org JSON-LD
	if ( ! empty( $schema ) ) {
		echo '<script type="application/ld+json">' . "\n";
		// Use JSON_UNESCAPED_SLASHES for cleaner URLs
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . "\n";
		echo '</script>' . "\n";
	}
}
add_action( 'wp_head', 'moncala_schema_org', 10 );

/**
 * Improve title tag format
 * Makes title more SEO-friendly
 *
 * @since 1.0.0
 */
function moncala_document_title_parts( $title_parts ) {
	// Add tagline to homepage
	if ( is_home() || is_front_page() ) {
		$title_parts['tagline'] = get_bloginfo( 'description' );
	}

	// For archives, improve format
	if ( is_category() ) {
		$category = get_queried_object();
		$title_parts['title'] = $category->name . ' - Articles';
	}

	return $title_parts;
}
add_filter( 'document_title_parts', 'moncala_document_title_parts' );

/**
 * Add preload hints for fonts
 * Improves performance for web fonts
 *
 * @since 1.0.0
 */
function moncala_preload_fonts() {
	?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="dns-prefetch" href="https://fonts.googleapis.com">
	<?php
}
add_action( 'wp_head', 'moncala_preload_fonts', 2 );

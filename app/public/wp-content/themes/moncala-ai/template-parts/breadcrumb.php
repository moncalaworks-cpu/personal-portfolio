<?php
/**
 * Breadcrumb Navigation
 *
 * Displays breadcrumb trail for navigation
 *
 * @package MonCala_AI
 */

$breadcrumbs = array();

// Home
$breadcrumbs[] = array(
	'url'   => home_url(),
	'label' => __( 'Home', 'moncala-ai' ),
);

// Blog (if not on blog index)
if ( ! is_home() && ! is_archive() ) {
	$breadcrumbs[] = array(
		'url'   => moncala_get_blog_url(),
		'label' => __( 'Blog', 'moncala-ai' ),
	);
}

// Current page/post
if ( is_singular( 'post' ) ) {
	$breadcrumbs[] = array(
		'url'   => false,
		'label' => get_the_title(),
	);
} elseif ( is_category() ) {
	$breadcrumbs[] = array(
		'url'   => false,
		'label' => single_cat_title( '', false ),
	);
} elseif ( is_tag() ) {
	$breadcrumbs[] = array(
		'url'   => false,
		'label' => single_tag_title( '', false ),
	);
} elseif ( is_search() ) {
	$breadcrumbs[] = array(
		'url'   => false,
		'label' => sprintf( __( 'Search: %s', 'moncala-ai' ), get_search_query() ),
	);
}
?>

<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'moncala-ai' ); ?>">
	<ol class="breadcrumb__list">
		<?php foreach ( $breadcrumbs as $index => $breadcrumb ) : ?>
			<li class="breadcrumb__item">
				<?php if ( $breadcrumb['url'] ) : ?>
					<a href="<?php echo esc_url( $breadcrumb['url'] ); ?>" class="breadcrumb__link">
						<?php echo esc_html( $breadcrumb['label'] ); ?>
					</a>
				<?php else : ?>
					<span class="breadcrumb__current">
						<?php echo esc_html( $breadcrumb['label'] ); ?>
					</span>
				<?php endif; ?>

				<?php if ( $index < count( $breadcrumbs ) - 1 ) : ?>
					<span class="breadcrumb__separator" aria-hidden="true">/</span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>

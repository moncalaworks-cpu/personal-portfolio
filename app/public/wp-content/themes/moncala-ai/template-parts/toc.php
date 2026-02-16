<?php
/**
 * Table of Contents
 *
 * Displays a table of contents for post content
 *
 * @package MonCala_AI
 */

$post_id  = get_the_ID();
$content  = get_post_field( 'post_content', $post_id );
$toc      = moncala_generate_table_of_contents( $content );

if ( empty( $toc ) ) {
	return;
}
?>

<div class="toc">
	<div class="toc__header">
		<h3 class="toc__title"><?php esc_html_e( 'Table of Contents', 'moncala-ai' ); ?></h3>
		<button class="toc__toggle" aria-label="<?php esc_attr_e( 'Toggle table of contents', 'moncala-ai' ); ?>" aria-expanded="true">
			<svg class="icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
				<path d="M7 12L10 9L13 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
			</svg>
		</button>
	</div>

	<nav class="toc__nav" aria-label="<?php esc_attr_e( 'Table of contents', 'moncala-ai' ); ?>">
		<ol class="toc__list">
			<?php foreach ( $toc as $item ) : ?>
				<li class="toc__item toc__item--level-<?php echo absint( $item['level'] ); ?>">
					<a href="#<?php echo esc_attr( $item['anchor'] ); ?>" class="toc__link">
						<?php echo esc_html( $item['text'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
</div>

<?php
/**
 * Post Card Component
 *
 * Displays a single post in card format for blog archive
 *
 * @package MonCala_AI
 */

$post_id = get_the_ID();
$title   = get_the_title( $post_id );
$excerpt = get_the_excerpt( $post_id );
$date    = get_the_date( 'M d, Y', $post_id );
$time    = moncala_get_reading_time( $post_id );
?>

<article class="post-card">
	<?php if ( has_post_thumbnail( $post_id ) ) : ?>
		<div class="post-card__image">
			<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
				<?php
				the_post_thumbnail(
					'medium',
					array(
						'alt' => sprintf(
							esc_attr__( 'Featured image for %s', 'moncala-ai' ),
							esc_attr( $title )
						),
					)
				);
				?>
			</a>
		</div>
	<?php endif; ?>

	<div class="post-card__content">
		<h3 class="post-card__title">
			<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
				<?php echo esc_html( $title ); ?>
			</a>
		</h3>

		<div class="post-card__meta">
			<span class="post-card__date">
				<?php echo esc_html( $date ); ?>
			</span>
			<span class="post-card__read-time">
				<?php echo esc_html( $time ); ?>
			</span>
		</div>

		<?php
		$categories = get_the_category( $post_id );
		if ( ! empty( $categories ) ) :
			?>
			<div class="post-card__categories">
				<?php
				foreach ( $categories as $category ) {
					printf(
						'<a href="%s" class="post-card__category">%s</a>',
						esc_url( get_category_link( $category ) ),
						esc_html( $category->name )
					);
				}
				?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $excerpt ) ) : ?>
			<p class="post-card__excerpt">
				<?php echo wp_kses_post( wp_trim_words( $excerpt, 20, '...' ) ); ?>
			</p>
		<?php endif; ?>

		<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="post-card__link">
			<?php esc_html_e( 'Read More', 'moncala-ai' ); ?>
			<svg class="icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
				<path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</a>
	</div>
</article>

<?php
/**
 * Related Posts
 *
 * Displays related posts based on category and tags
 *
 * @package MonCala_AI
 */

$post_id  = get_the_ID();
$related  = moncala_get_related_posts( $post_id, 3 );

if ( empty( $related ) ) {
	return;
}
?>

<section class="related-posts">
	<h3 class="related-posts__title"><?php esc_html_e( 'Recommended Reading', 'moncala-ai' ); ?></h3>

	<div class="related-posts__grid">
		<?php
		foreach ( $related as $post ) {
			setup_postdata( $post );
			?>
			<article class="related-post">
				<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
					<div class="related-post__image">
						<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>">
							<?php
							echo get_the_post_thumbnail(
								$post->ID,
								'medium',
								array(
									'alt' => sprintf(
										esc_attr__( 'Featured image for %s', 'moncala-ai' ),
										esc_attr( get_the_title( $post->ID ) )
									),
								)
							);
							?>
						</a>
					</div>
				<?php endif; ?>

				<div class="related-post__content">
					<h4 class="related-post__title">
						<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>">
							<?php echo esc_html( get_the_title( $post->ID ) ); ?>
						</a>
					</h4>

					<p class="related-post__meta">
						<span class="related-post__date">
							<?php echo esc_html( get_the_date( 'M d, Y', $post->ID ) ); ?>
						</span>
						<span class="related-post__read-time">
							<?php echo esc_html( moncala_get_reading_time( $post->ID ) ); ?>
						</span>
					</p>

					<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="related-post__link">
						<?php esc_html_e( 'Read More', 'moncala-ai' ); ?>
						<svg class="icon" width="14" height="14" viewBox="0 0 16 16" fill="none">
							<path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				</div>
			</article>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
</section>

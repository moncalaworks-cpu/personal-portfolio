<?php
/**
 * Social Sharing Buttons
 *
 * Displays social sharing buttons for blog posts
 *
 * @package MonCala_AI
 */

$post_id = get_the_ID();
$urls    = moncala_get_social_share_urls( $post_id );
?>

<div class="social-share">
	<p class="social-share__label"><?php esc_html_e( 'Share this post:', 'moncala-ai' ); ?></p>
	<div class="social-share__buttons">
		<?php if ( ! empty( $urls['twitter'] ) ) : ?>
			<a
				href="<?php echo esc_url( $urls['twitter'] ); ?>"
				class="social-share__button social-share__button--twitter"
				target="_blank"
				rel="noopener noreferrer"
				aria-label="<?php esc_attr_e( 'Share on Twitter', 'moncala-ai' ); ?>"
			>
				<svg class="icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
					<path d="M19.3 3.85c-.7.3-1.5.5-2.3.6.8-.5 1.4-1.3 1.7-2.3-.8.5-1.6.8-2.5 1-.7-.8-1.8-1.3-3-1.3-2.3 0-4.1 1.9-4.1 4.1 0 .3 0 .6.1.9C6.5 6.5 4.4 5.3 3 3.5c-.3.5-.5 1.1-.5 1.7 0 1.4.7 2.7 1.9 3.5-.7 0-1.3-.2-1.9-.5 0 2 1.4 3.7 3.3 4.1-.3.1-.7.1-1.1.1-.3 0-.5 0-.8-.1.5 1.6 2.1 2.8 3.9 2.8-1.4 1.1-3.2 1.7-5.1 1.7-.3 0-.6 0-.9-.1 1.8 1.2 4 1.9 6.3 1.9 7.6 0 11.7-6.3 11.7-11.7 0-.2 0-.4 0-.5.8-.6 1.5-1.4 2-2.3z"/>
				</svg>
				<span class="social-share__text"><?php esc_html_e( 'Twitter', 'moncala-ai' ); ?></span>
			</a>
		<?php endif; ?>

		<?php if ( ! empty( $urls['linkedin'] ) ) : ?>
			<a
				href="<?php echo esc_url( $urls['linkedin'] ); ?>"
				class="social-share__button social-share__button--linkedin"
				target="_blank"
				rel="noopener noreferrer"
				aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'moncala-ai' ); ?>"
			>
				<svg class="icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
					<path d="M2 2h16a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm3 9v7h3v-7H5zm1.5-3.8c1 0 1.8-.8 1.8-1.8S7.5 3.6 6.5 3.6 4.7 4.4 4.7 5.4s.8 1.8 1.8 1.8zM12 11c-1.3 0-2 .7-2.3 1.4h-.1V11h-3v7h3v-3.5c0-.9.2-1.8 1.3-1.8 1 0 1.1.9 1.1 1.8V18h3v-3.7c0-1.7-.4-3-2.3-3z"/>
				</svg>
				<span class="social-share__text"><?php esc_html_e( 'LinkedIn', 'moncala-ai' ); ?></span>
			</a>
		<?php endif; ?>

		<?php if ( ! empty( $urls['facebook'] ) ) : ?>
			<a
				href="<?php echo esc_url( $urls['facebook'] ); ?>"
				class="social-share__button social-share__button--facebook"
				target="_blank"
				rel="noopener noreferrer"
				aria-label="<?php esc_attr_e( 'Share on Facebook', 'moncala-ai' ); ?>"
			>
				<svg class="icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
					<path d="M18.9 0H1.1C.5 0 0 .5 0 1.1v17.8c0 .6.5 1.1 1.1 1.1h9.5v-7.7h-2.6v-3h2.6V7.4c0-2.6 1.6-4 3.9-4 1.1 0 2.1.1 2.4.1v2.8h-1.6c-1.3 0-1.5.6-1.5 1.5v2h3l-.4 3h-2.6v7.7h5.1c.6 0 1.1-.5 1.1-1.1V1.1C20 .5 19.5 0 18.9 0z"/>
				</svg>
				<span class="social-share__text"><?php esc_html_e( 'Facebook', 'moncala-ai' ); ?></span>
			</a>
		<?php endif; ?>

		<button
			class="social-share__button social-share__button--copy"
			data-copy-url="<?php echo esc_attr( $urls['copy_link'] ); ?>"
			aria-label="<?php esc_attr_e( 'Copy link to clipboard', 'moncala-ai' ); ?>"
		>
			<svg class="icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
				<path d="M7 9a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V9z"/>
				<path d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2V5h6a2 2 0 0 0-2-2H5z"/>
			</svg>
			<span class="social-share__text"><?php esc_html_e( 'Copy', 'moncala-ai' ); ?></span>
		</button>
	</div>
</div>

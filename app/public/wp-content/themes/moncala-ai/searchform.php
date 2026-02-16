<?php
/**
 * Search Form Template
 *
 * Displays the search form with accessibility support
 *
 * @package MonCala_AI
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="search-input" class="sr-only">
		<?php esc_html_e( 'Search for:', 'moncala-ai' ); ?>
	</label>
	<input
		type="search"
		id="search-input"
		class="search-form-input"
		placeholder="<?php esc_attr_e( 'Search articles...', 'moncala-ai' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
		aria-label="<?php esc_attr_e( 'Search blog articles', 'moncala-ai' ); ?>"
	>
	<button type="submit" class="search-form-button" aria-label="<?php esc_attr_e( 'Submit search', 'moncala-ai' ); ?>">
		<span aria-hidden="true">🔍</span>
		<span class="sr-only"><?php esc_html_e( 'Search', 'moncala-ai' ); ?></span>
	</button>
</form>

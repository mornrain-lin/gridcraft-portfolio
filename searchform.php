<?php
/**
 * 搜索表单模板。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

$gc_search_id = 'gc-search-' . wp_rand( 1000, 9999 );
?>

<form role="search" method="get" class="gc-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="gc-screen-reader-text" for="<?php echo esc_attr( $gc_search_id ); ?>">
		<?php esc_html_e( '搜索本站内容', 'gridcraft-portfolio' ); ?>
	</label>
	<input
		type="search"
		id="<?php echo esc_attr( $gc_search_id ); ?>"
		class="gc-search-form__field"
		placeholder="<?php esc_attr_e( '搜索作品、文章…', 'gridcraft-portfolio' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
	>
	<button type="submit" class="gc-search-form__submit">
		<?php esc_html_e( '搜索', 'gridcraft-portfolio' ); ?>
	</button>
</form>

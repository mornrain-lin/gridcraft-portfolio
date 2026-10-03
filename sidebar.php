<?php
/**
 * 侧栏模板。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}

gridcraft_sidebar( 'sidebar-1' );

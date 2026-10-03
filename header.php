<?php
/**
 * Gridcraft Portfolio 站点头部模板。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

$gc_transparent = is_front_page() && (bool) gridcraft_get_option( 'transparent_header' );
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
} else {
	do_action( 'wp_body_open' );
}
?>

<a class="gc-skip-link" href="#gc-content"><?php esc_html_e( '跳到正文', 'gridcraft-portfolio' ); ?></a>

<div id="page" class="gc-site">

	<header id="masthead" class="<?php echo esc_attr( 'gc-site-header' . ( $gc_transparent ? ' gc-site-header--transparent' : '' ) ); ?>">
		<div class="gc-container">
			<div class="gc-site-header__inner">

				<?php gridcraft_site_branding(); ?>

				<div class="gc-header-tools">
					<?php gridcraft_primary_nav(); ?>
				</div>

			</div>
		</div>
	</header>

	<main id="gc-content" class="gc-main" tabindex="-1">

<?php
/**
 * 作品归档模板（archive-portfolio.php）。
 *
 * 瀑布流网格 + 分类筛选条（原生 fetch，失败时降级为归档链接）。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();
?>

<div class="gc-container">
	<div class="gc-section gc-section--tight">

		<header class="gc-page-header">
			<h1 class="gc-page-header__title">
				<?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?>
			</h1>

			<?php
			$gc_description = get_the_archive_description();

			if ( $gc_description ) {
				echo '<div class="gc-page-header__description">' . wp_kses_post( wpautop( $gc_description ) ) . '</div>';
			}
			?>
		</header>

		<?php if ( gridcraft_get_option( 'filter_enabled' ) ) : ?>
			<?php gridcraft_filter_bar(); ?>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>

			<?php gridcraft_work_grid( $GLOBALS['wp_query'] ); ?>

			<?php gridcraft_pagination(); ?>

		<?php else : ?>

			<div class="gc-no-results">
				<h2><?php esc_html_e( '还没有作品', 'gridcraft-portfolio' ); ?></h2>
				<p><?php esc_html_e( '这个分类下暂时没有内容，先看看全部作品吧。', 'gridcraft-portfolio' ); ?></p>
			</div>

		<?php endif; ?>

	</div>
</div>

<?php
get_footer();

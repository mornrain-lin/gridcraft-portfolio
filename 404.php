<?php
/**
 * 404 未找到页面模板。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();
?>

<div class="gc-container">
	<div class="gc-404">
		<p class="gc-404__code">404</p>
		<h1 class="gc-404__title"><?php esc_html_e( '这个页面不存在', 'gridcraft-portfolio' ); ?></h1>
		<p class="gc-404__text"><?php esc_html_e( '地址可能已变更或被删除。看看下面的作品，或者直接搜索。', 'gridcraft-portfolio' ); ?></p>

		<div class="gc-404__actions">
			<a class="gc-button" href="<?php echo esc_url( (string) post_type_archive_link( 'portfolio' ) ); ?>">
				<?php esc_html_e( '浏览作品集', 'gridcraft-portfolio' ); ?>
			</a>
			<a class="gc-button gc-button--outline" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( '返回首页', 'gridcraft-portfolio' ); ?>
			</a>
		</div>

		<?php get_search_form(); ?>

		<?php
		$gc_recent = new WP_Query(
			array(
				'post_type'      => 'portfolio',
				'post_status'    => 'publish',
				'posts_per_page' => 6,
				'no_found_rows'  => true,
			)
		);

		if ( $gc_recent->have_posts() ) :
			?>
			<section class="gc-404__works">
				<h2><?php esc_html_e( '最新作品', 'gridcraft-portfolio' ); ?></h2>
				<div class="gc-post-list">
					<?php gridcraft_work_grid( $gc_recent, array( 'gc-grid--compact' ) ); ?>
				</div>
			</section>
			<?php
		endif;

		wp_reset_postdata();
		?>
	</div>
</div>

<?php
get_footer();

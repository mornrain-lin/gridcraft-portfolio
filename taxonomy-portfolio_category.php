<?php
/**
 * 作品分类法归档模板（taxonomy-portfolio_category.php）。
 *
 * 层级分类（portfolio_category）的归档页：
 * 顶部显示分类标题、描述与父级面包屑，下面是筛选条与作品网格。
 * 与 archive-portfolio.php 的区别在于这里按当前分类过滤，
 * 筛选条上的「全部」指向该分类的父级（若有）。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();

$gc_term     = get_queried_object();
$gc_term_id  = ( $gc_term instanceof WP_Term ) ? (int) $gc_term->term_id : 0;
$gc_taxonomy = ( $gc_term instanceof WP_Term ) ? $gc_term->taxonomy : 'portfolio_category';
?>

<div class="gc-container">
	<div class="gc-section gc-section--tight">

		<header class="gc-page-header">
			<?php if ( is_tax( $gc_taxonomy ) ) : ?>
				<p class="gc-page-header__eyebrow">
					<?php
					$gc_current = $gc_term_id ? get_term( $gc_term_id, $gc_taxonomy ) : null;
					$gc_parent  = ( $gc_current instanceof WP_Term ) ? (int) $gc_current->parent : 0;

					if ( $gc_parent > 0 ) {
						$gc_parent_term = get_term( $gc_parent, $gc_taxonomy );

						if ( $gc_parent_term instanceof WP_Term ) {
							$gc_parent_link = get_term_link( $gc_parent, $gc_taxonomy );

							if ( ! is_wp_error( $gc_parent_link ) ) {
								printf(
									'<a href="%1$s">%2$s</a>',
									esc_url( $gc_parent_link ),
									esc_html( $gc_parent_term->name )
								);
							}
						}
					} else {
						printf(
							'<a href="%1$s">%2$s</a>',
							esc_url( (string) post_type_archive_link( 'portfolio' ) ),
							esc_html__( '作品集', 'gridcraft-portfolio' )
						);
					}
					?>
				</p>
			<?php endif; ?>

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
				<h2><?php esc_html_e( '这个分类下暂时没有作品', 'gridcraft-portfolio' ); ?></h2>
				<p><?php esc_html_e( '换个分类看看，或回到全部作品。', 'gridcraft-portfolio' ); ?></p>
			</div>

		<?php endif; ?>

	</div>
</div>

<?php
get_footer();
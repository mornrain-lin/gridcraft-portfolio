<?php
/**
 * 搜索结果模板。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();

global $wp_query;
$gc_found = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
?>

<div class="gc-container">
	<div class="gc-section gc-section--tight">

		<header class="gc-page-header">
			<h1 class="gc-page-header__title">
				<?php
				printf(
					/* translators: %s：搜索关键词。 */
					esc_html__( '搜索：%s', 'gridcraft-portfolio' ),
					esc_html( get_search_query() )
				);
				?>
			</h1>

			<?php if ( have_posts() ) : ?>
				<p class="gc-page-header__description">
					<?php
					printf(
						/* translators: %s：结果数量。 */
						esc_html( _n( '共找到 %s 条结果。', '共找到 %s 条结果。', $gc_found, 'gridcraft-portfolio' ) ),
						esc_html( number_format_i18n( $gc_found ) )
					);
					?>
				</p>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="gc-post-list">
				<?php
				while ( have_posts() ) {
					the_post();
					?>
					<article <?php post_class( 'gc-work' ); ?>>
						<a class="gc-work__link" href="<?php the_permalink(); ?>">
							<div class="gc-work__media">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'gridcraft-card', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
								<?php else : ?>
									<div class="gc-work__placeholder"><?php esc_html_e( '暂无封面', 'gridcraft-portfolio' ); ?></div>
								<?php endif; ?>

								<div class="gc-work__overlay">
									<h2 class="gc-work__title"><?php the_title(); ?></h2>
								</div>
							</div>
						</a>
					</article>
					<?php
				}
				?>
			</div>

			<?php gridcraft_pagination(); ?>

		<?php else : ?>

			<div class="gc-no-results">
				<h2><?php esc_html_e( '没有找到相关内容', 'gridcraft-portfolio' ); ?></h2>
				<p><?php esc_html_e( '换个关键词试试，或直接浏览作品集。', 'gridcraft-portfolio' ); ?></p>
				<?php get_search_form(); ?>
			</div>

		<?php endif; ?>

	</div>
</div>

<?php
get_footer();

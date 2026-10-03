<?php
/**
 * 单个客户详情模板（single-client.php）。
 *
 * 客户页以「档案 + 作品清单」的形式呈现：
 * 顶部是 Logo、官网与行业信息，下方是关联到该客户的作品网格。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();

while ( have_posts() ) :
	the_post();

	$gc_client_id    = (int) get_the_ID();
	$gc_website      = gridcraft_get_work_meta( '_gridcraft_website', $gc_client_id );
	$gc_industry     = gridcraft_get_work_meta( '_gridcraft_industry', $gc_client_id );
	$gc_works_query  = new WP_Query(
		array(
			'post_type'           => 'portfolio',
			'post_status'         => 'publish',
			'posts_per_page'      => 12,
			'orderby'             => 'menu_order',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_query'          => array(
				array(
					'key'   => '_gridcraft_client',
					'value' => $gc_client_id,
				),
			),
		)
	);
	?>

	<article id="client-<?php echo esc_attr( (string) $gc_client_id ); ?>" <?php post_class( 'gc-single gc-single--client' ); ?>>
		<div class="gc-container">

			<header class="gc-single__header">
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="gc-single__gallery">
						<?php
						the_post_thumbnail(
							'gridcraft-card',
							array(
								'decoding' => 'async',
								'alt'      => the_title_attribute( array( 'echo' => false ) ),
							)
						);
						?>
					</figure>
				<?php endif; ?>

				<h1 class="gc-single__title"><?php echo esc_html( get_the_title() ); ?></h1>

				<?php if ( $gc_industry || $gc_website ) : ?>
					<p class="gc-page-header__description">
						<?php if ( $gc_industry ) : ?>
							<span class="gc-client__industry"><?php echo esc_html( $gc_industry ); ?></span>
						<?php endif; ?>

						<?php if ( $gc_website ) : ?>
							<a class="gc-client__url" href="<?php echo esc_url( $gc_website ); ?>" rel="noopener noreferrer nofollow" target="_blank">
								<?php
								$gc_host = wp_parse_url( $gc_website, PHP_URL_HOST );

								echo esc_html( $gc_host ? $gc_host : $gc_website );
								?>
							</a>
						<?php endif; ?>
					</p>
				<?php endif; ?>

				<?php if ( has_excerpt() ) : ?>
					<p class="gc-page-header__description"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</header>

			<div class="gc-single__content">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before'      => '<nav class="gc-pagination">' . esc_html__( '页面：', 'gridcraft-portfolio' ),
						'after'       => '</nav>',
						'link_before' => '<span class="page-numbers">',
						'link_after'  => '</span>',
					)
				);
				?>
			</div>

			<?php if ( $gc_works_query->have_posts() ) : ?>
				<section class="gc-section gc-section--soft">
					<div class="gc-section__header">
						<h2 class="gc-section__title"><?php esc_html_e( '该客户的作品', 'gridcraft-portfolio' ); ?></h2>
					</div>

					<?php
					gridcraft_work_grid( $gc_works_query );

					wp_reset_postdata();
					?>
				</section>
			<?php endif; ?>

		</div>
	</article>

	<?php
endwhile;

get_footer();
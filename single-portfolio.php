<?php
/**
 * 作品详情页模板（single-portfolio.php）。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();

while ( have_posts() ) :
	the_post();

	$gc_id       = (int) get_the_ID();
	$gc_link     = gridcraft_get_work_meta( '_gridcraft_link', $gc_id );
	$gc_stack    = gridcraft_get_work_stack( $gc_id );
	$gc_facts    = gridcraft_get_work_facts( $gc_id );
	$gc_previous = get_previous_post();
	$gc_next     = get_next_post();
	?>

	<article id="work-<?php echo esc_attr( (string) $gc_id ); ?>" <?php post_class( 'gc-single' ); ?>>
		<div class="gc-container">

			<header class="gc-single__header">
				<?php
				$gc_cats = get_the_term_list( $gc_id, 'portfolio_category', '', '' );

				if ( $gc_cats && ! is_wp_error( $gc_cats ) ) {
					echo '<div class="gc-single__cats">' . wp_kses_post( $gc_cats ) . '</div>';
				}
				?>

				<h1 class="gc-single__title"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="gc-page-header__description"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</header>

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

			<?php if ( gridcraft_get_option( 'portfolio_meta' ) && ( ! empty( $gc_facts ) || '' !== $gc_link ) ) : ?>
				<aside class="gc-single__facts" aria-label="<?php esc_attr_e( '项目信息', 'gridcraft-portfolio' ); ?>">
					<h2 class="gc-single__facts-title"><?php esc_html_e( '项目信息', 'gridcraft-portfolio' ); ?></h2>

					<?php if ( ! empty( $gc_facts ) ) : ?>
						<dl>
							<?php foreach ( $gc_facts as $gc_fact ) : ?>
								<dt><?php echo esc_html( $gc_fact['label'] ); ?></dt>
								<dd><?php echo esc_html( $gc_fact['value'] ); ?></dd>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>

					<?php if ( '' !== $gc_link ) : ?>
						<p>
							<a class="gc-button gc-button--small" href="<?php echo esc_url( $gc_link ); ?>" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( '访问项目', 'gridcraft-portfolio' ); ?>
								<?php echo gridcraft_inline_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 固定 SVG 表。 ?>
							</a>
						</p>
					<?php endif; ?>
				</aside>
			<?php endif; ?>

			<div class="gc-single__content">
				<?php
				the_content(
					sprintf(
						/* translators: %s：作品名称。 */
						esc_html__( '继续查看「%s」', 'gridcraft-portfolio' ),
						esc_html( get_the_title() )
					)
				);

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

			<?php if ( ! empty( $gc_stack ) ) : ?>
				<div class="gc-section gc-section--tight">
					<ul class="gc-tags">
						<?php foreach ( $gc_stack as $gc_item ) : ?>
							<li class="gc-tag"><?php echo esc_html( $gc_item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( $gc_previous || $gc_next ) : ?>
				<nav class="gc-single__nav" aria-label="<?php esc_attr_e( '作品导航', 'gridcraft-portfolio' ); ?>">
					<?php if ( $gc_previous ) : ?>
						<a class="gc-single__nav-link" href="<?php echo esc_url( (string) get_permalink( $gc_previous ) ); ?>" rel="prev">
							<span class="gc-single__nav-label"><?php esc_html_e( '上一个作品', 'gridcraft-portfolio' ); ?></span>
							<?php echo esc_html( get_the_title( $gc_previous ) ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $gc_next ) : ?>
						<a class="gc-single__nav-link" href="<?php echo esc_url( (string) get_permalink( $gc_next ) ); ?>" rel="next">
							<span class="gc-single__nav-label"><?php esc_html_e( '下一个作品', 'gridcraft-portfolio' ); ?></span>
							<?php echo esc_html( get_the_title( $gc_next ) ); ?>
						</a>
					<?php endif; ?>
				</nav>
			<?php endif; ?>

		</div>
	</article>

	<?php
	// 相关作品：同分类，排除当前作品。
	if ( gridcraft_get_option( 'portfolio_related' ) ) {
		$gc_related_args = array(
			'post_type'           => 'portfolio',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'post__not_in'        => array( $gc_id ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		$gc_term_ids = wp_get_post_terms( $gc_id, 'portfolio_category', array( 'fields' => 'ids' ) );

		if ( ! is_wp_error( $gc_term_ids ) && ! empty( $gc_term_ids ) ) {
			$gc_related_args['tax_query'] = array(
				array(
					'taxonomy' => 'portfolio_category',
					'field'    => 'term_id',
					'terms'    => $gc_term_ids,
				),
			);
		}

		$gc_related = new WP_Query( $gc_related_args );

		if ( $gc_related->have_posts() ) :
			?>
			<section class="gc-section gc-section--soft">
				<div class="gc-container">
					<div class="gc-section__header">
						<h2 class="gc-section__title"><?php esc_html_e( '相关作品', 'gridcraft-portfolio' ); ?></h2>
					</div>

					<?php gridcraft_work_grid( $gc_related ); ?>

					<p class="gc-section__more">
						<a class="gc-button gc-button--outline" href="<?php echo esc_url( (string) post_type_archive_link( 'portfolio' ) ); ?>">
							<?php esc_html_e( '查看全部作品', 'gridcraft-portfolio' ); ?>
						</a>
					</p>
				</div>
			</section>
			<?php
		endif;

		wp_reset_postdata();
	}

	if ( comments_open() || get_comments_number() ) {
		echo '<div class="gc-container">';
		comments_template();
		echo '</div>';
	}
	?>

	<?php
endwhile;

get_footer();

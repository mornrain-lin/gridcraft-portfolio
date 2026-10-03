<?php
/**
 * 单篇文章模板。
 *
 * 作品集主题里普通文章作为「创作笔记 / 博客」使用。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'gc-single' ); ?>>
		<div class="gc-container">
			<header class="gc-single__header">
				<h1 class="gc-single__title"><?php the_title(); ?></h1>

				<?php
				$gc_categories = get_the_category_list( ', ' );

				if ( $gc_categories ) {
					echo '<div class="gc-single__cats">' . wp_kses_post( $gc_categories ) . '</div>';
				}
				?>

				<p class="gc-page-header__description">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					·
					<?php echo esc_html( get_the_author() ); ?>
				</p>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="gc-single__gallery">
					<?php the_post_thumbnail( 'gridcraft-card', array( 'decoding' => 'async' ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="gc-single__content">
				<?php
				the_content(
					sprintf(
						/* translators: %s：文章标题。 */
						esc_html__( '继续阅读「%s」', 'gridcraft-portfolio' ),
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

			<?php
			$gc_tags = get_the_tag_list( '', '' );

			if ( $gc_tags && ! is_wp_error( $gc_tags ) ) {
				?>
				<div class="gc-section gc-section--tight">
					<ul class="gc-tags"><?php echo wp_kses_post( $gc_tags ); ?></ul>
				</div>
				<?php
			}

			$gc_previous = get_previous_post();
			$gc_next     = get_next_post();

			if ( $gc_previous || $gc_next ) :
				?>
				<nav class="gc-single__nav" aria-label="<?php esc_attr_e( '文章导航', 'gridcraft-portfolio' ); ?>">
					<?php if ( $gc_previous ) : ?>
						<a class="gc-single__nav-link" href="<?php echo esc_url( (string) get_permalink( $gc_previous ) ); ?>" rel="prev">
							<span class="gc-single__nav-label"><?php esc_html_e( '上一篇', 'gridcraft-portfolio' ); ?></span>
							<?php echo esc_html( get_the_title( $gc_previous ) ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $gc_next ) : ?>
						<a class="gc-single__nav-link" href="<?php echo esc_url( (string) get_permalink( $gc_next ) ); ?>" rel="next">
							<span class="gc-single__nav-label"><?php esc_html_e( '下一篇', 'gridcraft-portfolio' ); ?></span>
							<?php echo esc_html( get_the_title( $gc_next ) ); ?>
						</a>
					<?php endif; ?>
				</nav>
				<?php
			endif;
			?>
		</div>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="gc-container">';
		comments_template();
		echo '</div>';
	}
	?>

	<?php
endwhile;

get_footer();

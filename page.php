<?php
/**
 * 单页面模板。
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
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="gc-single__gallery">
					<?php the_post_thumbnail( 'gridcraft-card', array( 'decoding' => 'async' ) ); ?>
				</figure>
			<?php endif; ?>

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

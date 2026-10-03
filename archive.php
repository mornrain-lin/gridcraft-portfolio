<?php
/**
 * 归档模板：分类、标签、日期、作者。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();
?>

<div class="gc-container">
	<div class="gc-section gc-section--tight">

		<header class="gc-page-header">
			<h1 class="gc-page-header__title"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>

			<?php
			$gc_description = get_the_archive_description();

			if ( $gc_description ) {
				echo '<div class="gc-page-header__description">' . wp_kses_post( wpautop( $gc_description ) ) . '</div>';
			}
			?>
		</header>

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

	</div>
</div>

<?php
get_footer();

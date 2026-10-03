<?php
/**
 * 主模板文件（fallback template）。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();
?>

<div class="gc-container">
	<div class="gc-section gc-section--tight">

		<?php if ( have_posts() ) : ?>

			<header class="gc-page-header">
				<?php
				if ( is_home() && ! is_front_page() ) {
					$gc_page_for_posts = (int) get_option( 'page_for_posts' );
					$gc_blog_title = $gc_page_for_posts ? (string) get_the_title( $gc_page_for_posts ) : esc_html__( '最新文章', 'gridcraft-portfolio' );

					echo '<h1 class="gc-page-header__title">' . esc_html( $gc_blog_title ) . '</h1>';
				} else {
					echo '<h1 class="gc-page-header__title">' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</h1>';
				}

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

		<?php else : ?>

			<div class="gc-no-results">
				<h2><?php esc_html_e( '暂时没有内容', 'gridcraft-portfolio' ); ?></h2>
				<p><?php esc_html_e( '换个关键词，或从作品集开始浏览。', 'gridcraft-portfolio' ); ?></p>
				<p>
					<a class="gc-button" href="<?php echo esc_url( (string) post_type_archive_link( 'portfolio' ) ); ?>">
						<?php esc_html_e( '查看作品集', 'gridcraft-portfolio' ); ?>
					</a>
				</p>
			</div>

		<?php endif; ?>

	</div>
</div>

<?php
get_footer();

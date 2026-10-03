<?php
/**
 * 客户归档模板（archive-client.php）。
 *
 * 客户本身信息量少，因此采用「客户 + 其名下作品」的分组列表：
 * 每个客户一张卡片，卡片内列出关联作品缩略图。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();

$gc_client_query = new WP_Query(
	array(
		'post_type'           => 'client',
		'post_status'         => 'publish',
		'posts_per_page'      => 24,
		'orderby'             => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
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

		<?php if ( $gc_client_query->have_posts() ) : ?>

			<div class="gc-clients">
				<?php
				while ( $gc_client_query->have_posts() ) :
					$gc_client_query->the_post();

					$gc_client_id      = (int) get_the_ID();
					$gc_website        = gridcraft_get_work_meta( '_gridcraft_website', $gc_client_id );
					$gc_industry       = gridcraft_get_work_meta( '_gridcraft_industry', $gc_client_id );
					$gc_client_works   = get_posts(
						array(
							'post_type'      => 'portfolio',
							'post_status'    => 'publish',
							'posts_per_page' => 8,
							'fields'         => 'ids',
							'no_found_rows'  => true,
							'meta_query'     => array(
								array(
									'key'   => '_gridcraft_client',
									'value' => $gc_client_id,
								),
							),
						)
					);
					?>
					<article class="gc-client">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="gc-client__logo">
								<?php the_post_thumbnail( 'gridcraft-tile', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
							</div>
						<?php endif; ?>

						<h2 class="gc-client__name"><?php echo esc_html( get_the_title() ); ?></h2>

						<?php if ( $gc_website ) : ?>
							<a class="gc-client__url" href="<?php echo esc_url( $gc_website ); ?>" rel="noopener noreferrer nofollow" target="_blank">
								<?php
								$gc_host = wp_parse_url( $gc_website, PHP_URL_HOST );

								echo esc_html( $gc_host ? $gc_host : $gc_website );
								?>
							</a>
						<?php elseif ( $gc_industry ) : ?>
							<span class="gc-client__url"><?php echo esc_html( $gc_industry ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $gc_client_works ) ) : ?>
							<ul class="gc-client__works">
								<?php foreach ( $gc_client_works as $gc_work_id ) : ?>
									<?php
									$gc_work_permalink = get_permalink( (int) $gc_work_id );

									if ( ! $gc_work_permalink ) {
										continue;
									}
									?>
									<li>
										<a href="<?php echo esc_url( $gc_work_permalink ); ?>">
											<?php echo esc_html( get_the_title( (int) $gc_work_id ) ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php gridcraft_pagination(); ?>

		<?php else : ?>

			<div class="gc-no-results">
				<h2><?php esc_html_e( '还没有客户', 'gridcraft-portfolio' ); ?></h2>
				<p><?php esc_html_e( '在「作品 → 客户」中新建后，这里会列出全部合作方。', 'gridcraft-portfolio' ); ?></p>
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
wp_reset_postdata();

get_footer();
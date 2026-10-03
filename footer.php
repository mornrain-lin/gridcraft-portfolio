<?php
/**
 * Gridcraft Portfolio 站点页脚模板。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

?>
	</main><!-- #gc-content -->

	<footer id="colophon" class="gc-site-footer">
		<div class="gc-container">

			<?php gridcraft_footer_widgets(); ?>

			<div class="gc-site-info">
				<div class="gc-site-info__inner">
					<p>
						<?php
						printf(
							/* translators: 1：年份，2：站点名称。 */
							esc_html__( '© %1$s %2$s', 'gridcraft-portfolio' ),
							esc_html( gmdate( 'Y' ) ),
							esc_html( get_bloginfo( 'name', 'display' ) )
						);
						?>
					</p>

					<?php if ( has_nav_menu( 'footer' ) ) : ?>
						<nav aria-label="<?php esc_attr_e( '页脚导航', 'gridcraft-portfolio' ); ?>">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'footer',
									'container'      => false,
									'menu_class'     => 'gc-footer-menu',
									'depth'          => 1,
									'fallback_cb'    => false,
								)
							);
							?>
						</nav>
					<?php endif; ?>
				</div>
			</div>

		</div>
	</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>

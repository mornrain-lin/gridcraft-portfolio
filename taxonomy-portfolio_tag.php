<?php
/**
 * 作品标签归档模板（taxonomy-portfolio_tag.php）。
 *
 * 非层级标签（portfolio_tag）的归档页：顶部为标签云，
 * 方便在标签之间跳转，下面是当前标签下的作品网格。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();

$gc_term = get_queried_object();

if ( $gc_term instanceof WP_Term ) {
	$gc_tag_cloud = get_terms(
		array(
			'taxonomy'   => 'portfolio_tag',
			'hide_empty' => true,
			'number'     => 40,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
} else {
	$gc_tag_cloud = array();
}
?>

<div class="gc-container">
	<div class="gc-section gc-section--tight">

		<header class="gc-page-header">
			<p class="gc-page-header__eyebrow">
				<a href="<?php echo esc_url( (string) post_type_archive_link( 'portfolio' ) ); ?>">
					<?php esc_html_e( '作品集', 'gridcraft-portfolio' ); ?>
				</a>
			</p>

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

		<?php if ( ! is_wp_error( $gc_tag_cloud ) && count( $gc_tag_cloud ) > 1 ) : ?>
			<nav class="gc-tag-cloud" aria-label="<?php esc_attr_e( '作品标签', 'gridcraft-portfolio' ); ?>">
				<?php foreach ( $gc_tag_cloud as $gc_tag ) : ?>
					<?php
					$gc_tag_link = get_term_link( (int) $gc_tag->term_id, 'portfolio_tag' );

					if ( is_wp_error( $gc_tag_link ) ) {
						continue;
					}
					?>
					<a
						class="<?php echo esc_attr( 'gc-tag' . ( $gc_term instanceof WP_Term && (int) $gc_term->term_id === (int) $gc_tag->term_id ? ' is-active' : '' ) ); ?>"
						href="<?php echo esc_url( $gc_tag_link ); ?>"
						rel="tag"
					>
						<?php echo esc_html( $gc_tag->name ); ?>
						<span class="gc-tag__count"><?php echo esc_html( number_format_i18n( (int) $gc_tag->count ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>

			<?php gridcraft_work_grid( $GLOBALS['wp_query'] ); ?>

			<?php gridcraft_pagination(); ?>

		<?php else : ?>

			<div class="gc-no-results">
				<h2><?php esc_html_e( '这个标签下暂时没有作品', 'gridcraft-portfolio' ); ?></h2>
				<p><?php esc_html_e( '换个标签看看，或回到全部作品。', 'gridcraft-portfolio' ); ?></p>
			</div>

		<?php endif; ?>

	</div>
</div>

<?php
get_footer();
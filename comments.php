<?php
/**
 * 评论模板。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="gc-comments">

	<?php if ( have_comments() ) : ?>
		<h2 class="gc-comments__title">
			<?php
			$gc_comment_count = (int) get_comments_number();

			printf(
				/* translators: %s：评论数量。 */
				esc_html( _n( '%s 条评论', '%s 条评论', $gc_comment_count, 'gridcraft-portfolio' ) ),
				esc_html( number_format_i18n( $gc_comment_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 40,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => esc_html__( '上一页', 'gridcraft-portfolio' ),
				'next_text' => esc_html__( '下一页', 'gridcraft-portfolio' ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( '评论已关闭。', 'gridcraft-portfolio' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</div>

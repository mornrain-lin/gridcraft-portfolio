<?php
/**
 * Gridcraft Portfolio 作品筛选（AJAX）处理。
 *
 * 前端 filter.js 用原生 fetch 请求 admin-ajax.php，
 * 本文件负责 Nonce 校验、参数清洗与分页查询。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'gridcraft_ajax_filter_works' ) ) {
	/**
	 * 按分类 / 标签返回作品网格 HTML。
	 *
	 * @return void
	 */
	function gridcraft_ajax_filter_works() {
		// Nonce 校验。
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'gridcraft_filter' ) ) {
			wp_send_json_error(
				array( 'message' => esc_html__( '请求已失效，请刷新页面后重试。', 'gridcraft-portfolio' ) ),
				403
			);
		}

		$term_id = isset( $_POST['term'] ) ? absint( $_POST['term'] ) : 0;
		$paged   = isset( $_POST['paged'] ) ? max( 1, absint( $_POST['paged'] ) ) : 1;
		$per_page = (int) gridcraft_get_option( 'filter_per_page' );

		if ( $per_page < 1 || $per_page > 48 ) {
			$per_page = 12;
		}

		$args = array(
			'post_type'      => 'portfolio',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $paged,
			'no_found_rows'  => false,
		);

		if ( $term_id > 0 ) {
			$term = get_term( $term_id, 'portfolio_category' );

			if ( $term instanceof WP_Term ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'portfolio_category',
						'field'    => 'term_id',
						'terms'    => array( $term_id ),
					),
				);
			} else {
				wp_send_json_error(
					array( 'message' => esc_html__( '分类不存在。', 'gridcraft-portfolio' ) ),
					400
				);
			}
		}

		/**
		 * 过滤作品筛选的查询参数。
		 *
		 * @param array $args WP_Query 参数。
		 */
		$args = apply_filters( 'gridcraft_filter_query_args', $args );

		$query = new WP_Query( $args );

		ob_start();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				gridcraft_work_card( array( 'post_id' => (int) get_the_ID() ) );
			}
		}

		$html = ob_get_clean();

		wp_reset_postdata();

		$total = (int) $query->found_posts;
		$max_pages = (int) $query->max_num_pages;

		wp_send_json_success(
			array(
				'html'       => $html,
				'total'      => $total,
				'maxPages'   => $max_pages,
				'foundText'  => sprintf(
					/* translators: %d：结果数量。 */
					esc_html__( '共 %d 个作品', 'gridcraft-portfolio' ),
					$total
				),
			)
		);
	}
}
add_action( 'wp_ajax_gridcraft_filter_works', 'gridcraft_ajax_filter_works' );
add_action( 'wp_ajax_nopriv_gridcraft_filter_works', 'gridcraft_ajax_filter_works' );

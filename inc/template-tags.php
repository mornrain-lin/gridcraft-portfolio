<?php
/**
 * Gridcraft Portfolio 模板标签。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'gridcraft_get_work_meta' ) ) {
	/**
	 * 读取作品的单个 Meta 字段。
	 *
	 * @param string $key     字段名。
	 * @param int    $post_id 文章 ID，默认当前文章。
	 * @return string
	 */
	function gridcraft_get_work_meta( $key, $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		$value = get_post_meta( $post_id, $key, true );

		if ( ! is_scalar( $value ) ) {
			return '';
		}

		return (string) $value;
	}
}

if ( ! function_exists( 'gridcraft_get_work_stack' ) ) {
	/**
	 * 解析技术栈为数组（每行一项）。
	 *
	 * @param int $post_id 文章 ID。
	 * @return string[]
	 */
	function gridcraft_get_work_stack( $post_id = 0 ) {
		$raw = gridcraft_get_work_meta( '_gridcraft_stack', $post_id );

		if ( '' === $raw ) {
			return array();
		}

		$lines = preg_split( '/[\r\n,]+/', $raw );
		$items = array();

		foreach ( (array) $lines as $line ) {
			$line = trim( $line );

			if ( '' !== $line ) {
				$items[] = $line;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'gridcraft_get_work_roles' ) ) {
	/**
	 * 解析角色为数组。
	 *
	 * @param int $post_id 文章 ID。
	 * @return string[]
	 */
	function gridcraft_get_work_roles( $post_id = 0 ) {
		$raw = gridcraft_get_work_meta( '_gridcraft_role', $post_id );

		if ( '' === $raw ) {
			return array();
		}

		$parts = explode( ',', $raw );
		$items = array();

		foreach ( $parts as $part ) {
			$part = trim( $part );

			if ( '' !== $part ) {
				$items[] = $part;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'gridcraft_get_work_facts' ) ) {
	/**
	 * 汇总作品的「项目信息」条目。
	 *
	 * @param int $post_id 文章 ID。
	 * @return array<string,array{label:string,value:string}>
	 */
	function gridcraft_get_work_facts( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
		$facts   = array();

		$year = gridcraft_get_work_meta( '_gridcraft_year', $post_id );

		if ( '' !== $year ) {
			$facts[] = array(
				'label' => esc_html__( '年份', 'gridcraft-portfolio' ),
				'value' => $year,
			);
		}

		$client_id = (int) gridcraft_get_work_meta( '_gridcraft_client', $post_id );

		if ( $client_id > 0 ) {
			$client_title = get_the_title( $client_id );

			if ( $client_title ) {
				$facts[] = array(
					'label' => esc_html__( '客户', 'gridcraft-portfolio' ),
					'value' => $client_title,
				);
			}
		}

		$roles = gridcraft_get_work_roles( $post_id );

		if ( ! empty( $roles ) ) {
			$facts[] = array(
				'label' => esc_html__( '角色', 'gridcraft-portfolio' ),
				'value' => implode( ' · ', $roles ),
			);
		}

		$stack = gridcraft_get_work_stack( $post_id );

		if ( ! empty( $stack ) ) {
			$facts[] = array(
				'label' => esc_html__( '技术栈', 'gridcraft-portfolio' ),
				'value' => implode( ' / ', $stack ),
			);
		}

		$categories = get_the_term_list( $post_id, 'portfolio_category', '', ' / ' );

		if ( $categories && ! is_wp_error( $categories ) ) {
			$facts[] = array(
				'label' => esc_html__( '分类', 'gridcraft-portfolio' ),
				'value' => wp_strip_all_tags( $categories ),
			);
		}

		/**
		 * 过滤作品信息条目。
		 *
		 * @param array $facts   条目列表。
		 * @param int   $post_id 文章 ID。
		 */
		return apply_filters( 'gridcraft_work_facts', $facts, $post_id );
	}
}

if ( ! function_exists( 'gridcraft_work_card' ) ) {
	/**
	 * 输出作品网格中的一张卡片。
	 *
	 * @param array $args 额外参数：post_id / show_badge。
	 * @return void
	 */
	function gridcraft_work_card( $args = array() ) {
		$defaults = array(
			'post_id'    => 0,
			'show_badge' => true,
		);

		$args = wp_parse_args( $args, $defaults );

		$post_id = $args['post_id'] ? (int) $args['post_id'] : (int) get_the_ID();
		$post    = get_post( $post_id );

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$permalink = get_permalink( $post );

		if ( ! $permalink ) {
			return;
		}

		$term_slugs = wp_get_post_terms( $post_id, 'portfolio_category', array( 'fields' => 'slugs' ) );
		$term_slugs = is_wp_error( $term_slugs ) ? array() : $term_slugs;

		$term_names = wp_get_post_terms( $post_id, 'portfolio_category', array( 'fields' => 'names' ) );
		$term_names = is_wp_error( $term_names ) ? array() : $term_names;

		$featured = '1' === gridcraft_get_work_meta( '_gridcraft_featured', $post_id );
		$year     = gridcraft_get_work_meta( '_gridcraft_year', $post_id );

		?>
		<article
			id="work-<?php echo esc_attr( (string) $post_id ); ?>"
			class="gc-work"
			data-year="<?php echo esc_attr( $year ); ?>"
			<?php echo ! empty( $term_slugs ) ? ' data-terms="' . esc_attr( implode( ',', $term_slugs ) ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 片段已用 esc_attr 转义。 ?>
		>
			<a class="gc-work__link" href="<?php echo esc_url( $permalink ); ?>">

				<div class="gc-work__media">
					<?php if ( $args['show_badge'] && $featured ) : ?>
						<span class="gc-work__badge"><?php esc_html_e( '精选', 'gridcraft-portfolio' ); ?></span>
					<?php endif; ?>

					<?php if ( has_post_thumbnail( $post ) ) : ?>
						<?php
						echo get_the_post_thumbnail(
							$post,
							'gridcraft-card',
							array(
								'loading'  => 'lazy',
								'decoding' => 'async',
								'alt'      => the_title_attribute( array( 'echo' => false, 'post' => $post ) ),
							)
						);
						?>
					<?php else : ?>
						<div class="gc-work__placeholder"><?php esc_html_e( '暂无封面', 'gridcraft-portfolio' ); ?></div>
					<?php endif; ?>

					<div class="gc-work__overlay">
						<?php if ( ! empty( $term_names ) ) : ?>
							<span class="gc-work__cats"><?php echo esc_html( implode( ' · ', $term_names ) ); ?></span>
						<?php endif; ?>
						<h3 class="gc-work__title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
						<?php if ( '' !== $year ) : ?>
							<span class="gc-work__year"><?php echo esc_html( $year ); ?></span>
						<?php endif; ?>
					</div>
				</div>

			</a>
		</article>
		<?php
	}
}

if ( ! function_exists( 'gridcraft_work_grid' ) ) {
	/**
	 * 渲染作品网格。
	 *
	 * @param WP_Query $query   已执行的查询。
	 * @param array    $classes 额外的网格 class。
	 * @return void
	 */
	function gridcraft_work_grid( $query, $classes = array() ) {
		$columns = (string) gridcraft_get_option( 'grid_columns' );

		if ( ! in_array( $columns, array( '2', '3', '4' ), true ) ) {
			$columns = '3';
		}

		$class_names = array_merge( array( 'gc-grid' ), $classes );

		if ( '3' !== $columns ) {
			$class_names[] = 'gc-grid--' . $columns;
		}

		printf(
			'<div class="%1$s" data-grid role="feed" aria-busy="false">',
			esc_attr( implode( ' ', $class_names ) )
		);

		if ( $query instanceof WP_Query && $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				gridcraft_work_card( array( 'post_id' => (int) get_the_ID() ) );
			}

			// 本函数可能直接消费主查询，结束后必须复原全局 $post。
			wp_reset_postdata();
		}

		echo '</div>';
	}
}

if ( ! function_exists( 'gridcraft_filter_bar' ) ) {
	/**
	 * 输出作品分类筛选条。
	 *
	 * 分类数据以 JSON 输出在 data 属性中，供原生 fetch 筛选使用；
	 * 无JS 时分类归档链接依然可用（按钮是 <a> 而非 <button>）。
	 *
	 * @return void
	 */
	function gridcraft_filter_bar() {
		$terms = get_terms(
			array(
				'taxonomy'   => 'portfolio_category',
				'hide_empty' => true,
				'number'     => 20,
			)
		);

		if ( is_wp_error( $terms ) || count( $terms ) < 2 ) {
			return;
		}

		$queried = get_queried_object();
		$current = ( $queried instanceof WP_Term ) ? (int) $queried->term_id : 0;

		$total_all = wp_count_posts( 'portfolio' );
		$total_all = isset( $total_all->publish ) ? (int) $total_all->publish : 0;

		$items = array(
			array(
				'slug'  => 'all',
				'term'  => 0,
				'label' => esc_html__( '全部', 'gridcraft-portfolio' ),
				'count' => $total_all,
			),
		);

		foreach ( $terms as $term ) {
			$items[] = array(
				'slug'  => $term->slug,
				'term'  => (int) $term->term_id,
				'label' => $term->name,
				'count' => (int) $term->count,
			);
		}

		$archive_url = post_type_archive_link( 'portfolio' );

		?>
		<div
			class="gc-filter-bar"
			data-gridcraft-filter
			data-archive-url="<?php echo esc_url( (string) $archive_url ); ?>"
			data-items="<?php echo esc_attr( wp_json_encode( $items ) ); ?>"
		>
			<?php
			foreach ( $items as $index => $item ) :
				$is_active = ( 0 === $index && 0 === $current ) || ( $current === (int) $item['term'] && $current > 0 );
				$link      = ( $current === (int) $item['term'] && $current > 0 )
					? (string) $archive_url
					: ( $current > 0 && 0 === $index ? (string) $archive_url : (string) get_term_link( (int) $item['term'], 'portfolio_category' ) );
				?>
				<button
					type="button"
					class="<?php echo esc_attr( 'gc-filter' . ( $is_active ? ' is-active' : '' ) ); ?>"
					aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
					data-filter="<?php echo esc_attr( $item['slug'] ); ?>"
					data-term="<?php echo esc_attr( (string) $item['term'] ); ?>"
					data-count="<?php echo esc_attr( (string) $item['count'] ); ?>"
					<?php echo $link ? ' data-href="' . esc_url( $link ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 片段已用 esc_url 转义。 ?>
				>
					<?php echo esc_html( $item['label'] ); ?>
					<span class="gc-filter__count"><?php echo esc_html( number_format_i18n( (int) $item['count'] ) ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>
		<p class="gc-filter-status" data-gridcraft-status aria-live="polite"></p>
		<?php
	}
}

if ( ! function_exists( 'gridcraft_service_card' ) ) {
	/**
	 * 输出一张服务卡片。
	 *
	 * @param array $service 服务数据：title / text / icon / price。
	 * @return void
	 */
	function gridcraft_service_card( $service ) {
		$title = isset( $service['title'] ) ? (string) $service['title'] : '';
		$text  = isset( $service['text'] ) ? (string) $service['text'] : '';
		$icon  = isset( $service['icon'] ) ? (string) $service['icon'] : 'spark';
		$price = isset( $service['price'] ) ? (string) $service['price'] : '';

		if ( '' === $title ) {
			return;
		}

		echo '<article class="gc-service">';
		echo '<span class="gc-service__icon" aria-hidden="true">' . gridcraft_inline_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 内部为固定 SVG 表。
		echo '</span>';
		printf( '<h3 class="gc-service__title">%s</h3>', esc_html( $title ) );

		if ( '' !== $text ) {
			printf( '<p class="gc-service__text">%s</p>', esc_html( $text ) );
		}

		if ( '' !== $price ) {
			printf( '<span class="gc-service__price">%s</span>', esc_html( $price ) );
		}

		echo '</article>';
	}
}

if ( ! function_exists( 'gridcraft_inline_icon' ) ) {
	/**
	 * 内联 SVG 图标表。
	 *
	 * 只提供主题自身用到的少量图标，避免引入图标字体。
	 *
	 * @param string $name 图标名。
	 * @return string SVG HTML（已转义）。
	 */
	function gridcraft_inline_icon( $name ) {
		$icons = array(
			'spark'   => '<path d="M8 1.2 9.6 6.4 14.8 8l-5.2 1.6L8 14.8 6.4 9.6 1.2 8l5.2-1.6L8 1.2Z"/>',
			'palette' => '<path d="M8 1.4a6.6 6.6 0 0 0 0 13.2c.9 0 1.4-.6 1.4-1.3 0-.4-.2-.7-.4-1-.2-.3-.4-.6-.4-1 0-.6.5-1.1 1.2-1.1h1.3c1.9 0 3.5-1.6 3.5-3.6 0-2.7-2.8-5.2-6.6-5.2Zm-2.3 6.3a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8Zm1.9-2.4a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8Zm3 0a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8Zm1.9 2.4a.9.9 0 1 1 0-1.8.9.9 0 0 1 0 1.8Z"/>',
			'code'    => '<path d="M5.6 3.3 1.5 7.4l4.1 4.1 1-1L3.5 7.4l3.1-3.1-1-1Zm4.8 0-1 1 3.1 3.1-3.1 3.1 1 1 4.1-4.1L10.4 3.3Z"/>',
			'camera'  => '<path d="M6.2 2.2h3.6l.9 1.4h2.1c.6 0 1 .4 1 1v6.6c0 .6-.4 1-1 1H3.2c-.6 0-1-.4-1-1V4.6c0-.6.4-1 1-1h2.1l.9-1.4Zm1.8 3.3a2.7 2.7 0 1 0 0 5.4 2.7 2.7 0 0 0 0-5.4Z"/>',
			'chart'   => '<path d="M2.2 13.1V6.6h2.4v6.5H2.2Zm4.6 0V2.9h2.4v10.2H6.8Zm4.6 0V8.4h2.4v4.7h-2.4Z"/>',
			'clock'   => '<path d="M8 1.8a6.2 6.2 0 1 0 0 12.4A6.2 6.2 0 0 0 8 1.8Zm.8 6.4V3.6H7.2v4.6h4v1.6H7.2Z"/>',
			'arrow'   => '<path d="M8.9 3.3 7.8 4.4l3.1 3.1H2.4v1.6h8.5l-3.1 3.1 1.1 1.1 4.8-4.8-4.8-4.8Z"/>',
			'close'   => '<path d="M12.1 3.5 8.5 7.1l-3.6-3.6-1 1 3.6 3.6-3.6 3.6 1 1 3.6-3.6 3.6 3.6 1-1L9.5 8.1l3.6-3.6-1-1Z"/>',
			'left'    => '<path d="M9.9 3.3 8.8 4.4 11.9 7.5H2.4v1.6h9.5L8.8 12.2l1.1 1.1 4.8-4.8-4.8-4.8Z"/>',
			'zoom'    => '<path d="M6.8 1.9a4.9 4.9 0 1 0 2.9 8.8l2.9 2.9 1.1-1.1-2.9-2.9A4.9 4.9 0 0 0 6.8 1.9Zm0 1.6a3.3 3.3 0 1 1 0 6.6 3.3 3.3 0 0 1 0-6.6Z"/><path d="M5.5 5.2h2.6v1.2H5.5z"/>',
		);

		$name = (string) $name;

		if ( ! isset( $icons[ $name ] ) ) {
			$name = 'spark';
		}

		return sprintf(
			'<svg width="20" height="20" viewBox="0 0 16 16" focusable="false" aria-hidden="true">%s</svg>',
			$icons[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 固定字面量表，无外部输入。
		);
	}
}

if ( ! function_exists( 'gridcraft_get_services' ) ) {
	/**
	 * 返回默认服务列表。
	 *
	 * @return array<int,array<string,string>>
	 */
	function gridcraft_get_services() {
		$services = array(
			array(
				'icon'  => 'palette',
				'title' => esc_html__( '品牌与视觉设计', 'gridcraft-portfolio' ),
				'text'  => esc_html__( '从logo 到完整视觉系统，交付可落地的设计规范与源文件。', 'gridcraft-portfolio' ),
				'price' => esc_html__( '起价 ¥20,000', 'gridcraft-portfolio' ),
			),
			array(
				'icon'  => 'code',
				'title' => esc_html__( '网站与前端开发', 'gridcraft-portfolio' ),
				'text'  => esc_html__( '响应式网站与Web 应用，强调可访问性与加载性能。', 'gridcraft-portfolio' ),
				'price' => esc_html__( '起价 ¥35,000', 'gridcraft-portfolio' ),
			),
			array(
				'icon'  => 'camera',
				'title' => esc_html__( '摄影与影像', 'gridcraft-portfolio' ),
				'text'  => esc_html__( '商业摄影、产品图与动态影像，提供现场与后期全流程。', 'gridcraft-portfolio' ),
				'price' => esc_html__( '起价 ¥8,000', 'gridcraft-portfolio' ),
			),
			array(
				'icon'  => 'chart',
				'title' => esc_html__( '增长与优化', 'gridcraft-portfolio' ),
				'text'  => esc_html__( '转化路径优化、AB 测试与数据看板，让好设计产生实际收益。', 'gridcraft-portfolio' ),
				'price' => esc_html__( '按项目计费', 'gridcraft-portfolio' ),
			),
		);

		/**
		 * 过滤首页服务列表。
		 *
		 * @param array $services 服务卡片数据。
		 */
		return apply_filters( 'gridcraft_services', $services );
	}
}

if ( ! function_exists( 'gridcraft_get_stats' ) ) {
	/**
	 * 统计实际作品与客户数量，供首页统计条使用。
	 *
	 * @return array<int,array<string,string>>
	 */
	function gridcraft_get_stats() {
		$works_count = wp_count_posts( 'portfolio' );
		$works_count = isset( $works_count->publish ) ? (int) $works_count->publish : 0;

		$clients_count = wp_count_posts( 'client' );
		$clients_count = isset( $clients_count->publish ) ? (int) $clients_count->publish : 0;

		$years = array();

		$all_works = get_posts(
			array(
				'post_type'      => 'portfolio',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);

		foreach ( (array) $all_works as $work_id ) {
			$year = gridcraft_get_work_meta( '_gridcraft_year', (int) $work_id );

			if ( '' !== $year ) {
				$years[ (string) $year ] = true;
			}
		}

		$stats = array(
			array(
				'number' => number_format_i18n( $works_count ),
				/* translators: %s：作品数量。 */
				'label'  => sprintf( esc_html__( '个已交付作品', 'gridcraft-portfolio' ), number_format_i18n( $works_count ) ),
			),
			array(
				'number' => number_format_i18n( $clients_count ),
				/* translators: %s：客户数量。 */
				'label'  => sprintf( esc_html__( '个长期合作客户', 'gridcraft-portfolio' ), number_format_i18n( $clients_count ) ),
			),
			array(
				'number' => (string) count( $years ),
				/* translators: %s：年份跨度。 */
				'label'  => sprintf( esc_html__( '年创作跨度', 'gridcraft-portfolio' ), number_format_i18n( count( $years ) ) ),
			),
		);

		return $stats;
	}
}

if ( ! function_exists( 'gridcraft_site_branding' ) ) {
	/**
	 * 输出站点品牌区。
	 *
	 * @return void
	 */
	function gridcraft_site_branding() {
		if ( has_custom_logo() ) {
			echo '<a class="gc-brand" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
			the_custom_logo();
			echo '</a>';
			return;
		}

		$title_tag = ( is_front_page() && is_home() ) ? 'h1' : 'span';

		printf(
			'<a class="gc-brand" href="%1$s" rel="home"><%2$s class="gc-brand__title">%3$s</%2$s></a>',
			esc_url( home_url( '/' ) ),
			esc_attr( $title_tag ),
			esc_html( get_bloginfo( 'name', 'display' ) )
		);
	}
}

if ( ! function_exists( 'gridcraft_primary_nav' ) ) {
	/**
	 * 输出主导航。
	 *
	 * @return void
	 */
	function gridcraft_primary_nav() {
		?>
		<button type="button" class="gc-nav-toggle" data-gc-nav-toggle aria-expanded="false" aria-controls="gc-primary-nav">
			<span class="gc-nav-toggle__bars" aria-hidden="true"></span>
			<span><?php esc_html_e( '菜单', 'gridcraft-portfolio' ); ?></span>
		</button>
		<nav id="gc-primary-nav" class="gc-primary-nav" aria-label="<?php esc_attr_e( '主导航', 'gridcraft-portfolio' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'gc-primary-menu',
						'depth'          => 2,
						'fallback_cb'    => false,
					)
				);
			} else {
				echo '<ul class="gc-primary-menu">';
				printf(
					'<li><a href="%1$s">%2$s</a></li>',
					esc_url( (string) post_type_archive_link( 'portfolio' ) ),
					esc_html__( '作品', 'gridcraft-portfolio' )
				);
				printf(
					'<li><a href="%1$s">%2$s</a></li>',
					esc_url( home_url( '/' ) ),
					esc_html__( '首页', 'gridcraft-portfolio' )
				);
				echo '</ul>';
			}
			?>
		</nav>
		<?php
	}
}

if ( ! function_exists( 'gridcraft_pagination' ) ) {
	/**
	 * 输出分页标记。
	 *
	 * @param WP_Query|null $query 查询对象，默认主查询。
	 * @return void
	 */
	function gridcraft_pagination( $query = null ) {
		$target = $query instanceof WP_Query ? $query : $GLOBALS['wp_query'];

		if ( ! $target instanceof WP_Query || $target->max_num_pages < 2 ) {
			return;
		}

		$links = paginate_links(
			array(
				'total'     => $target->max_num_pages,
				'current'   => max( 1, (int) get_query_var( 'paged' ) ),
				'type'      => 'array',
				'mid_size'  => 1,
				'prev_text' => esc_html__( '上一页', 'gridcraft-portfolio' ),
				'next_text' => esc_html__( '下一页', 'gridcraft-portfolio' ),
			)
		);

		if ( ! $links ) {
			return;
		}

		echo '<nav class="gc-pagination" aria-label="' . esc_attr__( '分页导航', 'gridcraft-portfolio' ) . '"><div class="nav-links">';

		foreach ( $links as $link ) {
			echo wp_kses_post( $link );
		}

		echo '</div></nav>';
	}
}

if ( ! function_exists( 'gridcraft_lightbox_markup' ) ) {
	/**
	 * 输出 Lightbox 容器（页面底部）。
	 *
	 * @return void
	 */
	function gridcraft_lightbox_markup() {
		?>
		<div class="gc-lightbox" data-gc-lightbox role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( '图片查看器', 'gridcraft-portfolio' ); ?>" hidden>
			<button type="button" class="gc-lightbox__close" data-gc-lightbox-close>
				<?php echo gridcraft_inline_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 固定 SVG 表。 ?>
				<span class="gc-screen-reader-text"><?php esc_html_e( '关闭', 'gridcraft-portfolio' ); ?></span>
			</button>
			<button type="button" class="gc-lightbox__prev" data-gc-lightbox-prev>
				<?php echo gridcraft_inline_icon( 'left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 固定 SVG 表。 ?>
				<span class="gc-screen-reader-text"><?php esc_html_e( '上一张', 'gridcraft-portfolio' ); ?></span>
			</button>
			<button type="button" class="gc-lightbox__next" data-gc-lightbox-next>
				<?php echo gridcraft_inline_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 固定 SVG 表。 ?>
				<span class="gc-screen-reader-text"><?php esc_html_e( '下一张', 'gridcraft-portfolio' ); ?></span>
			</button>
			<figure class="gc-lightbox__figure">
				<img class="gc-lightbox__image" data-gc-lightbox-image src="" alt="">
				<figcaption class="gc-lightbox__caption" data-gc-lightbox-caption></figcaption>
			</figure>
		</div>
		<?php
	}
}
add_action( 'wp_footer', 'gridcraft_lightbox_markup', 20 );

if ( ! function_exists( 'gridcraft_content_image_lightbox' ) ) {
	/**
	 * 给正文中的大图加上 Lightbox 触发标记。
	 *
	 * @param string $content 文章内容。
	 * @return string
	 */
	function gridcraft_content_image_lightbox( $content ) {
		if ( is_admin() || ! is_singular( 'portfolio' ) ) {
			return $content;
		}

		// 排除已带链接的图片，避免嵌套交互。
		$content = preg_replace(
			'#<a\b[^>]*>\s*(<img\b[^>]*>)\s*</a>#i',
			'$1',
			$content
		);

		$content = preg_replace_callback(
			'#<img\b([^>]*?)(?<!/)>#i',
			'gridcraft_add_lightbox_attributes',
			$content
		);

		return (string) $content;
	}
}
add_filter( 'the_content', 'gridcraft_content_image_lightbox', 30 );

if ( ! function_exists( 'gridcraft_add_lightbox_attributes' ) ) {
	/**
	 * 给 img 标签补上 Lightbox 所需属性。
	 *
	 * @param array $matches 正则匹配。
	 * @return string
	 */
	function gridcraft_add_lightbox_attributes( $matches ) {
		$attrs = isset( $matches[1] ) ? $matches[1] : '';

		if ( false !== strpos( $attrs, 'data-gc-lightbox-item' ) ) {
			return $matches[0];
		}

		// 可聚焦 + role="button"，让键盘用户也能打开 Lightbox。
		return '<img' . $attrs . ' data-gc-lightbox-item="1" tabindex="0" role="button">';
	}
}

if ( ! function_exists( 'gridcraft_sidebar' ) ) {
	/**
	 * 输出指定 Widget 区域。
	 *
	 * @param string $sidebar_id Widget 区域 ID。
	 * @return void
	 */
	function gridcraft_sidebar( $sidebar_id = 'sidebar-1' ) {
		if ( ! is_active_sidebar( $sidebar_id ) ) {
			return;
		}

		echo '<aside class="gc-sidebar" aria-label="' . esc_attr__( '侧栏', 'gridcraft-portfolio' ) . '">';
		dynamic_sidebar( $sidebar_id );
		echo '</aside>';
	}
}

if ( ! function_exists( 'gridcraft_footer_widgets' ) ) {
	/**
	 * 输出页脚 Widget。
	 *
	 * @return void
	 */
	function gridcraft_footer_widgets() {
		if ( ! is_active_sidebar( 'footer-1' ) && ! is_active_sidebar( 'footer-2' ) && ! is_active_sidebar( 'footer-3' ) ) {
			return;
		}

		echo '<div class="gc-footer-widgets">';

		for ( $i = 1; $i <= 3; $i++ ) {
			$id = 'footer-' . $i;

			if ( ! is_active_sidebar( $id ) ) {
				continue;
			}

			echo '<div class="gc-footer-widgets__col">';
			dynamic_sidebar( $id );
			echo '</div>';
		}

		echo '</div>';
	}
}

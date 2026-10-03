<?php
/**
 * Gridcraft Portfolio Meta 框。
 *
 * 作品的字段：项目链接、客户、年份、角色、技术栈、精选标记。
 * 客户的字段：官网地址、所属行业。
 *
 * 所有字段都做 nonce 校验 + 权限校验 + 类型清洗。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'gridcraft_work_meta_fields' ) ) {
	/**
	 * 作品 Meta 字段定义表。
	 *
	 * @return array<string,array<string,mixed>>
	 */
	function gridcraft_work_meta_fields() {
		return array(
			'_gridcraft_link'   => array(
				'label'    => esc_html__( '项目链接', 'gridcraft-portfolio' ),
				'type'     => 'url',
				'help'     => esc_html__( '填写真实的作品访问地址，会显示在作品详情页的「访问项目」按钮上。', 'gridcraft-portfolio' ),
				'required' => false,
			),
			'_gridcraft_client' => array(
				'label'    => esc_html__( '客户', 'gridcraft-portfolio' ),
				'type'     => 'client',
				'help'     => esc_html__( '关联到「客户」类型中已发布的条目。', 'gridcraft-portfolio' ),
				'required' => false,
			),
			'_gridcraft_year'   => array(
				'label'    => esc_html__( '年份', 'gridcraft-portfolio' ),
				'type'     => 'text',
				'help'     => esc_html__( '例如 2026。', 'gridcraft-portfolio' ),
				'required' => false,
			),
			'_gridcraft_role'   => array(
				'label'    => esc_html__( '承担角色', 'gridcraft-portfolio' ),
				'type'     => 'text',
				'help'     => esc_html__( '多个角色用英文逗号分隔，例如：设计、前端开发。', 'gridcraft-portfolio' ),
				'required' => false,
			),
			'_gridcraft_stack'  => array(
				'label'    => esc_html__( '技术栈', 'gridcraft-portfolio' ),
				'type'     => 'textarea',
				'help'     => esc_html__( '每行一个，例如：WordPress / PHP / GSAP。', 'gridcraft-portfolio' ),
				'required' => false,
			),
		);
	}
}

if ( ! function_exists( 'gridcraft_client_meta_fields' ) ) {
	/**
	 * 客户 Meta 字段定义表。
	 *
	 * @return array<string,array<string,mixed>>
	 */
	function gridcraft_client_meta_fields() {
		return array(
			'_gridcraft_website' => array(
				'label'    => esc_html__( '官网地址', 'gridcraft-portfolio' ),
				'type'     => 'url',
				'help'     => esc_html__( '客户官网或社交主页。', 'gridcraft-portfolio' ),
				'required' => false,
			),
			'_gridcraft_industry' => array(
				'label'    => esc_html__( '所属行业', 'gridcraft-portfolio' ),
				'type'     => 'text',
				'help'     => esc_html__( '例如：金融科技。', 'gridcraft-portfolio' ),
				'required' => false,
			),
		);
	}
}

if ( ! function_exists( 'gridcraft_add_meta_boxes' ) ) {
	/**
	 * 注册 Meta 框。
	 *
	 * @return void
	 */
	function gridcraft_add_meta_boxes() {
		add_meta_box(
			'gridcraft-work-details',
			esc_html__( '作品信息', 'gridcraft-portfolio' ),
			'gridcraft_render_work_meta_box',
			'portfolio',
			'normal',
			'high'
		);

		add_meta_box(
			'gridcraft-client-details',
			esc_html__( '客户信息', 'gridcraft-portfolio' ),
			'gridcraft_render_client_meta_box',
			'client',
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'gridcraft_add_meta_boxes' );

if ( ! function_exists( 'gridcraft_render_work_meta_box' ) ) {
	/**
	 * 渲染作品的 Meta 框。
	 *
	 * @param WP_Post $post 当前文章。
	 * @return void
	 */
	function gridcraft_render_work_meta_box( $post ) {
		wp_nonce_field( 'gridcraft_save_work_meta', 'gridcraft_work_meta_nonce' );

		echo '<div class="gridcraft-meta-grid">';

		foreach ( gridcraft_work_meta_fields() as $key => $field ) {
			gridcraft_render_meta_field( $key, $field, get_post_meta( $post->ID, $key, true ) );
		}

		// 精选作品开关。
		$featured = get_post_meta( $post->ID, '_gridcraft_featured', true );

		echo '<p class="gridcraft-meta-field gridcraft-meta-field--checkbox">';
		echo '<label for="gridcraft-featured">';
		echo '<input type="checkbox" id="gridcraft-featured" name="gridcraft_meta[_gridcraft_featured]" value="1"' . checked( '1', $featured, false ) . '>';
		echo ' ' . esc_html__( '标记为精选作品（显示在首页精选区块）', 'gridcraft-portfolio' );
		echo '</label></p>';

		echo '</div>';
	}
}

if ( ! function_exists( 'gridcraft_render_client_meta_box' ) ) {
	/**
	 * 渲染客户的 Meta 框。
	 *
	 * @param WP_Post $post 当前文章。
	 * @return void
	 */
	function gridcraft_render_client_meta_box( $post ) {
		wp_nonce_field( 'gridcraft_save_client_meta', 'gridcraft_client_meta_nonce' );

		echo '<div class="gridcraft-meta-grid">';

		foreach ( gridcraft_client_meta_fields() as $key => $field ) {
			gridcraft_render_meta_field( $key, $field, get_post_meta( $post->ID, $key, true ) );
		}

		echo '</div>';
	}
}

if ( ! function_exists( 'gridcraft_render_meta_field' ) ) {
	/**
	 * 渲染单个 Meta 字段。
	 *
	 * @param string $key   字段名（同时作为 name）。
	 * @param array  $field 字段定义。
	 * @param mixed  $value 当前值。
	 * @return void
	 */
	function gridcraft_render_meta_field( $key, $field, $value ) {
		$input_id = 'gridcraft-field-' . sanitize_html_class( str_replace( '_', '-', $key ) );

		echo '<p class="gridcraft-meta-field">';
		printf(
			'<label for="%1$s"><strong>%2$s</strong></label>',
			esc_attr( $input_id ),
			esc_html( $field['label'] )
		);

		switch ( $field['type'] ) {
			case 'textarea':
				printf(
					'<textarea id="%1$s" name="gridcraft_meta[%2$s]" rows="5" class="widefat">%3$s</textarea>',
					esc_attr( $input_id ),
					esc_attr( $key ),
					esc_textarea( (string) $value )
				);
				break;

			case 'client':
				gridcraft_render_client_select( $input_id, $key, (int) $value );
				break;

			default:
				$input_type = ( 'url' === $field['type'] ) ? 'url' : 'text';
				printf(
					'<input type="%1$s" id="%2$s" name="gridcraft_meta[%3$s]" value="%4$s" class="widefat">',
					esc_attr( $input_type ),
					esc_attr( $input_id ),
					esc_attr( $key ),
					esc_attr( (string) $value )
				);
				break;
		}

		if ( ! empty( $field['help'] ) ) {
			printf( '<span class="description">%s</span>', esc_html( $field['help'] ) );
		}

		echo '</p>';
	}
}

if ( ! function_exists( 'gridcraft_render_client_select' ) ) {
	/**
	 * 渲染客户下拉选择。
	 *
	 * @param string $input_id 元素 ID。
	 * @param string $key      字段名。
	 * @param int    $selected 当前选中的客户 ID。
	 * @return void
	 */
	function gridcraft_render_client_select( $input_id, $key, $selected ) {
		$clients = get_posts(
			array(
				'post_type'      => 'client',
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => 200,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);

		printf( '<select id="%1$s" name="gridcraft_meta[%2$s]" class="widefat">', esc_attr( $input_id ), esc_attr( $key ) );
		printf( '<option value="0">%s</option>', esc_html__( '— 未关联 —', 'gridcraft-portfolio' ) );

		foreach ( $clients as $client ) {
			printf(
				'<option value="%1$d"%3$s>%2$s</option>',
				(int) $client->ID,
				esc_html( get_the_title( $client ) ),
				selected( $selected, (int) $client->ID, false )
			);
		}

		echo '</select>';
	}
}

if ( ! function_exists( 'gridcraft_save_meta' ) ) {
	/**
	 * 保存 Meta 字段。
	 *
	 * @param int $post_id 文章 ID。
	 * @return void
	 */
	function gridcraft_save_meta( $post_id ) {
		// 自动草稿不做处理。
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( 'portfolio' === $post_type ) {
			$nonce_field = 'gridcraft_work_meta_nonce';
			$action      = 'gridcraft_save_work_meta';
			$fields      = gridcraft_work_meta_fields();
		} elseif ( 'client' === $post_type ) {
			$nonce_field = 'gridcraft_client_meta_nonce';
			$action      = 'gridcraft_save_client_meta';
			$fields      = gridcraft_client_meta_fields();
		} else {
			return;
		}

		// Nonce 校验。
		if ( ! isset( $_POST[ $nonce_field ] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST[ $nonce_field ] ) );

		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			return;
		}

		// 权限校验：只有能编辑该文章的用户才能保存。
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$input = array();

		if ( isset( $_POST['gridcraft_meta'] ) && is_array( $_POST['gridcraft_meta'] ) ) {
			$input = wp_unslash( $_POST['gridcraft_meta'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- 下方逐字段清洗。
		}

		foreach ( $fields as $key => $field ) {
			$raw = isset( $input[ $key ] ) ? $input[ $key ] : '';

			switch ( $field['type'] ) {
				case 'url':
					$value = esc_url_raw( (string) $raw );
					break;

				case 'client':
					$value = (int) $raw > 0 ? (int) $raw : '';
					break;

				case 'textarea':
					$value = sanitize_textarea_field( (string) $raw );
					break;

				default:
					$value = sanitize_text_field( (string) $raw );
					break;
			}

			if ( '' === $value || 0 === $value ) {
				delete_post_meta( $post_id, $key );
				continue;
			}

			update_post_meta( $post_id, $key, $value );
		}

		// 精选标记。
		if ( 'portfolio' === $post_type ) {
			if ( ! empty( $input['_gridcraft_featured'] ) ) {
				update_post_meta( $post_id, '_gridcraft_featured', '1' );
			} else {
				delete_post_meta( $post_id, '_gridcraft_featured' );
			}
		}
	}
}
add_action( 'save_post', 'gridcraft_save_meta' );

if ( ! function_exists( 'gridcraft_register_meta' ) ) {
	/**
	 * 把自定义字段注册到 REST API，区块编辑器中可直接读取。
	 *
	 * @return void
	 */
	function gridcraft_register_meta() {
		$fields = gridcraft_work_meta_fields();

		foreach ( $fields as $key => $field ) {
			$sanitize = 'sanitize_text_field';

			if ( 'url' === $field['type'] ) {
				$sanitize = 'esc_url_raw';
			} elseif ( 'textarea' === $field['type'] ) {
				$sanitize = 'sanitize_textarea_field';
			} elseif ( 'client' === $field['type'] ) {
				$sanitize = 'absint';
			}

			register_post_meta(
				'portfolio',
				$key,
				array(
					'type'              => 'url' === $field['type'] ? 'string' : ( 'client' === $field['type'] ? 'integer' : 'string' ),
					'description'       => $field['label'],
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => $sanitize,
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}

		register_post_meta(
			'portfolio',
			'_gridcraft_featured',
			array(
				'type'              => 'boolean',
				'description'       => esc_html__( '是否为精选作品', 'gridcraft-portfolio' ),
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'gridcraft_register_meta' );

if ( ! function_exists( 'gridcraft_add_meta_box_style' ) ) {
	/**
	 * 后台 Meta 框样式。
	 *
	 * @param string $hook 当前后台页面。
	 * @return void
	 */
	function gridcraft_add_meta_box_style( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		wp_register_style( 'gridcraft-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), gridcraft_asset_version( 'assets/css/admin.css' ) );

		wp_enqueue_style( 'gridcraft-admin' );
	}
}
add_action( 'admin_enqueue_scripts', 'gridcraft_add_meta_box_style' );

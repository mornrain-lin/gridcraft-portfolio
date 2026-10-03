<?php
/**
 * Gridcraft Portfolio 自定义器配置。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'gridcraft_option_defaults' ) ) {
	/**
	 * 主题全部设置项默认值。
	 *
	 * @return array<string,mixed>
	 */
	function gridcraft_option_defaults() {
		return array(
			'accent_color'            => '',
			'base_background'         => '',
			'hero_eyebrow'            => '',
			'hero_title'              => '',
			'hero_text'               => '',
			'hero_cta_label'          => '',
			'hero_cta_url'            => '',
			'hero_secondary_label'    => '',
			'hero_secondary_url'      => '',
			'hero_background'         => '',
			'hero_image'              => 0,
			'hero_scroll_hint'        => true,
			'transparent_header'      => false,
			'featured_enabled'        => true,
			'featured_title'          => '',
			'featured_count'          => 6,
			'featured_show_all'       => true,
			'services_enabled'        => true,
			'services_title'          => '',
			'clients_enabled'         => true,
			'clients_title'           => '',
			'clients_count'           => 8,
			'cta_enabled'             => true,
			'cta_title'               => '',
			'cta_text'                => '',
			'cta_button_label'        => '',
			'cta_button_url'          => '',
			'grid_columns'            => '3',
			'filter_enabled'          => true,
			'filter_per_page'         => 12,
			'portfolio_archive_title' => '',
			'client_archive_title'    => '',
			'excerpt_length'          => 28,
			'portfolio_meta'          => true,
			'portfolio_related'       => true,
		);
	}
}

if ( ! function_exists( 'gridcraft_get_option' ) ) {
	/**
	 * 读取主题设置。
	 *
	 * @param string $key     设置键名。
	 * @param mixed  $default 自定义默认值。
	 * @return mixed
	 */
	function gridcraft_get_option( $key, $default = null ) {
		$defaults = gridcraft_option_defaults();

		if ( null !== $default ) {
			$defaults[ $key ] = $default;
		}

		if ( ! isset( $defaults[ $key ] ) ) {
			return $default;
		}

		return get_theme_mod( 'gridcraft_' . $key, $defaults[ $key ] );
	}
}

if ( ! function_exists( 'gridcraft_build_dynamic_css' ) ) {
	/**
	 * 把 Customizer 设置转为 CSS 变量。
	 *
	 * @return string
	 */
	function gridcraft_build_dynamic_css() {
		$vars = array();

		$accent = gridcraft_get_option( 'accent_color' );

		if ( $accent ) {
			$accent = sanitize_hex_color( $accent );

			if ( $accent ) {
				$vars['--gc-accent'] = $accent;
				$vars['--gc-accent-soft'] = gridcraft_hex_to_rgba( $accent, 0.1 );
				$vars['--gc-focus'] = $accent;
			}
		}

		$base = gridcraft_get_option( 'base_background' );

		if ( $base ) {
			$base = sanitize_hex_color( $base );

			if ( $base ) {
				$vars['--gc-bg'] = $base;
			}
		}

		if ( empty( $vars ) ) {
			return '';
		}

		$declarations = '';

		foreach ( $vars as $name => $value ) {
			$declarations .= $name . ':' . $value . ';';
		}

		return ':root{' . $declarations . '}';
	}
}

if ( ! function_exists( 'gridcraft_hex_to_rgba' ) ) {
	/**
	 * 十六进制颜色转 rgba。
	 *
	 * @param string $hex   颜色值。
	 * @param float  $alpha 透明度。
	 * @return string
	 */
	function gridcraft_hex_to_rgba( $hex, $alpha = 0.1 ) {
		$hex = ltrim( (string) $hex, '#' );

		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
			return 'rgba(255, 90, 54, ' . (float) $alpha . ')';
		}

		return 'rgba(' . hexdec( substr( $hex, 0, 2 ) ) . ', ' . hexdec( substr( $hex, 2, 2 ) ) . ', ' . hexdec( substr( $hex, 4, 2 ) ) . ', ' . (float) $alpha . ')';
	}
}

if ( ! function_exists( 'gridcraft_customize_register' ) ) {
	/**
	 * 注册 Customizer 面板与设置项。
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer 实例。
	 * @return void
	 */
	function gridcraft_customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'blogname',
				array(
					'selector'        => '.gc-brand__title',
					'render_callback' => 'gridcraft_customize_blogname',
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				array(
					'selector'        => '.gc-site-description',
					'render_callback' => 'gridcraft_customize_blogdescription',
				)
			);
		}

		$wp_customize->add_panel(
			'gridcraft_panel',
			array(
				'title'       => esc_html__( '主题设置', 'gridcraft-portfolio' ),
				'description' => esc_html__( 'Gridcraft Portfolio 的全部主题选项。', 'gridcraft-portfolio' ),
				'priority'    => 20,
			)
		);

		/* ------------------------------------------------------------------
		 * 分区一：配色
		 * ------------------------------------------------------------------ */
		$wp_customize->add_section(
			'gridcraft_colors',
			array(
				'title' => esc_html__( '配色', 'gridcraft-portfolio' ),
				'panel' => 'gridcraft_panel',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_accent_color',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_accent_color',
			array(
				'label'       => esc_html__( '强调色', 'gridcraft-portfolio' ),
				'description' => esc_html__( '留空使用主题默认的橙红色 #ff5a36。', 'gridcraft-portfolio' ),
				'section'     => 'gridcraft_colors',
				'type'        => 'color',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_base_background',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_base_background',
			array(
				'label'   => esc_html__( '页面底色', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_colors',
				'type'    => 'color',
			)
		);

		/* ------------------------------------------------------------------
		 * 分区二：首页 Hero
		 * ------------------------------------------------------------------ */
		$wp_customize->add_section(
			'gridcraft_hero',
			array(
				'title'       => esc_html__( '首页 Hero', 'gridcraft-portfolio' ),
				'description' => esc_html__( '留空的文案会使用主题内置的默认内容。', 'gridcraft-portfolio' ),
				'panel'       => 'gridcraft_panel',
			)
		);

		$gridcraft_hero_fields = array(
			'hero_eyebrow'         => array(
				'label'    => esc_html__( '眉标', 'gridcraft-portfolio' ),
				'section'  => 'gridcraft_hero',
				'type'     => 'text',
			),
			'hero_title'           => array(
				'label'    => esc_html__( '主标题', 'gridcraft-portfolio' ),
				'section'  => 'gridcraft_hero',
				'type'     => 'text',
			),
			'hero_text'            => array(
				'label'    => esc_html__( '副文案', 'gridcraft-portfolio' ),
				'section'  => 'gridcraft_hero',
				'type'     => 'textarea',
			),
			'hero_cta_label'       => array(
				'label'    => esc_html__( '主按钮文案', 'gridcraft-portfolio' ),
				'section'  => 'gridcraft_hero',
				'type'     => 'text',
			),
			'hero_cta_url'         => array(
				'label'    => esc_html__( '主按钮链接', 'gridcraft-portfolio' ),
				'section'  => 'gridcraft_hero',
				'type'     => 'url',
			),
			'hero_secondary_label' => array(
				'label'    => esc_html__( '次按钮文案', 'gridcraft-portfolio' ),
				'section'  => 'gridcraft_hero',
				'type'     => 'text',
			),
			'hero_secondary_url'   => array(
				'label'    => esc_html__( '次按钮链接', 'gridcraft-portfolio' ),
				'section'  => 'gridcraft_hero',
				'type'     => 'url',
			),
			'hero_background'      => array(
				'label'    => esc_html__( 'Hero 背景色', 'gridcraft-portfolio' ),
				'section'  => 'gridcraft_hero',
				'type'     => 'color',
			),
		);

		foreach ( $gridcraft_hero_fields as $key => $field ) {
			$sanitize = 'sanitize_text_field';

			if ( 'url' === $field['type'] ) {
				$sanitize = 'esc_url_raw';
			} elseif ( 'textarea' === $field['type'] ) {
				$sanitize = 'sanitize_textarea_field';
			} elseif ( 'color' === $field['type'] ) {
				$sanitize = 'sanitize_hex_color';
			}

			$wp_customize->add_setting(
				'gridcraft_' . $key,
				array(
					'default'           => '',
					'sanitize_callback' => $sanitize,
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				'gridcraft_' . $key,
				array(
					'label'   => $field['label'],
					'section' => $field['section'],
					'type'    => $field['type'],
				)
			);
		}

		$wp_customize->add_setting(
			'gridcraft_hero_image',
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'gridcraft_hero_image',
				array(
					'label'       => esc_html__( 'Hero 背景图', 'gridcraft-portfolio' ),
					'description' => esc_html__( '建议 2000 × 1000 像素。选择图片后会自动叠加暗色蒙层保证文字可读。', 'gridcraft-portfolio' ),
					'section'     => 'gridcraft_hero',
					'mime_type'   => 'image',
				)
			)
		);

		$wp_customize->add_setting(
			'gridcraft_transparent_header',
			array(
				'default'           => false,
				'sanitize_callback' => 'gridcraft_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_transparent_header',
			array(
				'label'       => esc_html__( '首页头部透明叠加', 'gridcraft-portfolio' ),
				'description' => esc_html__( '让头部浮在 Hero 区之上。移动端会自动改回实底。', 'gridcraft-portfolio' ),
				'section'     => 'gridcraft_hero',
				'type'        => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_hero_scroll_hint',
			array(
				'default'           => true,
				'sanitize_callback' => 'gridcraft_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_hero_scroll_hint',
			array(
				'label'   => esc_html__( '显示向下滚动提示', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_hero',
				'type'    => 'checkbox',
			)
		);

		/* ------------------------------------------------------------------
		 * 分区三：首页区块
		 * ------------------------------------------------------------------ */
		$wp_customize->add_section(
			'gridcraft_home',
			array(
				'title' => esc_html__( '首页区块', 'gridcraft-portfolio' ),
				'panel' => 'gridcraft_panel',
			)
		);

		$gridcraft_toggles = array(
			'featured_enabled'  => array(
				'label' => esc_html__( '显示精选作品区块', 'gridcraft-portfolio' ),
			),
			'services_enabled'  => array(
				'label' => esc_html__( '显示服务介绍区块', 'gridcraft-portfolio' ),
			),
			'clients_enabled'   => array(
				'label' => esc_html__( '显示客户区块', 'gridcraft-portfolio' ),
			),
			'cta_enabled'       => array(
				'label' => esc_html__( '显示约稿 CTA 区块', 'gridcraft-portfolio' ),
			),
		);

		foreach ( $gridcraft_toggles as $key => $field ) {
			$wp_customize->add_setting(
				'gridcraft_' . $key,
				array(
					'default'           => true,
					'sanitize_callback' => 'gridcraft_sanitize_checkbox',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				'gridcraft_' . $key,
				array(
					'label'   => $field['label'],
					'section' => 'gridcraft_home',
					'type'    => 'checkbox',
				)
			);
		}

		$wp_customize->add_setting(
			'gridcraft_featured_title',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_featured_title',
			array(
				'label'   => esc_html__( '精选区块标题', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_home',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_featured_count',
			array(
				'default'           => 6,
				'sanitize_callback' => 'gridcraft_sanitize_number',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_featured_count',
			array(
				'label'       => esc_html__( '精选作品数量', 'gridcraft-portfolio' ),
				'section'     => 'gridcraft_home',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 3,
					'max'  => 12,
					'step' => 1,
				),
			)
		);

		$wp_customize->add_setting(
			'gridcraft_featured_show_all',
			array(
				'default'           => true,
				'sanitize_callback' => 'gridcraft_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_featured_show_all',
			array(
				'label'   => esc_html__( '显示「查看全部作品」按钮', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_home',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_services_title',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_services_title',
			array(
				'label'   => esc_html__( '服务区块标题', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_home',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_clients_title',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_clients_title',
			array(
				'label'   => esc_html__( '客户区块标题', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_home',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_clients_count',
			array(
				'default'           => 8,
				'sanitize_callback' => 'gridcraft_sanitize_number',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_clients_count',
			array(
				'label'       => esc_html__( '展示客户数量', 'gridcraft-portfolio' ),
				'section'     => 'gridcraft_home',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 2,
					'max'  => 24,
					'step' => 1,
				),
			)
		);

		$wp_customize->add_setting(
			'gridcraft_cta_title',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_cta_title',
			array(
				'label'   => esc_html__( 'CTA 标题', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_home',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_cta_text',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_textarea_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_cta_text',
			array(
				'label'   => esc_html__( 'CTA 说明文字', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_home',
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_cta_button_label',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_cta_button_label',
			array(
				'label'   => esc_html__( 'CTA 按钮文案', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_home',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_cta_button_url',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_cta_button_url',
			array(
				'label'   => esc_html__( 'CTA 按钮链接', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_home',
				'type'    => 'url',
			)
		);

		/* ------------------------------------------------------------------
		 * 分区四：作品网格
		 * ------------------------------------------------------------------ */
		$wp_customize->add_section(
			'gridcraft_grid',
			array(
				'title' => esc_html__( '作品网格与筛选', 'gridcraft-portfolio' ),
				'panel' => 'gridcraft_panel',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_grid_columns',
			array(
				'default'           => '3',
				'sanitize_callback' => 'gridcraft_sanitize_columns',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_grid_columns',
			array(
				'label'       => esc_html__( '桌面端列数', 'gridcraft-portfolio' ),
				'description' => esc_html__( '900px 以下自动降为 2 列，640px 以下为 1 列。', 'gridcraft-portfolio' ),
				'section'     => 'gridcraft_grid',
				'type'        => 'select',
				'choices'     => array(
					'2' => esc_html__( '2 列', 'gridcraft-portfolio' ),
					'3' => esc_html__( '3 列', 'gridcraft-portfolio' ),
					'4' => esc_html__( '4 列', 'gridcraft-portfolio' ),
				),
			)
		);

		$wp_customize->add_setting(
			'gridcraft_filter_enabled',
			array(
				'default'           => true,
				'sanitize_callback' => 'gridcraft_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_filter_enabled',
			array(
				'label'       => esc_html__( '启用分类筛选条', 'gridcraft-portfolio' ),
				'description' => esc_html__( '关闭后作品归档页不输出筛选条。', 'gridcraft-portfolio' ),
				'section'     => 'gridcraft_grid',
				'type'        => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_filter_per_page',
			array(
				'default'           => 12,
				'sanitize_callback' => 'gridcraft_sanitize_number',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_filter_per_page',
			array(
				'label'       => esc_html__( '筛选每页作品数', 'gridcraft-portfolio' ),
				'section'     => 'gridcraft_grid',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 4,
					'max'  => 48,
					'step' => 1,
				),
			)
		);

		$wp_customize->add_setting(
			'gridcraft_excerpt_length',
			array(
				'default'           => 28,
				'sanitize_callback' => 'gridcraft_sanitize_number',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_excerpt_length',
			array(
				'label'   => esc_html__( '摘要长度（词）', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_grid',
				'type'    => 'number',
				'input_attrs' => array(
					'min'  => 8,
					'max'  => 100,
					'step' => 1,
				),
			)
		);

		/* ------------------------------------------------------------------
		 * 分区五：作品详情页
		 * ------------------------------------------------------------------ */
		$wp_customize->add_section(
			'gridcraft_single',
			array(
				'title' => esc_html__( '作品详情页', 'gridcraft-portfolio' ),
				'panel' => 'gridcraft_panel',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_portfolio_meta',
			array(
				'default'           => true,
				'sanitize_callback' => 'gridcraft_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_portfolio_meta',
			array(
				'label'   => esc_html__( '显示作品信息卡片', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_single',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_portfolio_related',
			array(
				'default'           => true,
				'sanitize_callback' => 'gridcraft_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_portfolio_related',
			array(
				'label'   => esc_html__( '显示相关作品', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_single',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_portfolio_archive_title',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_portfolio_archive_title',
			array(
				'label'   => esc_html__( '作品归档页标题', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_single',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'gridcraft_client_archive_title',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gridcraft_client_archive_title',
			array(
				'label'   => esc_html__( '客户归档页标题', 'gridcraft-portfolio' ),
				'section' => 'gridcraft_single',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'gridcraft_customize_register' );

if ( ! function_exists( 'gridcraft_customize_blogname' ) ) {
	/**
	 * 站点标题即时刷新。
	 *
	 * @return void
	 */
	function gridcraft_customize_blogname() {
		bloginfo( 'name', 'display' );
	}
}

if ( ! function_exists( 'gridcraft_customize_blogdescription' ) ) {
	/**
	 * 站点描述即时刷新。
	 *
	 * @return void
	 */
	function gridcraft_customize_blogdescription() {
		bloginfo( 'description', 'display' );
	}
}

if ( ! function_exists( 'gridcraft_customize_preview_js' ) ) {
	/**
	 * 加载 Customizer 预览脚本。
	 *
	 * @return void
	 */
	function gridcraft_customize_preview_js() {
		wp_enqueue_script(
			'gridcraft-customizer-preview',
			get_template_directory_uri() . '/assets/js/customizer-preview.js',
			array( 'customize-preview' ),
			gridcraft_asset_version( 'assets/js/customizer-preview.js' ),
			true
		);
	}
}
add_action( 'customize_preview_init', 'gridcraft_customize_preview_js' );

if ( ! function_exists( 'gridcraft_sanitize_checkbox' ) ) {
	/**
	 * 复选框清洗。
	 *
	 * @param mixed $checked 输入值。
	 * @return bool
	 */
	function gridcraft_sanitize_checkbox( $checked ) {
		return ( isset( $checked ) && true === (bool) $checked );
	}
}

if ( ! function_exists( 'gridcraft_sanitize_number' ) ) {
	/**
	 * 整数清洗。
	 *
	 * @param mixed $value 输入值。
	 * @return int
	 */
	function gridcraft_sanitize_number( $value ) {
		return (int) $value;
	}
}

if ( ! function_exists( 'gridcraft_sanitize_columns' ) ) {
	/**
	 * 网格列数白名单。
	 *
	 * @param mixed $value 输入值。
	 * @return string
	 */
	function gridcraft_sanitize_columns( $value ) {
		$allowed = array( '2', '3', '4' );
		$value   = is_string( $value ) ? $value : '3';

		return in_array( $value, $allowed, true ) ? $value : '3';
	}
}

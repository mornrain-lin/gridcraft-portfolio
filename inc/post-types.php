<?php
/**
 * Gridcraft Portfolio 自定义文章类型与分类法注册。
 *
 * 注册 `portfolio`（作品）与 `client`（客户）两个 CPT，
 * 作品挂 `portfolio_category` / `portfolio_tag` 两个分类法。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'gridcraft_get_work_taxonomies' ) ) {
	/**
	 * 作品类型关联的分类法列表。
	 *
	 * @return string[]
	 */
	function gridcraft_get_work_taxonomies() {
		return array( 'portfolio_category', 'portfolio_tag' );
	}
}

if ( ! function_exists( 'gridcraft_register_post_types' ) ) {
	/**
	 * 注册作品与客户类型。
	 *
	 * @return void
	 */
	function gridcraft_register_post_types() {

		$labels = array(
			'portfolio' => array(
				'name'                  => esc_html__( '作品', 'gridcraft-portfolio' ),
				'singular_name'         => esc_html__( '作品', 'gridcraft-portfolio' ),
				'menu_name'             => esc_html__( '作品', 'gridcraft-portfolio' ),
				'add_new'               => esc_html__( '添加作品', 'gridcraft-portfolio' ),
				/* translators: %s：作品类型名称。 */
				'add_new_item'          => sprintf( esc_html__( '添加新%s', 'gridcraft-portfolio' ), esc_html__( '作品', 'gridcraft-portfolio' ) ),
				/* translators: %s：作品类型名称。 */
				'edit_item'             => sprintf( esc_html__( '编辑%s', 'gridcraft-portfolio' ), esc_html__( '作品', 'gridcraft-portfolio' ) ),
				/* translators: %s：作品类型名称。 */
				'new_item'              => sprintf( esc_html__( '新%s', 'gridcraft-portfolio' ), esc_html__( '作品', 'gridcraft-portfolio' ) ),
				/* translators: %s：作品类型名称。 */
				'view_item'             => sprintf( esc_html__( '查看%s', 'gridcraft-portfolio' ), esc_html__( '作品', 'gridcraft-portfolio' ) ),
				/* translators: %s：作品类型名称。 */
				'search_items'          => sprintf( esc_html__( '搜索%s', 'gridcraft-portfolio' ), esc_html__( '作品', 'gridcraft-portfolio' ) ),
				'not_found'             => esc_html__( '还没有作品', 'gridcraft-portfolio' ),
				'not_found_in_trash'    => esc_html__( '回收站里没有作品', 'gridcraft-portfolio' ),
				'all_items'             => esc_html__( '全部作品', 'gridcraft-portfolio' ),
				'featured_image'        => esc_html__( '作品封面', 'gridcraft-portfolio' ),
				/* translators: %s：作品类型名称。 */
				'set_featured_image'    => sprintf( esc_html__( '设置%s封面', 'gridcraft-portfolio' ), esc_html__( '作品', 'gridcraft-portfolio' ) ),
				'remove_featured_image' => esc_html__( '移除封面', 'gridcraft-portfolio' ),
				'use_featured_image'    => esc_html__( '作为封面', 'gridcraft-portfolio' ),
				'item_published'        => esc_html__( '作品已发布', 'gridcraft-portfolio' ),
			),
			'client'     => array(
				'name'                  => esc_html__( '客户', 'gridcraft-portfolio' ),
				'singular_name'         => esc_html__( '客户', 'gridcraft-portfolio' ),
				'menu_name'             => esc_html__( '客户', 'gridcraft-portfolio' ),
				'add_new'               => esc_html__( '添加客户', 'gridcraft-portfolio' ),
				/* translators: %s：客户类型名称。 */
				'add_new_item'          => sprintf( esc_html__( '添加新%s', 'gridcraft-portfolio' ), esc_html__( '客户', 'gridcraft-portfolio' ) ),
				/* translators: %s：客户类型名称。 */
				'edit_item'             => sprintf( esc_html__( '编辑%s', 'gridcraft-portfolio' ), esc_html__( '客户', 'gridcraft-portfolio' ) ),
				/* translators: %s：客户类型名称。 */
				'new_item'              => sprintf( esc_html__( '新%s', 'gridcraft-portfolio' ), esc_html__( '客户', 'gridcraft-portfolio' ) ),
				/* translators: %s：客户类型名称。 */
				'view_item'             => sprintf( esc_html__( '查看%s', 'gridcraft-portfolio' ), esc_html__( '客户', 'gridcraft-portfolio' ) ),
				/* translators: %s：客户类型名称。 */
				'search_items'          => sprintf( esc_html__( '搜索%s', 'gridcraft-portfolio' ), esc_html__( '客户', 'gridcraft-portfolio' ) ),
				'not_found'             => esc_html__( '还没有客户', 'gridcraft-portfolio' ),
				'not_found_in_trash'    => esc_html__( '回收站里没有客户', 'gridcraft-portfolio' ),
				'all_items'             => esc_html__( '全部客户', 'gridcraft-portfolio' ),
			),
		);

		register_post_type(
			'portfolio',
			array(
				'labels'             => $labels['portfolio'],
				'description'        => esc_html__( '作品集条目。', 'gridcraft-portfolio' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_in_rest'       => true,
				'menu_position'      => 20,
				'menu_icon'          => 'dashicons-portfolio',
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'hierarchical'       => false,
				'rewrite'            => array(
					'slug'       => 'work',
					'with_front' => false,
				),
				'query_var'          => true,
				'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes' ),
				'taxonomies'         => gridcraft_get_work_taxonomies(),
			)
		);

		register_post_type(
			'client',
			array(
				'labels'             => $labels['client'],
				'description'        => esc_html__( '作品集中展示的客户 / 品牌。', 'gridcraft-portfolio' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_in_rest'       => true,
				'menu_position'      => 21,
				'menu_icon'          => 'dashicons-groups',
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'hierarchical'       => false,
				'rewrite'            => array(
					'slug'       => 'client',
					'with_front' => false,
				),
				'query_var'          => true,
				'supports'           => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			)
		);

		// 作品分类。
		register_taxonomy(
			'portfolio_category',
			array( 'portfolio' ),
			array(
				'labels'            => array(
					'name'              => esc_html__( '作品分类', 'gridcraft-portfolio' ),
					'singular_name'     => esc_html__( '作品分类', 'gridcraft-portfolio' ),
					'search_items'      => esc_html__( '搜索作品分类', 'gridcraft-portfolio' ),
					'all_items'         => esc_html__( '全部分类', 'gridcraft-portfolio' ),
					'parent_item'       => esc_html__( '上级分类', 'gridcraft-portfolio' ),
					'parent_item_colon' => esc_html__( '上级分类：', 'gridcraft-portfolio' ),
					'edit_item'         => esc_html__( '编辑分类', 'gridcraft-portfolio' ),
					'update_item'       => esc_html__( '更新分类', 'gridcraft-portfolio' ),
					'add_new_item'      => esc_html__( '添加分类', 'gridcraft-portfolio' ),
					'new_item_name'     => esc_html__( '新分类名称', 'gridcraft-portfolio' ),
					'menu_name'         => esc_html__( '作品分类', 'gridcraft-portfolio' ),
				),
				'public'            => true,
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'show_in_nav_menus' => true,
				'rewrite'           => array(
					'slug'         => 'work-category',
					'with_front'   => false,
					'hierarchical' => true,
				),
			)
		);

		// 作品标签。
		register_taxonomy(
			'portfolio_tag',
			array( 'portfolio' ),
			array(
				'labels'            => array(
					'name'          => esc_html__( '作品标签', 'gridcraft-portfolio' ),
					'singular_name' => esc_html__( '作品标签', 'gridcraft-portfolio' ),
					'search_items'  => esc_html__( '搜索作品标签', 'gridcraft-portfolio' ),
					'all_items'     => esc_html__( '全部标签', 'gridcraft-portfolio' ),
					'edit_item'     => esc_html__( '编辑标签', 'gridcraft-portfolio' ),
					'add_new_item'  => esc_html__( '添加标签', 'gridcraft-portfolio' ),
					'menu_name'     => esc_html__( '作品标签', 'gridcraft-portfolio' ),
				),
				'public'            => true,
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'work-tag',
					'with_front' => false,
				),
			)
		);
	}
}
add_action( 'init', 'gridcraft_register_post_types' );

if ( ! function_exists( 'gridcraft_flush_rewrite_on_switch' ) ) {
	/**
	 * 切换主题时刷新固定链接，避免作品页404。
	 *
	 * @return void
	 */
	function gridcraft_flush_rewrite_on_switch() {
		gridcraft_register_post_types();
		flush_rewrite_rules();
	}
}
add_action( 'after_switch_theme', 'gridcraft_flush_rewrite_on_switch' );

if ( ! function_exists( 'gridcraft_archive_titles' ) ) {
	/**
	 * 让作品归档页的标题更符合作品集语义。
	 *
	 * @param string $title 原标题。
	 * @return string
	 */
	function gridcraft_archive_titles( $title ) {
		if ( is_post_type_archive( 'portfolio' ) ) {
			$custom = gridcraft_get_option( 'portfolio_archive_title' );

			return $custom ? $custom : esc_html__( '作品集', 'gridcraft-portfolio' );
		}

		if ( is_post_type_archive( 'client' ) ) {
			$custom = gridcraft_get_option( 'client_archive_title' );

			return $custom ? $custom : esc_html__( '合作客户', 'gridcraft-portfolio' );
		}

		return $title;
	}
}
add_filter( 'get_the_archive_title', 'gridcraft_archive_titles' );

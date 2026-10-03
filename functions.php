<?php
/**
 * Gridcraft Portfolio 主题函数入口。
 *
 * 装配入口：主题支持、CPT 注册、Meta 框、前端资源、Customizer。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'GRIDCRAFT_VERSION' ) ) {
	define( 'GRIDCRAFT_VERSION', '1.0.0' );
}

if ( ! defined( 'GRIDCRAFT_TEXT_DOMAIN' ) ) {
	define( 'GRIDCRAFT_TEXT_DOMAIN', 'gridcraft-portfolio' );
}

/**
 * 读取主题资源的版本号，用于前端缓存 busting。
 *
 * 优先使用文件的最后修改时间，文件不可读时回落到主题版本号。
 *
 * @param string $relative_path 相对主题根目录的路径。
 * @return string
 */
function gridcraft_asset_version( $relative_path ) {
	$file = get_template_directory() . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $file ) ) {
		$mtime = filemtime( $file );

		if ( $mtime ) {
			return (string) $mtime;
		}
	}

	return GRIDCRAFT_VERSION;
}

require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/meta-boxes.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/ajax.php';


if ( ! function_exists( 'gridcraft_setup' ) ) {
	/**
	 * 声明主题基础能力。
	 *
	 * @return void
	 */
	function gridcraft_setup() {
		load_theme_textdomain( 'gridcraft-portfolio', get_template_directory() . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-font-sizes' );

		add_theme_support( 'editor-styles' );

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		add_theme_support(
			'custom-logo',
			array(
				'height'      => 60,
				'width'       => 240,
				'flex-height' => true,
				'flex-width'  => true,
				'header-text' => array( 'site-title', 'site-description' ),
			)
		);

		add_theme_support( 'post-formats', array( 'aside', 'gallery', 'image', 'video' ) );

		add_editor_style( 'assets/css/editor-style.css' );

		add_image_size( 'gridcraft-tile', 640, 480, true );
		add_image_size( 'gridcraft-card', 900, 600, true );
		add_image_size( 'gridcraft-hero', 2000, 1000, true );

		register_nav_menus(
			array(
				'primary' => esc_html__( '主导航', 'gridcraft-portfolio' ),
				'footer'  => esc_html__( '页脚导航', 'gridcraft-portfolio' ),
			)
		);

		// 让作品与客户类型出现在 REST API 与区块编辑器中。
		add_theme_support( 'custom-line-height' );
		add_theme_support( 'custom-spacing' );
		add_theme_support( 'appearance-tools' );
	}
}
add_action( 'after_setup_theme', 'gridcraft_setup' );

if ( ! function_exists( 'gridcraft_content_width' ) ) {
	/**
	 * 设置正文宽度。
	 *
	 * @global int $content_width
	 * @return void
	 */
	function gridcraft_content_width() {
		$width = 760;

		/**
		 * 过滤正文宽度。
		 *
		 * @param int $width 宽度（像素）。
		 */
		$width = (int) apply_filters( 'gridcraft_content_width', $width );

		$GLOBALS['content_width'] = $width;
	}
}
add_action( 'after_setup_theme', 'gridcraft_content_width', 0 );


if ( ! function_exists( 'gridcraft_widgets_init' ) ) {
	/**
	 * 注册侧栏 2 个 + 页脚 3 个 Widget 区域。
	 *
	 * @return void
	 */
	function gridcraft_widgets_init() {
		$defaults = array(
			'before_widget' => '<section id="%1$s" class="gc-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="gc-widget__title">',
			'after_title'   => '</h2>',
		);

		register_sidebar(
			array_merge(
				$defaults,
				array(
					'name'        => esc_html__( '侧栏 1（主侧栏）', 'gridcraft-portfolio' ),
					'id'          => 'sidebar-1',
					'description' => esc_html__( '显示在归档页与 404 页。', 'gridcraft-portfolio' ),
				)
			)
		);

		register_sidebar(
			array_merge(
				$defaults,
				array(
					'name'        => esc_html__( '侧栏 2（次要）', 'gridcraft-portfolio' ),
					'id'          => 'sidebar-2',
					'description' => esc_html__( '备用侧栏。', 'gridcraft-portfolio' ),
				)
			)
		);

		for ( $i = 1; $i <= 3; $i++ ) {
			register_sidebar(
				array_merge(
					$defaults,
					array(
						/* translators: %d：页脚栏序号。 */
						'name'        => sprintf( esc_html__( '页脚 %d', 'gridcraft-portfolio' ), $i ),
						'id'          => 'footer-' . $i,
						/* translators: %d：页脚栏序号。 */
						'description' => sprintf( esc_html__( '页脚第 %d 栏。', 'gridcraft-portfolio' ), $i ),
					)
				)
			);
		}
	}
}
add_action( 'widgets_init', 'gridcraft_widgets_init' );


if ( ! function_exists( 'gridcraft_scripts' ) ) {
	/**
	 * 加载前端样式与脚本。
	 *
	 * @return void
	 */
	function gridcraft_scripts() {
		wp_enqueue_style(
			'gridcraft-style',
			get_stylesheet_uri(),
			array(),
			gridcraft_asset_version( 'style.css' )
		);

		wp_style_add_data( 'gridcraft-style', 'rtl', 'replace' );

		wp_enqueue_script(
			'gridcraft-navigation',
			get_template_directory_uri() . '/assets/js/navigation.js',
			array(),
			gridcraft_asset_version( 'assets/js/navigation.js' ),
			true
		);

		// 作品归档页需要筛选与 Lightbox。
		if ( is_post_type_archive( 'portfolio' ) || is_tax( gridcraft_get_work_taxonomies() ) ) {
			wp_enqueue_script(
				'gridcraft-filter',
				get_template_directory_uri() . '/assets/js/filter.js',
				array(),
				gridcraft_asset_version( 'assets/js/filter.js' ),
				true
			);

			wp_localize_script(
				'gridcraft-filter',
				'gridcraftFilter',
				array(
					'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
					'nonce'      => wp_create_nonce( 'gridcraft_filter' ),
					'loading'    => esc_html__( '加载中…', 'gridcraft-portfolio' ),
					'error'      => esc_html__( '加载失败，请稍后重试。', 'gridcraft-portfolio' ),
					'allText'    => esc_html__( '全部', 'gridcraft-portfolio' ),
					'showingFmt' => esc_html__( '共 %d 个作品', 'gridcraft-portfolio' ),
				)
			);
		}

		if ( is_singular( 'portfolio' ) ) {
			wp_enqueue_script(
				'gridcraft-lightbox',
				get_template_directory_uri() . '/assets/js/lightbox.js',
				array(),
				gridcraft_asset_version( 'assets/js/lightbox.js' ),
				true
			);
		}

		// 告诉前端浏览器是否支持 JS，用于隐藏纯 CSS 降级元素。
		wp_add_inline_script(
			'gridcraft-navigation',
			'document.documentElement.classList.remove( "gc-no-js" );',
			'before'
		);

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'gridcraft_scripts' );

if ( ! function_exists( 'gridcraft_no_js_class' ) ) {
	/**
	 * 在 <html> 上标记是否启用 JS。
	 *
	 * 筛选条依赖 JS，默认隐藏，用 CSS 反转。
	 *
	 * @return void
	 */
	function gridcraft_no_js_class() {
		echo '<script>document.documentElement.className += " gc-no-js";</script>' . "\n";
	}
}
add_action( 'wp_head', 'gridcraft_no_js_class', 0 );

if ( ! function_exists( 'gridcraft_body_classes' ) ) {
	/**
	 * 为 <body> 追加状态类。
	 *
	 * @param array $classes 现有类名。
	 * @return array
	 */
	function gridcraft_body_classes( $classes ) {
		$classes[] = 'gc-site';

		if ( is_front_page() ) {
			$classes[] = 'gc-is-front';
		}

		if ( is_singular( 'portfolio' ) ) {
			$classes[] = 'gc-single-work';
		}

		return $classes;
	}
}
add_filter( 'body_class', 'gridcraft_body_classes' );

if ( ! function_exists( 'gridcraft_excerpt_length' ) ) {
	/**
	 * 摘要长度。
	 *
	 * @param int $length 默认长度。
	 * @return int
	 */
	function gridcraft_excerpt_length( $length ) {
		$custom = (int) gridcraft_get_option( 'excerpt_length' );

		return $custom > 0 ? $custom : (int) $length;
	}
}
add_filter( 'excerpt_length', 'gridcraft_excerpt_length' );

if ( ! function_exists( 'gridcraft_excerpt_more' ) ) {
	/**
	 * 摘要省略符。
	 *
	 * @param string $more 默认省略符。
	 * @return string
	 */
	function gridcraft_excerpt_more( $more ) {
		if ( is_admin() ) {
			return $more;
		}

		return '…';
	}
}
add_filter( 'excerpt_more', 'gridcraft_excerpt_more' );

if ( ! function_exists( 'gridcraft_pingback_header' ) ) {
	/**
	 * 输出 pingback 链接。
	 *
	 * @return void
	 */
	function gridcraft_pingback_header() {
		if ( is_singular() && pings_open() ) {
			printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
		}
	}
}
add_action( 'wp_head', 'gridcraft_pingback_header' );

if ( ! function_exists( 'gridcraft_dynamic_css' ) ) {
	/**
	 * 输出 Customizer 生成的 CSS 变量。
	 *
	 * @return void
	 */
	function gridcraft_dynamic_css() {
		$css = gridcraft_build_dynamic_css();

		if ( '' === $css ) {
			return;
		}

		wp_add_inline_style( 'gridcraft-style', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'gridcraft_dynamic_css', 20 );

if ( ! function_exists( 'gridcraft_woocommerce_declared' ) ) {
	/**
	 * 仅声明 WooCommerce 支持。
	 *
	 * @return void
	 */
	function gridcraft_woocommerce_declared() {
		add_theme_support( 'woocommerce' );
	}
}
add_action( 'after_setup_theme', 'gridcraft_woocommerce_declared' );

if ( ! function_exists( 'gridcraft_kses_allowed_html' ) ) {
	/**
	 * 为前台输出补充允许的 SVG 标签（内联图标）。
	 *
	 * @param array $tags 当前允许的标签。
	 * @return array
	 */
	function gridcraft_kses_allowed_html( $tags ) {
		$tags['svg'] = array(
			'class'   => true,
			'viewbox' => true,
			'width'   => true,
			'height'  => true,
			'fill'    => true,
			'focusable' => true,
			'aria-hidden' => true,
			'role'    => true,
			'xmlns'   => true,
		);

		$tags['path'] = array(
			'd'         => true,
			'fill'      => true,
			'fill-rule' => true,
			'clip-rule' => true,
		);

		$tags['circle'] = array(
			'cx'   => true,
			'cy'   => true,
			'r'    => true,
			'fill' => true,
		);

		$tags['rect'] = array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
			'fill'   => true,
		);

		$tags['line'] = array(
			'x1'   => true,
			'y1'   => true,
			'x2'   => true,
			'y2'   => true,
			'stroke' => true,
		);

		return $tags;
	}
}
add_filter( 'wp_kses_allowed_html', 'gridcraft_kses_allowed_html' );

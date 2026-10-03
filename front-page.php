<?php
/**
 * 首页模板：Hero 区 + 精选作品 + 服务介绍 + 客户 + 约稿 CTA。
 *
 * @package GridcraftPortfolio
 * @since   1.0.0
 */

get_header();

$gc_hero_image_id = (int) gridcraft_get_option( 'hero_image' );
$gc_hero_bg_color = (string) gridcraft_get_option( 'hero_background' );

$gc_hero_classes = 'gc-hero';
$gc_hero_style   = '';

if ( $gc_hero_image_id ) {
	$gc_hero_url = wp_get_attachment_image_url( $gc_hero_image_id, 'gridcraft-hero' );

	if ( $gc_hero_url ) {
		$gc_hero_classes .= ' gc-hero--image';
		$gc_hero_style    = 'background-image:url(' . esc_url_raw( $gc_hero_url ) . ');';
	}
} elseif ( '' !== $gc_hero_bg_color ) {
	$gc_hero_bg_color = sanitize_hex_color( $gc_hero_bg_color );

	$gc_hero_style = $gc_hero_bg_color ? 'background-color:' . $gc_hero_bg_color . ';' : '';
}

$gc_eyebrow   = (string) gridcraft_get_option( 'hero_eyebrow' );
$gc_title     = (string) gridcraft_get_option( 'hero_title' );
$gc_text      = (string) gridcraft_get_option( 'hero_text' );
$gc_cta_label = (string) gridcraft_get_option( 'hero_cta_label' );
$gc_cta_url   = (string) gridcraft_get_option( 'hero_cta_url' );
$gc_cta2_label = (string) gridcraft_get_option( 'hero_secondary_label' );
$gc_cta2_url   = (string) gridcraft_get_option( 'hero_secondary_url' );

if ( '' === $gc_eyebrow ) {
	$gc_eyebrow = esc_html__( '独立设计师 / 开发者', 'gridcraft-portfolio' );
}

if ( '' === $gc_title ) {
	$gc_title = esc_html__( '把想法做成看得见的作品', 'gridcraft-portfolio' );
}

if ( '' === $gc_text ) {
	$gc_text = esc_html__( '专注品牌视觉、网站与产品设计。每一个项目都从真实需求出发，到可落地的交付物结束。', 'gridcraft-portfolio' );
}

if ( '' === $gc_cta_label ) {
	$gc_cta_label = esc_html__( '查看作品', 'gridcraft-portfolio' );
	$gc_cta_url   = (string) post_type_archive_link( 'portfolio' );
}

if ( '' === $gc_cta2_label ) {
	$gc_cta2_label = esc_html__( '聊聊合作', 'gridcraft-portfolio' );
	$gc_cta2_url   = '';
}
?>

<section
	class="<?php echo esc_attr( $gc_hero_classes ); ?>"
	<?php echo '' !== $gc_hero_style ? ' style="' . esc_attr( $gc_hero_style ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 片段已用 esc_attr 转义。 ?>
>
	<div class="gc-container">
		<div class="gc-hero__inner">
			<?php if ( '' !== $gc_eyebrow ) : ?>
				<span class="gc-hero__eyebrow"><?php echo esc_html( $gc_eyebrow ); ?></span>
			<?php endif; ?>

			<h1 class="gc-hero__title"><?php echo esc_html( $gc_title ); ?></h1>

			<?php if ( '' !== $gc_text ) : ?>
				<p class="gc-hero__text"><?php echo esc_html( $gc_text ); ?></p>
			<?php endif; ?>

			<div class="gc-hero__actions">
				<?php if ( '' !== $gc_cta_url ) : ?>
					<a class="gc-button" href="<?php echo esc_url( $gc_cta_url ); ?>">
						<?php echo esc_html( $gc_cta_label ); ?>
					</a>
				<?php endif; ?>

				<?php if ( '' !== $gc_cta2_url ) : ?>
					<a class="gc-button gc-button--ghost-light" href="<?php echo esc_url( $gc_cta2_url ); ?>">
						<?php echo esc_html( $gc_cta2_label ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ( gridcraft_get_option( 'hero_scroll_hint' ) ) : ?>
		<a class="gc-hero__scroll" href="#gc-featured">
			<?php esc_html_e( '向下浏览', 'gridcraft-portfolio' ); ?>
			<span aria-hidden="true">
				<svg width="14" height="14" viewBox="0 0 16 16" focusable="false" aria-hidden="true"><path d="M8 2.4 2.8 7.6l1.06 1.06L8 4.52l4.14 4.14 1.06-1.06L8 2.4Z"/><path d="M2.8 10.4h10.4v1.6H2.8z"/></svg>
			</span>
		</a>
	<?php endif; ?>
</section>

<?php
// 精选作品区块。
if ( gridcraft_get_option( 'featured_enabled' ) ) :
	$gc_featured_count = (int) gridcraft_get_option( 'featured_count' );

	if ( $gc_featured_count < 3 ) {
		$gc_featured_count = 6;
	}

	$gc_featured_args = array(
		'post_type'      => 'portfolio',
		'post_status'    => 'publish',
		'posts_per_page' => $gc_featured_count,
		'no_found_rows'  => true,
		'meta_query'     => array(
			array(
				'key'     => '_gridcraft_featured',
				'value'   => '1',
				'compare' => '=',
			),
		),
	);

	$gc_featured_query = new WP_Query( $gc_featured_args );

	// 精选标记为空时退化为显示最新作品，避免首页空白。
	if ( ! $gc_featured_query->have_posts() ) {
		$gc_featured_query = new WP_Query(
			array(
				'post_type'           => 'portfolio',
				'post_status'         => 'publish',
				'posts_per_page'      => $gc_featured_count,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
	}

	if ( $gc_featured_query->have_posts() ) :
		$gc_featured_title = (string) gridcraft_get_option( 'featured_title' );

		if ( '' === $gc_featured_title ) {
			$gc_featured_title = esc_html__( '精选作品', 'gridcraft-portfolio' );
		}
		?>
		<section id="gc-featured" class="gc-section">
			<div class="gc-container">
				<div class="gc-section__header">
					<span class="gc-section__eyebrow"><?php esc_html_e( 'Selected Work', 'gridcraft-portfolio' ); ?></span>
					<h2 class="gc-section__title"><?php echo esc_html( $gc_featured_title ); ?></h2>
				</div>

				<?php
				gridcraft_work_grid( $gc_featured_query, array( 'gc-grid--featured' ) );
				wp_reset_postdata();
				?>

				<?php if ( gridcraft_get_option( 'featured_show_all' ) ) : ?>
					<p class="gc-section__more">
						<a class="gc-button gc-button--outline" href="<?php echo esc_url( (string) post_type_archive_link( 'portfolio' ) ); ?>">
							<?php esc_html_e( '查看全部作品', 'gridcraft-portfolio' ); ?>
						</a>
					</p>
				<?php endif; ?>
			</div>
		</section>
		<?php
	endif;
endif;

// 统计条。
$gc_stats = gridcraft_get_stats();
?>
<section class="gc-section gc-section--tight gc-section--dark">
	<div class="gc-container">
		<div class="gc-stats">
			<?php foreach ( $gc_stats as $gc_stat ) : ?>
				<div class="gc-stat">
					<span class="gc-stat__number"><?php echo esc_html( $gc_stat['number'] ); ?></span>
					<span class="gc-stat__label"><?php echo esc_html( $gc_stat['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
// 服务介绍区块。
if ( gridcraft_get_option( 'services_enabled' ) ) :
	$gc_services_title = (string) gridcraft_get_option( 'services_title' );

	if ( '' === $gc_services_title ) {
		$gc_services_title = esc_html__( '我能提供什么', 'gridcraft-portfolio' );
	}
	?>
	<section id="gc-services" class="gc-section gc-section--soft">
		<div class="gc-container">
			<div class="gc-section__header gc-section__header--center">
				<span class="gc-section__eyebrow"><?php esc_html_e( 'Services', 'gridcraft-portfolio' ); ?></span>
				<h2 class="gc-section__title"><?php echo esc_html( $gc_services_title ); ?></h2>
			</div>

			<div class="gc-services">
				<?php
				foreach ( gridcraft_get_services() as $gc_service ) {
					gridcraft_service_card( $gc_service );
				}
				?>
			</div>
		</div>
	</section>
	<?php
endif;

// 客户区块。
if ( gridcraft_get_option( 'clients_enabled' ) ) :
	$gc_clients_count = (int) gridcraft_get_option( 'clients_count' );

	if ( $gc_clients_count < 2 ) {
		$gc_clients_count = 8;
	}

	$gc_clients_query = new WP_Query(
		array(
			'post_type'      => 'client',
			'post_status'    => 'publish',
			'posts_per_page' => $gc_clients_count,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);

	if ( $gc_clients_query->have_posts() ) :
		$gc_clients_title = (string) gridcraft_get_option( 'clients_title' );

		if ( '' === $gc_clients_title ) {
			$gc_clients_title = esc_html__( '合作过的客户', 'gridcraft-portfolio' );
		}
		?>
		<section id="gc-clients" class="gc-section gc-section--tight">
			<div class="gc-container">
				<div class="gc-section__header gc-section__header--center">
					<h2 class="gc-section__title"><?php echo esc_html( $gc_clients_title ); ?></h2>
				</div>

				<div class="gc-clients">
					<?php
					while ( $gc_clients_query->have_posts() ) :
						$gc_clients_query->the_post();

						$gc_website = get_post_meta( get_the_ID(), '_gridcraft_website', true );
						$gc_client_industry = get_post_meta( get_the_ID(), '_gridcraft_industry', true );
						?>
						<article class="gc-client">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="gc-client__logo">
									<?php the_post_thumbnail( 'gridcraft-tile', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
								</div>
							<?php endif; ?>

							<h3 class="gc-client__name"><?php the_title(); ?></h3>

							<?php if ( $gc_website ) : ?>
								<a class="gc-client__url" href="<?php echo esc_url( $gc_website ); ?>" rel="noopener noreferrer nofollow" target="_blank">
									<?php echo esc_html( wp_parse_url( $gc_website, PHP_URL_HOST ) ? wp_parse_url( $gc_website, PHP_URL_HOST ) : $gc_website ); ?>
								</a>
							<?php elseif ( $gc_client_industry ) : ?>
								<span class="gc-client__url"><?php echo esc_html( $gc_client_industry ); ?></span>
							<?php endif; ?>
						</article>
						<?php
					endwhile;
					?>
				</div>
			</div>
		</section>
		<?php
		wp_reset_postdata();
	endif;
endif;

// 约稿 CTA。
if ( gridcraft_get_option( 'cta_enabled' ) ) :
	$gc_cta_title = (string) gridcraft_get_option( 'cta_title' );
	$gc_cta_text  = (string) gridcraft_get_option( 'cta_text' );
	$gc_cta_label = (string) gridcraft_get_option( 'cta_button_label' );
	$gc_cta_url   = (string) gridcraft_get_option( 'cta_button_url' );

	if ( '' === $gc_cta_title ) {
		$gc_cta_title = esc_html__( '有项目想聊？', 'gridcraft-portfolio' );
	}

	if ( '' === $gc_cta_text ) {
		$gc_cta_text = esc_html__( '告诉我你的目标与预算，我会在两个工作日内回复可行方案与报价。', 'gridcraft-portfolio' );
	}

	if ( '' === $gc_cta_label ) {
		$gc_cta_label = esc_html__( '发起联系', 'gridcraft-portfolio' );
	}
	?>
	<section class="gc-section">
		<div class="gc-container">
			<div class="gc-cta">
				<h2><?php echo esc_html( $gc_cta_title ); ?></h2>
				<p><?php echo esc_html( $gc_cta_text ); ?></p>

				<?php if ( '' !== $gc_cta_url ) : ?>
					<a class="gc-button" href="<?php echo esc_url( $gc_cta_url ); ?>">
						<?php echo esc_html( $gc_cta_label ); ?>
						<?php echo gridcraft_inline_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 固定 SVG 表。 ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
endif;
?>

<?php
get_footer();

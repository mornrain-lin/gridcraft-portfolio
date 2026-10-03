/**
 * Gridcraft Portfolio 导航交互脚本。
 *
 * 负责：移动端菜单、二级菜单折叠、背景图加载失败时降级。
 */
( function () {
	'use strict';

	function initNavToggle() {
		var toggle = document.querySelector( '[data-gc-nav-toggle]' );

		if ( ! toggle ) {
			return;
		}

		var nav = document.getElementById( toggle.getAttribute( 'aria-controls' ) );

		if ( ! nav ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';

			toggle.setAttribute( 'aria-expanded', expanded ? 'false' : 'true' );
			nav.classList.toggle( 'is-open', ! expanded );
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( ! nav.classList.contains( 'is-open' ) ) {
				return;
			}

			if ( nav.contains( event.target ) || toggle.contains( event.target ) ) {
				return;
			}

			nav.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' !== event.key ) {
				return;
			}

			nav.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
		} );
	}

	/**
	 * 移动端二级菜单：点击父项展开/收起。
	 */
	function initSubmenuToggle() {
		var nav = document.getElementById( 'gc-primary-nav' );

		if ( ! nav ) {
			return;
		}

		var parents = nav.querySelectorAll( 'li:has(> .sub-menu)' );

		Array.prototype.forEach.call( parents, function ( item ) {
			var link = item.querySelector( ':scope > a' );

			if ( ! link ) {
				return;
			}

			link.addEventListener( 'click', function ( event ) {
				if ( window.matchMedia( '(min-width: 783px)' ).matches ) {
					return;
				}

				var expanded = item.classList.toggle( 'is-expanded' );

				event.preventDefault();
				link.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
			} );
		} );
	}

	/**
	 * Hero 背景图加载失败时移除 image 修饰类，避免空白遮罩。
	 */
	function initHeroFallback() {
		var hero = document.querySelector( '.gc-hero--image' );

		if ( ! hero ) {
			return;
		}

		var url = getComputedStyle( hero ).backgroundImage;

		if ( ! url || 'none' === url ) {
			hero.classList.remove( 'gc-hero--image' );
		}
	}

	/**
	 * 页面内锚点平滑滚动时补偿固定头部高度。
	 */
	function initAnchorOffset() {
		var header = document.getElementById( 'masthead' );

		if ( ! header ) {
			return;
		}

		document.addEventListener( 'click', function ( event ) {
			var link = event.target.closest( 'a[href^="#"]' );

			if ( ! link ) {
				return;
			}

			var id = link.getAttribute( 'href' );

			if ( ! id || '#' === id ) {
				return;
			}

			var target = document.getElementById( id.slice( 1 ) );

			if ( ! target ) {
				return;
			}

			event.preventDefault();

			var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
			var top = target.getBoundingClientRect().top + window.pageYOffset - 24;

			window.scrollTo( { top: top, behavior: reduce ? 'auto' : 'smooth' } );

			// 更新地址栏 hash，但不产生历史记录。
			if ( window.history && window.history.replaceState ) {
				window.history.replaceState( null, '', id );
			}
		} );
	}

	function boot() {
		initNavToggle();
		initSubmenuToggle();
		initHeroFallback();
		initAnchorOffset();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();

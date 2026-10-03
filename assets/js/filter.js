/**
 * Gridcraft Portfolio 作品分类筛选脚本。
 *
 * 用原生 fetch 请求 admin-ajax.php，无 jQuery、无第三方库。
 * 失败时回退到分类归档链接，保证功能可用。
 */
( function () {
	'use strict';

	var bar = document.querySelector( '[data-gridcraft-filter]' );
	var grid = document.querySelector( '[data-grid]' );
	var status = document.querySelector( '[data-gridcraft-status]' );

	if ( ! bar || ! grid ) {
		return;
	}

	var l10n = window.gridcraftFilter || {};
	var archiveUrl = bar.getAttribute( 'data-archive-url' ) || '';
	var activeSlug = '';

	var buttons = bar.querySelectorAll( '.gc-filter' );

	// 找到当前处于激活态的按钮。
	Array.prototype.forEach.call( buttons, function ( button ) {
		if ( button.classList.contains( 'is-active' ) ) {
			activeSlug = button.getAttribute( 'data-filter' );
		}
	} );

	/**
	 * 更新按钮激活态。
	 */
	function setActiveButton( slug ) {
		Array.prototype.forEach.call( buttons, function ( button ) {
			var isActive = button.getAttribute( 'data-filter' ) === slug;

			button.classList.toggle( 'is-active', isActive );
			button.setAttribute( 'aria-pressed', isActive ? 'true' : 'false' );
		} );
	}

	/**
	 * 更新状态提示。
	 */
	function setStatus( text ) {
		if ( status ) {
			status.textContent = text || '';
		}
	}

	/**
	 * 纯客户端筛选：隐藏/显示已渲染的卡片。
	 */
	function filterClientSide( slug ) {
		var visible = 0;
		var items = grid.querySelectorAll( '.gc-work' );

		Array.prototype.forEach.call( items, function ( item ) {
			var terms = ( item.getAttribute( 'data-terms' ) || '' ).split( ',' );
			var show = 'all' === slug || terms.indexOf( slug ) !== -1;

			item.hidden = ! show;

			if ( show ) {
				visible += 1;
			}
		} );

		setStatus( '' );
		return visible;
	}

	/**
	 * 请求服务端渲染分页内容。
	 */
	function fetchFromServer( term, button ) {
		grid.setAttribute( 'aria-busy', 'true' );
		setStatus( l10n.loading || '' );

		var body = new URLSearchParams();

		body.append( 'action', 'gridcraft_filter_works' );
		body.append( 'nonce', l10n.nonce || '' );
		body.append( 'term', String( term || 0 ) );
		body.append( 'paged', '1' );

		return fetch( l10n.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString()
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( result ) {
				grid.setAttribute( 'aria-busy', 'false' );

				if ( ! result || ! result.success ) {
					throw new Error( 'filter-failed' );
				}

				grid.innerHTML = result.data.html;
				setStatus( result.data.foundText || '' );

				var href = button ? button.getAttribute( 'data-href' ) : '';

				// 用 history.pushState 保持可分享的 URL，但不触发页面跳转。
				if ( href && window.history && window.history.pushState ) {
					window.history.pushState( { gridcraftFilter: true }, '', href );
				}
			} )
			.catch( function () {
				grid.setAttribute( 'aria-busy', 'false' );
				setStatus( l10n.error || '' );
			} );
	}

	/**
	 * 判断当前页是否为归档首页（可以直接客户端筛选）。
	 */
	function canFilterClientSide() {
		return archiveUrl === '' || window.location.pathname === archiveUrl || window.location.search === '';
	}

	bar.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '.gc-filter' );

		if ( ! button ) {
			return;
		}

		event.preventDefault();

		var slug = button.getAttribute( 'data-filter' ) || 'all';
		var term = parseInt( button.getAttribute( 'data-term' ) || '0', 10 );

		if ( slug === activeSlug ) {
			return;
		}

		activeSlug = slug;
		setActiveButton( slug );

		// 当前页内容量足够时用客户端筛选（瞬时响应），
		// 否则请求服务端按分类分页。
		var itemCount = parseInt( button.getAttribute( 'data-count' ) || '0', 10 );
		var rendered = grid.querySelectorAll( '.gc-work' ).length;

		if ( rendered > 0 && itemCount <= rendered ) {
			filterClientSide( slug );
			return;
		}

		if ( ! canFilterClientSide() ) {
			// 不在归档首页，客户端筛选数据不完整，直接跳转分类归档。
			var href = button.getAttribute( 'data-href' );

			if ( href ) {
				window.location.href = href;
			}

			return;
		}

		fetchFromServer( term, button );
	} );
} )();

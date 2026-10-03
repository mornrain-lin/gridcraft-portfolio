/**
 * Gridcraft Portfolio Lightbox 脚本。
 *
 * 手写实现，无任何依赖：
 * - 收集页面上带 data-gc-lightbox-item 的图片
 * - 支持键盘导航（Esc / ← /→）、焦点陷阱、点击遮罩关闭
 * - 锁定 body 滚动
 */
( function () {
	'use strict';

	var lightbox = document.querySelector( '[data-gc-lightbox]' );

	if ( ! lightbox ) {
		return;
	}

	var image = lightbox.querySelector( '[data-gc-lightbox-image]' );
	var caption = lightbox.querySelector( '[data-gc-lightbox-caption]' );
	var closeBtn = lightbox.querySelector( '[data-gc-lightbox-close]' );
	var prevBtn = lightbox.querySelector( '[data-gc-lightbox-prev]' );
	var nextBtn = lightbox.querySelector( '[data-gc-lightbox-next]' );

	if ( ! image ) {
		return;
	}

	var items = [];
	var current = -1;
	var lastFocused = null;

	/**
	 * 收集页面上所有可放大的图片。
	 */
	function collectItems() {
		var nodes = document.querySelectorAll( '[data-gc-lightbox-item]' );

		items = [];

		Array.prototype.forEach.call( nodes, function ( node ) {
			var link = node.closest( 'a' );

			// 已经是链接的图片（指向更大图）不重复处理。
			if ( link ) {
				return;
			}

			var full = node.getAttribute( 'data-full' ) || node.currentSrc || node.src;
			var figcaption = '';

			if ( node.closest( 'figure' ) ) {
				var cap = node.closest( 'figure' ).querySelector( 'figcaption' );
				figcaption = cap ? cap.textContent.trim() : '';
			}

			items.push( {
				src: full,
				alt: node.getAttribute( 'alt' ) || '',
				caption: figcaption,
				el: node
			} );
		} );
	}

	/**
	 * 显示指定索引的图片。
	 */
	function show( index ) {
		if ( ! items.length ) {
			return;
		}

		current = ( index + items.length ) % items.length;

		var item = items[ current ];

		image.setAttribute( 'src', item.src );
		image.setAttribute( 'alt', item.alt );

		if ( caption ) {
			caption.textContent = item.caption || '';
		}

		// 单张图片时隐藏翻页按钮。
		var multiple = items.length > 1;

		if ( prevBtn ) {
			prevBtn.style.display = multiple ? '' : 'none';
		}

		if ( nextBtn ) {
			nextBtn.style.display = multiple ? '' : 'none';
		}
	}

	function open( index ) {
		lastFocused = document.activeElement;

		lightbox.hidden = false;
		// 强制重排，保证 CSS transition 生效。
		void lightbox.offsetWidth;
		lightbox.classList.add( 'is-open' );
		document.body.classList.add( 'gc-lightbox-open' );

		show( index );

		if ( closeBtn ) {
			closeBtn.focus();
		}
	}

	function close() {
		lightbox.classList.remove( 'is-open' );
		document.body.classList.remove( 'gc-lightbox-open' );
		lightbox.hidden = true;
		image.removeAttribute( 'src' );

		if ( lastFocused && lastFocused.focus ) {
			lastFocused.focus();
		}

		current = -1;
	}

	/**
	 * 把焦点限制在 Lightbox 内。
	 */
	function trapFocus( event ) {
		if ( 'Tab' !== event.key ) {
			return;
		}

		var focusable = lightbox.querySelectorAll( 'button:not([style*="display: none"])' );
		var list = [];

		Array.prototype.forEach.call( focusable, function ( node ) {
			if ( node.style.display !== 'none' && ! node.disabled ) {
				list.push( node );
			}
		} );

		if ( ! list.length ) {
			return;
		}

		var first = list[ 0 ];
		var last = list[ list.length - 1 ];

		if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	}

	// 事件绑定：委托到文档，动态插入的图片也能响应。
	document.addEventListener( 'click', function ( event ) {
		var node = event.target.closest( '[data-gc-lightbox-item]' );

		if ( ! node ) {
			return;
		}

		collectItems();

		var index = items.findIndex( function ( item ) {
			return item.el === node;
		} );

		if ( index === -1 ) {
			return;
		}

		event.preventDefault();
		open( index );
	} );

	// 键盘可达：图片本身不可点，用 Enter / 空格触发。
	document.addEventListener( 'keydown', function ( event ) {
		if ( lightbox.classList.contains( 'is-open' ) ) {
			return;
		}

		if ( 'Enter' !== event.key && ' ' !== event.key && 'Spacebar' !== event.key ) {
			return;
		}

		var node = event.target.closest ? event.target.closest( '[data-gc-lightbox-item]' ) : null;

		if ( ! node ) {
			return;
		}

		event.preventDefault();

		collectItems();

		var index = items.findIndex( function ( item ) {
			return item.el === node;
		} );

		if ( index !== -1 ) {
			open( index );
		}
	} );

	if ( closeBtn ) {
		closeBtn.addEventListener( 'click', close );
	}

	if ( prevBtn ) {
		prevBtn.addEventListener( 'click', function () {
			show( current - 1 );
		} );
	}

	if ( nextBtn ) {
		nextBtn.addEventListener( 'click', function () {
			show( current + 1 );
		} );
	}

	// 点击遮罩空白处关闭（点在图片上不关闭）。
	lightbox.addEventListener( 'click', function ( event ) {
		if ( event.target === lightbox ) {
			close();
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( ! lightbox.classList.contains( 'is-open' ) ) {
			return;
		}

		if ( 'Escape' === event.key ) {
			event.preventDefault();
			close();
			return;
		}

		if ( 'ArrowLeft' === event.key ) {
			event.preventDefault();
			show( current - 1 );
			return;
		}

		if ( 'ArrowRight' === event.key ) {
			event.preventDefault();
			show( current + 1 );
			return;
		}

		trapFocus( event );
	} );
} )();

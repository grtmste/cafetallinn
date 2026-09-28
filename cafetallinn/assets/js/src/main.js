/**
 * Café Tallinn — front-end behaviour (vanilla JS, no dependencies).
 *
 * - Sticky header that condenses on scroll
 * - Full-screen mobile navigation
 * - Menüü / Joogikaart dialogs (focus trap, ESC, backdrop, deep links,
 *   scroll lock, return focus, category scroll-spy)
 * - Scroll-spy for the /menuu/ and /joogikaart/ pages
 * - Gallery lightbox (keyboard + swipe)
 * - Click-to-load map
 */
( function () {
	'use strict';

	var d = document;
	var root = d.documentElement;
	var L = window.cafetallinnL10n || {
		close: 'Sulge',
		prev: 'Eelmine pilt',
		next: 'Järgmine pilt',
		counter: '%1$s / %2$s',
		lightbox: 'Pildivaade'
	};
	var FOCUSABLE = 'a[href], area[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), iframe, [tabindex]:not([tabindex="-1"])';
	var motionQuery = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	function reduced() {
		return motionQuery.matches;
	}

	function visible( el ) {
		return !! ( el && ( el.offsetWidth || el.offsetHeight || el.getClientRects().length ) );
	}

	function focusables( containers ) {
		var list = [];
		containers.forEach( function ( c ) {
			if ( ! c ) {
				return;
			}
			if ( c.matches && c.matches( FOCUSABLE ) ) {
				list.push( c );
			}
			Array.prototype.forEach.call( c.querySelectorAll( FOCUSABLE ), function ( el ) {
				list.push( el );
			} );
		} );
		return list.filter( visible );
	}

	function trapTab( e, containers ) {
		var items = focusables( containers );
		if ( ! items.length ) {
			e.preventDefault();
			return;
		}
		var first = items[ 0 ];
		var last = items[ items.length - 1 ];
		var inside = items.indexOf( d.activeElement ) !== -1;
		if ( e.shiftKey && ( d.activeElement === first || ! inside ) ) {
			e.preventDefault();
			last.focus();
		} else if ( ! e.shiftKey && ( d.activeElement === last || ! inside ) ) {
			e.preventDefault();
			first.focus();
		}
	}

	function afterTransition( el, cb ) {
		if ( reduced() || ! el ) {
			cb();
			return;
		}
		var done = false;
		function finish( ev ) {
			if ( done || ( ev && ev.target !== el ) ) {
				return;
			}
			done = true;
			el.removeEventListener( 'transitionend', finish );
			cb();
		}
		el.addEventListener( 'transitionend', finish );
		window.setTimeout( finish, 450 );
	}

	function scrollOpts( top, left ) {
		var o = { behavior: reduced() ? 'auto' : 'smooth' };
		if ( typeof top === 'number' ) {
			o.top = top;
		}
		if ( typeof left === 'number' ) {
			o.left = left;
		}
		return o;
	}

	/* ------------------------------------------------------------------
	 * Scroll lock + inert background (shared by nav, dialogs, lightbox)
	 * ---------------------------------------------------------------- */
	var locks = 0;

	function lock() {
		if ( locks++ === 0 ) {
			root.style.setProperty( '--scrollbar', ( window.innerWidth - root.clientWidth ) + 'px' );
			root.classList.add( 'is-locked' );
		}
	}

	function unlock() {
		if ( locks > 0 && --locks === 0 ) {
			root.classList.remove( 'is-locked' );
			root.style.removeProperty( '--scrollbar' );
		}
	}

	function setInert( keep ) {
		Array.prototype.forEach.call( d.body.children, function ( el ) {
			if ( keep ) {
				var kept = keep.some( function ( k ) {
					return k && ( el === k || el.contains( k ) );
				} );
				if ( ! kept && ! el.hasAttribute( 'inert' ) && el.tagName !== 'SCRIPT' ) {
					el.setAttribute( 'inert', '' );
					el.setAttribute( 'data-ct-inert', '' );
				}
			} else if ( el.hasAttribute( 'data-ct-inert' ) ) {
				el.removeAttribute( 'inert' );
				el.removeAttribute( 'data-ct-inert' );
			}
		} );
	}

	/* ------------------------------------------------------------------
	 * Header
	 * ---------------------------------------------------------------- */
	var header = d.querySelector( '[data-site-header]' );
	var sentinel = d.querySelector( '.scroll-sentinel' );

	if ( header && sentinel && 'IntersectionObserver' in window ) {
		new IntersectionObserver( function ( entries ) {
			header.classList.toggle( 'is-condensed', ! entries[ 0 ].isIntersecting );
		} ).observe( sentinel );
	}

	/* ------------------------------------------------------------------
	 * Mobile navigation
	 * ---------------------------------------------------------------- */
	var toggle = d.querySelector( '[data-nav-toggle]' );
	var mnav = d.querySelector( '[data-mobile-nav]' );
	var toggleLabel = toggle ? toggle.querySelector( '[data-nav-toggle-label]' ) : null;
	var navOpen = false;

	function openNav() {
		if ( ! mnav || navOpen ) {
			return;
		}
		navOpen = true;
		mnav.hidden = false;
		void mnav.offsetWidth;
		mnav.classList.add( 'is-open' );
		toggle.setAttribute( 'aria-expanded', 'true' );
		if ( toggleLabel ) {
			toggleLabel.textContent = toggleLabel.getAttribute( 'data-close' );
		}
		root.classList.add( 'nav-open' );
		lock();
		setInert( [ header, mnav ] );
		var first = mnav.querySelector( 'a' );
		if ( first ) {
			first.focus( { preventScroll: true } );
		}
	}

	function closeNav( returnFocus ) {
		if ( ! navOpen ) {
			return;
		}
		navOpen = false;
		mnav.classList.remove( 'is-open' );
		toggle.setAttribute( 'aria-expanded', 'false' );
		if ( toggleLabel ) {
			toggleLabel.textContent = toggleLabel.getAttribute( 'data-open' );
		}
		root.classList.remove( 'nav-open' );
		unlock();
		setInert( null );
		afterTransition( mnav, function () {
			if ( ! navOpen ) {
				mnav.hidden = true;
			}
		} );
		if ( returnFocus !== false ) {
			toggle.focus( { preventScroll: true } );
		}
	}

	if ( toggle && mnav ) {
		toggle.addEventListener( 'click', function () {
			if ( navOpen ) {
				closeNav();
			} else {
				openNav();
			}
		} );
		mnav.addEventListener( 'click', function ( e ) {
			var a = e.target.closest( 'a' );
			if ( a && ! a.hasAttribute( 'data-modal' ) && a.hash && a.pathname === window.location.pathname ) {
				closeNav( false );
			}
		} );
		window.matchMedia( '(min-width: 1024px)' ).addEventListener( 'change', function ( mq ) {
			if ( mq.matches ) {
				closeNav( false );
			}
		} );
	}

	/* ------------------------------------------------------------------
	 * Scroll-spy
	 * ---------------------------------------------------------------- */
	function makeSpy( nav, sections, scroller, getOffset, atBottom ) {
		var links = {};
		var current = null;
		var ticking = false;

		Array.prototype.forEach.call( nav.querySelectorAll( 'a[href^="#"]' ), function ( a ) {
			links[ a.getAttribute( 'href' ).slice( 1 ) ] = a;
		} );

		function reveal( a ) {
			var list = a.closest( '.menu-tabs__list' );
			if ( ! list || list.scrollWidth <= list.clientWidth + 2 ) {
				return;
			}
			var li = a.parentElement;
			var target = li.offsetLeft - ( list.clientWidth - li.offsetWidth ) / 2;
			list.scrollTo( scrollOpts( undefined, Math.max( 0, target ) ) );
		}

		function setActive( id ) {
			if ( id === current ) {
				return;
			}
			current = id;
			Object.keys( links ).forEach( function ( key ) {
				if ( key === id ) {
					links[ key ].setAttribute( 'aria-current', 'true' );
					reveal( links[ key ] );
				} else {
					links[ key ].removeAttribute( 'aria-current' );
				}
			} );
		}

		function update() {
			ticking = false;
			if ( ! sections.length ) {
				return;
			}
			var id = sections[ 0 ].id;
			for ( var i = 0; i < sections.length; i++ ) {
				if ( getOffset( sections[ i ] ) <= 0 ) {
					id = sections[ i ].id;
				} else {
					break;
				}
			}
			if ( atBottom() ) {
				id = sections[ sections.length - 1 ].id;
			}
			setActive( id );
		}

		( scroller === window ? window : scroller ).addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( update );
			}
		}, { passive: true } );

		return { update: update, setActive: setActive };
	}

	/* ------------------------------------------------------------------
	 * Menüü / Joogikaart dialogs
	 * ---------------------------------------------------------------- */
	var modals = {};
	var activeModal = null;
	var lastTrigger = null;

	Array.prototype.forEach.call( d.querySelectorAll( '[data-menu-modal]' ), function ( m ) {
		var body = m.querySelector( '[data-modal-body]' );
		var nav = m.querySelector( '.menu-tabs' );
		var sections = Array.prototype.slice.call( m.querySelectorAll( '.menu-section' ) );
		var spy = null;

		if ( nav && body ) {
			spy = makeSpy(
				nav,
				sections,
				body,
				function ( s ) {
					return s.offsetTop - body.scrollTop - 96;
				},
				function () {
					return body.scrollTop + body.clientHeight >= body.scrollHeight - 4;
				}
			);
			nav.addEventListener( 'click', function ( e ) {
				var a = e.target.closest( 'a[href^="#"]' );
				if ( ! a ) {
					return;
				}
				var target = m.querySelector( a.getAttribute( 'href' ) );
				if ( ! target ) {
					return;
				}
				e.preventDefault();
				body.scrollTo( scrollOpts( Math.max( 0, target.offsetTop - 16 ) ) );
				spy.setActive( target.id );
				history.replaceState( null, '', '#' + target.id );
			} );
		}

		m.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( '[data-close]' ) ) {
				e.preventDefault();
				closeModal();
			}
		} );

		modals[ m.id ] = { el: m, body: body, spy: spy };
	} );

	function modalFromHash( hash ) {
		var h = decodeURIComponent( ( hash || '' ).replace( /^#/, '' ) );
		if ( ! h ) {
			return null;
		}
		for ( var id in modals ) {
			if ( h === id ) {
				return { id: id, section: null };
			}
			if ( h.indexOf( id + '-' ) === 0 && modals[ id ].el.querySelector( '#' + CSS.escape( h ) ) ) {
				return { id: id, section: h };
			}
		}
		return null;
	}

	function openModal( id, trigger, sectionId ) {
		var entry = modals[ id ];
		if ( ! entry ) {
			return false;
		}
		var swapping = !! activeModal && activeModal !== entry;
		if ( activeModal === entry ) {
			return true;
		}
		if ( swapping ) {
			closeModal( { swap: true } );
		} else {
			lastTrigger = trigger || d.activeElement;
		}
		if ( navOpen ) {
			closeNav( false );
		}

		var m = entry.el;
		m.hidden = false;
		void m.offsetWidth;
		m.classList.add( 'is-open' );
		activeModal = entry;
		lock();
		setInert( [ m ] );

		var target = sectionId ? m.querySelector( '#' + CSS.escape( sectionId ) ) : null;
		entry.body.scrollTop = target ? Math.max( 0, target.offsetTop - 16 ) : 0;
		if ( entry.spy ) {
			entry.spy.update();
		}
		history.replaceState( null, '', '#' + ( sectionId || id ) );
		m.querySelector( '.menu-modal__close' ).focus( { preventScroll: true } );
		return true;
	}

	function closeModal( opts ) {
		opts = opts || {};
		var entry = activeModal;
		if ( ! entry ) {
			return;
		}
		activeModal = null;
		var m = entry.el;
		m.classList.remove( 'is-open' );
		unlock();
		setInert( null );

		if ( opts.swap ) {
			m.hidden = true;
			return;
		}
		afterTransition( m.querySelector( '.menu-modal__panel' ), function () {
			if ( activeModal !== entry ) {
				m.hidden = true;
			}
		} );
		if ( modalFromHash( window.location.hash ) ) {
			history.replaceState( null, '', window.location.pathname + window.location.search );
		}
		var back = lastTrigger;
		lastTrigger = null;
		if ( back && d.contains( back ) && visible( back ) ) {
			back.focus( { preventScroll: true } );
		} else if ( toggle && visible( toggle ) ) {
			toggle.focus( { preventScroll: true } );
		}
	}

	var TRIGGERS = '[data-modal], .js-open-menu > a, .js-open-drinks > a, a.js-open-menu, a.js-open-drinks, a[href$="#menyy"], a[href$="#joogikaart"]';

	d.addEventListener( 'click', function ( e ) {
		if ( e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey ) {
			return;
		}
		var a = e.target.closest( TRIGGERS );
		if ( ! a ) {
			return;
		}
		var id = a.getAttribute( 'data-modal' );
		if ( ! id ) {
			if ( a.closest( '.js-open-drinks' ) ) {
				id = 'joogikaart';
			} else if ( a.closest( '.js-open-menu' ) ) {
				id = 'menyy';
			} else {
				id = ( a.getAttribute( 'href' ) || '' ).split( '#' ).pop();
			}
		}
		if ( ! modals[ id ] ) {
			return;
		}
		e.preventDefault();
		openModal( id, activeModal ? lastTrigger : a );
	} );

	d.addEventListener( 'keydown', function ( e ) {
		if ( lightbox.isOpen() ) {
			return;
		}
		if ( activeModal ) {
			if ( e.key === 'Escape' ) {
				e.preventDefault();
				closeModal();
			} else if ( e.key === 'Tab' ) {
				trapTab( e, [ activeModal.el.querySelector( '.menu-modal__panel' ) ] );
			}
			return;
		}
		if ( navOpen ) {
			if ( e.key === 'Escape' ) {
				e.preventDefault();
				closeNav();
			} else if ( e.key === 'Tab' ) {
				trapTab( e, [ toggle, mnav ] );
			}
		}
	} );

	function handleHash() {
		var hit = modalFromHash( window.location.hash );
		if ( hit ) {
			openModal( hit.id, null, hit.section );
		} else if ( activeModal ) {
			closeModal();
		}
	}
	window.addEventListener( 'hashchange', handleHash );
	handleHash();

	/* ------------------------------------------------------------------
	 * Scroll-spy on the menu pages
	 * ---------------------------------------------------------------- */
	var pageTabs = d.querySelector( '[data-spy-tabs]' );
	var pageBody = d.querySelector( '[data-spy-body]' );
	if ( pageTabs && pageBody ) {
		var pageSections = Array.prototype.slice.call( pageBody.querySelectorAll( '.menu-section' ) );
		var pageSpy = makeSpy(
			pageTabs,
			pageSections,
			window,
			function ( s ) {
				var line = pageTabs.getBoundingClientRect().bottom + 24;
				return s.getBoundingClientRect().top - line;
			},
			function () {
				return window.innerHeight + window.scrollY >= d.documentElement.scrollHeight - 4;
			}
		);
		pageSpy.update();
	}

	/* ------------------------------------------------------------------
	 * Lightbox
	 * ---------------------------------------------------------------- */
	var ICON = {
		close: '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="m5 5 14 14M19 5 5 19"/></svg>',
		prev: '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg>',
		next: '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="m9 5 7 7-7 7"/></svg>'
	};
	var IMG_RE = /\.(jpe?g|png|webp|gif|avif)(\?.*)?$/i;

	var lightbox = ( function () {
		var box = null;
		var img;
		var caption;
		var counter;
		var items = [];
		var index = 0;
		var trigger = null;
		var open = false;
		var startX = 0;
		var startY = 0;

		function build() {
			box = d.createElement( 'div' );
			box.className = 'lightbox';
			box.hidden = true;
			box.setAttribute( 'role', 'dialog' );
			box.setAttribute( 'aria-modal', 'true' );
			box.setAttribute( 'aria-label', L.lightbox );
			box.innerHTML =
				'<div class="lightbox__bar"><span class="lightbox__counter" aria-live="polite"></span>' +
				'<button type="button" class="lightbox__btn" data-lb="close" aria-label="' + L.close + '">' + ICON.close + '</button></div>' +
				'<div class="lightbox__stage" data-lb="stage">' +
				'<button type="button" class="lightbox__btn lightbox__nav lightbox__nav--prev" data-lb="prev" aria-label="' + L.prev + '">' + ICON.prev + '</button>' +
				'<img class="lightbox__img" alt="" decoding="async">' +
				'<button type="button" class="lightbox__btn lightbox__nav lightbox__nav--next" data-lb="next" aria-label="' + L.next + '">' + ICON.next + '</button>' +
				'</div><p class="lightbox__caption"></p>';
			d.body.appendChild( box );
			img = box.querySelector( '.lightbox__img' );
			caption = box.querySelector( '.lightbox__caption' );
			counter = box.querySelector( '.lightbox__counter' );

			box.addEventListener( 'click', function ( e ) {
				var btn = e.target.closest( '[data-lb]' );
				var what = btn ? btn.getAttribute( 'data-lb' ) : '';
				if ( what === 'close' || ( what === 'stage' && e.target === btn ) ) {
					close();
				} else if ( what === 'prev' ) {
					show( index - 1 );
				} else if ( what === 'next' ) {
					show( index + 1 );
				}
			} );
			box.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Escape' ) {
					e.preventDefault();
					close();
				} else if ( e.key === 'ArrowLeft' ) {
					show( index - 1 );
				} else if ( e.key === 'ArrowRight' ) {
					show( index + 1 );
				} else if ( e.key === 'Tab' ) {
					trapTab( e, [ box ] );
				}
			} );
			var stage = box.querySelector( '.lightbox__stage' );
			stage.addEventListener( 'touchstart', function ( e ) {
				startX = e.changedTouches[ 0 ].clientX;
				startY = e.changedTouches[ 0 ].clientY;
			}, { passive: true } );
			stage.addEventListener( 'touchend', function ( e ) {
				var dx = e.changedTouches[ 0 ].clientX - startX;
				var dy = e.changedTouches[ 0 ].clientY - startY;
				if ( Math.abs( dx ) > 45 && Math.abs( dx ) > Math.abs( dy ) ) {
					show( index + ( dx < 0 ? 1 : -1 ) );
				}
			}, { passive: true } );
		}

		function describe( a ) {
			var fig = a.closest( 'figure' );
			var cap = fig ? fig.querySelector( 'figcaption' ) : null;
			var im = a.querySelector( 'img' );
			return {
				src: a.getAttribute( 'href' ),
				alt: im ? im.getAttribute( 'alt' ) || '' : '',
				caption: cap ? cap.textContent.trim() : ''
			};
		}

		function preload( i ) {
			if ( items.length > 1 ) {
				var n = items[ ( i + items.length ) % items.length ];
				( new Image() ).src = n.src;
			}
		}

		function show( i ) {
			if ( ! items.length ) {
				return;
			}
			index = ( i + items.length ) % items.length;
			var item = items[ index ];
			img.classList.add( 'is-loading' );
			var pre = new Image();
			pre.onload = pre.onerror = function () {
				if ( items[ index ] !== item ) {
					return;
				}
				img.src = item.src;
				img.alt = item.alt;
				img.classList.remove( 'is-loading' );
			};
			pre.src = item.src;
			caption.textContent = item.caption || item.alt;
			counter.textContent = L.counter.replace( '%1$s', index + 1 ).replace( '%2$s', items.length );
			box.querySelectorAll( '.lightbox__nav' ).forEach( function ( b ) {
				b.hidden = items.length < 2;
			} );
			preload( index + 1 );
			preload( index - 1 );
		}

		function openAt( list, i, from ) {
			if ( ! box ) {
				build();
			}
			items = list;
			trigger = from;
			open = true;
			box.hidden = false;
			void box.offsetWidth;
			box.classList.add( 'is-open' );
			lock();
			setInert( [ box ] );
			show( i );
			box.querySelector( '[data-lb="close"]' ).focus( { preventScroll: true } );
		}

		function close() {
			if ( ! open ) {
				return;
			}
			open = false;
			box.classList.remove( 'is-open' );
			unlock();
			setInert( null );
			afterTransition( box, function () {
				if ( ! open ) {
					box.hidden = true;
					img.removeAttribute( 'src' );
				}
			} );
			if ( trigger && d.contains( trigger ) ) {
				trigger.focus( { preventScroll: true } );
			}
		}

		return {
			openAt: openAt,
			isOpen: function () {
				return open;
			},
			describe: describe
		};
	}() );

	d.addEventListener( 'click', function ( e ) {
		if ( e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey ) {
			return;
		}
		var a = e.target.closest( '[data-lightbox-group] a[href]' );
		if ( ! a || ! IMG_RE.test( a.getAttribute( 'href' ) ) ) {
			return;
		}
		var group = a.closest( '[data-lightbox-group]' );
		var links = Array.prototype.filter.call( group.querySelectorAll( 'a[href]' ), function ( l ) {
			return IMG_RE.test( l.getAttribute( 'href' ) ) && l.querySelector( 'img' );
		} );
		var i = links.indexOf( a );
		if ( i === -1 ) {
			return;
		}
		e.preventDefault();
		lightbox.openAt( links.map( lightbox.describe ), i, a );
	} );

	/* ------------------------------------------------------------------
	 * Click-to-load map (no Google request before consent)
	 * ---------------------------------------------------------------- */
	d.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-map-load]' );
		if ( ! btn ) {
			return;
		}
		var facade = btn.closest( '.map-facade' );
		var frame = d.createElement( 'iframe' );
		frame.src = facade.getAttribute( 'data-map-src' );
		frame.title = facade.getAttribute( 'data-map-title' ) || '';
		frame.loading = 'lazy';
		frame.referrerPolicy = 'no-referrer-when-downgrade';
		frame.setAttribute( 'allowfullscreen', '' );
		facade.replaceChildren( frame );
		frame.setAttribute( 'tabindex', '0' );
		frame.focus();
	} );
}() );

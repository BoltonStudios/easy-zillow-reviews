/**
 * Easy Zillow Reviews Premium: reviews carousel.
 */
( function () {
	'use strict';

	var reduceMotion = !! ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches );

	function setup( container ) {
		var track = container.querySelector( '.ezrwp-carousel-track' );
		var controls = container.querySelector( '.ezrwp-carousel-controls' );
		var prev = container.querySelector( '.ezrwp-carousel-prev' );
		var next = container.querySelector( '.ezrwp-carousel-next' );
		var status = container.querySelector( '.ezrwp-carousel-status' );
		var pause = container.querySelector( '.ezrwp-carousel-pause' );
		var autoplay = parseInt( container.getAttribute( 'data-autoplay' ), 10 ) || 0;
		var paused = false;
		var hovering = false;
		var focused = false;
		var frame = 0;

		if ( ! track || container.hasAttribute( 'data-ezrwp-ready' ) ) {
			return;
		}
		container.setAttribute( 'data-ezrwp-ready', '1' );

		// The distance between pages: the visible width plus the gap between reviews.
		function gap() {
			return parseFloat( window.getComputedStyle( track ).columnGap ) || 0;
		}

		// The distance from one review to the next.
		function step() {
			var first = track.children[ 0 ];
			return first ? first.offsetWidth + gap() : track.clientWidth + gap();
		}

		// How many whole reviews fit in view (part of the next one may also show on phones).
		function perPage() {
			return Math.max( 1, Math.floor( ( track.clientWidth + gap() + 1 ) / step() ) );
		}

		// The distance between pages.
		function pageWidth() {
			return perPage() * step();
		}

		function pages() {
			return Math.max( 1, Math.ceil( track.children.length / perPage() ) );
		}

		function atStart() {
			return track.scrollLeft <= 1;
		}

		function atEnd() {
			return track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
		}

		// Size the track to the tallest review in view, so short reviews don't leave a gap
		// below them when a long review elsewhere in the carousel is much taller.
		function fitHeight() {
			var view = track.getBoundingClientRect();
			var tallest = 0;

			Array.prototype.forEach.call( track.children, function ( slide ) {
				var rect = slide.getBoundingClientRect();
				if ( rect.right > view.left + 1 && rect.left < view.right - 1 ) {
					tallest = Math.max( tallest, slide.offsetHeight );
				}
			} );

			if ( tallest > 0 ) {
				track.style.height = tallest + 'px';
			}
		}

		function update() {
			fitHeight();

			var total = pages();
			var current = atEnd() ? total : Math.min( total, Math.round( track.scrollLeft / pageWidth() ) + 1 );

			controls.hidden = total <= 1;
			prev.disabled = atStart();
			next.disabled = atEnd();
			status.textContent = current + ' / ' + total;
		}

		function go( direction ) {
			track.scrollBy( { left: direction * pageWidth(), behavior: reduceMotion ? 'auto' : 'smooth' } );
		}

		prev.addEventListener( 'click', function () {
			go( -1 );
		} );

		next.addEventListener( 'click', function () {
			go( 1 );
		} );

		track.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'ArrowRight' || event.key === 'ArrowLeft' ) {
				event.preventDefault();
				go( event.key === 'ArrowRight' ? 1 : -1 );
			}
		} );

		track.addEventListener( 'scroll', function () {
			cancelAnimationFrame( frame );
			frame = requestAnimationFrame( update );
		} );

		window.addEventListener( 'resize', update );

		track.classList.add( 'ezrwp-adaptive-height' );

		// Re-fit when a review changes height, e.g. "More" expands it or an image loads.
		if ( typeof ResizeObserver === 'function' ) {
			var observer = new ResizeObserver( function () {
				cancelAnimationFrame( frame );
				frame = requestAnimationFrame( update );
			} );
			Array.prototype.forEach.call( track.children, function ( slide ) {
				observer.observe( slide );
			} );
		} else {
			track.addEventListener( 'click', function () {
				setTimeout( update, 50 );
			} );
		}

		// Autoplay pauses on hover and keyboard focus, and never runs for visitors who prefer reduced motion.
		if ( autoplay > 0 && ! reduceMotion ) {
			status.setAttribute( 'aria-live', 'off' );

			container.addEventListener( 'mouseenter', function () {
				hovering = true;
			} );
			container.addEventListener( 'mouseleave', function () {
				hovering = false;
			} );
			container.addEventListener( 'focusin', function () {
				focused = true;
			} );
			container.addEventListener( 'focusout', function () {
				focused = false;
			} );

			if ( pause ) {
				pause.addEventListener( 'click', function () {
					paused = ! paused;
					pause.setAttribute( 'aria-pressed', paused ? 'true' : 'false' );
					pause.textContent = pause.getAttribute( paused ? 'data-label-play' : 'data-label-pause' );
				} );
			}

			setInterval( function () {
				if ( paused || hovering || focused || document.hidden ) {
					return;
				}
				if ( atEnd() ) {
					track.scrollTo( { left: 0, behavior: 'smooth' } );
				} else {
					go( 1 );
				}
			}, autoplay * 1000 );
		} else {
			status.setAttribute( 'aria-live', 'polite' );
			if ( pause ) {
				pause.parentNode.removeChild( pause );
			}
		}

		update();
	}

	function init() {
		Array.prototype.forEach.call( document.querySelectorAll( '.ezrwp-carousel-container' ), setup );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
	window.addEventListener( 'load', init );
} )();

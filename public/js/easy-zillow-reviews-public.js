/**
 * Easy Zillow Reviews: "More" / "Less" toggles for long reviews.
 */
( function () {
	'use strict';

	function setExpanded( button, text, expanded ) {
		var ellipsis = button.parentNode.querySelector( '.ellipsis' );

		text.hidden = ! expanded;
		if ( ellipsis ) {
			ellipsis.hidden = expanded;
		}
		button.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
		button.classList.toggle( 'open', expanded );
		button.textContent = expanded ? 'Less' : 'More';
	}

	function init() {
		var buttons = document.querySelectorAll( '[data-ezrwp-toggle]' );

		Array.prototype.forEach.call( buttons, function ( button ) {
			var text = document.getElementById( button.getAttribute( 'data-ezrwp-toggle' ) );

			if ( text && ! button.hasAttribute( 'data-ezrwp-ready' ) ) {
				button.setAttribute( 'data-ezrwp-ready', '1' );
				text.hidden = true;
				button.parentNode.hidden = false;
			}
		} );

		// Markup cached before version 1.7.0: show the "More" button and hide the rest of the text.
		Array.prototype.forEach.call( document.querySelectorAll( '.ezrwp-read-more[id]' ), function ( wrap ) {
			var text = wrap.previousElementSibling;

			wrap.style.display = '';
			if ( text && text.classList.contains( 'ezrwp-toggle' ) ) {
				text.style.display = 'none';
			}
		} );
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest ? event.target.closest( '[data-ezrwp-toggle]' ) : null;

		if ( ! button ) {
			return;
		}

		var text = document.getElementById( button.getAttribute( 'data-ezrwp-toggle' ) );

		if ( text ) {
			event.preventDefault();
			setExpanded( button, text, text.hidden );
		}
	} );

	// Support inline onclick handlers in pages cached before version 1.7.0.
	window.ezrwpToggleReadMore = function ( wrapperId, reviewId ) {
		var wrapper = document.getElementById( 'ezrwp-wrapper-' + wrapperId );
		var text = wrapper && wrapper.querySelector( '#ezrwp-toggle-' + reviewId );
		var readMore = wrapper && wrapper.querySelector( '#ezrwp-read-more-' + reviewId );
		var button = readMore && readMore.querySelector( 'button' );
		var ellipsis = readMore && readMore.querySelector( '.ellipsis' );

		if ( ! text || ! button ) {
			return;
		}

		var expand = text.style.display === 'none';
		text.style.display = expand ? '' : 'none';
		if ( ellipsis ) {
			ellipsis.style.display = expand ? 'none' : '';
		}
		button.textContent = expand ? 'Less' : 'More';
		button.classList.toggle( 'open', expand );
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

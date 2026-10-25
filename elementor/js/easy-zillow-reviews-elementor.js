/**
 * Easy Zillow Reviews: start reviews that Elementor adds or re-renders in the editor.
 */
( function () {
	'use strict';

	function ready( $scope ) {
		var root = $scope && $scope[ 0 ] ? $scope[ 0 ] : null;

		if ( ! root ) {
			return;
		}
		if ( window.ezrwpReadMoreInit ) {
			window.ezrwpReadMoreInit( root );
		}
		if ( window.ezrwpCarouselInit ) {
			window.ezrwpCarouselInit( root );
		}
	}

	var registered = false;

	// Elementor 4 dispatches a native event; older versions trigger it through jQuery.
	function register() {
		if ( registered || ! window.elementorFrontend || ! window.elementorFrontend.hooks ) {
			return;
		}
		registered = true;
		window.elementorFrontend.hooks.addAction( 'frontend/element_ready/easy-zillow-reviews.default', ready );
	}

	window.addEventListener( 'elementor/frontend/init', register );
	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', register );
	}
} )();

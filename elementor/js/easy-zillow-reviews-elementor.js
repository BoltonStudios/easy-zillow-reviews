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

	window.addEventListener( 'elementor/frontend/init', function () {
		window.elementorFrontend.hooks.addAction( 'frontend/element_ready/easy-zillow-reviews.default', ready );
	} );
} )();

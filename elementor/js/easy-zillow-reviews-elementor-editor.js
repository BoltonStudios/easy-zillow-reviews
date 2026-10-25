/**
 * Easy Zillow Reviews: Elementor editor tweaks.
 *
 * Hides Elementor's "Cache Settings" while editing the Zillow Reviews widget. The widget is never cached, so the
 * setting would have no effect.
 */
( function ( $ ) {
	'use strict';

	var className = 'ezrwp-editing-widget';

	function editing( isOurs ) {
		document.body.classList.toggle( className, !! isOurs );
	}

	$( window ).on( 'elementor:init', function () {
		var hooks = window.elementor && window.elementor.hooks;

		if ( ! hooks ) {
			return;
		}

		hooks.addAction( 'panel/open_editor/widget', function ( panel, model ) {
			editing( model && model.get( 'widgetType' ) === 'easy-zillow-reviews' );
		} );

		[ 'section', 'column', 'container' ].forEach( function ( type ) {
			hooks.addAction( 'panel/open_editor/' + type, function () {
				editing( false );
			} );
		} );
	} );

	$( function () {
		$( '<style>' )
			.text( 'body.' + className + ' .elementor-control-_element_cache { display: none !important; }' )
			.appendTo( document.head );
	} );
} )( jQuery );

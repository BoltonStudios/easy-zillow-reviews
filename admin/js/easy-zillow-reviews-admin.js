(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
    $(document).ready(function() {
        ezrwp_admin_scripts();
    });
})( jQuery );

function ezrwp_admin_scripts(){

	// Get the HTML elements to modify
    var ezrwp_layout = document.getElementsByClassName('ezrwp_layout')[0];
	var ezrwp_cols = document.getElementsByClassName('ezrwp_cols')[0];
	var ezrwp_disclaimer = document.getElementById('ezrwp_disclaimer');

    // Toggle grid columns field based on layout selected
	if( ezrwp_layout != null && ezrwp_cols != null ){

		ezrwp_cols.disabled = ezrwp_layout.value == 'list' ? true : false;
		ezrwp_layout.addEventListener('change', function () {
			ezrwp_cols.disabled = this.value == 'list' ? true : false;
		});
	}
    // Toggle disclaimer notice based on disclaimer setting
	if( ezrwp_disclaimer != null ){

		style = ezrwp_disclaimer.value == 1 ? 'block' : 'none';
		document.getElementById('disclaimer-warning').style.display = style;
		document.getElementById('ezrwp_disclaimer').addEventListener('change', function () {
			var style = this.value == 1 ? 'block' : 'none';
			document.getElementById('disclaimer-warning').style.display = style;
		});   
	}
}
// Test the API connection with the values currently entered on the settings page.
document.addEventListener( 'click', function ( event ) {
	var button = event.target.closest ? event.target.closest( '.ezrwp-test-connection button' ) : null;

	if ( ! button ) {
		return;
	}

	var container = button.parentNode;
	var result = container.querySelector( '.ezrwp-test-connection-result' );
	var fields = JSON.parse( container.getAttribute( 'data-fields' ) || '{}' );
	var body = new URLSearchParams();

	body.append( 'action', 'ezrwp_test_connection' );
	body.append( '_ajax_nonce', container.getAttribute( 'data-nonce' ) );
	body.append( 'type', container.getAttribute( 'data-type' ) );

	Object.keys( fields ).forEach( function ( key ) {
		var input = document.getElementById( fields[ key ] );
		body.append( 'fields[' + key + ']', input ? input.value : '' );
	} );

	button.disabled = true;
	result.className = 'ezrwp-test-connection-result';
	result.textContent = 'Testing…';

	fetch( window.ajaxurl, { method: 'POST', credentials: 'same-origin', body: body } )
		.then( function ( response ) {
			return response.json();
		} )
		.then( function ( response ) {
			result.className = 'ezrwp-test-connection-result ' + ( response.success ? 'is-success' : 'is-error' );
			result.textContent = response.data && response.data.message ? response.data.message : 'Unexpected response.';
		} )
		.catch( function () {
			result.className = 'ezrwp-test-connection-result is-error';
			result.textContent = 'The test could not be completed. Please try again.';
		} )
		.then( function () {
			button.disabled = false;
		} );
} );

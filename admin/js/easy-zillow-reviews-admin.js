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
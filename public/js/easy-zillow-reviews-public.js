(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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

    // Check if the document is ready.
    $( function() {

        // For each "read more" element, i.e., review...
        $( ".ezrwp-read-more" ).each( function(){

            // Display the Read More button.
            $( this ).show();
        });

        // For each review (toggle element).
        $( ".ezrwp-toggle" ).each( function(){

            // Hide the non-excerpt text.
            $( this ).hide();
        });
    });

})( jQuery );

// Define the onclick action event for the Read More button.
function ezrwpToggleReadMore( wrapperId, reviewId ){

    // Define targets.
    var textToggle = '#ezrwp-wrapper-' + wrapperId + ' #ezrwp-toggle-' + reviewId;
    var buttonToggle = '#ezrwp-wrapper-' + wrapperId + ' #ezrwp-read-more-' + reviewId;

    // Get the element, i.e., the review text, associated with the clicked button.
    $( textToggle ).each( function(){

        // Toggle its visibility.
        $( this ).toggle();
    });

    // Hide the Read More button.
    $( buttonToggle ).hide();
}
/**
 * Hello World: Step 1
 *
 * Simple block, renders and saves the same content without interactivity.
 *
 * Using inline styles - no external stylesheet needed.  Not recommended
 * because all of these styles will appear in `post_content`.
 */
( function( blocks, element ) {
	var el = element.createElement;

	blocks.registerBlockType( 'easy-zillow-reviews/professional-reviews', {
		title: 'Zillow Professional Reviews',
		category: 'widgets',
        example: {},        
        edit: function( props ) {
            return el(
                'p',
                { className: props.className },
				'This is the Zillow Reviews block (from the editor).'
			);
		},
		save: function() {
			return el(
				'p',
				{},
				'This is the Zillow Reviews block (from the frontend).'
			);
		},
	} );
} )( window.wp.blocks, window.wp.element );

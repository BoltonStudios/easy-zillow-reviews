<?php
/*
* Easy Zillow Reviews Premium Includes
*/
require_once( EASYZILLOWREVIEWS_PATH . 'premium/class-lender.php' );
require_once( EASYZILLOWREVIEWS_PATH . 'premium/class-lender-shortcodes.php' );

/**
 * Easy Zillow Reviews Main Class
 */
if ( ! class_exists( 'EasyZillowReviews_Premium' ) ) {
	define( 'EASYZILLOWREVIEWS_PREMIUM_PATH', plugin_dir_path( __FILE__ ) );
	define( 'EASYZILLOWREVIEWS_PREMIUM_URL', plugin_dir_url( __FILE__ ) );
	class EasyZillowReviews_Premium {
		function __construct() {
			new EasyZillowReviewsLenderShortcodes();
        }
    }
}
?>
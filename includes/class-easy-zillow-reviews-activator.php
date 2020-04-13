<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.boltonstudios.com
 * @since      1.0.0
 *
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */
class Easy_Zillow_Reviews_Activator {
    
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        
        /**
         * Migrate old plugin options to new format
         *
         * @since    1.1.1
         */
        $old_options = get_option('ezrwp_options');
        $general_options = get_option('ezrwp_general_options'); // General admin tab settings
        $professional_reviews_options = get_option('ezrwp_professional_reviews_options'); // Professionals Reviews admin tab settings
        
        if( $old_options != null){

            // Migrate ezrwp_options to ezrwp_general_options
            $general_options["ezrwp_count"] = $old_options["ezrwp_count"];
            $general_options["ezrwp_layout"] = $old_options["ezrwp_layout"];
            $general_options["ezrwp_disclaimer"] = $old_options["ezrwp_disclaimer"];
            $general_options["ezrwp_hide_date"] = $old_options["ezrwp_hide_date"];
            $general_options["ezrwp_quote_font_size"] = $old_options["ezrwp_quote_font_size"];
            $general_options["ezrwp_review_description_font_size"] = $old_options["ezrwp_review_description_font_size"];
            update_option('ezrwp_general_options', $general_options, null );
            
            // Migrate ezrwp_options to ezrwp_professional_reviews_options
            $professional_reviews_options["ezrwp_zwsid"] = $old_options["ezrwp_zwsid"];
            $professional_reviews_options["ezrwp_screenname"] = $old_options["ezrwp_screenname"];
            update_option('ezrwp_professional_reviews_options', $professional_reviews_options, null );

            // Delete ezrwp_options
            delete_option('ezrwp_options');
        }
	}

}

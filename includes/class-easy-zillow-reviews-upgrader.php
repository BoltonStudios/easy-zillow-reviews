<?php

/**
 * This class defines all code necessary to migrate old plugin options to the new format.
 *
 * @since      1.1.2
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */
class Easy_Zillow_Reviews_Upgrader {
    
	// Constructor
	/**
	 * Create upgrader class to handle behind-the-scenes changes necessary for new features.
	 *
	 *
	 * @since    1.1.2
	 */
	public function __construct() {
		$this->init();
	}
	public static function init() {
        
        /**
         * Migrate old plugin options to new format.
         *
         * @since    1.1.1
         */
        $old_options = get_option('ezrwp_options');
        $general_options = get_option('ezrwp_general_options'); // General admin tab settings
        $professional_reviews_options = get_option('ezrwp_professional_reviews_options'); // Professionals Reviews admin tab settings
        
        if( $old_options != null ){

            // Migrate ezrwp_options to ezrwp_general_options
            $general_options["ezrwp_count"] = $old_options["ezrwp_count"];
            $general_options["ezrwp_layout"] = $old_options["ezrwp_layout"];
            $general_options["ezrwp_cols"] = $old_options["ezrwp_cols"];
            $general_options["ezrwp_disclaimer"] = $old_options["ezrwp_disclaimer"];
            $general_options["ezrwp_hide_date"] = $old_options["ezrwp_hide_date"];
            $general_options["ezrwp_quote_font_size"] = $old_options["ezrwp_quote_font_size"];
            $general_options["ezrwp_review_description_font_size"] = $old_options["ezrwp_review_description_font_size"];
            $general_options["ezrwp_hide_view_all_link"] = $old_options["ezrwp_hide_view_all_link"];
            $general_options["ezrwp_hide_zillow_logo"] = $old_options["ezrwp_hide_zillow_logo"];
            $general_options["ezrwp_hide_stars"] = $old_options["ezrwp_hide_stars"];
            $general_options["ezrwp_hide_reviewer_summary"] = $old_options["ezrwp_hide_reviewer_summary"];
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

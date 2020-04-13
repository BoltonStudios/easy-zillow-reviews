<?php

/**
 * The Easy_Zillow_Reviews_Professional_Shortcodes class
 *
 * Adds the [ez-zillow-reviews] shortcode to WordPress
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */
    
class Easy_Zillow_Reviews_Professional_Shortcodes extends Easy_Zillow_Reviews_Professional{

    // Constructor
    function __construct(){
        
        add_action('plugins_loaded', array($this, 'init'));
    }

    // Methods
    function init(){
        
        add_shortcode('ez-zillow-reviews', array($this, 'display_professional_reviews'));
    }
    function display_professional_reviews($atts){

        // Get saved admin settings and defaults
        $general_options = get_option('ezrwp_general_options'); // General admin tab settings
        $professional_reviews_options = get_option('ezrwp_professional_reviews_options'); // Professionals Reviews admin tab settings
        $layout = isset($general_options['ezrwp_layout']) ? $general_options['ezrwp_layout'] : 'list';
        $grid_columns = isset($general_options['ezrwp_cols']) ? $general_options['ezrwp_cols'] : 3;
        $count = isset($general_options['ezrwp_count']) ? $general_options['ezrwp_count'] : 3;
        
        // Pass saved admin settings to this Easy_Zillow_Reviews_Lender_Shortcodes class instance
        $this->set_general_options($general_options);
        $this->set_professional_reviews_options($professional_reviews_options);
        $this->set_layout($layout);
        $this->set_grid_columns($grid_columns);
        $this->set_count($count);
        $this->set_show_team_members(true);

        // Get attributes from shortcode
        if( isset( $atts ) ){
            $atts = shortcode_atts( array(
                // Defaults passed from admin settings above.
                'layout' => $this->get_layout(),
                'columns' => $this->get_grid_columns(),
                'count' => $this->get_count()
            ), $atts );
            $layout = $atts[ 'layout' ];
            $cols = $atts[ 'columns' ];
            $count = $atts[ 'count' ];
        }

        // Review count cannot be more than 10 or less than 0.
        $count = ($count > 10 ) ? 10 : $count;
        $count = floor($count) > 0 ? floor( $count ) : 1;

        // Fetch reviews from Zillow
        $this->fetch_reviews_from_zillow( $count );

        // Render output
        if( $this->get_has_reviews() ){

            // Success
            $output = $this->layout_reviews( $layout, $cols );
        } else {

            // Error
            $output = '<p>Unable to load reviews. Zillow says: <strong>'. $this -> get_message() .'</strong>.</p>';
        }
        return $output;
    }
}
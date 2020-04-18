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
    
class Easy_Zillow_Reviews_Professional_Shortcodes{

	/**
	 *
	 *
	 * @since    1.1.4
	 * @access   private
	 * @var      Easy_Zillow_Reviews_Professional    $zillow_professional_data    
	 */
    private $zillow_professional_data;

    // Constructor
    function __construct( $zillow_professional_data ){

        $this->zillow_professional_data = $zillow_professional_data;

        add_action('plugins_loaded', array($this, 'init'));
    }

    // Methods
    function init(){
        
        add_shortcode('ez-zillow-reviews', array($this, 'display_professional_reviews'));
    }
    function display_professional_reviews($atts){
        
        // Get saved admin settings and defaults
        $reviews = $this->get_zillow_professional_data();

        // Get attributes from shortcode
        if( isset( $atts ) ){
            $atts = shortcode_atts( array(
                // Defaults passed from admin settings above.
                'layout' => $reviews->get_layout(),
                'columns' => $reviews->get_grid_columns(),
                'count' => $reviews->get_count()
            ), $atts );
            $layout = $atts[ 'layout' ];
            $cols = $atts[ 'columns' ];
            $count = $atts[ 'count' ];
        }

        // Review count cannot be more than 10 or less than 0.
        $count = ($count > 10 ) ? 10 : $count;
        $count = floor($count) > 0 ? floor( $count ) : 1;

        // Fetch reviews from Zillow
        $reviews->fetch_reviews_from_zillow( $count );

        // Render output
        if( $reviews->get_has_reviews() ){

            // Success
            $output = $reviews->layout_reviews( $layout, $cols );
        } else {

            // Error
            $output = '<p>Unable to load reviews. Zillow says: <strong>'. $reviews -> get_message() .'</strong>.</p>';
        }
        return $output;
    }
    
    /**
     * Get the value of zillow_professional_data
     *
     * @since    1.1.0
     */
    public function get_zillow_professional_data(){

        return $this->zillow_professional_data;
    }

    /**
     * Set the value of zillow_professional_data
     *
     * @return  self
     */ 
    public function set_zillow_professional_data( $zillow_professional_data ){

        $this->zillow_professional_data = $zillow_professional_data;
        return $this;
    }
}
<?php

/**
 * The Easy_Zillow_Reviews_Lender_Shortcodes class
 *
 * Adds the [ez-zillow-lender-reviews] shortcode to WordPress
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

if ( ! class_exists( 'Easy_Zillow_Reviews_Lender_Shortcodes' ) ) {
    
    class Easy_Zillow_Reviews_Lender_Shortcodes{

        /**
         *
         *
         * @since    1.1.4
         * @access   private
         * @var      Easy_Zillow_Reviews_Lender   $zillow_lender_data    
         */
        private $zillow_lender_data;

        // Constructor
        function __construct( $zillow_lender_data ){

            $this->zillow_lender_data = $zillow_lender_data;

            add_action('plugins_loaded', array($this, 'init'));
        }

        // Methods
        function init(){
            
            add_shortcode('ez-zillow-lender-reviews', array($this, 'display_lender_reviews'));
        }
        function display_lender_reviews($atts){

            // Get saved admin settings and defaults
            $reviews = $this->get_zillow_lender_data();

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
            $count = floor($count) > 0 ? floor($count) : 1;

            // Fetch reviews from Zillow
            $reviews->fetch_reviews_from_zillow($count);

            // Render output
            if( $reviews -> get_has_reviews() ){

                // Success
                $output = $reviews -> layout_lender_reviews($layout, $cols);
            } else {

                // Error
                $output = '<p>Unable to load reviews. Zillow says: <strong>'. $reviews -> get_message() .'</strong>.</p>';
            }
            return $output;
        }
        
        /**
         * Get the value of zillow_lender_data
         *
         * @since    1.1.0
         */
        public function get_zillow_lender_data(){

            return $this->zillow_lender_data;
        }

        /**
         * Set the value of zillow_lender_data
         *
         * @return  self
         */ 
        public function set_zillow_lender_data( $zillow_lender_data ){

            $this->zillow_lender_data = $zillow_lender_data;
            return $this;
        }
    }
}
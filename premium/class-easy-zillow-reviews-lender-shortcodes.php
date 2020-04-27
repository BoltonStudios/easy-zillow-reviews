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
        function display_lender_reviews( $atts ){
            
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
            return $reviews->get_reviews_output( $reviews, $layout, $cols, $count );
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
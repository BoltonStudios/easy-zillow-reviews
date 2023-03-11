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
    
if ( ! class_exists( 'Easy_Zillow_Reviews_Professional_Shortcodes' ) ) {
    
    class Easy_Zillow_Reviews_Professional_Shortcodes{

        /**
         * Initialize variables.
         */

        /**
         *
         *
         * @since    1.1.4
         * @access   private
         * @var      Easy_Zillow_Reviews_Professional    $zillow_professional_data    
         */
        private $zillow_professional_data;

        // Define the constructor method.
        function __construct( $zillow_professional_data ){

            // Update the local $zillow_professional_data property.
            $this->zillow_professional_data = $zillow_professional_data;

            // Run the init() function once the activated plugins have loaded.
            add_action( 'plugins_loaded', array( $this, 'init' ) );
        }

        /**
         * Define additional methods.
         */

        // Define a helper function to initialize the class object.
        function init(){
            
            // Create a shortcode that calls the 'display_professional_reviews' function.
            add_shortcode( 'ez-zillow-reviews', array( $this, 'display_professional_reviews' ) );
        }

        // Define a function that extracts the shortcode attributes and returns the HTML reviews output.
        function display_professional_reviews( $shortcode_attributes ){

            // Initialize variables.
            $reviews = $this->get_zillow_professional_data();
            $reviews_layout = $reviews->get_layout();
            $number_of_columns = $reviews->get_grid_columns();
            $number_of_reviews = $reviews->get_count();
            $screenname = $reviews->get_screenname();
            $word_limit = $reviews->get_word_limit();

            // Define the default shortcode attributes.
            $default_attributes = array(
                'layout' => $reviews_layout,
                'columns' => $number_of_columns,
                'count' => $number_of_reviews,
                'screenname' => $screenname,
                'excerpt' => $word_limit
            );

            // If the $shortcode_attributes argument is not null...
            if( isset( $shortcode_attributes ) ){

                // Get attributes from the $shortcode_attributes argument.
                $shortcode_attributes = shortcode_atts( $default_attributes, $shortcode_attributes );

                // Update the relevant local variables with the values from the $shortcode_attributes argument.
                $reviews_layout = $shortcode_attributes[ 'layout' ];
                $number_of_columns = $shortcode_attributes[ 'columns' ];
                $number_of_reviews = $shortcode_attributes[ 'count' ];
                $screenname = $shortcode_attributes[ 'screenname' ];
                $word_limit = $shortcode_attributes[ 'excerpt' ];
            }

            // Pass the shortcode parameters and get the reviews from Zillow.
            $output = $reviews->get_reviews_output( $reviews, $reviews_layout, $number_of_columns, $number_of_reviews, $screenname, $word_limit );

            // Return the output.
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
}
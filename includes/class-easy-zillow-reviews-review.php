<?php

/**
 * The Easy_Zillow_Reviews_Review class
 *
 * 
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.5.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

if ( ! class_exists( 'Easy_Zillow_Reviews_Review' ) ) {

    class Easy_Zillow_Reviews_Review{

        /**
         * The review quotation.
         *
         * @since    1.5.0
         * @access   private
         * @var      string    $description
         */
        private $description;

        /**
         * Identifing qualities of the reviewer, 
         * e.g., "helped me buy home"
         *
         * @since    1.5.0
         * @access   private
         * @var      string    $summary
         */
        private $summary;

        /**
         * A link to the reviewee's reviews.
         *
         * @since    1.5.0
         * @access   private
         * @var      string    $url
         */
        private $url;

        /**
         * The date the review was published.
         *
         * @since    1.5.0
         * @access   private
         * @var      string    $date
         */
        private $date;

        /**
         * A rating from 1.0 to 5.0
         *
         * @since    1.5.0
         * @access   private
         * @var      int    $rating
         */
        private $rating;

        /**
         * The location pertaining to the review.
         *
         * @since    1.5.0
         * @access   private
         * @var      string    $city
         */
        private $city;
        
        // Constructor
        public function __construct( $description, $summary, $url, $date, $rating, $city = "" ){

            // Initialize object properties.
            $this->description = $description;
            $this->url = $url;
            $this->date = $date;
            $this->rating = $rating;
            $this->city = $city;
            
            // Adjust the summary parameter value.
            switch ( strtolower( $summary ) ){
                
                case "helped me buy home" :
                    
                    $summary = "bought a home";
                    break;

                case "showed me homes" :
                    
                    $summary = "shopped for a new home";
                    break;

                default:
                
                    // Do not adjust the summary parameter value.
                    break;
            }

            // If the city property is not blank...
            if( $city != '' ){

                // Add the city to the local summary text.
                $summary .= ' in ' . $city;
            }

            // Update the summary instance variable.
            $this->summary = $summary .= '.';
        }

        // Methods
        public function init(){
        }

        /**
         * Get $date
         *
         * @return  string
         */ 
        public function get_date()
        {
                return $this->date;
        }

        /**
         * Set $date
         *
         * @param  string  $date
         *
         * @return  self
         */ 
        public function set_date(string $date)
        {
                $this->date = $date;

                return $this;
        }

        /**
         * Get $description
         *
         * @return  string
         */ 
        public function get_description()
        {
                return $this->description;
        }

        /**
         * Set $description
         *
         * @param  string  $description
         *
         * @return  self
         */ 
        public function set_description(string $description)
        {
                $this->description = $description;

                return $this;
        }

        /**
         * Get $summary
         *
         * @return  string
         */ 
        public function get_summary()
        {
                return $this->summary;
        }

        /**
         * Set $summary
         *
         * @param  string  $summary
         *
         * @return  self
         */ 
        public function set_summary(string $summary)
        {
                $this->summary = $summary;

                return $this;
        }

        /**
         * Get $url
         *
         * @return  string
         */ 
        public function get_url()
        {
                return $this->url;
        }

        /**
         * Set $url
         *
         * @param  string  $url
         *
         * @return  self
         */ 
        public function set_url(string $url)
        {
                $this->url = $url;

                return $this;
        }

        /**
         * Get $rating
         *
         * @return  int
         */ 
        public function get_rating()
        {
                return $this->rating;
        }

        /**
         * Set $rating
         *
         * @param  int  $rating
         *
         * @return  self
         */ 
        public function set_rating(int $rating)
        {
                $this->rating = $rating;

                return $this;
        }

        /**
         * Get $city
         *
         * @return  string
         */ 
        public function get_city()
        {
                return $this->city;
        }

        /**
         * Set $city
         *
         * @param  string  $city
         *
         * @return  self
         */ 
        public function set_city(string $city)
        {
                $this->city = $city;

                return $this;
        }
    }
}
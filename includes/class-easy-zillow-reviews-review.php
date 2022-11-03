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
         * A description goes here.
         *
         * @since    1.5.0
         * @access   private
         * @var      string    $description
         */
        private $description;

        /**
         * A description goes here.
         *
         * @since    1.5.0
         * @access   private
         * @var      string    $summary
         */
        private $summary;

        /**
         * A description goes here.
         *
         * @since    1.5.0
         * @access   private
         * @var      string    $url
         */
        private $url;

        /**
         * A description goes here.
         *
         * @since    1.5.0
         * @access   private
         * @var      string    $date
         */
        private $date;

        /**
         * A description goes here.
         *
         * @since    1.5.0
         * @access   private
         * @var      int    $rating
         */
        private $rating;
        
        // Constructor
        public function __construct( $description, $summary, $url, $date, $rating ){

            // Initialize object properties.
            $this->description = $description;
            $this->summary = $summary;
            $this->url = $url;
            $this->date = $date;
            $this->rating = $rating;
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
         * @param  string  $date  $date
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
         * @param  string  $description  $description
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
         * @param  string  $summary  $summary
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
         * @param  string  $url  $url
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
         * @param  int  $rating  $rating
         *
         * @return  self
         */ 
        public function set_rating(int $rating)
        {
                $this->rating = $rating;

                return $this;
        }
    }
}
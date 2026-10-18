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


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
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
            
            // Rephrase the service description from the reviewer's point of view.
            $phrases = array(
                'helped me buy home'               => 'bought a home',
                'helped me buy a home'             => 'bought a home',
                'helped me sell home'              => 'sold a home',
                'helped me sell a home'            => 'sold a home',
                'helped me buy and sell homes'     => 'bought and sold homes',
                'showed me homes'                  => 'shopped for a new home',
                'listed and sold a home'           => 'listed and sold a home',
                'helped me find a home to rent'    => 'rented a home',
                'helped me find tenant for rental' => 'found a tenant for a rental',
            );

            $key = strtolower( trim( (string) $summary ) );

            if( isset( $phrases[ $key ] ) ){
                $summary = $phrases[ $key ];
            } elseif( $key === '' || strpos( $key, 'helped me' ) === 0 || preg_match( '/^\d+$/', $key ) ){

                // Skip descriptions that would not read well after "who".
                $summary = 'worked with this agent';
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
         * The name of the reviewed team member, for team reviews.
         *
         * @since    1.7.0
         * @var      string
         */
        private $reviewee_name = '';

        /**
         * The Zillow profile URL of the reviewed team member, for team reviews.
         *
         * @since    1.7.0
         * @var      string
         */
        private $reviewee_url = '';

        /**
         * Get the reviewed team member's name.
         *
         * @since    1.7.0
         * @return   string
         */
        public function get_reviewee_name(){
            return $this->reviewee_name;
        }

        /**
         * Get the reviewed team member's Zillow profile URL.
         *
         * @since    1.7.0
         * @return   string
         */
        public function get_reviewee_url(){
            return $this->reviewee_url;
        }

        /**
         * Set the reviewed team member. The review links to their profile.
         *
         * @since    1.7.0
         * @param    string    $name    The team member's name.
         * @param    string    $url     The team member's Zillow profile URL.
         */
        public function set_reviewee( $name, $url ){
            $this->reviewee_name = (string) $name;
            $this->reviewee_url = (string) $url;
            $this->url = trailingslashit( $url ) . '#reviews';
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
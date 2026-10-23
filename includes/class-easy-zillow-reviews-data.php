<?php

/**
 * The Easy_Zillow_Reviews_Template class
 *
 * 
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Easy_Zillow_Reviews_Data' ) ) {

    class Easy_Zillow_Reviews_Data{

        /**
         * The unique identifier of this plugin.
         *
         * @since    1.1.5
         * @access   protected
         * @var      string    $plugin_slug    The string used to uniquely identify this plugin.
         */
        protected $plugin_slug;
        
        /**
         * The layout for reviews.
         * The user may select "grid" or "list" from the shortcode, widget, or admin settings.
         *
         * @since    1.1.0
         * @access   private
         * @var      string   $layout  TThe layout for reviews.
         */
        private $layout;

        /**
         * The number of grid columns in the grid layout.
         * The user may select a number between 2 and 6.
         *
         * @since    1.1.0
         * @access   private
         * @var      int   $grid_columns    The number of grid columns in the grid layout.
         */
        private $grid_columns;
        
        /**
         * The number of reviews to display.
         * The user may select a number between 1 and 10.
         *
         * @since    1.1.0
         * @access   private
         * @var      int   $count   The number of reviews to display.
         */
        private $count;
        
        /**
         * The state of having reviews or not having reviews.
         * This is determined by the results of the plugin's call to the Zillow API Network.
         *
         * @since    1.1.0
         * @access   private
         * @var      boolean   $has_reviews   The state of having reviews or not having reviews.
         */
        private $has_reviews;
        
        /**
         * The message returned by the Zillow API Network.
         * This is determined by the results of the plugin's call to the Zillow API Network.
         *
         * @since    1.1.0
         * @access   private
         * @var      string   $message   The message returned by the Zillow API Network.
         */
        private $message;

        /**
         * Extra request options, e.g. from shortcode attributes.
         *
         * @since    1.7.0
         * @access   protected
         * @var      array    $request_args
         */
        protected $request_args = array();
        
        /**
         * The user's saved plugin options.
         *
         * @since    1.1.0
         * @access   public
         * @var      object   $general_options   The user's saved plugin options.
         */
        public $general_options;
        
        /**
         * The URL for the individual review returned by the Zillow API Network.
         *
         * @since    1.1.0
         * @access   private
         * @var      string   $url   The URL for the individual review returned by the Zillow API Network.
         */
        private $url;
        
        /**
         * The profile information for the Zillow account selected by the user.
         * This is determined by the results of the plugin's call to the Zillow API Network.
         * 
         * @since    1.1.0
         * @access   private
         * @var      SimpleXMLElement   $info
         */
        private $info;
        
        /**
         * The name of the reviewee returned by the Zillow API Network.
         *
         * @since    1.5.0
         * @access   private
         * @var      string   $profile_name 
         */
        private $profile_name;
        
        /**
         * The image URL for the reviewee either uploaded to WordPress or returned by the Zillow API response.
         *
         * @since    1.5.0
         * @access   private
         * @var      string   $profile_image_url
         */
        private $profile_image_url;
        
        /**
         * The option to hide the time elapsed since the review was written.
         * Determined by options selected on Settings page.
         * 
         * @since    1.1.0
         * @access   private
         * @var      bool   $hide_date   
         */
        private $hide_date;
        
        /**
         * The option to hide Zillow's Mandatory Disclaimer.
         * Determined by options selected on Settings page.
         * 
         * @since    1.1.4
         * @access   private
         * @var      bool   $hide_disclaimer  
         */
        private $hide_disclaimer;
        
        /**
         * The option to hide the star rating.
         * Determined by options selected on Settings page.
         * 
         * @since    1.1.4
         * @access   private
         * @var      bool   $hide_stars 
         */
        private $hide_stars;
        
        /**
         * The option to hide the short description of the reviewer.
         * Determined by options selected on Settings page.
         * 
         * @since    1.1.4
         * @access   private
         * @var      bool   $hide_reviewer_summary 
         */
        private $hide_reviewer_summary;
        
        /**
         * The option to hide the average star rating for the Zillow profile.
         * Determined by options selected on Settings page.
         * 
         * @since    1.2.1
         * @access   private
         * @var      bool   $hide_profile_card
         */
        private $hide_profile_card;
        
        /**
         * The option to hide the link to the real estate professional directory on Zillow.com.
         * Determined by options selected on Settings page.
         * 
         * @since    1.1.4
         * @access   private
         * @var      bool   $hide_view_all_link  
         */
        private $hide_view_all_link;
        
        /**
         * The option to hide the Zillow logo.
         * Determined by options selected on Settings page.
         * 
         * @since    1.1.4
         * @access   private
         * @var      bool   $hide_zillow_logo  
         */
        private $hide_zillow_logo;

        /**
         * The average rating of all the published reviews. 
         * This value will not be present if the lender has no reviews.
         *
         * @since    1.2.1
         * @access   private
         * @var      int   $rating
         */
        private $rating;
        
        /**
         * The total number of sales that this profile has on Zillow.
         * This is determined by the results of the plugin's call to the Zillow API Network.
         *
         * @since    1.5.0
         * @access   private
         * @var      int   $sale_count   
         */
        private $sale_count;
        
        /**
         * The total number of reviews that this profile has on Zillow.
         * This is determined by the results of the plugin's call to the Zillow API Network.
         *
         * @since    1.1.0
         * @access   private
         * @var      int   $review_count   
         */
        private $review_count;
        
        /**
         * The reviews returned by the plugin's call to the Zillow API Network.
         *
         * @since    1.1.0
         * @access   protected
         * @var      array   $reviews   
         */
        protected $reviews;
        
        /**
         * The font size for the review quote text in pixels.
         * Determined by options selected on Settings page.
         * 
         * @since    1.1.4
         * @access   protected
         * @var      int   $quote_font_size  
         */
        protected $quote_font_size;
        
        /**
         * The font size for the reviewer description text in pixels.
         * Determined by options selected on Settings page.
         * 
         * @since    1.1.4
         * @access   protected
         * @var      int   $reviewer_description_font_size  
         */
        protected $reviewer_description_font_size;
        
        /**
         * The layouts available for the user to select.
         * Determined by plugin edition installed (free or paid editions).
         *
         * @since    1.2.0
         * @access   public
         * @var      array   $available_layouts
         */
        public $available_layouts;
        
        /**
         * The APIs available for the user to select.
         * Determined by plugin edition installed (free or paid editions).
         * 
         * @since    1.2.0
         * @access   public
         * @var      array   $available_apis
         */
        public $available_apis;

        // Constructor
        public function __construct(){
        }

        // Methods
        public function init(){

            // Get saved admin settings and defaults
            $general_options = get_option('ezrwp_general_options'); // General admin tab settings
            $layout = isset($general_options['ezrwp_layout']) ? $general_options['ezrwp_layout'] : 'list';
            $grid_columns = isset($general_options['ezrwp_cols']) ? $general_options['ezrwp_cols'] : 3;
            $count = isset($general_options['ezrwp_count']) ? $general_options['ezrwp_count'] : 3;
            $hide_date = isset($general_options['ezrwp_hide_date']) ? $general_options['ezrwp_hide_date'] : 0;
            $hide_disclaimer = isset($general_options['ezrwp_disclaimer']) ? $general_options['ezrwp_disclaimer'] : 0;
            $hide_stars = isset($general_options['ezrwp_hide_stars']) ? $general_options['ezrwp_hide_stars'] : 0;
            $hide_reviewer_summary = isset($general_options['ezrwp_hide_reviewer_summary']) ? $general_options['ezrwp_hide_reviewer_summary'] : 0;
            $hide_profile_card = isset($general_options['ezrwp_hide_profile_card']) ? $general_options['ezrwp_hide_profile_card'] : 0;
            $hide_view_all_link = isset($general_options['ezrwp_hide_view_all_link']) ? $general_options['ezrwp_hide_view_all_link'] : 0;
            $hide_zillow_logo = isset($general_options['ezrwp_hide_zillow_logo']) ? $general_options['ezrwp_hide_zillow_logo'] : 0;
            $quote_font_size = isset($general_options['ezrwp_quote_font_size']) ? $general_options['ezrwp_quote_font_size'] : 18;
            $reviewer_description_font_size = isset($general_options['ezrwp_review_description_font_size']) ? $general_options['ezrwp_review_description_font_size'] : 16;
            /**
             * Filters the available review layouts.
             *
             * @since 1.7.0
             * @param string[] $layouts Layout slugs.
             */
            $available_layouts = apply_filters( 'ezrwp_available_layouts', array( 'list', 'grid' ) );
            $available_apis = array( ['professional', 'Professional'] );

            // Store options in this class instance
            $this->set_general_options($general_options);
            $this->set_layout($layout);
            $this->set_grid_columns($grid_columns);
            $this->set_count($count);
            $this->set_hide_date(($hide_date == 1) ? true : false);
            $this->set_hide_disclaimer(($hide_disclaimer == 1) ? true : false);
            $this->set_hide_stars(($hide_stars == 1) ? true : false);
            $this->set_hide_reviewer_summary(($hide_reviewer_summary == 1) ? true : false);
            $this->set_hide_profile_card(($hide_profile_card == 1) ? true : false);
            $this->set_hide_view_all_link(($hide_view_all_link == 1) ? true : false);
            $this->set_hide_zillow_logo(($hide_zillow_logo == 1) ? true : false);
            $this->set_quote_font_size($quote_font_size);
            $this->set_reviewer_description_font_size($reviewer_description_font_size);
            $this->set_available_layouts($available_layouts);
            $this->set_available_apis($available_apis);
        }
        
        /**
         * 
         * 
         *
         * @since     1.2.0
         * @return    string    The name of the plugin.
         */
        public function get_reviews_output( $reviews, $layout, $cols, $number_of_reviews, $screenname = null, $word_limit = null, $args = array() ) {

            // Load the front-end assets only on pages that display reviews.
            wp_enqueue_style( 'easy-zillow-reviews' );
            wp_enqueue_script( 'easy-zillow-reviews' );

            // Extra options from shortcode, widget, or block attributes (e.g., Premium options).
            $reviews->set_request_args( is_array( $args ) ? $args : array() );

            // Sanitize the options, which may come from shortcode attributes, widgets, or blocks.
            $available_layouts = apply_filters( 'ezrwp_available_layouts', array( 'list', 'grid' ) );
            $layout = sanitize_key( (string) $layout );

            // "Slider" is the user-facing name of the carousel layout.
            $layout = ( $layout === 'slider' ) ? 'carousel' : $layout;
            $layout = in_array( $layout, $available_layouts, true ) ? $layout : $this->get_layout();
            $layout = in_array( $layout, $available_layouts, true ) ? $layout : 'list';
            $cols = min( max( absint( $cols ), 1 ), 6 );
            $number_of_reviews = min( max( absint( $number_of_reviews ), 1 ), 10 );
            $screenname = ( $screenname === null || $screenname === '' ) ? null : sanitize_text_field( (string) $screenname );
            $word_limit = ( $word_limit === null || $word_limit === '' || absint( $word_limit ) < 1 ) ? null : absint( $word_limit );

            // Fetch reviews from Zillow
            $reviews->fetch_reviews_from_zillow( $number_of_reviews, $screenname, $word_limit );

            // Render output
            if( $reviews -> get_has_reviews() ){

                // Success
                return $reviews -> layout_reviews( $layout, $cols );
            }

            // Only show API error details to site administrators.
            if( current_user_can( 'manage_options' ) ){

                return '<p class="ezrwp-error">' . esc_html__( 'Unable to load reviews. Zillow says:', 'easy-zillow-reviews' ) . ' <strong>' . esc_html( $reviews -> get_message() ) . '</strong> <em>' . esc_html__( '(This message is only visible to site administrators.)', 'easy-zillow-reviews' ) . '</em></p>';
            }

            return '';
        }

        /**
         * Send a GET request to a reviews API and decode the JSON response.
         *
         * Identical requests within the same page load share a single response,
         * so multiple shortcodes or widgets do not repeat API calls. Responses are
         * never stored beyond the current request (Zillow's terms prohibit caching).
         *
         * @since     1.6.5
         * @param     string    $url        The request URL.
         * @param     array     $headers    Optional request headers.
         * @return    array     array( 'code' => int, 'data' => mixed, 'error' => string )
         */
        public function remote_get_json( $url, $headers = array() ) {

            static $responses = array();

            $key = md5( $url . serialize( $headers ) );

            if( isset( $responses[ $key ] ) ){
                return $responses[ $key ];
            }

            $response = wp_remote_get(
                $url,
                array(
                    'timeout' => apply_filters( 'ezrwp_api_timeout', 8 ),
                    'headers' => $headers,
                )
            );

            if( is_wp_error( $response ) ){

                $result = array(
                    'code'  => 0,
                    'data'  => null,
                    'error' => $response->get_error_message(),
                );

            } else{

                $data = json_decode( wp_remote_retrieve_body( $response ) );

                $result = array(
                    'code'  => (int) wp_remote_retrieve_response_code( $response ),
                    'data'  => $data,
                    'error' => ( $data === null ) ? 'Invalid response from the reviews API' : '',
                );
            }

            $responses[ $key ] = $result;

            return $result;
        }

        /**
         * Build the escaped HTML for a review's text, truncated to a word limit with a "More" toggle.
         *
         * @since     1.6.5
         * @param     string    $description    The raw review text from the API.
         * @param     int|null  $word_limit     The maximum number of words to show before the toggle.
         * @param     string    $wrapper_id     The identifier of the reviews wrapper element.
         * @param     int       $i              The index of the review within the wrapper.
         * @return    string
         */
        protected function format_review_text( $description, $word_limit, $wrapper_id, $i ) {

            // Collapse whitespace, including line breaks.
            $description = trim( preg_replace( '/\s+/u', ' ', (string) $description ) );

            $word_limit = absint( $word_limit );

            if( $word_limit < 1 ){
                return esc_html( $description );
            }

            // Split the text into words, keeping each word's trailing whitespace.
            $words = preg_split( '/(?<=\s)/u', $description, -1, PREG_SPLIT_NO_EMPTY );

            if( ! is_array( $words ) || count( $words ) <= $word_limit ){
                return esc_html( $description );
            }

            $excerpt = rtrim( implode( '', array_slice( $words, 0, $word_limit ) ) );
            $rest = ' ' . implode( '', array_slice( $words, $word_limit ) );

            $toggle_id = 'ezrwp-toggle-' . sanitize_html_class( $wrapper_id ) . '-' . absint( $i );

            // The "More" button is hidden until JavaScript runs, so the full text is readable without JavaScript.
            $read_more_link  = '<span class="ezrwp-read-more" hidden><span class="ellipsis" aria-hidden="true">...</span>';
            $read_more_link .= '<button type="button" aria-expanded="false" aria-controls="' . esc_attr( $toggle_id ) . '" data-ezrwp-toggle="' . esc_attr( $toggle_id ) . '">' . esc_html__( 'More', 'easy-zillow-reviews' ) . '</button>';
            $read_more_link .= '</span>';

            return esc_html( $excerpt ) . '<span class="ezrwp-toggle" id="' . esc_attr( $toggle_id ) . '">' . esc_html( $rest ) . '</span>' . $read_more_link;
        }
        
        /**
         * The name of the plugin used to uniquely identify it within the context of
         * WordPress and to define internationalization functionality.
         *
         * @since     1.1.5
         * @return    string    The name of the plugin.
         */
        public function get_plugin_slug() {
            return $this->plugin_slug;
        }
        
        /**
         * 
         *
         * @since     1.1.5
         * @return    string    The name of the plugin.
         */
        public function set_plugin_slug($plugin_slug){
            
            $this->plugin_slug = $plugin_slug;

            return $this;
        }

        /**
         * Get the value of layout
         *
         * @since    1.1.0
         */
        public function get_layout()
        {
                return $this->layout;
        }

        /**
         * Set the value of layout
         *
         * @return  self
         */ 
        public function set_layout($layout)
        {
                $this->layout = $layout;

                return $this;
        }

        /**
         * Get the value of grid_columns
         *
         * @since    1.1.0
         */
        public function get_grid_columns()
        {
                return $this->grid_columns;
        }

        /**
         * Set the value of grid_columns
         *
         * @return  self
         */ 
        public function set_grid_columns($grid_columns)
        {
                $this->grid_columns = $grid_columns;

                return $this;
        }

        /**
         * Get the value of has_reviews
         *
         * @since    1.1.0
         */
        public function get_has_reviews()
        {
                return $this->has_reviews;
        }

        /**
         * Set the value of has_reviews
         *
         * @return  self
         */ 
        public function set_has_reviews($has_reviews)
        {
                $this->has_reviews = $has_reviews;

                return $this;
        }

        /**
         * Get the value of message
         */ 
        /**
         * Get the extra request options (e.g., Premium shortcode attributes).
         *
         * @since    1.7.0
         * @return   array
         */
        public function get_request_args() {
            return isset( $this->request_args ) && is_array( $this->request_args ) ? $this->request_args : array();
        }

        /**
         * Set the extra request options.
         *
         * @since    1.7.0
         * @param    array    $args    The options.
         */
        public function set_request_args( $args ) {
            $this->request_args = $args;
        }

        public function get_message()
        {
                return $this->message;
        }

        /**
         * Set the value of message
         *
         * @return  self
         */ 
        public function set_message($message)
        {
                $this->message = $message;

                return $this;
        }

        /**
         * Get the value of info
         */ 
        public function get_info()
        {
                return $this->info;
        }

        /**
         * Set the value of info
         *
         * @return  self
         */ 
        public function set_info($info)
        {
                $this->info = $info;

                return $this;
        }

        /**
         * Get the value of url
         */ 
        public function get_url()
        {
                return $this->url;
        }

        /**
         * Set the value of url
         *
         * @return  self
         */ 
        public function set_url($url)
        {
                $this->url = $url;

                return $this;
        }

        /**
         * Get the value of $profile_name
         */ 
        public function get_profile_name()
        {
                return $this->profile_name;
        }

        /**
         * Set the value of $profile_name
         *
         * @return  self
         */ 
        public function set_profile_name( $profile_name )
        {
                $this->profile_name = $profile_name;

                return $this;
        }

        /**
         * Get the value of $profile_image_url
         */ 
        public function get_profile_image_url()
        {
                return $this->profile_image_url;
        }

        /**
         * Set the value of $profile_image_url
         *
         * @return  self
         */ 
        public function set_profile_image_url( $profile_image_url )
        {
                $this->profile_image_url = $profile_image_url;

                return $this;
        }
        
        /**
         * Get the value of rating
         *
         * @since    1.2.1
         */
        public function get_rating(){

            return $this->rating;
        }

        /**
         * Set the value of rating
         *
         * @return  self
         */ 
        public function set_rating($rating){

            $this->rating = $rating;
            return $this;
        }

        /**
         * Get the value of sale_count
         */ 
        public function get_sale_count()
        {
                return $this->sale_count;
        }

        /**
         * Set the value of sale_count
         *
         * @return  self
         */ 
        public function set_sale_count( $sale_count )
        {
                $this->sale_count = $sale_count;

                return $this;
        }

        /**
         * Get the value of review_count
         */ 
        public function get_review_count()
        {
                return $this->review_count;
        }

        /**
         * Set the value of review_count
         *
         * @return  self
         */ 
        public function set_review_count($review_count)
        {
                $this->review_count = $review_count;

                return $this;
        }
        
        /**
         * Get the value of reviews
         */ 
        public function get_reviews()
        {
                return $this->reviews;
        }

        /**
         * Set the value of reviews
         *
         * @return  self
         */ 
        public function set_reviews($reviews_output)
        {
                $this->reviews = $reviews_output;

                return $this;
        }

        /**
         * Get the value of general_options
         */ 
        public function get_general_options()
        {
                return $this->general_options;
        }

        /**
         * Set the value of general_options
         *
         * @return  self
         */ 
        public function set_general_options($general_options)
        {
                $this->general_options = $general_options;

                return $this;
        }

        /**
         * Get the value of count
         */ 
        public function get_count()
        {
                return $this->count;
        }

        /**
         * Set the value of count
         *
         * @return  self
         */ 
        public function set_count($count)
        {
                $this->count = $count;

                return $this;
        }

        /**
         * Get the value of $hide_date
         */ 
        public function get_hide_date()
        {
                return $this->hide_date;
        }

        /**
         * Set the value of $hide_date
         *
         * @return  self
         */ 
        public function set_hide_date($hide_date)
        {
                $this->hide_date = $hide_date;

                return $this;
        }

        /**
         * Get the value of $hide_stars
         */ 
        public function get_hide_stars()
        {
                return $this->hide_stars;
        }

        /**
         * Set the value of $hide_stars
         *
         * @return  self
         */ 
        public function set_hide_stars($hide_stars)
        {
                $this->hide_stars = $hide_stars;

                return $this;
        }

        /**
         * Get the value of $hide_reviewer_summary
         */ 
        public function get_hide_reviewer_summary()
        {
                return $this->hide_reviewer_summary;
        }

        /**
         * Set the value of $hide_reviewer_summary
         *
         * @return  self
         */ 
        public function set_hide_reviewer_summary($hide_reviewer_summary)
        {
                $this->hide_reviewer_summary = $hide_reviewer_summary;

                return $this;
        }

        /**
         * Get the value of $hide_disclaimer
         */ 
        public function get_hide_disclaimer()
        {
                return $this->hide_disclaimer;
        }

        /**
         * Set the value of $hide_disclaimer
         *
         * @return  self
         */ 
        public function set_hide_disclaimer($hide_disclaimer)
        {
                $this->hide_disclaimer = $hide_disclaimer;

                return $this;
        }

        /**
         * Get the value of $hide_profile_card
         */ 
        public function get_hide_profile_card()
        {
                return $this->hide_profile_card;
        }

        /**
         * Set the value of $hide_profile_card
         *
         * @return  self
         */ 
        public function set_hide_profile_card($hide_profile_card)
        {
                $this->hide_profile_card = $hide_profile_card;

                return $this;
        }

        /**
         * Get the value of $hide_view_all_link
         */ 
        public function get_hide_view_all_link()
        {
                return $this->hide_view_all_link;
        }

        /**
         * Set the value of $hide_view_all_link
         *
         * @return  self
         */ 
        public function set_hide_view_all_link($hide_view_all_link)
        {
                $this->hide_view_all_link = $hide_view_all_link;

                return $this;
        }

        /**
         * Get the value of $hide_zillow_logo
         */ 
        public function get_hide_zillow_logo()
        {
                return $this->hide_zillow_logo;
        }

        /**
         * Set the value of $hide_zillow_logo
         *
         * @return  self
         */ 
        public function set_hide_zillow_logo($hide_zillow_logo)
        {
                $this->hide_zillow_logo = $hide_zillow_logo;

                return $this;
        }

        /**
         * Get the value of $quote_font_size
         */ 
        public function get_quote_font_size()
        {
                return $this->quote_font_size;
        }

        /**
         * Set the value of $quote_font_size
         *
         * @return  self
         */ 
        public function set_quote_font_size($quote_font_size)
        {
                $this->quote_font_size = $quote_font_size;

                return $this;
        }

        /**
         * Get the value of $reviewer_description_font_size
         */ 
        public function get_reviewer_description_font_size()
        {
                return $this->reviewer_description_font_size;
        }

        /**
         * Set the value of $reviewer_description_font_size
         *
         * @return  self
         */ 
        public function set_reviewer_description_font_size($reviewer_description_font_size)
        {
                $this->reviewer_description_font_size = $reviewer_description_font_size;

                return $this;
        }

        /**
         * Get the value of $available_layouts
         */ 
        public function get_available_layouts()
        {
                return $this->available_layouts;
        }

        /**
         * Set the value of $available_layouts
         *
         * @return  self
         */ 
        public function set_available_layouts($available_layouts)
        {
                $this->available_layouts = $available_layouts;

                return $this;
        }

        /**
         * Get the value of $available_apis
         */ 
        public function get_available_apis()
        {
                return $this->available_apis;
        }

        /**
         * Set the value of $available_apis
         *
         * @return  self
         */ 
        public function set_available_apis($available_apis)
        {
                $this->available_apis = $available_apis;

                return $this;
        }
    }
}
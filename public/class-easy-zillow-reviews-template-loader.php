<?php

/**
 * The Easy_Zillow_Reviews_Template class
 *
 * 
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.4
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */
    
if ( ! class_exists( 'Easy_Zillow_Reviews_Template_Loader' ) ) {

    class Easy_Zillow_Reviews_Template_Loader{
        
        /**
         * The option to show or hide the mandatory disclaimer.
         * If true, the disclaimer will be hidden.
         *
         * @since    1.1.4
         * @access   private
         * @var      bool   $hide_disclaimer  
         */
        private $hide_disclaimer;
        
        /**
         * The option to show or hide the average star rating for the Zillow profile.
         * If true, the rating will be hidden.
         *
         * @since    1.2.1
         * @access   private
         * @var      bool   $hide_profile_card
         */
        private $hide_profile_card;
        
        /**
         * The option to show or hide the link to the reviews page on Zillow.com.
         * If true, the link will be hidden.
         *
         * @since    1.1.4
         * @access   private
         * @var      bool   $hide_view_all_link  
         */
        private $hide_view_all_link;
        
        /**
         * The option to show or hide the Zillow logo.
         * If true, the logo will be hidden.
         *
         * @since    1.1.4
         * @access   private
         * @var      bool   $hide_zillow_logo
         */
        private $hide_zillow_logo;
        
        /**
         * URL link to the professional's profile on Zillow.
         *
         * @since    1.1.4
         * @access   private
         * @var      bool   $profile_url
         */
        private $profile_url;
        
        /**
         * The number of reviews for the professional.
         *
         * @since    1.1.4
         * @access   private
         * @var      bool   $review_count
         */
        private $review_count;
        
        /**
         * The name of the professional.
         * 
         * @since    1.2.1
         * @access   private
         * @var      string   $name
         */
        private $name;
        
        /**
         * A link to the profile photo of the professional.
         * 
         * @since    1.2.1
         * @access   private
         * @var      string   $photo
         */
        private $photo;
        
        /**
         * The average rating for all of the professional's ratings.
         * 
         * @since    1.2.1
         * @access   private
         * @var      int   $average_rating
         */
        private $average_rating;
        
        /**
         * The number of recent sales for the professional.
         * 
         * @since    1.2.1
         * @access   private
         * @var      int   $recent_sale_count
         */
        private $recent_sale_count;
        
        /**
         *
         * 
         * @since    1.2.1
         * @access   private
         * @var      string   $profile_card
         */
        private $profile_card;


        // Constructor
        public function __construct(){
        }

        // Methods
        public function init(){
        }

        /**
         * Build the HTML output for the Zillow Reviews
         */ 
        public function generate_reviews_wrapper( $reviews_output, $layout, $grid_columns ){

            // User Options
            $hide_disclaimer = $this->get_hide_disclaimer();
            $hide_profile_card = $this->get_hide_profile_card();
            $hide_view_all_link = $this->get_hide_view_all_link();
            $hide_zillow_logo = $this->get_hide_zillow_logo();
            $profile_url = $this->get_profile_url();
            $review_count = $this->get_review_count();
            $profile_card = $this->get_profile_card();

            // Layout Options
            $grid_columns_class = ($layout == "grid" && $grid_columns > 0) 
                                ? 'ezrwp-grid-'. $grid_columns 
                                : '';

            // Other options
            if( $hide_profile_card == true ){
                $profile_card = '';
            }
            $view_all_link = '';
            if( $hide_view_all_link == false ){
                $view_all_link = '
                    <p class="ezrwp-call-to-action">
                        <a href="'. $profile_url . '#reviews" class="z-profile-link" target="_blank" rel="nofollow">
                            View all '. $review_count .' reviews.
                        </a>
                    </p>';
            }

            $zillow_logo = '';
            if( $hide_zillow_logo == false ){
                $zillow_logo = '
                    <p class="ezrwp-attribution">
                        <a href="'. $profile_url . '" class="z-profile-link" target="_blank" rel="nofollow">
                            <img src="//www.zillow.com/widgets/GetVersionedResource.htm?path=/static/logos/Zillowlogo_200x50.gif" width="150" height="38" alt="Real Estate on Zillow">
                        </a>
                    </p>';
            }

            $mandatory_disclaimer = '';
            if( $hide_disclaimer == false ){
                $mandatory_disclaimer = '
                    <p class="ezrwp-mandatory-disclaimer">
                        © Zillow, Inc., 2006-2016. Use is subject to 
                        <a href="https://www.zillow.com/corp/Terms.htm" target="_blank">Terms of Use</a><br />
                        <a href="https://www.zillow.com/wikipages/What-is-a-Zestimate/" target="_blank">What\'s a Zestimate?</a>
                    </p>';
            }
            
            $output = sprintf(
                '<div class="ezrwp-wrapper ezrwp-%1$s %2$s">
                    <div class="ezrwp-content">
                        %3$s %4$s %5$s %6$s %7$s
                    </div>
                </div>',
                $layout,
                $grid_columns_class,
                $reviews_output,
                $profile_card,
                $view_all_link,
                $zillow_logo,
                $mandatory_disclaimer
            );

            return $output;

        }

        /**
         * Convert date to time elapsed
         *
         * @since    1.1.0
         */
        public function convert_date_to_time_elapsed($datetime, $full = false) {
            $now = new DateTime;
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);

            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;

            $string = array(
                    'y' => 'year',
                    'm' => 'month',
                    'w' => 'week',
                    'd' => 'day',
                    'h' => 'hour',
                    'i' => 'minute',
                    's' => 'second',
            );
            foreach ($string as $k => &$v) {
                    if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                    } else {
                    unset($string[$k]);
                    }
            }

            if (!$full) $string = array_slice($string, 0, 1);
            return $string ? implode(', ', $string) . ' ago' : 'just now';
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
         * Get the value of $profile_url
         */ 
        public function get_profile_url()
        {
                return $this->profile_url;
        }

        /**
         * Set the value of $profile_url
         *
         * @return  self
         */ 
        public function set_profile_url($profile_url)
        {
                $this->profile_url = $profile_url;

                return $this;
        }

        /**
         * Get the value of $review_count
         */ 
        public function get_review_count()
        {
                return $this->review_count;
        }

        /**
         * Set the value of $review_count
         *
         * @return  self
         */ 
        public function set_review_count($review_count)
        {
                $this->review_count = $review_count;

                return $this;
        }

        /**
         * Get the value of $name
         */ 
        public function get_name()
        {
                return $this->name;
        }

        /**
         * Set the value of $name
         *
         * @return  self
         */ 
        public function set_name($name)
        {
                $this->name = $name;

                return $this;
        }

        /**
         * Get the value of $photo
         */ 
        public function get_photo()
        {
                return $this->photo;
        }

        /**
         * Set the value of $photo
         *
         * @return  self
         */ 
        public function set_photo($photo)
        {
                $this->photo = $photo;

                return $this;
        }

        /**
         * Get the value of $average_rating
         */ 
        public function get_average_rating()
        {
                return $this->average_rating;
        }

        /**
         * Set the value of $average_rating
         *
         * @return  self
         */ 
        public function set_average_rating($average_rating)
        {
                $this->average_rating = $average_rating;

                return $this;
        }

        /**
         * Get the value of $recent_sale_count
         */ 
        public function get_recent_sale_count()
        {
                return $this->recent_sale_count;
        }

        /**
         * Set the value of $recent_sale_count
         *
         * @return  self
         */ 
        public function set_recent_sale_count($recent_sale_count)
        {
                $this->recent_sale_count = $recent_sale_count;

                return $this;
        }

        /**
         * Get the value of $profile_card
         */ 
        public function get_profile_card()
        {
                return $this->profile_card;
        }

        /**
         * Set the value of $profile_card
         *
         * @return  self
         */ 
        public function set_profile_card($profile_card)
        {
                $this->profile_card = $profile_card;

                return $this;
        }
    }
}
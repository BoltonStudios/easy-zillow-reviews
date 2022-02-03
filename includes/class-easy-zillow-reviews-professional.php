<?php

/**
 * The Easy_Zillow_Reviews_Professional class
 *
 * Fetches Professional Reviews from the Zillow API Network.
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */
    
if ( ! class_exists( 'Easy_Zillow_Reviews_Professional' ) ) {

    class Easy_Zillow_Reviews_Professional extends Easy_Zillow_Reviews_Data{

        /**
         * The user's settings from the Professional Reviews admin tab.
         *
         * @since    1.1.0
         * @access   private
         * @var      array    $professional_reviews_options    The user's settings from the Professional Reviews admin tab.
         */
        private $professional_reviews_options;

        /**
         * The URL for the Reviews API Web Service.
         *
         * @since    1.1.0
         * @access   private
         * @var      string   $zillow_api_url
         */
        private $zillow_api_url;

        /**
         * The Zillow Web Service Identifier (ZWSID).
         *
         * @since    1.1.0
         * @access   private
         * @var      string   $zwsid
         */
        private $zwsid;

        /**
         * The screenname of the user whose reviews will be fetched.
         *
         * @since    1.1.0
         * @access   private
         * @var      string   $screenname
         */
        private $screenname;

        /**
         * The option to fetch reviews for individual team members in team profiles.
         *
         * @since    1.1.0
         * @access   private
         * @var      bool   $show_team_members 
         */
        private $show_team_members;

        // Constructor
        public function __construct(){

            $this->init();
            $this->set_professional_reviews_options( get_option('ezrwp_professional_reviews_options') );
            $this->set_zwsid( $this->professional_reviews_options['ezrwp_zwsid'] );
            $this->set_screenname( $this->professional_reviews_options['ezrwp_screenname'] );
        }

        // Methods
        /**
         * Get reviews data from Zillow using the Zillow ProReviews API.
         *
         * @since    1.1.0
         */
        public function fetch_reviews_from_zillow( $count, String $screenname = null ){
            
            // Initialize variables.
            $zwsid = $this->get_zwsid();
            $disallowed_characters = array("-", " ");
            $toggle_team_members = $this->get_show_team_members() ? '&returnTeamMemberReviews=true' : '';

            // If the $screenname argument is not null...
            if( isset( $screenname ) ){

                // Assign the value of the $screenname argument to the local $screenname variable.
                $screenname = $screenname ;

            } else{

                // Assign the value of the screenname from the Settings page to the local $screenname variable.
                $screenname = $this->get_screenname();

            }
            
            // Strip spaces from the screenname.
            $screenname = str_replace( $disallowed_characters, "%20", $screenname );
            
            // Construct the URL for a Zillow Professional.
            $zillow_url = 'http://www.zillow.com/webservice/ProReviews.htm?zws-id='. $zwsid .'&screenname='. $screenname .'&count='. $count . $toggle_team_members;

            // Fetch data from Zillow.

            // Enable user error handling. Use for debugging.
            // libxml_use_internal_errors(true);
            
            /**
             * allow_url_fopen must be enabled to use simplexml_load_file().
             * Some hosts disable allow_url_fopen for security reasons.
             * Fall back to cURL if allow_url_fopen is disabled in the PHP configuration.
             * 
             * */
            $allow_url = ini_get( 'allow_url_fopen' );
            if( $allow_url ){

                // Fetch data from the Zillow API Network.
                $zillow_data = simplexml_load_file( $zillow_url );
                
            } else{

                // Fetch data from the Zillow API Network.
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, $zillow_url);
                $curl_result = curl_exec($ch);
                curl_close($ch);

                // Interpret XML Data into an object.
                $zillow_data = simplexml_load_string( $curl_result );
            }

            // Handle XML errors.  
            if( $zillow_data === false ){

                echo "Failed loading XML.";

                // Handle errors in debugging.
                /*
                foreach(libxml_get_errors() as $error) {
                    echo "\n\t", $error->message;
                }
                */
            }
            
            // Pass data from Zillow to this class instance.
            $this->set_message($zillow_data->message->text);
            $this->set_has_reviews(( $zillow_data->message->code > 0 ) ? false : true);
            if($this->get_has_reviews()){

                // Success
                $this->set_info($zillow_data->response->result->proInfo);
                $this->set_url($zillow_data->response->result->proInfo->profileURL);
                $this->set_rating($zillow_data->response->result->proInfo->avgRating);
                $this->set_review_count($zillow_data->response->result->proInfo->reviewCount);
                $this->set_reviews($zillow_data->response->result->proReviews);
            }
        }
        
        /**
         * Flow the professional reviews data from this object into HTML elements.
         *
         * @since    1.1.0
         * @return   string
         */
        public function layout_reviews( $as_layout, $number_cols ){

            // User Options
            $hide_date = $this->get_hide_date();
            $hide_stars = $this->get_hide_stars();
            $hide_reviewer_summary = $this->get_hide_reviewer_summary();
            $hide_disclaimer = $this->get_hide_disclaimer();
            $hide_profile_card = $this->get_hide_profile_card();
            $hide_view_all_link = $this->get_hide_view_all_link();
            $hide_zillow_logo = $this->get_hide_zillow_logo();
            $layout = ($as_layout == '') ? $this->get_layout() : $as_layout;
            $number_cols = ($number_cols == '') ? $this->get_grid_columns() : $number_cols;
            $profile_url = $this->get_url();
            $review_count = $this->get_review_count();
            $name = $this->get_info()->name;
            $photo = $this->get_info()->photo;
            $rating = $this->get_rating();
            $sale_count = $this->get_info()->recentSaleCount;
            $profile_card = $this->get_profile_card( $name, $photo, $profile_url, $rating, $review_count, $sale_count );

            // Output
            $i = 0;
            $reviews_output = '';
            $template = new Easy_Zillow_Reviews_Template_Loader();
            $template->set_hide_disclaimer( $hide_disclaimer );
            $template->set_hide_profile_card( $hide_profile_card );
            $template->set_hide_view_all_link( $hide_view_all_link );
            $template->set_hide_zillow_logo( $hide_zillow_logo );
            $template->set_profile_url( $profile_url );
            $template->set_review_count( $review_count );
            $template->set_profile_card( $profile_card );
            
            // Iterate over reviews.
            foreach( $this->reviews->review as $review ) :

                // Update local variables.
                $description = $review->description;
                $summary = lcfirst( $review->reviewSummary );
                $url = $review->reviewURL;
                $date = ( !$hide_date ) ? '<div class="ezrwp-date">'. $template->convert_date_to_time_elapsed(date( "Y-m-d", strtotime($review->reviewDate))) .'</div>' : '';
                $reviewer_summary = ( !$hide_reviewer_summary ) ? '<span class="review-summary">who '. $summary .'</span>' : '';
                $stars = 0;
                if( !$hide_stars ){
                    $stars = floatval( $review->rating );
                    $star_count = floor($stars); // count whole stars
                    $half_star_toggle = '';
                    if( $stars - floor($stars) > 0 ){
                        // add half star if required
                        $half_star_toggle = "ezrwp-plus-half-star";
                    } 

                    $stars = '
                        <div class="ezrwp-stars ezrwp-stars-'. $star_count .' '. $half_star_toggle .'"></div>
                    ';
                }
                $reviewer = '
                    <div class="ezrwp-reviewer">
                        <p> &mdash; <a href="'. $url .'" target="_blank" rel="nofollow">Zillow Reviewer</a> '. $reviewer_summary .'
                        </p>
                    </div>
                ';
                $review_quote = '<blockquote>'. $description .'</blockquote>';
                $before_review = '<div class="col ezrwp-col">';
                $after_review = '</div>';

                $reviews_output .= $before_review;
                $reviews_output .= $review_quote;
                $reviews_output .= $stars;
                $reviews_output .= $date;
                $reviews_output .= $reviewer;
                $reviews_output .= $after_review;

                $i++;
                if( $i % $number_cols == 0 ){
                    $reviews_output .= '<div style="clear:both"></div>';
                }
            endforeach;

            return $template->generate_reviews_wrapper( $reviews_output, $layout, $number_cols );
        }
        
        /**
         * Return a string of HTML for the profile card.
         * 
         * @since    1.2.1
         * @param    string     $name               The name of the professional.
         * @param    string     $photo              A link to the profile photo of the professional.
         * @param    string     $url                URL link to the professional's profile on Zillow.
         * @param    int        $rating             The average rating for all of the professional's ratings.
         * @param    int        $review_count       The number of reviews for the professional.
         * @param    int        $sale_count         The number of recent sales for the professional
         * @return   string                         The modified data.
         */
        function get_profile_card( $name, $photo, $url, $rating, $review_count, $sale_count ){

            // Initialize variables.
            $star_average = '';
            if( $rating == 0 ){
                $star_average = 'star-0';
            } elseif( $rating <= 2.0 ){
                $star_average = 'star-25';
            } elseif( $rating <= 3.5 ){
                $star_average = 'star-50';
            } elseif( $rating <= 4.9 ){
                $star_average = 'star-75';
            } else{
                $star_average = 'star-100';
            }
            if( $star_average !== '' ){
                $star_average = '<span class="ezrwp-star-average ezrwp-icon-'. $star_average .'"></span>';
            }

            // Construct the Recent Sales output.
            $recent_sales = '';
            
            // Check if the profile has any recent sales.
            if( $sale_count > 0 ){

                // If the $sale count is exactly 1, use the singular form of 'sales'.
                $sales_descriptor = $sale_count == 1 ? 'sale' : 'sales';

                // If the profile has recent sales, update the output.
                $recent_sales = '<div class="ezrwp-activity-sales">
                                    '. $sale_count .' recent home '. $sales_descriptor .'
                                </div>';
            }

            // Construct the Profile Card output.
            $profile_card = '
                <div style="clear:both"></div>
                <div class="ezrwp-profile-card">
                    <div class="ezrwp-profile-card-left">
                        <div class="ezrwp-profile-image-container">
                            <img class="ezrwp-photo" src="'. $photo .'" alt="" width="94" height="94" />
                        </div>
                    </div>
                    <div class="ezrwp-profile-card-right">
                        <p class="ezrwp-profile-name"><strong>'. $name .'</strong></p>
                        <div class="ezrwp-activity">
                            <div class="ezrwp-activity-reviews">
                                <div class="ezrwp-rating-reviews">
                                    <span class="ezrwp-avg-rating">'. $star_average . $rating .'</span>/<span class="ezrwp-max-rating">5</span>
                                    <a href="'. $url . '#reviews" class="ezrwp-reviews-count">'. $review_count .' Reviews</a>
                                </div>
                            </div>
                            '. $recent_sales .'
                        </div>
                    </div>
                </div>
            ';

            // Return the Profile Card output.
            return $profile_card;
        }
        
        /**
         * Return reviews output based on options provided in attributes
         * 
         * @since    1.2.0
         * @param    string     $output        The data that will be modified.
         * @param    array      $attributes    The arguments passed from the Gutenberg block options that the user selected.  
         * @return   string                    The modified data.
         */
        function update_reviews_in_block( $output, $attributes ){
            
            // Get this Eazy_Zillow_Reviews_Professional object instance.
            $reviews = $this;

            // Parse attributes selected by the user in the Gutenberg block.
            $layout = isset( $attributes[ 'reviewsLayout' ] ) ? $attributes[ 'reviewsLayout' ] : $reviews->get_layout();
            $cols = isset( $attributes[ 'gridColumns' ] ) ? $attributes[ 'gridColumns' ] : $reviews->get_grid_columns();
            $count = isset( $attributes[ 'reviewsCount' ] ) ? $attributes[ 'reviewsCount' ] : $reviews->get_count();
            
            // Overwite the Gutenberg block output with professional reviews from this object instance.
            $output = $reviews->get_reviews_output( $reviews, $layout, $cols, $count );

            // Return the updated output.
            return $output;
        }
        
        // Getters & Setters
        /**
         * Get the value of zwsid
         *
         * @since    1.1.0
         */
        public function get_zwsid(){

            return $this->zwsid;
        }

        /**
         * Set the value of zwsid
         *
         * @return  self
         */ 
        public function set_zwsid($zwsid){

            $this->zwsid = $zwsid;
            return $this;
        }
        
        /**
         * Get the value of screenname
         *
         * @since    1.1.0
         */
        public function get_screenname(){

            return $this->screenname;
        }

        /**
         * Set the value of screenname
         *
         * @return  self
         */ 
        public function set_screenname($screenname){

            $this->screenname = $screenname;
            return $this;
        }
        
        /**
         * Get the value of show_team_members
         *
         * @since    1.1.0
         */
        public function get_show_team_members(){

            return $this->show_team_members;
        }

        /**
         * Set the value of show_team_members
         *
         * @return  self
         */ 
        public function set_show_team_members($show_team_members){

            $this->show_team_members = $show_team_members;
            return $this;
        }
        
        /**
         * Get the value of zillow_api_url
         *
         * @since    1.1.0
         */
        public function get_zillow_api_url(){

            return $this->zillow_api_url;
        }

        /**
         * Set the value of zillow_api_url
         *
         * @return  self
         */ 
        public function set_zillow_api_url($zillow_api_url){

            $this->zillow_api_url = $zillow_api_url;
            return $this;
        }
        
        /**
         * Get the value of professional_reviews_options
         *
         * @since    1.1.0
         */
        public function get_professional_reviews_options(){

            return $this->professional_reviews_options;
        }

        /**
         * Set the value of professional_reviews_options
         *
         * @return  self
         */ 
        public function set_professional_reviews_options($professional_reviews_options){

            $this->professional_reviews_options = $professional_reviews_options;
            return $this;
        }
    }
}
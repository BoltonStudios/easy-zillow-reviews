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

include 'class-easy-zillow-reviews-review.php';

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
         * The Bridge API Access Token.
         *
         * @since    1.5.0
         * @access   private
         * @var      string   $bridge_token
         */
        private $bridge_token;

        /**
         * The Zillow Web Service Identifier (ZWSID).
         * 
         * Deprecated
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

        /**
         * The review text word limit set by the user.
         *
         * @since    1.6.0
         * @access   private
         * @var      int   $word_limit
         */
        private $word_limit;

        // Constructor
        public function __construct(){

            $this->init();
            $this->set_professional_reviews_options( get_option( 'ezrwp_professional_reviews_options' ) );

            /** 
             * Check if the following options appear in the 'ezrwp_professional_reviews_options' row of the 'wp_options' table.
             * If not, set the variables to null.
            */
            $bridge_token = isset( $this->professional_reviews_options['ezrwp_bridge_token_1'] ) ? $this->professional_reviews_options['ezrwp_bridge_token_1'] : null;
            $screenname = isset( $this->professional_reviews_options['ezrwp_screenname'] ) ? $this->professional_reviews_options['ezrwp_screenname'] : null;
            $zwsid = isset( $this->professional_reviews_options['ezrwp_zwsid'] ) ? $this->professional_reviews_options['ezrwp_zwsid'] : null;
            $word_limit = isset( $this->professional_reviews_options['ezrwp_word_limit'] ) ? $this->professional_reviews_options['ezrwp_word_limit'] : null;
            
            // Update the instance variables.
            $this->set_bridge_token( $bridge_token );
            $this->set_zwsid( $zwsid );
            $this->set_screenname( $screenname );
            $this->set_word_limit( $word_limit );
        }

        // Methods
        /**
         * Get reviews data from the Zillow Reviews API (via Bridge Interactive).
         *
         * @since    1.1.0
         * @param    int          $count         The number of reviews to fetch (1-10).
         * @param    string|null  $screenname    The Zillow screenname. Defaults to the screenname in the plugin settings.
         * @param    int|null     $word_limit    The maximum number of words per review. Defaults to the plugin settings.
         */
        public function fetch_reviews_from_zillow( $count, $screenname = null, $word_limit = null ){

            // Initialize variables.
            $bridge_token = $this->get_bridge_token();
            $count = min( max( absint( $count ), 1 ), 10 );
            $screenname = trim( (string) ( ( $screenname === null || $screenname === '' ) ? $this->get_screenname() : $screenname ) );
            $message = '';
            $has_reviews = false;
            $reviews = array();

            // Update the value of $word_limit for this Easy_Zillow_Reviews_Professional object.
            $this->set_word_limit( ( $word_limit === null ) ? $this->get_word_limit() : $word_limit );

            if( empty( $bridge_token ) ){

                // Zillow retired the ZWSID-based API in favor of Bridge Interactive.
                $message = $this->get_zwsid()
                    ? 'Zillow no longer supports the Zillow Web Services ID (ZWSID). Please add a Bridge API Access Token on the plugin Settings page'
                    : 'Please add a Bridge API Access Token on the plugin Settings page';

            } elseif( $screenname === '' ){

                $message = 'Please add a Zillow Screenname on the plugin Settings page';

            } else{

                $headers = array( 'Authorization' => 'Bearer ' . $bridge_token );

                // Look up the reviewee (the Zillow professional) by screenname.
                $account = $this->fetch_bridge_reviewee( $screenname, $headers, $message );

                if( $account ){

                    // Request the most recent reviews. Chronology is the only selection
                    // permitted by Zillow's terms for showing a subset of reviews.
                    $reviews_url = add_query_arg(
                        array(
                            '$filter'  => rawurlencode( "AccountIdReviewee eq '" . $account->AccountIdReviewee . "'" ),
                            '$orderby' => rawurlencode( 'ReviewDate desc' ),
                            '$top'     => $count,
                        ),
                        'https://api.bridgedataoutput.com/api/v2/OData/reviews/Review'
                    );

                    $result = $this->remote_get_json( $reviews_url, $headers );

                    if( $result['code'] !== 200 || ! isset( $result['data']->value ) || ! is_array( $result['data']->value ) ){

                        $message = $this->get_bridge_error_message( $result );

                    } else{

                        $profile_url = isset( $account->RevieweeProfileURL ) ? $account->RevieweeProfileURL : 'https://www.zillow.com/profile/' . rawurlencode( $screenname ) . '/';

                        foreach( $result['data']->value as $item ){
                            $reviews[] = $this->create_review_from_bridge( $item, $profile_url );
                        }

                        // Sort reviews by date, newest first.
                        usort( $reviews, function( $a, $b ){
                            return strtotime( $b->get_date() ) - strtotime( $a->get_date() );
                        });

                        $has_reviews = true;

                        // The following data was available in the old Zillow API implementation,
                        // so we may re-include it if Bridge shares this data in the future:
                        // sale count and profile image URL.
                        $this->set_profile_name( isset( $account->RevieweeFullName ) ? $account->RevieweeFullName : '' );
                        $this->set_profile_image_url( '' );
                        $this->set_url( $profile_url );
                        $this->set_rating( isset( $account->AverageReviewRating ) ? floatval( $account->AverageReviewRating ) : 0.0 );
                        $this->set_sale_count( 0 );
                        $this->set_review_count( isset( $account->ReviewCount ) ? absint( $account->ReviewCount ) : 0 );
                        $this->set_reviews( array_slice( $reviews, 0, $count ) );
                    }
                }
            }

            // Pass data from Zillow to this class instance.
            $this->set_message( $message );
            $this->set_has_reviews( $has_reviews );
        }

        /**
         * Look up a Zillow professional's Bridge reviewee account by screenname.
         *
         * @since    1.6.5
         * @param    string    $screenname    The Zillow screenname.
         * @param    array     $headers       Request headers, including authorization.
         * @param    string    $message       Updated with an error message if the lookup fails.
         * @return   object|null              The reviewee account, or null.
         */
        private function fetch_bridge_reviewee( $screenname, $headers, &$message ){

            $candidates = array( $screenname );

            // Earlier versions replaced hyphens with spaces, so try that too for existing settings.
            if( strpos( $screenname, '-' ) !== false ){
                $candidates[] = str_replace( '-', ' ', $screenname );
            }

            foreach( $candidates as $candidate ){

                $url = add_query_arg(
                    'RevieweeScreenName',
                    rawurlencode( $candidate ),
                    'https://api.bridgedataoutput.com/api/v2/reviews/reviewee'
                );

                $result = $this->remote_get_json( $url, $headers );

                if( $result['code'] !== 200 ){
                    $message = $this->get_bridge_error_message( $result );
                    return null;
                }

                if( isset( $result['data']->bundle[0]->AccountIdReviewee ) ){
                    return $result['data']->bundle[0];
                }
            }

            $message = 'No Zillow profile found for the screenname "' . $screenname . '"';

            return null;
        }

        /**
         * Create a review object from a Bridge API review record.
         *
         * @since    1.6.5
         * @param    object    $item           The review record.
         * @param    string    $profile_url    The professional's Zillow profile URL.
         * @return   Easy_Zillow_Reviews_Review
         */
        private function create_review_from_bridge( $item, $profile_url ){

            $location = isset( $item->FreeFormLocation ) ? array_map( 'trim', explode( ',', $item->FreeFormLocation ) ) : array();
            $city = isset( $location[1] ) ? mb_convert_case( $location[1], MB_CASE_TITLE, 'UTF-8' ) : '';
            $state = isset( $location[2] ) ? ', ' . strtoupper( $location[2] ) : '';

            // If the "city" is *not* empty but the "state" is empty, assume the "city" is the two-letter state code.
            $city = ( $city !== '' && $state === '' ) ? strtoupper( $city ) : $city . $state;

            return new Easy_Zillow_Reviews_Review(
                isset( $item->Description ) ? (string) $item->Description : '',
                isset( $item->ServiceProviderDesc ) ? lcfirst( (string) $item->ServiceProviderDesc ) : '',
                trailingslashit( $profile_url ) . '#reviews',
                isset( $item->ReviewDate ) ? (string) $item->ReviewDate : '',
                isset( $item->Rating ) ? floatval( $item->Rating ) : 0,
                $city
            );
        }

        /**
         * Build a readable error message from a failed Bridge API response.
         *
         * @since    1.6.5
         * @param    array    $result    The result of remote_get_json().
         * @return   string
         */
        private function get_bridge_error_message( $result ){

            $data = $result['data'];

            if( $result['error'] !== '' && $data === null ){
                return $result['error'];
            }

            foreach( array( 'bundle', 'error' ) as $key ){
                if( isset( $data->$key->message ) ){
                    return ( isset( $data->$key->name ) ? $data->$key->name . ': ' : '' ) . $data->$key->message;
                }
            }

            return 'The reviews API returned an unexpected response (HTTP ' . $result['code'] . ')';
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
            $name = $this->get_profile_name();
            $number_cols = ($number_cols == '') ? $this->get_grid_columns() : $number_cols;
            $photo = $this->get_profile_image_url();
            $profile_url = $this->get_url();
            $rating = $this->get_rating();
            $review_count = $this->get_review_count();
            $sale_count = $this->get_sale_count();
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
            $wrapper_id = $template->get_wrapper_id();
            
            // Iterate over reviews.
            foreach( $this->reviews as $review ) :

                // Update local variables.
                $url = $review->get_url();
                $date = ( !$hide_date ) ? '<div class="ezrwp-date">'. $template->convert_date_to_time_elapsed(date( "Y-m-d", strtotime( $review->get_date() ) ) ) .'</div>' : '';
                $stars = 0;
                $star_rating_html = "";
                $summary = lcfirst( $review->get_summary() );
                $reviewer_summary = ( !$hide_reviewer_summary ) ? '<span class="review-summary">who '. esc_html( $summary ) .'</span>' : '';
                $word_limit = $this->get_word_limit();
                $before_review = '<div class="col ezrwp-col">';
                $after_review = '</div>';

                // Escape the review text and truncate it to the word limit.
                $review_quote = '<blockquote>'. $this->format_review_text( $review->get_description(), $word_limit, $wrapper_id, $i ) . '</blockquote>';

                // If the user did not opt to hide the stars...
                if( !$hide_stars ){

                    // Initialize new local variables.
                    $half_star_toggle = '';

                    // Get the float value of the review rating.
                    $stars = floatval( $review->get_rating() );

                    // Count whole stars.
                    $star_count = floor( $stars );

                    // If the rating contains a fraction...
                    // The float value of the rating less the count of whole "stars" will be greater than zero.
                    if( $stars - $star_count > 0 ){

                        // Add a half star.
                        $half_star_toggle = "ezrwp-plus-half-star";
                    }

                    // Construct the HTML string for the star rating.
                    $star_rating_html = '
                        <div class="ezrwp-stars ezrwp-stars-'. $star_count .' '. $half_star_toggle .'"></div>
                    ';
                }

                // Construct the HTML string for the reviewer element.
                $reviewer = '
                    <div class="ezrwp-reviewer">
                        <p> &mdash;<a href="'. esc_url( $url ) .'" target="_blank" rel="nofollow">Zillow Reviewer</a> '. $reviewer_summary .'
                        </p>
                    </div>
                ';

                // Update the reviews output HTML string.
                $reviews_output .= $before_review;
                $reviews_output .= $review_quote;
                $reviews_output .= $star_rating_html;
                $reviews_output .= $date;
                $reviews_output .= $reviewer;
                $reviews_output .= $after_review;

                // Increment the counter.
                $i++;

                // If the counter in the current loop iteration is divisible by the users' chosen number of columns...
                if( $i % $number_cols == 0 ){

                    // Add an element to create a line break.
                    $reviews_output .= '<div style="clear:both"></div>';
                }
            endforeach;

            // Return the output.
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
            $card_left = '';
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
                                    '. absint( $sale_count ) .' recent home '. $sales_descriptor .'
                                </div>';
            }
            
            // If the photo exists...
            if( $photo != '' ){

                // Add the photo to the card_left output.
                $card_left = '
                    <div class="ezrwp-profile-card-left">
                        <div class="ezrwp-profile-image-container">
                            <img class="ezrwp-photo" src="'. esc_url( $photo ) .'" alt="" width="94" height="94" />
                        </div>
                    </div>';
            }

            // Construct the Profile Card output.
            $profile_card = '
                <div style="clear:both"></div>
                <div class="ezrwp-profile-card">
                    '. $card_left .'
                    <div class="ezrwp-profile-card-right">
                        <p class="ezrwp-profile-name"><strong>'. esc_html( $name ) .'</strong></p>
                        <div class="ezrwp-activity">
                            <div class="ezrwp-activity-reviews">
                                <div class="ezrwp-rating-reviews">
                                    <span class="ezrwp-avg-rating">'. $star_average . esc_html( round( floatval( $rating ), 2 ) ) .'</span>/<span class="ezrwp-max-rating">5</span>
                                    <a href="'. esc_url( $url . '#reviews' ) . '" class="ezrwp-reviews-count">'. absint( $review_count ) .' Reviews</a>
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
            $word_limit = isset( $attributes[ 'wordLimit' ] ) ? $attributes[ 'wordLimit' ] : $reviews->get_word_limit();
            
            // Overwite the Gutenberg block output with professional reviews from this object instance.
            $output = $reviews->get_reviews_output( $reviews, $layout, $cols, $count, null, $word_limit );

            // Return the updated output.
            return $output;
        }
        
        // Getters & Setters
        /**
         * Get the value of $bridge_token.
         *
         * @since    1.5.0
         */
        public function get_bridge_token(){

            return $this->bridge_token;
        }

        /**
         * Set the value of $bridge_token.
         *
         * @return  self
         */ 
        public function set_bridge_token($token){

            $this->bridge_token = $token;
            return $this;
        }

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
        
        /**
         * Get the value of word_count
         *
         * @since    1.6.0
         */
        public function get_word_limit(){

            return $this->word_limit;
        }

        /**
         * Set the value of word_count
         *
         * @return  self
         */ 
        public function set_word_limit($word_count){

            $this->word_limit = $word_count;
            return $this;
        }
    }
}
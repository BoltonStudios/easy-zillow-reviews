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
            $this->set_professional_reviews_options( get_option( 'ezrwp_professional_reviews_options' ) );

            /** 
             * Check if the following options appear in the 'ezrwp_professional_reviews_options' row of the 'wp_options' table.
             * If not, set the variables to null.
            */
            $bridge_token = isset( $this->professional_reviews_options['ezrwp_bridge_token_1'] ) ? $this->professional_reviews_options['ezrwp_bridge_token_1'] : null;
            $screenname = isset( $this->professional_reviews_options['ezrwp_screenname'] ) ? $this->professional_reviews_options['ezrwp_screenname'] : null;
            $zwsid = isset( $this->professional_reviews_options['ezrwp_zwsid'] ) ? $this->professional_reviews_options['ezrwp_zwsid'] : null;
            
            // Update the instances variables.
            $this->set_bridge_token( $bridge_token );
            $this->set_zwsid( $zwsid );
            $this->set_screenname( $screenname );
        }

        // Methods
        /**
         * Get reviews data from the Zillow Reviews API.
         *
         * @since    1.1.0
         */
        public function fetch_reviews_from_zillow( $count, String $screenname = null ){
            
            // Initialize variables.
            $bridge_token = $this->get_bridge_token();
            $zwsid = $this->get_zwsid(); // deprecated
            $disallowed_characters = array("-", " ");
            $toggle_team_members = $this->get_show_team_members() ? '&returnTeamMemberReviews=true' : '';
            $message = "";
            $error_name = "";
            $code = 0;
            $profile_url = "";
            $profile_name = "";
            $profile_image_url = "";
            $sale_count = 0;
            $review_count = 0;
            $rating = 0.0;
            $reviews = array();
            
            /**
             * allow_url_fopen must be enabled to use simplexml_load_file().
             * Some hosts disable allow_url_fopen for security reasons.
             * Logic below falls back to cURL if allow_url_fopen is disabled in the PHP configuration.
             * 
             * */
            $allow_url = ini_get( 'allow_url_fopen' );

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
            
            // If the $bridge_token argument is not null...
            if( isset( $bridge_token ) && $bridge_token != "" ){

                // Fetch data from Bridge.

                // Construct the Bridge URL for a Zillow Professional.
                $bridge_account_url = 'https://api.bridgedataoutput.com/api/v2/reviews/reviewee?access_token='. $bridge_token .'&RevieweeScreenName='. $screenname;
                
                // Fetch data from the Zillow API Network.
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, $bridge_account_url);
                $bridge_account_data = curl_exec($ch);
                curl_close($ch);

                // Decode the account data.
                $bridge_account_data = json_decode( $bridge_account_data );

                // Handle errors
                // If the API returned the status item...
                if( isset( $bridge_account_data->status) ){

                    // Update the $code variable.
                    $code = $bridge_account_data->status;
                }

                // If the code is neither 200 (OK) nor 0 (default value)...
                // Or if the response contains no data...
                if( ( $code != 200 && $code != 0 ) || !isset( $bridge_account_data->bundle[0] ) ){

                    // If the code is neither 200 nor 0, access denied...
                    if ( $code != 200 && $code != 0 ){

                        // Update the error message variables to be returned to the user.
                        $error_name = $bridge_account_data->bundle->name;
                        $error_message = $bridge_account_data->bundle->message;
                        $message = $error_name . ": " . $error_message;

                    } else if( !isset( $bridge_account_data->bundle[0] ) ){

                        // If access granted but no data returned...

                        // Update the error message.
                        $message = "Access granted but no data returned";

                        // Flag error (any code other than 200 or 0 );
                        $code = 1;

                    }

                } else{

                    // Update the bridge account data variable.
                    $bridge_account_data = $bridge_account_data->bundle[0];

                    // Get the reviewee ID.
                    $reviewee_id = $bridge_account_data->AccountIdReviewee;

                    // Construct the Bridge URL for the Zillow Professional's reviews.
                    $bridge_reviews_url = 'https://api.bridgedataoutput.com/api/v2/OData/reviews/Review?access_token='. $bridge_token .'&$filter=AccountIdReviewee%20eq%20%27'. $reviewee_id . '%27';

                    // Fetch data from the Zillow API Network.
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_URL, $bridge_reviews_url);
                    $bridge_reviews_data = curl_exec($ch);
                    curl_close($ch);

                    // Decode the account data.
                    $bridge_reviews_data = json_decode( $bridge_reviews_data );

                    // Handle errors
                    // If the API returned the status item...
                    if( isset( $bridge_reviews_data->status) ){

                        // Update the $code variable.
                        $code = $bridge_reviews_data->status;
                    }

                    // If the code is neither 200 nor 0 (default)...
                    if( $code != 200 && $code != 0 ){

                        // Update the error message variables to be returned to the user.
                        $error_name = $bridge_reviews_data->error->name;
                        $message = $error_name . ": " . $bridge_reviews_data->error->message;

                    } else{

                        // Update other variables.
                        $profile_url = $bridge_account_data->RevieweeProfileURL;
                        $profile_name = $bridge_account_data->RevieweeFullName;
                        $profile_url = $bridge_account_data->RevieweeProfileURL;
                        $rating = $bridge_account_data->AverageReviewRating;
                        $review_count = $bridge_account_data->ReviewCount;
                        $zillow_reviews_data = $bridge_reviews_data->value;

                        /* The following data was available in the old Zillow API implementation,
                         * so we may re-include it if Bridge shares this data in the future.
                         */
                        // $sale_count = $bridge_account_data->;
                        // $profile_image_url = $bridge_account_data->;

                        // $zillow_reviews_data must be an array or an object that 
                        // implements Countable.
                        if( gettype( $zillow_reviews_data ) == 'array' ){

                            // Count the reviews available in the API response.
                            $reviews_available_count = count( $zillow_reviews_data );
                            
                            // Iterate over the elements in $zillow_reviews_data.
                            for( $i = 0; $i < $reviews_available_count; $i++ ){
                                
                                $review_data = $zillow_reviews_data[ $i ];
                                $description = $review_data->Description;
                                $url = 'https://www.zillow.com/profile/'. $screenname .'/#reviews';
                                $date = $review_data->ReviewDate;
                                $rating = floatval( $review_data->Rating );
                                $location = explode( ",", $review_data->FreeFormLocation );
                                $city = isset( $location[ 1 ] ) ? $location[ 1 ] : "";
                                $city .= isset( $location[ 2 ] ) ? ", " . $location[ 2 ] : "";
                                $summary = lcfirst( $review_data->ServiceProviderDesc );
                                
                                $reviews[ $i ] = new Easy_Zillow_Reviews_Review(
                                    $description,
                                    $summary,
                                    $url,
                                    $date,
                                    $rating,
                                    $city
                                );
                            }
                            
                            /**
                             * Sort reviews by date.
                             * 
                             * Citation
                             * Title: "Heres a nicer way using ..."
                             * Author: Scott Quinlan
                             * Date: 04/15/2012
                             * Availability: https://stackoverflow.com/questions/4282413/sort-array-of-objects-by-one-property
                             */
                            usort( $reviews, function( $a, $b ){
                                return strtotime( $b->get_date() ) - strtotime( $a->get_date() );
                            });

                            // Check if the user specified the $count parameter.
                            // If not, set the $count parameter equal to the number of reviews available.
                            $count = isset( $count ) ? $count : $reviews_available_count;

                            // The $count parameter should provide a subset of the reviews available.
                            // Check if the user-specified $count parameter is less than the reviews available.
                            // If not, set the $count parameter equal to the number of reviews available.
                            $count = $count < $reviews_available_count ? $count : $reviews_available_count;

                            // If the $count is different than $reviews_available_count...
                            if( $count != $reviews_available_count ){

                                // Create a temporary reviews array.
                                $temp_reviews = array();

                                // Iterate over the elements in $reviews.
                                for( $i = 0; $i < $count; $i++ ){

                                    // Populate the temporary array with a subset of reviews.
                                    $temp_reviews[ $i ] = $reviews[ $i ];
                
                                }

                                // Update the $reviews array.
                                $reviews = $temp_reviews;
                            }
                        }
                    }
                } 
            } else{

                // The following applies only to sites that still use the deprecated ZWSID.
            
                // Construct the URL for a Zillow Professional.
                $zillow_url = 'http://www.zillow.com/webservice/ProReviews.htm?zws-id='. $zwsid .'&screenname='. $screenname .'&count='. $count . $toggle_team_members;

                // Fetch data from Zillow.

                // Enable user error handling. Use for debugging.
                // libxml_use_internal_errors(true);
                
                // If allow_url_fopen is enabled...
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

                // Update instance variables.
                $message = $zillow_data->message->text;
                $code = $zillow_data->message->code;

                // If the API returned reviews...
                if( $code > 0 ? false : true ){

                    // Update more instance variables.
                    $this->set_info( $zillow_data->response->result->proInfo );
                    $sale_count = $this->get_info()->recentSaleCount;
                    $profile_name = $this->get_info()->name;
                    $profile_image_url = $this->get_info()->photo;
                    $profile_url = $zillow_data->response->result->proInfo->profileURL;
                    $rating = $zillow_data->response->result->proInfo->avgRating;
                    $review_count = $zillow_data->response->result->proInfo->reviewCount;
                    $zillow_reviews_data = $zillow_data->response->result->proReviews->review;

                    //
                    for( $i = 0; $i < count( $zillow_reviews_data ); $i++ ){

                        $review_data = $zillow_reviews_data[ $i ];
                        $description = $review_data->description;
                        $summary = lcfirst( $review_data->reviewSummary );
                        $url = $review_data->reviewURL;
                        $date = $review_data->reviewDate;
                        $rating = floatval( $review_data->rating );
                        
                        $reviews[ $i ] = new Easy_Zillow_Reviews_Review(
                            $description,
                            $summary,
                            $url,
                            $date,
                            $rating
                        );
                    }
                }
            }

            // Pass data from Zillow to this class instance.
            $this->set_message( $message );
            $this->set_has_reviews( ($code != 200 && $code != 0 ) ? false : true );

            // If the API returned reviews...
            if( $this->get_has_reviews() ){

                // Success
                // Update the object properties with Zillow data.
                //$this->set_info( $zillow_data->response->result->proInfo );
                $this->set_profile_name( $profile_name );
                $this->set_profile_image_url( $profile_image_url );
                $this->set_url( $profile_url );
                $this->set_rating( $rating );
                $this->set_sale_count( $sale_count );
                $this->set_review_count( $review_count );
                $this->set_reviews( $reviews );
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
            
            // Iterate over reviews.
            foreach( $this->reviews as $review ) :

                // Update local variables.
                $url = $review->get_url();
                $date = ( !$hide_date ) ? '<div class="ezrwp-date">'. $template->convert_date_to_time_elapsed(date( "Y-m-d", strtotime( $review->get_date() ) ) ) .'</div>' : '';
                $stars = 0;
                $star_rating_html = "";
                $summary = lcfirst( $review->get_summary() );
                $reviewer_summary = ( !$hide_reviewer_summary ) ? '<span class="review-summary">who '. $summary .'</span>' : '';
                $description = $review->get_description();
                $review_quote = '<blockquote>'. $description .'</blockquote>';
                $before_review = '<div class="col ezrwp-col">';
                $after_review = '</div>';

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
                        <p> &mdash;<a href="'. $url .'" target="_blank" rel="nofollow">Zillow Reviewer</a> '. $reviewer_summary .'
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
                                    '. $sale_count .' recent home '. $sales_descriptor .'
                                </div>';
            }
            
            // If the photo exists...
            if( $photo != '' ){

                // Add the photo to the card_left output.
                $card_left = '
                    <div class="ezrwp-profile-card-left">
                        <div class="ezrwp-profile-image-container">
                            <img class="ezrwp-photo" src="'. $photo .'" alt="" width="94" height="94" />
                        </div>
                    </div>';
            }

            // Construct the Profile Card output.
            $profile_card = '
                <div style="clear:both"></div>
                <div class="ezrwp-profile-card">
                    '. $card_left .'
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
    }
}
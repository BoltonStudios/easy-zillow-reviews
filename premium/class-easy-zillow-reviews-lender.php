<?php

/**
 * The Easy_Zillow_Reviews_Lender class
 *
 * Fetches Lender Reviews from the Zillow API Network.
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/premium
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

if ( ! class_exists( 'Easy_Zillow_Reviews_Lender' ) ) {

    class Easy_Zillow_Reviews_Lender extends Easy_Zillow_Reviews_Data{

        /**
         * The user's settings from the Lender Reviews admin tab.
         *
         * @since    1.1.0
         * @access   private
         * @var      array   $lender_reviews_options
         */
        private $lender_reviews_options;

        /**
         * The client's partner ID.
         *
         * @since    1.1.4
         * @access   private
         * @var      string   $zwsid
         */
        private $zmpid;

        /**
         * The lender's NMLS ID.
         *
         * @since    1.1.4
         * @access   private
         * @var      string   $nmlsid
         */
        private $nmlsid;

        /**
         * The company name of the lender. This must be provided for institutional lenders.
         *
         * @since    1.1.4
         * @access   private
         * @var      string   $company_name
         */
        private $company_name;

        // Constructor
        public function __construct(){

            $this->init();
            $this->set_lender_reviews_options( get_option( 'ezrwp_lender_reviews_options' ) );

            /** 
             * Check if the following options appear in the 'ezrwp_lender_reviews_options' row of the 'wp_options' table.
             * If not, set the variables to null.
            */
            $zmpid = isset( $this->lender_reviews_options['ezrwp_zmpid'] ) ? $this->lender_reviews_options['ezrwp_zmpid'] : null;
            $nmlsid = isset( $this->lender_reviews_options['ezrwp_nmlsid'] ) ? $this->lender_reviews_options['ezrwp_nmlsid'] : null;
            $company_name = isset( $this->lender_reviews_options['ezrwp_company_name'] ) ? $this->lender_reviews_options['ezrwp_company_name'] : null;
            
            // Update the instances variables.
            $this->set_zmpid( $zmpid );
            $this->set_nmlsid( $nmlsid );
            $this->set_company_name( $company_name );
        }

        // Methods
        
        /**
         * Get lender reviews data from Zillow using the Zillow Mortgage API.
         *
         * @since    1.1.0
         * @param    $count     The number of reviews to fetch.
         */
        public function fetch_reviews_from_zillow( $count ){
            
            $zmpid = $this->get_zmpid();
            $nmlsid = $this->get_nmlsid();
            $company_name = $this->get_company_name();
            $company_name = str_replace( array("-", " "), "%20", $company_name ); // Encode space characters.
            $company_name = str_replace( "&", "%26", $company_name ); // Encode ampersand character.

            // Contstruct the Zillow URL for an Individual Loan Officer.
            $zillow_url = 'https://mortgageapi.zillow.com/zillowLenderReviews?partnerId='. $zmpid .'&nmlsId='.$nmlsid.'&reviewLimit='. $count;

            // If the user set a Company Name, add it to the Zillow URL to fetch Company reviews.
            if($company_name != ''){
                $zillow_url = $zillow_url . '&companyName=' . $company_name;
            }
            
            // Fetch data from Zillow API Network.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $zillow_url);
            $result = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($result);

            // Check for errors
            $zillow_api_error = false;
            if( isset( $json->error ) ){
                $zillow_api_error = true;
            }
            $this->set_has_reviews( $zillow_api_error ? false : true );

            // Store Zillow data in this Easy_Zillow_Reviews_Data child object instance.
            if($this->get_has_reviews()){

                // Success
                $this->set_url($json->profileURL);
                $this->set_rating($json->rating);
                $this->set_review_count($json->totalReviews);
                $this->set_reviews($json->reviews);
            } else{

                // Error
                $this->set_message($json->error);
            }
        }
        
        /**
         * Flow the lender reviews data from this object into HTML elements.
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
            $rating = $this->get_rating();
            $profile_card = $this->get_profile_card( $rating, $review_count );

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

            // Lender Reviews
            foreach( $this->reviews as $review ) :
                $reviewer_name = $review->reviewerName->displayName;
                $description = $review->content;

                // Check if these properties exist in the Zillow Reviews API response and store their values
                $loan_service_provided = property_exists( $review, 'serviceProvided' ) ? $this->format_loan_service_provided( $review->serviceProvided ) : '';
                $loan_type = property_exists( $review, 'loanType' ) ? $this->format_loan_type( $review->loanType ) : '';
                $loan_program = property_exists( $review, 'loanProgram' ) ? $this->format_loan_program( $loan_type, $review->loanProgram ) : '';
                $loan_purpose = property_exists( $review, 'loanPurpose' ) ? $this->format_loan_purpose( $loan_type, $loan_program, $review->loanPurpose ) : '';
                $review_date = $review->created;

                $loan_summary = $loan_service_provided . " " . $loan_type . " " . $loan_program . " " . $loan_purpose;
                $date = ( $hide_date == false ) ? '<div class="ezrwp-date">'. $template->convert_date_to_time_elapsed(date( "Y-m-d", strtotime( $review_date ))) .'</div>' : '';
                $reviewer_summary = ( $hide_reviewer_summary == false ) ? '<span class="review-summary">who '. $loan_summary .' loan.</span>' : '';
                $stars = '';
                if( $hide_stars == false ){
                    $stars = $review->rating;
                    $star_count = floor( $stars ); // count whole stars
                    $half_star_toggle = '';
                    if( $stars - floor( $stars ) > 0 ){
                        // add half star if required
                        $half_star_toggle = "ezrwp-plus-half-star";
                    } 

                    $stars = '
                        <div class="ezrwp-stars ezrwp-stars-'. $star_count .' '. $half_star_toggle .'"></div>
                    ';
                }
                $reviewer_info = '
                    <div class="ezrwp-reviewer">
                        <p> &mdash; Zillow Reviewer '. $reviewer_summary .'
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
                $reviews_output .= $reviewer_info;
                $reviews_output .= $after_review;

                $i++;
                if( $i % $number_cols == 0 ){
                    $reviews_output .= '<div style="clear:both"></div>';
                }
            endforeach;

            return $template->generate_reviews_wrapper( $reviews_output, $layout, $number_cols );
        }

        /**
         * Return a sentence fragment based on the value of the loanServiceProvided output parameter provided by Zillow.
         * 
         * @param  string  $loan_service_provided
         * @return string
         */ 
        private function format_loan_service_provided( $loan_service_provided ){
            $output = '';
            switch( $loan_service_provided ){
                case 'LoanClosed' :
                    $output = 'closed on';
                    break;
                case 'LoanInProgress' :
                    $output = 'is seeking';
                    break;
                case 'PreQualified' :
                    $output = 'pre-qualified for';
                    break;
                default :
                    $output = 'expressed interest in';
                    break;
            }
            return $output;
        }

        /**
         * Return a sentence fragment based on the value of the loanProgram output parameter provided by Zillow.
         * 
         * @param  string  $loan_program
         * @return string
         */ 
        private function format_loan_program( $loan_type, $loan_program ){

            /**
             * The Loan Type normally comes before the Loan Program. Add the
             * sentence article before the Loan Program if the Loan Type is blank.
             */
            $article = ( $loan_type === '' ) ? "a " : '';

            // Format loan purpose.
            $output = '';
            switch( $loan_program ){
                case 'Fixed30Year' :
                    $output = '30-year fixed-rate';
                    break;
                case 'Fixed20Year' :
                    $output = '20-year fixed-rate';
                    break;
                case 'Fixed15Year' :
                    $output = '15-year fixed-rate';
                    break;
                case 'Fixed10Year' :
                    $output = '10-year fixed-rate';
                    break;
                case 'ARM3' :
                    $output = '3-year ARM';
                    break;
                case 'ARM5' :
                    $output = '5-year ARM';
                    break;
                case 'ARM7' :
                    $output = '7-year ARM';
                    break;
                case 'HomeEquity30Year' :
                    $output = '30-year home equity';
                    break;
                case 'HomeEquity30YearDueIn15' :
                    $output = '30-year due in 15 home equity';
                    break;
                case 'HomeEquity15Year' :
                    $output = '15-year home equity';
                    break;
                case 'HELOC20Year' :
                    $output = '20-year home equity line of credit (HELOC)';
                    break;
                case 'HELOC15Year' :
                    $output = '15-year home equity line of credit (HELOC)';
                    break;
                case 'LowOrNoDown' :
                    $output = 'low or no down payment';
                    break;
                case 'InterestOnly' :
                    $article = ( $loan_type === '' ) ? "an " : '';
                    $output = 'interest-only';
                    break;
                default :
                    break;
            }
            $output = $article . $output;
            return $output;
        }

        /**
         * Return a sentence fragment based on the value of the loanPurpose output parameter provided by Zillow.
         * 
         * @param  string  $loan_purpose
         * @return string
         */ 
        private function format_loan_purpose( $loan_type, $loan_program, $loan_purpose ){

            /**
             * The Loan Type and Loan Program normally come before the Loan Purpose. Add the
             * sentence article before the Loan Purpose if the Loan Type and Program are blank.
             */
            $article = ( $loan_type === '' && $loan_program === '' ) ? "a " : '';

            // Format loan purpose.
            $output = '';
            switch( $loan_purpose ){
                case 'HomeEquity' :
                    $output = 'home equity';
                    break;
                case 'HELOC' :
                    $output = $loan_purpose;
                    break;
                default :
                    $output = strToLower( $loan_purpose );
                break;
            }
            $output = $article . $output;
            return $output;
        }

        /**
         * Return a sentence fragment based on the value of the loanType output parameter provided by Zillow.
         * 
         * @param  string  $loan_type
         * @return string
         */ 
        private function format_loan_type( $loan_type ){
            $output = '';
            switch( $loan_type ){
                case 'Conventional' :
                    $output = 'a ' . strToLower( $loan_type );
                    break;
                case 'Jumbo' :
                    $output = 'a ' . strToLower( $loan_type );
                    break;
                case 'FHA' :
                    $output = 'an ' . $loan_type;
                    break;
                case 'VA' :
                    $output = 'a ' . $loan_type;
                    break;
                case 'USDA' :
                    $output = 'a ' . $loan_type;
                    break;
                case 'Other' :
                    $output = 'a';
                    break;
                default :
                    $output = $loan_type;
                break;
            }
            return $output;
        }
        
        /**
         * Return a string of HTML for the profile card.
         * 
         * @since    1.2.1
         * @param    int        $rating             The average rating for all of the professional's ratings.
         * @param    int        $review_count       The number of reviews for the professional.
         * @return   string                         The modified data.
         */
        function get_profile_card( $rating, $review_count ){
            
            $whole_stars = round($rating); // count whole stars
            $half_star_toggle = '';
            if( $rating - $whole_stars > 0 ){
                // add half star if required
                $half_star_toggle = "ezrwp-plus-half-star";
            }
            $stars = '
                <div class="ezrwp-lender-star-average ezrwp-stars ezrwp-stars-'. $whole_stars .' '. $half_star_toggle .'"></div>
            ';

            $profile_card = '
                <div style="clear: both"></div>
                <div class="ezrwp-lender-profile-card">
                    '. $stars .'
                    <div class="ezrwp-lender-activity">
                        '. round( $rating, 2 ) .' Stars • '. $review_count .' Reviews
                    </div>
                </div>
            ';
            return $profile_card;
        }
        
        /**
         * Add premium options to Gutenberg block
         * 
         * @since    1.2.0
         * @param    Easy_Zillow_Reviews_Data  $user_options        The data that will be modified.
         * @return   Easy_Zillow_Reviews_Data  The modified data.
         */
        function update_options_in_block( $user_options ){

            // Get the available API's
            $available_apis = $user_options->get_available_apis();

            // Add Lender & Loan Office option (enables reviews from Zillow Mortgage API)
            array_push( $available_apis, array( 'lender', 'Lender & Loan Officer' ) );

            // Update list of available API's with new option(s)
            $user_options->set_available_apis( $available_apis );

            // Return updated user options
            return $user_options;
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
            
            // If the user selected the Lender & Loan Officer Reviews type option, update the reviews output.
            if( isset( $attributes[ 'reviewsType' ] ) ){
                if( $attributes[ 'reviewsType' ] == 'lender' ){

                    // Get this Eazy_Zillow_Reviews_Lender object instance.
                    $reviews = $this;

                    // Parse attributes selected by the user in the Gutenberg block.
                    $layout = isset( $attributes[ 'reviewsLayout' ] ) ? $attributes[ 'reviewsLayout' ] : $reviews->get_layout();
                    $cols = isset( $attributes[ 'gridColumns' ] ) ? $attributes[ 'gridColumns' ] : $reviews->get_grid_columns();
                    $count = isset( $attributes[ 'reviewsCount' ] ) ? $attributes[ 'reviewsCount' ] : $reviews->get_count();
                    
                    // Overwite the Gutenberg block output with lender reviews from this object instance.
                    $output = $reviews->get_reviews_output( $reviews, $layout, $cols, $count );
                }
            }

            // Return the updated output.
            return $output;
        }

        /**
         * Get the value of lender_reviews_options
         *
         * @since    1.1.4
         */
        public function get_lender_reviews_options()
        {
                return $this->lender_reviews_options;
        }

        /**
         * Set the value of lender_reviews_options
         *
         * @return  self
         */ 
        public function set_lender_reviews_options($lender_reviews_options)
        {
                $this->lender_reviews_options = $lender_reviews_options;

                return $this;
        }
        
        /**
         * Get the value of zmpid
         *
         * @since    1.1.4
         */
        public function get_zmpid(){

            return $this->zmpid;
        }

        /**
         * Set the value of zmpid
         *
         * @return  self
         */ 
        public function set_zmpid($zmpid){

            $this->zmpid = $zmpid;
            return $this;
        }
        
        /**
         * Get the value of nmlsid
         *
         * @since    1.1.4
         */
        public function get_nmlsid(){

            return $this->nmlsid;
        }

        /**
         * Set the value of nmlsid
         *
         * @return  self
         */ 
        public function set_nmlsid($nmlsid){

            $this->nmlsid = $nmlsid;
            return $this;
        }
        
        /**
         * Get the value of company_name
         *
         * @since    1.1.4
         */
        public function get_company_name(){

            return $this->company_name;
        }

        /**
         * Set the value of company_name
         *
         * @return  self
         */ 
        public function set_company_name($company_name){

            $this->company_name = $company_name;
            return $this;
        }
    }
}
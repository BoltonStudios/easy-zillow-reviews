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
         * 
         *
         * @since    1.1.0
         * @access   private
         * @var      string   $zillow_api_url    The URL for the Reviews API Web Service.
         */
        private $zillow_api_url;

        /**
         * 
         *
         * @since    1.1.0
         * @access   private
         * @var      string   $zwsid    The Zillow Web Service Identifier.
         */
        private $zwsid;

        /**
         * 
         *
         * @since    1.1.0
         * @access   private
         * @var      string   $screenname   The screenname of the user whose reviews will be fetched
         */
        private $screenname;

        /**
         * 
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
         * Get lender reviews data from Zillow using the Zillow ProReviews API.
         *
         * @since    1.1.0
         */
        public function fetch_reviews_from_zillow($count){
            
            $zwsid = $this->get_zwsid();
            $screenname = $this->get_screenname();
            $disallowed_characters = array("-", " ");
            $screenname = str_replace($disallowed_characters, "%20", $screenname);
            $toggle_team_members = $this->get_show_team_members() ? '&returnTeamMemberReviews=true' : '';
            
            // Contstruct the URL for a Zillow Professional.
            $url = 'https://www.zillow.com/webservice/ProReviews.htm?zws-id='. $zwsid .'&screenname='. $screenname .'&count='. $count . $toggle_team_members;

            /*
            *
            *  LOCAL DEVELOPMENT ONLY. Comment out for Production
            * 
            *
            *
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $assertion = file_get_contents($url, false, stream_context_create($arrContextOptions));
            $xml = simplexml_load_string($assertion);
            /*
            *
            *  END LOCAL DEVELOPMENT ONLY.
            */

            /*
            *
            *  PRODUCTION ONLY. Uncomment for Production
            * 
            */
            // Fetch data from Zillow.
            $xml = simplexml_load_file($url) or die("Error: Cannot create object");
            /*
            *
            *  END PRODUCTION ONLY.
            */
            
            // Pass data from Zillow to this class instance.
            $this->set_message($xml->message->text);
            $this->set_has_reviews(( $xml->message->code > 0 ) ? false : true);
            if($this->get_has_reviews()){

                // Success
                $this->set_info($xml->response->result->proInfo);
                $this->set_url($xml->response->result->proInfo->profileURL);
                $this->set_review_count($xml->response->result->proInfo->reviewCount);
                $this->set_reviews($xml->response->result->proReviews);
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
            $hide_view_all_link = $this->get_hide_view_all_link();
            $hide_zillow_logo = $this->get_hide_zillow_logo();
            $layout = ($as_layout == '') ? $this->get_layout() : $as_layout;
            $number_cols = ($number_cols == '') ? $this->get_grid_columns() : $number_cols;
            $profile_url = $this->get_url();
            $review_count = $this->get_review_count();

            // Output
            $i = 0;
            $reviews_output = '';
            $template = new Easy_Zillow_Reviews_Template_Loader();
            $template->set_hide_disclaimer( $hide_disclaimer );
            $template->set_hide_view_all_link( $hide_view_all_link );
            $template->set_hide_zillow_logo( $hide_zillow_logo );
            $template->set_profile_url( $profile_url );
            $template->set_review_count( $review_count );

            // Professional Reviews
            foreach($this->reviews->review as $review) :
                $description = $review->description;
                $summary = lcfirst($review->reviewSummary);
                $url = $review->reviewURL;
                $date = ( !$hide_date ) ? '<div class="ezrwp-date">'. $template->convert_date_to_time_elapsed(date( "Y-m-d", strtotime($review->reviewDate))) .'</div>' : '';
                $reviewer_summary = ( !$hide_reviewer_summary ) ? '<span class="review-summary">who '. $summary .'</span>' : '';
                $stars = '';
                if( !$hide_stars ){
                    $stars = $review->rating;
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

            return $template->generate_reviews_wrapper($reviews_output, $layout, $number_cols);
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
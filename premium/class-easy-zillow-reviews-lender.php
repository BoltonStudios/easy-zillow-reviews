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

class Easy_Zillow_Reviews_Lender extends Easy_Zillow_Reviews_Data{

	/**
	 * The user's settings from the Lender Reviews admin tab.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      array   $lender_reviews_options  The user's settings from the Lender Reviews admin tab.
	 */
    private $lender_reviews_options;

	/**
	 * 
	 *
	 * @since    1.1.4
	 * @access   private
	 * @var      string   $zwsid    The.
	 */
    private $zmpid;

	/**
	 * 
	 *
	 * @since    1.1.4
	 * @access   private
	 * @var      string   $screenname   The
	 */
    private $nmlsid;

	/**
	 * 
	 *
	 * @since    1.1.4
	 * @access   private
	 * @var      string   $company_name   The
	 */
    private $company_name;

    // Constructor
    public function __construct(){

        $this->init();
        $this->set_lender_reviews_options( get_option('ezrwp_lender_reviews_options') );
        $this->set_zmpid( $this->lender_reviews_options['ezrwp_zmpid'] );
        $this->set_nmlsid( $this->lender_reviews_options['ezrwp_nmlsid'] );
    }

    // Methods
    
    /**
     * Get lender reviews data from Zillow using the Zillow Mortgage API.
     *
     * @since    1.1.0
     */
    public function fetch_reviews_from_zillow($count){
        
        $zmpid = $this->get_zmpid();
        $nmlsid = $this->get_nmlsid();
        $disallowed_characters = array("-", " ");
        $company_name = $this->get_company_name();
        $company_name = str_replace($disallowed_characters, "%20", $company_name);

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
    public function layout_lender_reviews($as_layout, $number_cols){

        // User Options
        $hide_date = $this->get_hide_date();
        $hide_stars = $this->get_hide_stars();
        $hide_reviewer_summary = $this->get_hide_reviewer_summary();
        $hide_disclaimer = $this->get_hide_disclaimer();
        $hide_view_all_link = $this->get_hide_view_all_link();
        $hide_zillow_logo = $this->get_hide_zillow_logo();
        $layout = ($as_layout == '') ? $this->get_layout() : $as_layout;
        $number_cols = ($number_cols == '') ? $this->get_grid_columns() : $number_cols;

        // Output
        $i = 0;
        $reviews_output = '';
        $template = new Easy_Zillow_Reviews_Template_Loader();
        $template->set_hide_disclaimer( $hide_disclaimer );
        $template->set_hide_view_all_link( $hide_view_all_link );
        $template->set_hide_zillow_logo( $hide_zillow_logo );

        // Lender Reviews
        foreach($this->reviews as $review) :
            $reviewer_name = $review->reviewerName->displayName;
            $description = $review->content;

            // Check if these properties exist in the Zillow Reviews API response and store their values
            $loan_service_provided = property_exists($review, 'serviceProvided') ? $this->format_loan_service_provided($review->serviceProvided) : '';
            $loan_program = property_exists($review, 'loanProgram') ? $this->format_loan_program( $review->loanProgram ) : '';
            $loan_purpose = property_exists($review, 'loanPurpose') ? $this->format_loan_purpose($review->loanPurpose) : '';
            $loan_type = property_exists($review, 'loanType') ? $this->format_loan_type($review->loanType) : '';
            $review_date = $review->created;

            $loan_summary = $loan_service_provided . " " . $loan_type . " " . $loan_program . " " . $loan_purpose;

            if( $hide_date == false ){
                $date = 
                    '<div class="ezrwp-date">
                        '. $template->convert_date_to_time_elapsed(date( "Y-m-d", strtotime($review_date))) .'
                    </div>';
            }
            if( $hide_stars == false ){
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
            if( $hide_reviewer_summary == false ){
                $reviewer_summary = '<span class="review-summary">who '. $loan_summary .' loan.</span>';
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

        return $template->generate_reviews_wrapper($reviews_output, $layout, $number_cols);
    }

    /**
     * Return a sentence fragment based on the value of the loanServiceProvided output parameter provided by Zillow.
     * 
     * @return string
     */ 
    private function format_loan_service_provided($loan_service_provided){
        $output = '';
        switch($loan_service_provided){
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
     * @return string
     */ 
    private function format_loan_program( $loan_program ){
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
                $output = 'interest-only';
                break;
            default :
                break;
        }
        return $output;
    }

    /**
     * Return a sentence fragment based on the value of the loanPurpose output parameter provided by Zillow.
     * 
     * @return string
     */ 
    private function format_loan_purpose($loan_purpose){
        $output = '';
        switch($loan_purpose){
            case 'HomeEquity' :
                $output = 'home equity';
                break;
            case 'HELOC' :
                $output = $loan_purpose;
                break;
            default :
                $output = strToLower($loan_purpose);
            break;
        }
        return $output;
    }

    /**
     * Return a sentence fragment based on the value of the loanType output parameter provided by Zillow.
     * 
     * @return string
     */ 
    private function format_loan_type($loan_type){
        $output = '';
        switch($loan_type){
            case 'Conventional' :
                $output = 'a ' . strToLower($loan_type);
                break;
            case 'Jumbo' :
                $output = 'a ' . strToLower($loan_type);
                break;
            case 'FHA' :
                $output = 'an ' . $loan_type;
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
?>
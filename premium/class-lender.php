<?php
/*
* Easy Zillow Reviews Lender
*/
if(!class_exists('EasyZillowReviewsLender')){
    
class EasyZillowReviewsLender extends EasyZillowReviewsData{
        
        // Vars
        private $lender_reviews_options;

        // Constructor
        public function __construct(){
        }

        // Methods
        public function fetch_reviews_from_zillow($count){

            $zmpid= $this->lender_reviews_options['ezrwp_zmpid'];
            $nlmsid = $this->lender_reviews_options['ezrwp_nlmsid'];
            $company_name = $this->lender_reviews_options['ezrwp_company_name'];

            // Contstruct the Zillow URL for an Individual Loan Officer.
            $zillow_url = 'https://mortgageapi.zillow.com/zillowLenderReviews?partnerId='. $zmpid .'&nmlsId='.$nlmsid.'&reviewLimit='. $count;

            // If the user set a Company Name, add it to the Zillow URL to fetch Company reviews.
            if($company_name != ''){
                $company_name = str_replace(' ', '%20', $company_name);
                $zillow_url = $zillow_url . '&companyName='.$company_name;
            }

            // Fetch data from Zillow.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $zillow_url);
            $result = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($result);

            // Pass data to EasyZillowReviewsData object.
            $this->set_has_reviews(($json->error) ? false : true);
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
         * Get the value of lender_reviews_options
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
    }
}
?>
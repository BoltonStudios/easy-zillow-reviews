<?php
/*
* Easy Zillow Reviews Professional
*/
if(!class_exists('EasyZillowReviewsProfessional')){
    
class EasyZillowReviewsProfessional extends EasyZillowReviewsData{
        
        // Vars
        private $professional_reviews_options;

        // Constructor
        public function __construct(){
        }

        // Methods
        public function fetch_reviews_from_zillow($count){

            $zwsid = $this->professional_reviews_options['ezrwp_zwsid'];
            $screenname = $this->professional_reviews_options['ezrwp_screenname'];

            // Contstruct the URL for a Zillow Professional.
            $url = 'https://www.zillow.com/webservice/ProReviews.htm?zws-id='. $zwsid .'&screenname='. $screenname .'&count='. $count;

            // Fetch data from Zillow.
            $xml = simplexml_load_file($url) or die("Error: Cannot create object");

            // Pass data to object.
            $this->set_message($xml->message->text);
            $this->set_has_reviews(( $xml->message->code > 0 ) ? false : true);
            if($this->get_has_reviews()){

                // Success
                $this->set_info($xml->response->result->proInfo);
                $this->set_url($xml->response->result->proInfo->profileURL);
                $this->set_review_count($xml->response->result->proInfo->review_count);
                $this->set_reviews($xml->response->result->proReviews);
            }
        }

        /**
         * Get the value of professional_reviews_options
         */ 
        public function get_professional_reviews_options()
        {
                return $this->professional_reviews_options;
        }

        /**
         * Set the value of professional_reviews_options
         *
         * @return  self
         */ 
        public function set_professional_reviews_options($professional_reviews_options)
        {
                $this->professional_reviews_options = $professional_reviews_options;

                return $this;
        }
    }
}
?>
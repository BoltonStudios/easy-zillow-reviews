<?php
/*
* Easy Zillow Reviews Data
*/
if(!class_exists('EasyZillowReviewsData')){
    
class EasyZillowReviewsData{
        
        // Vars
        private $layout;
        private $grid_columns;
        private $count;
        private $has_reviews;
        private $message;
        private $general_options;
        private $url;
        private $info;
        private $review_count;
        private $reviews;

        // Constructor
        public function __construct(){
        }
        public function generate_reviews_wrapper($reviews, $as_layout, $number_cols){

            // User Options
            $hide_disclaimer = isset($this->general_options['ezrwp_disclaimer']) == 1 ? true : false;
            $hide_view_all_link = isset($this->general_options['ezrwp_hide_view_all_link']) == 1 ? true : false;
            $hide_zillow_logo = isset($this->general_options['ezrwp_hide_zillow_logo']) == 1 ? true : false;

            // Layout Options
            // Get Layout
            $layout = $this->layout;
            if( isset($as_layout) && $as_layout != null ){
                $layout = $as_layout;
            }

            // Get Grid Columns
            $grid_columns = $this->grid_columns;
            if( $layout == "grid" ){
                // Grid
                if(isset($number_cols) && $number_cols != null){
                    $grid_columns = $number_cols;
                }
            }
            $grid_columns_class = ($layout == "grid" && $grid_columns > 0) ? 'ezrwp-grid-'. $grid_columns : '';

            // Other options
            $view_all_link = '';
            if( !$hide_view_all_link ){
                $view_all_link = '<p class="ezrwp-call-to-action"><a href="'. $this->url . '#reviews" class="z-profile-link" target="_blank" rel="nofollow">View all '. $this->info->reviewCount .' reviews.</a></p>';
            }

            $zillow_logo = '';
            if( !$hide_zillow_logo ){
                $zillow_logo = '<p class="ezrwp-attribution"><a href="'. $this->url . '" class="z-profile-link" target="_blank" rel="nofollow"><img src="https://www.zillow.com/widgets/GetVersionedResource.htm?path=/static/logos/zillow_logo_200x50.gif" width="150" height="38" alt="Real Estate on Zillow"></a></p>';
            }

            $mandatory_disclaimer = '';
            if( !$hide_disclaimer ){
                $mandatory_disclaimer = '
                    <p class="ezrwp-mandatory-disclaimer">© Zillow, Inc., 2006-2016. Use is subject to <a href="https://www.zillow.com/corp/Terms.htm" target="_blank">Terms of Use</a><br />
                        <a href="https://www.zillow.com/wikipages/What-is-a-Zestimate/" target="_blank">What\'s a Zestimate?</a>
                    </p>';
            }

            $wrapper_start = '<div class="ezrwp-wrapper ezrwp-'. $layout .' '. $grid_columns_class .'"><div class="ezrwp-content">';
            $wrapper_end = '</div></div>';

            $output = $wrapper_start;
            $output .= $reviews;
            $output .= $view_all_link;
            $output .= $zillow_logo;
            $output .= $mandatory_disclaimer;
            $output .= $wrapper_end;

            return $output;

        }
        public function layout_reviews($as_layout, $number_cols){

            // User Options
            $hide_date = isset($this->general_options['ezrwp_hide_date']) == 1 ? true : false;
            $hide_stars = isset($this->general_options['ezrwp_hide_stars']) == 1 ? true : false;
            $hide_reviewer_summary = isset($this->general_options['ezrwp_hide_reviewer_summary']) == 1 ? true : false;
            $layout = ($as_layout == '') ? $this->layout : $as_layout;
            $number_cols = ($number_cols == '') ? $this->grid_columns : $number_cols;

            // Output
            $i = 0;
            $reviews = '';

            // Professional Reviews
            foreach($this->reviews->review as $review) :
                $description = $review->description;
                $summary = lcfirst($review->reviewSummary);
                $url = $review->reviewURL;
                if( !$hide_date ){
                    $date = 
                        '<div class="ezrwp-date">
                            '. $this->convert_date_to_time_elapsed(date( "Y-m-d", strtotime($review->reviewDate))) .'
                        </div>';
                }
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
                if( !$hide_reviewer_summary ){
                    $reviewer_summary = '<span class="review-summary">who '. $summary .'</span>';
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

                $reviews .= $before_review;
                $reviews .= $review_quote;
                $reviews .= $stars;
                $reviews .= $date;
                $reviews .= $reviewer;
                $reviews .= $after_review;

                $i++;
                if( $i % $number_cols == 0 ){
                    $reviews .= '<div style="clear:both"></div>';
                }
            endforeach;

            return $this->generate_reviews_wrapper($reviews, $layout, $number_cols);
        }
        // Convert date to time elapsed
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
         * Get the value of layout
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
        public function set_reviews($reviews)
        {
                $this->reviews = $reviews;

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
                $this->options = $general_options;

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
    }
}
?>
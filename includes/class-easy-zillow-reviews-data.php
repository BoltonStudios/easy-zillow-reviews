<?php

/**
 * The Easy_Zillow_Reviews_Data class
 *
 * Adds the [ez-zillow-reviews] shortcode to WordPress
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

class Easy_Zillow_Reviews_Data{
    /**
	 * The layout for reviews.
        * The user may select "grid" or "list" from the shortcode, widget, or admin settings.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string   $layout  TThe layout for reviews.
	 */
    private $layout;

	/**
	 * The number of grid columns in the grid layout.
     * The user may select a number between 2 and 6.
     *
	 * @since    1.1.0
	 * @access   private
	 * @var      int   $grid_columns    The number of grid columns in the grid layout.
	 */
    private $grid_columns;
    
	/**
	 * The number of reviews to display.
     * The user may select a number between 1 and 10.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      int   $count   The number of reviews to display.
	 */
    private $count;
    
	/**
	 * The state of having reviews or not having reviews.
     * This is determined by the results of the plugin's call to the Zillow API Network.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      boolean   $has_reviews   The state of having reviews or not having reviews.
	 */
    private $has_reviews;
    
	/**
	 * The message returned by the Zillow API Network.
     * This is determined by the results of the plugin's call to the Zillow API Network.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string   $message   The message returned by the Zillow API Network.
	 */
    private $message;
    
	/**
	 * The user's saved plugin options.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      object   $general_options   The user's saved plugin options.
	 */
    private $general_options;
    
	/**
	 * The URL for the individual review returned by the Zillow API Network.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string   $url   The URL for the individual review returned by the Zillow API Network.
	 */
    private $url;
    
	/**
	 * The profile information for the Zillow account selected by the user.
	 * This is determined by the results of the plugin's call to the Zillow API Network.
	 * @since    1.1.0
	 * @access   private
	 * @var      string   $info
	 */
    private $info;
    
	/**
	 * 
     * 
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      bool   $hide_date   
	 */
    private $hide_date;
    
	/**
	 * The total number of reviews that this profile has on Zillow.
     * This is determined by the results of the plugin's call to the Zillow API Network.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      int   $review_count   
	 */
    private $review_count;
    
	/**
	 * The reviews returned by the plugin's call to the Zillow API Network.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      array   $reviews   
	 */
    protected $reviews;

    // Constructor
    public function __construct(){
        add_action('plugins_loaded', array($this, 'init'));
    }

    // Methods
    public function init(){

        // Get saved admin settings and defaults
        $general_options = get_option('ezrwp_general_options'); // General admin tab settings
        $layout = isset($general_options['ezrwp_layout']) ? $general_options['ezrwp_layout'] : 'list';
        $grid_columns = isset($general_options['ezrwp_cols']) ? $general_options['ezrwp_cols'] : 3;
        $count = isset($general_options['ezrwp_count']) ? $general_options['ezrwp_count'] : 3;
        
        // Pass saved admin settings to this Easy_Zillow_Reviews_Professional_Shortcode class instance
        $this->set_general_options($general_options);
        $this->set_layout($layout);
        $this->set_grid_columns($grid_columns);
        $this->set_count($count);
        $this->set_hide_date($hide_date = isset($this->get_general_options()['ezrwp_hide_date']) == 1 ? true : false);
    }

    /**
     * Build the HTML output for the Zillow Reviews
     */ 
    public function generate_reviews_wrapper($reviews_output, $as_layout, $number_cols){

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
            $zillow_logo = '<p class="ezrwp-attribution"><a href="'. $this->url . '" class="z-profile-link" target="_blank" rel="nofollow"><img src="//www.zillow.com/widgets/GetVersionedResource.htm?path=/static/logos/Zillowlogo_200x50.gif" width="150" height="38" alt="Real Estate on Zillow"></a></p>';
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
        $output .= $reviews_output;
        $output .= $view_all_link;
        $output .= $zillow_logo;
        $output .= $mandatory_disclaimer;
        $output .= $wrapper_end;

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
     * Get the value of layout
     *
     * @since    1.1.0
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
     *
     * @since    1.1.0
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
     *
     * @since    1.1.0
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
    public function set_reviews($reviews_output)
    {
            $this->reviews = $reviews_output;

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

    /**
     * Get the value of $hide_date
     */ 
    public function get_hide_date()
    {
            return $this->hide_date;
    }

    /**
     * Set the value of $hide_date
     *
     * @return  self
     */ 
    public function set_hide_date($hide_date)
    {
            $this->hide_date = $hide_date;

            return $this;
    }
}
?>
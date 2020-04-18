<?php

/**
 * The Easy_Zillow_Reviews_Template class
 *
 * 
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.4
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

class Easy_Zillow_Reviews_Template_Loader{
    
	/**
	 * 
     * 
	 *
	 * @since    1.1.4
	 * @access   private
	 * @var      bool   $hide_disclaimer  
	 */
    private $hide_disclaimer;
    
	/**
	 * 
     * 
	 *
	 * @since    1.1.4
	 * @access   private
	 * @var      bool   $hide_view_all_link  
	 */
    private $hide_view_all_link;
    
	/**
	 * 
     * 
	 *
	 * @since    1.1.4
	 * @access   private
	 * @var      bool   $hide_zillow_logo
	 */
    private $hide_zillow_logo;
    
	/**
	 * 
     * 
	 *
	 * @since    1.1.4
	 * @access   private
	 * @var      bool   $profile_url
	 */
    private $profile_url;
    
	/**
	 * 
     * 
	 *
	 * @since    1.1.4
	 * @access   private
	 * @var      bool   $review_count
	 */
    private $review_count;


    // Constructor
    public function __construct(){
    }

    // Methods
    public function init(){
    }

    /**
     * Build the HTML output for the Zillow Reviews
     */ 
    public function generate_reviews_wrapper( $reviews_output, $layout, $grid_columns ){

        // User Options
        $hide_disclaimer = $this->get_hide_disclaimer();
        $hide_view_all_link = $this->get_hide_view_all_link();
        $hide_zillow_logo = $this->get_hide_zillow_logo();
        $profile_url = $this->get_profile_url();
        $review_count = $this->get_review_count();

        // Layout Options
        $grid_columns_class = ($layout == "grid" && $grid_columns > 0) ? 'ezrwp-grid-'. $grid_columns : '';

        // Other options
        $view_all_link = '';
        if( $hide_view_all_link == false ){
            $view_all_link = '<p class="ezrwp-call-to-action"><a href="'. $profile_url . '#reviews" class="z-profile-link" target="_blank" rel="nofollow">View all '. $review_count .' reviews.</a></p>';
        }

        $zillow_logo = '';
        if( $hide_zillow_logo == false ){
            $zillow_logo = '<p class="ezrwp-attribution"><a href="'. $profile_url . '" class="z-profile-link" target="_blank" rel="nofollow"><img src="//www.zillow.com/widgets/GetVersionedResource.htm?path=/static/logos/Zillowlogo_200x50.gif" width="150" height="38" alt="Real Estate on Zillow"></a></p>';
        }

        $mandatory_disclaimer = '';
        if( $hide_disclaimer == false ){
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
     * 
     *
     * @since    1.1.4
     */
    public function generate_inline_styles(){
                
        // User Options
        $options = $GLOBALS['ezrwpOptions'];
        $quote_font_size = $options['ezrwp_quote_font_size'];
        $reviewer_description_font_size = $options['ezrwp_reviewer_description_font_size'];
        $quote_styles = '';

        // Styles
        if( $quote_font_size != null && $quote_font_size != '' ){
            $quote_styles = '
            /* Review Quote Font Size */
            body .ezrwp-wrapper .ezrwp-content blockquote,
            body .entry-content .ezrwp-wrapper .ezrwp-content blockquote{
                font-size: ' . $quote_font_size . 'px;
            }
            ';
        }
        if( $reviewer_description_font_size != null && $reviewer_description_font_size != '' ){
            $reviewer_description_font_size = '
            /* Reviewer Description Font Size */
            body .ezrwp-wrapper .ezrwp-content .ezrwp-reviewer p,
            body .ezrwp-wrapper .ezrwp-content .ezrwp-reviewer *{
                font-size: '. $reviewer_description_font_size .'px;
            }
            ';
        }
        $before_inline_styles = '<!-- Easy Zillow Reviews Inline Styles --><style>';
        $after_inline_styles = '</style>';
        $inline_styles = $before_inline_styles;
        $inline_styles .= $quote_styles;
        $inline_styles .= $reviewer_description_font_size;
        $inline_styles .= $after_inline_styles;

        return $inline_styles;
        
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
     * Get the value of $hide_disclaimer
     */ 
    public function get_hide_disclaimer()
    {
            return $this->hide_disclaimer;
    }

    /**
     * Set the value of $hide_disclaimer
     *
     * @return  self
     */ 
    public function set_hide_disclaimer($hide_disclaimer)
    {
            $this->hide_disclaimer = $hide_disclaimer;

            return $this;
    }

    /**
     * Get the value of $hide_view_all_link
     */ 
    public function get_hide_view_all_link()
    {
            return $this->hide_view_all_link;
    }

    /**
     * Set the value of $hide_view_all_link
     *
     * @return  self
     */ 
    public function set_hide_view_all_link($hide_view_all_link)
    {
            $this->hide_view_all_link = $hide_view_all_link;

            return $this;
    }

    /**
     * Get the value of $hide_zillow_logo
     */ 
    public function get_hide_zillow_logo()
    {
            return $this->hide_zillow_logo;
    }

    /**
     * Set the value of $hide_zillow_logo
     *
     * @return  self
     */ 
    public function set_hide_zillow_logo($hide_zillow_logo)
    {
            $this->hide_zillow_logo = $hide_zillow_logo;

            return $this;
    }

    /**
     * Get the value of $profile_url
     */ 
    public function get_profile_url()
    {
            return $this->profile_url;
    }

    /**
     * Set the value of $profile_url
     *
     * @return  self
     */ 
    public function set_profile_url($profile_url)
    {
            $this->profile_url = $profile_url;

            return $this;
    }

    /**
     * Get the value of $review_count
     */ 
    public function get_review_count()
    {
            return $this->review_count;
    }

    /**
     * Set the value of $review_count
     *
     * @return  self
     */ 
    public function set_review_count($review_count)
    {
            $this->review_count = $review_count;

            return $this;
    }
}
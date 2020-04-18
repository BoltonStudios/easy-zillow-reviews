<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       https://www.boltonstudios.com
 * @since      1.0.0
 *
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/public
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */
class Easy_Zillow_Reviews_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The ID of this plugin.
	 */
	private $plugin_slug;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
    
	/**
	 * 
	 *
	 * @since    1.1.5
	 * @access   protected
	 * @var      int   $quote_font_size  
	 */
    protected $quote_font_size;
    
	/**
	 * 
	 *
	 * @since    1.1.5
	 * @access   protected
	 * @var      int   $reviewer_description_font_size  
	 */
    protected $reviewer_description_font_size;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_slug       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_slug, $version ) {

		$this->plugin_slug = $plugin_slug;
        $this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Easy_Zillow_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Easy_Zillow_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'css/easy-zillow-reviews-public.css', array(), $this->version, 'all' );
        //wp_add_inline_style( $this->plugin_slug, $this->generate_inline_styles() );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Easy_Zillow_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Easy_Zillow_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'js/easy-zillow-reviews-public.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * 
     *
     * @since    1.1.5
     */
    public function generate_inline_styles(){

        $quote_font_size = $this->get_quote_font_size();
        $reviewer_description_font_size = $this->get_reviewer_description_font_size();
        $quote_styles = '';

        // Styles
        if( $quote_font_size != null && $quote_font_size != '' ){
            $quote_styles = '
            body .ezrwp-wrapper .ezrwp-content blockquote,
            body .entry-content .ezrwp-wrapper .ezrwp-content blockquote{
                font-size: ' . $quote_font_size . 'px;
            }
            ';
        }
        if( $reviewer_description_font_size != null && $reviewer_description_font_size != '' ){
            $reviewer_description_font_size = '
            body .ezrwp-wrapper .ezrwp-content .ezrwp-reviewer p,
            body .ezrwp-wrapper .ezrwp-content .ezrwp-reviewer *{
                font-size: '. $reviewer_description_font_size .'px;
            }
            ';
        }
        $inline_styles = '';
        $inline_styles .= $quote_styles;
        $inline_styles .= $reviewer_description_font_size;

        return $inline_styles;
        
    }
    /**
     * Get the value of $quote_font_size
     */ 
    public function get_quote_font_size()
    {
            return $this->quote_font_size;
    }

    /**
     * Set the value of $quote_font_size
     *
     * @return  self
     */ 
    public function set_quote_font_size($quote_font_size)
    {
            $this->quote_font_size = $quote_font_size;

            return $this;
    }

    /**
     * Get the value of $reviewer_description_font_size
     */ 
    public function get_reviewer_description_font_size()
    {
            return $this->reviewer_description_font_size;
    }

    /**
     * Set the value of $reviewer_description_font_size
     *
     * @return  self
     */ 
    public function set_reviewer_description_font_size($reviewer_description_font_size)
    {
            $this->reviewer_description_font_size = $reviewer_description_font_size;

            return $this;
    }
}

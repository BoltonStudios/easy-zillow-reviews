<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 *
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.1.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */
class Easy_Zillow_Reviews {

	// Properties
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      Easy_Zillow_Reviews_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The name of this plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to title this plugin.
	 */
	protected $plugin_name;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $plugin_slug    The string used to uniquely identify this plugin.
	 */
	protected $plugin_slug;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * 
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      Easy_Zillow_Reviews_Admin_Settings    $plugin_settings
	 */
	private $plugin_settings;

	/**
	 *
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      Easy_Zillow_Reviews_Admin    $plugin_admin
	 */
	private $plugin_admin;
	
	// Constructor
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.1.0
	 */
	public function __construct() {
		if ( defined( 'EASY_ZILLOW_REVIEWS_VERSION' ) ) {
			$this->version = EASY_ZILLOW_REVIEWS_VERSION;
		} else {
			$this->version = '1.1.0';
		}
		$this->plugin_name = 'Easy Zillow Reviews';
		$this->plugin_slug = 'easy-zillow-reviews';

		$this->load_dependencies();
		$this->set_locale();

		$this->init();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}
	
	// Methods
	/**
	 * 
	 *
	 * @since    1.1.0
	 */
	public function init() {

		$this->plugin_settings = new Easy_Zillow_Reviews_Admin_Settings(
			$this->get_plugin_name(),
			$this->get_plugin_slug(),
			$this->get_version()
		);
		
		$this->plugin_admin = new Easy_Zillow_Reviews_Admin(
			$this->get_plugin_name(),
			$this->get_plugin_slug(),
			$this->get_version(),
			apply_filters('easy-zillow-reviews-settings-override', $this->get_plugin_settings())
		);
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Easy_Zillow_Reviews_Loader. Orchestrates the hooks of the plugin.
	 * - Easy_Zillow_Reviews_i18n. Defines internationalization functionality.
	 * - Easy_Zillow_Reviews_Admin. Defines all hooks for the admin area.
	 * - Easy_Zillow_Reviews_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * Include all the essential class files
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/includes.php';

		$this->loader = new Easy_Zillow_Reviews_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Easy_Zillow_Reviews_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Easy_Zillow_Reviews_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		
		$this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $this->plugin_admin, 'init_settings' );
		$this->loader->add_action( 'admin_menu', $this->plugin_admin, 'add_options_page');
		$this->loader->add_filter( 'plugin_action_links', $this->plugin_admin, 'admin_plugin_listing_actions');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Easy_Zillow_Reviews_Public( $this->get_plugin_slug(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	// Getters & Setters
	/**
	 * Retreive the name of the plugin used to title it within the WordPress Dashboard.
	 *
	 * @since     1.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.1.0
	 * @return    Easy_Zillow_Reviews_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 *
	 *
	 * @return  Easy_Zillow_Reviews_Admin_Settings
	 */ 
	public function get_plugin_settings()
	{
		return $this->plugin_settings;
	}

	/**
	 *
	 *
	 * @param  Easy_Zillow_Reviews_Admin_Settings  $plugin_settings  $instance
	 *
	 * @return  self
	 */ 
	public function set_plugin_settings(Easy_Zillow_Reviews_Admin_Settings $plugin_settings)
	{
		$this->plugin_settings = $plugin_settings;

		return $this;
	}

	/**
	 * 
	 *
	 * @return  Easy_Zillow_Reviews_Admin
	 */ 
	public function get_plugin_admin()
	{
		return $this->plugin_admin;
	}

	/**
	 *
	 *
	 * @param  Easy_Zillow_Reviews_Admin  $plugin_admin  $instance
	 *
	 * @return  self
	 */ 
	public function set_plugin_admin(Easy_Zillow_Reviews_Admin  $plugin_admin)
	{
		$this->plugin_admin = $plugin_admin;

		return $this;
	}
}

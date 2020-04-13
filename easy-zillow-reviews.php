<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.boltonstudios.com
 * @since             1.1.0
 * @package           Easy_Zillow_Reviews
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Zillow Reviews
 * Plugin URI:        https://wordpress.org/plugins/easy-zillow-reviews/
 * Description:       Display reviews from Zillow on your site.
 * Version:           1.1.0
 * Author:            Aaron Bolton
 * Author URI:        https://www.boltonstudios.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       easy-zillow-reviews
 * Domain Path:       /languages
 *
 * @fs_premium_only /premium/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EASY_ZILLOW_REVIEWS_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-easy-zillow-reviews-activator.php
 */
function activate_easy_zillow_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-zillow-reviews-activator.php';
	Easy_Zillow_Reviews_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-easy-zillow-reviews-deactivator.php
 */
function deactivate_easy_zillow_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-zillow-reviews-deactivator.php';
	Easy_Zillow_Reviews_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_easy_zillow_reviews' );
register_deactivation_hook( __FILE__, 'deactivate_easy_zillow_reviews' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-easy-zillow-reviews.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.1.0
 */
function run_easy_zillow_reviews() {

	// Freemius
	if (function_exists('ezrwp_fs')) {

		ezrwp_fs()->set_basename(true, __FILE__);
	} else {

		if (!function_exists('ezrwp_fs')) {

			// Create a helper function for easy SDK access.
			function ezrwp_fs()
			{

				global $ezrwp_fs;

				if (!isset($ezrwp_fs)) {

					// Include Freemius SDK.
					require_once dirname(__FILE__) . '/freemius/start.php';

					$ezrwp_fs = fs_dynamic_init(array(
						'id'                  => '4652',
						'slug'                => 'easy-zillow-reviews',
						'type'                => 'plugin',
						'public_key'          => 'pk_5fa54ca20b9465c9db23a010b43f5',
						'is_premium'          => true,
						'premium_suffix'      => 'Premium',
						// If your plugin is a serviceware, set this option to false.
						'has_premium_version' => true,
						'has_addons'          => false,
						'has_paid_plans'      => true,
						'menu'                => array(
							'slug'           => 'easy-zillow-reviews',
							'parent'         => array(
								'slug' => 'options-general.php',
							),
						),
					));
				}

				return $ezrwp_fs;
			}

			// Init Freemius.
			ezrwp_fs();

			// Signal that SDK was initiated.
			do_action('ezrwp_fs_loaded');
		}

		// Load the essential plugin features including settings and admin
		$plugin = new Easy_Zillow_Reviews();

		// Fetch Zillow Data and prepare it for use
		new Easy_Zillow_Reviews_Data();
		
		// Define plugin shortcodes
		new Easy_Zillow_Reviews_Professional_Shortcodes();

		// Define plugin widget
		new Easy_Zillow_Reviews_Professional_Widget_Init();

		// This IF block will be auto removed from the Free version.
		if ( ezrwp_fs()->is__premium_only() ) {

			// This IF will be executed only if the user in a trial mode or have a valid license.
			if ( ezrwp_fs()->can_use_premium_code() ) {

				// ... premium only logic ...
				require_once plugin_dir_path( __FILE__ ) . 'premium/class-easy-zillow-reviews-premium.php';

				// Load Premium Features and pass the plugin object to be modified
				new Easy_Zillow_Reviews_Premium($plugin);

				// Define Premium shortcodes
				new Easy_Zillow_Reviews_Lender_Shortcodes();
				
				// Define Premium widget
				new Easy_Zillow_Reviews_Lender_Widget_Init();
			}
        }
		
		// Run Plugin
		$plugin->run();
	}
}
run_easy_zillow_reviews();

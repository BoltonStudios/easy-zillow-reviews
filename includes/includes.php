<?php
/**
 * The class responsible for orchestrating the actions and filters of the
 * core plugin.
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-zillow-reviews-loader.php';

/**
 * The class responsible for defining internationalization functionality
 * of the plugin.
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-zillow-reviews-i18n.php';

/**
 * The class responsible for defining the plugin upgrade functionality.
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-zillow-reviews-upgrader.php';

/**
 * The class responsible for defining all actions that occur in the admin area.
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-easy-zillow-reviews-admin.php';

/**
 * The class responsible for defining all the settings in the plugin admin menu.
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-easy-zillow-reviews-admin-settings.php';

/**
 * The class responsible for defining all actions that occur in the public-facing
 * side of the site.
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-easy-zillow-reviews-public.php';

/**
 *
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-zillow-reviews-data.php';

/**
 *
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-easy-zillow-reviews-template-loader.php';

/**
 *
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-zillow-reviews-professional.php';

/**
 *
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-easy-zillow-reviews-professional-shortcodes.php';

/**
 *
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-easy-zillow-reviews-professional-widget.php';


/**
 *
 */
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'gutenberg/class-easy-zillow-reviews-gutenberg.php';
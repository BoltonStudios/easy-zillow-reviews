=== Easy Zillow Reviews ===
Contributors: boltonstudios
Donate link: http://ko-fi.com/boltonstudios
Tags: zillow, reviews, gutenberg, block, real estate, lender
Requires at least: 4.0.0
Tested up to: 5.8.1
Requires PHP: 5.4
Stable tag: 1.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display reviews from Zillow on your website.

== Description ==

*Easy Zillow Reviews* makes it easy for realtors and lenders to fetch and display reviews from the Zillow API Network.

View the [Demo](https://www.boltonstudios.com/easy-zillow-reviews/).

= Free Edition Features =
* Free for Real Estate Agents (individuals and teams)
* Display your reviews using the official Zillow API Network 
* Compliant with Zillow's [Terms of Use](https://www.zillow.com/corp/Terms.htm) and [Branding Requirements](https://www.zillow.com/howto/api/BrandingRequirements.htm)
* Grid and List layout options
* Widget and Shortcode options
* Gutenberg block (edit review layouts in the new WordPress editor!)
* Mobile responsive

= Mortgages Partner Edition Features =
* Lender Reviews
* Individual Loan Officer Reviews
* Company Profile Reviews 

= Requirements =

The Free Edition requires a [Zillow Web Services ID (ZWSID)](https://www.zillow.com/howto/api/APIOverview.htm).

The Mortgages Partner Edition requires a [Zillow Mortgages Partner ID (ZPID)](https://www.zillow.com/mortgage/api/).

= Limitations =

**Important**: Review the following limitations from [Zillow's Review API FAQ](https://www.zillow.com/howto/api/faq.htm) to avoid unexpected errors.

* Include the approved Zillow logo on your website wherever reviews appear. *Easy Zillow Reviews* includes this logo, but you can disable it in Settings > Easy Zillow Reviews if you wish to add it elsewhere adjacent to the Reviews per the [Branding Guidelines](https://www.zillow.com/howto/api/BrandingRequirements.htm).
* Include the Mandatory Disclaimer wherever reviews appear. *Easy Zillow Reviews* includes this disclaimer, but you can disable it in Settings > Easy Zillow Reviews if you wish to add it elsewhere (such as in the footer).
* No local storage of reviews. *Easy Zillow Reviews* will always fetch the latest reviews directly from Zillow's Reviews API using your ZWSID or ZPID.
* No more than 1,000 calls to the Reviews API in a day, and no more than 20 on one page at one time. *Easy Zillow Reviews* will display a simple text error message to help identify the problem if Zillow blocks your ZWSID or ZPID for any reason. Zillow may permit more calls for your account upon request. Per the FAQ, you may reach out to <huann@zillow.com> or <reviewsapi@zillow.com> for more info.

= Shortcode =

    [ez-zillow-reviews]
    [ez-zillow-lender-reviews]

= Optional Shortcode Parameters =

    [ez-zillow-reviews layout="grid" columns="2" count="4"]

* layout..."list" or "grid"
* columns...A number between 1 and 6
* count...A number between 1 and 10

== Installation ==

1. Upload "easy-zillow-reviews" to the /wp-content/plugins/ directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Add your Zillow Web Services ID (ZWSID) and Zillow Screenname to the Easy Zillow Reviews Settings page.
4. Adjust the Default Plugin Settings on the Easy Zillow Reviews Settings page (optional).
4. Add an *Easy Zillow Reviews* widget or [ez-zillow-reviews] shortcode to your page or post.
5. Save Changes.

== Screenshots ==

1. Zillow Reviews block editor
2. Shortcode output for [ez-zillow-reviews layout="grid" columns="2" count="2"]
3. Widget
4. Settings

== Changelog ==

= 1.3.1 =
* Date Updated: 2021-11-09
* Removed unused .po language files from Freemius SDK.
* Minor style changes to plugin CSS file.
* Changed the Profile Card text "sales in the last 12 months" to "recent home sales".
* Change the expected type of the Easy_Zillow_Reviews_Data object $reviews property from "array" to "object".

= 1.3.0 =
* Date Updated: 2021-09-09
* Updated Freemius SDK to version 2.4.2.

= 1.2.3 =
* Date Updated: 2020-29-05
* Bug Fix: Fixed bug that caused Reviews Summary badge to appear in-line with reviews instead of below them in certain grid layouts.
* Hardcoded blockquote line-height in public CSS file.
* Modified labels for a few Dashboard settings.

= 1.2.2 =
* Date Updated: 2020-28-05
* Minor style changes to plugin CSS file.

= 1.2.1 =
* Date Updated: 2020-28-05
* New Feature: Display a Zillow profile card with average star rating.
* Bug Fix: Added fallback to cURL for PHP configurations that have allow_url_fopen disabled.
* Bug Fix: Removed erroneous reference to a second Gutenberg block.
* Bug Fix: Removed two instances of Undefined Index errors in widgets and lender functions.
* Bug Fix: Improved sentence construction for loan descriptions in Premium version.
* Updated Freemius SDK to version 2.3.2.

= 1.2.0 =
* Date Updated: 2020-28-04
* New Feature: Gutenberg Block
* Moved the Settings page menu item to the top-level of the Dashboard Menu

= 1.1.7 =
* Date Updated: 2020-21-04
* Bug Fix: Fixed error that prevented review count and profile link from displaying in Mortgages Partner Edition.
* Bug Fix: Fixed error that caused "Settings" plugin action link to appear under other plugin names in the Plugin menu.
* Added attribution for function copied from FooGallery by Brad Vincent and Contributors.

= 1.1.6 =
* Date Updated: 2020-19-04
* Bug Fix: Fixed error that prevented Mortgages Partner Edition from activating while Free Edition was active.
* Bug Fix: Fixed error that prevented reviews for Lending Companies from displaying.

= 1.1.5 =
* Date Updated: 2020-18-04
* Bug Fix: Resolved PHP notices when certain elements are set to hidden and WP_DEBUG is true.
* Bug Fix: Restored feature to hide Reviewer Summary.

= 1.1.4 =
* Date Updated: 2020-18-04
* *NOTE* Due to changes in v1.1.x, please check your widgets to confirm that Easy Zillow Reviews is still active.
* Bug Fix: Changed plugin widget name back to 'ezrwp_widget' (free version) 
* Bug Fix: Restored options to style and hide reviews output.
* Bug Fix: Resolved miscellaneous notices when WP_DEBUG is true.

= 1.1.3 =
* Date Updated: 2020-13-04
* Added more fields to Upgrader class, which assists with backwards compatibility from v1.0 to v1.1.

= 1.1.2 =
* Date Updated: 2020-13-04
* Bug Fix: Migrated backwards compatibility patch to main plugin file to better support updates, in addition to activations.

= 1.1.1 =
* Date Updated: 2020-13-04
* Bug Fix: Added backwards compatibility for new database option names.

= 1.1.0 =
* Date Updated: 2020-13-04
* New Feature: Option to upgrade for access to Lender Reviews.
* New Feature: Tabbed Settings interface improves organization of plugin options.
* Bug Fix: Added fix to prevent errors generated by dashes and spaces in screennames.

**Premium Version**
* New Feature: Lender Reviews
* New Feature: Individual Loan Officer Reviews
* New Feature: Company Profile Reviews

= 1.0.3 =
* Date Updated: 2019-03-01
* Bug Fix: Replaced dashicons with image sprite.

= 1.0.2 =
* Date Updated: 2018-31-12
* Bug Fix: Fixed typo in admin Settings page.

= 1.0.1 =
* Date Updated: 2018-31-12
* New Feature: New Settings Options: Hide Zillow Logo and "View All" link.
* New Feature: Added "Settings" plugin action link.
* Bug Fixes: Resolved miscellaneous notices when WP_DEBUG is true.
* Added support for PHP 5.4, 5.5, 5.6 and 7.2.

= 1.0.0 =
* Date Released: 2018-30-12
* Initial Release
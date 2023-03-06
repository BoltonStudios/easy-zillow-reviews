=== Easy Zillow Reviews ===
Contributors: boltonstudios
Donate link: http://ko-fi.com/boltonstudios
Tags: zillow, reviews, gutenberg, block, real estate, lender
Requires at least: 4.0.0
Tested up to: 6.1.1
Requires PHP: 5.4
Stable tag: 1.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display reviews from Zillow on your website.

== Description ==

*Easy Zillow Reviews* helps real estate agents and lenders display their Zillow reviews using the official API.

View the [Demo](https://www.boltonstudios.com/easy-zillow-reviews/).

= Features (Free) =
* Display latest reviews for real estate agents and teams.
* Show reviews from multiple profiles.
* List and grid layouts.
* Widget and shortcode options.
* Gutenberg block (edit review layouts in the new WordPress editor!).
* Compliant with Zillow's Terms of Use and Branding Requirements.

= Premium Features =
* Lender reviews.
* Priority support.
* [Get the Premium Version](https://www.boltonstudios.com/easy-zillow-reviews/).

= Requirements =

The free plugin requires a [Bridge API Access Token](https://bridgedataoutput.com/zgdata).

The lender reviews require a [Zillow Mortgages Partner ID (ZPID)](https://www.zillow.com/mortgage/api/).

= Supporting Easy Zillow Reviews =

If you found this free plugin helpful, you can support the developer by upgrading to the Premium Version or making a donation:

* [Buy me a coffee](http://ko-fi.com/boltonstudios)

= Shortcode =

    [ez-zillow-reviews]
    [ez-zillow-lender-reviews]

= Optional Shortcode Parameters =

    [ez-zillow-reviews layout="grid" columns="2" count="4" screenname="jsmith"]

* layout..."list" or "grid".
* columns...A number between 1 and 6.
* count...A number between 1 and 10.
* screenname...The screenname that appears in your Zillow profile link.

== Installation ==

1. Upload "easy-zillow-reviews" to the /wp-content/plugins/ directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Add your Bridge Access Token and Zillow Screenname to the **Settings** page.
4. Adjust the Default Plugin Settings on the Settings page (optional).
4. Add an *Easy Zillow Reviews* widget or **[ez-zillow-reviews]** shortcode to your page or post.
5. Save Changes.

== Screenshots ==

1. Zillow Reviews block editor
2. Shortcode output for [ez-zillow-reviews layout="grid" columns="2" count="2" screenname="pstalker"]
3. Widget
4. Settings

== Changelog ==

= 1.5.3 =
* Date Updated: 2023-03-06
* Bug Fix: Fixed bug that caused reviews to appear out of sequence (should be newest first).

= 1.5.2 =
* Date Updated: 2023-03-05
* Implemented new method to count reviews.
* Updated @wordpress/scripts Gutenberg dependency package to v25.5.0.

= 1.5.1 =
* Date Updated: 2022-11-21
* Bug Fix: Fixed bug that raised warnings about an undefined array key after upgrading to v 1.5.0.

= 1.5.0 =
* Date Updated: 2022-11-19
* Zillow transitioned to Bridge Interative API access. Create a Bridge API access token to get reviews.
* New Feature: Added support for Bridge Interative API access tokens.
* Updated @wordpress/scripts Gutenberg dependency package to v24.6.0.
* Cleaned up Settings page by rewriting labels and hiding deprecated fields.

= 1.4.4 =
* Date Updated: 2022-11-02
* Bug Fix: Fixed bug preventing default numeric values from loading in some Settings Sidebar fields.
* Bug Fix: Fixed CSS layout bug causing elements to wrap to a new line in the Block editor content area.
* Updated @wordpress/scripts Gutenberg dependency package to v24.4.0 and recompiled blocks code.
* Changed the name of the premium plugin version to "Premium Version".

= 1.4.3 =
* Date Updated: 2022-10-28
* Rolling back to Freemius SDK version 2.4.3.

= 1.4.2 =
* Date Updated: 2022-10-26
* Updated Freemius SDK to version 2.5.0.
* Bug Fix: Fixed bug that caused "invalidInput" error message for Mortgage Lender names containing ampersands.

= 1.4.1 =
* Date Updated: 2022-03-18
* Updated Freemius SDK to version 2.4.3.

= 1.4.0 =
* Date Updated: 2022-02-03
* New Feature: Added a "screenname" attribute to the shortcode.

= 1.3.1 =
* Date Updated: 2021-11-09
* Removed unused .po language files from Freemius SDK.
* Added minor style changes to plugin CSS file.
* Changed the Profile Card text "sales in the last 12 months" to "recent home sales".
* Changed the expected type of the Easy_Zillow_Reviews_Data object $reviews property from "array" to "object".

= 1.3.0 =
* Date Updated: 2021-09-09
* Updated Freemius SDK to version 2.4.2.

= 1.2.3 =
* Date Updated: 2020-05-29
* Bug Fix: Fixed bug that caused Reviews Summary badge to appear in-line with reviews instead of below them in certain grid layouts.
* Hardcoded blockquote line-height in public CSS file.
* Modified labels for a few Dashboard settings.

= 1.2.2 =
* Date Updated: 2020-05-28
* Minor style changes to plugin CSS file.

= 1.2.1 =
* Date Updated: 2020-05-28
* New Feature: Display a Zillow profile card with average star rating.
* Bug Fix: Added fallback to cURL for PHP configurations that have allow_url_fopen disabled.
* Bug Fix: Removed erroneous reference to a second Gutenberg block.
* Bug Fix: Removed two instances of Undefined Index errors in widgets and lender functions.
* Bug Fix: Improved sentence construction for loan descriptions in Premium version.
* Updated Freemius SDK to version 2.3.2.

= 1.2.0 =
* Date Updated: 2020-04-28
* New Feature: Gutenberg Block
* Moved the Settings page menu item to the top-level of the Dashboard Menu

= 1.1.7 =
* Date Updated: 2020-04-21
* Bug Fix: Fixed error that prevented review count and profile link from displaying in Mortgages Partner Edition.
* Bug Fix: Fixed error that caused "Settings" plugin action link to appear under other plugin names in the Plugin menu.
* Added attribution for function copied from FooGallery by Brad Vincent and Contributors.

= 1.1.6 =
* Date Updated: 2020-04-19
* Bug Fix: Fixed error that prevented Mortgages Partner Edition from activating while Free Edition was active.
* Bug Fix: Fixed error that prevented reviews for Lending Companies from displaying.

= 1.1.5 =
* Date Updated: 2020-04-18
* Bug Fix: Resolved PHP notices when certain elements are set to hidden and WP_DEBUG is true.
* Bug Fix: Restored feature to hide Reviewer Summary.

= 1.1.4 =
* Date Updated: 2020-04-18
* *NOTE* Due to changes in v1.1.x, please check your widgets to confirm that Easy Zillow Reviews is still active.
* Bug Fix: Changed plugin widget name back to 'ezrwp_widget' (free version) 
* Bug Fix: Restored options to style and hide reviews output.
* Bug Fix: Resolved miscellaneous notices when WP_DEBUG is true.

= 1.1.3 =
* Date Updated: 2020-04-13
* Added more fields to Upgrader class, which assists with backwards compatibility from v1.0 to v1.1.

= 1.1.2 =
* Date Updated: 2020-04-13
* Bug Fix: Migrated backwards compatibility patch to main plugin file to better support updates, in addition to activations.

= 1.1.1 =
* Date Updated: 2020-04-13
* Bug Fix: Added backwards compatibility for new database option names.

= 1.1.0 =
* Date Updated: 2020-04-13
* New Feature: Option to upgrade for access to Lender Reviews.
* New Feature: Tabbed Settings interface improves organization of plugin options.
* Bug Fix: Added fix to prevent errors generated by dashes and spaces in screennames.

**Premium Version**
* New Feature: Lender Reviews
* New Feature: Individual Loan Officer Reviews
* New Feature: Company Profile Reviews

= 1.0.3 =
* Date Updated: 2019-01-03
* Bug Fix: Replaced dashicons with image sprite.

= 1.0.2 =
* Date Updated: 2018-12-31
* Bug Fix: Fixed typo in admin Settings page.

= 1.0.1 =
* Date Updated: 2018-12-31
* New Feature: New Settings Options: Hide Zillow Logo and "View All" link.
* New Feature: Added "Settings" plugin action link.
* Bug Fixes: Resolved miscellaneous notices when WP_DEBUG is true.
* Added support for PHP 5.4, 5.5, 5.6 and 7.2.

= 1.0.0 =
* Date Released: 2018-12-30
* Initial Release
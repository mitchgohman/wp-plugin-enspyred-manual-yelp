=== Enspyred Manual Yelp ===
Contributors: enspyred
Tags: yelp, reviews, testimonials, ratings
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 8.0
Stable tag: 1.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display manually curated Yelp reviews on your WordPress site via shortcodes.

== Description ==

Enspyred Manual Yelp allows you to display Yelp reviews on your WordPress site without API rate limits. Simply copy review data from Yelp and store it in galleries, then display reviews anywhere using shortcodes.

**Key Features:**

* Create multiple review galleries
* Filter reviews by branch/location
* Limit number of reviews displayed
* Beautiful, responsive card-style display
* Direct links back to original Yelp reviews
* React-based frontend for fast performance
* No Yelp API required (no rate limits)

== Installation ==

= IMPORTANT: Download the Correct ZIP File =

**DO NOT** download the repository ZIP from the main branch! The repository includes development files (node_modules, source code, etc.) totaling ~129MB and will not work properly when installed in WordPress.

**ALWAYS** download the official release ZIP from GitHub Releases. These are clean, production-ready distributions (~2-3MB) that contain only the necessary files.

= Installation Steps =

1. Download the latest **release ZIP** from GitHub: https://github.com/enspyred/enspyred-manual-yelp/releases
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded release ZIP file and click "Install Now"
5. Activate the plugin
6. Configure your Yelp reviews via the plugin settings

== Usage ==

**Basic shortcode:**
`[enspyred_yelp gallery="homepage-reviews"]`

**Filter by branch:**
`[enspyred_yelp gallery="all-reviews" branch="Baldwin Park"]`

**Limit number of reviews:**
`[enspyred_yelp gallery="featured-reviews" limit="5"]`

**Combine filters:**
`[enspyred_yelp gallery="all-reviews" branch="Riverside" limit="3"]`

== Frequently Asked Questions ==

= How do I get review data from Yelp? =

You'll need to manually copy review information from your Yelp business page and format it as JSON. See the example format in the gallery editor.

= Can I use multiple galleries on one page? =

Yes! You can use as many shortcodes as you want on a single page, each pointing to different galleries.

= Does this use the Yelp API? =

No, this plugin stores review data locally in your WordPress database, so there are no API rate limits or costs.

= Are images hosted on Yelp? =

Yes, reviewer photos and other images are still loaded from Yelp's servers via direct URLs.

== Changelog ==

= 1.0.0 =
* Initial release
* Gallery management system
* React-based review display
* Shortcode support with filtering options
* Responsive design

== Download & Updates ==

Download the latest version from GitHub: https://github.com/enspyred/wp-plugin-enspyred-manual-yelp/releases

The plugin includes automatic update notifications - you'll be notified in your WordPress admin when new versions are available.

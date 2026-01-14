<?php
/*
Plugin Name: Enspyred Manual Yelp
Description: Display Yelp review embeds via shortcodes with React UI.
Version: 2.0.1
Author: Enspyred
Author URI: https://enspyred.com
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: enspyred-manual-yelp
*/

// Plugin Update Checker
require plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5p6\PucFactory;

$enspyredManualYelpUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/enspyred/wp-plugin-enspyred-manual-yelp',
	__FILE__,
	'enspyred-manual-yelp'
);
$enspyredManualYelpUpdateChecker->getVcsApi()->enableReleaseAssets();

// general
require_once plugin_dir_path(__FILE__) . 'inc/helpers.php';
require_once plugin_dir_path(__FILE__) . 'inc/router.php';
require_once plugin_dir_path(__FILE__) . 'inc/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'inc/menus.php';

// tools
require_once plugin_dir_path(__FILE__) . 'inc/react.php';
require_once plugin_dir_path(__FILE__) . 'inc/api.php';

// pages
require_once plugin_dir_path(__FILE__) . 'inc/pages/galleries.php';
require_once plugin_dir_path(__FILE__) . 'inc/pages/edit-gallery.php';
require_once plugin_dir_path(__FILE__) . 'inc/pages/settings.php';

// start her up
require_once plugin_dir_path(__FILE__) . 'inc/init.php';

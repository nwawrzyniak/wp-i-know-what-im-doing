<?php
/**
 * Plugin Name: WP I Know What I'm Doing
 * Plugin URI: https://github.com/nwawrzyniak/wp-i-know-what-i-am-doing
 * Description: Adds a direct link to the All Options page in the WordPress admin menu.
 * Version: 1.2.0
 * Author: nwawrzyniak
 * Author URI: https://nwawsoft.com
 * License: GPL-2.0+
 * Text Domain: wp-i-know-what-i-am-doing
 * Domain Path: /languages
 */

// Define plugin version constant
define('WPIKWIAD_VERSION', '1.2.0');

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'all-options-in-menu.php';
require_once plugin_dir_path(__FILE__) . 'option-deletion.php';

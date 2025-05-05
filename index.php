<?php
/**
 * Plugin Name: WP I Know What I'm Doing
 * Plugin URI: https://github.com/nwawrzyniak/wp-i-know-what-im-doing
 * Description: Adds a direct link to the All Options page in the WordPress admin menu.
 * Version: 1.0.0
 * Author: nwawrzyniak
 * Author URI: https://nwawsoft.com
 * License: GPL-2.0+
 * Text Domain: wp-i-know-what-im-doing
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Add link to All Options page in the Settings submenu
 */
function wpikwid_add_all_options_link() {
    add_options_page(
        'All Options',      // Page title
        'All Options',      // Menu title
        'manage_options',   // Capability required
        'options.php',      // Menu slug (URL)
        ''                  // Function (blank as we're linking directly to options.php)
    );
}
add_action('admin_menu', 'wpikwid_add_all_options_link', 99);

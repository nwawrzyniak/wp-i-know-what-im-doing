<?php
/**
 * Handles the All Options menu registration
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Add link to All Options page in the Settings submenu
 */
function wpikwiad_add_all_options_link() {
    add_options_page(
        __('All Options', 'wp-i-know-what-i-am-doing'),      // Page title
        __('All Options', 'wp-i-know-what-i-am-doing'),      // Menu title
        'manage_options',   // Capability required
        'options.php',      // Menu slug (URL)
        ''                  // Function (blank as we're linking directly to options.php)
    );
}
add_action('admin_menu', 'wpikwiad_add_all_options_link', 99);

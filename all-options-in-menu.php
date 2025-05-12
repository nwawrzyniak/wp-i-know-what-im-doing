<?php
/**
 * Handles the All Options menu registration and related functionality
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

/**
 * Enqueue necessary scripts and styles for the options page
 */
function wpikwiad_enqueue_scripts($hook) {
    // Check if we're on the options.php page, either through menu or direct access
    $current_screen = get_current_screen();
    if (!$current_screen || ($current_screen->id !== 'options' && !in_array($hook, array('options.php', 'settings_page_options')))) {
        return;
    }

    // Debug output to help troubleshoot
    error_log('WP I Know What I\'m Doing: Loading scripts on page ' . $hook);
    error_log('Current screen ID: ' . ($current_screen ? $current_screen->id : 'none'));

    // Get the plugin directory URL - using the plugin's main file to ensure correct path
    $plugin_url = plugins_url('', dirname(__FILE__) . '/index.php');
    error_log('Plugin URL: ' . $plugin_url); // Debug log the URL

    wp_enqueue_script(
        'wpikwiad-options',
        $plugin_url . '/js/options.js',
        array('jquery'),
        WPIKWIAD_VERSION,
        true
    );

    wp_localize_script('wpikwiad-options', 'wpikwiadData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wpikwiad_delete_option'),
        'confirmMessage' => __('Are you sure you want to delete this option? This action cannot be undone.', 'wp-i-know-what-i-am-doing')
    ));

    wp_enqueue_style(
        'wpikwiad-options',
        $plugin_url . '/css/options.css',
        array(),
        WPIKWIAD_VERSION
    );
}
add_action('admin_enqueue_scripts', 'wpikwiad_enqueue_scripts'); 

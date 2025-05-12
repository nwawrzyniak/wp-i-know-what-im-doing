<?php
/**
 * Plugin Name: WP I Know What I'm Doing
 * Plugin URI: https://github.com/nwawrzyniak/wp-i-know-what-i-am-doing
 * Description: Adds a direct link to the All Options page in the WordPress admin menu.
 * Version: 1.1.0
 * Author: nwawrzyniak
 * Author URI: https://nwawsoft.com
 * License: GPL-2.0+
 * Text Domain: wp-i-know-what-i-am-doing
 * Domain Path: /languages
 */

// Define plugin version constant
define('WPIKWIAD_VERSION', '1.1.0');

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Add link to All Options page in the Settings submenu
 */
function wpikwiad_add_all_options_link() {
    add_options_page(
        'All Options',      // Page title
        'All Options',      // Menu title
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

    wp_enqueue_script(
        'wpikwiad-options',
        plugins_url('js/options.js', __FILE__),
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
        plugins_url('css/options.css', __FILE__),
        array(),
        WPIKWIAD_VERSION
    );
}
add_action('admin_enqueue_scripts', 'wpikwiad_enqueue_scripts');

/**
 * Handle AJAX request to delete an option
 */
function wpikwiad_delete_option() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wpikwiad_delete_option')) {
        wp_send_json_error('Invalid nonce');
    }

    // Verify user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    // Get and sanitize option name
    $option_name = isset($_POST['option_name']) ? sanitize_text_field($_POST['option_name']) : '';
    if (empty($option_name)) {
        wp_send_json_error('Invalid option name');
    }

    // Delete the option
    $result = delete_option($option_name);
    
    if ($result) {
        wp_send_json_success('Option deleted successfully');
    } else {
        wp_send_json_error('Failed to delete option');
    }
}
add_action('wp_ajax_wpikwiad_delete_option', 'wpikwiad_delete_option');

/**
 * Add delete buttons to the options table
 */
function wpikwiad_add_delete_buttons() {
    // Debug output to help troubleshoot
    error_log('WP I Know What I\'m Doing: Attempting to add delete buttons');
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        console.log('WP I Know What I\'m Doing: Script loaded, adding delete buttons');
        // Add delete buttons to each option row
        $('.form-table tr').each(function() {
            var $row = $(this);
            var $th = $row.find('th');
            var $td = $row.find('td');
            if ($th.length && $td.length) {
                var optionName = $th.find('label').attr('for');
                if (optionName) {
                    console.log('Adding delete button for option:', optionName);
                    var $deleteButton = $('<button>')
                        .addClass('button wpikwiad-delete-option')
                        .text('<?php _e('Delete', 'wp-i-know-what-i-am-doing'); ?>')
                        .attr('data-option', optionName);
                    // Add the button after the input field in the td
                    $td.append(' ').append($deleteButton);
                }
            }
        });
    });
    </script>
    <?php
}
// Only hook into admin_footer-options.php to avoid duplicates
add_action('admin_footer-options.php', 'wpikwiad_add_delete_buttons');

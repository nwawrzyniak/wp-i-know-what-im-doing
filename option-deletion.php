<?php
/**
 * Handles the option deletion functionality including AJAX handlers and UI elements
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Handle AJAX request to delete an option
 */
function wpikwiad_delete_option() {
    // Debug logging
    error_log('WP I Know What I\'m Doing: Delete option request received');
    
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wpikwiad_delete_option')) {
        error_log('WP I Know What I\'m Doing: Nonce verification failed');
        wp_send_json_error('Invalid nonce');
    }

    // Verify user capabilities
    if (!current_user_can('manage_options')) {
        error_log('WP I Know What I\'m Doing: User capability check failed');
        wp_send_json_error('Insufficient permissions');
    }

    // Get and sanitize option name
    $option_name = isset($_POST['option_name']) ? sanitize_text_field($_POST['option_name']) : '';
    if (empty($option_name)) {
        error_log('WP I Know What I\'m Doing: Empty option name received');
        wp_send_json_error('Invalid option name');
    }

    error_log('WP I Know What I\'m Doing: Attempting to delete option: ' . $option_name);
    
    // Check if option exists before deletion
    $option_exists = get_option($option_name) !== false;
    error_log('WP I Know What I\'m Doing: Option exists before deletion: ' . ($option_exists ? 'yes' : 'no'));

    // Delete the option
    $result = delete_option($option_name);
    
    // Verify deletion
    $option_still_exists = get_option($option_name) !== false;
    error_log('WP I Know What I\'m Doing: Delete result: ' . ($result ? 'success' : 'failed'));
    error_log('WP I Know What I\'m Doing: Option still exists after deletion: ' . ($option_still_exists ? 'yes' : 'no'));
    
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
    <!-- Delete Confirmation Modal -->
    <div id="wpikwiad-delete-modal" class="wpikwiad-modal" style="display: none;">
        <div class="wpikwiad-modal-content">
            <div class="wpikwiad-modal-header">
                <h2><?php _e('Confirm Deletion', 'wp-i-know-what-i-am-doing'); ?></h2>
                <span class="wpikwiad-modal-close">&times;</span>
            </div>
            <div class="wpikwiad-modal-body">
                <p><?php _e('Are you sure you want to delete this option?', 'wp-i-know-what-i-am-doing'); ?></p>
                <p class="wpikwiad-modal-option-name"></p>
                <p class="wpikwiad-modal-warning"><?php _e('This action cannot be undone.', 'wp-i-know-what-i-am-doing'); ?></p>
            </div>
            <div class="wpikwiad-modal-footer">
                <button class="button wpikwiad-modal-cancel"><?php _e('Cancel', 'wp-i-know-what-i-am-doing'); ?></button>
                <button class="button button-primary wpikwiad-modal-confirm"><?php _e('Delete', 'wp-i-know-what-i-am-doing'); ?></button>
            </div>
        </div>
    </div>

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

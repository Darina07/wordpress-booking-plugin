<?php
namespace calendar\Admin;

class Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles_and_scripts'));

        // Register the AJAX action hooks
        add_action('wp_ajax_get_bookings_data', array($this, 'get_bookings_data_callback'));
        add_action('wp_ajax_nopriv_get_bookings_data', array($this, 'get_bookings_data_callback'));

        add_action('wp_ajax_update_booking_data', array($this, 'update_booking_data_callback'));
        add_action('wp_ajax_nopriv_update_booking_data', array($this, 'update_booking_data_callback'));

    }

    public function add_admin_menu() {
        add_submenu_page(
            'options-general.php', // Parent menu slug (Options)
            'Calendar Settings',    // Page title
            'Calendar Settings',    // Menu title
            'manage_options',       // Capability required to access the page
            'calendar-settings',    // Menu slug
            array($this, 'display_admin_page') // Callback function to display the page
        );
    }

    public function enqueue_admin_styles_and_scripts() {
        // Enqueue the admin-style.css file
        wp_enqueue_style('admin-style', plugin_dir_url(__FILE__) . '../assets/css/admin-style.css');

        // Enqueue the admin-script.js file
        wp_enqueue_script('admin-script', plugin_dir_url(__FILE__) . '../assets/js/admin-script.js', array('jquery'), null, true);
    }

    public function display_admin_page() {
        include_once(plugin_dir_path(__FILE__) . 'admin-calendar.php');
    }

    public function get_bookings_data_callback() {
        global $wpdb;

        $bookings_data = $wpdb->get_results("SELECT booking_date, time_slot, name FROM {$wpdb->prefix}bookings", ARRAY_A);

        wp_send_json($bookings_data);
    }

    public function update_booking_data_callback() {
        global $wpdb;
    
        // Get data from the AJAX request
        $booking_date = sanitize_text_field($_POST['booking_date']);
        $time_slot = sanitize_text_field($_POST['time_slot']);
        $name = sanitize_text_field($_POST['name']);
    
        // Trim the name and check if it's empty or whitespace
        $trimmed_name = trim($name);
        if (empty($trimmed_name)) {
            // If name is empty, delete the corresponding record
            $wpdb->delete(
                $wpdb->prefix . 'bookings',
                array('booking_date' => $booking_date, 'time_slot' => $time_slot)
            );
    
            // Send a JSON response
            wp_send_json(array('success' => true, 'message' => 'Record deleted successfully'));
        } else {
            // Check if the record already exists
            $existing_record = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}bookings WHERE booking_date = %s AND time_slot = %s", $booking_date, $time_slot),
                ARRAY_A
            );
    
            if ($existing_record) {
                // Update the existing record
                $wpdb->update(
                    $wpdb->prefix . 'bookings',
                    array('name' => $trimmed_name),
                    array('booking_date' => $booking_date, 'time_slot' => $time_slot)
                );
            } else {
                // Insert a new record
                $wpdb->insert(
                    $wpdb->prefix . 'bookings',
                    array('booking_date' => $booking_date, 'time_slot' => $time_slot, 'name' => $trimmed_name)
                );
            }
    
            // Send a JSON response
            wp_send_json(array('success' => true, 'message' => 'Record updated successfully'));
        }
    }
    
    
    
}

new Admin();

<?php

/*
Plugin Name: Calendar Booking Appointment
Description: You can book your appointment with us here
Version: 1.0
Author: Darina Zvetanova
*/

if (!defined('ABSPATH')) : exit();
endif;

require_once 'vendor/autoload.php';

use calendar\Frontend\Frontend;
use calendar\Frontend\BookedHoursHandler;
use calendar\Admin\Admin;

class Calendar {
    function __construct() {
        $this->init_modules();
        register_activation_hook(__FILE__, array($this, 'activate'));
    }

    public function init_modules() {
        $frontend = new Frontend();
        add_action('wp', array($frontend, 'display_template'));
        $booked_hours_handler = new BookedHoursHandler();
        $admin = new Admin();
    }

    public function activate() {
        $this->create_calendar_page_if_needed();
    }

    public function create_calendar_page_if_needed() {
        $page_exists = get_page_by_title('Calendar Page');

        if (!$page_exists) {
            $post_data = array(
                'post_title'    => 'Calendar Page',
                'post_content'  => 'Your page content goes here.',
                'post_status'   => 'publish',
                'post_type'     => 'page',
            );

            $post_id = wp_insert_post($post_data);

            update_post_meta($post_id, '_wp_page_template', 'calendar.php');

            echo 'Calendar Page created successfully!';
        } else {
            echo 'Calendar Page already exists!';
        }
    }
}

new Calendar();

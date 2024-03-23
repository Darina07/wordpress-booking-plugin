<?php
namespace calendar\Frontend;

class Frontend {
    public function display_template() {
        $booked_hours_handler = new BookedHoursHandler();
        
        // Enqueue CSS and JavaScript
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles_and_scripts'));


        //Shortcode to display calendar
        add_shortcode('booking_calendar', array($this, 'darina_booking_calendar_cb'));

    }

    public function enqueue_styles_and_scripts() {
        // Enqueue  CSS file from the assets/css folder
        wp_enqueue_style('calendar-plugin-style', plugin_dir_url(__FILE__) . '../assets/css/style.css', array(), rand(1,9999));

        // Enqueue  JavaScript file from the assets/js folder
        wp_register_script('calendar-plugin-script', plugin_dir_url(__FILE__) . '../assets/js/script.js', array('jquery'), rand(1,9999), true);

        wp_localize_script('calendar-plugin-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

        // Enqueue the script
        wp_enqueue_script('calendar-plugin-script');
    }

    private function load_plugin_header() {
        include plugin_dir_path(__FILE__) . '../includes/header.php';
    }

    private function get_template_path() {
        $use_custom_template = true;

        if ($use_custom_template) {
            return plugin_dir_path(__FILE__) . 'calendar.php';
        }

        return false;
    }

    
    public function darina_booking_calendar_cb(){

    $booked_hours_handler = new BookedHoursHandler();
        ob_start();

   
        $step = isset($_REQUEST['step']) ? $_REQUEST['step'] : 1;
        switch ($step) {
        case 1:
            $template = 'calendar-template.html';
            break;
        case 2:
            $template = 'calendar-send-form.php';
            break;
        case 3:
            $template = 'calendar-confirmation.php';
            break;
        default:
            $template = 'calendar-template.html';
        }
        
        include(plugin_dir_path(__FILE__) . '../templates/'.$template);
        $output_string = ob_get_contents();
        ob_end_clean();
        return $output_string;
    }
}


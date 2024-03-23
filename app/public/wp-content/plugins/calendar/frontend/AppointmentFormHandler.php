<?php 
namespace calendar\Frontend;

class AppointmentFormHandler {
    
    public function __construct() {
        add_action('template_redirect', array($this, 'handleFormSubmission'));
    }

    public function handleFormSubmission() {
        // Process the form data when the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sanitize and get form data
            $selectedDate = sanitize_text_field($_GET['date']);
            $selectedTime = sanitize_text_field($_GET['time']);
            $name = sanitize_text_field($_POST['name']);
            $email = sanitize_email($_POST['email']);
            $phone = sanitize_text_field($_POST['phone']);
            $checkbox1 = isset($_POST['checkbox1']) ? 'Yes' : 'No';
            $checkbox2 = isset($_POST['checkbox2']) ? 'Yes' : 'No';

            // Insert data into wp_bookings table
            global $wpdb;
            $table_name = $wpdb->prefix . 'bookings';

            $data_to_insert = array(
                'name'          => $name,
                'email'         => $email,
                'phone'         => $phone,
                'booking_date'  => $selectedDate,
                'time_slot'     => $selectedTime,
                'day_of_week'   => date('l', strtotime($selectedDate)),
            );

            $wpdb->insert($table_name, $data_to_insert);

            // Load HTML template
            $template_path = plugin_dir_path(__FILE__) . '../templates/email/new-appointment.html';
            $email_content = file_get_contents($template_path);

            // Replace placeholders in the template
            $placeholders = array(
                '{date}' => $selectedDate,
                '{time}' => $selectedTime,
                '{name}' => $name,
                '{email}' => $email,
                '{phone}' => $phone,
                '{checkbox1}' => $checkbox1,
                '{checkbox2}' => $checkbox2,
            );
            $email_content = strtr($email_content, $placeholders);

            // Admin email address
            $admin_email = get_option('admin_email');

            // Email headers
            $headers = array('Content-Type: text/html; charset=UTF-8');

            // Send the email
            wp_mail($admin_email, 'New Appointment Booking', $email_content, $headers);

        }
    }
}

<?php
/* Template Name: Calendar Booking Form */

use calendar\Frontend\AppointmentFormHandler;

$selectedDate = sanitize_text_field($_GET['date']);
$selectedTime = sanitize_text_field($_GET['time']);

$formHandler = new AppointmentFormHandler();

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formHandler->handleFormSubmission();
}

include(plugin_dir_path(__FILE__) . '../templates/calendar-send-form.html');


?>

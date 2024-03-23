<?php
/* Template Name: Calendar Confirmation */

// Get data from the URL parameters
$selectedDate = sanitize_text_field($_GET['date']);
$selectedTime = sanitize_text_field($_GET['time']);



include(plugin_dir_path(__FILE__) . '../templates/calendar-confirmation.html');


?>
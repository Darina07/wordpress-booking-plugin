<?php 
namespace calendar\Frontend;

class BookedHoursHandler {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'bookings';

        add_action('wp_ajax_nopriv_get_booked_hours_callback', array($this, 'get_booked_hours_callback'));
        add_action('wp_ajax_get_booked_hours_callback', array($this, 'get_booked_hours_callback'));

        add_action('wp_ajax_nopriv_set_booking_hours_callback', array($this, 'set_booking_hours_callback'));
        add_action('wp_ajax_set_booking_hours_callback', array($this, 'set_booking_hours_callback'));
        
    }


    public function set_booking_hours_callback() {
        // Check nonce
        // $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

        // if (!wp_verify_nonce($nonce, 'get-bookings-hours-nonce')) {
        //     wp_send_json_error(array('message' => 'Invalid nonce.'));
        // }
        $this->wpdb->insert($this->table_name, array(
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_text_field($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'booking_date' => $_POST['date'],
            'time_slot' => $_POST['time'],
        ));

        if($this->wpdb->insert_id > 0){
            $body = "<table>";
            foreach($_POST as $label=>$data){
                if($label == 'action')continue;
                $body .= "<tr><td>".ucfirst($label)."</td><td>".$data."</td></tr>";
            }
            $body .= "</table>";

            wp_mail(get_bloginfo('admin_email').',darina.franktrax@gmail.com','New Booking', $body);
            wp_send_json_success(array('booked_hours' => $_POST));
            wp_die();
        }else{
            wp_send_json_error(array('booked_hours' => $_POST));
            wp_die();
        }
    }

    public function get_booked_hours_callback() {
        $date = sanitize_text_field($_POST['date']);
        $newDate = date("Y-m-d", strtotime($date));
        $booked_hours = $this->get_booked_hours($newDate);

        wp_send_json_success(array('booked_hours' => $booked_hours));
        wp_die();
    }

    private function get_booked_hours($date) {
        $results = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT time_slot FROM $this->table_name WHERE booking_date = %s",
                $date
            )
        );

        
        $booked_hours = array();

        foreach ($results as $result) {
            $time = strtotime($result->time_slot);
            $booked_hours[] = date('G:i',$time);
        }

        return $booked_hours;
    }
}
?>
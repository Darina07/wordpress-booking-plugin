<div class="container">
    <h1>Booking Confirmation</h1>
    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium</p>
    <form id="bookingForm" method="post">
        <div class="row">
            <div class="col">
                <?php echo isset($_REQUEST['date']) ? date("l jS \of F Y", strtotime($_REQUEST['date'])) : 'N/A'; ?>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <?php echo isset($_REQUEST['time']) ? date("h:i A", strtotime($_REQUEST['time'])) : 'N/A'; ?>
            </div>
        </div>

        <p><b>Confirmed. You booked an appointment </b></p>
    </form>
    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium</p>

</div>
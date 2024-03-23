<div class="container">
    <h1>Lorem Ipsum</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum</p>
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

        <p><b>Your information for contact: </b></p>

        <!-- Form fields (email, name, phone) -->
        <input type="text" id="name" name="name" placeholder="Name" required>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="tel" id="phone" name="phone" placeholder="Phone number" required>
        <input type="hidden" id="date">
        <input type="hidden" id="time">
        <!-- Checkboxes -->
        <div class="checkbox-group">
            <div class="d-flex mb-1">
                <input type="checkbox" class="mr-1" id="checkbox1" name="checkbox1" required>
                <label for="checkbox1" class="mb-0">Checkbox 1</label>
            </div>

            <div class="d-flex mb-1">
                <input type="checkbox" class="mr-1" id="checkbox2" name="checkbox2">
                <label for="checkbox2" class="mb-0">Checkbox 2</label>
            </div>
        </div>

        <!-- Save appointment button -->
        <div id="btn-submit">
            <button type="submit" id="saveAppointmentButton" class="btn-success">Book an Appointment</button>
        </div>
    </form>
</div>

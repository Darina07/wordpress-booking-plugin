document.addEventListener("DOMContentLoaded", function () {
    var currentDate = new Date();

    function updateCalendar() {
        var startOfWeek = currentDate.getDate() - currentDate.getDay() + (currentDate.getDay() === 0 ? -6 : 1);
        var weekStartDate = new Date(currentDate.setDate(startOfWeek));
    
        document.querySelector(".week-mo-year").textContent = `${weekStartDate.getDate()}-${weekStartDate.getDate() + 6}.${weekStartDate.getMonth() + 1}.${weekStartDate.getFullYear()}`;
    
        var table = "<table><tr><th></th>";
    
        for (var k = 0; k < 7; k++) {
            var currentDay = new Date(weekStartDate);
            currentDay.setDate(weekStartDate.getDate() + k);
    
            var day = currentDay.getDate();
            var month = currentDay.getMonth() + 1;
    
            var isToday = currentDay.toDateString() === new Date().toDateString();
    
            table += `<th${isToday ? ' class="today"' : ''}>${getDayName(k)} ${day}.${month}</th>`;
        }
    
        table += "</tr>";
    
        for (var i = 9; i <= 17; i++) {
            for (var j = 0; j < 2; j++) {
                var time = i + (j === 0 ? ':00' : ':30');
                table += `<tr><td>${time}</td>`;
    
                for (var k = 0; k < 7; k++) {
                    var currentDay = new Date(weekStartDate);
                    currentDay.setDate(weekStartDate.getDate() + k);
    
                    var isToday = currentDay.toDateString() === new Date().toDateString();
    
                    table += `<td id="${getFormattedId(currentDay, i, j)}" ${isToday ? ' class="today"' : ''}></td>`;
                }
    
                table += `</tr>`;
            }
        }
    
        table += "</table>";
    
        document.querySelector(".calendar").innerHTML = table;
    }
    

    // Helper function to get day name based on index
    function getDayName(index) {
        var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        return days[index];
    }

    // Helper function to format the ID
    function getFormattedId(date, hour, halfHour) {
        var formattedDate = `${date.getFullYear()}-${padZero(date.getMonth() + 1)}-${padZero(date.getDate())}`;
        var formattedTime = `${padZero(hour)}:${halfHour === 0 ? '00' : '30'}:00`;

        return `${formattedDate}-${formattedTime}`;
    }

    // Helper function to pad zero for single-digit values
    function padZero(value) {
        return value < 10 ? `0${value}` : value;
    }

    document.querySelector(".prev").addEventListener("click", function () {
        currentDate.setDate(currentDate.getDate() - 7);
        updateCalendar();
        fetchData();
    });

    document.querySelector(".next").addEventListener("click", function () {
        currentDate.setDate(currentDate.getDate() + 7);
        updateCalendar();
        fetchData();
    });

    updateCalendar();
    fetchData();

    // Function to fetch data with Ajax
    function fetchData() {
        fetch(ajaxurl + '?action=get_bookings_data')
            .then(response => response.json())
            .then(data => {
                // Loop through the data and update the corresponding td innerHTML
                data.forEach(entry => {
                    var key = `${entry.booking_date}-${entry.time_slot}`;
                    var tdElement = document.getElementById(key);

                    if (tdElement) {
                        tdElement.innerHTML = entry.name;
                        tdElement.classList.add('grey-background');
                    }
                });

                // Add click event listener to all td elements
                document.querySelectorAll('.calendar td').forEach(td => {
                    td.addEventListener('click', function () {
                        var currentValue = this.innerHTML;
                        var newValue = prompt('Enter new value:', currentValue);

                        if (newValue !== null) {
                            this.innerHTML = newValue;
                            var key = this.id;
                            updateBackend(key, newValue);
                        }
                    });
                });

            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // Function to update data in the backend using Ajax
    function updateBackend(key, newValue) {
        var booking_date = key.substring(0, 10);
        var time_slot = key.substring(key.length - 8);

        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update_booking_data&booking_date=${booking_date}&time_slot=${time_slot}&name=${newValue}`,
        })
        .then(response => response.json())
        .then(data => {
            console.log('Data updated successfully:', data);
        })
        .catch(error => console.error('Error updating data:', error));
    }


});

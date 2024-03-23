document.addEventListener('DOMContentLoaded', function () {
  const calendarBody = document.getElementById('calendar-body')
  const monthYearElement = document.querySelector('.month-year ul li.mo-year')
  const prevButton = document.querySelector('.month-year .prev')
  const nextButton = document.querySelector('.month-year .next')
  const continueButton = document.getElementById('continueButton')
  const bookingForm = document.getElementById('bookingForm')

  let currentYear
  let currentMonth
  let selectedTime
  let selectedDate
  const hoursContainer = document.getElementById('hours')
  const startTime = 9 // Start time in hours
  const endTime = 17.5 // End time in hours (17:30 = 17.5)

  function generateTimeSlots (bookedHours) {
    // Clear existing time slots
    hoursContainer.innerHTML = ''

    for (let hour = startTime; hour <= endTime; hour += 0.5) {
      const formattedTime = formatTime(hour)
      const timeBox = document.createElement('div')
      timeBox.classList.add(
        'col-2',
        'col-sm-2',
        'col-md-2',
        'col-lg-2',
        'col-xl-2',
        'time-box'
      )

      // Check if the current time slot is booked
      if (bookedHours.includes(formattedTime)) {
        timeBox.classList.add('booked-hour')
      }

      timeBox.innerHTML = `
                <div class="time-box" data-time="${formattedTime}">${formattedTime}</div>
            `

      // Add click event to each time slot
      timeBox.addEventListener('click', handleTimeBoxClick)

      hoursContainer.appendChild(timeBox)

      // Trigger reflow to apply styles immediately
      timeBox.offsetHeight

      // Apply styling for each booked hour
      if (bookedHours.includes(formattedTime)) {
        const timeBoxElement = hoursContainer.querySelector(
          `.time-box[data-time="${formattedTime}"]`
        )
        if (timeBoxElement) {
          timeBoxElement.classList.add('booked-hour')
        }
      }
    }

    function handleTimeBoxClick (event) {
      // Reset styling for all time slots
      const allTimeBoxes = document.querySelectorAll('.time-box')
      allTimeBoxes.forEach(box => {
        box.classList.remove('selected')
      })

      // Find the clicked time box
      const clickedTimeBox = event.currentTarget

      // Apply styling for the clicked time slot
      const clickedTimeboxNotBooked = clickedTimeBox.querySelector(
        '.time-box:not(.booked-hour)'
      )
      console.log(clickedTimeboxNotBooked)
      if (clickedTimeboxNotBooked) {
        clickedTimeboxNotBooked.classList.add('selected')
      }
      // Set the selectedTime variable
      selectedTime = clickedTimeBox.dataset.time

      // Show the "Continue" button
      document.getElementById('btn-continue').style.display = 'block'
    }
  }

  function formatTime (hour) {
    const formattedHour = Math.floor(hour)
    const minutes = (hour - formattedHour) * 60
    const formattedMinutes = minutes === 0 ? '00' : '30'
    return `${formattedHour}:${formattedMinutes}`
  }

  function generateCalendar (year, month) {
    const firstDay = new Date(year, month, 1).getDay()
    const daysInMonth = new Date(year, month + 1, 0).getDate()
    const today = new Date();
    today.setHours(0,0,0,0);
   
    monthYearElement.textContent =
      new Intl.DateTimeFormat('en-US', { month: 'short' })
        .formatToParts(new Date(year, month))[0]
        .value.toUpperCase() +
      ' ' +
      year

    let date = 1
    let day = 1

    for (let i = 0; i < 6; i++) {
      const row = document.createElement('tr')

      for (let j = 0; j < 7; j++) {
        if (i === 0 && j < firstDay) {
          const cell = document.createElement('td')
          row.appendChild(cell)
        } else if (day <= daysInMonth) {
          const cell = document.createElement('td')
          cell.textContent = date

          var current_month = today.getUTCMonth()+1;
          var cal_month = month+1;
          if((cell.textContent <= today.getUTCDate() && cal_month <= current_month )  || year < today.getUTCFullYear())//disable click for past dates
          {
              cell.className="cell-disabled";

          }else{
          // Add a click event to each date cell
          cell.addEventListener('click', function (event) {
            selectedDate = `${year}-${month + 1}-${cell.textContent}`
            const allDates = calendarBody.querySelectorAll('TD')
            allDates.forEach(date => {
                date.classList.remove('selected')
              })
        
              const target = event.target
              
              if (target.tagName === 'TD') {
                target.classList.toggle('selected')
                //selectedDate = `${year}-${month + 1}-${target.textContent}`
                document.getElementById(
                  'choose-hour'
                ).textContent = `Choose hour: ${selectedDate}`
              }
              

            
            console.log(cell.textContent);
            
            document.getElementById(
              'choose-hour'
            ).textContent = `Choose hour: `
            document.getElementById('hours').style.display = 'flex'

            var formData = new FormData()
            formData.append('action', 'get_booked_hours_callback')
            formData.append('date', selectedDate)

            fetch(my_ajax_object.ajax_url, {
              method: 'POST',

              body: formData
            })
              .then(response => response.json())
              .then(data => {
                // Apply styling for each booked hour
                const bookedHours = data.data.booked_hours

                // Call the function to generate time slots and pass the booked hours
                generateTimeSlots(bookedHours)

                applyBookedHoursStyling(bookedHours)
                /*if(bookedHours){
                            bookedHours.forEach(bookedHour => {
                                applyBookedHoursStyling(bookedHour);
                            });
                        }*/
              })
              .catch(error => {
                console.error('Error:', error)
              })
          })
        }

          date++
          day++
          row.appendChild(cell)
        }
      }

      calendarBody.appendChild(row)
    }

    function applyBookedHoursStyling (bookedHours) {
      // Reset styling
      const allTimeBoxes = document.querySelectorAll('.time-box')
      allTimeBoxes.forEach(timeBox => {
        timeBox.classList.remove('booked-hour')
      })

      // Apply styling for each booked hour
      bookedHours.forEach(bookedHour => {
        const timeBox = document.querySelector(
          `.time-box[data-time="${bookedHour}"]`
        )
        if (timeBox) {
          timeBox.classList.add('booked-hour')
        }
      })
    }
  }

  /*function applyBookedHourStyling(hour){
        console.log();
        document.querySelectorAll(`[data-time="${hour}"]`);
    }*/

  function updateCalendar () {
    calendarBody.innerHTML = '' // Clear the existing calendar
    generateCalendar(currentYear, currentMonth)
  }

  function showNextMonth () {
    currentMonth++
    if (currentMonth > 11) {
      currentMonth = 0
      currentYear++
    }
    updateCalendar()
  }

  function showPrevMonth () {
    currentMonth--
    if (currentMonth < 0) {
      currentMonth = 11
      currentYear--
    }
    updateCalendar()
  }

  if (calendarBody) {
    prevButton.addEventListener('click', showPrevMonth)
    nextButton.addEventListener('click', showNextMonth)

    // Initialize with the current month
    const currentDate = new Date()
    currentYear = currentDate.getFullYear()
    currentMonth = currentDate.getMonth()
    generateCalendar(currentYear, currentMonth)


    // Code for the "Continue" button
    continueButton.addEventListener('click', function () {
      const selectedTimeElement = document.querySelector('.time-box.selected')
      if (!selectedTimeElement) {
        alert('Please select a time.')
        return
      }

      // Retrieve the data-time attribute from the selected time box
      const selectedTime = selectedTimeElement.dataset.time


      const newPageURL = `?date=${selectedDate}&time=${selectedTime}&step=2`
      window.location.href = newPageURL
    })
  }

  if (bookingForm) {
    bookingForm.addEventListener('submit', function (evt) {
      evt.preventDefault()

      const nameBox = document.querySelector('#name')
      if (!nameBox) {
        alert('Please enter your name.')
        return
      }

      const emailBox = document.querySelector('#email')
      if (!emailBox) {
        alert('Please enter your email.')
        return
      }

      const phoneBox = document.querySelector('#phone')
      if (!phoneBox) {
        alert('Please enter your phone.')
        return
      }


      const formData = new FormData(bookingForm)
      formData.append('action', 'set_booking_hours_callback')
      formData.append('date', getUrlParameter('date'))
      formData.append('time', getUrlParameter('time'))

      fetch(my_ajax_object.ajax_url, {
        method: 'POST',

        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const newPageURL = `?date=${getUrlParameter(
              'date'
            )}&time=${getUrlParameter('time')}&step=3`
            window.location.href = newPageURL
          } else {
            alert('Booking failed! Please retry again.')
          }
        })
        .catch(error => {
          console.error('Error:', error)
        })

      return
    })
  }
})

function getUrlParameter (sParam) {
  var sPageURL = window.location.search.substring(1),
    sURLVariables = sPageURL.split('&'),
    sParameterName,
    i

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split('=')

    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined
        ? true
        : decodeURIComponent(sParameterName[1])
    }
  }
  return false
}

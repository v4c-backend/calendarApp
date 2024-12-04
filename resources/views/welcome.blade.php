<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Event Calendar</title>

    <!-- Include FullCalendar Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet" />

    <!-- Include jQuery (necessary for FullCalendar and AJAX integration) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include FullCalendar Script -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>

    <style>
        /* Add some basic styling for the calendar */
        #calendar {
            max-width: 900px;
            margin: 40px auto;
            padding: 10px;
        }

        /* Add some basic styling for the modal */
        #eventModal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
        }

        #eventModal form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
        }

        #deleteEventBtn {
            background-color: red;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }

        #deleteEventBtn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

    <h1>Event Calendar</h1>

    <!-- FullCalendar container -->
    <div id="calendar"></div>

    <!-- Modal for adding or editing events -->
    <div id="eventModal">
        <form id="eventForm">
            <label for="eventName">Event Name:</label>
            <input type="text" id="eventName" name="eventName" required><br>

            <label for="start_time">Start Time:</label>
            <input type="datetime-local" id="start_time" name="start_time" required><br>

            <label for="end_time">End Time:</label>
            <input type="datetime-local" id="end_time" name="end_time" required><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea><br>

            <input type="hidden" id="eventId" name="eventId">
            <button type="submit">Save Event</button>
            <button type="button" id="deleteEventBtn" style="display:none;">Delete Event</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize FullCalendar using the correct syntax for FullCalendar 5.x
            var calendar = new FullCalendar.Calendar($('#calendar')[0], {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    // Fetch events from the backend API
                    $.ajax({
                        url: "/api/events",  // API route to fetch events
                        dataType: 'json',
                        success: function(data) {
                            var events = data.map(function(event) {
                                return {
                                    title: event.name,
                                    start: event.start_time,
                                    end: event.end_time,
                                    description: event.description,
                                    id: event.id
                                };
                            });
                            successCallback(events);  // Return events to FullCalendar
                        }
                    });
                },

                // Allow adding events by clicking on the calendar
                selectable: true,
                select: function(info) {
                    $('#eventModal').show();  // Show the event form modal
                    $('#start_time').val(info.startStr);  // Set default start time
                    $('#end_time').val(info.endStr);  // Set default end time
                },

                // Allow editing and deleting events on click
                eventClick: function(info) {
                    $('#eventModal').show();
                    $('#eventName').val(info.event.title);
                    $('#start_time').val(info.event.start.toISOString().slice(0, 16));  // Format ISO datetime for input
                    $('#end_time').val(info.event.end.toISOString().slice(0, 16));
                    $('#description').val(info.event.extendedProps.description);
                    $('#eventId').val(info.event.id);  // Set hidden event ID

                    // Event deletion
                    $('#deleteEventBtn').show().off('click').on('click', function() {
                        $.ajax({
                            url: '/api/events/' + info.event.id,
                            method: 'DELETE',
                            success: function() {
                                calendar.refetchEvents();  // Reload events
                                $('#eventModal').hide();
                            }
                        });
                    });
                }
            });

            // Render the calendar
            calendar.render();

            // Form submission for adding/updating events
            $('#eventForm').on('submit', function(e) {
                e.preventDefault();  // Prevent the form from submitting normally

                var eventData = {
                    name: $('#eventName').val(),
                    start_time: $('#start_time').val(),
                    end_time: $('#end_time').val(),
                    description: $('#description').val(),
                };

                var eventId = $('#eventId').val();
                if (eventId) {
                    // Update an existing event
                    $.ajax({
                        url: '/api/events/' + eventId,  // Update event route
                        method: 'PUT',
                        data: eventData,
                        success: function(data) {
                            calendar.refetchEvents();  // Reload events
                            $('#eventModal').hide();
                        }
                    });
                } else {
                    // Create a new event
                    $.ajax({
                        url: '/api/events',  // Create event route
                        method: 'POST',
                        data: eventData,
                        success: function(data) {
                            calendar.refetchEvents();  // Reload events
                            $('#eventModal').hide();
                        }
                    });
                }
            });
        });
    </script>
    
</body>
</html>

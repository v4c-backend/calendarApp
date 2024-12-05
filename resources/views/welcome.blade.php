<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Event Calendar</title>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        h1 {
            text-align: center;
            color: #058b63;
            font-size: 2.5rem;
            margin-top: 20px;
            font-family: 'Helvetica', sans-serif;
            font-weight: bold;
        }

        #calendar {
            max-width: 500px;
            margin: 20px auto;
            padding: 0;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

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
            z-index: 9999;
        }

        #eventModal form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        #eventModal form label {
            font-size: 1rem;
            font-weight: bold;
            color: #333333;
        }

        #eventModal form input,
        #eventModal form textarea {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #cccccc;
            border-radius: 5px;
        }

        #eventModal form button {
            padding: 10px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #eventModal form button[type="submit"] {
            background-color: #058b63;
            color: white;
            font-weight: bold;
        }

        #eventModal form button[type="submit"]:hover {
            background-color: #046c4b;
        }

        #deleteEventBtn {
            background-color: #ff4d4d;
            color: white;
            font-weight: bold;
        }

        #deleteEventBtn:hover {
            background-color: #cc0000;
        }

        #closeModalBtn {
            background-color: #cccccc;
            color: #333333;
            font-weight: bold;
        }

        #closeModalBtn:hover {
            background-color: #aaaaaa;
        }
    </style>
</head>
<body>

    <h1>Event Calendar</h1>

    <div id="calendar"></div>

    <div id="eventModal">
        <form id="eventForm">
            <label for="eventName">Event Name:</label>
            <input type="text" id="eventName" name="eventName" required>

            <label for="start_time">Start Time:</label>
            <input type="datetime-local" id="start_time" name="start_time" required>

            <label for="end_time">End Time:</label>
            <input type="datetime-local" id="end_time" name="end_time" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="3"></textarea>

            <input type="hidden" id="eventId" name="eventId">
            
            <div style="display: flex; justify-content: space-between; gap: 10px;">
                <button type="submit">Save Event</button>
                <button type="button" id="deleteEventBtn" style="display:none;">Delete Event</button>
                <button type="button" id="closeModalBtn">Cancel</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            var calendar = new FullCalendar.Calendar($('#calendar')[0], {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: function (fetchInfo, successCallback, failureCallback) {
                    $.ajax({
                        url: "/api/events",
                        dataType: 'json',
                        success: function (data) {
                            var events = data.map(function (event) {
                                return {
                                    title: event.name,
                                    start: event.start_time,
                                    end: event.end_time,
                                    description: event.description,
                                    id: event.id
                                };
                            });
                            successCallback(events);
                        }
                    });
                },
                selectable: true,
                select: function (info) {
                    $('#eventModal').show();
                    $('#start_time').val(info.startStr);
                    $('#end_time').val(info.endStr);
                },
                eventClick: function (info) {
                    $('#eventModal').show();
                    $('#eventName').val(info.event.title);
                    $('#start_time').val(info.event.start.toISOString().slice(0, 16));
                    $('#end_time').val(info.event.end.toISOString().slice(0, 16));
                    $('#description').val(info.event.extendedProps.description);
                    $('#eventId').val(info.event.id);

                    $('#deleteEventBtn').show().off('click').on('click', function () {
                        $.ajax({
                            url: '/api/events/' + info.event.id,
                            method: 'DELETE',
                            success: function () {
                                calendar.refetchEvents();
                                $('#eventModal').hide();
                            }
                        });
                    });
                }
            });

            calendar.render();

            $('#eventForm').on('submit', function (e) {
                e.preventDefault();
                var eventData = {
                    name: $('#eventName').val(),
                    start_time: $('#start_time').val(),
                    end_time: $('#end_time').val(),
                    description: $('#description').val(),
                };

                var eventId = $('#eventId').val();
                if (eventId) {
                    $.ajax({
                        url: '/api/events/' + eventId,
                        method: 'PUT',
                        data: eventData,
                        success: function () {
                            calendar.refetchEvents();
                            $('#eventModal').hide();
                        }
                    });
                } else {
                    $.ajax({
                        url: '/api/events',
                        method: 'POST',
                        data: eventData,
                        success: function () {
                            calendar.refetchEvents();
                            $('#eventModal').hide();
                        }
                    });
                }
            });

            $('#closeModalBtn').on('click', function () {
                $('#eventModal').hide();
            });
        });
    </script>

</body>
</html>

@props(['leaves'])

<!-- Include FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
<!-- Include FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>

<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl lg:text-2xl font-semibold text-blue-800 mb-4">Employee Leave & Holiday Calendar</h2>
    <div id='calendar'></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let holidayEvents = [];

        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: function(fetchInfo, successCallback, failureCallback) {
                // Combine leave events with holiday events
                const leaveEvents = [
                    @foreach ($leaves as $leave)
                    {
                        title: '{{ $leave->leave_type }} - {{ $leave->user->name }}',
                        start: '{{ $leave->start_date }}',
                        end: '{{ \Carbon\Carbon::parse($leave->end_date)->addDay()->format('Y-m-d') }}',
                        color: getColorForLeaveType('{{ $leave->leave_type }}')
                    },
                    @endforeach
                ];
                successCallback([...leaveEvents, ...holidayEvents]);
            },
            datesSet: function() {
                loadHolidays();
            },
            dayCellClassNames: function(arg) {
                return arg.isToday ? ['highlight-today'] : [];
            }
        });

        calendar.render();

        function loadHolidays() {
            fetch('/api/holidays')
                .then(response => response.json())
                .then(data => {
                    // Map holidays as background events for full-cell highlight
                    holidayEvents = data.map(holiday => ({
                        start: holiday.holiday_date,
                        end: holiday.holiday_date,
                        display: 'background',
                        color: '#32CD3233',
                        title: holiday.description
                    }));

                    calendar.refetchEvents(); // Refresh calendar events
                })
                .catch(error => console.error('Error loading holidays:', error));
        }

        function getColorForLeaveType(leaveType) {
            switch(leaveType) {
                case 'Annual Leave':
                    return '#E2BD6B';
                case 'Casual Leave':
                    return '#FF4500';
                case 'Medical Leave':
                    return '#32CD32';
                case 'Short Leave':
                    return '#1E90FF';
                case 'Half Day':
                    return '#8A2BE2';
                case 'Duty Leave':
                    return '#FF69B4';
                case 'Maternity/Paternity Leave':
                    return '#FF6347';
                case 'No Pay Leave':
                    return '#2F4F4F';
                case 'Paternity Leave':
                    return '#4682B4';
                case 'Study/Training Leave':
                    return '#20B2AA';
                default:
                    return '#808080';
            }
        }
    });
</script>

<style>
    .highlight-today {
        background-color: #F4E0AF !important;
        color: white !important;
        border-radius: 4px;
    }
</style>


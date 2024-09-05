@props(['leaves'])

<!-- Include FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
<!-- Include FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>

<div class="bg-blue-200 shadow-md rounded-lg p-6">
    <h2 class="text-xl lg:text-2xl font-semibold text-blue-800 mb-4">Employee Leave Calendar</h2>
    <div id='calendar'></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: [
                @foreach ($leaves as $leave)
                {
                    title: '{{ $leave->leave_type }} - {{ $leave->user->name }}',
                    start: '{{ $leave->start_date }}',
                    end: '{{ \Carbon\Carbon::parse($leave->end_date)->addDay()->format('Y-m-d') }}',
                    color: getColorForLeaveType('{{ $leave->leave_type }}')
                },
                @endforeach
            ],
            dayCellClassNames: function(arg) {
                // Highlight today with a distinct class
                if (arg.isToday) {
                    return ['highlight-today'];
                }
                return [];
            }
        });

        calendar.render();
    });

    function getColorForLeaveType(leaveType) {
        switch(leaveType) {
            case 'Annual Leave':
                return '#E2BD6B'; // Dark Yellow 
            case 'Casual Leave':
                return '#FF4500'; // Orange Red
            case 'Medical Leave':
                return '#32CD32'; // Lime Green
            case 'Short Leave':
                return '#1E90FF'; // Dodger Blue
            case 'Half Day':
                return '#8A2BE2'; // Blue Violet
            case 'Duty Leave':
                return '#FF69B4'; // Hot Pink
            case 'Maternity/Paternity Leave':
                return '#FF6347'; // Tomato
            case 'No Pay Leave':
                return '#2F4F4F'; // Dark Slate Gray
            case 'Paternity Leave':
                return '#4682B4'; // Steel Blue
            case 'Study/Training Leave':
                return '#20B2AA'; // Light Sea Green
            default:
            return '#808080'; // Gray for other types
        }
    }
</script>

<!-- Add custom styling for today's date -->
<style>
    .highlight-today {
        background-color: #d9adfa !important; /* Bright orange color */
        color: white !important;
        border-radius: 0%;
    }
</style>

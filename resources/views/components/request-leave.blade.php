<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HRM System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- jQuery and jQuery UI JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Page Content -->
        <main>
            <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                <x-application-logo class="block h-12 w-auto" />

                <h1 class="mt-8 text-2xl font-medium text-gray-900">
                    Request Leave
                </h1>
            </div>

            <div>
                <div class="py-6">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                            <div class="p-6">
                                <x-validation-errors class="mb-4" />

                                <form method="POST" action="/saveLeave">
                                    @csrf

                                    @if(session('msg'))
                                        <div class="alert alert-success">{{session('msg')}} </div>
                                    @endif

                                    <div class="w-full">
                                        <x-label for="leave_type" value="{{ __('Leave Type') }}" />
                                        <select id="leave_type_select" name="leave_type" class="block mt-1 w-full">
                                            <option value="" disabled selected>Select Leave Type</option>
                                            @if(auth()->user()->category == 'internship')
                                                <!-- Show Study/Training Leave as a priority option for interns -->
                                                <option value="Study/Training Leave">Study/Training Leave</option>
                                                <option value="Half Day">Half Day</option>
                                                <option value="Short Leave">Short Leave</option>
                                                <option value="Duty Leave">Duty Leave</option>
                                                <option value="Election Leave - Half Day">Election Leave - Half Day</option>
                                                <option value="Election Leave - Full Day">Election Leave - Full Day</option>
                                            @else
                                                <!-- Common leave options available to all users -->
                                                <option value="Casual Leave">Casual Leave</option>
                                                <option value="Annual Leave">Annual Leave</option>
                                                <option value="Medical Leave">Medical Leave</option>
                                                <option value="Maternity/Paternity Leave">Maternity/Paternity Leave</option>
                                                <option value="Work On Leave">Work On Leave</option>
                                                <option value="No Pay Leave">No Pay Leave</option>
                                                <option value="Half Day">Half Day</option>
                                                <option value="Short Leave">Short Leave</option>
                                                <option value="Duty Leave">Duty Leave</option>
                                                <option value="Study/Training Leave">Study/Training Leave</option>
                                                <option value="Election Leave - Half Day">Election Leave - Half Day</option>
                                                <option value="Election Leave - Full Day">Election Leave - Full Day</option>
                                                <option value="Other">Other</option>
                                            @endif
                                        </select>
                                    </div>

                                    <!-- Time selection slots -->
                                    <div class="mt-4" id="timeSelection" style="display: none;">
                                        <!-- Time selection for Short Leave -->
                                        <div id="shortLeaveTime" style="display: none;">
                                            <x-label for="short_leave_time" value="{{ __('Select Time Slot') }}" />
                                            <select id="short_leave_time" name="short_leave_time" class="block mt-1 w-full">
                                                <option value="" disabled selected>Select Time Slot</option>
                                                <option value="8:30 AM - 10:00 AM">8:30 AM - 10:00 AM</option>
                                                <option value="3:30 PM - 5:00 PM">3:30 PM - 5:00 PM</option>
                                            </select>
                                        </div>

                                        <!-- Time selection for Half Day -->
                                        <div id="halfDayTime" style="display: none;">
                                            <x-label for="half_day_time" value="{{ __('Select Time Slot') }}" />
                                            <select id="half_day_time" name="half_day_time" class="block mt-1 w-full">
                                                <option value="" disabled selected>Select Time Slot</option>
                                                <option value="8:30 AM - 12:30 PM">8:30 AM - 12:30 PM</option>
                                                <option value="12:30 PM - 5:00 PM">12:30 PM - 5:00 PM</option>
                                            </select>
                                        </div>

                                        <!-- Time selection for Duty Leave -->
                                        <div id="dutyLeaveTime" style="display: none;">
                                            <x-label for="duty_leave_time" value="{{ __('Select Time Slot') }}" />
                                            <select id="duty_leave_time" name="duty_leave_time" class="block mt-1 w-full">
                                                <option value="" disabled selected>Select Time Slot</option>
                                                <option value="8:30 AM - 12:30 PM">8:30 AM - 12:30 PM - Half Day - Morning</option>
                                                <option value="12:30 PM - 5:00 PM">12:30 PM - 5:00 PM - Half Day - Evening</option>
                                                <option value="8:30 AM - 10:00 AM">8:30 AM - 10:00 AM - Short Leave - Morning</option>
                                                <option value="3:30 PM - 5:00 PM">12:30 PM - 5:00 PM - Short Leave - Evening</option>
                                                <option value="8:30 AM - 5:00 PM">8:30 AM - 5:00 PM - Full Day</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-4" id="otherLeaveType" style="display: none;">
                                        <x-label for="other_leave_type" value="{{ __('Other Leave Type') }}" />
                                        <input type="text" id="other_leave_type" name="other_leave_type" class="block mt-1 w-full" placeholder="Enter Other Leave Type">
                                    </div>

                                    <div class="mt-4">
                                        <x-label for="start_date" value="{{ __('Start Date') }}" />
                                        <x-input id="start_date" class="block mt-1 w-full" type="text" name="start_date" :value="old('start_date')" placeholder="YYYY-MM-DD"/>
                                    </div>

                                    <div class="mt-4">
                                        <x-label for="end_date" value="{{ __('End Date') }}" />
                                        <x-input id="end_date" class="block mt-1 w-full" type="text" name="end_date" :value="old('end_date')" placeholder="YYYY-MM-DD" />
                                    </div>

                                    <div class="mt-4">
                                        <x-label for="reason" value="{{ __('Reason') }}" />
                                        <textarea id="reason" name="reason" rows="4" cols="50" class="form-textarea mt-1 block w-full">{{ old('reason') }}</textarea>
                                    </div>

                                    <div class="mt-4">
                                        <x-label for="additional_notes" value="{{ __('Additional Notes') }}" />
                                        <textarea id="additional_notes" name="additional_notes" rows="4" cols="50" class="form-textarea mt-1 block w-full">{{ old('additional_notes') }}</textarea>
                                    </div>

                                    <div class="mt-4">
                                        <x-label for="covering_person" value="{{ __('Covering Person') }}" />
                                        <select id="covering_person" name="covering_person" class="block mt-1 w-full">
                                            <option value="" disabled selected>Select Employee</option>
                                            @foreach($users as $data)
                                                <option value="{{$data->id}}">{{$data->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                                        <x-button id="submitRequestButton" class="ml-4">
                                            {{ __('Submit Request') }}
                                        </x-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @stack('modals')

    @livewireScripts

    <script>
        document.getElementById('leave_type_select').addEventListener('change', function() {
            var selectedValue = this.value;
            var timeSelection = document.getElementById('timeSelection');
            var shortLeaveTime = document.getElementById('shortLeaveTime');
            var halfDayTime = document.getElementById('halfDayTime');
            var dutyLeaveTime = document.getElementById('dutyLeaveTime');
            var otherLeaveType = document.getElementById('otherLeaveType');

            // Hide all time selection elements initially
            shortLeaveTime.style.display = 'none';
            halfDayTime.style.display = 'none';
            dutyLeaveTime.style.display = 'none';

            if (selectedValue === 'Other') {
                otherLeaveType.style.display = 'block';
                timeSelection.style.display = 'none';
            } else if (selectedValue === 'Short Leave') {
                timeSelection.style.display = 'block';
                shortLeaveTime.style.display = 'block';
                otherLeaveType.style.display = 'none';
            } else if (selectedValue === 'Half Day') {
                timeSelection.style.display = 'block';
                halfDayTime.style.display = 'block';
                otherLeaveType.style.display = 'none';
            } else if (selectedValue === 'Duty Leave') {
                timeSelection.style.display = 'block';
                dutyLeaveTime.style.display = 'block';
                otherLeaveType.style.display = 'none';
            } else {
                timeSelection.style.display = 'none';
                otherLeaveType.style.display = 'none';
            }
        });

        // Initialize date picker
        $(function() {
            function isHolidayOrWeekend(date) {
                var day = date.getDay();
                var dateString = $.datepicker.formatDate('yy-mm-dd', date);
                return (day === 0 || day === 6 || holidays.includes(dateString));
            }

            var holidays = @json(\App\Models\Holiday::pluck('holiday_date')->toArray());

            $('#start_date, #end_date').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 0, // Ensures today and future dates are selectable
                beforeShowDay: function(date) {
                    return [!isHolidayOrWeekend(date), ''];
                }
            });
        });
    </script>
</body>
</html>

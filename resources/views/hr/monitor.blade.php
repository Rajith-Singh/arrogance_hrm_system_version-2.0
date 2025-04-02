<x-app-layout>
    <div class="flex">
        <x-hr-sidebar />

        <div class="flex-1">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="container">
                            <h1 class="text-center mb-4">Remote Attendance Monitoring</h1>
                        
                            <form method="GET" action="{{ route('attendance.monitor') }}" class="mb-4">
                                <label for="date" class="form-label">Select Date</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ $date }}" />
                                <button type="submit" class="btn btn-primary mt-3">Filter by Date</button>
                            </form>
                        
                            @if($attendances->isEmpty())
                                <div class="alert alert-warning">No attendance records found for this date.</div>
                            @else
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Employee Name</th>
                                            <th>Check-in Time</th>
                                            <th>Check-in Location</th>
                                            <th>Check-out Image</th>
                                            <th>Check-out Time</th>
                                            <th>Check-out Location</th>
                                            <th>Check-out Image</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attendances as $attendance)
                                            <tr>
                                                <td>{{ $attendance->emp_id }}</td>
                                                <td>{{ $attendance->employee_name }}</td>  <!-- Display employee name -->
                                                <td>{{ $attendance->check_in_time ?? '' }}</td>
                                                <td id="checkin-location-{{ $attendance->id }}"></td>
                                                <td>
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#imageModal-{{ $attendance->id }}">
                                                        <img src="https://outstation.arrogance.lk/{{ $attendance->check_in_image_path }}" alt="Employee Image" width="100">
                                                    </a>
                                                </td>
                                                <td>{{ $attendance->check_out_time ? $attendance->check_out_time : '' }}</td>
                                                <td id="checkout-location-{{ $attendance->id }}"></td>
                                                <td>
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#imageModal-{{ $attendance->id }}-checkout">
                                                        <img src="https://outstation.arrogance.lk/{{ $attendance->check_out_image_path }}" alt="Employee Image" width="100">
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    <!-- Dropdown List -->
                                                    <select class="form-select" id="leave-status-{{ $attendance->id }}" style="display: inline-block; width: 200px;">
                                                        <option value="No Leave" selected>No Leave</option>
                                                        <option value="Short Leave">Short Leave</option>
                                                        <option value="Half Day">Half Day</option>
                                                        <option value="Casual Leave">Casual Leave</option>
                                                    </select>
                                        
                                                    <!-- Buttons -->
                                                    <button class="btn btn-success mx-2" onclick="handleApprove('{{ $attendance->id }}')">
                                                        Check-in
                                                    </button>
                                                    <button class="btn btn-warning" onclick="handleReject('{{ $attendance->id }}')">
                                                        Check-out
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Modal for check-in image -->
                                            <div class="modal fade" id="imageModal-{{ $attendance->id }}" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="imageModalLabel">Check-in Image for {{ $attendance->employee_name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <img src="https://outstation.arrogance.lk/{{ $attendance->check_in_image_path }}" class="img-fluid" alt="Full Image">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal for check-out image -->
                                            <div class="modal fade" id="imageModal-{{ $attendance->id }}-checkout" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="imageModalLabel">Check-out Image for {{ $attendance->employee_name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <img src="https://outstation.arrogance.lk/{{ $attendance->check_out_image_path }}" class="img-fluid" alt="Full Image">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <script>
                                                // Geocode Check-in Location
                                                fetch(`https://nominatim.openstreetmap.org/reverse?lat={{ $attendance->check_in_latitude }}&lon={{ $attendance->check_in_longitude }}&format=json`)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        document.getElementById('checkin-location-{{ $attendance->id }}').innerText = data.display_name;
                                                    })
                                                    .catch(err => console.error('Geocoding Error: ', err));

                                                // Geocode Check-out Location
                                                fetch(`https://nominatim.openstreetmap.org/reverse?lat={{ $attendance->check_out_latitude }}&lon={{ $attendance->check_out_longitude }}&format=json`)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        document.getElementById('checkout-location-{{ $attendance->id }}').innerText = data.display_name;
                                                    })
                                                    .catch(err => console.error('Geocoding Error: ', err));
                                            </script>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

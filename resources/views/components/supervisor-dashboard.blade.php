<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            background: #b71c1c;
            color: #fff;
            position: fixed;
        }

        .content {
            margin-left: auto;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #b71c1c;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        .highlight {
            color: #b71c1c;
            font-weight: bold;
        }
        a {
        text-decoration: none;
        }
    </style>
</head>
<body>


<div class="p-6 lg:p-8 bg-white border-b border-gray-200">

<div class="content">
    <div class="header">
        <h2>Welcome, {{ Auth::user()->name }}</h2>
    <p>Your dashboard overview</p>
</div>

    <p class="mt-6 text-gray-500 leading-relaxed">
        Welcome to your dedicated dashboard where you can efficiently manage leave requests from your team members. 
        As a supervisor, you play a crucial role in ensuring smooth operations and supporting your team members' 
        work-life balance. Here, you can review and approve or reject leave requests submitted by your direct reports. 
        Your timely actions help maintain productivity and ensure adequate coverage for team tasks. Thank you for your 
        dedication and commitment to supporting your team's well-being. Should you have any questions or need assistance, 
        please don't hesitate to reach out.
    </p>
</div>

<div class="row">
            <!-- Personal Overview -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="Profile Picture" class="rounded-circle me-3" style="object-fit: cover; border: 2px solid #e63946;" width="100px" height="100px">
                        <div>
                            <h5 class="card-title">{{ Auth::user()->name }}</h5>
                            <p class="card-text">Designation: [Designation]</p>
                            <p class="card-text">Department: {{ Auth::user()->department }}</p>
                            <p class="card-text">Employee ID: {{ str_pad(Auth::user()->emp_no, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Attendance Overview -->
                        <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Overview</h5>
                        <p class="card-text highlight">Today's Attendance: 8 Hours</p>
                        <button class="btn btn-danger mt-2">Clock In</button>
                    </div>
                </div>
            </div>
        </div>



    
        <div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-xl lg:text-2xl font-semibold text-blue-800">Leave Management Overview</h5>
                <p>Track your leave balances and usage in a quick and visual way.</p>
                <div class="table-responsive">
                    @if ((auth()->user()->category == 'internship') || (auth()->user()->category == 'probation'))
                        <table class="table table-bordered">
                            <thead class="table-danger">
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Leaves Taken</th>
                                    <th>Remaining Leaves</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $remainingLeaves['Leave Type'] }}</td>
                                    <td>{{ $remainingLeaves['Leaves Taken'] }}</td>
                                    <td class="{{ $remainingLeaves['Remaining Leaves'] == 0 ? 'text-danger' : '' }}">
                                        {{ $remainingLeaves['Remaining Leaves'] }}
                                    </td>
                                    <td class="{{ $remainingLeaves['Status'] == 'No Pay' ? 'text-danger' : '' }}">
                                        {{ $remainingLeaves['Status'] }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @elseif (auth()->user()->category == 'permanent')
                        <table class="table table-bordered">
                            <thead class="table-danger">
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Total Allocated</th>
                                    <th>Allocated Per Month</th>
                                    <th>Leaves Taken</th>
                                    <th>Remaining Leaves</th>
                                    <th>Usage Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($remainingLeaves as $type => $data)
                                    @php
                                        $allocated = $data['Total Allocated'];
                                        $allocatedPerMonth = $data['Allocated per month'];
                                        $taken = $data['Leaves Taken'];
                                        $remaining = $data['Remaining Leaves'];

                                        // Special logic for short leave
                                        if ($type === 'Short Leave') {
                                            $usageValue = $allocatedPerMonth > 0 ? $allocatedPerMonth - $taken : 0;
                                            $usagePercentage = $allocatedPerMonth > 0 ? round(($taken / $allocatedPerMonth) * 100) : 0;
                                        } else {
                                            $usageValue = $allocated > 0 ? $allocated - $taken : 0;
                                            $usagePercentage = $allocated > 0 ? round(($taken / $allocated) * 100) : 0;
                                        }

                                        $progressBarClass = $usagePercentage >= 100 ? 'bg-danger' : 'bg-success';
                                    @endphp
                                    <tr>
                                        <td>{{ $type }}</td>
                                        <td>{{ $allocated }}</td>
                                        <td>{{ $allocatedPerMonth }}</td>
                                        <td>{{ $taken }}</td>
                                        <td>{{ $remaining }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar {{ $progressBarClass }}" role="progressbar"
                                                    style="width: {{ $usagePercentage }}%;"
                                                    aria-valuenow="{{ $usagePercentage }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $usagePercentage }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    @elseif (auth()->user()->category == 'probation')
                        <table class="table table-bordered">
                            <thead class="table-danger">
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Total Allocated</th>
                                    <th>Allocated Per Month</th>
                                    <th>Leaves Taken</th>
                                    <th>Remaining Leaves</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($remainingLeaves as $type => $data)
                                    @php
                                        $allocated = $data['Total Allocated'];
                                        $taken = $data['Leaves Taken'];
                                        $rowClass = $allocated <= $taken ? 'text-danger' : '';
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>{{ $type }}</td>
                                        <td>{{ $allocated }}</td>
                                        <td>{{ $data['Allocated per month'] }}</td>
                                        <td>{{ $taken }}</td>
                                        <td>{{ $data['Remaining Leaves'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Charts -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Trends (Current Week)</h5>
                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Leave Behavior (Current Week)</h5>
                        <div class="chart-container">
                            <canvas id="leaveChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


<script>
    // Get the authenticated user's ID dynamically from the backend
    const userId = {{ Auth::user()->emp_no }};

    async function fetchAttendanceData() {
        try {
            // Fetch attendance data from the backend
            const response = await fetch(`/api/attendance?user_id=${userId}`, {
                method: 'GET',
                credentials: 'include', // Include credentials for session authentication
            });

            if (!response.ok) {
                throw new Error('Failed to fetch attendance data');
            }

            const attendanceData = await response.json();

            // Process attendance data for the chart
            const labels = attendanceData.map(item =>
                new Date(item.date).toLocaleDateString('en-US', {
                    weekday: 'short',
                    day: '2-digit',
                    month: 'short',
                })
            );

            const checkInTimes = attendanceData.map(item => {
                const [hours, minutes] = item.check_in.split(':');
                return parseFloat(hours) + parseFloat(minutes) / 60; // Convert HH:mm to decimal hours
            });

            const checkOutTimes = attendanceData.map(item => {
                const [hours, minutes] = item.check_out.split(':');
                return parseFloat(hours) + parseFloat(minutes) / 60; // Convert HH:mm to decimal hours
            });

            // Render the attendance chart
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Check-In Time',
                            data: checkInTimes,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderWidth: 2,
                            tension: 0.4,
                        },
                        {
                            label: 'Check-Out Time',
                            data: checkOutTimes,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 2,
                            tension: 0.4,
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            title: {
                                display: true,
                                text: 'Time (Hours)',
                            },
                            ticks: {
                                stepSize: 1,
                                callback: value => `${Math.floor(value)}:${(value % 1) * 60}`, // Convert decimal hours to HH:mm
                            },
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date',
                            },
                        },
                    },
                },
            });
        } catch (error) {
            console.error('Error fetching attendance data:', error);
        }
    }

    // Fetch and render attendance data when the page loads
    fetchAttendanceData();
</script>

<script>
    async function fetchLeaveData() {
        try {
            // Fetch leave data from the backend
            const response = await fetch('/api/leave-data', {
                method: 'GET',
                credentials: 'include', // Include credentials for session authentication
            });

            if (!response.ok) {
                throw new Error('Failed to fetch leave data');
            }

            const leaveData = await response.json();

            // Days of the week (Monday to Friday) for the X-axis
            const labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

            // Get all unique leave types from the data
            const leaveTypes = [...new Set(leaveData.map(item => item.leave_type))];

            // Initialize datasets for each leave type with zero values
            const datasets = leaveTypes.map(type => {
                const data = labels.map(day => {
                    const dayLeave = leaveData.find(item => item.date === day && item.leave_type === type);
                    return dayLeave ? dayLeave.leave_count : 0; // Default to 0 if no leave for this type on the day
                });

                return {
                    label: type, // Label for the leave type
                    data: data,
                    backgroundColor: getRandomColor(), // Random color for each leave type
                };
            });

            // Function to generate random colors for each leave type
            function getRandomColor() {
                const r = Math.floor(Math.random() * 255);
                const g = Math.floor(Math.random() * 255);
                const b = Math.floor(Math.random() * 255);
                return `rgba(${r}, ${g}, ${b}, 0.7)`;
            }

            // Render the leave chart
            const ctx = document.getElementById('leaveChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels, // Days of the week (X-axis)
                    datasets: datasets, // Leave type datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `${context.dataset.label}: ${context.raw}`;
                                },
                            },
                        },
                        legend: {
                            position: 'top', // Show legend for leave types
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Days of the Week',
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Leaves',
                            },
                            ticks: {
                                beginAtZero: true,
                            },
                        },
                        x: {
                            stacked: true, // Enable stacked bar chart on X-axis
                        },
                        y: {
                            stacked: true, // Enable stacked bar chart on Y-axis
                        },
                    },
                },
            });
        } catch (error) {
            console.error('Error fetching leave data:', error);
        }
    }

    // Fetch and render leave data when the page loads
    fetchLeaveData();
</script>
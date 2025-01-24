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
        Welcome to your dedicated dashboard where you can oversee and manage leave requests across the organization. 
        As part of the management team, you play a pivotal role in ensuring effective resource allocation and maintaining a 
        productive workforce. Here, you can review and provide final approval for leave requests, ensuring that operations 
        continue smoothly while supporting employee well-being. Your leadership is instrumental in fostering a positive work 
        environment and driving organizational success. Should you have any inquiries or require assistance, please feel free to 
        reach out.
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


        <!-- Key Metrics Section -->
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Employees</h5>
                        <p class="highlight">500</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Active Employees</h5>
                        <p class="highlight">450</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">On Leave</h5>
                        <p class="highlight">25</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Pending Approvals</h5>
                        <p class="highlight">15</p>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Charts Section -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Trends</h5>
                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Employee Distribution</h5>
                        <div class="chart-container">
                            <canvas id="employeeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Analytics -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Leave Analysis</h5>
                        <div class="chart-container">
                            <canvas id="leaveChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gender Diversity</h5>
                        <div class="chart-container">
                            <canvas id="diversityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recruitment and Onboarding -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recruitment Pipeline</h5>
                        <div class="chart-container">
                            <canvas id="recruitmentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">New Hires</h5>
                        <div class="chart-container">
                            <canvas id="newHiresChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Training and Development -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Training Sessions</h5>
                        <div class="chart-container">
                            <canvas id="trainingChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Skill Gaps</h5>
                        <div class="chart-container">
                            <canvas id="skillGapsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        // Sample Chart Data
        const attendanceData = {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Attendance',
                data: [90, 85, 88, 92],
                backgroundColor: '#b71c1c',
                borderColor: '#d32f2f',
                borderWidth: 1
            }]
        };

        const employeeData = {
            labels: ['IT', 'HR', 'Finance', 'Marketing', 'Operations'],
            datasets: [{
                label: 'Employees',
                data: [120, 60, 80, 100, 140],
                backgroundColor: ['#f44336', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5'],
            }]
        };

        const leaveData = {
            labels: ['Annual', 'Casual', 'Medical'],
            datasets: [{
                label: 'Leaves',
                data: [40, 20, 15],
                backgroundColor: ['#ff9800', '#4caf50', '#03a9f4'],
            }]
        };

        const diversityData = {
            labels: ['Male', 'Female'],
            datasets: [{
                label: 'Gender',
                data: [60, 40],
                backgroundColor: ['#2196f3', '#f06292'],
            }]
        };

        const recruitmentData = {
            labels: ['Applied', 'Interviewed', 'Hired'],
            datasets: [{
                label: 'Recruitment',
                data: [150, 50, 20],
                backgroundColor: ['#03a9f4', '#4caf50', '#ff9800'],
            }]
        };

        const newHiresData = {
            labels: ['January', 'February', 'March'],
            datasets: [{
                label: 'New Hires',
                data: [10, 15, 12],
                backgroundColor: ['#9c27b0', '#673ab7', '#3f51b5'],
            }]
        };

        const trainingData = {
            labels: ['Technical', 'Leadership', 'Soft Skills'],
            datasets: [{
                label: 'Training Sessions',
                data: [30, 20, 25],
                backgroundColor: ['#ff5722', '#795548', '#607d8b'],
            }]
        };

        const skillGapsData = {
            labels: ['Communication', 'Coding', 'Management'],
            datasets: [{
                label: 'Skill Gaps',
                data: [15, 10, 8],
                backgroundColor: ['#f44336', '#e91e63', '#9c27b0'],
            }]
        };

        new Chart(document.getElementById('attendanceChart'), {
            type: 'line',
            data: attendanceData,
            options: { responsive: true }
        });

        new Chart(document.getElementById('employeeChart'), {
            type: 'bar',
            data: employeeData,
            options: { responsive: true }
        });

        new Chart(document.getElementById('leaveChart'), {
            type: 'bar',
            data: leaveData,
            options: { responsive: true }
        });

        new Chart(document.getElementById('diversityChart'), {
            type: 'doughnut',
            data: diversityData,
            options: { responsive: true }
        });

        new Chart(document.getElementById('recruitmentChart'), {
            type: 'bar',
            data: recruitmentData,
            options: { responsive: true }
        });

        new Chart(document.getElementById('newHiresChart'), {
            type: 'line',
            data: newHiresData,
            options: { responsive: true }
        });

        new Chart(document.getElementById('trainingChart'), {
            type: 'bar',
            data: trainingData,
            options: { responsive: true }
        });

        new Chart(document.getElementById('skillGapsChart'), {
            type: 'pie',
            data: skillGapsData,
            options: { responsive: true }
        });
    </script>
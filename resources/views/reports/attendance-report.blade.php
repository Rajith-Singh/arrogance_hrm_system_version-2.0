<!DOCTYPE html>
<html>
<head>
    <style>
        @font-face {
            font-family: 'Prosper';
            src: url('{{ storage_path('fonts/Prosper.ttf') }}') format('truetype');
        }
        body {
            font-family: 'Prosper', Arial, sans-serif;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header img {
            height: 80px;
        }
        .header h1 {
            margin: 0;
            color: red;
            flex-grow: 1;
            text-align: center;
        }
        .details {
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .details .info {
            flex-grow: 1;
        }
        .details p {
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: red;
            color: white;
        }
        .employee-photo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 50%;
        }
        .initials {
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Logo.png') }}" alt="Company Logo">
        <h1>Attendance Report</h1>
        <div style="width: 80px;"></div>
    </div>

    <div class="details">
        <div class="info">
            <p>Employee ID: <b>{{ $employee->emp_no }}</b></p>
            <p>Employee Name: <b>{{ $employee->name }}</b></p>
        </div>
        @if($employee->profile_photo_path)
            <img src="{{ storage_path('app/public/' . $employee->profile_photo_path) }}" alt="Employee Photo" class="employee-photo">
        @else
            <div class="initials">
                {{ strtoupper(substr($employee->name, 0, 1)) }}
            </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->date }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->real_check_in)->format('H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->real_check_out)->format('H:i') }}</td>
                    <td>{{ $record->verify_code }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Date: {{ \Carbon\Carbon::now('Asia/Colombo')->format('Y-m-d') }}</p>
        <p>Time: {{ \Carbon\Carbon::now('Asia/Colombo')->format('H:i') }}</p>
        <p>Generated by: {{ auth()->user()->name }}</p>
        <br><br>
        <p>[Auto generated report]</p>
    </div>
</body>
</html>

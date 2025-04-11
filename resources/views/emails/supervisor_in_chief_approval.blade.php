<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Approval Required</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #EC1F27;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .email-body {
            padding: 20px;
            line-height: 1.6;
        }
        .email-body h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }
        .email-body p {
            margin-bottom: 10px;
        }
        .email-footer {
            background-color: #f4f4f4;
            color: #777777;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            border-top: 1px solid #dddddd;
        }
        .email-footer a {
            color: #EC1F27;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            Leave Request for {{ $leave->user->name }} Requires Your Approval
        </div>
        <div class="email-body">
            <h2>Hello Supervisor,</h2>
            <p>A leave request has been approved by {{ $supervisorName }} and requires your review. Here are the details:</p>

            <ul>
                <li><strong>Leave Type:</strong> {{ $leave->leave_type }}</li>
                <li><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($leave->start_date)->format('F j, Y') }}</li>
                <li><strong>End Date:</strong> {{ \Carbon\Carbon::parse($leave->end_date)->format('F j, Y') }}</li>
                <li><strong>Reason:</strong> {{ $leave->reason }}</li>
                <li><strong>Employee:</strong> {{ $leave->user->name }}</li>
                <li><strong>Covering Person:</strong> {{ $leave->coveringPerson->name }}</li>
            </ul>

            <p>Please visit the Arrogance HRM System to review and approve the leave request.</p>
        </div>
        <div class="email-footer">
            <p>This is an auto-generated email. Please do not reply.</p>
            <p>&copy; {{ date('Y') }} Arrogance Technologies Pvt Ltd. (Software Team). All rights reserved.</p>
        </div>
    </div>
</body>
</html>

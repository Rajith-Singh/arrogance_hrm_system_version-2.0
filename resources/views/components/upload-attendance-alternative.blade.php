<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Attendance Upload</title>
    <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="file"], input[type="date"] {
            padding: 10px;
            margin-bottom: 20px;
        }
        button {
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
        .success {
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Upload Attendance CSV</h1>

    <!-- Display errors or success message -->
    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('message'))
        <div class="success">
            {{ session('message') }}
        </div>
    @endif

    <!-- Simplified File Upload Form -->
    <form action="/upload-attendance" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Date Input -->
        <label for="date">Date</label>
        <input type="date" id="date" name="date" required>

        <!-- File Input -->
        <label for="attendance_file">Select Attendance CSV File</label>
        <input type="file" id="attendance_file" name="attendance_file" accept=".csv" required>

        <!-- Submit Button -->
        <button type="submit">Upload CSV</button>
    </form>
</div>

</body>
</html>

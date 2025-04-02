<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Marking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .register-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .register-date {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }
        .table td, .table th {
            vertical-align: middle;
            text-align: center;
        }
        .toggle-switch {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .toggle-switch input[type="checkbox"] {
            display: none;
        }
        .toggle-switch label {
            width: 50px;
            height: 25px;
            background-color: #ccc;
            border-radius: 15px;
            cursor: pointer;
            position: relative;
        }
        .toggle-switch label::after {
            content: '';
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 50%;
            position: absolute;
            top: 2.5px;
            left: 2.5px;
            transition: 0.3s;
        }
        .toggle-switch input[type="checkbox"]:checked + label {
            background-color: #4caf50;
        }
        .toggle-switch input[type="checkbox"]:checked + label::after {
            transform: translateX(25px);
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="register-header mb-4">
        <h2>Mark Attendance</h2>
        <div class="register-date">Date: <span id="currentDate"></span></div>
    </div>
    <form id="registerForm">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="registerEntries">
                <!-- Employee rows will be dynamically populated here -->
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary mt-3">Submit Register</button>
    </form>
</div>

<script>
    // Function to get the current date in India timezone
    function getCurrentDateIndia() {
        const options = { year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Asia/Kolkata' };
        return new Date().toLocaleDateString('en-IN', options);
    }

    // Set the current date in the header
    document.getElementById('currentDate').innerText = getCurrentDateIndia();

    // Fetch today's register data and populate the table
    document.addEventListener('DOMContentLoaded', function () {
        fetch('/api/register/today')
            .then(response => response.json())
            .then(data => {
                const registerEntries = document.getElementById('registerEntries');
                registerEntries.innerHTML = ''; // Clear any existing rows

                data.forEach(employee => {
                    const row = document.createElement('tr');

                    row.innerHTML = `
                        <td>${employee.emp_no}</td>
                        <td>${employee.name}</td>
                        <td>
                            <div class="toggle-switch">
                                <input type="checkbox" id="status-${employee.id}" data-user-id="${employee.id}" ${employee.status === 1 ? 'checked' : ''}>
                                <label for="status-${employee.id}"></label>
                            </div>
                        </td>
                    `;

                    registerEntries.appendChild(row);
                });
            })
            .catch(error => console.error('Error fetching register:', error));
    });

    // Handle form submission to update the register
    document.getElementById('registerForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const today = new Date().toISOString().split('T')[0]; // Current date in 'YYYY-MM-DD'
        const register = [];

        // Collect data from toggles
        document.querySelectorAll('[data-user-id]').forEach(toggle => {
            const userId = toggle.getAttribute('data-user-id');
            const status = toggle.checked ? 1 : 0; // 1 for Active, 0 for On Leave
            register.push({ user_id: userId, status: status, date: today });
        });

        // Submit register data via API
        fetch('/api/register/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ register })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message); // Notify the user of success
        })
        .catch(error => {
            console.error('Error submitting register:', error);
        });
    });
</script>
</body>
</html>

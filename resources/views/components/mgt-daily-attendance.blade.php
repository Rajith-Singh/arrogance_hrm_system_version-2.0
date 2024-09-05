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
                    Daily Staff Attendance
                </h1>
            </div>

            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <x-validation-errors class="mb-4" />

                            <!-- Date Filter Form -->
                            <form id="filter-form" class="mb-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 mr-4">
                                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                        <input type="date" id="start_date" name="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div class="flex-1 mr-4">
                                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                        <input type="date" id="end_date" name="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div class="flex-1">
                                        <label for="filter" class="block text-sm font-medium text-gray-700 invisible">Filter</label>
                                        <button type="submit" class="mt-1 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Filter
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Attendance Records -->
                            <div id="attendance-records" class="mt-6">
                                <!-- Attendance records will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('filter-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            fetch(`/emp-daily-attendance-tracking?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    const recordsDiv = document.getElementById('attendance-records');
                    recordsDiv.innerHTML = '';

                    if (data.length > 0) {
                        const table = document.createElement('table');
                        table.className = 'min-w-full divide-y divide-gray-200';

                        const thead = document.createElement('thead');
                        thead.innerHTML = `
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Real Check In</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Real Check Out</th>
                            </tr>
                        `;
                        table.appendChild(thead);

                        const tbody = document.createElement('tbody');
                        tbody.className = 'bg-white divide-y divide-gray-200';

                        data.forEach(record => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap">${record.employee_name}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${record.date}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${record.check_in}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${record.check_out}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${record.real_check_in}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${record.real_check_out}</td>
                            `;
                            tbody.appendChild(row);
                        });

                        table.appendChild(tbody);
                        recordsDiv.appendChild(table);
                    } else {
                        recordsDiv.innerHTML = '<p class="text-gray-500">No attendance records found for the selected date range.</p>';
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    @livewireScripts
</body>
</html>

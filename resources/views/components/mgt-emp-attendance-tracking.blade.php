<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <x-application-logo class="block h-12 w-auto" />
    <h1 class="mt-8 text-2xl font-medium text-gray-900">View Attendance</h1>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <x-validation-errors class="mb-4" />

                <!-- Date and Employee ID/Name Filter Form -->
                <form id="filter-form" class="mb-6">
                    <div class="flex items-center justify-between">
                        <!-- Employee ID / Name Autocomplete -->
                        <div class="flex-1 mr-4 relative">
                            <label for="employee_search" class="block text-sm font-medium text-gray-700">Employee Name or ID</label>
                            <input type="text" id="employee_search" name="employee_search" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter Employee ID or Name">

                            <!-- Autocomplete Suggestions Dropdown -->
                            <ul id="employee_suggestions" class="absolute z-10 w-full bg-white shadow-lg rounded-md mt-1 hidden"></ul>
                        </div>
                        
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

<script>
    document.getElementById('employee_search').addEventListener('input', function () {
        const query = this.value;

        if (query.length >= 2) { // Start searching after 2 characters
            fetch(`/employee-search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    const suggestions = document.getElementById('employee_suggestions');
                    suggestions.innerHTML = '';
                    suggestions.classList.remove('hidden');

                    if (data.length > 0) {
                        data.forEach(item => {
                            const suggestionItem = document.createElement('li');
                            suggestionItem.className = 'p-2 hover:bg-indigo-600 hover:text-white cursor-pointer';
                            suggestionItem.textContent = `${item.name} (${item.emp_no})`;
                            suggestionItem.onclick = function () {
                                document.getElementById('employee_search').value = item.name; // Fill in Employee Name
                                suggestions.classList.add('hidden'); // Hide suggestions after selecting
                            };
                            suggestions.appendChild(suggestionItem);
                        });
                    } else {
                        const noResultItem = document.createElement('li');
                        noResultItem.className = 'p-2 text-gray-500';
                        noResultItem.textContent = 'No matching results';
                        suggestions.appendChild(noResultItem);
                    }
                })
                .catch(error => console.error('Error fetching suggestions:', error));
        } else {
            document.getElementById('employee_suggestions').classList.add('hidden');
        }
    });

    // Handle form submission for filtering attendance records
    document.getElementById('filter-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const employeeId = document.getElementById('employee_search').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        fetch(`/emp-attendance-tracking-mgt?employee_id=${employeeId}&start_date=${startDate}&end_date=${endDate}`)
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
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verify Code</th>
                        </tr>
                    `;
                    table.appendChild(thead);

                    const tbody = document.createElement('tbody');
                    tbody.className = 'bg-white divide-y divide-gray-200';

                    data.forEach(record => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap">${record.date}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${record.real_check_in}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${record.real_check_out}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${record.verify_code}</td>
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

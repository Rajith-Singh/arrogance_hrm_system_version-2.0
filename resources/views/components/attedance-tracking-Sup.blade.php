<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <x-application-logo class="block h-12 w-auto" />

    <h1 class="mt-8 text-2xl font-medium text-gray-900">
        Track Your Attendance
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

<script>
    document.getElementById('filter-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        fetch(`/attendance-tracking-sup?start_date=${startDate}&end_date=${endDate}`)
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
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    `;
                    table.appendChild(thead);

                    const tbody = document.createElement('tbody');
                    tbody.className = 'bg-white divide-y divide-gray-200';

                    data.forEach(record => {
                        const row = document.createElement('tr');
                        
                        // Check if the Check In > 8:30 AM or Check Out < 5:00 PM and highlight accordingly
                        const checkInTime = new Date(`1970-01-01T${record.real_check_in}`);
                        const checkOutTime = new Date(`1970-01-01T${record.real_check_out}`);
                        const checkInLimit = new Date('1970-01-01T08:30:00');
                        const checkOutLimit = new Date('1970-01-01T17:00:00');

                        let checkInClass = checkInTime > checkInLimit ? 'text-red-600 font-bold' : '';
                        let checkOutClass = checkOutTime < checkOutLimit ? 'text-red-600 font-bold' : '';

                        // Only show text fields for late or early check-outs
                        let reasonField = '';
                        let actionButton = '';

                        if (checkInClass || checkOutClass) {
                            reasonField = `<span id="reason-text-${record.id}">${record.reason || ''}</span>`;

                            // If reason exists, show "Edit" button (yellow), else show "Add" button (blue)
                            if (!record.reason) {
                                actionButton = `<button onclick="editReason(${record.id})" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add</button>`;
                            } else {
                                actionButton = `<button onclick="editReason(${record.id})" class="ml-2 bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">Edit</button>`;
                            }
                        } else {
                            reasonField = `${record.reason || ''}`;
                        }

                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap">${record.date}</td>
                            <td class="px-6 py-4 whitespace-nowrap ${checkInClass}">${record.real_check_in}</td>
                            <td class="px-6 py-4 whitespace-nowrap ${checkOutClass}">${record.real_check_out}</td>
                            <td class="px-6 py-4 whitespace-nowrap" id="reason-container-${record.id}">${reasonField}</td>
                            <td class="px-6 py-4 whitespace-nowrap" id="action-${record.id}">${actionButton}</td>
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

    // Function to show input field for adding/editing reason
    function editReason(recordId) {
        const reasonText = document.getElementById(`reason-text-${recordId}`).innerText || '';
        document.getElementById(`reason-container-${recordId}`).innerHTML = `
            <input type="text" id="reason-${recordId}" value="${reasonText}" class="border-gray-300 rounded-md shadow-sm">
        `;
        document.getElementById(`action-${recordId}`).innerHTML = `
            <button onclick="updateReason(${recordId})" class="ml-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Update</button>
        `;
    }

    // Function to submit or update reason
    function updateReason(recordId) {
        const reason = document.getElementById(`reason-${recordId}`).value;
        fetch(`/submit-reason-sup`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                record_id: recordId,
                reason: reason
            })
        }).then(response => response.json())
          .then(data => {
              alert('Reason updated successfully!');
              document.getElementById(`reason-container-${recordId}`).innerHTML = `<span id="reason-text-${recordId}">${reason}</span>`;
              document.getElementById(`action-${recordId}`).innerHTML = `<button onclick="editReason(${recordId})" class="ml-2 bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">Edit</button>`;
          }).catch(error => {
              console.error('Error:', error);
          });
    }
</script>

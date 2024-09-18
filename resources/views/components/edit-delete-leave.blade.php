
<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <x-application-logo class="block h-12 w-auto" />
    <h1 class="mt-8 text-2xl font-medium text-gray-900">Edit/Delete Leave</h1>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <x-validation-errors class="mb-4" />
                <!-- Date and Employee ID Filter Form -->
                <form id="filter-form" class="mb-6">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div class="flex-1 mr-4">
                            <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
                            <input type="text" id="employee_id" name="employee_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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
                            <button type="submit" class="mt-1 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Filter</button>
                        </div>
                    </div>
                </form>

                <!-- Leave Records -->
                <div id="leave-records" class="mt-6"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Deletion Reason and Attachment -->
<div id="deleteModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl max-w-lg w-full">
        <h2 class="text-xl font-semibold mb-4">Enter Reason for Deletion</h2>
        <form id="delete-form" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                <textarea id="reason" name="reason" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required></textarea>
            </div>
            <div class="mb-4">
                <label for="attachment" class="block text-sm font-medium text-gray-700">Attachment (optional)</label>
                <input type="file" id="attachment" name="attachment" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <input type="hidden" id="leave_id" name="leave_id">
            <div class="flex justify-end">
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2" onclick="closeModal()">Cancel</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
// Handle form submission
document.getElementById('delete-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent the default form submission

    const leaveId = document.getElementById('leave_id').value;
    const reason = document.getElementById('reason').value;
    const attachment = document.getElementById('attachment').files[0];

    const formData = new FormData();
    formData.append('leave_id', leaveId);
    formData.append('reason', reason);
    if (attachment) {
        formData.append('attachment', attachment);
    }

    // Send the request via fetch API
    fetch(`/leaves/${leaveId}/delete-with-reason`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Leave deletion request submitted successfully.');
            closeModal();
            document.getElementById('filter-form').submit(); // Refresh leave records after submission
        } else {
            alert('Failed to submit deletion request.');
        }
    })
    .catch(error => {
        console.error('Error:', error); // Log any errors
        alert('An error occurred: ' + error.message);
    });
});

function deleteLeave(leaveId) {
    document.getElementById('leave_id').value = leaveId; // Set the leave ID in the hidden input field
    document.getElementById('deleteModal').classList.remove('hidden'); // Show the modal
}

function closeModal() {
    document.getElementById('deleteModal').classList.add('hidden'); // Hide the modal
}

// Handle the filter form submission and fetch the leave records
document.getElementById('filter-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const employeeId = document.getElementById('employee_id').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    fetch(`/leaves/search`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ employee_id: employeeId, start_date: startDate, end_date: endDate })
    })
    .then(response => response.json())
    .then(data => {
        const recordsDiv = document.getElementById('leave-records');
        recordsDiv.innerHTML = '';

        if (data.leaves.length > 0) {
            const employeeSection = document.createElement('div');
            employeeSection.className = 'flex items-center mb-4';

            const profilePhotoPath = data.profile_photo_path ? `/${data.profile_photo_path}` : null;
            const profilePhoto = profilePhotoPath 
                ? `<img src="${profilePhotoPath}" alt="${data.employee_name}" class="h-12 w-12 rounded-full mr-4">` 
                : `<div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center text-xl font-semibold text-gray-700 mr-4">${data.employee_name.split(' ').map(name => name[0]).join('')}</div>`;

            employeeSection.innerHTML = `${profilePhoto}<h2 class="text-xl font-semibold text-gray-900">Leave Records for ${data.employee_name}</h2>`;
            recordsDiv.appendChild(employeeSection);

            const table = document.createElement('table');
            table.className = 'min-w-full divide-y divide-gray-200';

            const thead = document.createElement('thead');
            thead.innerHTML = `
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            `;
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            tbody.className = 'bg-white divide-y divide-gray-200';

            data.leaves.forEach(leave => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">${leave.leave_type}</td>
                <td class="px-6 py-4 whitespace-nowrap">${leave.start_date}</td>
                <td class="px-6 py-4 whitespace-nowrap">${leave.end_date}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button onclick="deleteLeave(${leave.id})" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 ml-2">Delete</button>
                </td>
            `;
                tbody.appendChild(row);
            });

            table.appendChild(tbody);
            recordsDiv.appendChild(table);
        } else {
            recordsDiv.innerHTML = '<p class="text-gray-500">No leave records found for the selected date range.</p>';
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>


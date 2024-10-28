<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <x-application-logo class="block h-12 w-auto" />

    <h1 class="mt-8 text-2xl font-medium text-gray-900">
        Manage Employee Leave
    </h1>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <x-validation-errors class="mb-4" />

                @if(session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if(session('del-message'))
                    <div class="alert alert-primary">{{ session('del-message') }}</div>
                @endif

                <!-- Filter Section -->
                <div class="mb-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="filter-name" class="block text-sm font-medium text-gray-700">Filter by Name</label>
                        <input type="text" id="filter-name" class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Enter Employer Name" onkeyup="filterTable(true)">
                    </div>
                    <div>
                        <label for="filter-type" class="block text-sm font-medium text-gray-700">Filter by Leave Type</label>
                        <select id="filter-type" class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" onchange="filterTable(true)">
                        <option value="">All Types</option>
                            <option value="Casual Leave">Casual Leave</option>
                            <option value="Annual Leave">Annual Leave</option>
                            <option value="Medical Leave">Medical Leave</option>
                            <option value="Duty Leave">Duty Leave</option>
                            <option value="Study/Training Leave">Study/Training Leave</option>
                            <option value="Half Day">Half Day</option>
                            <option value="Short Leave">Short Leave</option>
                            <!-- Add more leave types as needed -->
                        </select>
                    </div>
                    <div>
                        <label for="filter-date" class="block text-sm font-medium text-gray-700">Filter by Leave Date</label>
                        <input type="date" id="filter-date" class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" onchange="filterTable(true)">
                    </div>
                </div>

                <!-- Leave Table -->
                <div class="overflow-auto">
                    <table class="w-full table-auto bg-white border border-gray-300 rounded-lg shadow-md">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left border-b">Employer Name</th>
                                <th class="py-3 px-6 text-left border-b">Leave Type</th>
                                <th class="py-3 px-6 text-left border-b">Leave Date</th>
                                <th class="py-3 px-6 text-left border-b">Supervisor Approval</th>
                                <th class="py-3 px-6 text-left border-b">Management Approval</th>
                                <th class="py-3 px-6 text-center border-b">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="leave-table-body" class="text-gray-700 text-sm font-light">
                            @foreach($leave as $data)
                            <tr class="hover:bg-gray-100 border-b border-gray-200" data-leave-date="{{ $data->start_date }}">
                                <td class="py-3 px-6 text-left">{{ $data->name }}</td>
                                <td class="py-3 px-6 text-left">{{ $data->leave_type }}</td>
                                <td class="py-3 px-6 text-left">{{ $data->start_date }}</td>
                                <td class="py-3 px-6 text-left">{{ $data->supervisor_approval }}</td>
                                <td class="py-3 px-6 text-left">{{ $data->management_approval }}</td>
                                <td class="py-3 px-6 text-center">
                                    <a href="/view-emp-leave/{{ $data->user_id }}/{{ $data->id }}" class="text-white bg-blue-500 hover:bg-blue-600 font-medium rounded-md px-3 py-1">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function filterTable(isFiltered) {
        const nameInput = document.getElementById('filter-name').value.toLowerCase();
        const typeInput = document.getElementById('filter-type').value.toLowerCase();
        const dateInput = document.getElementById('filter-date').value;

        const tableBody = document.getElementById('leave-table-body');
        const rows = tableBody.getElementsByTagName('tr');

        const currentDate = new Date();
        const pastMonthDate = new Date();
        pastMonthDate.setMonth(currentDate.getMonth() - 1);

        for (const row of rows) {
            const leaveDate = new Date(row.getAttribute('data-leave-date'));
            const name = row.cells[0].textContent.toLowerCase();
            const type = row.cells[1].textContent.toLowerCase();
            const date = row.cells[2].textContent;

            const matchesDateRange = leaveDate >= pastMonthDate && leaveDate <= currentDate;
            const matchesName = name.includes(nameInput);
            const matchesType = typeInput === "" || type === typeInput;
            const matchesDate = dateInput === "" || date === dateInput;

            // Display rows based on initial date range or applied filters
            if (isFiltered) {
                row.style.display = matchesName && matchesType && matchesDate ? "" : "none";
            } else {
                row.style.display = matchesDateRange ? "" : "none";
            }
        }
    }

    // Initial call to display leaves within the last month
    document.addEventListener("DOMContentLoaded", function() {
        filterTable(false);
    });
</script>

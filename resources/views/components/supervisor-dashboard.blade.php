<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <x-application-logo class="block h-12 w-auto" />

    <h1 class="mt-8 text-2xl font-medium text-gray-900">
        Welcome to the Human Resources Management System
    </h1>

    <p class="mt-6 text-gray-500 leading-relaxed">
        Welcome to your dedicated dashboard where you can efficiently manage leave requests from your team members. 
        As a supervisor, you play a crucial role in ensuring smooth operations and supporting your team members' 
        work-life balance. Here, you can review and approve or reject leave requests submitted by your direct reports. 
        Your timely actions help maintain productivity and ensure adequate coverage for team tasks. Thank you for your 
        dedication and commitment to supporting your team's well-being. Should you have any questions or need assistance, 
        please don't hesitate to reach out.
    </p>
</div>

<div class="bg-gray-100 grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 p-6 lg:p-8">
    <div class="bg-purple-200 shadow-md rounded-lg p-6 flex items-center">
        <!-- User Profile Picture -->
        <img class="img logo rounded-circle mb-5" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" /> &nbsp&nbsp&nbsp

        <!-- Content Section -->
        <div>
            <h2 class="text-xl lg:text-2xl font-semibold text-purple-800 mb-4">Hi {{ auth()->user()->name }} </h2>
            <p class="text-sm font-semibold">
                Department: {{ auth()->user()->category ? auth()->user()->category : 'Management' }}
            </p>
        </div>
    </div>

    <!-- User Profile Information Component -->
    <div class="bg-yellow-200 shadow-md rounded-lg p-6">
        <h2 class="text-xl lg:text-2xl font-semibold text-yellow-800 mb-4">User Profile</h2>
        <div>
            <h3 class="text-lg font-medium text-yellow-700">Name</h3>
            <p class="text-sm text-yellow-700">{{ auth()->user()->name }}</p>
        </div>
        <div class="mt-4">
            <h3 class="text-lg font-medium text-yellow-700">Email</h3>
            <p class="text-sm text-yellow-700">{{ auth()->user()->email }}</p>
        </div>
        <div class="mt-4">
            <h3 class="text-lg font-medium text-yellow-700">Department</h3>
            <p class="text-sm text-yellow-700">{{ auth()->user()->department }}</p>
        </div>
    </div>

        <!-- Remaining Leaves Component -->
        <div class="bg-blue-200 shadow-md rounded-lg p-6">
        <h2 class="text-xl lg:text-2xl font-semibold text-blue-800 mb-4">Remaining Leaves</h2>

       
        <p class="text-gray-700"> </p>
       
       
    </div>

    
    </div>

<!-- Remaining Leaves Component -->
<div class="bg-blue-200 shadow-md rounded-lg p-6">
    <h2 class="text-xl lg:text-2xl font-semibold text-blue-800 mb-4">Remaining Leaves</h2>
    <table class="table table-responsive-sm table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Leave Type</th>
                <th>Total Allocated</th>
                <th>Allocated per month</th>
                <th>Leaves Taken</th>
                <th>Remaining Leaves</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($remainingLeaves as $type => $data)
                @php
                    $allocated = $data['Total Allocated'];
                    $taken = $data['Leaves Taken'];
                    $allocatedPerMonth = $data['Allocated per month'];
                    $remaining = $allocated - $taken; // Default calculation for remaining leaves

                    // Specific calculation for 'Short Leave'
                    if ($type === 'Short Leave') {
                        $remaining = $allocatedPerMonth - $taken;
                        $rowClass = $remaining < 0 ? 'text-danger' : 'text-blue-700';
                    } else {
                        $rowClass = $taken > $allocated ? 'text-danger' : 'text-blue-700';
                    }

                    // Special color for designated leave types
                    $blackLeaves = ['Duty Leave', 'Maternity/Paternity Leave', 'No Pay Leave', 'Paternity Leave', 'Study/Training Leave'];
                    if (in_array($type, $blackLeaves)) {
                        $rowClass = '.text-white'; // Override with black color for specific leave types
                    }

                    // Special color for designated leave types
                    $darkLeaves = ['Half Day'];
                    if (in_array($type, $darkLeaves)) {
                        $rowClass = 'text-dark'; // Override with dark color for specific leave types
                    }
                @endphp
                <tr class="{{ $rowClass }}" style="font-weight: bold;">
                    <td>{{ $type }}</td>
                    <td>{{ $allocated }}</td>
                    <td>{{ $allocatedPerMonth }}</td>
                    <td>{{ $taken }}</td>
                    <td>{{ max(0, $remaining) }}</td> <!-- Ensure that remaining never goes negative -->
                </tr>
            @endforeach
        </tbody>
    </table>
</div>




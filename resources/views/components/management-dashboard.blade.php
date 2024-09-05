<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <x-application-logo class="block h-12 w-auto" />

    <h1 class="mt-8 text-2xl font-medium text-gray-900">
        Welcome to the Human Resources Management System
    </h1>

    <p class="mt-6 text-gray-500 leading-relaxed">
        Welcome to your dedicated dashboard where you can oversee and manage leave requests across the organization. 
        As part of the management team, you play a pivotal role in ensuring effective resource allocation and maintaining a 
        productive workforce. Here, you can review and provide final approval for leave requests, ensuring that operations 
        continue smoothly while supporting employee well-being. Your leadership is instrumental in fostering a positive work 
        environment and driving organizational success. Should you have any inquiries or require assistance, please feel free to 
        reach out.
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
        @if ((auth()->user()->category == 'internship') || (auth()->user()->category == 'probation'))
            <table class="table table-responsive-sm table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Leave Type</th>
                        <th>Leaves Taken</th>
                        <th>Remaining Leaves</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $remainingLeaves['Leave Type'] }}</td>
                        <td>{{ $remainingLeaves['Leaves Taken'] }}</td>
                        <td class="{{ $remainingLeaves['Remaining Leaves'] == 0 ? 'text-danger' : '' }}">
                            {{ $remainingLeaves['Remaining Leaves'] }}
                        </td>
                        <td class="{{ $remainingLeaves['Status'] == 'No Pay' ? 'text-danger' : '' }}">
                            {{ $remainingLeaves['Status'] }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @elseif (auth()->user()->category == 'permanent')
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
                            $rowClass = $allocated <= $taken ? 'text-danger' : '';
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ $type }}</td>
                            <td>{{ $allocated }}</td>
                            <td>{{ $data['Allocated per month'] }}</td>
                            <td>{{ $taken }}</td>
                            <td>{{ $data['Remaining Leaves'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @elseif (auth()->user()->category == 'probation')
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
                            $rowClass = $allocated <= $taken ? 'text-danger' : '';
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ $type }}</td>
                            <td>{{ $allocated }}</td>
                            <td>{{ $data['Allocated per month'] }}</td>
                            <td>{{ $taken }}</td>
                            <td>{{ $data['Remaining Leaves'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>


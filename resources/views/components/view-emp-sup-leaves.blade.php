<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <x-application-logo class="block h-12 w-auto" />

    <h1 class="mt-8 text-2xl font-medium text-gray-900">
        View My Leaves
    </h1>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($leave as $data)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{$data->leave_type}}</h2>
                <div class="flex items-center mb-2">
                    <span class="text-gray-600 mr-2">Start Date:</span>
                    <span class="text-gray-800">{{$data->start_date}}</span>
                </div>
                <div class="flex items-center mb-2">
                    <span class="text-gray-600 mr-2">End Date:</span>
                    <span class="text-gray-800">{{$data->end_date}}</span>
                </div>
                <div class="flex items-center mb-2">
                    <span class="text-gray-600 mr-2">Reason:</span>
                    <span class="text-gray-800">{{$data->reason}}</span>
                </div>
                <div class="flex items-center mb-2">
                    <span class="text-gray-600 mr-2">Additional Notes:</span>
                    <span class="text-gray-800">{{$data->additional_notes}}</span>
                </div>

                <div class="flex items-center mb-2">
                    <span class="text-gray-600 mr-2">Supervisor in Chief Approval:</span>
                    <span class="@if($data->supervisor_in_chief_approval === 'Approved') text-green-600 @elseif($data->supervisor_in_chief_approval === 'Rejected') text-red-600 @else text-gray-600 @endif">{{$data->management_approval}}</span>
                </div>
                <div class="flex items-center mb-2">
                    <span class="text-gray-600 mr-2">Supervisor in Chief Comment:</span>
                    <span class="text-gray-800">{{$data->supervisor_in_chief_note}}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

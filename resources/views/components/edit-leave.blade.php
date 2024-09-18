<!-- resources/views/components/edit-leave.blade.php -->

<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <x-application-logo class="block h-12 w-auto" />

    <h1 class="mt-8 text-2xl font-medium text-gray-900">
        Update Leave Request
    </h1>
</div>

<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <x-validation-errors class="mb-4" />

                    <form method="POST" action="/updateLeave">
                        @csrf

                        <input type="hidden" name="id" value="{{ $data->id }}">

                        <div class="w-full">
                            <x-label for="leave_type" value="{{ __('Leave Type') }}" />
                            <select id="leave_type_select" name="leave_type" class="block mt-1 w-full">
                                <option value="" disabled selected>Select Leave Type</option>
                                <option value="Casual Leave" {{ $data->leave_type == 'Casual Leave' ? 'selected' : '' }}>Casual Leave</option>
                                <option value="Annual Leave" {{ $data->leave_type == 'Annual Leave' ? 'selected' : '' }}>Annual Leave</option>
                                <option value="Medical Leave" {{ $data->leave_type == 'Medical Leave' ? 'selected' : '' }}>Medical Leave</option>
                                <option value="Maternity/Paternity Leave" {{ $data->leave_type == 'Maternity/Paternity Leave' ? 'selected' : '' }}>Maternity/Paternity Leave</option>
                                <option value="Work On Leave" {{ $data->leave_type == 'Work On Leave' ? 'selected' : '' }}>Work On Leave</option>
                                <option value="No Pay Leave" {{ $data->leave_type == 'No Pay Leave' ? 'selected' : '' }}>No Pay Leave</option>
                                <option value="Short Leave" {{ $data->leave_type == 'Short Leave' ? 'selected' : '' }}>Short Leave</option>
                                <option value="Half Day" {{ $data->leave_type == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                                <option value="Duty Leave" {{ $data->leave_type == 'Duty Leave' ? 'selected' : '' }}>Duty Leave</option>
                                <option value="Other" {{ $data->leave_type == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Time Selection for Short Leave -->
                        <div class="mt-4" id="shortLeaveTime" style="display: none;">
                            <x-label for="short_leave_time" value="{{ __('Select Time Slot') }}" />
                            <select id="short_leave_time" name="short_leave_time" class="block mt-1 w-full">
                                <option value="" disabled selected>Select Time Slot</option>
                                <option value="8:45 AM - 10:00 AM">8:45 AM - 10:00 AM</option>
                                <option value="3:30 PM - 5:00 PM">3:30 PM - 5:00 PM</option>
                            </select>
                        </div>

                        <!-- Time Selection for Half Day -->
                        <div class="mt-4" id="halfDayTime" style="display: none;">
                            <x-label for="half_day_time" value="{{ __('Select Time Slot') }}" />
                            <select id="half_day_time" name="half_day_time" class="block mt-1 w-full">
                                <option value="" disabled selected>Select Time Slot</option>
                                <option value="8:30 AM - 12:30 PM">8:30 AM - 12:30 PM</option>
                                <option value="12:30 PM - 5:00 PM">12:30 PM - 5:00 PM</option>
                            </select>
                        </div>

                        <!-- Time Selection for Duty Leave -->
                        <div class="mt-4" id="dutyLeaveTime" style="display: none;">
                            <x-label for="duty_leave_time" value="{{ __('Select Time Slot') }}" />
                            <select id="duty_leave_time" name="duty_leave_time" class="block mt-1 w-full">
                                <option value="" disabled selected>Select Time Slot</option>
                                <option value="8:30 AM - 12:30 PM">8:30 AM - 12:30 PM</option>
                                <option value="8:30 AM - 5:00 PM">8:30 AM - 5:00 PM</option>
                            </select>
                        </div>

                        <!-- Input for Other Leave Type -->
                        <div class="mt-4" id="otherLeaveType" style="display: none;">
                            <x-label for="other_leave_type" value="{{ __('Other Leave Type') }}" />
                            <input type="text" id="other_leave_type" name="other_leave_type" class="block mt-1 w-full" placeholder="Enter Other Leave Type" value="{{ isset($data->other_leave_type) ? $data->other_leave_type : '' }}">
                        </div>

                        <div class="mt-4">
                            <x-label for="start_date" value="Start Date" />
                            <x-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" value="{{ $data->start_date }}" />
                        </div>

                        <div class="mt-4">
                            <x-label for="end_date" value="End Date" />
                            <x-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" value="{{ $data->end_date }}" />
                        </div>

                        <div class="mt-4">
                            <x-label for="reason" value="Reason" />
                            <textarea id="reason" name="reason" rows="4" cols="50" class="form-textarea mt-1 block w-full">{{ $data->reason }}</textarea>
                        </div>

                        <div class="mt-4">
                            <x-label for="additional_notes" value="Additional Notes" />
                            <textarea id="additional_notes" name="additional_notes" rows="4" cols="50" class="form-textarea mt-1 block w-full">{{ $data->additional_notes }}</textarea>
                        </div>

                        <div class="mt-4">
                            <x-label for="covering_person" value="{{ __('Covering Person') }}" />
                            <select id="covering_person" name="covering_person" class="block mt-1 w-full">
                                <option value="" disabled selected>Select Employee</option>
                                @foreach($users as $user)
                                    <option value="{{$user->id}}" {{ $data->covering_person == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Update Request') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add event listener to update the time slots dynamically
document.getElementById('leave_type_select').addEventListener('change', function() {
    var selectedValue = this.value;
    document.getElementById('shortLeaveTime').style.display = selectedValue === 'Short Leave' ? 'block' : 'none';
    document.getElementById('halfDayTime').style.display = selectedValue === 'Half Day' ? 'block' : 'none';
    document.getElementById('dutyLeaveTime').style.display = selectedValue === 'Duty Leave' ? 'block' : 'none';
    document.getElementById('otherLeaveType').style.display = selectedValue === 'Other' ? 'block' : 'none';
});
</script>

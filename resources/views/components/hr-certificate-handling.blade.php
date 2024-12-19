@props(['users'])

<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <x-application-logo class="block h-12 w-auto" />

    <h1 class="mt-8 text-2xl font-medium text-gray-900">
        Certificate Management
    </h1>
</div>

<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative">
                <!-- Loader -->
                <div id="loader-overlay" class="hidden absolute inset-0 bg-gray-100 bg-opacity-90 flex items-center justify-center z-50">
                    <div class="text-center">
                        <svg class="animate-spin h-12 w-12 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <p class="mt-4 text-lg font-medium text-blue-700">Submitting your request, please wait...</p>
                    </div>
                </div>

                <div class="p-6">
                    <x-validation-errors class="mb-4" />

                    <form action="{{ route('certificates.store') }}" method="POST" enctype="multipart/form-data" id="certificateForm">
                        @csrf

                        @if(session('msg'))
                            <div class="alert alert-success">{{ session('msg') }}</div>
                        @endif

                        <div class="w-full">
                            <x-label for="employee_id" value="{{ __('Employee Name') }}" />
                            <select id="employee_id" name="employee_id" class="block mt-1 w-full">
                                <option value="" disabled selected>Select Employee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <x-label for="certificate_type" value="{{ __('Certificate Type') }}" />
                            <select id="certificate_type" name="certificate_type" class="block mt-1 w-full">
                                <option value="" disabled selected>Select Certificate Type</option>
                                <option value="Expiration Based">Expiration Based</option>
                                <option value="Non-Expiration Based">Non-Expiration Based</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <x-label for="issued_date" value="{{ __('Issued Date') }}" />
                            <x-input id="issued_date" class="block mt-1 w-full" type="date" name="issued_date" />
                        </div>

                        <div class="mt-4">
                            <x-label for="expire_date" value="{{ __('Expire Date') }}" />
                            <x-input id="expire_date" class="block mt-1 w-full" type="date" name="expire_date" />
                        </div>

                        <div class="mt-4">
                            <x-label for="certificate_file" value="{{ __('Certificate File') }}" />
                            <x-input id="certificate_file" class="block mt-1 w-full" type="file" name="certificate_file" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button id="submitRequestButton" class="ml-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700" type="submit">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('certificateForm').addEventListener('submit', function(e) {
        // Show the loader overlay
        document.getElementById('loader-overlay').classList.remove('hidden');

        // Disable the submit button
        document.getElementById('submitRequestButton').disabled = true;
    });
</script>

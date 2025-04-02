<x-app-layout>
    <div class="flex">
        <x-user-sidebar />

        <div class="flex-1">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 bg-white rounded shadow">
                            <h2 class="text-xl font-semibold mb-4">Upload Employee Document</h2>

                            @if(session('success'))
                                <div class="bg-green-200 text-green-800 p-2 rounded">{{ session('success') }}</div>
                            @endif

                            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="hidden" name="employee_id" value="{{ auth()->user()->id }}">
                                <div>
                                    <label class="block text-sm font-medium">Document Type</label>
                                    <select name="document_type" required class="border p-2 rounded w-full">
                                        <option value="Birth Certificate">Birth Certificate</option>
                                        <option value="NIC">NIC</option>
                                        <option value="Passport">Passport</option>
                                        <option value="Driving License">Driving License</option>
                                        <option value="Educational Certificate">Educational Certificate</option>
                                        <option value="Professional Certificate">Professional Certificate</option>
                                        <option value="Police Clearance">Police Clearance Report</option>
                                        <option value="Passport Size Photo">Passport Size Photo</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">Document Files</label>
                                    <input type="file" name="document_files[]" multiple required class="border p-2 rounded w-full">
                                </div>
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload Documents</button>
                            </form>

                            <h2 class="text-xl font-semibold mt-6">Uploaded Documents</h2>
                            <table class="w-full mt-4 border-collapse border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="border p-2">Document Type</th>
                                        <th class="border p-2">File</th>
                                        <th class="border p-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $document)
                                        <tr>
                                            <td class="border p-2">{{ $document->document_type }}</td>
                                            <td class="border p-2">
                                                <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-blue-500">View</a>
                                            </td>
                                            <td class="border p-2">
                                                <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                                                </form>
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
    </div>
</x-app-layout>

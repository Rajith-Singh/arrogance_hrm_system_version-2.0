<x-app-layout>
    <!-- Success Modal -->
    @if(session('success'))
        <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Success</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ session('success') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a href="{{ route('users.index') }}" class="btn btn-primary">Back to Employees</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="container py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="d-flex justify-content-between mb-4">
                    <h1 class="text-2xl font-semibold text-gray-800">Edit User: {{ $user->name }}</h1>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to Employee</a>
                </div>

                <div class="card shadow-sm p-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.update', $user) }}">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ old('name', $user->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="emp_no" class="form-label">Employee Number</label>
                                        <input type="text" class="form-control" id="emp_no" name="emp_no" 
                                               value="{{ old('emp_no', $user->emp_no) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="department" class="form-label">Department</label>
                                        <select class="form-select" id="department" name="department" required>
                                            @foreach($departments as $key => $deptName)
                                                <option value="{{ $key }}" {{ old('department', $user->department) == $key ? 'selected' : '' }}>
                                                    {{ $deptName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="usertype" class="form-label">User Type</label>
                                        <select class="form-select" id="usertype" name="usertype" required>
                                            <option value="user" {{ old('usertype', $user->usertype) == 'user' ? 'selected' : '' }}>Normal User</option>
                                            <option value="supervisor" {{ old('usertype', $user->usertype) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                            <option value="supervisor-in-chief" {{ old('usertype', $user->usertype) == 'supervisor-in-chief' ? 'selected' : '' }}>Supervisor-in-Chief</option>
                                            <option value="admin" {{ old('usertype', $user->usertype) == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="hr" {{ old('usertype', $user->usertype) == 'hr' ? 'selected' : '' }}>HR</option>
                                        </select>
                                    </div>
                                </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="{{ old('phone', $user->phone) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}" {{ old('category', $user->category) == $category ? 'selected' : '' }}>{{ ucfirst($category) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="sub_category" class="form-label">Sub Category</label>
                                        <select class="form-select" id="sub_category" name="sub_category">
                                            <option value="">Select Sub Category</option>
                                            @foreach($subCategories as $subCat)
                                                <option value="{{ $subCat }}" {{ old('sub_category', $user->sub_category) == $subCat ? 'selected' : '' }}>
                                                    {{ $subCat ? ucfirst($subCat) : 'None' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="permanent_date" class="form-label">Permanent Date</label>
                                        <input type="date" class="form-control" id="permanent_date" name="permanent_date" 
                                               value="{{ old('permanent_date', $user->permanent_date ? \Carbon\Carbon::parse($user->permanent_date)->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="mb-3">
                                    <label for="joining_date" class="form-label">Joining Date</label>
                                    <input type="date" class="form-control" id="joining_date" name="joining_date" 
                                           value="{{ old('joining_date', $user->joining_date ? \Carbon\Carbon::parse($user->joining_date)->format('Y-m-d') : '') }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Show modal if success message exists
            @if(session('success'))
                $('#successModal').modal('show');
            @endif

            // Enable/disable sub_category based on category selection
            document.getElementById('category').addEventListener('change', function() {
                const subCategorySelect = document.getElementById('sub_category');
                if (this.value === 'permanent') {
                    subCategorySelect.disabled = false;
                } else {
                    subCategorySelect.disabled = true;
                    subCategorySelect.value = '';
                }
            });

            // Initialize the sub_category field state on page load
            document.addEventListener('DOMContentLoaded', function() {
                const categorySelect = document.getElementById('category');
                if (categorySelect.value !== 'permanent') {
                    document.getElementById('sub_category').disabled = true;
                }
            });
        </script>
    @endpush
</x-app-layout>

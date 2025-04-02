<x-app-layout>
    <div class="flex">
        <x-hr-sidebar />

        <div class="flex-1">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="container-fluid">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h2>User Management</h2>
                                </div>
                                <div class="col-md-6 text-end">
                                </div>
                            </div>
                        
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Employee No</th>
                                                    <th>Department</th>
                                                    <th>Category</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($users as $user)
                                                <tr>
                                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->emp_no }}</td>
                                                    <td>{{ $user->department }}</td>
                                                    <td>
                                                        @if($user->category)
                                                            {{ ucfirst($user->category) }}
                                                            @if($user->sub_category)
                                                                <span class="badge bg-info">{{ $user->sub_category }}</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">Not set</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($user->status == 1)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('users.edit', $user->id) }}" 
                                                           class="btn btn-sm btn-primary" 
                                                           title="Edit User">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                        
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $users->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast Notification -->
    @if(session('success'))
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        // Show toast notification if success message exists
        @if(session('success'))
            var toastEl = document.getElementById('successToast');
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        @endif
    </script>
    @endpush

    @push('styles')
    <style>
        .table th {
            white-space: nowrap;
        }
        .badge {
            font-size: 0.85em;
        }
    </style>
    @endpush
</x-app-layout>

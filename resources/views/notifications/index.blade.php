<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-2xl font-semibold text-gray-800">
                            {{ __('Your Notifications') }}
                        </div>
                        <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Mark all as read
                            </button>
                        </form>
                    </div>
                    <div class="mt-6">
                        <ul class="space-y-4">
                            @foreach ($notifications as $notification)
                                @php
                                    $leave = \App\Models\Leave::find($notification->leave_id);
                                @endphp
                                @if($leave)
                                    <li class="p-4 border rounded-lg @if(!$notification->read) bg-blue-50 border-blue-200 @else bg-white border-gray-200 @endif">
                                        <div class="flex justify-between items-center">
                                            <div class="text-sm text-gray-700 @if(!$notification->read) font-bold @endif">
                                                {{ $notification->message }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </div>
                                            @if(!$notification->read && !in_array($leave->leave_type, ['Short Leave']))
                                                @if(auth()->user()->id != $leave->user_id)
                                                    <div class="flex space-x-2 ml-4" id="actions-{{ $notification->id }}">
                                                        @if(auth()->user()->usertype === 'supervisor')
                                                            <a href="{{ url('/view-emp-leave/' . $notification->emp_id . '/' . $notification->leave_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                                View
                                                            </a>
                                                            <form method="POST" action="{{ route('notifications.supervisor.approve', $notification->id) }}">
                                                                @csrf
                                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-2 rounded approve-btn" data-id="{{ $notification->id }}">
                                                                    Approve
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="{{ route('notifications.supervisor.reject', $notification->id) }}">
                                                                @csrf
                                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded reject-btn" data-id="{{ $notification->id }}">
                                                                    Reject
                                                                </button>
                                                            </form>
                                                        @elseif(auth()->user()->usertype === 'management')
                                                            <a href="{{ url('/view-mgt-leave/' . $notification->emp_id . '/' . $notification->leave_id) }}" class="bg-gray-500 hover:bg-gray-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                                View
                                                            </a>
                                                            <form method="POST" action="{{ route('notifications.management.approve', $notification->id) }}">
                                                                @csrf
                                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-2 rounded approve-btn" data-id="{{ $notification->id }}">
                                                                    Approve
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="{{ route('notifications.management.reject', $notification->id) }}">
                                                                @csrf
                                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded reject-btn" data-id="{{ $notification->id }}">
                                                                    Reject
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const approveButtons = document.querySelectorAll('.approve-btn');
            const rejectButtons = document.querySelectorAll('.reject-btn');

            approveButtons.forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    const notificationId = this.getAttribute('data-id');
                    const form = this.closest('form');
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    }).then(response => response.json()).then(data => {
                        if (data.status === 'success') {
                            document.getElementById(`actions-${notificationId}`).style.display = 'none';
                            alert(data.message);

                            if (data.approval_status === 'Approved') {
                                // Send email to management
                                fetch('/send-management-email', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        leave_id: data.leave_id,
                                        supervisor_name: data.supervisor_name
                                    })
                                }).then(response => response.json()).then(emailData => {
                                    alert(emailData.message);
                                }).catch(error => console.error('Error sending management email:', error));
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    }).catch(error => console.error('Error:', error));
                });
            });

            rejectButtons.forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    const notificationId = this.getAttribute('data-id');
                    const form = this.closest('form');
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    }).then(response => response.json()).then(data => {
                        if (data.status === 'success') {
                            document.getElementById(`actions-${notificationId}`).style.display = 'none';
                            alert(data.message);
                        } else {
                            alert('Error: ' + data.message);
                        }
                    }).catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</x-app-layout>

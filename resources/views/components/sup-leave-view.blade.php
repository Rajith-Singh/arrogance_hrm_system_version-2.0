<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <x-application-logo class="block h-12 w-auto" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

    <style>
        .radio-container {
            position: relative;
            margin-right: 1rem;
        }

        .radio-container input {
            display: none;
        }

        .radio-checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 24px;
            width: 24px;
            background-color: #ccc;
            border-radius: 50%;
        }

        .radio-container:hover .radio-checkmark {
            background-color: #ddd;
        }

        .radio-container.pending input:checked + .radio-checkmark {
            background-color: #FFD700;
        }

        .radio-container.approved input:checked + .radio-checkmark {
            background-color: #008000;
        }

        .radio-container.rejected input:checked + .radio-checkmark {
            background-color: #FF0000;
        }

        .radio-container.pending input:checked + .radio-checkmark::before {
            content: '?';
            font-size: 1rem;
            color: black;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .radio-container.approved input:checked + .radio-checkmark::before {
            content: '\2713'; /* Unicode checkmark symbol */
            font-size: 1rem;
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .radio-container.rejected input:checked + .radio-checkmark::before {
            content: '\2718'; /* Unicode cross mark symbol */
            font-size: 1rem;
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .radio-label {
            margin-left: 2rem;
        }

        .details-container {
            margin-top: 2rem;
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .details-container div {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .details-container x-label, .details-container x-input, .details-container textarea {
            width: 100%;
            margin-top: 0.5rem;
        }

        .chart-container {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .chart-container canvas {
            max-width: 400px;
            max-height: 400px;
        }
    </style>

    <h1 class="mt-8 text-2xl font-medium text-gray-900">
        View Leave Request
    </h1>

    <div class="details-container">
        <div>
            <x-label for="emp_name" value="Employer's Name" />
            <x-input id="emp_name" type="text" name="emp_name" value="{{ $data->name }}" readonly />
        </div>

        <div>
            <x-label for="leave_type" value="Leave Type" />
            <x-input id="leave_type" type="text" name="leave_type" value="{{ $data->leave_type }}" readonly />
        </div>

        <div>
            <x-label for="start_date" value="Start Date" />
            <x-input id="start_date" type="text" name="start_date" value="{{ $data->start_date ? \Carbon\Carbon::parse($data->start_date)->format('d/m/Y') : '' }}" readonly />
        </div>

        <div>
            <x-label for="end_date" value="End Date" />
            <x-input id="end_date" type="text" name="end_date" value="{{ $data->end_date ? \Carbon\Carbon::parse($data->end_date)->format('d/m/Y') : '' }}" readonly />
        </div>

        <div class="col-span-2">
            <x-label for="reason" value="Reason" />
            <textarea id="reason" name="reason" rows="4" class="form-textarea" readonly>{{ $data->reason }}</textarea>
        </div>

        <div class="col-span-2">
            <x-label for="additional_notes" value="Additional Notes" />
            <textarea id="additional_notes" name="additional_notes" rows="4" class="form-textarea" readonly>{{ $data->additional_notes }}</textarea>
        </div>
    </div>

    <h2 class="text-2xl font-medium text-gray-900 mt-8">Supervisor Approval</h2>
    <hr>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mt-6">
        <form method="POST" action="/update-supervisor-approval">
            @csrf

            <input type="hidden" name="user_id" value="{{ $data->user_id }}" readonly>
            <input type="hidden" name="leave_id" value="{{ $data->id }}" readonly>

            <div class="flex items-center justify-between">
                <label class="radio-container pending">
                    <input type="radio" name="approval_status" value="Pending" checked>
                    <div class="radio-checkmark"></div>
                    <span class="radio-label">Pending</span>
                </label>

                <label class="radio-container approved">
                    <input type="radio" name="approval_status" value="Approved">
                    <div class="radio-checkmark"></div>
                    <span class="radio-label">Approved</span>
                </label>

                <label class="radio-container rejected">
                    <input type="radio" name="approval_status" value="Rejected">
                    <div class="radio-checkmark"></div>
                    <span class="radio-label">Rejected</span>
                </label>
            </div>

            <div class="mt-4">
                <x-label for="note" value="Note" />
                <textarea id="note" name="supervisor_note" rows="4" cols="50" class="form-textarea mt-1 block w-full"></textarea>
            </div>

            <div class="mt-4" hidden>
                <x-label for="supervisor" value="Supervisor" />
                <x-input id="supervisor" class="block mt-1 w-full" type="text" name="supervisor" value="{{ Auth::user()->name }}" readonly />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="ml-4">
                    {{ __('Submit') }}
                </x-button>
            </div>
        </form>
    </div>

    <!-- Leave History Chart -->
    <div class="chart-container">
        <h2 class="text-2xl font-medium text-gray-900 mt-8">Leave History</h2>
        <canvas id="leaveHistoryChart" width="400" height="400"></canvas>
        <script>
            const ctx = document.getElementById('leaveHistoryChart').getContext('2d');
            const leaveHistoryData = @json($leaveHistory);
            const leaveTypes = leaveHistoryData.map(item => item.leave_type);
            const leaveCounts = leaveHistoryData.map(item => item.total);

            const chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: leaveTypes,
                    datasets: [{
                        label: 'Number of Leaves',
                        data: leaveCounts,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Leave History'
                        }
                    }
                }
            });
        </script>
    </div>
</div>

<!-- resources/views/supervisor-in-chief/home.blade.php -->

<x-app-layout>
    <div class="flex">
        <!-- Include the supervisor sidebar -->
        <x-supervisor-in-chief-sidebar />

        <div class="flex-1">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <!-- Pass remainingLeaves to the supervisor-dashboard component -->
                        <x-supervisor-in-chief-dashboard :remainingLeaves="$remainingLeaves" />
                    </div>
                    
                    <div class="mt-8">
                        <!-- Call the leave-calendar component and pass the leaves data -->
                        <x-leave-calendar :leaves="$leaves" />
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

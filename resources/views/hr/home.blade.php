<!-- resources/views/hr/home.blade.php -->

<x-app-layout>
    <div class="flex">
        <!-- Include the hr sidebar -->
        <x-hr-sidebar />

        <div class="flex-1">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <!-- Pass remainingLeaves to the hr-dashboard component -->
                        <x-hr-dashboard :remainingLeaves="$remainingLeaves" />
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

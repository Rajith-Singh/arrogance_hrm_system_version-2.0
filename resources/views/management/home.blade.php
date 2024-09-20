<!-- resources/views/management/home.blade.php -->

<x-app-layout>
    <div class="flex">
        <!-- Include the management sidebar -->
        <x-management-sidebar />

        <div class="flex-1">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <!-- Pass remainingLeaves to the management-dashboard component -->
                        <x-management-dashboard  />

                        <div class="mt-8">
                            <!-- Call the leave-calendar component and pass the leaves data -->
                            <x-leave-calendar :leaves="$leaves" />
                        </div>
                        
                    </div>
                    


                </div>
            </div>
        </div>
    </div>
</x-app-layout>

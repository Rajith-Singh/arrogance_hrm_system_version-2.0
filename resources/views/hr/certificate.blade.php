<x-app-layout>
    <div class="flex">
        <x-hr-sidebar />

        <div class="flex-1">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <x-hr-certificate-handling :users="$users" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

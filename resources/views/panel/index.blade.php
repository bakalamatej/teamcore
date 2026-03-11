<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        {{-- Sidebar --}}
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow-xl rounded-lg">
                    <div class="max-w-xl">
                        @include('panel.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow-xl rounded-lg">
                    <div class="max-w-xl">
                        @include('panel.partials.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow-xl rounded-lg">
                    <div class="max-w-xl">
                        @include('panel.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </main>
    </div>        
</x-app-layout>
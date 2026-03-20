<x-panel-layout>
            <div class="space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow-xl rounded-lg">
                    <div class="max-w-xl">
                        @include('panel.update.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow-xl rounded-lg">
                    <div class="max-w-xl">
                        @include('panel.update.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow-xl rounded-lg">
                    <div class="max-w-xl">
                        @include('panel.update.delete-user-form')
                    </div>
                </div>
            </div>
        </main>    
</x-panel-layout>
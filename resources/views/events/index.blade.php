@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        <div class="hidden xl:block">
            @include('events.sidebar')
        </div>

        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="my-heading text-2xl">{{ __('Events') }}</h1>
                    <span class="text-sm text-gray-600">{{ $events->total() }} {{ __('events total') }}</span>
                </div>

                <div id="results">
                    @include('events._table', compact('events', 'userClubIds', 'userEventIds'))
                </div>
            </div>
        </main>    
    </div>    
</x-app-layout>
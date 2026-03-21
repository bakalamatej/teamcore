@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('Event Types Management') }}</h1>
            <span class="text-sm text-gray-600">{{ $eventTypes->total() }} {{ __('event types total') }}</span>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('panel.admin.event-types.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <div class="flex-1">
                    <x-input-label :value="__('Search')" />
                    <x-text-input
                        id="search"
                        type="text"
                        name="search"
                        placeholder="{{ __('Event type name...') }}"
                        class="mt-1 block w-full text-sm"
                        :value="request('search')"
                    />
                </div>

                <div class="flex-1">
                    <x-input-label :value="__('Sport')" />
                    <x-select-input
                        id="sport_id"
                        name="sport_id"
                        :options="$sportOptions"
                        :selected="request('sport_id')"
                        placeholder="{{ __('Select sport') }}"
                        class="mt-1 block w-full text-sm"
                    />
                </div>

                <div class="flex items-end justify-end">
                    <x-add-button href="{{ route('panel.admin.event-types.create') }}" class="!h-9" />
                </div>
            </form>
        </div>

        <div id="results">
            @include('panel.admin.event-types._table', compact('eventTypes'))
        </div>
    </div>
</x-panel-layout>

@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('My Results') }}</h1>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('panel.results.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <div class="flex-1">
                    <x-input-label :value="__('Club')" />
                    <x-select-input
                        id="member_club_id"
                        name="member_club_id"
                        :options="$clubOptions"
                        :selected="$selectedMemberClubId"
                        placeholder="{{ __('All clubs') }}"
                        class="mt-1 block w-full text-sm"
                    />
                </div>
                <div class="flex-1">
                    <x-input-label :value="__('Event Type')" />
                    <x-select-input
                        id="event_type_id"
                        name="event_type_id"
                        :options="$eventTypeOptions"
                        :selected="$selectedEventTypeId"
                        placeholder="{{ __('All event types') }}"
                        class="mt-1 block w-full text-sm"
                    />
                </div>
            </form>
        </div>

        <div id="results">
            @include('panel.results._table', compact('results'))
        </div>
    </div>
</x-panel-layout>
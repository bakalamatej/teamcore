@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-app-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('Events') }}</h1>
            <span class="text-sm text-gray-600">{{ $events->total() }} {{ __('events total') }}</span>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('events.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[180px]">
                    <x-input-label :value="__('Search')" />
                    <x-text-input
                        id="search"
                        type="text"
                        name="search"
                        placeholder="{{ __('Search...') }}"
                        class="mt-1 block w-full text-sm"
                        :value="request('search')"
                    />
                </div>

                <div>
                    <x-input-label :value="__('Location')" />
                    <x-select-input
                        id="sport_field_id"
                        name="sport_field_id"
                        :options="$sportFieldOptions"
                        :selected="request('sport_field_id')"
                        placeholder="{{ __('Select location') }}"
                        class="mt-1 block max-w-[220px] text-sm"
                    />
                </div>

                <div>
                    <x-input-label :value="__('Status')" />
                    <x-select-input
                        id="status"
                        name="status"
                        :options="$statusOptions"
                        :selected="request('status')"
                        placeholder="{{ __('Select status') }}"
                        class="mt-1 block max-w-[220px] text-sm"
                    />
                </div>

                <div>
                    <x-input-label :value="__('Type')" />
                    <x-select-input
                        id="type"
                        name="type"
                        :options="$eventTypeOptions"
                        :selected="request('type')"
                        placeholder="{{ __('All types') }}"
                        class="mt-1 block max-w-[220px] text-sm"
                    />
                </div>

                <div>
                    <x-input-label :value="__('Date from')" />
                    <x-text-input
                        id="start_date_from"
                        type="date"
                        name="start_date_from"
                        class="mt-1 block w-[160px] text-sm"
                        :value="request('start_date_from')"
                    />
                </div>

                <div>
                    <x-input-label :value="__('Date to')" />
                    <x-text-input
                        id="start_date_to"
                        type="date"
                        name="start_date_to"
                        class="mt-1 block w-[160px] text-sm"
                        :value="request('start_date_to')"
                    />
                </div>
            </form>
        </div>

        <div id="results">
            @include('events._table', compact('events', 'userHasMember'))
        </div>
    </div>
</x-app-layout>
@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('Tournament Management') }}</h1>
            <span class="text-sm text-gray-600">{{ $tournaments->total() }} {{ __('tournaments total') }}</span>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('panel.coach.tournaments.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <div class="flex-1">
                    <x-input-label :value="__('Search')" />
                    <x-text-input id="search" type="text" name="search" placeholder="{{ __('Title...') }}" class="mt-1 block w-full text-sm" :value="request('search')" />
                </div>
                <div class="flex-1">
                    <x-input-label :value="__('Sport Field')" />
                    <x-select-input id="sport_field_id" name="sport_field_id" :options="$sportFieldOptions" :selected="request('sport_field_id')" placeholder="{{ __('Select sport field') }}" class="mt-1 block w-full text-sm" />
                </div>
                <div class="flex-1">
                    <x-input-label :value="__('Status')" />
                    <x-select-input id="status" name="status" :options="$statusOptions" :selected="request('status')" placeholder="{{ __('Select status') }}" class="mt-1 block w-full text-sm" />
                </div>
                <div class="flex items-end justify-end">
                    <x-add-button href="{{ route('panel.coach.tournaments.create') }}" class="!h-9" />
                </div>
            </form>
        </div>

        <div id="results">
            @include('panel.coach.tournaments._table', compact('tournaments'))
        </div>
    </div>
</x-panel-layout>
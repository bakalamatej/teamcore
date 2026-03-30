@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('Clubs Management') }}</h1>
            <span class="text-sm text-gray-600">{{ $clubs->total() }} {{ __('clubs total') }}</span>
        </div>
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('panel.coach.clubs.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <div class="flex-1">
                    <x-input-label :value="__('Search')" />
                    <x-text-input
                        id="search"
                        type="text"
                        name="search"
                        placeholder="{{ __('Name or email...') }}"
                        class="mt-1 block w-full text-sm"
                        :value="request('search')"
                    />
                </div>

                <div class="flex-1">
                    <x-input-label :value="__('City')" />
                    <x-select-input
                        id="city"
                        name="city"
                        :options="$cityOptions"
                        :selected="request('city')"
                        placeholder="{{ __('Select city') }}"
                        class="mt-1 block w-full text-sm"
                    />
                </div>

            </form>
        </div>

        <div id="results">
            @include('panel.coach.clubs._table', ['clubs' => $clubs, 'myClub' => $myClub])
        </div>
    </div>
</x-panel-layout>

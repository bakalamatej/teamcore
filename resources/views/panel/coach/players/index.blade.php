@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('Players') }}</h1>
            <span class="text-sm text-gray-600">{{ $players->total() }} {{ __('players total') }}</span>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('panel.coach.players.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <div class="flex-1">
                    <x-input-label :value="__('Search')" />
                    <x-text-input
                        id="search"
                        type="text"
                        name="search"
                        placeholder="{{ __('Name...') }}"
                        class="mt-1 block w-full"
                        :value="request('search')"
                    />
                </div>
                <div class="flex-1">
                    <x-input-label :value="__('Role')" />
                    <x-select-input
                        id="role"
                        name="role"
                        :options="$roleOptions"
                        :selected="request('role')"
                        placeholder="{{ __('All roles') }}"
                        class="mt-1 block w-full"
                    />
                </div>
            </form>
        </div>

        <div id="results">
            @include('panel.coach.players._table', compact('players'))
        </div>
    </div>
</x-panel-layout>

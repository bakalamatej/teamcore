@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('Coach Evaluations') }}</h1>
            <span class="text-sm text-gray-600">{{ $members->total() }} {{ __('coaches total') }}</span>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('panel.admin.coach-evaluations.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <div class="flex-1">
                    <x-input-label :value="__('Search')" />
                    <x-text-input
                        id="search"
                        type="text"
                        name="search"
                        placeholder="{{ __('Coach name...') }}"
                        class="mt-1 block w-full text-sm"
                        :value="request('search')"
                    />
                </div>
            </form>
        </div>

        <div id="results">
            @include('panel.admin.coach-evaluations._table', compact('members'))
        </div>
    </div>
</x-panel-layout>

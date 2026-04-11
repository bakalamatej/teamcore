@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('My Evaluations') }}</h1>
            <span class="text-sm text-gray-600">
                {{ $evaluations->total()? $evaluations->total() . ' ' . __('evaluations') : __('No evaluations found') }}
            </span>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('panel.my-evaluations.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <div class="flex-1">
                    <x-input-label :value="__('Search')" />
                    <x-text-input
                        id="search"
                        type="text"
                        name="search"
                        placeholder="{{ __('Name...') }}"
                        class="mt-1 block w-full text-sm"
                        :value="request('search')"
                    />
                </div>
            </form>
        </div>

        <div id="results">
            @include('panel.my-evaluations._table', compact('evaluations'))
        </div>
    </div>
</x-panel-layout>
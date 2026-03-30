@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('My Files') }}</h1>
            <span class="text-sm text-gray-600">{{ $files->total() }} {{ __('files total') }}</span>
        </div>

        {{-- Tabs --}}
        <div class="flex border-b border-gray-200 mb-6">
            <a href="{{ route('panel.files.index', ['tab' => 'my'] + request()->except('tab', 'page')) }}"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px {{ $tab === 'my' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ __('Files Shared With Me') }}
            </a>
            <a href="{{ route('panel.files.index', ['tab' => 'uploaded'] + request()->except('tab', 'page')) }}"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px {{ $tab === 'uploaded' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ __('Files I Uploaded') }}
            </a>
        </div>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('panel.files.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="flex-1">
                    <x-input-label :value="__('Search')" />
                    <x-text-input id="search" type="text" name="search" placeholder="{{ __('File name...') }}" class="mt-1 block w-full text-sm" :value="request('search')" />
                </div>
                <div class="flex-1">
                    <x-input-label :value="__('Category')" />
                    <x-select-input id="category_id" name="category_id" :options="$categoryOptions" :selected="request('category_id')" placeholder="{{ __('All categories') }}" class="mt-1 block w-full text-sm" />
                </div>
            </form>
        </div>

        <div id="results">
            @include('panel.files._table', compact('files', 'tab'))
        </div>
    </div>
</x-panel-layout>
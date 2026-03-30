@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('File Management') }}</h1>
            <span class="text-sm text-gray-600">{{ $files->total() }} {{ __('files total') }}</span>
        </div>

        {{-- Tabs --}}
        <div class="flex border-b border-gray-200 mb-6">
            <a href="{{ route('panel.admin.files.index', ['tab' => 'events'] + request()->except('tab', 'page')) }}"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px {{ $tab === 'events' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ __('Event Files') }}
            </a>
            <a href="{{ route('panel.admin.files.index', ['tab' => 'clubs'] + request()->except('tab', 'page')) }}"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px {{ $tab === 'clubs' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ __('Club Files') }}
            </a>
            <a href="{{ route('panel.admin.files.index', ['tab' => 'members'] + request()->except('tab', 'page')) }}"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px {{ $tab === 'members' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ __('Member Files') }}
            </a>
        </div>

        {{-- Filters --}}
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('panel.admin.files.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="flex-1">
                    <x-input-label :value="__('Search')" />
                    <x-text-input id="search" type="text" name="search" placeholder="{{ __('File name...') }}" class="mt-1 block w-full text-sm" :value="request('search')" />
                </div>
                <div class="flex-1">
                    <x-input-label :value="__('Category')" />
                    <x-select-input id="category_id" name="category_id" :options="$categoryOptions" :selected="request('category_id')" placeholder="{{ __('All categories') }}" class="mt-1 block w-full text-sm" />
                </div>
                @if($tab === 'clubs')
                    <div class="flex-1">
                        <x-input-label :value="__('Club')" />
                        <x-select-input id="club_id" name="club_id" :options="$clubOptions" :selected="request('club_id')" placeholder="{{ __('All clubs') }}" class="mt-1 block w-full text-sm" />
                    </div>
                @elseif($tab === 'members')
                    <div class="flex-1">
                        <x-input-label :value="__('Member')" />
                        <x-select-input id="member_club_id" name="member_club_id" :options="$memberOptions" :selected="request('member_club_id')" placeholder="{{ __('All members') }}" class="mt-1 block w-full text-sm" />
                    </div>
                @else
                    <div class="flex-1">
                        <x-input-label :value="__('Event')" />
                        <x-select-input id="event_id" name="event_id" :options="$eventOptions" :selected="request('event_id')" placeholder="{{ __('All events') }}" class="mt-1 block w-full text-sm" />
                    </div>
                @endif
            </form>
        </div>

        <div id="results">
            @include('panel.admin.files._table', compact('files', 'tab'))
        </div>
    </div>
</x-panel-layout>
@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="my-heading text-2xl">{{ __('Membership Management') }}</h1>
                    <span class="text-sm text-gray-600">{{ $memberships->total() }} {{ __('memberships total') }}</span>
                </div>

                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <form id="filter-form" method="GET" action="{{ route('panel.memberships.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                        <div class="flex-1">
                            <x-input-label :value="__('Search')" />
                            <x-text-input
                                id="search"
                                type="text"
                                name="search"
                                placeholder="{{ __('Member name...') }}"
                                class="mt-1 block w-full"
                                :value="request('search')"
                            />
                        </div>

                        <div class="flex-1">
                            <x-input-label :value="__('Role')" />
                            <x-select-input
                                id="role"
                                name="role"
                                :options="['player' => __('Player'), 'coach' => __('Coach')]"
                                :selected="request('role')"
                                placeholder="{{ __('All roles') }}"
                                class="mt-1 block w-full"
                            />
                        </div>

                        <div class="flex-1">
                            <x-input-label :value="__('Club')" />
                            <x-select-input
                                id="club_id"
                                name="club_id"
                                :options="$clubOptions"
                                :selected="request('club_id')"
                                placeholder="{{ __('All clubs') }}"
                                class="mt-1 block w-full"
                            />
                        </div>

                        <div class="flex items-end justify-end">
                            <x-add-button href="{{ route('panel.memberships.create') }}" class="!h-9" />
                        </div>
                    </form>
                </div>

                <div id="results">
                    @include('panel.memberships._table', compact('memberships'))
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

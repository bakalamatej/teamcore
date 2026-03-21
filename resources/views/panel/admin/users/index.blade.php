@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
            <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="my-heading text-2xl">{{ __('Users Management') }}</h1>
                    <span class="text-sm text-gray-600">{{ $users->total() }} {{ __('users total') }}</span>
                </div>

                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <form id="filter-form" method="GET" action="{{ route('panel.admin.users.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
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
                            <x-input-label :value="__('Admin')" />
                            <x-select-input
                                id="is_admin"
                                name="is_admin"
                                :options="['1' => __('Admin'), '0' => __('Non-admin')]"
                                :selected="request('is_admin')"
                                placeholder="{{ __('All users') }}"
                                class="mt-1 block w-full"
                            />
                        </div>
                    </form>
                </div>

                <div id="results">
                    @include('panel.admin.users._table', compact('users'))
                </div>
            </div>
        </main>
    </div>
</x-panel-layout>

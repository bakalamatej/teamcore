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
                    <h1 class="my-heading text-2xl">{{ __('Sport Fields Management') }}</h1>
                    <span class="text-sm text-gray-600">{{ $sportFields->total() }} {{ __('sport fields total') }}</span>
                </div>

                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <form id="filter-form" method="GET" action="{{ route('panel.sport-fields.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                        <div class="flex-1">
                            <x-input-label :value="__('Search')" />
                            <x-text-input
                                id="search"
                                type="text"
                                name="search"
                                placeholder="{{ __('Sport field name...') }}"
                                class="mt-1 block w-full text-sm"
                                :value="request('search')"
                            />
                        </div>

                        <div class="flex-1">
                            <x-input-label :value="__('Location')" />
                            <x-select-input
                                id="location"
                                name="location"
                                :options="$cityOptions"
                                :selected="request('location')"
                                placeholder="{{ __('Select city') }}"
                                class="mt-1 block w-full text-sm"
                            />
                        </div>

                        <div class="flex-1">
                            <x-input-label :value="__('Field Type')" />
                            <x-select-input
                                id="field_type"
                                name="field_type"
                                :options="$fieldTypeOptions"
                                :selected="request('field_type')"
                                placeholder="{{ __('Select field type') }}"
                                class="mt-1 block w-full text-sm"
                            />
                        </div>

                        <div class="flex items-end justify-end">
                            <x-add-button href="{{ route('panel.sport-fields.create') }}" class="!h-9" />
                        </div>
                    </form>
                </div>

                <div id="results">
                    @include('panel.sport-fields._table', compact('sportFields'))
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

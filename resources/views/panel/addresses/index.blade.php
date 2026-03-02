<!-- Load search JS for real-time filtering -->
@push('scripts')
    @vite(['resources/js/shared/table-search.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-screen">
        <!-- Admin panel sidebar navigation -->
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        <!-- Main content: addresses list with search/filter -->
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="my-heading text-2xl">{{ __('Addresses Management') }}</h1>
                    <span class="text-sm text-gray-600">{{ $addresses->total() }} {{ __('addresses total') }}</span>
                </div>

                <!-- Search section -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <form method="GET" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <x-input-label :value="__('Search')" />
                            <x-text-input
                                id="search"
                                type="text"
                                name="search"
                                placeholder="{{ __('City or street...') }}"
                                class="mt-1 block w-full text-sm"
                                :value="request('search')"
                            />
                        </div>

                        <div class="flex-1">
                            <x-input-label :value="__('Country')" />
                            <x-select-input
                                id="country"
                                name="country"
                                :options="$countries->mapWithKeys(fn($c) => [$c => $c])->toArray()"
                                :selected="request('country')"
                                placeholder="{{ __('Select country') }}"
                                class="mt-1 block w-full text-sm"
                            />
                        </div>

                        <div class="flex-1">
                            <x-input-label :value="__('City')" />
                            <x-select-input
                                id="city"
                                name="city"
                                :options="$cities->mapWithKeys(fn($c) => [$c => $c])->toArray()"
                                :selected="request('city')"
                                placeholder="{{ __('Select city') }}"
                                class="mt-1 block w-full text-sm"
                            />
                        </div>

                        <!-- Submit button aligned with inputs -->
                        <div class="flex items-end justify-end gap-3">
                            <x-primary-button type="submit" class="!h-9">
                                {{ __('Filter') }}
                            </x-primary-button>
                            
                            <x-add-button href="{{ route('panel.addresses.create') }}" class="!h-9" />
                        </div>
                    </form>
                </div>

                <!-- Addresses Table -->
                <div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
                    <table class="w-full data-table">
                        <thead class="bg-gray-100">
                            <tr class="border-b">
                                <th class="p-3 text-left">{{ __('City') }}</th>
                                <th class="p-3 text-left">{{ __('Street') }}</th>
                                <th class="p-3 text-left">{{ __('Country') }}</th>
                                <th class="p-3 text-left">{{ __('Zip Code') }}</th>
                                <th class="p-3 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($addresses as $address)
                                <tr class="border-b hover:bg-gray-50 data-row">
                                    <td class="p-3 font-medium">{{ $address->city }}</td>
                                    <td class="p-3 text-sm text-gray-600">{{ $address->street ?? '-' }}</td>
                                    <td class="p-3 text-sm text-gray-600">{{ $address->country ?? '-' }}</td>
                                    <td class="p-3 text-sm text-gray-600">{{ $address->zip_code ?? '-' }}</td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('panel.addresses.edit', $address) }}" class="table-action edit mr-2">
                                            {{ __('Edit') }}
                                        </a>

                                        <button type="button" class="table-action delete"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'confirm-address-deletion-{{ $address->id }}')">
                                            {{ __('Delete') }}
                                        </button>

                                        <x-modal name="confirm-address-deletion-{{ $address->id }}" :show="false" focusable>
                                            <form method="POST" action="{{ route('panel.addresses.destroy', $address) }}" class="p-6 text-left">
                                                @csrf
                                                @method('DELETE')

                                                <h2 class="my-heading">{{ __('Delete Address') }}</h2>
                                                <p class="my-text">
                                                    {{ __('Are you sure you want to delete') }} <strong>{{ $address->city }}, {{ $address->street }}</strong>?
                                                    {{ __('This action cannot be undone.') }}
                                                </p>

                                                <div class="flex justify-end gap-3 mt-6">
                                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                                        {{ __('Cancel') }}
                                                    </x-secondary-button>

                                                    <x-danger-button type="submit">
                                                        {{ __('Delete Address') }}
                                                    </x-danger-button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">
                                        {{ __('No addresses found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $addresses->links() }}
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

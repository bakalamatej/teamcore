<!-- Load club search JS for real-time filtering -->
@push('scripts')
    @vite(['resources/js/clubs/club-search.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-screen">
        <!-- Admin panel sidebar navigation -->
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        <!-- Main content: clubs list with search/filter -->
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="my-heading text-2xl">{{ __('Clubs Management') }}</h1>
                    <span class="text-sm text-gray-600">{{ $clubs->total() }} {{ __('clubs total') }}</span>
                </div>

                <!-- Search & city filter section -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <form method="GET" class="flex flex-col sm:flex-row gap-4">
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

                        <!-- Submit button aligned with inputs -->
                        <div class="flex items-end">
                            <x-primary-button type="submit" class="mt-7">
                                {{ __('Filter') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                <!-- Clubs Table -->
                <div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
                    <table class="w-full data-table">
                        <thead class="bg-gray-100">
                            <tr class="border-b">
                                <th class="p-3 text-left">{{ __('Name') }}</th>
                                <th class="p-3 text-left">{{ __('Email') }}</th>
                                <th class="p-3 text-left">{{ __('City') }}</th>
                                <th class="p-3 text-left">{{ __('Phone') }}</th>
                                <th class="p-3 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clubs as $club)
                                <tr class="border-b hover:bg-gray-50 data-row"
                                    data-name="{{ strtolower($club->name) }}"
                                    data-city="{{ strtolower($club->address->city ?? '') }}">
                                    <td class="p-3 font-medium">{{ $club->name }}</td>
                                    <td class="p-3 text-sm text-gray-600">{{ $club->email ?? '-' }}</td>
                                    <td class="p-3 text-sm text-gray-600">{{ $club->address->city ?? '-' }}</td>
                                    <td class="p-3 text-sm text-gray-600">{{ $club->phone ?? '-' }}</td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('clubs.show', $club) }}" class="table-action view mr-2">
                                            {{ __('View') }}
                                        </a>

                                        <a href="{{ route('clubs.edit', $club) }}" class="table-action edit mr-2">
                                            {{ __('Edit') }}
                                        </a>

                                        <button type="button" class="table-action delete"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'confirm-club-deletion-{{ $club->id }}')">
                                            {{ __('Delete') }}
                                        </button>

                                        <x-modal name="confirm-club-deletion-{{ $club->id }}" :show="false" focusable>
                                            <form method="POST" action="{{ route('clubs.destroy', $club) }}" class="p-6 text-left">
                                                @csrf
                                                @method('DELETE')

                                                <h2 class="my-heading">{{ __('Delete Club') }}</h2>
                                                <p class="my-text">
                                                    {{ __('Are you sure you want to delete') }} <strong>{{ $club->name }}</strong>?
                                                    {{ __('This action cannot be undone.') }}
                                                </p>

                                                <div class="flex justify-end gap-3 mt-6">
                                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                                        {{ __('Cancel') }}
                                                    </x-secondary-button>

                                                    <x-danger-button type="submit">
                                                        {{ __('Delete Club') }}
                                                    </x-danger-button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">
                                        {{ __('No clubs found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $clubs->links() }}
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

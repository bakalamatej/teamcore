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

        <!-- Main content: sports list with search/filter -->
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="my-heading text-2xl">{{ __('Sports Management') }}</h1>
                    <span class="text-sm text-gray-600">{{ $sports->total() }} {{ __('sports total') }}</span>
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
                                placeholder="{{ __('Sport name...') }}"
                                class="mt-1 block w-full text-sm"
                                :value="request('search')"
                            />
                        </div>

                        <!-- Submit button aligned with inputs -->
                        <div class="flex items-end justify-end gap-3">
                            <x-primary-button type="submit" class="!h-9">
                                {{ __('Filter') }}
                            </x-primary-button>
                            
                            <x-add-button href="{{ route('panel.sports.create') }}" class="!h-9" />
                        </div>
                    </form>
                </div>

                <!-- Sports Table -->
                <div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
                    <table class="w-full data-table">
                        <thead class="bg-gray-100">
                            <tr class="border-b">
                                <th class="p-3 text-left">{{ __('Name') }}</th>
                                <th class="p-3 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sports as $sport)
                                <tr class="border-b hover:bg-gray-50 data-row">
                                    <td class="p-3 font-medium">{{ $sport->name }}</td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('panel.sports.edit', $sport) }}" class="table-action edit mr-2">
                                            {{ __('Edit') }}
                                        </a>

                                        <button type="button" class="table-action delete"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'confirm-sport-deletion-{{ $sport->id }}')">
                                            {{ __('Delete') }}
                                        </button>

                                        <x-modal name="confirm-sport-deletion-{{ $sport->id }}" :show="false" focusable>
                                            <form method="POST" action="{{ route('panel.sports.destroy', $sport) }}" class="p-6 text-left">
                                                @csrf
                                                @method('DELETE')

                                                <h2 class="my-heading">{{ __('Delete Sport') }}</h2>
                                                <p class="my-text">
                                                    {{ __('Are you sure you want to delete') }} <strong>{{ $sport->name }}</strong>?
                                                    {{ __('This action cannot be undone.') }}
                                                </p>

                                                <div class="flex justify-end gap-3 mt-6">
                                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                                        {{ __('Cancel') }}
                                                    </x-secondary-button>

                                                    <x-danger-button type="submit">
                                                        {{ __('Delete Sport') }}
                                                    </x-danger-button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="p-4 text-center text-gray-500">
                                        {{ __('No sports found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $sports->links() }}
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

@push('scripts')
    @vite(['resources/js/clubs/club-search.js'])
@endpush

<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <x-text-input
                id="search"
                type="text"
                name="search"
                placeholder="Search..."
                class="w-1/3"
            />
        </div>

        <div class="border border-gray-300 rounded-md overflow-hidden mt-6 shadow-md">
            <table class="w-full data-table">
                <thead class="bg-gray-100">
                    <tr class="border-b">
                        <th class="p-3 text-left">{{ __('Name') }}</th>
                        <th class="p-3 text-left">{{ __('City') }}</th>
                        <th class="p-3 text-left">{{ __('Webpage') }}</th>
                        <th class="p-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clubs as $club)
                        <tr class="data-row"
                            data-name="{{ strtolower($club->name) }}"
                            data-city="{{ strtolower($club->address->city ?? '') }}">
                            <td>{{ $club->name }}</td>
                            <td>{{ $club->address->city ?? '-' }}</td>
                            <td>
                                @if($club->webpage)
                                    <a href="{{ $club->webpage }}" target="_blank" class="table-action">
                                        {{ $club->webpage }}
                                    </a>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('clubs.show', $club) }}" class="table-action view">{{ __('View') }}</a>

                                @auth
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('clubs.edit', $club) }}" class="table-action edit">{{ __('Edit') }}</a>

                                        <button type="button" class="table-action delete"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'confirm-club-deletion-{{ $club->id }}')">
                                            {{ __('Delete') }}
                                        </button>

                                        <x-modal name="confirm-club-deletion-{{ $club->id }}" :show="false" focusable>
                                            <form method="POST" action="{{ route('clubs.destroy', $club) }}" class="p-6 text-left">
                                                @csrf
                                                @method('DELETE')

                                                <h2 class="my-heading">
                                                    {{ __('Are you sure you want to delete this club?') }}
                                                </h2>

                                                <p class="my-text">
                                                    {{ __('Once deleted, this club cannot be recovered.') }}
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
                                    @endif
                                @endauth
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
            <div class="mt-4">
                {{ $clubs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    @vite(['resources/js/club-search.js'])
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

            @auth
                @if(auth()->user()->role === 'admin')
                    <x-primary-button :href="route('clubs.create')">
                        {{ __('Add Club') }}
                    </x-primary-button>
                @endif
            @endauth
        </div>

        <div class="border border-gray-300 rounded-md overflow-hidden mt-6 shadow-md">
            <table class="w-full clubs-table">
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
                        <tr class="club-row" 
                            data-name="{{ strtolower($club->name) }}" 
                            data-city="{{ strtolower($club->address->city ?? '') }}">
                            <td class="p-3">{{ $club->name }}</td>
                            <td class="p-3">{{ $club->address->city ?? '-' }}</td>
                            <td class="p-3">
                                @if($club->webpage)
                                    <a href="{{ $club->webpage }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $club->webpage }}
                                    </a>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('clubs.show', $club) }}" class="text-blue-600 hover:underline mr-2">
                                    {{ __('View') }}
                                </a>

                                @auth
                                    @if(auth()->user()->role === 'admin')
                                        <a href="{{ route('clubs.edit', $club) }}" class="text-yellow-600 hover:underline mr-2">
                                            {{ __('Edit') }}
                                        </a>

                                        <form action="{{ route('clubs.destroy', $club) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline" 
                                                    onclick="return confirm('{{ __('Delete club?') }}')">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $clubs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

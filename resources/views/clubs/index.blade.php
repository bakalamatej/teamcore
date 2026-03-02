<!-- Load club search JS for real-time filtering -->
@push('scripts')
    @vite(['resources/js/shared/table-search.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-screen">
        <!-- Filter sidebar: search by name/city (hidden on mobile) -->
        <div class="hidden xl:block">
            @include('clubs.sidebar')
        </div>

        <!-- Main content area with clubs table -->
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <!-- Header: title + club count -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="my-heading text-2xl">{{ __('Clubs') }}</h1>
                    <span class="text-sm text-gray-600">{{ $clubs->total() }} {{ __('clubs total') }}</span>
                </div>

                <!-- Clubs table with search functionality -->
                <div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
                    <table class="w-full data-table">
                        <thead class="bg-gray-100">
                            <tr class="border-b">
                                <th class="p-3 text-left">{{ __('Name') }}</th>
                                <th class="p-3 text-left">{{ __('Email') }}</th>
                                <th class="p-3 text-left">{{ __('City') }}</th>
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
                                    <td class="p-3 text-right">
                                        <a href="{{ route('clubs.show', $club) }}" class="table-action view mr-2">{{ __('View') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500">
                                        {{ __('No clubs found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                    <div class="mt-4" >
                        {{ $clubs->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>        
</x-app-layout>

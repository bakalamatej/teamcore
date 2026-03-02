<!-- Load event search JS for real-time filtering -->
@push('scripts')
    @vite(['resources/js/shared/table-search.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-screen">
        <!-- Filter sidebar (visible only on desktop) -->
        <div class="hidden xl:block">
            @include('events.sidebar')
        </div>

        <!-- Main content area with events table -->
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <!-- Header: title + event count -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="my-heading text-2xl">{{ __('Events') }}</h1>
                    <span class="text-sm text-gray-600">{{ $events->total() }} {{ __('events total') }}</span>
                </div>

                <!-- Events table with search functionality -->
                <div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
                    <table class="w-full data-table">
                        <!-- Table headers: Title, Location, Date, Status, Actions -->
                        <thead class="bg-gray-100">
                            <tr class="border-b">
                                <th class="p-3 text-left">{{ __('Title') }}</th>
                                <th class="p-3 text-left">{{ __('Location') }}</th>
                                <th class="p-3 text-left">{{ __('Start Date') }}</th>
                                <th class="p-3 text-center">{{ __('Status') }}</th>
                                <th class="p-3 text-right">{{ __('Actions') }}</th>
                            </tr>   
                        </thead>
                        <tbody>
                            <!-- Loop through events, display with data attributes for JS search -->
                            @forelse($events as $event)
                                <tr class="border-b hover:bg-gray-50 data-row"
                                    data-title="{{ strtolower($event->title) }}" 
                                    data-location="{{ strtolower($event->location) }}">
                                    <td class="p-3 font-medium">{{ $event->title }}</td>
                                    <td class="p-3 text-sm text-gray-600">{{ $event->location }}</td>
                                    <td class="p-3 text-sm text-gray-600">{{ $event->start_date->format('d.m.Y H:i') }}</td>
                                    <td class="p-3 text-center">
                                        <!-- Status badge: green=scheduled, red=cancelled, gray=finished -->
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($event->status === 'finished') bg-gray-200 text-gray-800
                                            @elseif($event->status === 'cancelled') bg-red-200 text-red-800
                                            @else bg-green-200 text-green-800
                                            @endif">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>

                                    <td class="p-3 text-right">
                                        <!-- View button (all users) -->
                                        <a href="{{ route('events.show', $event) }}" class="table-action view mr-2">{{ __('View') }}</a>

                                        <!-- Register/Unregister button (if event is from user's club) -->
                                        @auth
                                            @if(auth()->user()->member)
                                                @php
                                                    $userClubIds = auth()->user()->member->activeClubs()->pluck('clubs.id')->toArray();
                                                    $eventClubIds = $event->activeClubs()->pluck('clubs.id')->toArray();
                                                    $eventBelongsToUserClub = !empty(array_intersect($userClubIds, $eventClubIds));
                                                    $isRegistered = auth()->user()->member->activeEvents()->where('event_id', $event->id)->exists();
                                                @endphp

                                                @if($eventBelongsToUserClub)
                                                    @if($isRegistered)
                                                        <!-- Unregister button -->
                                                        <form method="POST" action="{{ route('events.unregister', $event) }}" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="table-action unregister mr-2">{{ __('Unregister') }}</button>
                                                        </form>
                                                    @else
                                                        <!-- Register button -->
                                                        <form method="POST" action="{{ route('events.register', $event) }}" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="table-action register mr-2">{{ __('Register') }}</button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endif
                                        @endauth
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">
                                        {{ __('No events found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                    <div class="mt-4">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </main>    
    </div>    
</x-app-layout> 
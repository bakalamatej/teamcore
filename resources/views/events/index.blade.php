@push('scripts')
    @vite(['resources/js/events/event-search.js'])
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

            <div class="flex items-center space-x-3">
                <form method="GET" action="{{ route('events.index') }}">
                    <x-secondary-button type="submit" name="my_events" value="1">
                        {{ __('My Events') }}
                    </x-secondary-button>
                </form>
            </div>
        </div>


        <div class="border border-gray-300 rounded-md overflow-hidden mt-6 shadow-md">
            <table class="w-full data-table">
                <thead class="bg-gray-100">
                    <tr class="border-b">
                        <th class="p-3 text-left">{{ __('Title') }}</th>
                        <th class="p-3 text-left">{{ __('Location') }}</th>
                        <th class="p-3 text-left">{{ __('Start date') }}</th>
                        <th class="p-3 text-right">{{ __('Actions') }}</th>
                    </tr>   
                </thead>
                <tbody>
                    @foreach($events as $event)
                        <tr class="data-row"
                            data-title="{{ strtolower($event->title) }}" 
                            data-location="{{ strtolower($event->location) }}">
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->location }}</td>
                            <td>{{ $event->start_date }}</td>

                            <td class="text-right">
                                <a href="{{ route('events.show', $event) }}" class="table-action view">{{ __('View') }}</a>

                                @auth
                                    @if(auth()->user()->isAdmin() || auth()->id() === $event->user_id)
                                        <a href="{{ route('events.edit', $event) }}" class="table-action edit">{{ __('Edit') }}</a>

                                        <button type="button" class="table-action delete"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'confirm-event-deletion-{{ $event->id }}')">
                                            {{ __('Delete') }}
                                        </button>

                                        <x-modal name="confirm-event-deletion-{{ $event->id }}" :show="false" focusable>
                                            <form method="POST" action="{{ route('events.destroy', $event) }}" class="p-6 text-left">
                                                @csrf
                                                @method('DELETE')

                                                <h2 class="my-heading">
                                                    {{ __('Are you sure you want to delete this event?') }}
                                                </h2>

                                                <p class="my-text">
                                                    {{ __('Once deleted, this event cannot be recovered.') }}
                                                </p>

                                                <div class="flex justify-end gap-3 mt-6">
                                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                                        {{ __('Cancel') }}
                                                    </x-secondary-button>

                                                    <x-danger-button type="submit">
                                                        {{ __('Delete Event') }}
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
                {{ $events->links() }}
            </div>
        </div>
    </div>    
</x-app-layout>
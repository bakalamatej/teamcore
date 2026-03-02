<!-- Load event search JS for real-time filtering -->
@push('scripts')
    @vite(['resources/js/shared/table-search.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-screen">
        <!-- Admin panel sidebar navigation -->
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        <!-- Main content: events list with search/filter -->
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="my-heading text-2xl">{{ __('Events Management') }}</h1>
                    <span class="text-sm text-gray-600">{{ $events->total() }} {{ __('events total') }}</span>
                </div>

                <!-- Search & filter section -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <form method="GET" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <x-input-label :value="__('Search')" />
                            <x-text-input
                                id="search"
                                type="text"
                                name="search"
                                placeholder="{{ __('Title...') }}"
                                class="mt-1 block w-full text-sm"
                                :value="request('search')"
                            />
                        </div>

                        <div class="flex-1">
                            <x-input-label :value="__('Sport Field')" />
                            <x-select-input
                                id="sport_field_id"
                                name="sport_field_id"
                                :options="$sportFields->pluck('name', 'id')->toArray()"
                                :selected="request('sport_field_id')"
                                placeholder="{{ __('Select sport field') }}"
                                class="mt-1 block w-full text-sm"
                            />
                        </div>

                        <div class="flex-1">
                            <x-input-label :value="__('Status')" />
                            <x-select-input
                                id="status"
                                name="status"
                                :options="['scheduled' => __('Scheduled'), 'cancelled' => __('Cancelled'), 'finished' => __('Finished')]"
                                :selected="request('status')"
                                placeholder="{{ __('Select status') }}"
                                class="mt-1 block w-full text-sm"
                            />
                        </div>

                        <!-- Submit button aligned with inputs -->
                        <div class="flex items-end justify-end gap-3">
                            <x-primary-button type="submit" class="!h-9">
                                {{ __('Filter') }}
                            </x-primary-button>
                            
                            <x-add-button href="{{ route('events.create') }}" class="!h-9" />
                        </div>
                    </form>
                </div>

                <!-- Events Table -->
                <div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
                    <table class="w-full data-table">
                        <thead class="bg-gray-100">
                            <tr class="border-b">
                                <th class="p-3 text-left">{{ __('Title') }}</th>
                                <th class="p-3 text-left">{{ __('Event Type') }}</th>
                                <th class="p-3 text-left">{{ __('Start Date') }}</th>
                                <th class="p-3 text-center">{{ __('Status') }}</th>
                                <th class="p-3 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr class="border-b hover:bg-gray-50 data-row">
                                    <td class="p-3 font-medium">{{ $event->title }}</td>
                                    <td class="p-3 text-sm text-gray-600">{{ $event->eventType->name ?? '-' }}</td>
                                    <td class="p-3 text-sm text-gray-600">{{ $event->start_date->format('d.m.Y') }}</td>
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
                                        <a href="{{ route('events.show', $event) }}" class="table-action view mr-2">
                                            {{ __('View') }}
                                        </a>

                                        <a href="{{ route('events.edit', $event) }}" class="table-action edit mr-2">
                                            {{ __('Edit') }}
                                        </a>

                                        <button type="button" class="table-action delete"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'confirm-event-deletion-{{ $event->id }}')">
                                            {{ __('Delete') }}
                                        </button>

                                        <x-modal name="confirm-event-deletion-{{ $event->id }}" :show="false" focusable>
                                            <form method="POST" action="{{ route('events.destroy', $event) }}" class="p-6 text-left">
                                                @csrf
                                                @method('DELETE')

                                                <h2 class="my-heading">{{ __('Delete Event') }}</h2>
                                                <p class="my-text">
                                                    {{ __('Are you sure you want to delete') }} <strong>{{ $event->title }}</strong>?
                                                    {{ __('This action cannot be undone.') }}
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

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $events->links() }}
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

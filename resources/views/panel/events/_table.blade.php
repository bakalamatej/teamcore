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
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($event->status === \App\Enums\EventStatus::FINISHED) bg-gray-200 text-gray-800
                            @elseif($event->status === \App\Enums\EventStatus::CANCELED) bg-red-200 text-red-800
                            @else bg-green-200 text-green-800
                            @endif">
                            {{ ucfirst($event->status->value) }}
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
                                x-on:click="$dispatch('open-modal', 'confirm-event-deletion-{{ $event->event_id }}')">
                            {{ __('Delete') }}
                        </button>

                        <x-modal name="confirm-event-deletion-{{ $event->event_id }}" :show="false" focusable>
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

<div class="mt-4">
    {{ $events->links() }}
</div>

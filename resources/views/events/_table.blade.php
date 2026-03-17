<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
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
            @forelse($events as $event)
                <tr class="border-b hover:bg-gray-50 data-row"
                    data-title="{{ strtolower($event->title) }}">
                    <td class="p-3 font-medium">{{ $event->title }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $event->sportField->name ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $event->start_date->format('d.m.Y H:i') }}</td>
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
                        <a href="{{ route('events.show', $event) }}" class="table-action view mr-2">{{ __('View') }}</a>

                        @auth
                            @if($userHasMember)
                                @if($event->canUnregister)
                                    <form method="POST" action="{{ route('events.unregister', $event) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="table-action unregister mr-2">{{ __('Unregister') }}</button>
                                    </form>
                                @elseif($event->canRegister)
                                    <form method="POST" action="{{ route('events.register', $event) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="table-action register mr-2">{{ __('Register') }}</button>
                                    </form>
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
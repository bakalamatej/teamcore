@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    <div class="mb-6 p-4 sm:p-8 bg-white rounded-lg">
        <h2 class="text-xl font-bold mb-4">{{ $date->format('j F Y') }} ({{ $date->format('l') }})</h2>
        <div class="space-y-4">
            @forelse($events as $event)
                <div class="border rounded-lg p-4 bg-gray-50 flex flex-col max-w-md">
                    <div class="font-semibold text-lg mb-1">{{ $event->title }}</div>
                    <div class="text-xs text-gray-500 mb-2">{{ $event->start_date->format('H:i') }} - {{ $event->end_date?->format('H:i') }}</div>
                    <div class="mb-2">{{ $event->description }}</div>
                    <a href="{{ route('events.show', $event->event_id) }}" class="text-blue-600 hover:underline self-start mt-2">{{ __('Show event detail') }}</a>
                </div>
            @empty
                <div class="text-gray-500">{{ __('No events for this day.') }}</div>
            @endforelse
        </div>
    </div>
</x-app-layout>

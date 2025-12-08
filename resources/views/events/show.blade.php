<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading">{{ $event->title }}</h1>

        <div class="mb-2">
            <p><strong>{{ __('Location (Sport Field):') }}</strong> {{ $event->sportField?->name ?? 'N/A' }}</p>
            <p><strong>{{ __('City:') }}</strong> {{ $event->sportField?->address?->city ?? 'N/A' }}</p>
            <p><strong>{{ __('Type:') }}</strong> {{ $event->eventType?->name ?? 'N/A' }}</p>
            <p><strong>{{ __('Status:') }}</strong> {{ ucfirst($event->status) }}</p>
            <p><strong>{{ __('Start:') }}</strong> {{ $event->start_date?->format('d.m.Y H:i') ?? 'N/A' }}</p>
            <p><strong>{{ __('End:') }}</strong> {{ $event->end_date?->format('d.m.Y H:i') ?? 'N/A' }}</p>
        </div>

        <div class="mb-4">
            <p><strong>{{ __('Description:') }}</strong></p>
            <p>{{ $event->description ?? '-' }}</p>
        </div>

        <div class="mb-4">
            <p><strong>{{ __('Clubs:') }}</strong></p>
            <ul>
                @foreach($event->allClubs as $club)
                    <li>{{ $club->name }}</li>
                @endforeach
            </ul>
        </div>


        <x-primary-button class="ms-3" :href="route('events.index')">
            {{ __('Back to Events') }}
        </x-primary-button>
    </div>
</x-app-layout>

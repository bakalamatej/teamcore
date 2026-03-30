<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading text-2xl mb-4">{{ __('Gallery') }}</h1>
        {{-- Filter --}}
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form method="GET" action="{{ route('gallery.index') }}" class="flex gap-4 flex-wrap items-end">
                <div class="flex-1 max-w-xs">
                    <x-input-label :value="__('Event')" />
                    <x-select-input
                        name="event_id"
                        :options="$eventOptions"
                        :selected="request('event_id')"
                        placeholder="{{ __('All events') }}"
                        class="mt-1 block w-full text-sm"
                        data-submit-on-choose
                    />
                </div>
                <div class="ml-auto">
                    <x-primary-button type="submit">{{ __('Filter') }}</x-primary-button>
                </div>
            </form>
        </div>

        @forelse($events as $event)
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $event->title }}</h2>
                    <span class="text-sm text-gray-500">{{ $event->start_date->format('d.m.Y') }}</span>
                    <span class="text-sm text-gray-400">({{ $event->eventFiles->count() }} {{ __('photos') }})</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($event->eventFiles as $file)
                        <a href="{{ route('files.download', $file) }}" target="_blank" class="block aspect-square overflow-hidden rounded-lg border border-gray-200 hover:opacity-90 transition-opacity">
                            @if(str_starts_with($file->file_type, 'image/'))
                                <img
                                    src="{{ route('files.download', $file) }}"
                                    alt="{{ $file->file_name }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                />
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <span class="text-xs text-gray-500">{{ $file->file_name }}</span>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-12">{{ __('No photos available.') }}</p>
        @endforelse
    </div>
</x-app-layout>
@push('scripts')
    @vite(['resources/js/events/event-update.js'])
@endpush

<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading">{{ __('Edit Event') }}</h1>

        <form id="updateEventForm" data-action="{{ route('events.update', $event) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div id="formErrorBox">
                <span id="formErrorMessage"></span>
                <button type="button" id="formErrorClose">×</button>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1 space-y-4">
                    <div>
                        <x-input-label :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ $event->title }}" required />
                    </div>

                    <div>
                        <x-input-label :value="__('Sport Field')" />
                        <x-select-input
                            id="sport_field_id"
                            name="sport_field_id"
                            :options="$sportFields->mapWithKeys(fn($f) => [$f->id => $f->name . ' (' . ($f->address->city ?? '-') . ')'])->toArray()"
                            :selected="$event->sport_field_id"
                            placeholder="Select location"
                        />
                    </div>

                    <div>
                        <x-input-label :value="__('Event Type')" />
                        <x-select-input
                            id="event_type_id"
                            name="event_type_id"
                            :options="$eventTypes->pluck('name','id')"
                            :selected="$event->event_type_id"
                            placeholder="Select type"
                        />
                    </div>

                    <div>
                        <x-input-label :value="__('Start Date')" />
                        <x-text-input id="start_date" name="start_date" type="datetime-local" class="mt-1 block w-full"
                            value="{{ $event->start_date->format('Y-m-d\TH:i') }}" required />
                    </div>

                    <div>
                        <x-input-label :value="__('End Date')" />
                        <x-text-input id="end_date" name="end_date" type="datetime-local" class="mt-1 block w-full"
                            value="{{ $event->end_date->format('Y-m-d\TH:i') }}" required />
                    </div>
                </div>

                <div class="flex-1">
                    <x-input-label :value="__('Description')" />
                    <x-textarea-input id="description" name="description" :value="$event->description" class="h-full" />
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
                <x-danger-button type="button" onclick="window.location='{{ route('events.index') }}'">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>

        <x-modal name="update-event" :show="false">
            <div class="p-4">
                <h2 class="text-lg font-semibold mb-2">{{ __('Event updated successfully!') }}</h2>
                <p class="text-sm text-gray-700">{{ __('Your changes have been saved.') }}</p>
                <div class="mt-4 text-right">
                    <button
                        x-on:click="$dispatch('close-modal', 'update-event')"
                        class="bg-indigo-600 text-white px-4 py-2 rounded"
                    >
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </x-modal>
    </div>
</x-app-layout>

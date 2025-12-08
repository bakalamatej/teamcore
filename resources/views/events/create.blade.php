@push('scripts')
    @vite(['resources/js/events/event-create.js'])
@endpush

<x-panel-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading">{{ __('Create Event') }}</h1>

        <form id="eventCreateForm" data-action="{{ route('events.store') }}" method="POST" class="space-y-4">
            @csrf

            <div id="formErrorBox">
                <span id="formErrorMessage"></span>
                <button type="button" id="formErrorClose">×</button>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1 space-y-4">
                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                    </div>

                    <div>
                        <x-input-label for="event_type_id" :value="__('Event Type')" />
                        <x-select-input
                            id="event_type_id"
                            name="event_type_id"
                            :options="$eventTypes->pluck('name','id')"
                            :selected="old('event_type_id')"
                            placeholder="-- {{ __('Select type') }} --"
                        />
                    </div>

                    <div>
                        <x-input-label for="sport_field_id" :value="__('Location')" />
                        <x-select-input
                            id="sport_field_id"
                            name="sport_field_id"
                            :options="$sportFields->mapWithKeys(fn($f) => [$f->id => $f->name . ' (' . ($f->address->city ?? '-') . ')'])->toArray()"
                            :selected="old('sport_field_id')"
                            placeholder="-- {{ __('Select location') }} --"
                        />
                    </div>

                    <div>
                        <x-input-label for="start_date" :value="__('Start Date')" />
                        <x-text-input id="start_date" name="start_date" type="datetime-local" class="mt-1 block w-full" required />
                    </div>

                    <div>
                        <x-input-label for="end_date" :value="__('End Date')" />
                        <x-text-input id="end_date" name="end_date" type="datetime-local" class="mt-1 block w-full" required />
                    </div>
                </div>

                <div class="flex-1">
                    <x-input-label for="description" :value="__('Description')" />
                    <x-textarea-input id="description" name="description" class="h-full" />
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <x-danger-button type="button" onclick="window.location='{{ route('panel.index') }}'">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>

        <x-modal name="create-event" :show="false">
            <div class="p-4">
                <h2 class="text-lg font-semibold mb-2">{{ __('Event created successfully!') }}</h2>
                <p class="text-sm text-gray-700">{{ __('Your event has been saved.') }}</p>
                <div class="mt-4 text-right">
                    <button
                        x-on:click="$dispatch('close-modal', 'create-event')"
                        class="bg-indigo-600 text-white px-4 py-2 rounded"
                    >
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </x-modal>
    </div>
</x-panel-layout>

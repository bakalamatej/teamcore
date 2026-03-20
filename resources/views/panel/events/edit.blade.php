@push('scripts')
    @vite(['resources/js/events/event-form.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <h1 class="my-heading">{{ __('Edit Event') }}</h1>

        <form method="POST" action="{{ route('panel.events.update', $event) }}" class="space-y-6">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="{{ $event->status->value }}">

            <div class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1 space-y-4">
                    <div>
                        <x-input-label :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ $event->title }}" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('Sport')" />
                        <x-select-input
                            id="sport_id"
                            name="sport_id"
                            :options="$sportOptions"
                            :selected="$event->sport_id"
                            placeholder="{{ __('Select sport') }}"
                            class="w-full"
                        />
                        <x-input-error :messages="$errors->get('sport_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('Event Type')" />
                        <x-select-input
                            id="event_type_id"
                            name="event_type_id"
                            :options="$eventTypesBySport[$event->sport_id] ?? []"
                            :selected="$event->event_type_id"
                            placeholder="{{ __('Select type') }}"
                            class="w-full"
                        />
                        <x-input-error :messages="$errors->get('event_type_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('Participating Clubs')" />
                        <x-multiselect-input
                            id="club_ids"
                            name="club_ids"
                            :options="$clubsBySport[$event->sport_id] ?? []"
                            :selected="$selectedClubIds"
                            placeholder="{{ __('Select participating clubs') }}"
                            class="w-full"
                        />
                        <x-input-error :messages="$errors->get('club_ids')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('Sport Field')" />
                        <x-select-input
                            id="sport_field_id"
                            name="sport_field_id"
                            :options="$sportFieldOptions"
                            :selected="$event->sport_field_id"
                            placeholder="Select location"
                            class="w-full"
                        />
                        <x-input-error :messages="$errors->get('sport_field_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('Start Date')" />
                        <x-text-input id="start_date" name="start_date" type="datetime-local" class="mt-1 block w-full"
                            value="{{ $event->start_date->format('Y-m-d\TH:i') }}" required />
                        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('End Date')" />
                        <x-text-input id="end_date" name="end_date" type="datetime-local" class="mt-1 block w-full"
                            value="{{ $event->end_date->format('Y-m-d\TH:i') }}" required />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>
                </div>

                <div class="flex-1 flex flex-col">
                    <x-input-label :value="__('Description')" />
                    <x-textarea-input id="description" placeholder="{{ __('Enter description') }}" name="description" :value="$event->description" class="mt-1 flex-1" />
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
                <x-danger-button :href="route('panel.events.index')">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>

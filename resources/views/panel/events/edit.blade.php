@push('scripts')
    @vite(['resources/js/events/event-update.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading">{{ __('Edit Event') }}</h1>

        <form
            id="updateEventForm"
            data-action="{{ route('panel.events.update', $event) }}"
            method="POST"
            class="space-y-6"
            x-data="{
                openSport: false,
                selectedSport: @js(old('sport_id', $event->sport_id)),
                previousSport: @js(old('sport_id', $event->sport_id)),
                sportOptions: @js($sportOptions),
                openEventType: false,
                selectedEventType: @js(old('event_type_id', $event->event_type_id)),
                eventTypesBySport: @js($eventTypesBySport),
                selectedClubIds: @js(collect(old('club_ids', $selectedClubIds))->map(fn($id) => (string) $id)->values()),
                clubsBySport: @js($clubsBySport),
                get availableEventTypes() {
                    if (!this.selectedSport) {
                        return {};
                    }

                    return this.eventTypesBySport[this.selectedSport] ?? {};
                },
                get availableClubs() {
                    if (!this.selectedSport) {
                        return {};
                    }

                    return this.clubsBySport[this.selectedSport] ?? {};
                },
                syncSportChange() {
                    if (this.selectedSport !== this.previousSport) {
                        this.selectedEventType = '';
                        this.selectedClubIds = [];
                        this.previousSport = this.selectedSport;
                    }
                }
            }"
        >
            <div x-effect="syncSportChange()"></div>
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="{{ $event->status->value }}">

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
                        <x-input-label :value="__('Sport')" />
                        <x-filtered-select
                            name="sport_id"
                            open-var="openSport"
                            selected-var="selectedSport"
                            options-var="sportOptions"
                            :placeholder="__('Select sport')"
                        />
                    </div>

                    <div>
                        <x-input-label :value="__('Event Type')" />
                        <x-filtered-select
                            name="event_type_id"
                            open-var="openEventType"
                            selected-var="selectedEventType"
                            options-var="availableEventTypes"
                            disabled-when="!selectedSport"
                            :placeholder="__('Select type')"
                        />
                    </div>

                    <div>
                        <x-input-label :value="__('Participating Clubs')" />
                        <div class="mt-2">
                            <x-multiselect-input
                                id="club_ids"
                                name="club_ids"
                                options-var="availableClubs"
                                :selected="$selectedClubIds"
                                :placeholder="__('Select participating clubs')"
                                disabled-when="!selectedSport"
                                x-model="selectedClubIds"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <div>
                        <x-input-label :value="__('Sport Field')" />
                        <x-select-input
                            id="sport_field_id"
                            name="sport_field_id"
                            :options="$sportFieldOptions"
                            :selected="$event->sport_field_id"
                            placeholder="Select location"
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

                <div class="flex-1 flex flex-col">
                    <x-input-label :value="__('Description')" />
                    <x-textarea-input id="description" placeholder="{{ __('Enter description') }}" name="description" :value="$event->description" class="mt-1 flex-1" />
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
                <x-danger-button :href="route('panel.events.index')">
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
        </main>
    </div>
</x-app-layout>

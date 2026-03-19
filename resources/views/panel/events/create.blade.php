@push('scripts')
    @vite(['resources/js/events/event-create.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        {{-- Sidebar --}}
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <h1 class="my-heading">{{ __('Create Event') }}</h1>
                <p class="my-text">{{ __('Create a new sporting event. Fill in all required fields and select the appropriate location and type.') }}</p>

                <form
                    id="eventCreateForm"
                    data-action="{{ route('panel.events.store') }}"
                    method="POST"
                    class="space-y-6"
                    x-data="eventForm"
                    x-init="
                        selectedSport = @js(old('sport_id'));
                        previousSport = @js(old('sport_id'));
                        sportOptions = @js($sportOptions);
                        selectedEventType = @js(old('event_type_id'));
                        eventTypesBySport = @js($eventTypesBySport);
                        selectedClubIds = @js(collect(old('club_ids', []))->map(fn($id) => (string) $id)->values());
                        clubsBySport = @js($clubsBySport);
                    "
                >
                    <div x-effect="syncSportChange()"></div>
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
                                <x-input-label for="sport_id" :value="__('Sport')" />
                                <x-filtered-select
                                    name="sport_id"
                                    open-var="openSport"
                                    selected-var="selectedSport"
                                    options-var="sportOptions"
                                    :placeholder="__('Select sport')"
                                />
                            </div>

                            <div>
                                <x-input-label for="event_type_id" :value="__('Event Type')" />
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
                                        :selected="old('club_ids', [])"
                                        :placeholder="__('Select participating clubs')"
                                        disabled-when="!selectedSport"
                                        x-model="selectedClubIds"
                                        class="w-full"
                                    />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="sport_field_id" :value="__('Location')" />
                                <x-select-input
                                    id="sport_field_id"
                                    name="sport_field_id"
                                    :options="$sportFieldOptions"
                                    :selected="old('sport_field_id')"
                                    placeholder="{{ __('Select location') }}"
                                    class="w-full"
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

                        <div class="flex-1 flex flex-col">
                            <x-input-label for="description" :value="__('Description')" />
                            <x-textarea-input id="description" placeholder="{{ __('Enter description') }}" name="description" class="mt-1 flex-1" />
                        </div>
                    </div>

                    <div class="flex gap-4 mt-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <x-danger-button :href="route('panel.index')">
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
        </main>
    </div>
</x-app-layout>
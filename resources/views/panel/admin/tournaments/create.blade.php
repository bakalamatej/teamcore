<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <h1 class="my-heading">{{ __('Create Tournament') }}</h1>
        <p class="my-text">{{ __('Create a new tournament. You can add matches and events after creation.') }}</p>

        <form action="{{ route('panel.admin.tournaments.store') }}" method="POST" class="space-y-6"
            x-data="eventForm"
            x-init="
                selectedEventType = @js(old('event_type_id', ''));
                selectedSport = @js(old('sport_id', ''));
                selectedSportField = @js(old('sport_field_id', ''));
                selectedClubs = @js(collect(old('club_ids', []))->map(fn($id) => (string) $id)->values());
                eventTypesBySport = @js($eventTypesBySport);
                clubsBySport = @js($clubsBySport);
                sportFieldsBySport = @js($sportFieldsBySport ?? []);
            ">
            <div x-effect="syncSportChange()"></div>
            @csrf

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1 space-y-4">
                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" required />
                    </div>
                    <div>
                        <x-input-label for="event_type_id" :value="__('Tournament Type')" />
                        <x-filtered-select name="event_type_id" open-var="openEventType" selected-var="selectedEventType" options-var="availableEventTypes" :placeholder="__('Select type')" />
                    </div>
                    <div>
                        <x-input-label :value="__('Participating Clubs')" />
                        <x-multiselect-input id="club_ids" name="club_ids" options-var="availableClubs" :selected="old('club_ids', [])" :placeholder="__('Select participating clubs')" x-model="selectedClubs" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="sport_field_id" :value="__('Location')" />
                        <x-filtered-select name="sport_field_id" open-var="openSportField" selected-var="selectedSportField" options-var="availableSportFields" :placeholder="__('Select location')" />
                    </div>
                    <div>
                        <x-input-label for="start_date" :value="__('Start Date')" />
                        <x-text-input id="start_date" name="start_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('start_date') }}" required />
                    </div>
                    <div>
                        <x-input-label for="end_date" :value="__('End Date')" />
                        <x-text-input id="end_date" name="end_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('end_date') }}" required />
                    </div>
                </div>
                <div class="flex-1 flex flex-col">
                    <x-input-label for="description" :value="__('Description')" />
                    <x-textarea-input id="description" placeholder="{{ __('Enter description') }}" name="description" :value="old('description')" class="mt-1 flex-1" />
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Create Tournament') }}</x-primary-button>
                <x-danger-button :href="route('panel.admin.tournaments.index')">{{ __('Discard') }}</x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>
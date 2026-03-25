<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <h1 class="my-heading">{{ __('Edit Tournament') }}</h1>

        <form method="POST" action="{{ route('panel.admin.tournaments.update', $tournament) }}" class="space-y-6"
            x-data="eventForm"
            x-init="
                selectedEventType = @js(old('event_type_id', (string) $tournament->event_type_id));
                selectedSport = @js(old('sport_id', ''));
                selectedSportField = @js(old('sport_field_id', (string) $tournament->sport_field_id));
                selectedClubs = @js(collect(old('club_ids', $selectedClubIds))->map(fn($id) => (string) $id)->values());
                eventTypesBySport = @js($eventTypesBySport);
                clubsBySport = @js($clubsBySport);
                sportFieldsBySport = @js($sportFieldsBySport ?? []);
            ">
            <div x-effect="syncSportChange()"></div>
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="{{ $tournament->status->value }}">

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
                        <x-input-label :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $tournament->title) }}" required />
                    </div>
                    <div>
                        <x-input-label :value="__('Tournament Type')" />
                        <x-filtered-select name="event_type_id" open-var="openEventType" selected-var="selectedEventType" options-var="availableEventTypes" :placeholder="__('Select type')" />
                    </div>
                    <div>
                        <x-input-label :value="__('Participating Clubs')" />
                        <x-multiselect-input id="club_ids" name="club_ids" options-var="availableClubs" :selected="old('club_ids', $selectedClubIds)" :placeholder="__('Select participating clubs')" x-model="selectedClubs" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label :value="__('Location')" />
                        <x-filtered-select name="sport_field_id" open-var="openSportField" selected-var="selectedSportField" options-var="availableSportFields" :placeholder="__('Select location')" />
                    </div>
                    <div>
                        <x-input-label :value="__('Start Date')" />
                        <x-text-input id="start_date" name="start_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('start_date', $tournament->start_date->format('Y-m-d\TH:i')) }}" required />
                    </div>
                    <div>
                        <x-input-label :value="__('End Date')" />
                        <x-text-input id="end_date" name="end_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('end_date', $tournament->end_date->format('Y-m-d\TH:i')) }}" required />
                    </div>
                </div>
                <div class="flex-1 flex flex-col">
                    <x-input-label :value="__('Description')" />
                    <x-textarea-input id="description" placeholder="{{ __('Enter description') }}" name="description" :value="old('description', $tournament->description)" class="mt-1 flex-1" />
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Update Tournament') }}</x-primary-button>
                <x-danger-button :href="route('panel.admin.tournaments.index', $tournament)">{{ __('Discard') }}</x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>
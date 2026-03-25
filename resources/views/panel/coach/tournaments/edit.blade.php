<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <h1 class="my-heading">{{ __('Edit Tournament') }}</h1>

        <form method="POST" action="{{ route('panel.coach.tournaments.update', $tournament) }}" class="space-y-6">
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
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label :value="__('Tournament Type')" />
                        <x-select-input
                            id="event_type_id"
                            name="event_type_id"
                            :options="$eventTypeOptions"
                            :selected="old('event_type_id', $selectedEventTypeId)"
                            placeholder="{{ __('Select type') }}"
                            class="mt-1 block w-full"
                        />
                        <x-input-error :messages="$errors->get('event_type_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label :value="__('Participating Clubs')" />
                        <x-multiselect-input
                            id="club_ids"
                            name="club_ids"
                            :options="$clubOptions"
                            :selected="old('club_ids', $selectedClubIds)"
                            placeholder="{{ __('Select participating clubs') }}"
                            class="mt-1"
                        />
                        <x-input-error :messages="$errors->get('club_ids')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label :value="__('Location')" />
                        <x-select-input
                            id="sport_field_id"
                            name="sport_field_id"
                            :options="$sportFieldOptions"
                            :selected="old('sport_field_id', (string) $tournament->sport_field_id)"
                            placeholder="{{ __('Select location') }}"
                            class="mt-1 block w-full"
                        />
                        <x-input-error :messages="$errors->get('sport_field_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label :value="__('Start Date')" />
                        <x-text-input id="start_date" name="start_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('start_date', $tournament->start_date->format('Y-m-d\TH:i')) }}" required />
                        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label :value="__('End Date')" />
                        <x-text-input id="end_date" name="end_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('end_date', $tournament->end_date->format('Y-m-d\TH:i')) }}" required />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>
                </div>
                <div class="flex-1 flex flex-col">
                    <x-input-label :value="__('Description')" />
                    <x-textarea-input id="description" placeholder="{{ __('Enter description') }}" name="description" :value="old('description', $tournament->description)" class="mt-1 flex-1" />
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Update Tournament') }}</x-primary-button>
                <x-danger-button :href="route('panel.coach.tournaments.show', $tournament)">{{ __('Discard') }}</x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>
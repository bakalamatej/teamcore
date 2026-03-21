<x-panel-layout>
	<div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
		<h1 class="my-heading">{{ __('Edit Event') }}</h1>

		<form
			method="POST"
			action="{{ route('panel.admin.events.update', $event) }}"
			class="space-y-6"
			x-data="eventForm"
			x-init="
				selectedSport = @js(old('sport_id', (string) $event->sport_id));
				previousSport = @js(old('sport_id', (string) $event->sport_id));
				selectedEventType = @js(old('event_type_id', (string) $event->event_type_id));
				selectedClubs = @js(collect(old('club_ids', $selectedClubIds))->map(fn($id) => (string) $id)->values());
				sportOptions = @js($sportOptions);
				eventTypesBySport = @js($eventTypesBySport);
				clubsBySport = @js($clubsBySport);
			"
		>
			<div x-effect="syncSportChange()"></div>
			@csrf
			@method('PATCH')
			<input type="hidden" name="status" value="{{ $event->status->value }}">

			<div class="flex flex-col lg:flex-row gap-6">
				<div class="flex-1 space-y-4">
					<div>
						<x-input-label :value="__('Title')" />
						<x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $event->title) }}" required />
						<x-input-error :messages="$errors->get('title')" class="mt-2" />
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
						<x-input-error :messages="$errors->get('sport_id')" class="mt-2" />
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
						<x-input-error :messages="$errors->get('event_type_id')" class="mt-2" />
					</div>

					<div>
						<div x-effect="syncSportChange()"></div>

						<x-input-label :value="__('Participating Clubs')" />
                        <x-multiselect-input
                            id="club_ids"
                            name="club_ids"
                            options-var="availableClubs"
                            :selected="old('club_ids', $selectedClubIds)"
                            :placeholder="__('Select participating clubs')"
                            disabled-when="!selectedSport"
                            x-model="selectedClubs"
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
							:selected="old('sport_field_id', $event->sport_field_id)"
							placeholder="{{ __('Select location') }}"
							class="mt-1"
						/>
						<x-input-error :messages="$errors->get('sport_field_id')" class="mt-2" />
					</div>

					<div>
						<x-input-label :value="__('Start Date')" />
						<x-text-input id="start_date" name="start_date" type="datetime-local" class="mt-1 block w-full"
							value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}" required />
						<x-input-error :messages="$errors->get('start_date')" class="mt-2" />
					</div>

					<div>
						<x-input-label :value="__('End Date')" />
						<x-text-input id="end_date" name="end_date" type="datetime-local" class="mt-1 block w-full"
							value="{{ old('end_date', $event->end_date->format('Y-m-d\TH:i')) }}" required />
						<x-input-error :messages="$errors->get('end_date')" class="mt-2" />
					</div>
				</div>

				<div class="flex-1 flex flex-col">
					<x-input-label :value="__('Description')" />
					<x-textarea-input id="description" placeholder="{{ __('Enter description') }}" name="description" :value="old('description', $event->description)" class="mt-1 flex-1" />
					<x-input-error :messages="$errors->get('description')" class="mt-2" />
				</div>
			</div>

			<div class="flex gap-4 mt-4">
				<x-primary-button>{{ __('Update') }}</x-primary-button>
				<x-danger-button :href="route('panel.admin.events.index')">
					{{ __('Discard') }}
				</x-danger-button>
			</div>
		</form>
	</div>
</x-panel-layout>
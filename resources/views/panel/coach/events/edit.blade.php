<x-panel-layout>
	<div class="bg-white shadow-xl rounded-lg p-4 sm:p-8">
		<h1 class="my-heading">{{ __('Edit Event') }}</h1>

		<form
			method="POST"
			action="{{ route('panel.coach.events.update', $event) }}"
			class="space-y-6"
		>
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
						<x-input-label for="event_type_id" :value="__('Event Type')" />
						<x-select-input
							id="event_type_id"
							name="event_type_id"
							:options="$eventTypeOptions"
							:selected="old('event_type_id', $event->event_type_id)"
							placeholder="{{ __('Select type') }}"
							class="mt-1"
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
							:placeholder="__('Select participating clubs')"
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
							value="{{ old('start_date', $event->start_date->format('Y-m-d\\TH:i')) }}" required />
						<x-input-error :messages="$errors->get('start_date')" class="mt-2" />
					</div>

					<div>
						<x-input-label :value="__('End Date')" />
						<x-text-input id="end_date" name="end_date" type="datetime-local" class="mt-1 block w-full"
							value="{{ old('end_date', $event->end_date->format('Y-m-d\\TH:i')) }}" required />
						<x-input-error :messages="$errors->get('end_date')" class="mt-2" />
					</div>
				</div>

				<div class="flex-1 flex flex-col min-w-0">
					<div class="mb-4">
						<x-input-label :value="__('Participating Members')" />
						<x-multiselect-input
							id="member_club_ids"
							name="member_club_ids"
							:options="$memberOptions"
							:selected="old('member_club_ids', $selectedMemberIds)"
							:placeholder="__('Select members')"
							class="mt-1"
						/>
						<x-input-error :messages="$errors->get('member_club_ids')" class="mt-2" />
					</div>
					
					<x-input-label :value="__('Description')" />
					<x-textarea-input id="description" placeholder="{{ __('Enter description') }}" name="description" :value="old('description', $event->description)" class="mt-1 flex-1" />
					<x-input-error :messages="$errors->get('description')" class="mt-2" />
				</div>
			</div>

			<div class="flex gap-4 mt-4">
				<x-primary-button>{{ __('Update') }}</x-primary-button>
				<x-danger-button :href="route('panel.coach.events.index')">
					{{ __('Discard') }}
				</x-danger-button>
			</div>
		</form>
	</div>
</x-panel-layout>
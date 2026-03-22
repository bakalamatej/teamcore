<x-panel-layout>
	<div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
		<h1 class="my-heading">{{ __('Create Event') }}</h1>
		<p class="my-text">{{ __('Create a new sporting event. Fill in all required fields and select the appropriate location and type.') }}</p>

			<form
				action="{{ route('panel.coach.events.store') }}"
				method="POST"
				class="space-y-6"
			>
				<input type="hidden" name="sport_id" value="{{ $club->sport_id }}">
			@csrf

			<div class="flex flex-col lg:flex-row gap-6">
				<div class="flex-1 space-y-4">
					<div>
						<x-input-label for="title" :value="__('Title')" />
						<x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" required />
						<x-input-error :messages="$errors->get('title')" class="mt-2" />
					</div>
						<div>
							<x-input-label for="event_type_id" :value="__('Event Type')" />
							<x-select-input
								id="event_type_id"
								name="event_type_id"
								:options="$eventTypes"
								:selected="old('event_type_id')"
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
								:options="$clubs"
								:selected="old('club_ids', [$club->club_id])"
								:placeholder="__('Select participating clubs')"
								class="mt-1"
							/>
							<x-input-error :messages="$errors->get('club_ids')" class="mt-2" />
						</div>

					<div>
						<x-input-label for="sport_field_id" :value="__('Location')" />
						<x-select-input
							id="sport_field_id"
							name="sport_field_id"
							:options="$sportFieldOptions"
							:selected="old('sport_field_id')"
							placeholder="{{ __('Select location') }}"
                            class="mt-1"
						/>
						<x-input-error :messages="$errors->get('sport_field_id')" class="mt-2" />
					</div>

					<div>
						<x-input-label for="start_date" :value="__('Start Date')" />
						<x-text-input id="start_date" name="start_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('start_date') }}" required />
						<x-input-error :messages="$errors->get('start_date')" class="mt-2" />
					</div>

					<div>
						<x-input-label for="end_date" :value="__('End Date')" />
						<x-text-input id="end_date" name="end_date" type="datetime-local" class="mt-1 block w-full" value="{{ old('end_date') }}" required />
						<x-input-error :messages="$errors->get('end_date')" class="mt-2" />
					</div>
				</div>

				<div class="flex-1 flex flex-col">
					<x-input-label for="description" :value="__('Description')" />
					<x-textarea-input id="description" placeholder="{{ __('Enter description') }}" name="description" :value="old('description')" class="mt-1 flex-1" />
					<x-input-error :messages="$errors->get('description')" class="mt-2" />
				</div>
			</div>

			<div class="flex gap-4 mt-4">
				<x-primary-button>{{ __('Save') }}</x-primary-button>
				<x-danger-button :href="route('panel.coach.events.index')">
					{{ __('Discard') }}
				</x-danger-button>
			</div>
		</form>
	</div>
</x-panel-layout>
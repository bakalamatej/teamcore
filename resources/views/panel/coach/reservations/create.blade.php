<x-panel-layout>
	<div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
		<h1 class="my-heading">{{ __('Create Reservation') }}</h1>
		<p class="my-text">{{ __('Add a new reservation request.') }}</p>

		<form
			method="POST"
			action="{{ route('panel.coach.reservations.store') }}"
			class="space-y-6"
		>
			@csrf
			<input type="hidden" name="created_by_member_club_id" value="{{ $memberClubId }}">

			<div class="flex flex-col lg:flex-row gap-6">
				<div class="flex-1 space-y-4">
					<div>
						<x-input-label for="title" :value="__('Title')" />
						<x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" required />
						<x-input-error :messages="$errors->get('title')" class="mt-2" />
					</div>
					<div>
						<x-input-label for="sport_field_id" :value="__('Sport Field')" />
						<x-select-input
							id="sport_field_id"
							name="sport_field_id"
							:options="$sportFieldOptions"
							:selected="old('sport_field_id')"
							placeholder="{{ __('Select sport field') }}"
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
					<x-textarea-input id="description" name="description" class="mt-1 flex-1" rows="3" :value="old('description')" />
					<x-input-error :messages="$errors->get('description')" class="mt-2" />
				</div>
			</div>

			<div class="flex gap-4 mt-6">
				<x-primary-button>{{ __('Create') }}</x-primary-button>
				<x-danger-button :href="route('panel.coach.reservations.index')">{{ __('Discard') }}</x-danger-button>
			</div>
		</form>
	</div>
</x-panel-layout>

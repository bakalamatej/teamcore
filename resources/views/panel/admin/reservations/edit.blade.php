<x-panel-layout>
	<div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
		<h1 class="my-heading">{{ __('Edit Reservation') }}</h1>

		<form
			method="POST"
			action="{{ route('panel.admin.reservations.update', $reservation) }}"
			class="space-y-6"
			x-data="reservationForm"
			x-init="
				selectedSport = @js(old('sport_id', (string) $reservation->sport_id));
				previousSport = @js(old('sport_id', (string) $reservation->sport_id));
				selectedSportField = @js(old('sport_field_id', (string) $reservation->sport_field_id));
				selectedClub = @js(old('club_id', (string) $reservation->club_id));
				sportOptions = @js($sportOptions);
				sportFieldsBySport = @js($sportFieldsBySport);
				clubsBySport = @js($clubsBySport);
				selectedMembership = @js(old('created_by_member_club_id', (string) $reservation->created_by_member_club_id));
				membershipsByClub = @js($membershipsByClub);
			"
		>
			<div x-effect="syncSportChange()"></div>
			@csrf
			@method('PATCH')

			<div class="flex flex-col lg:flex-row gap-6">
				<div class="flex-1 space-y-4">
					<div>
						<x-input-label for="title" :value="__('Title')" />
						<x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $reservation->title) }}" required />
						<x-input-error :messages="$errors->get('title')" class="mt-2" />
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
						<x-input-error :messages="$errors->get('sport_id')" class="mt-2" />
					</div>

					<div>
						<x-input-label for="sport_field_id" :value="__('Sport Field')" />
						<x-filtered-select
							name="sport_field_id"
							open-var="openSportField"
							selected-var="selectedSportField"
							options-var="availableSportFields"
							disabled-when="!selectedSport"
							:placeholder="__('Select sport field')"
						/>
						<x-input-error :messages="$errors->get('sport_field_id')" class="mt-2" />
					</div>

					<div>
						<x-input-label for="club_id" :value="__('Club')" />
						<x-filtered-select
							name="club_id"
							open-var="openClub"
							selected-var="selectedClub"
							options-var="availableClubs"
							disabled-when="!selectedSport"
							:placeholder="__('Select club')"
						/>
						<x-input-error :messages="$errors->get('club_id')" class="mt-2" />
					</div>

					<div>
						<div x-effect="syncClubChange()"></div>

						<div>
							<x-input-label for="created_by_member_club_id" :value="__('Created By Membership')" />
							<x-filtered-select
								name="created_by_member_club_id"
								open-var="openMembership"
								selected-var="selectedMembership"
								options-var="availableMemberships"
								disabled-when="!selectedClub"
								:placeholder="__('Select membership')"
							/>
							<x-input-error :messages="$errors->get('created_by_member_club_id')" class="mt-2" />
						</div>
					</div>

					<div>
						<x-input-label for="start_date" :value="__('Start Date')" />
						<x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" value="{{ old('start_date', optional($reservation->start_date)->format('Y-m-d')) }}" required />
						<x-input-error :messages="$errors->get('start_date')" class="mt-2" />
					</div>

					<div>
						<x-input-label for="end_date" :value="__('End Date')" />
						<x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" value="{{ old('end_date', optional($reservation->end_date)->format('Y-m-d')) }}" required />
						<x-input-error :messages="$errors->get('end_date')" class="mt-2" />
					</div>
				</div>

				<div class="flex-1 flex flex-col">
					<x-input-label for="description" :value="__('Description')" />
					<x-textarea-input id="description" name="description" :value="old('description', $reservation->description)" placeholder="{{ __('Enter description') }}" class="mt-1 flex-1" />
					<x-input-error :messages="$errors->get('description')" class="mt-2" />
				</div>
			</div>

			<div class="flex gap-4 mt-6">
				<x-primary-button>{{ __('Update') }}</x-primary-button>
				<x-danger-button :href="route('panel.admin.reservations.index')">{{ __('Discard') }}</x-danger-button>
			</div>
		</form>
	</div>
</x-panel-layout>
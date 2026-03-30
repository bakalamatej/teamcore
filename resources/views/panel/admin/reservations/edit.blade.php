<x-panel-layout>
	<div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
		<h1 class="my-heading">{{ __('Edit Reservation') }}</h1>

		<form
			method="POST"
			action="{{ route('panel.admin.reservations.update', $reservation) }}"
			class="space-y-6"
			x-data="reservationForm"
			x-init="
				selectedMembership = @js((string) old('created_by_member_club_id', $reservation->created_by_member_club_id));
				selectedSportField = @js((string) old('sport_field_id', $reservation->sport_field_id));
				memberships = @js($membershipOptions);
				membershipMeta = @js($membershipMeta);
				sportFieldsBySport = @js($sportFieldsBySport);
			"
		>
			<div x-effect="syncMembershipChange()"></div>
			@csrf
			@method('PATCH')

			<div class="flex flex-col lg:flex-row gap-6">
				<div class="flex-1 space-y-4">
					<div>
						<x-input-label for="title" :value="__('Title')" />
						<x-text-input
							id="title"
							name="title"
							type="text"
							class="mt-1 block w-full"
							value="{{ old('title', $reservation->title) }}"
							required
						/>
						<x-input-error :messages="$errors->get('title')" class="mt-2" />
					</div>

					<div>
						<x-input-label for="created_by_member_club_id" :value="__('Created By Membership')" />
						<x-filtered-select
							name="created_by_member_club_id"
							open-var="openMembership"
							selected-var="selectedMembership"
							options-var="memberships"
							:placeholder="__('Select membership')"
						/>
						<x-input-error :messages="$errors->get('created_by_member_club_id')" class="mt-2" />
					</div>

					<div>
						<x-input-label for="sport_field_id" :value="__('Sport Field')" />
						<x-filtered-select
							name="sport_field_id"
							open-var="openSportField"
							selected-var="selectedSportField"
							options-var="availableSportFields"
							:placeholder="__('Select sport field')"
						/>
						<x-input-error :messages="$errors->get('sport_field_id')" class="mt-2" />
					</div>

					<div>
						<x-input-label for="start_date" :value="__('Start Date')" />
						<x-text-input
							id="start_date"
							name="start_date"
							type="datetime-local"
							class="mt-1 block w-full"
							value="{{ old('start_date', optional($reservation->start_date)->format('Y-m-d\TH:i')) }}"
							required
						/>
						<x-input-error :messages="$errors->get('start_date')" class="mt-2" />
					</div>

					<div>
						<x-input-label for="end_date" :value="__('End Date')" />
						<x-text-input
							id="end_date"
							name="end_date"
							type="datetime-local"
							class="mt-1 block w-full"
							value="{{ old('end_date', optional($reservation->end_date)->format('Y-m-d\TH:i')) }}"
							required
						/>
						<x-input-error :messages="$errors->get('end_date')" class="mt-2" />
					</div>
				</div>

				<div class="flex-1 flex flex-col">
					<x-input-label for="description" :value="__('Description')" />
					<x-textarea-input
						id="description"
						name="description"
						:value="old('description', $reservation->description)"
						placeholder="{{ __('Enter description') }}"
						class="mt-1 flex-1"
					/>
					<x-input-error :messages="$errors->get('description')" class="mt-2" />
				</div>
			</div>

			<div class="flex gap-4 mt-6">
				<x-primary-button>{{ __('Update') }}</x-primary-button>
				<x-danger-button :href="route('panel.admin.reservations.index')">
					{{ __('Discard') }}
				</x-danger-button>
			</div>
		</form>
	</div>
</x-panel-layout>
<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <div class="mb-4 pb-4 border-b-2 border-gray-200">
            <h1 class="my-heading">{{ __('Create Event from Reservation') }}</h1>
            <p class="my-text">{{ __('Reservation') }}: <strong>{{ $reservation->title }}</strong></p>
        </div>

        <form action="{{ route('panel.coach.reservations.store-event', $reservation) }}" method="POST" class="space-y-6">
            @csrf

            <input type="hidden" name="sport_field_id" value="{{ $reservation->sport_field_id }}">
            <input type="hidden" name="start_date" value="{{ $reservation->start_date->format('Y-m-d\TH:i') }}">
            <input type="hidden" name="end_date" value="{{ $reservation->end_date->format('Y-m-d\TH:i') }}">

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Locked fields from reservation -->
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-2">
                <p class="text-sm font-medium text-gray-700">{{ __('Prefilled from reservation (cannot be changed):') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium">{{ __('Sport Field:') }}</span>
                        <span>{{ $reservation->sportField?->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">{{ __('Start:') }}</span>
                        <span>{{ $reservation->start_date?->format('d.m.Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="font-medium">{{ __('End:') }}</span>
                        <span>{{ $reservation->end_date?->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>

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
                            :options="$eventTypeOptions"
                            :selected="old('event_type_id')"
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
                </div>
                <div class="flex-1 flex flex-col">
                    <x-input-label for="description" :value="__('Description')" />
                    <x-textarea-input id="description" placeholder="{{ __('Enter description') }}" name="description" :value="old('description')" class="mt-1 flex-1" />
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Create Event') }}</x-primary-button>
                <x-danger-button :href="route('panel.coach.reservations.show', $reservation)">{{ __('Discard') }}</x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>
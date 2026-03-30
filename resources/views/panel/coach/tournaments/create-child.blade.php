<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="mb-4 pb-4 border-b-2 border-gray-200">
            <h1 class="my-heading">{{ __('Add Match to Tournament') }}</h1>
            <p class="my-text">{{ __('Tournament') }}: <strong>{{ $tournament->title }}</strong></p>
        </div>

        <form action="{{ route('panel.coach.tournaments.children.store', $tournament) }}" method="POST" class="space-y-6">
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
                        <x-input-label for="event_type_id" :value="__('Event Type')" />
                        <x-select-input
                            id="event_type_id"
                            name="event_type_id"
                            :options="$eventTypeOptions"
                            :selected="old('event_type_id', $selectedEventTypeId)"
                            placeholder="{{ __('Select type') }}"
                            class="mt-1 block w-full"
                        />
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
                    </div>
                    <div>
                        <x-input-label for="sport_field_id" :value="__('Location')" />
                        <x-select-input
                            id="sport_field_id"
                            name="sport_field_id"
                            :options="$sportFieldOptions"
                            :selected="old('sport_field_id')"
                            placeholder="{{ __('Select location') }}"
                            class="mt-1 block w-full"
                        />
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
                <x-primary-button>{{ __('Add to Tournament') }}</x-primary-button>
                <x-danger-button :href="route('panel.coach.tournaments.show', $tournament)">{{ __('Discard') }}</x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>
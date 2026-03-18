<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <h1 class="my-heading">{{ __('Create Reservation') }}</h1>
                <p class="my-text">{{ __('Add a new reservation request.') }}</p>

                <form method="POST" action="{{ route('panel.reservations.store') }}" class="space-y-6">
                    @csrf

                    <div class="flex flex-col lg:flex-row gap-6">
                        <div class="flex-1 space-y-4">
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="sport_id" :value="__('Sport')" />
                            <x-select-input
                                id="sport_id"
                                name="sport_id"
                                :options="$sportOptions"
                                :selected="old('sport_id')"
                                placeholder="{{ __('Select sport') }}"
                                class="w-full"
                            />
                            <x-input-error :messages="$errors->get('sport_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="sport_field_id" :value="__('Sport Field')" />
                            <x-select-input
                                id="sport_field_id"
                                name="sport_field_id"
                                :options="$sportFieldOptions"
                                :selected="old('sport_field_id')"
                                placeholder="{{ __('Select sport field') }}"
                                class="w-full"
                            />
                            <x-input-error :messages="$errors->get('sport_field_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="club_id" :value="__('Club')" />
                            <x-select-input
                                id="club_id"
                                name="club_id"
                                :options="$clubOptions"
                                :selected="old('club_id')"
                                placeholder="{{ __('Select club') }}"
                                class="w-full"
                            />
                            <x-input-error :messages="$errors->get('club_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="created_by_member_club_id" :value="__('Created By Membership')" />
                            <x-select-input
                                id="created_by_member_club_id"
                                name="created_by_member_club_id"
                                :options="$memberClubOptions"
                                :selected="old('created_by_member_club_id')"
                                placeholder="{{ __('Select membership') }}"
                                class="w-full"
                            />
                            <x-input-error :messages="$errors->get('created_by_member_club_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" value="{{ old('start_date') }}" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="end_date" :value="__('End Date')" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" value="{{ old('end_date') }}" required />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>
                        </div>

                        <div class="flex-1 flex flex-col">
                            <x-input-label for="description" :value="__('Description')" />
                            <x-textarea-input id="description" name="description" :value="old('description')" placeholder="{{ __('Enter description') }}" class="mt-1 flex-1" />
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <x-danger-button :href="route('panel.reservations.index')">
                            {{ __('Discard') }}
                        </x-danger-button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-app-layout>

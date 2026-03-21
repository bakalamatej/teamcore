<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8"
        x-data="membershipForm"
        x-init="
            selectedMember = @js((string) ($selectedMemberId ?? old('member_id', '')));
            selectedClub = @js((string) old('club_id', ''));
            previousClub = @js((string) old('club_id', ''));
                    selectedSport = @js((string) old('sport_id', ''));
                    memberOptions = @js(collect($memberOptions)->mapWithKeys(fn($label, $id) => [(string) $id => $label]));
                    clubOptions = @js(collect($clubOptions)->mapWithKeys(fn($label, $id) => [(string) $id => $label]));
                    sportsByClub = @js($sportsByClub);
                "
            >
                <div x-effect="syncClubChange()"></div>

                <h1 class="my-heading">{{ __('Add Membership') }}</h1>
                <p class="my-text">{{ __('Create a new membership for a selected user.') }}</p>

                <form method="POST" action="{{ route('panel.admin.memberships.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl">
                        <div x-on:click.outside="openMember = false" class="md:col-span-2">
                            <x-input-label :value="__('User / Member')" />
                            <x-filtered-select
                                name="member_id"
                                open-var="openMember"
                                selected-var="selectedMember"
                                options-var="memberOptions"
                                :placeholder="__('Select user')"
                            />
                            <x-input-error :messages="$errors->get('member_id')" class="mt-2" />
                        </div>

                        <div x-on:click.outside="openClub = false">
                            <x-input-label :value="__('Club')" />
                            <x-filtered-select
                                name="club_id"
                                open-var="openClub"
                                selected-var="selectedClub"
                                options-var="clubOptions"
                                :placeholder="__('Select club')"
                            />
                            <x-input-error :messages="$errors->get('club_id')" class="mt-2" />
                        </div>

                        <div x-on:click.outside="openSport = false">
                            <x-input-label :value="__('Sport')" />
                            <x-filtered-select
                                name="sport_id"
                                open-var="openSport"
                                selected-var="selectedSport"
                                options-var="availableSports"
                                disabled-when="!selectedClub"
                                :placeholder="__('Select sport')"
                            />
                            <x-input-error :messages="$errors->get('sport_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label :value="__('Role')" />
                            <x-select-input
                                name="role"
                                :options="['player' => __('Player'), 'coach' => __('Coach')]"
                                :selected="old('role', 'player')"
                                class="mt-1 block w-full"
                            />
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label :value="__('Joined At')" />
                            <x-text-input
                                name="joined_at"
                                type="date"
                                class="mt-1 block w-full"
                                :value="old('joined_at', now()->format('Y-m-d'))"
                                required
                            />
                            <x-input-error :messages="$errors->get('joined_at')" class="mt-2" />
                        </div>

                    </div>

                    <div class="flex gap-4 mt-4">
                        <x-primary-button>{{ __('Create') }}</x-primary-button>
                        <x-danger-button :href="route('panel.admin.memberships.index')">
                            {{ __('Discard') }}
                        </x-danger-button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-panel-layout>

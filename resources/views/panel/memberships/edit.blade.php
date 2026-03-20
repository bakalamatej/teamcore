<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8"
        x-data="membershipForm"
        x-init="
            selectedClub = @js(old('club_id', (string) $memberClub->club_id));
            previousClub = @js(old('club_id', (string) $memberClub->club_id));
            selectedSport = @js(old('sport_id', (string) $memberClub->sport_id));
                    clubOptions = @js(collect($clubOptions)->mapWithKeys(fn($label, $id) => [(string) $id => $label]));
                    sportsByClub = @js($sportsByClub);
                "
            >
                <div x-effect="syncClubChange()"></div>

                <h1 class="my-heading">{{ __('Edit Membership') }}</h1>
                <p class="my-text mb-4">{{ __('Manage membership details for this user.') }}</p>

                <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">{{ __('Member') }}:</span>
                        {{ $memberClub->member?->full_name ?? '—' }}
                    </p>
                    <p class="text-sm text-gray-500">{{ $memberClub->member?->user?->email ?? '—' }}</p>
                </div>

                <form method="POST" action="{{ route('panel.memberships.update', $memberClub) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="member_id" value="{{ $memberClub->member_id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl">
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
                                :selected="old('role', $memberClub->role->value)"
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
                                :value="old('joined_at', optional($memberClub->joined_at)->format('Y-m-d'))"
                                required
                            />
                            <x-input-error :messages="$errors->get('joined_at')" class="mt-2" />
                        </div>

                    </div>

                    <div class="flex gap-4 mt-4">
                        <x-primary-button>{{ __('Update') }}</x-primary-button>
                        <x-danger-button :href="route('panel.memberships.index')">
                            {{ __('Discard') }}
                        </x-danger-button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-panel-layout>

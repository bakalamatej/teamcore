<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        {{-- Sidebar --}}
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <h1 class="my-heading">{{ __('Club Memberships') }}</h1>
                <p class="my-text">{{ __('Update users memberships and roles.') }}</p>

                {{-- ── PATCH form: existing memberships ── --}}
                @if($allMemberships->isNotEmpty())
                <form method="POST" action="{{ route('panel.memberships.update', $member) }}" class="space-y-4 max-w-2xl mt-6">
                    @csrf
                    @method('PATCH')

                    @foreach($allMemberships as $mc)
                    <div class="max-w-2xl border border-gray-200 rounded-lg p-4 space-y-3 bg-gray-50">
                        <p class="font-semibold text-gray-800">{{ $mc->club->name }}</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label :value="__('Role')" />
                                <x-select-input
                                    name="memberships[{{ $mc->member_club_id }}][role]"
                                    :options="['player' => __('Player'), 'coach' => __('Coach')]"
                                    :selected="old('memberships.' . $mc->member_club_id . '.role', $mc->role->value)"
                                    placeholder=""
                                    class="mt-1 block w-full"
                                />
                                <x-input-error :messages="$errors->get('memberships.' . $mc->member_club_id . '.role')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label :value="__('Sport')" />
                                <x-select-input
                                    name="memberships[{{ $mc->member_club_id }}][sport_id]"
                                    :options="$mc->club->sports->pluck('name', 'sport_id')->toArray()"
                                    :selected="old('memberships.' . $mc->member_club_id . '.sport_id', $mc->sport_id)"
                                    :placeholder="__('Select sport')"
                                    class="mt-1 block w-full"
                                />
                                <x-input-error :messages="$errors->get('memberships.' . $mc->member_club_id . '.sport_id')" class="mt-1" />
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="flex gap-4 pt-2">
                        <x-primary-button>{{ __('Update') }}</x-primary-button>
                        <x-danger-button type="button" onclick="window.location='{{ route('panel.memberships.index') }}'">
                            {{ __('Discard') }}
                        </x-danger-button>
                    </div>
                </form>
                @endif

                {{-- ── Add new club membership ── --}}
                <div
                    class="max-w-2xl mt-10 border-t pt-6"
                    x-data="membershipAddForm"
                    data-sport-ids='@json($memberSportIds->map(fn($id) => (string)$id)->values())'
                    data-clubs='@json($allClubsWithSports)'
                    data-sports='@json($allSports->pluck("name", "sport_id"))'
                >
                    <h2 class="font-semibold text-gray-700 text-base mb-4">{{ __('Add Club Membership') }}</h2>

                    {{-- Sports filter --}}
                    <div class="mb-4">
                        <x-input-label :value="__('Filter clubs by sport')" class="mb-2" />
                        <div class="flex flex-wrap gap-3">
                            @foreach($allSports as $sport)
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    :checked="selectedSports.includes('{{ $sport->sport_id }}')"
                                    @change="toggleSport({{ $sport->sport_id }})"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <span class="text-sm text-gray-700">{{ $sport->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-gray-400">{{ __('Only clubs that share a selected sport are shown below.') }}</p>
                    </div>

                    {{-- Add membership form --}}
                    <form method="POST" action="{{ route('panel.memberships.club.store', $member) }}" class="space-y-3">
                        @csrf

                        @if($errors->has('club_id') || $errors->has('sport_id') || $errors->has('role') || $errors->has('joined_at'))
                            <div class="p-3 bg-red-50 border border-red-200 rounded-md text-sm text-red-700 space-y-1">
                                <x-input-error :messages="$errors->get('club_id')" />
                                <x-input-error :messages="$errors->get('sport_id')" />
                                <x-input-error :messages="$errors->get('role')" />
                                <x-input-error :messages="$errors->get('joined_at')" />
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Club --}}
                            <div x-on:click.outside="openClub = false">
                                <x-input-label :value="__('Club')" />
                                <x-filtered-select
                                    name="club_id"
                                    open-var="openClub"
                                    selected-var="newClubId"
                                    options-var="filteredClubs"
                                    :placeholder="__('Select club…')"
                                />
                            </div>

                            {{-- Sport --}}
                            <div x-on:click.outside="openSport = false">
                                <x-input-label :value="__('Sport')" />
                                <x-filtered-select
                                    name="sport_id"
                                    open-var="openSport"
                                    selected-var="newSportId"
                                    options-var="sportsForNewClub"
                                    disabled-when="!newClubId"
                                    :placeholder="__('Select sport…')"
                                />
                            </div>

                            {{-- Role --}}
                            <div>
                                <x-input-label :value="__('Role')" />
                                <x-select-input
                                    name="role"
                                    :options="['player' => __('Player'), 'coach' => __('Coach')]"
                                    selected="player"
                                    placeholder=""
                                    class="mt-1 block w-full"
                                />
                            </div>

                            {{-- Joined at --}}
                            <div>
                                <x-input-label :value="__('Joined at')" />
                                <x-text-input
                                    name="joined_at"
                                    type="date"
                                    class="mt-1 block w-full"
                                    value="{{ old('joined_at', now()->format('Y-m-d')) }}"
                                    required
                                />
                            </div>
                        </div>

                        <div class="pt-1">
                            <x-primary-button :disabled="false">{{ __('Add Membership') }}</x-primary-button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>
</x-app-layout>

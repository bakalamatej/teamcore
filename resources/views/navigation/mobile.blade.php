@php
$panelRoutes = request()->routeIs('panel.*');
@endphp

<div x-show="open"
        class="absolute top-full left-0 w-full bg-white shadow-xl z-50 rounded-lg overflow-y-auto max-h-[80vh]"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        @click.away="open = false">

    <!-- Links -->
    <div class="pt-2 pb-3">
        @auth
            @if(count($membershipOptions) > 1)
                <form method="POST" action="{{ route('memberships.active.update') }}" class="px-4 pb-1">
                    @csrf
                    <label for="active_member_club_id_mobile" class="block text-xs text-gray-500 mb-1">{{ __('Active membership') }}</label>
                    <div class="w-full">
                        <x-select-refresh
                            id="active_member_club_id_mobile"
                            name="member_club_id"
                            :options="collect($membershipOptions)->mapWithKeys(fn($option) => [$option['id'] => $option['label']])->toArray()"
                            :selected="(string) ($activeMembership?->member_club_id ?? '')"
                            :required="true"
                            :disabled="false"
                            placeholder="{{ __('Choose club & sport') }}"
                            class="w-full text-sm"
                        />
                    </div>
                </form>
            @endif
        @endauth

        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('clubs.my')" :active="request()->routeIs('clubs.my')">
            {{ __('My Club') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')">
            {{ __('Events') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.index')">
            {{ __('Calendar') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('gallery')" :active="request()->routeIs('gallery')">
            {{ __('Media') }}
        </x-responsive-nav-link>
    </div>

    <!-- PANEL LINKS -->
    @auth
        <div>
            @if($panelRoutes)
                <div class="border-t border-gray-200 pt-3 pb-3 mt-2">
                    <x-responsive-nav-link :href="route('panel.update.index')" :active="request()->routeIs('panel.update.index')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('panel.statistics.index')" :active="request()->routeIs('panel.statistics.index')">
                        {{ __('Statistics') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('panel.results.index')" :active="request()->routeIs('panel.results.index')">
                        {{ __('Results') }}
                    </x-responsive-nav-link>

                    @if($role === 'coach')
                        <x-responsive-nav-link :href="route('panel.coach.players.index')" :active="request()->routeIs('panel.coach.players.*')">
                            {{ __('Players') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.coach.events.index')" :active="request()->routeIs('panel.coach.events.*')">
                            {{ __('Events') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.coach.clubs.index')" :active="request()->routeIs('panel.coach.clubs.*')">
                            {{ __('Clubs') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.coach.reservations.index')" :active="request()->routeIs('panel.coach.reservations.*')">
                            {{ __('Reservations') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.coach.my-evaluations.index')" :active="request()->routeIs('panel.coach.my-evaluations.*')">
                            {{ __('My Evaluations') }}
                        </x-responsive-nav-link>
                    @endif

                    @if($role === 'admin')
                        <x-responsive-nav-link :href="route('panel.admin.users.index')" :active="request()->routeIs('panel.admin.users.*')">
                            {{ __('Users') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.admin.memberships.index')" :active="request()->routeIs('panel.admin.memberships.*')">
                            {{ __('Memberships') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.admin.clubs.index')" :active="request()->routeIs('panel.admin.clubs.*')">
                            {{ __('Clubs') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.admin.events.index')" :active="request()->routeIs('panel.admin.events.*')">
                            {{ __('Events') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.admin.reservations.index')" :active="request()->routeIs('panel.admin.reservations.*')">
                            {{ __('Reservations') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.admin.sports.index')" :active="request()->routeIs('panel.admin.sports.*')">
                            {{ __('Sports') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.admin.sport-fields.index')" :active="request()->routeIs('panel.admin.sport-fields.*')">
                            {{ __('Sport Fields') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.admin.field-types.index')" :active="request()->routeIs('panel.admin.field-types.*')">
                            {{ __('Field Types') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.admin.addresses.index')" :active="request()->routeIs('panel.admin.addresses.*')">
                            {{ __('Addresses') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.admin.event-types.index')" :active="request()->routeIs('panel.admin.event-types.*')">
                            {{ __('Event Types') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('panel.admin.coach-evaluations.index')" :active="request()->routeIs('panel.admin.coach-evaluations.*')">
                            {{ __('Coach Evaluations') }}
                        </x-responsive-nav-link>
                    @endif
                </div>
            @endif
        </div>
    @endauth

    <!-- Auth buttons -->
    <div class="flex flex-col px-4 pb-3">
        @auth
            <x-responsive-secondary-button :href="route('panel.update.index')" class="">
                {{ __('Panel') }}
            </x-responsive-secondary-button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-danger-button type="submit" class="bg-red-500 hover:bg-red-700">
                    {{ __('Sign out') }}
                </x-responsive-danger-button>
            </form>
        @else
            <x-responsive-secondary-button :href="route('register')" class="">
                {{ __('Sign up') }}
            </x-responsive-secondary-button>

            <x-responsive-primary-button :href="route('login')" class="">
                {{ __('Sign in') }}
            </x-responsive-primary-button>
        @endauth
    </div>
</div>
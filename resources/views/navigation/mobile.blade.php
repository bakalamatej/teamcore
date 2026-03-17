@php
$panelRoutes = ['panel.index', 'clubs.create', 'events.create'];
@endphp

<div x-show="open"
        class="absolute top-full left-0 w-full bg-white shadow-xl z-50 rounded-lg"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        @click.away="open = false">

    <!-- Links -->
    <div class="block xl-custom:hidden pt-2 pb-3">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('clubs.my')" :active="request()->routeIs('clubs.my')">
            {{ __('My Club') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')">
            {{ __('Events') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('calendar')" :active="request()->routeIs('calendar')">
            {{ __('Calendar') }}
        </x-responsive-nav-link>
        <x-responsive-nav-link :href="route('gallery')" :active="request()->routeIs('gallery')">
            {{ __('Media') }}
        </x-responsive-nav-link>
    </div>

    <!-- PANEL LINKS -->
    @auth
        <div>
            @if(in_array($currentRoute, $panelRoutes))
                <div class="border-t border-gray-200 pt-3 pb-3 mt-2">
                <x-responsive-nav-link :href="route('panel.index')" :active="request()->routeIs('panel.index')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if($role === 'player')
                    <x-responsive-nav-link :href="route('panel.stats')" :active="request()->routeIs('panel.stats')">
                        {{ __('Statistics') }}
                    </x-responsive-nav-link>
                @endif

                @if($role === 'coach')
                    <x-responsive-nav-link :href="route('coach.players')" :active="request()->routeIs('coach.players')">
                        {{ __('Players') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('coach.trainings')" :active="request()->routeIs('coach.trainings')">
                        {{ __('Trainings') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('events.create')" :active="request()->routeIs('events.create')">
                        {{ __('Create event') }}
                    </x-responsive-nav-link>
                @endif

                @if($role === 'admin')
                    <x-responsive-nav-link :href="route('clubs.create')" :active="request()->routeIs('clubs.create')">
                        {{ __('Add club') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('panel.users.index')" :active="request()->routeIs('panel.users.index')">
                        {{ __('Users') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('events.create')" :active="request()->routeIs('events.create')">
                        {{ __('Create event') }}
                    </x-responsive-nav-link>

                @endif  
            @endif
        </div>
    @endauth

    <!-- Auth buttons -->
    <div class="flex flex-col px-4 pb-3">
        @auth
            <x-responsive-secondary-button :href="route('panel.index')" class="">
                {{ __($panelLabel) }}
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
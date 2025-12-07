@php
    $role = Auth::user()->role ?? null;

    $panelLabel = match ($role) {
        'admin' => 'Admin panel',
        'coach' => 'Coach panel',
        default => 'Profile',
    };
@endphp

<nav x-data="{ open: false }" 
        class="fixed top-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-xl z-50 w-[90%] lg:w-[70%]">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between min-h-[80px] items-center">

            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Nav Links  -->
                <div class="hidden xl-custom:flex space-x-8 xl-custom:ms-10">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('clubs.index')" :active="request()->routeIs('clubs.index')">
                        {{ __('Clubs') }}
                    </x-nav-link>
                    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')">
                        {{ __('Events') }}
                    </x-nav-link>
                    <x-nav-link :href="route('calendar')" :active="request()->routeIs('calendar')">
                        {{ __('Calendar') }}
                    </x-nav-link>
                    <x-nav-link :href="route('gallery')" :active="request()->routeIs('gallery')">
                        {{ __('Gallery') }}
                    </x-nav-link>
                    
                </div>

                <x-nav-link href="javascript:void(0)" 
                            @click="open = !open"
                            class="flex xl-custom:hidden ms-4 sm:ms-10">
                    {{ __('Menu') }}
                </x-nav-link>
            </div>

            <!-- Auth Buttons -->
            <div class="hidden xl:flex items-center">
                @auth
                    <x-secondary-button class="ms-3" :href="route('panel.index')">
                        {{ __($panelLabel) }}
                    </x-secondary-button>

                    <form method="POST" action="{{ route('logout') }}" class="ms-3">
                        @csrf
                        <x-danger-button type="submit">
                            {{ __('Sign out') }}
                        </x-danger-button>
                    </form>
                @else
                    <x-secondary-button class="ms-3" :href="route('register')">
                        {{ __('Sign up') }}
                    </x-secondary-button>

                    <x-primary-button class="ms-3" :href="route('login')">
                        {{ __('Sign in') }}
                    </x-primary-button>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="flex items-center xl:hidden">
                <button @click="open = ! open"
                        class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open}"
                                class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open}"
                                class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Responsive menu -->
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
            <x-responsive-nav-link :href="route('clubs.index')" :active="request()->routeIs('clubs.index')">
                {{ __('Clubs') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')">
                {{ __('Events') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('calendar')" :active="request()->routeIs('calendar')">
                {{ __('Calendar') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('gallery')" :active="request()->routeIs('gallery')">
                {{ __('Gallery') }}
            </x-responsive-nav-link>
        </div>

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
</nav>

<div class="w-[90%] lg:w-[70%] left-1/2 transform -translate-x-1/2 fixed h-[80px] w-full bg-neutral-300/30 backdrop-blur-sm"></div>
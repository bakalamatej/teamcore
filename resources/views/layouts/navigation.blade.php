<nav x-data="{ open: false }" class="bg-white mx-auto shadow-sm rounded-lg mt-6">

    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">

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

                    <x-nav-link :href="route('club')" :active="request()->routeIs('club')">
                        {{ __('Club') }}
                    </x-nav-link>

                    <x-nav-link :href="route('events')" :active="request()->routeIs('events')">
                        {{ __('Events') }}
                    </x-nav-link>

                    <x-nav-link :href="route('calendar')" :active="request()->routeIs('calendar')">
                        {{ __('Calendar') }}
                    </x-nav-link>

                    <x-nav-link :href="route('gallery')" :active="request()->routeIs('gallery')">
                        {{ __('Gallery') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Auth Buttons -->
            <div class="hidden xl-custom:flex items-center xl-custom:ms-6">
                @auth
                    <x-secondary-button class="ms-3" :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-secondary-button>

                    <form method="POST" action="{{ route('logout') }}" class="ms-3">
                        @csrf
                        <x-primary-button type="submit" class="bg-red-500 hover:bg-red-700">
                            {{ __('Sign out') }}
                        </x-primary-button>
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
            <div class="flex items-center xl-custom:hidden">
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
    <div :class="{'block': open, 'hidden': ! open}" class="hidden xl-custom:hidden">

        <!-- Links -->
        <div class="pt-2 pb-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('club')" :active="request()->routeIs('club')">
                {{ __('Club') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('events')" :active="request()->routeIs('events')">
                {{ __('Events') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('calendar')" :active="request()->routeIs('calendar')">
                {{ __('Calendar') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('gallery')" :active="request()->routeIs('gallery')">
                {{ __('Gallery') }}
            </x-responsive-nav-link>
        </div>

        <!-- Auth -->
        <div class="flex flex-col px-4 pb-3">
            @auth
                <x-responsive-secondary-button :href="route('profile.edit')" class="">
                    {{ __('Profile') }}
                </x-responsive-secondary-button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-primary-button type="submit" class="bg-red-500 hover:bg-red-700">
                        {{ __('Sign out') }}
                    </x-responsive-primary-button>
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

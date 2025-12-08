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
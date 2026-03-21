@php
    $user = Auth::user();
    $role = $user?->getRole();
    $activeMembership = $user?->activeMembership();
    $membershipOptions = $user?->availableMembershipOptions() ?? [];
    $currentRoute = Route::currentRouteName();

    $panelLabel = match ($role) {
        'admin' => 'Admin panel',
        'coach' => 'Coach panel',
        default => 'Profile',
    };
@endphp

<nav x-data="{ open: false }" 
        class="fixed top-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-xl z-50 w-[90%] lg:w-[70%]">
    @include('navigation.desktop')
    <div x-show="open" class="block xl:hidden" style="display: none;">
        @include('navigation.mobile')
    </div>
</nav>

<div class="w-[90%] lg:w-[70%] left-1/2 transform -translate-x-1/2 fixed h-[80px] w-full bg-neutral-300/30 backdrop-blur-sm"></div>
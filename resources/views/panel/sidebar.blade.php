@php
    $role = Auth::user()->role ?? null;

    $panelLabel = match ($role) {
        'admin' => 'Admin panel',
        'coach' => 'Coach panel',
        default => 'Profile',
    };
@endphp

<aside class="fixed w-64 bg-white h-[calc(100vh-11rem)] shadow-xl rounded-lg p-4 sm:p-8">
    <div class="sidebar-heading ">
        {{ __($panelLabel) }}
    </div>

    <nav class="flex flex-col">

        @if($role === 'player')
            <a href="{{ route('panel.index') }}" class="sidebar-link">{{ __('Profile') }}</a>
            <a href="{{ route('panel.stats') }}" class="sidebar-link">{{ __('Statistics') }}</a>
        @endif
            
        @if($role === 'coach')
            <a href="{{ route('coach.players') }}" class="sidebar-link">{{ __('Players') }}</a>
            <a href="{{ route('coach.trainings') }}" class="sidebar-link">{{ __('Trainings') }}</a>
            <a href="{{ route('coach.events') }}" class="sidebar-link">{{ __('Events') }}</a>
        @endif

        @if($role === 'admin')
            <a href="{{ route('admin.clubs') }}" class="sidebar-link">{{ __('Clubs') }}</a>
            <a href="{{ route('admin.users') }}" class="sidebar-link">{{ __('Users') }}</a>
            <a href="{{ route('admin.events') }}" class="sidebar-link">{{ __('Events') }}</a>
            <a href="{{ route('admin.types') }}" class="sidebar-link">{{ __('Event Types') }}</a>
            <a href="{{ route('admin.fields') }}" class="sidebar-link">{{ __('Fields') }}</a>
        @endif

    </nav>

</aside>

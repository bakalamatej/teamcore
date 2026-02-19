@php
    // Determine user role for conditional menu display
    $role = Auth::user()->getRole() ?? 'player';
    $panelLabel = match ($role) {
        'admin' => 'Admin panel',
        'coach' => 'Coach panel',
        default => 'Profile',
    };
@endphp

<x-sidebar :title="__($panelLabel)">
    <!-- Profile link (all users) -->
    <a href="{{ route('panel.index') }}" class="sidebar-link">{{ __('Profile') }}</a>

    <!-- Player menu: statistics -->
    @if($role === 'player')
        <a href="{{ route('panel.stats') }}" class="sidebar-link">{{ __('Statistics') }}</a>
    @endif
        
    <!-- Coach menu: players, trainings, create event -->
    @if($role === 'coach')
        <a href="{{ route('coach.players') }}" class="sidebar-link">{{ __('Players') }}</a>
        <a href="{{ route('coach.trainings') }}" class="sidebar-link">{{ __('Trainings') }}</a>
        <a href="{{ route('events.create') }}" class="sidebar-link">{{ __('Create event') }}</a>
    @endif

    <!-- Admin menu: clubs, users, events, types, fields -->
    @if($role === 'admin')
        <a href="{{ route('clubs.create') }}" class="sidebar-link">{{ __('Add club') }}</a>
        <a href="{{ route('panel.users.index') }}" class="sidebar-link">{{ __('Users') }}</a>
        <a href="{{ route('panel.clubs.index') }}" class="sidebar-link">{{ __('Clubs') }}</a>
        <a href="{{ route('events.create') }}" class="sidebar-link">{{ __('Create event') }}</a>
    @endif
</x-sidebar>
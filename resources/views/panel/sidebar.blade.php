@php
    $role = Auth::user()->role ?? null;
    $panelLabel = match ($role) {
        'admin' => 'Admin panel',
        'coach' => 'Coach panel',
        default => 'Profile',
    };
@endphp

<x-sidebar :title="__($panelLabel)">
    <a href="{{ route('panel.index') }}" class="sidebar-link">{{ __('Profile') }}</a>

    @if($role === 'player')
        <a href="{{ route('panel.stats') }}" class="sidebar-link">{{ __('Statistics') }}</a>
    @endif
        
    @if($role === 'coach')
        <a href="{{ route('coach.players') }}" class="sidebar-link">{{ __('Players') }}</a>
        <a href="{{ route('coach.trainings') }}" class="sidebar-link">{{ __('Trainings') }}</a>
        <a href="{{ route('events.create') }}" class="sidebar-link">{{ __('Create event') }}</a>
    @endif

    @if($role === 'admin')
        <a href="{{ route('clubs.create') }}" class="sidebar-link">{{ __('Add club') }}</a>
        <a href="{{ route('panel.users.index') }}" class="sidebar-link">{{ __('Users') }}</a>
        <a href="{{ route('events.create') }}" class="sidebar-link">{{ __('Create event') }}</a>
        <a href="{{ route('admin.types') }}" class="sidebar-link">{{ __('Event Types') }}</a>
        <a href="{{ route('admin.fields') }}" class="sidebar-link">{{ __('Fields') }}</a>
    @endif
</x-sidebar>
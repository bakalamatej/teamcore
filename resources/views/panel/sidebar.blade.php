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
        <a href="{{ route('panel.events.create') }}" class="sidebar-link">{{ __('Create event') }}</a>
    @endif

    <!-- Admin menu: users, clubs, events -->
    @if($role === 'admin')
        <a href="{{ route('panel.users.index') }}" class="sidebar-link">{{ __('Users') }}</a>
        <a href="{{ route('panel.memberships.index') }}" class="sidebar-link">{{ __('Memberships') }}</a>
        <a href="{{ route('panel.clubs.index') }}" class="sidebar-link">{{ __('Clubs') }}</a>
        <a href="{{ route('panel.events.index') }}" class="sidebar-link">{{ __('Events') }}</a>
        <a href="{{ route('panel.reservations.index') }}" class="sidebar-link">{{ __('Reservations') }}</a>
        <a href="{{ route('panel.sports.index') }}" class="sidebar-link">{{ __('Sports') }}</a>
        <a href="{{ route('panel.sport-fields.index') }}" class="sidebar-link">{{ __('Sport Fields') }}</a>
        <a href="{{ route('panel.field-types.index') }}" class="sidebar-link">{{ __('Field Types') }}</a>
        <a href="{{ route('panel.addresses.index') }}" class="sidebar-link">{{ __('Addresses') }}</a>
        <a href="{{ route('panel.event-types.index') }}" class="sidebar-link">{{ __('Event Types') }}</a>
        <a href="{{ route('panel.coach-evaluations.index') }}" class="sidebar-link">{{ __('Coach Evaluations') }}</a>
    @endif
</x-sidebar>
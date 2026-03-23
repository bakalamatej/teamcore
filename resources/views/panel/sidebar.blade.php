<x-sidebar :title="__($panelLabel)">
    <div class="flex flex-col flex-1">
        <div class="flex flex-col gap-1">
            <a href="{{ route('panel.update.index') }}" class="sidebar-link">{{ __('Profile') }}</a>
            <a href="{{ route('panel.statistics.index') }}" class="sidebar-link">{{ __('Statistics') }}</a>
            <a href="{{ route('panel.results.index') }}" class="sidebar-link">{{ __('Results') }}</a>
            <!-- Coach menu: players, trainings, create event -->
            @if($role === 'coach')
                <a href="{{ route('panel.coach.players.index') }}" class="sidebar-link">{{ __('Players') }}</a>
                <a href="{{ route('panel.coach.events.index') }}" class="sidebar-link">{{ __('Events') }}</a>
                <a href="{{ route('panel.coach.clubs.index') }}" class="sidebar-link">{{ __('Clubs') }}</a>
                <a href="{{ route('panel.coach.reservations.index') }}" class="sidebar-link">{{ __('Reservations') }}</a>
                <a href="{{ route('panel.coach.my-evaluations.index') }}" class="sidebar-link">{{ __('My Evaluations') }}</a>
            @endif
            <!-- Admin menu: users, clubs, events -->
            @if($role === 'admin')
                <a href="{{ route('panel.admin.users.index') }}" class="sidebar-link">{{ __('Users') }}</a>
                <a href="{{ route('panel.admin.memberships.index') }}" class="sidebar-link">{{ __('Memberships') }}</a>
                <a href="{{ route('panel.admin.clubs.index') }}" class="sidebar-link">{{ __('Clubs') }}</a>
                <a href="{{ route('panel.admin.events.index') }}" class="sidebar-link">{{ __('Events') }}</a>
                <a href="{{ route('panel.admin.reservations.index') }}" class="sidebar-link">{{ __('Reservations') }}</a>
                <a href="{{ route('panel.admin.sports.index') }}" class="sidebar-link">{{ __('Sports') }}</a>
                <a href="{{ route('panel.admin.sport-fields.index') }}" class="sidebar-link">{{ __('Sport Fields') }}</a>
                <a href="{{ route('panel.admin.field-types.index') }}" class="sidebar-link">{{ __('Field Types') }}</a>
                <a href="{{ route('panel.admin.addresses.index') }}" class="sidebar-link">{{ __('Addresses') }}</a>
                <a href="{{ route('panel.admin.event-types.index') }}" class="sidebar-link">{{ __('Event Types') }}</a>
                <a href="{{ route('panel.admin.coach-evaluations.index') }}" class="sidebar-link">{{ __('Coach Evaluations') }}</a>
            @endif
        </div>
        <div class="mt-auto mb-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-danger-button type="submit" class="w-full text-center">
                    <span class="w-full block text-center">{{ __('Sign out') }}</span>
                </x-danger-button>
            </form>
        </div>
    </div>
</x-sidebar>
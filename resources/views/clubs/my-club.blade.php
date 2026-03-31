<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
    <!-- Header -->
        <div class="mb-4 pb-4 border-b-2 border-gray-200">
            <h1 class="my-heading text-3xl mb-2">{{ $club->name }}</h1>
            <div class="flex items-center">
                <div>
                    <p class="text-gray-600">{{ __('Created') }}: {{ $club->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <div class="ml-auto">
                    <x-file-upload 
                        model-type="club" 
                        :model-id="$club->club_id"
                        :categories="$fileCategories"
                    />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Club Details -->
                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Club Information') }}</h2>
                    
                    <div class="space-y-4">
                        <!-- Sport -->
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Sport:') }}</span>
                            <span class="detail-item-value">{{ $club->sport?->name ?? __('N/A') }}</span>
                        </div>
                        <!-- Email -->
                        @if($club->email)
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Email:') }}</span>
                                <a href="mailto:{{ $club->email }}" class="detail-list-item-link">
                                    {{ $club->email }}
                                </a>
                            </div>
                        @endif 

                        <!-- Phone -->
                        @if($club->phone)
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Phone:') }}</span>
                                <a href="tel:{{ $club->phone }}" class="detail-list-item-link">
                                    {{ $club->phone }}
                                </a>
                            </div>
                        @endif

                        <!-- Website -->
                        @if($club->webpage)
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Website:') }}</span>
                                <a href="{{ $club->webpage }}" target="_blank" class="detail-list-item-link">
                                    {{ $club->webpage }}
                                </a>
                            </div>
                        @endif

                        <!-- Location -->
                        <div class="detail-item-divider">
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Location:') }}</span>
                                <div class="detail-item-value">
                                    <p class="text-gray-900">{{ $club->address?->street ?? __('N/A') }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $club->address?->zip_code ?? '' }} 
                                        {{ $club->address?->city ?? '' }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $club->address?->country ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Members -->
                <div class="detail-card">
                    <h2 class="detail-card-header">
                        {{ __('Members') }}
                    </h2>
                    
                    @if($activeMembersCount > 0)
                        <div class="overflow-x-auto">
                            <table class="data-table w-full text-left">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Role') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeMembers as $member)
                                        <tr class="data-row border-b">
                                            <td class="py-3 px-4">
                                                <span class="font-medium">{{ $member->full_name ?? $member->user?->name ?? __('N/A') }}</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <a href="mailto:{{ $member->user?->email }}" class="text-indigo-600 hover:text-indigo-800">
                                                    {{ $member->user?->email ?? '-' }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-4">{{ $member->phone ?? '-' }}</td>
                                            <td class="py-3 px-4">
                                                <span class="position-badge 
                                                    {{ $member->roleValue === 'coach' ? 'position-coach' : 'position-player' }}">
                                                    {{ ucfirst($member->roleValue ?? 'player') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-4">
                                {{ $activeMembers->links() }}
                            </div>
                        </div>
                    @else 
                        <p class="text-gray-600">{{ __('No members in this club') }}</p>
                    @endif
                </div>

                <!-- Recent Events -->
                <div class="detail-card">
                    <h2 class="detail-card-header">
                        {{ __('Recent Events') }}
                    </h2>
                    
                    @if($activeEventsCount > 0)
                        <div class="space-y-2">
                            @foreach($recentEvents as $event)
                                <div class="detail-list-item" style="display: block;">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <a href="{{ route('events.show', $event) }}" class="detail-list-item-link">
                                                {{ $event->title }}
                                            </a>
                                            <p class="detail-list-secondary" style="font-size: 0.75rem;">
                                                {{ $event->start_date->format('d.m.Y H:i') }}
                                            </p>
                                        </div>
                                        <span class="status-badge
                                            {{ $event->statusValue === 'finished' ? 'status-finished' : 
                                                ($event->statusValue === 'canceled' ? 'status-cancelled' : 'status-scheduled') }}">
                                            {{ ucfirst($event->statusValue) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No events found') }}</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 flex flex-col">
                <!-- Club Statistics -->
                <div class="sidebar-card sidebar-card-indigo">
                    <div class="space-y-2">
                         <div>
                            <p class="stat-label" style="color: #3730a3;">{{ __('Active Members') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $stats?->active_members ?? 0 }}</p>
                        </div>
                        <div class="stat-divider" style="border-top-color: #c7d2fe;">
                            <p class="stat-label" style="color: #3730a3;">{{ __('Total Coaches') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $stats?->total_coaches ?? 0 }}</p>
                        </div>
                        <div class="stat-divider" style="border-top-color: #c7d2fe;">
                            <p class="stat-label" style="color: #3730a3;">{{ __('Matches Played') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $stats?->matches_played ?? 0 }}</p>
                        </div>
                        <div class="stat-divider" style="border-top-color: #c7d2fe;">
                            <p class="stat-label" style="color: #3730a3;">{{ __('Tournaments Attended') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $stats?->tournaments_attended ?? 0 }}</p>
                        </div>
                        <div class="stat-divider" style="border-top-color: #c7d2fe;">
                            <p class="stat-label" style="color: #3730a3;">{{ __('Total Wins') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $stats?->total_wins ?? 0 }}</p>
                        </div>
                        <div class="stat-divider" style="border-top-color: #c7d2fe;">
                            <p class="stat-label" style="color: #3730a3;">{{ __('Total Losses') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $stats?->total_losses ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Coaches -->
                <div class="sidebar-card sidebar-card-gray">
                    <h3 class="sidebar-card-title">{{ __('Coaches') }}</h3>
                    @if($coaches->count() > 0)
                        <div class="space-y-2">
                            @foreach($coaches as $coachMembership)
                            <div class="detail-list-item flex justify-between items-center">
                                <div>
                                    <a href="{{ route('members.showCoach', $coachMembership->member) }}"
                                        class="inline-flex items-center gap-1 font-medium text-sm text-indigo-600">
                                        {{ $coachMembership->member?->full_name }}
                                    </a>
                                    <p class="text-xs text-gray-600">{{ $coachMembership->member?->user?->email }}</p>
                                </div>
                                <a href="#"
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'rate-coach-{{ $coachMembership->member_club_id }}')"
                                    style="color: #2563eb; text-decoration: none; font-size: 0.875rem; cursor: pointer;"
                                    onmouseover="this.style.textDecoration='underline'"
                                    onmouseout="this.style.textDecoration='none'">
                                    {{ __('Rate') }}
                                </a>
                            </div>

                            <x-modal name="rate-coach-{{ $coachMembership->member_club_id }}" :show="false" focusable>
                                <form method="POST" action="{{ route('clubs.coach.rate', [$club, $coachMembership->member_club_id]) }}" class="p-6">
                                    @csrf
                                    <input type="hidden" name="coach_member_id" value="{{ $coachMembership->member_id }}">
                                    <h2 class="my-heading">{{ __('Rate Coach') }}: {{ $coachMembership->member?->full_name }}</h2>
                                    <p class="my-text mb-4">{{ __('Please provide your rating and comment.') }}</p>

                                    <div class="flex flex-col gap-4 mt-4">
                                        <div>
                                            <x-input-label for="rating_{{ $coachMembership->member_club_id }}" :value="__('Rating (1-5)')" />
                                            <x-text-input
                                                id="rating_{{ $coachMembership->member_club_id }}"
                                                name="rating"
                                                type="number"
                                                min="1"
                                                max="5"
                                                step="0.1"
                                                class="mt-1 block w-full"
                                                placeholder="1-5"
                                            />
                                        </div>
                                        <div>
                                            <x-input-label for="comment_{{ $coachMembership->member_club_id }}" :value="__('Comment')" />
                                            <textarea
                                                id="comment_{{ $coachMembership->member_club_id }}"
                                                name="comment"
                                                rows="3"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                placeholder="{{ __('Your comment...') }}"
                                            ></textarea>
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-3 mt-6">
                                        <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                            {{ __('Discard') }}
                                        </x-secondary-button>
                                        <x-primary-button type="submit">
                                            {{ __('Save') }}
                                        </x-primary-button>
                                    </div>
                                </form>
                            </x-modal>
                        @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-600">{{ __('No coaches assigned') }}</p>
                    @endif
                </div>
            </div>
        </div>  
    </div>
</x-app-layout>
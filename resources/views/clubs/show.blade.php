<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8 pb-12">
        <!-- Header -->
        <div class="mb-8 pb-6 border-b-2 border-gray-200">
            <h1 class="my-heading text-3xl mb-2">{{ $club->name }}</h1>
            <p class="text-gray-600">{{ __('Created') }}: {{ $club->created_at->format('d.m.Y H:i') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Club Details -->
                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Club Information') }}</h2>
                    
                    <div class="space-y-4">
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
                        @if($club->website)
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Website:') }}</span>
                                <a href="{{ $club->website }}" target="_blank" class="detail-list-item-link">
                                    {{ $club->website }}
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

                        <!-- Description -->
                        @if($club->description)
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                                <span class="detail-item-label block mb-2">{{ __('Description:') }}</span>
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $club->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Active Members -->
                <div class="detail-card">
                    <h2 class="detail-card-header">
                        {{ __('Members') }} ({{ $club->activeMembers->count() }})
                    </h2>
                    
                    @if($club->activeMembers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="data-table w-full text-left">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Position') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($club->activeMembers as $member)
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
                                                    {{ $member->position === 'coach' ? 'position-coach' : 'position-player' }}">
                                                    {{ ucfirst($member->position ?? 'player') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No members in this club') }}</p>
                    @endif
                </div>

                <!-- Recent Events -->
                <div class="detail-card">
                    <h2 class="detail-card-header">
                        {{ __('Recent Events') }} ({{ $club->activeEvents->count() }})
                    </h2>
                    
                    @if($club->activeEvents->count() > 0)
                        <div class="space-y-2">
                            @foreach($club->activeEvents->take(5) as $event)
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
                                            {{ $event->status === 'finished' ? 'status-finished' : 
                                               ($event->status === 'cancelled' ? 'status-cancelled' : 'status-scheduled') }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($club->activeEvents->count() > 5)
                            <p class="text-sm text-gray-600 mt-3">{{ __('and') }} {{ $club->activeEvents->count() - 5 }} {{ __('more...') }}</p>
                        @endif
                    @else
                        <p class="text-gray-600">{{ __('No events found') }}</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Club Statistics -->
                <div class="sidebar-card sidebar-card-indigo">
                    <h3 class="sidebar-card-title">{{ __('Statistics') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="stat-label" style="color: #3730a3;">{{ __('Total Members') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $club->activeMembers->count() }}</p>
                        </div>
                        <div class="stat-divider" style="border-top-color: #c7d2fe;">
                            <p class="stat-label" style="color: #3730a3;">{{ __('Total Events') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $club->activeEvents->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Coaches -->
                <div class="sidebar-card sidebar-card-gray">
                    <h3 class="sidebar-card-title">{{ __('Coaches') }}</h3>
                    @php
                        $coaches = $club->activeMembers->where('position', 'coach');
                    @endphp
                    
                    @if($coaches->count() > 0)
                        <div class="space-y-2">
                            @foreach($coaches as $coach)
                                <div class="p-2 bg-white rounded border border-gray-200">
                                    <p class="font-medium text-sm text-gray-900">{{ $coach->full_name ?? $coach->user?->name }}</p>
                                    <p class="text-xs text-gray-600">{{ $coach->user?->email }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-600">{{ __('No coaches assigned') }}</p>
                    @endif
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <x-primary-button class="w-full justify-center" :href="route('clubs.edit', $club)">
                                {{ __('Edit Club') }}
                            </x-primary-button>

                            <x-danger-button type="button" class="w-full justify-center"
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'confirm-club-deletion-{{ $club->id }}')">
                                {{ __('Delete Club') }}
                            </x-danger-button>

                            <x-modal name="confirm-club-deletion-{{ $club->id }}" :show="false" focusable>
                                <form method="POST" action="{{ route('clubs.destroy', $club) }}" class="p-6 text-left">
                                    @csrf
                                    @method('DELETE')

                                    <h2 class="my-heading">{{ __('Delete Club') }}</h2>
                                    <p class="my-text">{{ __('Are you sure you want to delete this club? This action cannot be undone.') }}</p>

                                    <div class="flex justify-end gap-3 mt-6">
                                        <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                            {{ __('Cancel') }}
                                        </x-secondary-button>

                                        <x-danger-button type="submit">
                                            {{ __('Delete') }}
                                        </x-danger-button>
                                    </div>
                                </form>
                            </x-modal>
                        @endif
                    @endauth

                    <x-secondary-button class="w-full justify-center" :href="route('clubs.index')">
                        {{ __('Back to Clubs') }}
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

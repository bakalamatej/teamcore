<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <!-- Header -->
        <div class="mb-8 pb-6 border-b-2 border-gray-200">
            <h1 class="my-heading text-3xl mb-2">{{ $event->title }}</h1>
            <div class="flex items-center gap-4">
                <span class="px-3 py-1 rounded-full text-sm font-semibold 
                    {{ $event->status === \App\Enums\EventStatus::FINISHED ? 'bg-gray-200 text-gray-800' : 
                       ($event->status === \App\Enums\EventStatus::CANCELED ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800') }}">
                    {{ ucfirst($event->status->value) }}
                </span>
                <span class="text-gray-600 text-sm">{{ __('Created') }}: {{ $event->created_at->format('d.m.Y H:i') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Event Details -->
                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Event Details') }}</h2>
                    
                    <div class="space-y-4">
                        <!-- Type -->
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Type:') }}</span>
                            <span class="detail-item-value">{{ $event->eventType?->name ?? __('N/A') }}</span>
                        </div>

                        <!-- Location -->
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Location:') }}</span>
                            <div class="detail-item-value">
                                <p class="text-gray-900">{{ $event->sportField?->name ?? __('N/A') }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $event->sportField?->address?->street ?? '' }}
                                    @if($event->sportField?->address?->street)
                                        <br>
                                    @endif
                                    {{ $event->sportField?->address?->zip_code ?? '' }} 
                                    {{ $event->sportField?->address?->city ?? '' }}
                                </p>
                            </div>
                        </div>

                        <!-- Field Type -->
                        @if($event->sportField?->field_type)
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Field Type:') }}</span>
                                <span class="detail-item-value">{{ $event->sportField->field_type }}</span>
                            </div>
                        @endif

                        <!-- Dates -->
                        <div class="detail-item-divider">
                            <div class="flex justify-between items-start mb-3">
                                <span class="detail-item-label">{{ __('Start:') }}</span>
                                <span class="detail-item-value">{{ $event->start_date?->format('d.m.Y H:i') ?? __('N/A') }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('End:') }}</span>
                                <span class="detail-item-value">{{ $event->end_date?->format('d.m.Y H:i') ?? __('N/A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($event->description)
                    <div class="detail-card">
                        <h2 class="detail-card-header">{{ __('Description') }}</h2>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $event->description }}</p>
                    </div>
                @endif

                <!-- Participating Clubs -->
                <div class="detail-card">
                    <h2 class="detail-card-header">
                        {{ __('Participating Clubs') }} ({{ $event->activeClubs->count() }})
                    </h2>
                    
                    @if($event->activeClubs->count() > 0)
                        <div class="space-y-2">
                            @foreach($event->activeClubs as $club)
                                <div class="detail-list-item">
                                    <a href="{{ route('clubs.show', $club) }}" class="detail-list-item-link">
                                        {{ $club->name }}
                                    </a>
                                    <span class="detail-list-secondary">{{ $club->address?->city ?? '-' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No clubs registered for this event') }}</p>
                    @endif
                </div>

                <!-- Participating Members -->
                <div class="detail-card">
                    <h2 class="detail-card-header">
                        {{ __('Participating Members') }} ({{ $event->activeMembers->count() }})
                    </h2>
                    
                    @if($event->activeMembers->count() > 0)
                        <div class="space-y-2">
                            @foreach($event->activeMembers as $member)
                                <div class="detail-list-item">
                                    <span class="font-medium text-gray-900">{{ $member->full_name }}</span>
                                    <span class="detail-list-secondary">{{ $member->user?->email ?? 'N/A' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No members registered for this event') }}</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 flex flex-col">
                <!-- Event Duration -->
                <div class="sidebar-card sidebar-card-blue">
                    <h3 class="sidebar-card-title">{{ __('Duration') }}</h3>
                    <p class="stat-value" style="color: #2563eb;">
                        @php
                            $duration = $event->start_date->diff($event->end_date);
                            if ($duration->days > 0) {
                                echo $duration->days . ' ' . __('day(s)');
                            } else {
                                echo $duration->h . 'h ' . $duration->i . 'm';
                            }
                        @endphp
                    </p>
                </div>

                <!-- Statistics -->
                <div class="sidebar-card sidebar-card-gray">
                    <h3 class="sidebar-card-title">{{ __('Statistics') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="stat-label">{{ __('Clubs') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $event->activeClubs->count() }}</p>
                        </div>
                        <div class="stat-divider">
                            <p class="stat-label">{{ __('Members') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $event->activeMembers->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3 mt-auto">
                    @auth
                        @if(auth()->user()->isAdmin() || auth()->user()->isCoach())
                            <x-primary-button class="w-full justify-center" :href="route('events.edit', $event)">
                                {{ __('Edit Event') }}
                            </x-primary-button>

                            <x-danger-button type="button" class="w-full justify-center"
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'confirm-event-deletion-{{ $event->id }}')">
                                {{ __('Delete Event') }}
                            </x-danger-button>

                            <x-modal name="confirm-event-deletion-{{ $event->id }}" :show="false" focusable>
                                <form method="POST" action="{{ route('events.destroy', $event) }}" class="p-6 text-left">
                                    @csrf
                                    @method('DELETE')

                                    <h2 class="my-heading">{{ __('Delete Event') }}</h2>
                                    <p class="my-text">{{ __('Are you sure you want to delete this event? This action cannot be undone.') }}</p>

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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

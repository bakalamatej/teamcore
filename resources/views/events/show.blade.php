<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <!-- Header -->
        <div class="mb-4 pb-4 border-b-2 border-gray-200">
            <h1 class="my-heading text-3xl mb-2">{{ $event->title }}</h1>
            <div class="flex items-center gap-4">
                <span @class([
                    'px-3 py-1 rounded-full text-sm font-semibold',
                    'bg-gray-200 text-gray-800' => $statusValue === 'finished',
                    'bg-green-200 text-green-800' => !in_array($statusValue, ['finished'], true),
                ])>
                    {{ ucfirst($statusValue) }}
                </span>
                <span class="text-gray-600 text-sm">{{ __('Created') }}: {{ $event->created_at->format('d.m.Y H:i') }}</span>
                <div class="ml-auto flex items-center gap-2">
                    <x-file-upload 
                        model-type="event" 
                        :model-id="$event->event_id"
                        :categories="$fileCategories"
                        :can-upload="$canManageEvent"
                    />

                    @if($event->status === \App\Enums\EventStatus::FINISHED)
                    <x-secondary-button type="button" class="ml-auto" x-data x-on:click="$dispatch('open-modal', 'event-results-{{ $event->event_id }}')">
                        {{ __('Show Results') }}
                    </x-secondary-button>

                    <x-modal name="event-results-{{ $event->event_id }}" :show="false" focusable>
                        <div class="p-6 space-y-6 max-h-[80vh] overflow-y-auto">
                            <h2 class="my-heading">{{ __('Event Results') }}: {{ $event->title }}</h2>

                            {{-- Club Results --}}
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">{{ __('Club Results') }}</h3>
                                @php($clubResults = $event->clubResults->sortBy('ranking'))
                                @if($clubResults->isNotEmpty())
                                    <div class="border border-gray-200 rounded-md overflow-auto max-h-60">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50">
                                                <tr class="border-b">
                                                    <th class="p-3 text-left">{{ __('Rank') }}</th>
                                                    <th class="p-3 text-left">{{ __('Club') }}</th>
                                                    <th class="p-3 text-left">{{ __('Score') }}</th>
                                                    <th class="p-3 text-left">{{ __('Note') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($clubResults as $result)
                                                    <tr class="border-b hover:bg-gray-50">
                                                        <td class="p-3 font-medium">{{ $result->ranking ?? '-' }}</td>
                                                        <td class="p-3">{{ $result->club?->name ?? '-' }}</td>
                                                        <td class="p-3">{{ $result->value ?? '-' }}</td>
                                                        <td class="p-3 text-gray-600">{{ $result->note ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">{{ __('No club results available.') }}</p>
                                @endif
                            </div>

                            {{-- Member Results --}}
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">{{ __('Member Results') }}</h3>
                                @php($memberResults = $event->memberResults->sortBy('ranking'))
                                @if($memberResults->isNotEmpty())
                                    <div class="border border-gray-200 rounded-md overflow-auto max-h-100">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50">
                                                <tr class="border-b">
                                                    <th class="p-3 text-left">{{ __('Rank') }}</th>
                                                    <th class="p-3 text-left">{{ __('Member') }}</th>
                                                    <th class="p-3 text-left">{{ __('Club') }}</th>
                                                    <th class="p-3 text-left">{{ __('Score') }}</th>
                                                    <th class="p-3 text-left">{{ __('Note') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($memberResults as $result)
                                                    <tr class="border-b hover:bg-gray-50">
                                                        <td class="p-3 font-medium">{{ $result->ranking ?? '-' }}</td>
                                                        <td class="p-3">{{ $result->memberClub?->member?->full_name ?? '-' }}</td>
                                                        <td class="p-3 text-gray-600">{{ $result->memberClub?->club?->name ?? '-' }}</td>
                                                        <td class="p-3">{{ $result->value ?? '-' }}</td>
                                                        <td class="p-3 text-gray-600">{{ $result->note ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">{{ __('No member results available.') }}</p>
                                @endif
                            </div>

                            <div class="flex justify-end">
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Close') }}</x-secondary-button>
                            </div>
                        </div>
                    </x-modal>
                @endif
                </div>
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
                        {{ __('Participating Clubs') }}
                    </h2>
                    
                    @if($activeClubsCount > 0)
                        <div class="space-y-2">
                            @foreach($activeClubs as $club)
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
                        {{ __('Participating Members') }}
                    </h2>
                    
                    @if($activeMembersCount > 0)
                        <div class="space-y-2">
                            @foreach($activeMembers as $member)
                                <div class="detail-list-item">
                                    <span class="font-medium text-gray-900">{{ $member->full_name }}</span>
                                    <span class="detail-list-secondary">{{ $member->user?->email ?? 'N/A' }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $activeMembers->links() }}
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
                    <p class="stat-value" style="color: #2563eb;">{{ $durationText }}</p>
                </div>

                <!-- Statistics -->
                <div class="sidebar-card sidebar-card-gray">
                    <h3 class="sidebar-card-title">{{ __('Statistics') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="stat-label">{{ __('Clubs') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $statisticsClubsCount }}</p>
                        </div>
                        <div class="stat-divider">
                            <p class="stat-label">{{ __('Participants') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $statisticsMembersCount }}</p>
                        </div>
                    </div>
                </div>
                <!-- Coaches -->
                @if($activeCoaches->isNotEmpty())
                    <div class="sidebar-card sidebar-card-gray">
                        <h3 class="sidebar-card-title">{{ __('Coaches') }}</h3>
                        <div class="space-y-2">
                            @foreach($activeCoaches as $coachMembership)
                                <div class="detail-list-item flex justify-between items-center">
                                    <p class="text-sm text-gray-700 font-medium">{{ $coachMembership->member->full_name }}</p>
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
                                    <form method="POST" action="{{ route('events.coach.rate', [$event, $coachMembership->member_id]) }}" class="p-6">
                                        @csrf
                                        <input type="hidden" name="coach_member_id" value="{{ $coachMembership->member_id }}">
                                        <h2 class="my-heading">{{ __('Rate Coach') }}: {{ $coachMembership->member->full_name }}</h2>
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
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</x-app-layout>

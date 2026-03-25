<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <!-- Header -->
        <div class="mb-4 pb-4 border-b-2 border-gray-200">
            <h1 class="my-heading text-3xl mb-2">{{ $tournament->title }}</h1>
            <div class="flex items-center gap-4">
                <span class="px-3 py-1 rounded-full text-sm font-semibold
							@if($tournament->status === \App\Enums\EventStatus::FINISHED) bg-gray-200 text-gray-800
                            @elseif($tournament->status === \App\Enums\EventStatus::CANCELED) bg-red-200 text-red-800
                            @elseif($tournament->status === \App\Enums\EventStatus::ONGOING) bg-blue-200 text-blue-800
                            @else bg-green-200 text-green-800
							@endif">
							{{ ucfirst($tournament->status->value) }}
						</span>
                <span class="text-gray-600 text-sm">{{ __('Created') }}: {{ $tournament->created_at->format('d.m.Y H:i') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Tournament Details -->
                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Tournament Details') }}</h2>
                    <div class="space-y-4">
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Type:') }}</span>
                            <span class="detail-item-value">{{ $tournament->eventType?->name ?? __('N/A') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Location:') }}</span>
                            <div class="detail-item-value">
                                <p class="text-gray-900">{{ $tournament->sportField?->name ?? __('N/A') }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $tournament->sportField?->address?->street ?? '' }}
                                    {{ $tournament->sportField?->address?->city ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div class="detail-item-divider">
                            <div class="flex justify-between items-start mb-3">
                                <span class="detail-item-label">{{ __('Start:') }}</span>
                                <span class="detail-item-value">{{ $tournament->start_date?->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('End:') }}</span>
                                <span class="detail-item-value">{{ $tournament->end_date?->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($tournament->description)
                    <div class="detail-card">
                        <h2 class="detail-card-header">{{ __('Description') }}</h2>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $tournament->description }}</p>
                    </div>
                @endif

                <!-- Participating Clubs -->
                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Participating Clubs') }}</h2>
                    @if($tournament->clubs->count() > 0)
                        <div class="space-y-2">
                            @foreach($tournament->clubs as $club)
                                <div class="detail-list-item">
                                    <a href="{{ route('panel.coach.clubs.show', $club) }}" class="detail-list-item-link">{{ $club->name }}</a>
                                    <span class="detail-list-secondary">{{ $club->address?->city ?? '-' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No clubs registered') }}</p>
                    @endif
                </div>

                <!-- Child Events -->
                <div class="detail-card">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="detail-card-header !mb-0">{{ __('Matches / Events') }}</h2>
                        <div class="flex gap-2">
                            <x-add-button href="{{ route('panel.coach.tournaments.children.create', $tournament) }}" class="!h-9" />
                        </div>
                    </div>

                    @if($childEvents->count() > 0)
                        <div class="border border-gray-300 rounded-md overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full data-table">
                                    <thead class="bg-gray-100">
                                        <tr class="border-b">
                                            <th class="p-3 text-left">{{ __('Title') }}</th>
                                            <th class="p-3 text-left">{{ __('Type') }}</th>
                                            <th class="p-3 text-left">{{ __('Date') }}</th>
                                            <th class="p-3 text-center">{{ __('Status') }}</th>
                                            <th class="p-3 text-right">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($childEvents as $child)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-3 font-medium">{{ $child->title }}</td>
                                                <td class="p-3 text-sm text-gray-600">{{ $child->eventType?->name ?? '-' }}</td>
                                                <td class="p-3 text-sm text-gray-600">{{ $child->start_date->format('d.m.Y H:i') }}</td>
                                                <td class="p-3 text-center">
                                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                        @if($child->status === \App\Enums\EventStatus::FINISHED) bg-gray-200 text-gray-800
                                                        @elseif($child->status === \App\Enums\EventStatus::CANCELED) bg-red-200 text-red-800
                                                        @else bg-green-200 text-green-800
                                                        @endif">
                                                        {{ ucfirst($child->status->value) }}
                                                    </span>
                                                </td>
                                                <td class="p-3 text-right">
                                                    <a href="{{ route('panel.coach.events.show', $child) }}" class="table-action view mr-2">{{ __('View') }}</a>
                                                    <a href="{{ route('panel.coach.events.edit', $child) }}" class="table-action edit mr-2">{{ __('Edit') }}</a>
                                                    <button type="button" class="table-action delete" x-data x-on:click="$dispatch('open-modal', 'detach-child-{{ $child->event_id }}')">
                                                        {{ __('Remove') }}
                                                    </button>
                                                    <x-modal name="detach-child-{{ $child->event_id }}" :show="false" focusable>
                                                        <form method="POST" action="{{ route('panel.coach.tournaments.children.detach', [$tournament, $child]) }}" class="p-6 text-left">
                                                            @csrf
                                                            @method('DELETE')
                                                            <h2 class="my-heading">{{ __('Remove from Tournament') }}</h2>
                                                            <p class="my-text">{{ __('Remove') }} <strong>{{ $child->title }}</strong> {{ __('from this tournament? The event will not be deleted.') }}</p>
                                                            <div class="flex justify-end gap-3 mt-6">
                                                                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                                                <x-danger-button type="submit">{{ __('Remove') }}</x-danger-button>
                                                            </div>
                                                        </form>
                                                    </x-modal>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-2">
                                {{ $childEvents->links() }}
                            </div>
                    @else
                        <p class="text-gray-600">{{ __('No events in this tournament yet.') }}</p>
                    @endif

                    <!-- Attach existing event -->
                    @if($availableEvents->count() > 0)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('Attach Existing Event') }}</h3>
                            <form method="POST" action="{{ route('panel.coach.tournaments.children.attach', $tournament) }}" class="flex items-center gap-2 items-end">
                                @csrf
                                <div class="flex-1">
                                    <x-select-input
                                        id="event_id"
                                        name="event_id"
                                        :options="$availableEventOptions"
                                        :selected="''"
                                        placeholder="{{ __('Select event...') }}"
                                        class="w-full text-sm"
                                    />
                                </div>
                                <x-primary-button type="submit">{{ __('Attach') }}</x-primary-button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 flex flex-col">
                <div class="sidebar-card sidebar-card-blue">
                    <h3 class="sidebar-card-title">{{ __('Duration') }}</h3>
                    <p class="stat-value" style="color: #2563eb;">{{ $durationText }}</p>
                </div>

                <div class="sidebar-card sidebar-card-gray">
                    <h3 class="sidebar-card-title">{{ __('Statistics') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="stat-label">{{ __('Total Matches') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $childEvents->count() }}</p>
                        </div>
                        <div class="stat-divider">
                            <p class="stat-label">{{ __('Clubs') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $tournament->clubs->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 mt-auto">
                    <x-primary-button class="w-full justify-center" :href="route('panel.coach.tournaments.edit', $tournament)">
                        {{ __('Edit Tournament') }}
                    </x-primary-button>
                    <x-danger-button type="button" class="w-full justify-center" x-data x-on:click="$dispatch('open-modal', 'confirm-tournament-deletion-{{ $tournament->event_id }}')">
                        {{ __('Delete Tournament') }}
                    </x-danger-button>
                    <x-modal name="confirm-tournament-deletion-{{ $tournament->event_id }}" :show="false" focusable>
                        <form method="POST" action="{{ route('panel.coach.tournaments.destroy', $tournament) }}" class="p-6 text-left">
                            @csrf
                            @method('DELETE')
                            <h2 class="my-heading">{{ __('Delete Tournament') }}</h2>
                            <p class="my-text">{{ __('Are you sure? Child events will be detached but not deleted.') }}</p>
                            <div class="flex justify-end gap-3 mt-6">
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                            </div>
                        </form>
                    </x-modal>
                </div>
            </div>
        </div>
    </div>
</x-panel-layout>
<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="mb-4 pb-4 border-b-2 border-gray-200">
            <h1 class="my-heading text-3xl mb-2">{{ $tournament->title }}</h1>

            <div class="flex items-center gap-4">
                <span @class([
                    'px-3 py-1 rounded-full text-sm font-semibold',
                    'bg-gray-200 text-gray-800' => $statusValue === 'finished',
                    'bg-red-200 text-red-800' => $statusValue === 'canceled',
                    'bg-blue-200 text-blue-800' => $statusValue === 'ongoing',
                    'bg-green-200 text-green-800' => !in_array($statusValue, ['finished', 'canceled', 'ongoing'], true),
                ])>
                    {{ ucfirst($statusValue) }}
                </span>

                <span class="text-gray-600 text-sm">
                    {{ __('Created') }}: {{ $tournament->created_at->format('d.m.Y H:i') }}
                </span>
                <a href="{{ route('tournaments.results', $tournament) }}" class="ml-auto">
                    <x-secondary-button type="button">
                        {{ __('Show Results') }}
                    </x-secondary-button>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
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
                                    @if($tournament->sportField?->address?->street)
                                        <br>
                                    @endif
                                    {{ $tournament->sportField?->address?->zip_code ?? '' }}
                                    {{ $tournament->sportField?->address?->city ?? '' }}
                                </p>
                            </div>
                        </div>

                        <div class="detail-item-divider">
                            <div class="flex justify-between items-start mb-3">
                                <span class="detail-item-label">{{ __('Start:') }}</span>
                                <span class="detail-item-value">{{ $tournament->start_date?->format('d.m.Y H:i') ?? __('N/A') }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('End:') }}</span>
                                <span class="detail-item-value">{{ $tournament->end_date?->format('d.m.Y H:i') ?? __('N/A') }}</span>
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

                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Participating Clubs') }}</h2>

                    @if($tournament->clubs->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($tournament->clubs as $club)
                                <div class="detail-list-item">
                                    <a href="{{ route('clubs.show', $club) }}" class="detail-list-item-link">
                                        {{ $club->name }}
                                    </a>
                                    <span class="detail-list-secondary">{{ $club->address?->city ?? '-' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No clubs registered for this tournament') }}</p>
                    @endif
                </div>

                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Tournament Events') }}</h2>

                    @if($childEvents->isNotEmpty())
                        <div class="border border-gray-300 rounded-md overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full data-table">
                                    <thead class="bg-gray-100">
                                        <tr class="border-b">
                                            <th class="p-3 text-left">{{ __('Title') }}</th>
                                            <th class="p-3 text-left">{{ __('Type') }}</th>
                                            <th class="p-3 text-left">{{ __('Location') }}</th>
                                            <th class="p-3 text-left">{{ __('Start') }}</th>
                                            <th class="p-3 text-center">{{ __('Status') }}</th>
                                            <th class="p-3 text-right">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($childEvents as $child)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-3 font-medium">{{ $child->title }}</td>
                                                <td class="p-3 text-sm text-gray-600">{{ $child->eventType?->name ?? '-' }}</td>
                                                <td class="p-3 text-sm text-gray-600">{{ $child->sportField?->name ?? '-' }}</td>
                                                <td class="p-3 text-sm text-gray-600">{{ $child->start_date?->format('d.m.Y H:i') ?? '-' }}</td>
                                                <td class="p-3 text-center">
                                                    <span @class([
                                                        'px-2 py-1 rounded-full text-xs font-semibold',
                                                        'bg-gray-200 text-gray-800' => $child->status === \App\Enums\EventStatus::FINISHED,
                                                        'bg-red-200 text-red-800' => $child->status === \App\Enums\EventStatus::CANCELED,
                                                        'bg-blue-200 text-blue-800' => $child->status === \App\Enums\EventStatus::ONGOING,
                                                        'bg-green-200 text-green-800' => !in_array($child->status, [
                                                            \App\Enums\EventStatus::FINISHED,
                                                            \App\Enums\EventStatus::CANCELED,
                                                            \App\Enums\EventStatus::ONGOING,
                                                        ], true),
                                                    ])>
                                                        {{ ucfirst($child->status->value) }}
                                                    </span>
                                                </td>
                                                <td class="p-3 text-right">
                                                    <a href="{{ route('events.show', $child) }}" class="table-action view">
                                                        {{ __('View') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No events in this tournament yet.') }}</p>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-1 flex flex-col">
                <div class="sidebar-card sidebar-card-blue">
                    <h3 class="sidebar-card-title">{{ __('Duration') }}</h3>
                    <p class="stat-value" style="color: #2563eb;">{{ $durationText }}</p>
                </div>

                <div class="sidebar-card sidebar-card-gray">
                    <h3 class="sidebar-card-title">{{ __('Statistics') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="stat-label">{{ __('Clubs') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $tournament->clubs->count() }}</p>
                        </div>
                        <div class="stat-divider">
                            <p class="stat-label">{{ __('Tournament Events') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $childEvents->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
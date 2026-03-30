<x-app-layout>
    <div class="overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <!-- Upcoming Event (Rectangle - Left Side) -->
            <div class="lg:col-span-1 lg:row-span-2 mb-6 lg:mb-0">
                <div class="bg-white rounded-lg shadow-lg p-4 sm:p-8 h-full lg:mr-6 flex flex-col flex-1">
                    <h2 class="my-heading mb-2">{{ __('Upcoming Event') }}</h2>

                    @if ($upcomingEvent)
                        <div class="space-y-4 flex flex-col flex-1">
                            <!-- Event Title & Type -->
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">
                                    {{ $upcomingEvent->title }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    {{ $upcomingEvent->eventType->name ?? 'N/A' }}
                                </p>
                            </div>

                            <!-- Event Details Grid -->
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Date & Time -->
                                <div class="border-l-4 border-indigo-500 pl-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('Date') }}</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $upcomingEvent->start_date->format('d. M Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $upcomingEvent->start_date->format('H:i') }} - {{ $upcomingEvent->end_date->format('H:i') }}
                                    </p>
                                </div>

                                <!-- Location -->
                                <div class="border-l-4 border-green-500 pl-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('Location') }}</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $upcomingEvent->sportField->name ?? 'N/A' }}
                                    </p>
                                </div>

                                <!-- Clubs -->
                                <div class="border-l-4 border-red-500 pl-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('Participating Clubs') }}</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        @if ($upcomingEvent->clubs->count() > 0)
                                            @php
                                                $clubNames = $upcomingEvent->clubs->take(2)->pluck('name')->implode(', ');
                                                $remainingCount = $upcomingEvent->clubs->count() - 2;
                                            @endphp
                                            {{ $clubNames }}
                                            @if ($remainingCount > 0)
                                                <span class="text-gray-600">...</span>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                    @if ($upcomingEvent->clubs->count() > 2)
                                        <p class="text-sm text-gray-600">
                                            +{{ $upcomingEvent->clubs->count() - 2 }} {{ __('more') }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Status -->
                                <div class="border-l-4 border-orange-500 pl-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('Status') }}</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        <span >
                                            {{ $upcomingEvent->status }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Description -->
                            @if ($upcomingEvent->description)
                                <div class="mt-4 pt-4 border-t">
                                    <p class="text-sm text-gray-700">
                                        {{ Str::limit($upcomingEvent->description, 200) }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div class="pt-auto">
                            <!-- Action Button -->
                            <div class="pt-4 border-t">
                                <x-primary-button class="w-full justify-center" href="{{ route('events.show', $upcomingEvent) }}">{{ __('View Details') }}</x-primary-button>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 mb-4">{{ __('No upcoming events') }}</p>
                            <a href="{{ route('events.index') }}"
                               class="inline-block px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                {{ __('Go to Events') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <!-- Latest Results (Top Right Square) -->
                <div class="lg:col-span-1 mb-6">
                    <div class="bg-white rounded-lg shadow-lg p-6 h-full">
                        <h2 class="my-heading mb-2">{{ __('Latest Results') }}</h2>

                        @if ($latestResults->count() > 0)
                            <div class="space-y-4">
                                <!-- Event Name -->
                                @if ($latestEvent)
                                    <div class="pb-3 border-b">
                                        <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('Event') }}</p>
                                        <p class="text-lg font-bold text-gray-900">{{ $latestEvent->title }}</p>
                                    </div>
                                @endif

                                <!-- Club Results Section -->
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-3">{{ __('Club Results') }}</p>
                                    <p class="text-sm font-semibold text-gray-700 mb-2">{{ $activeMembership->club->name }}</p>
                                    <div class="space-y-2">
                                        @foreach ($latestResults->take(3) as $result)
                                            <div class="p-2 bg-gray-50 rounded-lg border-l-4 border-indigo-500">
                                                <p class="font-semibold text-gray-900 text-sm">
                                                    {{ $result->value ?? 'N/A' }}
                                                    <span class="text-xs text-gray-500">
                                                        ({{ $result->result_type }})
                                                    </span>
                                                </p>
                                                @if ($result->ranking)
                                                    <p class="text-xs text-gray-600">
                                                        {{ __('Ranking') }}: <strong>#{{ $result->ranking }}</strong>
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- My Results Section -->
                                @if ($myResults->count() > 0)
                                    <div class="pt-3 border-t">
                                        <p class="text-xs font-semibold text-gray-500 uppercase mb-3">{{ __('My Results') }}</p>
                                        <div class="space-y-2">
                                            @foreach ($myResults as $result)
                                                <div class="p-2 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                                    <p class="font-semibold text-gray-900 text-sm">
                                                        {{ $result->value ?? 'N/A' }}
                                                        <span class="text-xs text-gray-500">
                                                            ({{ $result->result_type }})
                                                        </span>
                                                    </p>
                                                    @if ($result->ranking)
                                                        <p class="text-xs text-gray-600">
                                                            {{ __('Ranking') }}: <strong>#{{ $result->ranking }}</strong>
                                                        </p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">{{ __('No results') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Member Statistics (Bottom Right Square) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 h-full">
                        <h2 class="my-heading mb-2">{{ __('Your Statistics') }}</h2>

                        @if ($memberStatistics)
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">{{ __('Attendance') }}</span>
                                    <span class="font-bold text-lg text-indigo-500">
                                        {{ $memberStatistics->events_attended ?? 0 }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">{{ __('Wins') }}</span>
                                    <span class="font-bold text-lg text-green-600">
                                        {{ $memberStatistics->total_wins ?? 0 }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">{{ __('Training Sessions') }}</span>
                                    <span class="font-bold text-lg text-purple-600">
                                        {{ $memberStatistics->training_sessions ?? 0 }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">{{ __('Tournaments') }}</span>
                                    <span class="font-bold text-lg text-orange-600">
                                        {{ $memberStatistics->tournaments_attended ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">{{ __('No statistics') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
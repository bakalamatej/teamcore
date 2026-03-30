<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="mb-6 pb-4 border-b-2 border-gray-200">
            <h1 class="my-heading text-3xl mb-1">{{ $tournament->title }}</h1>
            <p class="text-gray-500 text-sm">
                {{ $tournament->start_date->format('d.m.Y') }} — {{ $tournament->end_date->format('d.m.Y') }}
                · {{ $tournament->eventType?->name }}
            </p>
        </div>

        @forelse($tournament->childEvents->sortBy('start_date') as $event)
            <div class="mb-8 border border-gray-200 rounded-lg overflow-hidden">
                {{-- Event Header --}}
                <div class="bg-gray-50 px-4 py-3 flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-gray-800">{{ $event->title }}</h2>
                        <p class="text-xs text-gray-500">
                            {{ $event->start_date->format('d.m.Y H:i') }} — {{ $event->end_date->format('H:i') }}
                            · {{ $event->eventType?->name }}
                        </p>
                    </div>
                    <x-secondary-button
                        type="button"
                        x-data
                        x-on:click="$dispatch('open-modal', 'event-results-{{ $event->event_id }}')"
                    >
                        {{ __('Show Results') }}
                    </x-secondary-button>
                </div>

                {{-- Club Results Table --}}
                @if($event->clubResults->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr class="border-b">
                                    <th class="p-3 text-left">{{ __('Rank') }}</th>
                                    <th class="p-3 text-left">{{ __('Club') }}</th>
                                    <th class="p-3 text-left">{{ __('Value') }}</th>
                                    <th class="p-3 text-left">{{ __('Type') }}</th>
                                    <th class="p-3 text-left">{{ __('Note') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($event->clubResults->sortBy('ranking') as $result)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-3 font-medium">{{ $result->ranking ?? '-' }}</td>
                                        <td class="p-3">{{ $result->club?->name ?? '-' }}</td>
                                        <td class="p-3">{{ $result->value ?? '-' }}</td>
                                        <td class="p-3 text-gray-500 text-xs">{{ $result->result_type?->value ?? '-' }}</td>
                                        <td class="p-3 text-gray-600">{{ $result->note ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="p-4 text-gray-500 text-sm">{{ __('No club results available.') }}</p>
                @endif

                {{-- Member Results Modal --}}
                <x-modal name="event-results-{{ $event->event_id }}" :show="false" focusable>
                    <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                        <h2 class="my-heading">{{ $event->title }} — {{ __('Member Results') }}</h2>

                        @if($event->memberResults->isNotEmpty())
                            <div class="border border-gray-200 rounded-md overflow-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr class="border-b">
                                            <th class="p-3 text-left">{{ __('Rank') }}</th>
                                            <th class="p-3 text-left">{{ __('Member') }}</th>
                                            <th class="p-3 text-left">{{ __('Club') }}</th>
                                            <th class="p-3 text-left">{{ __('Value') }}</th>
                                            <th class="p-3 text-left">{{ __('Type') }}</th>
                                            <th class="p-3 text-left">{{ __('Note') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($event->memberResults->sortBy('ranking') as $result)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="p-3 font-medium">{{ $result->ranking ?? '-' }}</td>
                                                <td class="p-3">{{ $result->memberClub?->member?->full_name ?? '-' }}</td>
                                                <td class="p-3 text-gray-600">{{ $result->memberClub?->club?->name ?? '-' }}</td>
                                                <td class="p-3">{{ $result->value ?? '-' }}</td>
                                                <td class="p-3 text-gray-500 text-xs">{{ $result->result_type?->value ?? '-' }}</td>
                                                <td class="p-3 text-gray-600">{{ $result->note ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">{{ __('No member results available.') }}</p>
                        @endif

                        <div class="flex justify-end">
                            <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                {{ __('Close') }}
                            </x-secondary-button>
                        </div>
                    </div>
                </x-modal>
            </div>
        @empty
            <p class="text-gray-500 text-center py-12">{{ __('No matches in this tournament.') }}</p>
        @endforelse
    </div>
</x-app-layout>
<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <div class="mb-4 pb-4 border-b-2 border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="my-heading text-3xl mb-2">{{ __('Results') }}: {{ $event->title }}</h1>
                    <p class="text-gray-600 text-sm">{{ $event->start_date->format('d.m.Y H:i') }} — {{ $event->end_date->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('panel.admin.events.results.store', $event) }}" class="space-y-8">
            @csrf

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $resultTypeOptions = collect(\App\Enums\ResultType::cases())
                    ->mapWithKeys(fn($t) => [$t->value => ucfirst($t->value)])
                    ->toArray();
            @endphp

            <!-- Club Results -->
            <div class="detail-card overflow-visible">
                <h2 class="detail-card-header">{{ __('Club Results') }}</h2>
                <table class="w-full data-table">
                    <thead class="bg-gray-100">
                        <tr class="border-b">
                            <th class="p-3 text-left">{{ __('Club') }}</th>
                            <th class="p-3 text-left w-36">{{ __('Result Type') }}</th>
                            <th class="p-3 text-left w-32">{{ __('Value') }}</th>
                            <th class="p-3 text-left w-32">{{ __('Ranking') }}</th>
                            <th class="p-3 text-left">{{ __('Note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clubs as $club)
                            @php $clubResult = $clubResults[$club->club_id] ?? null; @endphp
                            <tr class="border-b">
                                <td class="p-3 font-medium">{{ $club->name }}</td>
                                <td class="p-3 relative z-10">
                                    <x-select-input
                                        name="clubs[{{ $club->club_id }}][result_type]"
                                        :options="$resultTypeOptions"
                                        :selected="old('clubs.' . $club->club_id . '.result_type', $clubResult?->result_type?->value)"
                                        placeholder="{{ __('Type') }}"
                                        class="block w-full text-sm"
                                    />
                                </td>
                                <td class="p-3">
                                    <x-text-input
                                        name="clubs[{{ $club->club_id }}][value]"
                                        type="text"
                                        class="block w-full text-sm"
                                        value="{{ old('clubs.' . $club->club_id . '.value', $clubResult?->value) }}"
                                        placeholder="0"
                                    />
                                </td>
                                <td class="p-3">
                                    <x-text-input
                                        name="clubs[{{ $club->club_id }}][ranking]"
                                        type="number" min="1"
                                        class="block w-full text-sm"
                                        value="{{ old('clubs.' . $club->club_id . '.ranking', $clubResult?->ranking) }}"
                                        placeholder="1"
                                    />
                                </td>
                                <td class="p-3">
                                    <x-text-input
                                        name="clubs[{{ $club->club_id }}][note]"
                                        type="text"
                                        class="block w-full text-sm"
                                        value="{{ old('clubs.' . $club->club_id . '.note', $clubResult?->note) }}"
                                        placeholder="{{ __('Note...') }}"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Member Results -->
            <div class="detail-card overflow-visible">
                <h2 class="detail-card-header">{{ __('Member Results') }}</h2>
                @if($memberClubs->isNotEmpty())
                    @foreach($clubs as $club)
                        @php $clubMembers = $memberClubs[$club->club_id] ?? collect(); @endphp
                        @if($clubMembers->isNotEmpty())
                            <h3 class="text-md font-semibold text-gray-700 px-3 pt-4 pb-2">{{ $club->name }}</h3>
                            <table class="w-full data-table">
                                <thead class="bg-gray-100">
                                    <tr class="border-b">
                                        <th class="p-3 text-left">{{ __('Member') }}</th>
                                        <th class="p-3 text-left w-36">{{ __('Result Type') }}</th>
                                        <th class="p-3 text-left w-32">{{ __('Value') }}</th>
                                        <th class="p-3 text-left w-32">{{ __('Ranking') }}</th>
                                        <th class="p-3 text-left">{{ __('Note') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clubMembers as $memberClub)
                                        @php $result = $memberResults[$memberClub->member_club_id] ?? null; @endphp
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-3 font-medium">{{ $memberClub->member?->full_name ?? '—' }}</td>
                                            <td class="p-3 relative z-10">
                                                <x-select-input
                                                    name="members[{{ $memberClub->member_club_id }}][result_type]"
                                                    :options="$resultTypeOptions"
                                                    :selected="old('members.' . $memberClub->member_club_id . '.result_type', $result?->result_type?->value)"
                                                    placeholder="{{ __('Type') }}"
                                                    class="block w-full text-sm"
                                                />
                                            </td>
                                            <td class="p-3">
                                                <x-text-input
                                                    name="members[{{ $memberClub->member_club_id }}][value]"
                                                    type="text"
                                                    class="block w-full text-sm"
                                                    value="{{ old('members.' . $memberClub->member_club_id . '.value', $result?->value) }}"
                                                    placeholder="0"
                                                />
                                            </td>
                                            <td class="p-3">
                                                <x-text-input
                                                    name="members[{{ $memberClub->member_club_id }}][ranking]"
                                                    type="number" min="1"
                                                    class="block w-full text-sm"
                                                    value="{{ old('members.' . $memberClub->member_club_id . '.ranking', $result?->ranking) }}"
                                                    placeholder="1"
                                                />
                                            </td>
                                            <td class="p-3">
                                                <x-text-input
                                                    name="members[{{ $memberClub->member_club_id }}][note]"
                                                    type="text"
                                                    class="block w-full text-sm"
                                                    value="{{ old('members.' . $memberClub->member_club_id . '.note', $result?->note) }}"
                                                    placeholder="{{ __('Note...') }}"
                                                />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    @endforeach
                @else
                    <p class="text-gray-600">{{ __('No members registered for this event.') }}</p>
                @endif
            </div>

            <div class="flex gap-4">
                <x-primary-button>{{ __('Save Results') }}</x-primary-button>
                <x-danger-button :href="route('panel.admin.events.show', $event)">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>
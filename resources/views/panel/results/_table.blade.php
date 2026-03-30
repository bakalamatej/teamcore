<div class="border border-gray-300 rounded-md overflow-hidden overflow-x-auto shadow-md">
    <table class="w-full data-table min-w-[500px]">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Event') }}</th>
                <th class="p-3 text-left">{{ __('Event Type') }}</th>
                <th class="p-3 text-left">{{ __('Club') }}</th>
                <th class="p-3 text-left">{{ __('Date') }}</th>
                <th class="p-3 text-center">{{ __('Score') }}</th>
                <th class="p-3 text-center">{{ __('Ranking') }}</th>
                <th class="p-3 text-left">{{ __('Note') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $result)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $result->event?->title ?? '—' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $result->event?->eventType?->name ?? '—' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $result->memberClub?->club?->name ?? '—' }}</td>
                    <td class="p-3 text-sm text-gray-600">
                        {{ $result->event?->start_date?->format('d.m.Y') ?? '—' }}
                    </td>
                    <td class="p-3 text-center text-gray-700">{{ $result->value ?? '—' }}</td>
                    <td class="p-3 text-center">
                        @if($result->ranking)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                #{{ $result->ranking }}
                            </span>
                        @else
                            <span class="text-gray-500">—</span>
                        @endif
                    </td>
                    <td class="p-3 text-sm text-gray-500">{{ $result->note ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        {{ __('No results found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

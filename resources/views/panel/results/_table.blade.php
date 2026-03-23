@if($results->isEmpty())
    <p class="text-gray-600">{{ __('No results available.') }}</p>
@else
    <div class="detail-card">
        <h2 class="detail-card-header">{{ __('Event Results') }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead class="bg-gray-100">
                    <tr class="border-b">
                        <th class="p-3 text-left">{{ __('Event') }}</th>
                        <th class="p-3 text-left">{{ __('Club') }}</th>
                        <th class="p-3 text-left">{{ __('Date') }}</th>
                        <th class="p-3 text-left">{{ __('Score') }}</th>
                        <th class="p-3 text-left">{{ __('Ranking') }}</th>
                        <th class="p-3 text-left">{{ __('Note') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $result)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 font-medium text-gray-900">
                                {{ $result->event?->title ?? '—' }}
                                @if($result->event?->eventType)
                                    <span class="text-xs text-gray-500 block">{{ $result->event->eventType->name }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-gray-700">{{ $result->memberClub?->club?->name ?? '—' }}</td>
                            <td class="p-3 text-gray-700">
                                {{ $result->event?->start_date?->format('d.m.Y') ?? '—' }}
                            </td>
                            <td class="p-3 text-gray-700">{{ $result->score ?? '—' }}</td>
                            <td class="p-3 text-gray-700">
                                @if($result->ranking)
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                        #{{ $result->ranking }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="p-3 text-gray-500 text-sm">{{ $result->note ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
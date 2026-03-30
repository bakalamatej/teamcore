<div class="border border-gray-300 rounded-md overflow-hidden overflow-x-auto shadow-md">
    <table class="w-full data-table min-w-[500px]">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Club') }}</th>
                <th class="p-3 text-left">{{ __('Status') }}</th>
                <th class="p-3 text-left">{{ __('Events') }}</th>
                <th class="p-3 text-left">{{ __('Trainings') }}</th>
                <th class="p-3 text-left">{{ __('Matches') }}</th>
                <th class="p-3 text-left">{{ __('Tournaments') }}</th>
                <th class="p-3 text-left">{{ __('Wins') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats as $stat)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium text-gray-900">{{ $stat->memberClub->club->name ?? '—' }}</td>
                    <td class="p-3">
                        @if($stat->memberClub->left_at)
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">{{ __('Former') }}</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-800">{{ __('Active') }}</span>
                        @endif
                    </td>
                    <td class="p-3 text-gray-700">{{ $stat->events_attended }}</td>
                    <td class="p-3 text-gray-700">{{ $stat->training_sessions }}</td>
                    <td class="p-3 text-gray-700">{{ $stat->matches_played }}</td>
                    <td class="p-3 text-gray-700">{{ $stat->tournaments_attended }}</td>
                    <td class="p-3 text-gray-700">{{ $stat->total_wins }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        {{ __('No statistics found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
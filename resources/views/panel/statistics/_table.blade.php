@if(!$stats || $stats->isEmpty())
    <p class="text-gray-600">{{ __('No statistics available.') }}</p>
@else
    <div class="detail-card mb-6">
        <h2 class="detail-card-header">{{ __('Overall Totals') }}</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 p-4">
            <div class="sidebar-card sidebar-card-blue text-center flex flex-col items-center">
                <p class="stat-label min-h-[2.5rem] flex items-center justify-center">{{ __('Events Attended') }}</p>
                <p class="stat-value" style="color: #2563eb;">{{ $aggregated['events_attended'] }}</p>
            </div>
            <div class="sidebar-card sidebar-card-gray text-center flex flex-col items-center">
                <p class="stat-label min-h-[2.5rem] flex items-center justify-center">{{ __('Training Sessions') }}</p>
                <p class="stat-value" style="color: #4f46e5;">{{ $aggregated['training_sessions'] }}</p>
            </div>
            <div class="sidebar-card sidebar-card-blue text-center flex flex-col items-center">
                <p class="stat-label min-h-[2.5rem] flex items-center justify-center">{{ __('Matches Played') }}</p>
                <p class="stat-value" style="color: #2563eb;">{{ $aggregated['matches_played'] }}</p>
            </div>
            <div class="sidebar-card sidebar-card-gray text-center flex flex-col items-center">
                <p class="stat-label min-h-[2.5rem] flex items-center justify-center">{{ __('Tournaments') }}</p>
                <p class="stat-value" style="color: #4f46e5;">{{ $aggregated['tournaments_attended'] }}</p>
            </div>
            <div class="sidebar-card sidebar-card-blue text-center flex flex-col items-center">
                <p class="stat-label min-h-[2.5rem] flex items-center justify-center">{{ __('Total Wins') }}</p>
                <p class="stat-value" style="color: #2563eb;">{{ $aggregated['total_wins'] }}</p>
            </div>
        </div>
    </div>

    <div class="detail-card">
        <h2 class="detail-card-header">{{ __('Breakdown by Club') }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full data-table">
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
                    @foreach($stats as $stat)
                        <tr class="border-b hover:bg-gray-50">
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
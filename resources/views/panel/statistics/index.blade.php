@push('scripts')
    @vite(['resources/js/shared/filter.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="my-heading text-2xl">{{ __('My Statistics') }}</h1>
        </div>

        <!-- Overall Totals - always full, not affected by filter -->
        <div class="detail-card mb-6">
            <h2 class="detail-card-header">{{ __('Overall Totals') }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 p-2">
                <div class="sidebar-card sidebar-card-blue text-center flex flex-col items-center">
                    <p class="stat-label min-h-[2.5rem] flex items-center justify-center">{{ __('Events Attended') }}</p>
                    <p class="stat-value" style="color: #2563eb;">{{ $totalAggregated['events_attended'] }}</p>
                </div>
                <div class="sidebar-card sidebar-card-gray text-center flex flex-col items-center">
                    <p class="stat-label min-h-[2.5rem] flex items-center justify-center">{{ __('Training Sessions') }}</p>
                    <p class="stat-value" style="color: #4f46e5;">{{ $totalAggregated['training_sessions'] }}</p>
                </div>
                <div class="sidebar-card sidebar-card-blue text-center flex flex-col items-center">
                    <p class="stat-label min-h-[2.5rem] flex items-center justify-center">{{ __('Matches Played') }}</p>
                    <p class="stat-value" style="color: #2563eb;">{{ $totalAggregated['matches_played'] }}</p>
                </div>
                <div class="sidebar-card sidebar-card-gray text-center flex flex-col items-center">
                    <p class="stat-label min-h-[2.5rem] flex items-center justify-center">{{ __('Tournaments') }}</p>
                    <p class="stat-value" style="color: #4f46e5;">{{ $totalAggregated['tournaments_attended'] }}</p>
                </div>
                <div class="sidebar-card sidebar-card-blue text-center flex flex-col items-center">
                    <p class="stat-label min-h-[2.5rem] flex items-center justify-center">{{ __('Total Wins') }}</p>
                    <p class="stat-value" style="color: #2563eb;">{{ $totalAggregated['total_wins'] }}</p>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form id="filter-form" method="GET" action="{{ route('panel.statistics.index') }}" class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <div class="flex-1">
                    <x-input-label :value="__('Club')" />
                    <x-select-input
                        id="member_club_id"
                        name="member_club_id"
                        :options="$clubOptions"
                        :selected="$selectedMemberClubId"
                        placeholder="{{ __('All clubs') }}"
                        class="mt-1 block w-full text-sm"
                    />
                </div>
            </form>
        </div>

        <div id="results">
            @include('panel.statistics._table', compact('stats'))
        </div>
    </div>
</x-panel-layout>
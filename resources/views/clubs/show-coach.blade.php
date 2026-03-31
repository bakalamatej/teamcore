<x-app-layout>
    <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="mb-6 pb-4 border-b-2 border-gray-200">
            <h1 class="my-heading text-3xl mb-2">{{ $member->full_name }}</h1>
            <p class="text-gray-600 text-sm">{{ __('Coach Profile') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Coach Information') }}</h2>

                    <div class="space-y-4">
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Full Name:') }}</span>
                            <span class="detail-item-value">{{ $member->full_name }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Phone:') }}</span>
                            <span class="detail-item-value">{{ $member->phone ?? __('N/A') }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Email:') }}</span>
                            <span class="detail-item-value">{{ $member->user?->email ?? __('N/A') }}</span>
                        </div>

                        @if($member->date_of_birth)
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Date of Birth:') }}</span>
                                <span class="detail-item-value">{{ $member->date_of_birth->format('d.m.Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Active Clubs as Coach') }}</h2>

                    @if($coachMemberships->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($coachMemberships as $membership)
                                <div class="detail-list-item">
                                    <span class="font-medium text-gray-900">
                                        {{ $membership->club?->name ?? __('Unknown club') }}
                                    </span>
                                    <span class="detail-list-secondary">
                                        {{ $membership->club?->email ?? '-' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('This member is not an active coach in any club.') }}</p>
                    @endif
                </div>

                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Coach Evaluations') }}</h2>

                    @if($evaluations->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($evaluations as $evaluation)
                                <div class="border bg-white border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between mb-2">
                                        <div>
                                            <p class="font-medium">
                                                {{ $evaluation->evaluatedByMember?->full_name ?? 'Unknown member' }}
                                            </p>
                                        </div>

                                        <div class="text-indigo-600 font-semibold">
                                            {{ $evaluation->rating }}/5
                                        </div>
                                    </div>

                                    @if($evaluation->comment)
                                        <p class="text-gray-700">{{ $evaluation->comment }}</p>
                                    @endif

                                    <p class="text-xs text-gray-400 mt-2">
                                        {{ $evaluation->created_at?->format('d.m.Y H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No evaluations available for this coach.') }}</p>
                    @endif
                    <div class="mt-4">
                        {{ $evaluations->links() }}
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 flex flex-col">
                <div class="sidebar-card sidebar-card-blue">
                    <h3 class="sidebar-card-title">{{ __('Average Rating') }}</h3>
                    <p class="stat-value" style="color: #2563eb;">
                        {{ $averageRating !== null ? number_format($averageRating, 1) . '/5' : __('N/A') }}
                    </p>
                </div>

                <div class="sidebar-card sidebar-card-gray">
                    <h3 class="sidebar-card-title">{{ __('Statistics') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="stat-label">{{ __('Active Coach Clubs') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $coachMemberships->count() }}</p>
                        </div>

                        <div class="stat-divider">
                            <p class="stat-label">{{ __('Evaluations Count') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">{{ $evaluationsCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
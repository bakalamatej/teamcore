<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <h1 class="my-heading text-2xl">{{ $member->full_name }}</h1>
                <p class="my-text mb-4">{{ __('Coach rating overview') }} </p>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    <div class="detail-card lg:col-span-2">
                        <h2 class="detail-card-header">{{ __('Active Clubs as Coach') }}</h2>

                        <div class="space-y-3">
                            @forelse($activeCoachClubs as $membership)
                                <div class="detail-item">
                                    <span class="detail-item-value">{{ $membership->club->name ?? '-' }}</span>
                                    <span class="text-sm text-gray-500">{{ __('since') }} {{ optional($membership->joined_at)->format('d.m.Y') ?? '-' }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">{{ __('No active coach club memberships.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="sidebar-card sidebar-card-blue">
                        <h3 class="sidebar-card-title">{{ __('Average Rating') }}</h3>
                        <p class="text-3xl font-bold" style="color: #4f46e5;">
                            {{ $averageRating ? number_format((float) $averageRating, 1) : '-' }}
                        </p>
                        <p class="text-sm text-gray-600 mt-2">
                            {{ __('Total ratings:') }} {{ $ratings->count() }}
                        </p>
                    </div>
                </div>

                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('All Ratings') }}</h2>

                    <div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
                        <table class="w-full data-table">
                            <thead class="bg-gray-100">
                                <tr class="border-b">
                                    <th class="p-3 text-left">{{ __('From') }}</th>
                                    <th class="p-3 text-left">{{ __('Rating') }}</th>
                                    <th class="p-3 text-left">{{ __('Note') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ratings as $rating)
                                    <tr class="border-b hover:bg-gray-50 data-row">
                                        <td class="p-3 font-medium">{{ $rating->evaluatedByMember->full_name ?? '-' }}</td>
                                        <td class="p-3">{{ number_format((float) $rating->rating, 1) }}</td>
                                        <td class="p-3 text-sm text-gray-600">{{ $rating->comment ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-4 text-center text-gray-500">
                                            {{ __('No ratings found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

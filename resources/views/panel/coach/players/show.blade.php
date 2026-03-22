<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="my-heading text-2xl">{{ $player->member?->full_name ?? '—' }}</h1>
                <p class="text-gray-600">{{ $player->member?->user?->email ?? '—' }}</p>
            </div>
            <span @class([
                'px-3 py-1 rounded-full text-sm font-semibold',
                'bg-blue-200 text-blue-800' => $primaryRole === \App\Enums\MemberClubRole::COACH->value,
                'bg-green-200 text-green-800' => $primaryRole !== \App\Enums\MemberClubRole::COACH->value,
            ])>
                {{ ucfirst($primaryRole) }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Player Details -->
                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('Player Information') }}</h2>
                    <div class="space-y-4">
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('First Name:') }}</span>
                            <span class="detail-item-value">{{ $player->member?->first_name ?? '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Last Name:') }}</span>
                            <span class="detail-item-value">{{ $player->member?->last_name ?? '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Email:') }}</span>
                            <span class="detail-item-value">{{ $player->member?->user?->email ?? '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Phone:') }}</span>
                            <span class="detail-item-value">{{ $player->member?->phone ?? '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Date of Birth:') }}</span>
                            <span class="detail-item-value">{{ $player->member?->date_of_birth?->format('d.m.Y') ?? '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Joined at:') }}</span>
                            <span class="detail-item-value">{{ $player->joined_at?->format('d.m.Y') ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Statistics (Club Context) -->
                <div class="detail-card mt-6">
                    <h2 class="detail-card-header">{{ __('Statistics') }}</h2>
                    <div class="space-y-4">
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Events attended:') }}</span>
                            <span class="detail-item-value">{{ $clubStat?->events_attended ?? 0 }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Matches played:') }}</span>
                            <span class="detail-item-value">{{ $clubStat?->matches_played ?? 0 }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Total wins:') }}</span>
                            <span class="detail-item-value">{{ $clubStat?->total_wins ?? 0 }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Training sessions:') }}</span>
                            <span class="detail-item-value">{{ $clubStat?->training_sessions ?? 0 }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Tournaments attended:') }}</span>
                            <span class="detail-item-value">{{ $clubStat?->tournaments_attended ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 flex flex-col">
                <div class="sidebar-card sidebar-card-blue">
                    <div class="space-y-3">
                        <div>
                            <p class="stat-label">{{ __('Membership Age') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">
                                {{ $player->joined_at ? (int) $player->joined_at->diffInDays(now()) : 0 }} {{ __('days') }}
                            </p>
                        </div>
                        @if($player->member)
                            <div class="stat-divider">
                                <p class="stat-label">{{ __('Member Since') }}</p>
                                <p class="stat-value" style="color: #4f46e5;">{{ $player->member->created_at?->format('M Y') ?? '—' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @if($player->member?->user_id !== Auth::id())
                <div class="space-y-3 mt-auto">
                    <x-danger-button type="button" class="w-full justify-center"
                        x-data
                        x-on:click="$dispatch('open-modal', 'confirm-player-deletion')">
                        {{ __('Delete Membership') }}
                    </x-danger-button>

                    <x-modal name="confirm-player-deletion" :show="false" focusable>
                        <form method="POST" action="{{ route('panel.coach.players.destroy', $player) }}" class="p-6 text-left">
                            @csrf
                            @method('DELETE')

                            <h2 class="my-heading">{{ __('Delete Membership') }}</h2>
                            <p class="my-text">{{ __('Are you sure you want to remove this player from the club? This action cannot be undone.') }}</p>

                            <div class="flex justify-end gap-3 mt-6">
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                    {{ __('Cancel') }}
                                </x-secondary-button>
                                <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                            </div>
                        </form>
                    </x-modal>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-panel-layout>

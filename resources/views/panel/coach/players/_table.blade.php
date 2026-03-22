<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Name') }}</th>
                <th class="p-3 text-left">{{ __('Email') }}</th>
                <th class="p-3 text-center">{{ __('Role') }}</th>
                <th class="p-3 text-left">{{ __('Joined At') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($players as $player)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $player->member?->full_name ?? '—' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $player->member?->user?->email ?? '—' }}</td>
                    <td class="p-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-800">
                            {{ ucfirst($player->role->value) }}
                        </span>
                    </td>
                    <td class="p-3 text-sm text-gray-600">{{ $player->joined_at?->format('Y-m-d') ?? '—' }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.coach.players.show', $player) }}" class="table-action view mr-2" title="{{ __('View') }}">
                            {{ __('View') }}
                        </a>
                        @if($player->member?->user?->user_id !== Auth::id())
                            <button type="button" class="table-action delete ml-2"
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'confirm-player-deletion-{{ $player->member_club_id }}')">
                                {{ __('Delete') }}
                            </button>

                            <x-modal name="confirm-player-deletion-{{ $player->member_club_id }}" :show="false" focusable>
                                <form method="POST" action="{{ route('panel.coach.players.destroy', $player) }}" class="p-6 text-left">
                                    @csrf
                                    @method('DELETE')

                                    <h2 class="my-heading">{{ __('End Membership') }}</h2>
                                    <p class="my-text">
                                        {{ __('Are you sure you want to end membership for') }} <strong>{{ $player->member?->full_name ?? '—' }}</strong>?
                                        {{ __('This will set left date and remove the player from the active list.') }}
                                    </p>

                                    <div class="flex justify-end gap-3 mt-6">
                                        <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                            {{ __('Cancel') }}
                                        </x-secondary-button>

                                        <x-danger-button type="submit">
                                            {{ __('End Membership') }}
                                        </x-danger-button>
                                    </div>
                                </form>
                            </x-modal>
                        @else
                            <span class="text-xs text-gray-500">{{ __('(You)') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        {{ __('No players found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $players->links() }}
</div>

<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Member') }}</th>
                <th class="p-3 text-left">{{ __('Email') }}</th>
                <th class="p-3 text-left">{{ __('Club') }}</th>
                <th class="p-3 text-left">{{ __('Sport') }}</th>
                <th class="p-3 text-left">{{ __('Role') }}</th>
                <th class="p-3 text-left">{{ __('Joined At') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($memberships as $membership)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $membership->member?->full_name ?? '—' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $membership->member?->user?->email ?? '—' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $membership->club?->name ?? '—' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $membership->sport?->name ?? '—' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ ucfirst($membership->role->value) }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $membership->joined_at?->format('Y-m-d') ?? '—' }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.memberships.edit', $membership) }}" class="table-action edit">
                            {{ __('Edit') }}
                        </a>

                        <button type="button" class="table-action delete ml-2"
                                x-data
                                x-on:click="$dispatch('open-modal', 'confirm-membership-deletion-{{ $membership->member_club_id }}')">
                            {{ __('Delete') }}
                        </button>

                        <x-modal name="confirm-membership-deletion-{{ $membership->member_club_id }}" :show="false" focusable>
                            <form method="POST" action="{{ route('panel.memberships.destroy', $membership) }}" class="p-6 text-left">
                                @csrf
                                @method('DELETE')

                                <h2 class="my-heading">{{ __('End Membership') }}</h2>
                                <p class="my-text">
                                    {{ __('Are you sure you want to end this membership? It will set left date and remove it from active list.') }}
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
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        {{ __('No memberships found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($memberships->hasPages())
    <div class="mt-4">
        {{ $memberships->links() }}
    </div>
@endif

<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Name') }}</th>
                <th class="p-3 text-left">{{ __('Email') }}</th>
                <th class="p-3 text-left">{{ __('Memberships') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $member->full_name }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $member->user?->email ?? '—' }}</td>
                    <td class="p-3 text-sm text-gray-600">
                        @forelse($member->clubMemberships as $mc)
                            <span class="inline-block">
                                {{ $mc->club?->name }}
                                <span class="text-gray-400">({{ ucfirst($mc->role->value) }}{{ $mc->sport ? ' · ' . $mc->sport->name : '' }})</span>
                            </span>{{ !$loop->last ? ', ' : '' }}
                        @empty
                            <span class="text-gray-400">{{ __('No memberships') }}</span>
                        @endforelse
                    </td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.memberships.edit', $member) }}" class="table-action edit">
                            {{ __('Edit') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">
                        {{ __('No members found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($members->hasPages())
    <div class="mt-4">
        {{ $members->links() }}
    </div>
@endif

<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Name') }}</th>
                <th class="p-3 text-left">{{ __('Sport') }}</th>
                <th class="p-3 text-left">{{ __('Address') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clubs as $club)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $club->name }}</td>
                    <td class="p-3 text-sm text-gray-600">
                        @if($club->sports && $club->sports->count())
                            {{ $club->sports->pluck('name')->join(', ') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="p-3 text-sm text-gray-600">
                        @if($club->address)
                            {{ $club->address->street ? $club->address->street . ', ' : '' }}{{ $club->address->zip_code }} {{ $club->address->city }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.coach.clubs.show', $club) }}" class="table-action view mr-2">
                            {{ __('View') }}
                        </a>
                        @if($canManageClubId && $canManageClubId === $club->club_id)
                            <a href="{{ route('panel.coach.clubs.edit', $club) }}" class="table-action edit mr-2">
                                {{ __('Edit') }}
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">
                        {{ __('No clubs found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $clubs->links() }}
</div>

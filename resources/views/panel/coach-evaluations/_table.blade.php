<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Coach') }}</th>
                <th class="p-3 text-left">{{ __('Phone') }}</th>
                <th class="p-3 text-left">{{ __('Average Rating') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $member->full_name }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $member->phone ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $member->average_rating ? number_format((float) $member->average_rating, 1) : '-' }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.coach-evaluations.show', $member) }}" class="table-action view">
                            {{ __('View') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">
                        {{ __('No coaches found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $members->links() }}
</div>

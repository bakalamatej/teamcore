<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('By') }}</th>
                <th class="p-3 text-left">{{ __('Rating') }}</th>
                <th class="p-3 text-left">{{ __('Comment') }}</th>
                <th class="p-3 text-left">{{ __('Created') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evaluations as $evaluation)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 text-sm text-gray-600">
                        {{ $evaluation->evaluatedByMember?->full_name ?? '-' }}
                    </td>
                    <td class="p-3 text-sm text-gray-600">
                        {{ $evaluation->rating ?? '-' }}
                    </td>
                    <td class="p-3 text-sm text-gray-600">
                        {{ $evaluation->comment ?? '-' }}
                    </td>
                    <td class="p-3 text-sm text-gray-600">
                        {{ $evaluation->created_at?->format('d.m.Y H:i') ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">
                        {{ __('No evaluations found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

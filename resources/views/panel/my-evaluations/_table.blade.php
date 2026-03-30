<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Coach') }}</th>
                <th class="p-3 text-left">{{ __('Rating') }}</th>
                <th class="p-3 text-left">{{ __('Comment') }}</th>
                <th class="p-3 text-left">{{ __('Created') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evaluations as $evaluation)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 text-sm text-gray-600">{{ $evaluation->coach?->full_name ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $evaluation->rating ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $evaluation->comment ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $evaluation->created_at?->format('d.m.Y H:i') ?? '-' }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.my-evaluations.edit', $evaluation) }}" class="table-action edit mr-2">
                            {{ __('Edit') }}
                        </a>
                        <button type="button" class="table-action delete"
                            x-data
                            x-on:click="$dispatch('open-modal', 'confirm-evaluation-deletion-{{ $evaluation->evaluation_id }}')">
                            {{ __('Delete') }}
                        </button>
                        <x-modal name="confirm-evaluation-deletion-{{ $evaluation->evaluation_id }}" :show="false" focusable>
                            <form method="POST" action="{{ route('panel.my-evaluations.destroy', $evaluation) }}" class="p-6 text-left">
                                @csrf
                                @method('DELETE')
                                <h2 class="my-heading">{{ __('Delete Evaluation') }}</h2>
                                <p class="my-text">{{ __('Are you sure you want to delete this evaluation? This action cannot be undone.') }}</p>
                                <div class="flex justify-end gap-3 mt-6">
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>
                                    <x-danger-button type="submit">
                                        {{ __('Delete') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">
                        {{ __('No evaluations found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $evaluations->links() }}
</div>
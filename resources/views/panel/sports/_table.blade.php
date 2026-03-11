<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Name') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sports as $sport)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $sport->name }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.sports.edit', $sport) }}" class="table-action edit mr-2">
                            {{ __('Edit') }}
                        </a>

                        <button type="button" class="table-action delete"
                                x-data
                                x-on:click="$dispatch('open-modal', 'confirm-sport-deletion-{{ $sport->sport_id }}')">
                            {{ __('Delete') }}
                        </button>

                        <x-modal name="confirm-sport-deletion-{{ $sport->sport_id }}" :show="false" focusable>
                            <form method="POST" action="{{ route('panel.sports.destroy', $sport) }}" class="p-6 text-left">
                                @csrf
                                @method('DELETE')

                                <h2 class="my-heading">{{ __('Delete Sport') }}</h2>
                                <p class="my-text">
                                    {{ __('Are you sure you want to delete') }} <strong>{{ $sport->name }}</strong>?
                                    {{ __('This action cannot be undone.') }}
                                </p>

                                <div class="flex justify-end gap-3 mt-6">
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>

                                    <x-danger-button type="submit">
                                        {{ __('Delete Sport') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="p-4 text-center text-gray-500">
                        {{ __('No sports found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $sports->links() }}
</div>

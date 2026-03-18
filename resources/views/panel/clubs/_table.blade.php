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
                    <td class="p-3 text-sm text-gray-600">{{ $club->sport->name ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">
                        @if($club->address)
                            {{ $club->address->street }}, {{ $club->address->zip_code }} {{ $club->address->city }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.clubs.show', $club) }}" class="table-action view mr-2">
                            {{ __('View') }}
                        </a>

                        <a href="{{ route('panel.clubs.edit', $club) }}" class="table-action edit mr-2">
                            {{ __('Edit') }}
                        </a>

                        <button type="button" class="table-action delete"
                                x-data
                                x-on:click="$dispatch('open-modal', 'confirm-club-deletion-{{ $club->club_id }}')">
                            {{ __('Delete') }}
                        </button>

                        <x-modal name="confirm-club-deletion-{{ $club->club_id }}" :show="false" focusable>
                            <form method="POST" action="{{ route('panel.clubs.destroy', $club) }}" class="p-6 text-left">
                                @csrf
                                @method('DELETE')

                                <h2 class="my-heading">{{ __('Delete Club') }}</h2>
                                <p class="my-text">
                                    {{ __('Are you sure you want to delete') }} <strong>{{ $club->name }}</strong>?
                                    {{ __('This action cannot be undone.') }}
                                </p>

                                <div class="flex justify-end gap-3 mt-6">
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>

                                    <x-danger-button type="submit">
                                        {{ __('Delete Club') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
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

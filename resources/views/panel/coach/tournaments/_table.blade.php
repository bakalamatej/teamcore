<div class="border border-gray-300 rounded-md overflow-hidden overflow-x-auto shadow-md">
    <table class="w-full data-table min-w-[700px]">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Title') }}</th>
                <th class="p-3 text-left">{{ __('Sport Field') }}</th>
                <th class="p-3 text-left">{{ __('Dates') }}</th>
                <th class="p-3 text-center">{{ __('Matches') }}</th>
                <th class="p-3 text-center">{{ __('Status') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tournaments as $tournament)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $tournament->title }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $tournament->sportField?->name ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">
                        {{ $tournament->start_date->format('d.m.Y') }} - {{ $tournament->end_date->format('d.m.Y') }}
                    </td>
                    <td class="p-3 text-center text-sm text-gray-600">{{ $tournament->childEvents->count() }}</td>
                    <td class="p-3 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
							@if($tournament->status === \App\Enums\EventStatus::FINISHED) bg-gray-200 text-gray-800
                            @elseif($tournament->status === \App\Enums\EventStatus::CANCELED) bg-red-200 text-red-800
                            @elseif($tournament->status === \App\Enums\EventStatus::ONGOING) bg-blue-200 text-blue-800
                            @else bg-green-200 text-green-800
							@endif">
							{{ ucfirst($tournament->status->value) }}
						</span>
                    </td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.coach.tournaments.show', $tournament) }}" class="table-action view mr-2">{{ __('View') }}</a>
                        <a href="{{ route('panel.coach.tournaments.edit', $tournament) }}" class="table-action edit mr-2">{{ __('Edit') }}</a>
                        <button type="button" class="table-action delete" x-data x-on:click="$dispatch('open-modal', 'confirm-tournament-deletion-{{ $tournament->event_id }}')">
                            {{ __('Delete') }}
                        </button>
                        <x-modal name="confirm-tournament-deletion-{{ $tournament->event_id }}" :show="false" focusable>
                            <form method="POST" action="{{ route('panel.coach.tournaments.destroy', $tournament) }}" class="p-6 text-left">
                                @csrf
                                @method('DELETE')
                                <h2 class="my-heading">{{ __('Delete Tournament') }}</h2>
                                <p class="my-text">{{ __('Are you sure you want to delete') }} <strong>{{ $tournament->title }}</strong>? {{ __('Child events will be detached but not deleted.') }}</p>
                                <div class="flex justify-end gap-3 mt-6">
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                    <x-danger-button type="submit">{{ __('Delete Tournament') }}</x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">{{ __('No tournaments found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $tournaments->links() }}
</div>
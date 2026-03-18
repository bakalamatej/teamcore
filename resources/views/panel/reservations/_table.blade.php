<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Title') }}</th>
                <th class="p-3 text-left">{{ __('Sport Field') }}</th>
                <th class="p-3 text-left">{{ __('Club') }}</th>
                <th class="p-3 text-left">{{ __('Dates') }}</th>
                <th class="p-3 text-center">{{ __('Status') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $reservation->title }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $reservation->sportField->name ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $reservation->club->name ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">
                        {{ $reservation->start_date?->format('d.m.Y') ?? '-' }}
                        -
                        {{ $reservation->end_date?->format('d.m.Y') ?? '-' }}
                    </td>
                    <td class="p-3 text-center">
                        @php($statusValue = $reservation->status->value)
                        <span @class([
                            'px-3 py-1 rounded-full text-xs font-semibold',
                            'bg-yellow-200 text-yellow-800' => $statusValue === 'pending',
                            'bg-green-200 text-green-800' => $statusValue === 'approved',
                            'bg-red-200 text-red-800' => in_array($statusValue, ['rejected', 'canceled'], true),
                        ])>
                            {{ ucfirst($statusValue) }}
                        </span>
                    </td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.reservations.show', $reservation) }}" class="table-action view mr-2">
                            {{ __('View') }}
                        </a>

                        <a href="{{ route('panel.reservations.edit', $reservation) }}" class="table-action edit mr-2">
                            {{ __('Edit') }}
                        </a>

                        <button type="button" class="table-action delete"
                                x-data
                                x-on:click="$dispatch('open-modal', 'confirm-reservation-deletion-{{ $reservation->reservation_id }}')">
                            {{ __('Delete') }}
                        </button>

                        <x-modal name="confirm-reservation-deletion-{{ $reservation->reservation_id }}" :show="false" focusable>
                            <form method="POST" action="{{ route('panel.reservations.destroy', $reservation) }}" class="p-6 text-left">
                                @csrf
                                @method('DELETE')

                                <h2 class="my-heading">{{ __('Delete Reservation') }}</h2>
                                <p class="my-text">
                                    {{ __('Are you sure you want to delete') }} <strong>{{ $reservation->title }}</strong>?
                                    {{ __('This action cannot be undone.') }}
                                </p>

                                <div class="flex justify-end gap-3 mt-6">
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>

                                    <x-danger-button type="submit">
                                        {{ __('Delete Reservation') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">
                        {{ __('No reservations found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $reservations->links() }}
</div>

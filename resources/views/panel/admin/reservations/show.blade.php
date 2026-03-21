<x-panel-layout>
            <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
                @php($statusValue = $reservation->status->value)

                <div class="mb-4 pb-4 border-b-2 border-gray-200">
                    <h1 class="my-heading text-3xl mb-2">{{ $reservation->title }}</h1>
                    <div class="flex items-center gap-4">
                        <span @class([
                            'px-3 py-1 rounded-full text-sm font-semibold',
                            'bg-yellow-200 text-yellow-800' => $statusValue === 'pending',
                            'bg-green-200 text-green-800' => $statusValue === 'approved',
                            'bg-red-200 text-red-800' => in_array($statusValue, ['rejected', 'canceled'], true),
                        ])>
                            {{ ucfirst($statusValue) }}
                        </span>
                        <span class="text-gray-600 text-sm">{{ __('Created') }}: {{ $reservation->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="detail-card">
                            <h2 class="detail-card-header">{{ __('Reservation Details') }}</h2>

                            <div class="space-y-4">
                                <div class="detail-item">
                                    <span class="detail-item-label">{{ __('Sport:') }}</span>
                                    <span class="detail-item-value">{{ $reservation->sport?->name ?? __('N/A') }}</span>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-item-label">{{ __('Club:') }}</span>
                                    <span class="detail-item-value">{{ $reservation->club?->name ?? __('N/A') }}</span>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-item-label">{{ __('Sport Field:') }}</span>
                                    <div class="detail-item-value">
                                        <p class="text-gray-900">{{ $reservation->sportField?->name ?? __('N/A') }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $reservation->sportField?->address?->street ?? '' }}
                                            @if($reservation->sportField?->address?->street)
                                                <br>
                                            @endif
                                            {{ $reservation->sportField?->address?->zip_code ?? '' }}
                                            {{ $reservation->sportField?->address?->city ?? '' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <span class="detail-item-label">{{ __('Created By:') }}</span>
                                    <span class="detail-item-value">
                                        {{ $reservation->createdByMemberClub?->club?->name ?? '-' }} -
                                        {{ $reservation->createdByMemberClub?->member?->full_name ?? __('N/A') }}
                                    </span>
                                </div>

                                <div class="detail-item-divider">
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="detail-item-label">{{ __('Start:') }}</span>
                                        <span class="detail-item-value">{{ $reservation->start_date?->format('d.m.Y') ?? __('N/A') }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-item-label">{{ __('End:') }}</span>
                                        <span class="detail-item-value">{{ $reservation->end_date?->format('d.m.Y') ?? __('N/A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($reservation->description)
                            <div class="detail-card">
                                <h2 class="detail-card-header">{{ __('Description') }}</h2>
                                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $reservation->description }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="lg:col-span-1 flex flex-col">
                        <div class="space-y-3 mt-auto">
                            <x-primary-button class="w-full justify-center" :href="route('panel.admin.reservations.edit', $reservation)">
                                {{ __('Edit Reservation') }}
                            </x-primary-button>

                            <x-danger-button type="button" class="w-full justify-center"
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'confirm-reservation-deletion-{{ $reservation->reservation_id }}')">
                                {{ __('Delete Reservation') }}
                            </x-danger-button>
                        </div>
                    </div>
                </div>

                <x-modal name="confirm-reservation-deletion-{{ $reservation->reservation_id }}" :show="false" focusable>
                    <form method="POST" action="{{ route('panel.admin.reservations.destroy', $reservation) }}" class="p-6 text-left">
                        @csrf
                        @method('DELETE')

                        <h2 class="my-heading">{{ __('Delete Reservation') }}</h2>
                        <p class="my-text">{{ __('Are you sure you want to delete this reservation? This action cannot be undone.') }}</p>

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
            </div>
        </main>
    </div>
</x-panel-layout>

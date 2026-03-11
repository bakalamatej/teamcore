<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('City') }}</th>
                <th class="p-3 text-left">{{ __('Street') }}</th>
                <th class="p-3 text-left">{{ __('Country') }}</th>
                <th class="p-3 text-left">{{ __('Zip Code') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($addresses as $address)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $address->city }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $address->street ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $address->country ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $address->zip_code ?? '-' }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.addresses.edit', $address) }}" class="table-action edit mr-2">
                            {{ __('Edit') }}
                        </a>

                        <button type="button" class="table-action delete"
                                x-data
                                x-on:click="$dispatch('open-modal', 'confirm-address-deletion-{{ $address->address_id }}')">
                            {{ __('Delete') }}
                        </button>

                        <x-modal name="confirm-address-deletion-{{ $address->address_id }}" :show="false" focusable>
                            <form method="POST" action="{{ route('panel.addresses.destroy', $address) }}" class="p-6 text-left">
                                @csrf
                                @method('DELETE')

                                <h2 class="my-heading">{{ __('Delete Address') }}</h2>
                                <p class="my-text">
                                    {{ __('Are you sure you want to delete') }} <strong>{{ $address->city }}, {{ $address->street }}</strong>?
                                    {{ __('This action cannot be undone.') }}
                                </p>

                                <div class="flex justify-end gap-3 mt-6">
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>

                                    <x-danger-button type="submit">
                                        {{ __('Delete Address') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        {{ __('No addresses found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $addresses->links() }}
</div>

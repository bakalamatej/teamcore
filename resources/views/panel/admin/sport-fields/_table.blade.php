<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Name') }}</th>
                <th class="p-3 text-left">{{ __('Type') }}</th>
                <th class="p-3 text-left">{{ __('Location') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sportFields as $sportField)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $sportField->name }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $sportField->fieldType->name ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $sportField->address->city ?? '-' }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.admin.sport-fields.edit', $sportField) }}" class="table-action edit mr-2">
                            {{ __('Edit') }}
                        </a>

                        <button type="button" class="table-action delete"
                                x-data
                                x-on:click="$dispatch('open-modal', 'confirm-sport-field-deletion-{{ $sportField->sport_field_id }}')">
                            {{ __('Delete') }}
                        </button>

                        <x-modal name="confirm-sport-field-deletion-{{ $sportField->sport_field_id }}" :show="false" focusable>
                            <form method="POST" action="{{ route('panel.admin.sport-fields.destroy', $sportField) }}" class="p-6 text-left">
                                @csrf
                                @method('DELETE')

                                <h2 class="my-heading">{{ __('Delete Sport Field') }}</h2>
                                <p class="my-text">
                                    {{ __('Are you sure you want to delete') }} <strong>{{ $sportField->name }}</strong>?
                                    {{ __('This action cannot be undone.') }}
                                </p>

                                <div class="flex justify-end gap-3 mt-6">
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>

                                    <x-danger-button type="submit">
                                        {{ __('Delete Sport Field') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-500">
                        {{ __('No sport fields found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $sportFields->links() }}
</div>

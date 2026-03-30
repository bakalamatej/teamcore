<div class="border border-gray-300 rounded-md overflow-hidden overflow-x-auto shadow-md">
    <table class="w-full data-table min-w-[700px]">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('File Name') }}</th>
                <th class="p-3 text-left">{{ __('Type') }}</th>
                <th class="p-3 text-left">{{ __('Uploaded By') }}</th>
                <th class="p-3 text-left">{{ __('Date') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($files as $file)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $file->file_name }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $file->file_type }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $file->uploadedByUser?->email ?? '-' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $file->created_at->format('d.m.Y') }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('files.download', $file) }}" class="table-action view mr-2">{{ __('Download') }}</a>
                        @if(Auth::user()->user_id === $file->uploaded_by_user_id)
                            <button type="button" class="table-action delete" x-data x-on:click="$dispatch('open-modal', 'delete-file-{{ $file->file_id }}')">
                                {{ __('Delete') }}
                            </button>
                            <x-modal name="delete-file-{{ $file->file_id }}" :show="false" focusable>
                                <form method="POST" action="{{ route('files.delete', ['club', $file->clubs->first()?->club_id, $file->file_id]) }}" class="p-6 text-left">
                                    @csrf
                                    @method('DELETE')
                                    <h2 class="my-heading">{{ __('Delete File') }}</h2>
                                    <p class="my-text">{{ __('Are you sure you want to delete') }} <strong>{{ $file->file_name }}</strong>?</p>
                                    <div class="flex justify-end gap-3 mt-6">
                                        <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                        <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                                    </div>
                                </form>
                            </x-modal>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">{{ __('No files found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $files->links() }}
</div>
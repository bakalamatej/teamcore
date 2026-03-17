<div class="border border-gray-300 rounded-md overflow-hidden shadow-md">
    <table class="w-full data-table">
        <thead class="bg-gray-100">
            <tr class="border-b">
                <th class="p-3 text-left">{{ __('Name') }}</th>
                <th class="p-3 text-left">{{ __('Email') }}</th>
                <th class="p-3 text-center">{{ __('Role') }}</th>
                <th class="p-3 text-left">{{ __('Created') }}</th>
                <th class="p-3 text-right">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr class="border-b hover:bg-gray-50 data-row">
                    <td class="p-3 font-medium">{{ $user->member?->full_name ?? 'N/A' }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $user->email }}</td>
                    <td class="p-3 text-center">
                        <span @class([
                            'px-3 py-1 rounded-full text-xs font-semibold',
                            'bg-red-200 text-red-800' => $user->primaryRole === 'admin',
                            'bg-blue-200 text-blue-800' => $user->primaryRole === 'coach',
                            'bg-green-200 text-green-800' => !in_array($user->primaryRole, ['admin', 'coach'], true),
                        ])>
                            {{ ucfirst($user->primaryRole) }}
                        </span>
                    </td>
                    <td class="p-3 text-sm text-gray-600">{{ $user->created_at->format('d.m.Y') }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('panel.users.show', $user) }}" class="table-action view mr-2">
                            {{ __('View') }}
                        </a>

                        @if($user->user_id !== Auth::id())
                            <a href="{{ route('panel.users.edit', $user) }}" class="table-action edit mr-2">
                                {{ __('Edit') }}
                            </a>

                            <button type="button" class="table-action delete"
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'confirm-user-deletion-{{ $user->user_id }}')">
                                {{ __('Delete') }}
                            </button>

                            <x-modal name="confirm-user-deletion-{{ $user->user_id }}" :show="false" focusable>
                                <form method="POST" action="{{ route('panel.users.destroy', $user) }}" class="p-6 text-left">
                                    @csrf
                                    @method('DELETE')

                                    <h2 class="my-heading">{{ __('Delete User') }}</h2>
                                    <p class="my-text">
                                        {{ __('Are you sure you want to delete') }} <strong>{{ $user->member?->full_name ?? $user->email }}</strong>?
                                        {{ __('This action cannot be undone.') }}
                                    </p>

                                    <div class="flex justify-end gap-3 mt-6">
                                        <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                            {{ __('Cancel') }}
                                        </x-secondary-button>

                                        <x-danger-button type="submit">
                                            {{ __('Delete User') }}
                                        </x-danger-button>
                                    </div>
                                </form>
                            </x-modal>
                        @else
                            <span class="text-xs text-gray-500">{{ __('(You)') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        {{ __('No users found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>

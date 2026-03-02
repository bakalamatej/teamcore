<!-- Load user search JS for real-time filtering -->
@push('scripts')
    @vite(['resources/js/shared/table-search.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-screen">
        <!-- Admin panel sidebar navigation -->
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        <!-- Main content: users list with search/filter -->
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="my-heading text-2xl">{{ __('Users Management') }}</h1>
                    <span class="text-sm text-gray-600">{{ $users->total() }} {{ __('users total') }}</span>
                </div>

                <!-- Search & role filter section -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <form method="GET" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <x-input-label :value="__('Search')" />
                            <x-text-input
                                id="search"
                                type="text"
                                name="search"
                                placeholder="{{ __('Name or email...') }}"
                                class="mt-1 block w-full text-sm"
                                :value="request('search')"
                            />
                        </div>

                        <!-- Filter button -->
                        <div class="flex items-end">
                            <x-primary-button type="submit" class="mt-7">
                                {{ __('Filter') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                <!-- Users Table -->
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
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($user->getRole() === 'admin') bg-red-200 text-red-800
                                            @elseif($user->getRole() === 'coach') bg-blue-200 text-blue-800
                                            @else bg-green-200 text-green-800
                                            @endif">
                                            {{ ucfirst($user->getRole()) }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-sm text-gray-600">{{ $user->created_at->format('d.m.Y') }}</td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('panel.users.show', $user) }}" class="table-action view mr-2">
                                            {{ __('View') }}
                                        </a>

                                        @if($user->id !== Auth::id())
                                            <a href="{{ route('panel.users.edit', $user) }}" class="table-action edit mr-2">
                                                {{ __('Edit') }}
                                            </a>

                                            <button type="button" class="table-action delete"
                                                    x-data
                                                    x-on:click="$dispatch('open-modal', 'confirm-user-deletion-{{ $user->id }}')">
                                                {{ __('Delete') }}
                                            </button>

                                            <x-modal name="confirm-user-deletion-{{ $user->id }}" :show="false" focusable>
                                                <form method="POST" action="{{ route('panel.users.destroy', $user) }}" class="p-6 text-left">
                                                    @csrf
                                                    @method('DELETE')

                                                    <h2 class="my-heading">{{ __('Delete User') }}</h2>
                                                    <p class="my-text">
                                                        {{ __('Are you sure you want to delete') }} <strong>{{ $user->name }}</strong>?
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

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
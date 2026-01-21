<x-app-layout>
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="my-heading text-2xl">{{ $user->name }}</h1>
                <p class="text-gray-600">{{ $user->email }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-semibold
                @if($user->role === 'admin') bg-red-200 text-red-800
                @elseif($user->role === 'coach') bg-blue-200 text-blue-800
                @else bg-green-200 text-green-800
                @endif">
                {{ ucfirst($user->role) }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- User Details -->
                <div class="detail-card">
                    <h2 class="detail-card-header">{{ __('User Information') }}</h2>
                    
                    <div class="space-y-4">
                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Name:') }}</span>
                            <span class="detail-item-value">{{ $user->name }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Email:') }}</span>
                            <span class="detail-item-value">{{ $user->email }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Role:') }}</span>
                            <span class="detail-item-value">{{ ucfirst($user->role) }}</span>
                        </div>

                        <div class="detail-item-divider">
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Created:') }}</span>
                                <span class="detail-item-value">{{ $user->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <span class="detail-item-label">{{ __('Last Updated:') }}</span>
                            <span class="detail-item-value">{{ $user->updated_at->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Member Info -->
                @if($user->member)
                    <div class="detail-card mt-6">
                        <h2 class="detail-card-header">{{ __('Member Profile') }}</h2>
                        
                        <div class="space-y-4">
                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Full Name:') }}</span>
                                <span class="detail-item-value">{{ $user->member->full_name }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Phone:') }}</span>
                                <span class="detail-item-value">{{ $user->member->phone ?? '-' }}</span>
                            </div>

                            <div class="detail-item-divider">
                                <span class="detail-item-label">{{ __('Clubs:') }}</span>
                                <span class="detail-item-value">{{ $user->member->activeClubs->count() }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-item-label">{{ __('Events:') }}</span>
                                <span class="detail-item-value">{{ $user->member->activeEvents->count() }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 flex flex-col">
                <div class="sidebar-card sidebar-card-blue">
                    <h3 class="sidebar-card-title">{{ __('Statistics') }}</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="stat-label">{{ __('Account Age') }}</p>
                            <p class="stat-value" style="color: #4f46e5;">
                                {{ (int) $user->created_at->diffInDays(now()) }} {{ __('days') }}
                            </p>
                        </div>
                        
                        @if($user->member)
                            <div class="stat-divider">
                                <p class="stat-label">{{ __('Member Since') }}</p>
                                <p class="stat-value" style="color: #4f46e5;">{{ $user->member->created_at->format('M Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3 mt-auto">
                    <x-primary-button class="w-full justify-center" :href="route('panel.users.edit', $user)">
                        {{ __('Edit User') }}
                    </x-primary-button>

                    @if($user->id !== Auth::id())
                        <x-danger-button type="button" class="w-full justify-center"
                                x-data
                                x-on:click="$dispatch('open-modal', 'confirm-user-deletion')">
                            {{ __('Delete User') }}
                        </x-danger-button>

                        <x-modal name="confirm-user-deletion" :show="false" focusable>
                            <form method="POST" action="{{ route('panel.users.destroy', $user) }}" class="p-6 text-left">
                                @csrf
                                @method('DELETE')

                                <h2 class="my-heading">{{ __('Delete User') }}</h2>
                                <p class="my-text">{{ __('This action cannot be undone.') }}</p>

                                <div class="flex justify-end gap-3 mt-6">
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>
                                    <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    @endif

                    <x-secondary-button class="w-full justify-center" :href="route('panel.users.index')">
                        {{ __('Back to Users') }}
                    </x-secondary-button>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
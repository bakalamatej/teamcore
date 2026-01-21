<x-app-layout>
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <h1 class="my-heading">{{ __('Edit User') }}</h1>
                <p class="my-text">{{ __('Update the user information such as name, email, and role.') }}</p>

            <form method="POST" action="{{ route('panel.users.update', $user) }}" class="space-y-4 max-w-2xl">
                @csrf
                @method('PATCH')

                <!-- Name -->
                <div class="max-w-xl">
                    <x-input-label :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-[70%]"
                        value="{{ old('name', $user->name) }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="max-w-xl">
                    <x-input-label :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-[70%]"
                        value="{{ old('email', $user->email) }}" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Role -->
                <div class="max-w-xl">
                    <x-input-label :value="__('Role')" />
                    <x-select-input
                        id="role"
                        name="role"
                        :options="['player' => __('Player'), 'coach' => __('Coach'), 'admin' => __('Admin')]"
                        :selected="old('role', $user->role)"
                        class="mt-1 block w-[70%]"
                        required
                    />
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 mt-6">
                    <x-primary-button>{{ __('Update User') }}</x-primary-button>
                    <x-secondary-button type="button" onclick="window.history.back()">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                </div>
            </form>
            </div>
        </main>
    </div>
</x-app-layout>
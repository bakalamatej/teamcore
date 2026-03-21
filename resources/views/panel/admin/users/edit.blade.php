<x-panel-layout>
            <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
                <h1 class="my-heading">{{ __('Edit User') }}</h1>
                <p class="my-text">{{ __('Update the user information such as name, email, and role.') }}</p>

                <form method="POST" action="{{ route('panel.admin.users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <div class="flex flex-col gap-4">
                        <div class="max-w-xl">
                            <x-input-label :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-[70%]"
                                value="{{ old('email', $user->email) }}" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="max-w-xl">
                            <x-input-label :value="__('First Name')" />
                            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-[70%]"
                                value="{{ old('first_name', $user->member?->first_name ?? '') }}" required />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>

                        <div class="max-w-xl">
                            <x-input-label :value="__('Last Name')" />
                            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-[70%]"
                                value="{{ old('last_name', $user->member?->last_name ?? '') }}" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>

                        <div class="max-w-xl">
                            <x-input-label :value="__('Phone Number')" />
                            <x-text-input id="phone_number" name="phone_number" type="tel" class="mt-1 block w-[70%]"
                                value="{{ old('phone_number', $user->member?->phone_number ?? '') }}" />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>

                        <div class="max-w-xl">
                            <x-input-label :value="__('Date of Birth')" />
                            <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-[70%]"
                                value="{{ old('date_of_birth', $user->member?->date_of_birth?->format('Y-m-d') ?? '') }}" />
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                        </div>

                        <div class="max-w-xl">
                            <div class="flex items-center gap-3 mt-1">
                                <input id="is_admin" type="checkbox" name="is_admin" value="1" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    @if(old('is_admin', $user->is_admin)) checked @endif />
                                <x-input-label htmlFor="is_admin" :value="__('Admin')" class="mt-0" />
                            </div>
                            <x-input-error :messages="$errors->get('is_admin')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 mt-6">
                        <x-primary-button>{{ __('Update User') }}</x-primary-button>
                        <x-danger-button :href="route('panel.admin.users.index')">
                            {{ __('Discard') }}
                        </x-danger-button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-panel-layout>
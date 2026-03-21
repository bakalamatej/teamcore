<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <h1 class="my-heading">{{ __('Create Address') }}</h1>
        <p class="my-text">{{ __('Add a new address location.') }}</p>

        <form method="POST" action="{{ route('panel.admin.addresses.store') }}" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div class="max-w-xl">
                    <x-input-label for="country" :value="__('Country')" />
                    <x-text-input id="country" name="country" type="text" class="mt-1 block w-[70%]" value="{{ old('country') }}" required />
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>

                <div class="max-w-xl">
                    <x-input-label for="city" :value="__('City')" />
                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-[70%]" value="{{ old('city') }}" required />
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>

                <div class="max-w-xl">
                    <x-input-label for="street" :value="__('Street')" />
                    <x-text-input id="street" name="street" type="text" class="mt-1 block w-[70%]" value="{{ old('street') }}" required />
                    <x-input-error :messages="$errors->get('street')" class="mt-2" />
                </div>

                <div class="max-w-xl">
                    <x-input-label for="zip_code" :value="__('Zip Code')" />
                    <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-[70%]" value="{{ old('zip_code') }}" required />
                    <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <x-danger-button :href="route('panel.admin.addresses.index')">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>

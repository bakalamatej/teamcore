<x-app-layout>
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading">{{ __('Edit Address') }}</h1>

        <form method="POST" action="{{ route('panel.addresses.update', $address) }}" class="space-y-4 max-w-2xl">
            @csrf
            @method('PATCH')

            <div class="space-y-4">
                <div class="max-w-xl">
                    <x-input-label for="country" :value="__('Country')" />
                    <x-text-input id="country" name="country" type="text" class="mt-1 block w-[70%]" value="{{ $address->country }}" required />
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>

                <div class="max-w-xl">
                    <x-input-label for="city" :value="__('City')" />
                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-[70%]" value="{{ $address->city }}" required />
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>

                <div class="max-w-xl">
                    <x-input-label for="street" :value="__('Street')" />
                    <x-text-input id="street" name="street" type="text" class="mt-1 block w-[70%]" value="{{ $address->street }}" required />
                    <x-input-error :messages="$errors->get('street')" class="mt-2" />
                </div>

                <div class="max-w-xl">
                    <x-input-label for="zip_code" :value="__('Zip Code')" />
                    <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-[70%]" value="{{ $address->zip_code }}" required />
                    <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
                <x-danger-button type="button" onclick="window.location='{{ route('panel.addresses.index') }}'">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
            </div>
        </main>
    </div>
</x-app-layout>

@push('scripts')
    @vite(['resources/js/clubs/address-toggle.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <h1 class="my-heading">{{ __('Create Club') }}</h1>
        <p class="my-text">{{ __('Create a new sports club. Provide all necessary information including name, contact details, and location.') }}</p>
        <form action="{{ route('panel.admin.clubs.store') }}" method="POST" class="space-y-6">
        @csrf
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1 space-y-4">
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>       
                        <x-input-label for="phone" :value="__('Phone')" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" value="{{ old('phone') }}"  />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="webpage" :value="__('Webpage')" />
                        <x-text-input id="webpage" name="webpage" type="text" class="mt-1 block w-full" value="{{ old('webpage') }}" />
                        <x-input-error :messages="$errors->get('webpage')" class="mt-2" />
                    </div>
                </div>
                <div class="flex-1 space-y-4">
                    <div>
                        <div>
                            <x-input-label for="sport_id" :value="__('Sport')" />
                            <x-select-input id="sport_id" name="sport_id" :options="$sportOptions" :selected="old('sport_id')" placeholder="{{ __('Select sport') }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('sport_id')" class="mt-2" />
                        </div>
                    </div>
                    <div id="existing-address-block">
                        <x-input-label for="address_id" :value="__('Address')" />
                        <x-select-input id="address_id" name="address_id" :options="$addressOptions" :selected="old('address_id')" placeholder="{{ __('Select address') }}" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('address_id')" class="mt-2" />
                        <p class="mt-2 text-sm text-blue-600 hover:underline cursor-pointer" id="open-new-address">{{ __('Can\'t find your address?') }}</p>
                    </div>

                    <div id="new-address-fields" class="hidden rounded">
                        <div class="space-y-4 mb-4">
                            <div>
                                <x-input-label for="country" :value="__('Country')" />
                                <x-select-input id="country" name="country" :options="$countryOptions" :selected="old('country')" placeholder="{{ __('Select country') }}" class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('country')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="city" :value="__('City')" />
                                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" value="{{ old('city') }}" />
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="street" :value="__('Street')" />
                                <x-text-input id="street" name="street" type="text" class="mt-1 block w-full" value="{{ old('street') }}" />
                                <x-input-error :messages="$errors->get('street')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="zip_code" :value="__('Zip Code')" />
                                <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-full" value="{{ old('zip_code') }}" />
                                <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                            </div>
                        </div>
                    <p class="mt-2 text-sm text-blue-600 hover:underline cursor-pointer" id="use-existing-address">{{ __('Use existing address') }}</p>
                    </div>
                </div>


            </div>
            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Create') }}</x-primary-button>
                <x-danger-button :href="route('panel.admin.clubs.index')">{{ __('Discard') }}</x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>
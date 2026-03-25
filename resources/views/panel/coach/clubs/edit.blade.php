@push('scripts')
    @vite(['resources/js/clubs/address-toggle.js'])
@endpush

<x-panel-layout>
    <div class="bg-white shadow-xl rounded-lg p-4 sm:p-8">
        <div class="max-w-xl">
            <section class="space-y-6">
                <header>
                    <h2 class="my-heading">{{ __('Edit Club') }}</h2>
                </header>

                <form action="{{ route('panel.coach.clubs.update', $club) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')
                    <div class="flex flex-col gap-4">
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $club->name) }}" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('Phone')" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" value="{{ old('phone', $club->phone) }}" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $club->email) }}" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="webpage" :value="__('Webpage')" />
                                <x-text-input id="webpage" name="webpage" type="text" class="mt-1 block w-full" value="{{ old('webpage', $club->webpage) }}" />
                                <x-input-error :messages="$errors->get('webpage')" class="mt-2" />
                            </div>
                            <div id="existing-address-block">
                                <x-input-label for="address_id" :value="__('Address')" />
                                <x-select-input id="address_id" name="address_id" :options="$addressOptions" :selected="old('address_id', $club->address_id)" placeholder="{{ __('Select address') }}" class="mt-1 block w-full" />
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
                    <div class="flex gap-4 mt-4">
                        <x-primary-button>{{ __('Update') }}</x-primary-button>
                        <x-danger-button :href="route('panel.coach.clubs.index')">{{ __('Discard') }}</x-danger-button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-panel-layout>

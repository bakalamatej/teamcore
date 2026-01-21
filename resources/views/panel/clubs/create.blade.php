@push('scripts')
    @vite(['resources/js/clubs/club-create.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
                <h1 class="my-heading">{{ __('Create Club') }}</h1>
                <p class="my-text">{{ __('Create a new sports club. Provide all necessary information including name, contact details, and location.') }}</p>

                <form id="clubCreateForm" data-action="{{ route('clubs.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div id="formErrorBox">
                        <span id="formErrorMessage"></span>
                        <button type="button" id="formErrorClose">×</button>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-6">
                        <div class="flex-1 space-y-4">
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="__('Phone')" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" required />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                            </div>

                            <div>
                                <x-input-label for="webpage" :value="__('Webpage')" />
                                <x-text-input id="webpage" name="webpage" type="text" class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div class="flex-1 space-y-4">
                            <div>
                                <x-input-label for="address_id" :value="__('Address')" />
                                <x-select-input
                                    id="address_id"
                                    name="address_id"
                                    :options="$addresses->mapWithKeys(fn($a) => [
                                        $a->id => $a->street.' '.$a->number.', '.$a->city
                                    ])->toArray()"
                                    :selected="old('address_id')"
                                    placeholder="{{ __('Select address') }}"
                                    class="w-full"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <x-danger-button type="button" onclick="window.location='{{ route('panel.index') }}'">
                            {{ __('Discard') }}
                        </x-danger-button>
                    </div>
                </form>

                <x-modal name="create-club" :show="false">
                    <div class="p-4">
                        <h2 class="text-lg font-semibold mb-2">{{ __('Club created successfully!') }}</h2>
                        <p class="text-sm text-gray-700">{{ __('The club has been saved.') }}</p>
                        <div class="mt-4 text-right">
                            <button
                                x-on:click="$dispatch('close-modal', 'create-club')"
                                class="bg-indigo-600 text-white px-4 py-2 rounded"
                            >
                                {{ __('Close') }}
                            </button>
                        </div>
                    </div>
                </x-modal>
            </div>
        </main>
    </div>
</x-app-layout>
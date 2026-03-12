@push('scripts')
    @vite(['resources/js/clubs/club-update.js'])
@endpush

<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading">{{ __('Edit Club') }}</h1>

        <form id="updateClubForm" data-action="{{ route('clubs.update', $club) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <div id="formErrorBox">
                <span id="formErrorMessage"></span>
                <button type="button" id="formErrorClose">×</button>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1 space-y-4">
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ $club->name }}" required />
                    </div>

                    <div>
                        <x-input-label for="phone" :value="__('Phone')" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" value="{{ $club->phone }}" required />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ $club->email }}" required />
                    </div>

                    <div>
                        <x-input-label for="webpage" :value="__('Webpage')" />
                        <x-text-input id="webpage" name="webpage" type="text" class="mt-1 block w-full" value="{{ $club->webpage }}" />
                    </div>
                </div>

                <div class="flex-1 space-y-4">
                    <div>
                        <x-input-label for="address_id" :value="__('Address')" />
                        <x-select-input
                            id="address_id"
                            name="address_id"
                            :options="$addresses->mapWithKeys(fn($a) => [$a->address_id => $a->street.' '.$a->number.', '.$a->city])->toArray()"
                            :selected="$club->address_id"
                            placeholder="Select address"
                            class="mt-1 block w-full"
                        />
                    </div>

                    <div>
                        <x-input-label :value="__('Sports')" />
                        <x-multiselect-input
                            id="sport_ids"
                            name="sport_ids"
                            :options="$sports->pluck('name', 'sport_id')->toArray()"
                            :selected="$club->sports->pluck('sport_id')->toArray()"
                            placeholder="{{ __('Select sports...') }}"
                            class="mt-1 block w-full"
                        />
                    </div>
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
                <x-danger-button type="button" onclick="window.location='{{ route('clubs.index') }}'">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>

        <x-modal name="update-club" :show="false">
            <div class="p-4">
                <h2 class="text-lg font-semibold mb-2">{{ __('Club updated successfully!') }}</h2>
                <p class="text-sm text-gray-700">{{ __('Your changes have been saved.') }}</p>
                <div class="mt-4 text-right">
                    <button
                        x-on:click="$dispatch('close-modal', 'update-club')"
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

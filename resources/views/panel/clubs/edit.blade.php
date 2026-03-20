@push('scripts')
    @vite(['resources/js/clubs/club-update.js'])
@endpush

<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <h1 class="my-heading">{{ __('Edit Club') }}</h1>

        <form id="updateClubForm" data-action="{{ route('panel.clubs.update', $club) }}" method="POST" class="space-y-6">
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
                            :options="$addressOptions"
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
                            :options="$sportOptions"
                            :selected="$selectedSportIds"
                            placeholder="{{ __('Select sports...') }}"
                            class="mt-1 block w-full"
                        />
                    </div>
                </div>
            </div>

            <div class="flex gap-4 mt-4">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
                <x-danger-button :href="route('panel.clubs.index')">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>

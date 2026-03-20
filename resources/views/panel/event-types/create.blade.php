<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <h1 class="my-heading">{{ __('Create Event Type') }}</h1>
        <p class="my-text">{{ __('Add a new event type.') }}</p>

        <form method="POST" action="{{ route('panel.event-types.store') }}" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div class="max-w-xl">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-[70%]" value="{{ old('name') }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="max-w-xl">
                    <x-input-label for="sport_id" :value="__('Sport')" />
                    <x-select-input
                        id="sport_id"
                        name="sport_id"
                        :options="$sportOptions"
                        :selected="old('sport_id')"
                        placeholder="{{ __('Select sport') }}"
                        class="w-[70%]"
                    />
                    <x-input-error :messages="$errors->get('sport_id')" class="mt-2" />
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <x-danger-button :href="route('panel.event-types.index')">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
    </div>
</x-panel-layout>

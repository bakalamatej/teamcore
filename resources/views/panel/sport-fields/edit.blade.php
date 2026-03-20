<x-panel-layout>
        <div class="bg-white overflow-visible shadow-xl rounded-lg sm:p-8">
            <h1 class="my-heading">{{ __('Edit Sport Field') }}</h1>

            <form method="POST" action="{{ route('panel.sport-fields.update', $sportField) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="space-y-4">
                    <div class="max-w-xl">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-[70%]" value="{{ $sportField->name }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="max-w-xl">
                        <x-input-label for="field_type_id" :value="__('Field Type')" />
                        <x-select-input
                            id="field_type_id"
                            name="field_type_id"
                            :options="$fieldTypeOptions"
                            :selected="old('field_type_id', $sportField->field_type_id)"
                            placeholder="{{ __('Select field type') }}"
                            class="w-[70%]"
                        />
                        <x-input-error :messages="$errors->get('field_type_id')" class="mt-2" />
                    </div>

                    <div class="max-w-xl">
                        <x-input-label for="address_id" :value="__('Address')" />
                        <x-select-input
                            id="address_id"
                            name="address_id"
                            :options="$addressOptions"
                            :selected="$sportField->address_id"
                            placeholder="{{ __('Select address') }}"
                            class="w-[70%]"
                        />
                        <x-input-error :messages="$errors->get('address_id')" class="mt-2" />
                    </div>

                    <div class="max-w-xl">
                        <x-input-label :value="__('Sports')" />
                        <x-multiselect-input
                            id="sport_ids"
                            name="sport_ids"
                            :options="$sportOptions"
                            :selected="old('sport_ids', $selectedSportIds)"
                            :placeholder="__('Select sports')"
                            class="mt-1 block w-[70%]"
                        />
                        <x-input-error :messages="$errors->get('sport_ids')" class="mt-2" />
                        <x-input-error :messages="$errors->get('sport_ids.*')" class="mt-2" />
                    </div>
                </div>

                <div class="flex gap-4 mt-6">
                    <x-primary-button>{{ __('Update') }}</x-primary-button>
                    <x-danger-button :href="route('panel.sport-fields.index')">
                        {{ __('Discard') }}
                    </x-danger-button>
                </div>
            </form>
            </div>
        </main>
    </div>
</x-panel-layout>

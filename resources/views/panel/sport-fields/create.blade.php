<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        {{-- Sidebar --}}
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading">{{ __('Create Sport Field') }}</h1>
        <p class="my-text">{{ __('Add a new sport field.') }}</p>

        <form method="POST" action="{{ route('panel.sport-fields.store') }}" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div class="max-w-xl">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-[70%]" value="{{ old('name') }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="max-w-xl">
                    <x-input-label for="field_type" :value="__('Field Type')" />
                    <x-text-input id="field_type" name="field_type" type="text" class="mt-1 block w-[70%]" value="{{ old('field_type') }}" required />
                    <x-input-error :messages="$errors->get('field_type')" class="mt-2" />
                </div>

                <div class="max-w-xl">
                    <x-input-label for="address_id" :value="__('Address')" />
                    <x-select-input
                        id="address_id"
                        name="address_id"
                        :options="$addresses->mapWithKeys(fn($a) => [$a->address_id => $a->street . ', ' . $a->zip_code . ' ' . $a->city])->toArray()"
                        :selected="old('address_id')"
                        placeholder="{{ __('Select address') }}"
                        class="w-[70%]"
                    />
                    <x-input-error :messages="$errors->get('address_id')" class="mt-2" />
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <x-danger-button type="button" onclick="window.location='{{ route('panel.sport-fields.index') }}'">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
            </div>
        </main>
    </div>
</x-app-layout>

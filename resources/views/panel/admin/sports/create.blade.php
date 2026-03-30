<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading">{{ __('Create Sport') }}</h1>
        <p class="my-text">{{ __('Add a new sport type.') }}</p>

        <form method="POST" action="{{ route('panel.admin.sports.store') }}" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div class="max-w-xl">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-[70%]" value="{{ old('name') }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <x-danger-button :href="route('panel.admin.sports.index')">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
            </div>
        </main>
    </div>
</x-panel-layout>

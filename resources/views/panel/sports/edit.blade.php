<x-panel-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:p-8">
        <h1 class="my-heading">{{ __('Edit Sport') }}</h1>

        <form method="POST" action="{{ route('panel.sports.update', $sport) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="space-y-4">
                <div class="max-w-xl">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-[70%]" value="{{ $sport->name }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
                <x-danger-button :href="route('panel.sports.index')">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
            </div>
        </main>
    </div>
</x-panel-layout>

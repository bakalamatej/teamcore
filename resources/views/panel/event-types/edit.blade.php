<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        {{-- Sidebar --}}
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pl-0 xl:pl-[280px]">
            <div class="mx-auto bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading">{{ __('Edit Event Type') }}</h1>

        <form method="POST" action="{{ route('panel.event-types.update', $eventType) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="space-y-4">
                <div class="max-w-xl">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-[70%]" value="{{ $eventType->name }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="max-w-xl">
                    <x-input-label for="sport_id" :value="__('Sport')" />
                    <x-select-input
                        id="sport_id"
                        name="sport_id"
                        :options="$sportOptions"
                        :selected="$eventType->sport_id"
                        placeholder="{{ __('Select sport') }}"
                        class="w-[70%]"
                    />
                    <x-input-error :messages="$errors->get('sport_id')" class="mt-2" />
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
                <x-danger-button :href="route('panel.event-types.index')">
                    {{ __('Discard') }}
                </x-danger-button>
            </div>
        </form>
            </div>
        </main>
    </div>
</x-app-layout>

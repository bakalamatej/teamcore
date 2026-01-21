<x-sidebar :title="__('Filters')">
    <!-- Search & filter form -->
    <form method="GET" class="space-y-4">
        <!-- Search by club name -->
        <div>
            <x-input-label :value="__('Search')" />
            <x-text-input
                id="search"
                type="text"
                name="search"
                placeholder="{{ __('Search...') }}"
                class="mt-1 block w-full text-sm"
                :value="request('search')"
            />
        </div>    

        <!-- Filter by city -->
        <div>
            <x-input-label :value="__('City')" />
            <x-select-input
                id="city"
                name="city"
                :options="$cityOptions"
                :selected="request('city')"
                placeholder="{{ __('Select city') }}"
                class="mt-1 block w-full text-sm"
            />
        </div>

        <!-- Submit button -->
        <x-primary-button type="submit" class="w-full justify-center mt-6">
            {{ __('Filter') }}
        </x-primary-button>
    </form>
</x-sidebar>
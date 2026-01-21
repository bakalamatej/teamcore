<x-sidebar :title="__('Filters')">
    <!-- Search & filter form -->
    <form method="GET" class="space-y-4">
        <!-- Search by event title -->
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

        <!-- Filter by location (sport field) -->
        <div>
            <x-input-label :value="__('Location')" />
            <x-select-input
                id="sport_field_id"
                name="sport_field_id"
                :options="$sportFields->mapWithKeys(fn($f) => [$f->id => $f->name . ' (' . ($f->address->city ?? '-') . ')'])->toArray()"
                :selected="request('sport_field_id')"
                placeholder="{{ __('Select location') }}"
                class="mt-1 block w-full text-sm"
            />
        </div>

        <div>
            <x-input-label :value="__('Status')" />
            <x-select-input
                id="status"
                name="status"
                :options="['' => __('All'), 'scheduled' => __('Scheduled'), 'finished' => __('Finished'), 'cancelled' => __('Cancelled')]"
                :selected="request('status')"
                class="mt-1 block w-full text-sm"
            />
        </div>

        <div>
            <x-input-label :value="__('Type')" />
            <x-select-input
                id="type"
                name="type"
                :options="$eventTypes->mapWithKeys(fn($t) => [$t->id => $t->name])->toArray()"
                :selected="request('type')"
                placeholder="Select type"
                class="mt-1 block w-full text-sm"
            />
        </div>

        <x-primary-button type="submit" class="w-full justify-center">
            {{ __('Filter') }}
        </x-primary-button>
    </form>
</x-sidebar>
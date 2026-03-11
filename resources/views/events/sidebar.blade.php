<x-sidebar :title="__('Filters')">
    <form id="filter-form" method="GET" action="{{ route('events.index') }}" class="space-y-4">

        <!-- Search -->
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
                :options="$sportFields->mapWithKeys(fn($f) => [$f->sport_field_id => $f->name . ' (' . ($f->address->city ?? '-') . ')'])->toArray()"
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
                placeholder="{{ __('Select status') }}"
                class="mt-1 block w-full text-sm"
            />
        </div>

        <div>
            <x-input-label :value="__('Type')" />
            <x-select-input
                id="type"
                name="type"
                :options="$eventTypes->mapWithKeys(fn($t) => [$t->event_type_id => $t->name])->toArray()"
                :selected="request('type')"
                placeholder="{{ __('All types') }}"
                class="mt-1 block w-full text-sm"
            />
        </div>

        <div>
            <x-input-label :value="__('Date from')" />
            <x-text-input
                id="start_date_from"
                type="date"
                name="start_date_from"
                class="mt-1 block w-full text-sm"
                :value="request('start_date_from')"
            />
        </div>

    </form>
</x-sidebar>
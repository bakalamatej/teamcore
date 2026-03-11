@props(['id', 'name', 'options' => [], 'selected' => [], 'placeholder' => 'Select options'])

<div
    x-data="{
        open: false,
        selected: {{ json_encode(array_map('strval', (array) $selected)) }},
        toggle(val) {
            val = String(val);
            if (this.selected.includes(val)) {
                this.selected = this.selected.filter(v => v !== val);
            } else {
                this.selected.push(val);
            }
            this.$nextTick(() => {
                this.$el.dispatchEvent(new Event('input', { bubbles: true }));
            });
        },
        label(options) {
            if (this.selected.length === 0) return '{{ $placeholder }}';
            return this.selected.map(v => options[v] ?? v).join(', ');
        }
    }"
    x-on:click.outside="open = false"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    {{-- Hidden inputs --}}
    <template x-for="val in selected" :key="val">
        <input type="hidden" name="{{ $name }}[]" :value="val" />
    </template>

    {{-- Trigger button --}}
    <button
        type="button"
        x-on:click="open = !open"
        class="w-full flex items-center justify-between border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md shadow-md bg-white px-3 py-[10px] text-sm text-left"
    >
        <span x-text="label({{ json_encode($options) }})" class="truncate" :class="selected.length === 0 ? 'text-gray-400' : 'text-gray-800'"></span>
        <svg class="ml-2 h-4 w-4 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-56 overflow-y-auto"
        style="display:none"
    >
        @foreach ($options as $key => $label)
            <label class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 cursor-pointer">
                <input
                    type="checkbox"
                    :checked="selected.includes('{{ $key }}')"
                    x-on:change="toggle('{{ $key }}')"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm"
                />
                {{ $label }}
            </label>
        @endforeach
    </div>
</div>

@props([
    'id',
    'name',
    'options' => [],
    'optionsVar' => null,
    'selected' => [],
    'placeholder' => 'Select options',
    'disabledWhen' => null,
])

@php
    $staticOptionsJs = \Illuminate\Support\Js::from($options);
@endphp

<div
    x-data="{
        open: false,
        dropdownUp: false,
        selected: {{ json_encode(array_map('strval', (array) $selected)) }},
        updateDropdownPlacement() {
            this.$nextTick(() => {
                const triggerRect = this.$el.getBoundingClientRect();
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                const dropdownHeight = 240;
                const spaceBelow = viewportHeight - triggerRect.bottom - 8;
                const spaceAbove = triggerRect.top;

                this.dropdownUp = spaceBelow < dropdownHeight && spaceAbove > spaceBelow;
            });
        },
        toggleDropdown() {
            if ({{ $disabledWhen ?? 'false' }}) {
                return;
            }

            this.open = !this.open;
            if (this.open) {
                this.updateDropdownPlacement();
                requestAnimationFrame(() => this.updateDropdownPlacement());
            }
        },
        toggle(val) {
            val = String(val);
            const current = (this.selected ?? []).map(v => String(v));

            if (current.includes(val)) {
                this.selected = current.filter(v => v !== val);
            } else {
                this.selected = [...current, val];
            }

            this.$nextTick(() => {
                this.$el.dispatchEvent(new Event('input', { bubbles: true }));
            });
        },
        label(options) {
            if (this.selected.length === 0) return '{{ $placeholder }}';
            return this.selected.map(v => options[String(v)] ?? v).join(', ');
        }
    }"
    x-modelable="selected"
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
        @if($disabledWhen) :disabled="{{ $disabledWhen }}" @endif
        x-on:click="toggleDropdown()"
        class="w-full flex items-center justify-between border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md shadow-md bg-white px-3 py-[10px] text-sm text-left"
        :class="{ 'opacity-50 cursor-not-allowed': {{ $disabledWhen ?? 'false' }} }"
    >
        @if ($optionsVar)
            <span x-text="label({!! $optionsVar !!})" class="truncate" :class="selected.length === 0 ? 'text-gray-400' : 'text-gray-800'"></span>
        @else
            <span x-text="label({{ $staticOptionsJs }})" class="truncate" :class="selected.length === 0 ? 'text-gray-400' : 'text-gray-800'"></span>
        @endif
        <svg class="ml-2 h-4 w-4 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown panel --}}
    <div
        x-ref="dropdown"
        x-show="open"
        x-on:window.resize="if (open) updateDropdownPlacement()"
        x-on:window.scroll="if (open) updateDropdownPlacement()"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-56 overflow-y-auto"
        :class="dropdownUp ? 'bottom-full mb-1' : 'mt-1'"
        style="display:none"
    >
        @if ($optionsVar)
            <template x-for="[optionKey, optionLabel] in Object.entries({!! $optionsVar !!})" :key="optionKey">
                <label class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 cursor-pointer">
                    <input
                        type="checkbox"
                        :checked="(selected ?? []).map(v => String(v)).includes(String(optionKey))"
                        x-on:change="toggle(String(optionKey))"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm"
                    />
                    <span x-text="optionLabel"></span>
                </label>
            </template>
        @else
            @foreach ($options as $optionKey => $optionLabel)
                <label class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 cursor-pointer">
                    <input
                        type="checkbox"
                        :checked="(selected ?? []).map(v => String(v)).includes('{{ (string) $optionKey }}')"
                        x-on:change="toggle('{{ (string) $optionKey }}')"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm"
                    />
                    {{ $optionLabel }}
                </label>
            @endforeach
        @endif
    </div>
</div>

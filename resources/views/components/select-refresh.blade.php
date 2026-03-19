@props([
    'id' => null,
    'name',
    'options' => [],
    'selected' => null,
    'required' => false,
    'placeholder' => null,
    'disabled' => false
])

<div
    x-data="{
        open: false,
        selected: '{{ $selected }}',
        options: @js($options),
        choose(val) {
            this.selected = val;
            this.open = false;
            this.$nextTick(() => {
                let form = this.$el.closest('form');
                if (form && form.tagName === 'FORM') {
                    form.querySelector('input[name=\'{{ $name }}\']').value = val;
                    form.submit();
                }
            });
        }
    }"
    class="relative w-42"
>
    <input type="hidden" name="{{ $name }}" :value="selected" />
    <button
        type="button"
        @if($id) id="{{ $id }}" @endif
        x-on:click="open = !open"
        @if($disabled) disabled @endif
        class="w-full flex items-center justify-between border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md shadow-md bg-white px-3 py-0.5 text-sm text-left text-gray-800 overflow-hidden max-w-sm"
        :class="{ 'opacity-50 cursor-not-allowed': {{ $disabled ? 'true' : 'false' }} }"
        style="min-height: 2rem; max-w-xs;"
    >
        <span
            x-text="selected !== '' && selected !== null ? options[selected] ?? '{{ $placeholder }}' : '{{ $placeholder }}'"
            class="truncate block max-w-xs"
            style="max-w-sm;"
            :class="selected !== '' && selected !== null ? 'text-gray-800' : 'text-gray-400'"
        ></span>
        <svg class="ml-2 h-4 w-4 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <div
        x-show="open"
        x-on:click.outside="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-56 overflow-y-auto mt-1"
        style="display:none"
    >
        @if($placeholder)
            <div
                x-on:click="choose('')"
                class="px-3 py-2 text-sm text-gray-400 hover:bg-gray-50 cursor-pointer"
            >{{ $placeholder }}</div>
        @endif
        <template x-for="(label, key) in options" :key="String(key)">
            <div
                x-on:click="choose(String(key))"
                class="px-3 py-2 text-sm text-gray-800 hover:bg-gray-50 cursor-pointer"
                :class="selected == String(key) ? 'bg-indigo-50 font-medium' : ''"
                x-text="label"
            ></div>
        </template>
    </div>
</div>

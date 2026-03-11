@props(['id' => null, 'name', 'options' => [], 'selected' => null, 'required' => false, 'placeholder' => null, 'disabled' => false])

<div
    x-data="{
        open: false,
        selected: '{{ $selected }}',
        choose(val) {
            this.selected = val;
            this.open = false;
            this.$nextTick(() => {
                this.$el.dispatchEvent(new Event('input', { bubbles: true }));
            });
        }
    }"
    x-on:click.outside="open = false"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    {{-- Hidden input --}}
    <input type="hidden" name="{{ $name }}" :value="selected" @if($required) required @endif />

    {{-- Trigger button --}}
    <button
        type="button"
        @if($id) id="{{ $id }}" @endif
        x-on:click="open = !open"
        @disabled($disabled)
        class="w-full flex items-center justify-between border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md shadow-md bg-white px-3 py-[10px] text-sm text-left text-gray-800"
        :class="{ 'opacity-50 cursor-not-allowed': {{ $disabled ? 'true' : 'false' }} }"
    >
        <span
            x-text="selected !== '' && selected !== null ? ({{ json_encode($options) }})[selected] ?? '{{ $placeholder ?? '' }}' : '{{ $placeholder ?? '' }}'"
            class="truncate"
            :class="selected !== '' && selected !== null ? 'text-gray-800' : 'text-gray-400'"
        ></span>
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
        @if($placeholder)
            <div
                x-on:click="choose('')"
                class="px-3 py-2 text-sm text-gray-400 hover:bg-gray-50 cursor-pointer"
            >{{ $placeholder }}</div>
        @endif
        @foreach($options as $key => $label)
            <div
                x-on:click="choose('{{ $key }}')"
                class="px-3 py-2 text-sm text-gray-800 hover:bg-gray-50 cursor-pointer"
                :class="selected == '{{ $key }}' ? 'bg-indigo-50 font-medium' : ''"
            >{{ $label }}</div>
        @endforeach
    </div>
</div>
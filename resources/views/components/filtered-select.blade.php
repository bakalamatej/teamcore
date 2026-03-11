@props(['name', 'openVar', 'selectedVar', 'optionsVar', 'placeholder' => 'Select...', 'disabledWhen' => null])

<div class="relative" x-on:click.outside="{{ $openVar }} = false">
    <input type="hidden" name="{{ $name }}" :value="{{ $selectedVar }}" />
    <button type="button"
        @if($disabledWhen) :disabled="{{ $disabledWhen }}" @endif
        x-on:click="{{ $openVar }} = !{{ $openVar }}"
        class="mt-1 w-full flex items-center justify-between border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-md shadow-md bg-white px-3 py-[10px] text-sm text-left"
        @if($disabledWhen) :class="{{ $disabledWhen }} ? 'opacity-50 cursor-not-allowed' : ''" @endif
    >
        <span class="truncate" :class="{{ $selectedVar }} ? 'text-gray-800' : 'text-gray-400'"
            x-text="{{ $selectedVar }} ? {{ $optionsVar }}[{{ $selectedVar }}] : '{{ $placeholder }}'"></span>
        <svg :class="{{ $openVar }} ? 'rotate-180' : ''" class="ml-2 h-4 w-4 text-gray-600 flex-shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div x-show="{{ $openVar }}"
        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-56 overflow-y-auto" style="display:none">
        <template x-for="[id, name] in Object.entries({{ $optionsVar }})" :key="id">
            <div x-on:click="{{ $selectedVar }} = id; {{ $openVar }} = false"
                class="px-3 py-2 text-sm text-gray-800 hover:bg-gray-50 cursor-pointer"
                :class="{{ $selectedVar }} == id ? 'bg-indigo-50 font-medium' : ''"
                x-text="name"></div>
        </template>
    </div>
</div>

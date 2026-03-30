@props([
    'id' => null,
    'name',
    'options' => [],
    'selected' => null,
    'required' => false,
    'placeholder' => null,
    'disabled' => false,
    'searchable' => true,
    'searchPlaceholder' => 'Search...'
])

<div
    x-data="selectInput({
        options: @js($options),
        selected: @js($selected),
        placeholderText: @js($placeholder ?? ''),
        inputPlaceholderText: @js($placeholder ?? $searchPlaceholder),
        isDisabled: @js($disabled),
        searchable: {{ $searchable ? 'true' : 'false' }}
    })"
    x-on:click.outside="closeDropdown()"
    x-on:keydown.escape.window="closeDropdown()"
    {{ $attributes->merge(['class' => 'relative']) }}
    :class="open ? 'z-[9999]' : 'z-0'"
>
   <input
        x-ref="hiddenInput"
        type="hidden"
        name="{{ $name }}"
        :value="selected"
        @if($required) required @endif
    />

    @if($searchable)
        <div
            x-ref="trigger"
            class="w-full flex items-center border border-gray-300 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 rounded-md shadow-md bg-white px-3 py-[10px]"
            :class="{ 'opacity-50 cursor-not-allowed': isDisabled }"
        >
            <input
                type="text"
                @if($id) id="{{ $id }}" @endif
                x-model="search"
                x-on:focus="clearAndOpen()"
                x-on:click="clearAndOpen()"
                x-on:input="handleInput()"
                :placeholder="inputPlaceholderText"
                @disabled($disabled)
                class="w-full text-sm text-left text-gray-800 placeholder:text-gray-400 border-0 p-0 m-0 bg-transparent focus:ring-0"
            >

            <button
                type="button"
                x-on:click.stop="toggleDropdown()"
                @disabled($disabled)
                class="ml-2"
                tabindex="-1"
            >
                <svg class="h-4 w-4 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>
    @else
        <button
            x-ref="trigger"
            type="button"
            @if($id) id="{{ $id }}" @endif
            x-on:click.stop="toggleDropdown()"
            @disabled($disabled)
            class="w-full flex items-center justify-between border border-gray-300 rounded-md shadow-sm text-sm px-3 py-[10px] focus:border-indigo-500 focus:ring-indigo-500 bg-white text-left text-gray-800"
            :class="{ 'opacity-50 cursor-not-allowed': isDisabled }"
            style="min-height: 2.25rem;"
        >
            <span
                x-text="selected !== '' && selected !== null ? options[selected] ?? placeholderText : placeholderText"
                class="truncate"
                :class="selected !== '' && selected !== null ? 'text-gray-800' : 'text-gray-400'"
            ></span>

            <svg class="ml-2 h-4 w-4 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    @endif

    <template x-teleport="body">
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
            class="bg-white border border-gray-200 rounded-md shadow-lg max-h-56 overflow-y-auto"
            x-bind:style="dropdownStyle"
            style="display: none;"
        >
            @if($placeholder)
                <div
                    x-on:click="choose('')"
                    class="px-3 py-2 text-sm text-gray-400 hover:bg-gray-50 cursor-pointer"
                >
                    {{ $placeholder }}
                </div>
            @endif

            <template x-for="[key, label] in Object.entries(filteredOptions)" :key="String(key)">
                <div
                    x-on:click="choose(String(key))"
                    class="px-3 py-2 text-sm text-gray-800 hover:bg-gray-50 cursor-pointer"
                    :class="selected == String(key) ? 'bg-indigo-50 font-medium' : ''"
                    x-text="label"
                ></div>
            </template>
        </div>
    </template>
</div>
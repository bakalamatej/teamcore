@props(['id', 'name', 'value' => '', 'required' => false])

<textarea
    id="{{ $id }}"
    name="{{ $name }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-md']) }}
>{{ old($name, $value) }}</textarea>

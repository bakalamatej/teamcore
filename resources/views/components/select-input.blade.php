@props(['id', 'name', 'options' => [], 'selected' => null, 'required' => false, 'placeholder' => null])

<select
    id="{{ $id }}"
    name="{{ $name }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-md']) }}
>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif

    @foreach($options as $key => $label)
        <option value="{{ $key }}" {{ $key == $selected ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>

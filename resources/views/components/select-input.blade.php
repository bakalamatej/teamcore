@props(['id', 'name', 'options' => [], 'selected' => null, 'required' => false, 'placeholder' => null, 'disabled' => false])

<select
    id="{{ $id }}"
    name="{{ $name }}"
    @disabled($disabled)
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-md']) }}
>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif

    @foreach($options as $key => $label)
        <option value="{{ $key }}" @selected($key == $selected)>
            {{ $label }}
        </option>
    @endforeach
</select>
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm rounded-md shadow-md py-[10px] text-gray-800 placeholder:text-gray-400']) }}>

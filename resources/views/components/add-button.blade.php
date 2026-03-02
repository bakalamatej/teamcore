@props(['href' => '#'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 font-bold text-lg transition ease-in-out duration-150 shadow-md']) }}>
    +
</a>

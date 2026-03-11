@props(['href' => '#'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center bg-green-600 text-white rounded-md hover:bg-green-700 font-bold text-lg transition ease-in-out py-[20px] px-[15px] duration-150 shadow-md']) }}>
    +
</a>
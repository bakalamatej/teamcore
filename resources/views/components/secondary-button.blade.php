@props(['href' => null, 'type' => 'submit'])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge([
        'class' => 'inline-flex items-center px-4 py-2 border border-indigo-500 text-indigo-500 rounded-md shadow-xl font-semibold text-xs uppercase tracking-widest hover:bg-indigo-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap'
    ]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge([
        'class' => 'inline-flex items-center px-4 py-2 border border-indigo-500 text-indigo-500 rounded-md shadow-xl font-semibold text-xs uppercase tracking-widest hover:bg-indigo-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap'
    ]) }}>
        {{ $slot }}
    </button>
@endif

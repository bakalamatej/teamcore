<button 
    type="{{ $type ?? 'button' }}" 
    {{ $attributes->merge([
        'class' => 'inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md shadow-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition ease-in-out duration-150'
    ]) }}
>
    {{ $slot }}
</button>

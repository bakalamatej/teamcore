<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-start items-center pt-24 bg-neutral-300">
            <div class="w-[90%] sm-custom:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden rounded-lg">
                <div class="mb-6 justify-center flex items-baseline">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    <span class="text-3xl font-bold text-gray-800">eamcore</span>
                </div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

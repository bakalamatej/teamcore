<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/favicon2.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')

    </head>
    <body class="font-sans antialiased bg-neutral-300">
        <div class="w-[90%] lg:w-[70%] mx-auto ">
            @include('layouts.navigation-layout')

            <!-- Page Content -->
            <main class="pt-[128px] pb-12" >
                {{ $slot }}
            </main>
        </div>
    </body>
</html>

<x-app-layout>
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <div class="hidden xl-custom:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pb-12 pl-0 xl-custom:pl-[280px]">
            @yield('content')
        </main>

    </div>
</x-app-layout>

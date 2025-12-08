<x-app-layout>
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <div class="hidden xl:block">
            @include('panel.sidebar')
        </div>

        {{-- Content --}}
        <main class="flex-1 pb-12 pl-0 xl:pl-[280px]">
            {{ $slot }}
        </main>
    </div>
</x-app-layout>

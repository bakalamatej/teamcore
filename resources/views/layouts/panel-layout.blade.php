@props([])
<x-app-layout>
    <div class="flex min-h-[calc(100vh-11rem)]">
        <div class="hidden xl:block shrink-0 w-[15vw] max-w-[256px] min-w-[198px]">
            @include('panel.sidebar')
        </div>
        <div class="flex-1 min-w-0 pl-0 xl:pl-6">
            {{ $slot }}
        </div>
    </div>
</x-app-layout>
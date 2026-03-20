@props(['title' => 'Panel'])
<aside class="fixed w-64 bg-white h-[calc(100vh-11rem)] shadow-xl rounded-lg p-4 sm:p-8 flex flex-col">
    <div class="sidebar-heading">
        {{ $title }}
    </div>
    <nav class="flex flex-col mt-6 flex-1 overflow-y-auto">
        {{ $slot }}
    </nav>
</aside>
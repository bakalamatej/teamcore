@props(['title' => 'Panel'])
<aside class="fixed h-[calc(100vh-11rem)] bg-white shadow-xl rounded-lg p-4 sm:p-8 flex flex-col pb-4" style="width: inherit;">
    <div class="sidebar-heading">
        {{ $title }}
    </div>
    <nav class="flex flex-col mt-6 flex-1 overflow-y-auto">
        {{ $slot }}
    </nav>
</aside>
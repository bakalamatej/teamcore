<x-panel-layout>
    <h1 class="text-2xl font-bold mb-4">My Club(s)</h1>

    @forelse($clubs as $club)
        <div class="p-4 bg-white rounded-lg shadow mb-4">
            <h2 class="text-xl font-semibold">{{ $club->name }}</h2>
            <p>Email: {{ $club->email }}</p>
            <p>Phone: {{ $club->phone }}</p>
            <p>Web: <a href="{{ $club->webpage }}" class="text-blue-600">{{ $club->webpage }}</a></p>

            <h3 class="mt-2 font-semibold">Members:</h3>
            <ul class="list-disc list-inside">
                @foreach($club->activeMembers as $member)
                    <li>{{ $member->user->name ?? $member->name }}</li>
                @endforeach
            </ul>
        </div>
    @empty
        <p>You are not part of any club.</p>
    @endforelse
</x-panel-layout>
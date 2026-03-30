<x-app-layout>
<div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
    <h1 class="my-heading text-2xl mb-4">{{ __('Events calendar') }}</h1>

    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <form method="GET" action="{{ route('calendar.index') }}">
            <div class="flex justify-between items-end">
                <div class="flex gap-4">
                    <div>
                        <x-input-label :value="__('Year')" />
                        <x-select-input name="year" :options="$yearOptions" :selected="$year" :searchable="false" class="mt-1 block w-[160px] text-sm" />
                    </div>
                    <div>
                        <x-input-label :value="__('Month')" />
                        <x-select-input name="month" :options="$monthOptions" :selected="$month" :searchable="false" class="mt-1 block w-[160px] text-sm" />
                    </div>
                </div>
                <x-primary-button type="submit">{{ __('Show') }}</x-primary-button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-7 gap-1 sm:gap-2">
        @for($i = 1; $i < $firstDay; $i++)
            <div></div>
        @endfor

        @for($d = 1; $d <= $daysInMonth; $d++)
            @php
                $date = \Carbon\Carbon::create($year, $month, $d);
                $dayEvents = $events->where('start_date', '>=', $date->copy()->startOfDay())
                                    ->where('start_date', '<', $date->copy()->endOfDay());
                $isWeekend = in_array($date->dayOfWeekIso, [6, 7]);
            @endphp
            <a href="{{ route('calendar.day', [$year, $month, $d]) }}"
                class="aspect-square border rounded-lg p-1 sm:p-2 flex flex-col
                {{ $isWeekend ? 'bg-purple-200 hover:bg-blue-200' : 'bg-neutral-100 hover:bg-blue-100' }}
                {{ $date->isToday() ? 'ring-2 ring-red-500' : '' }}">
                <div class="text-xs font-bold">
                    {{ $d }}
                    <span class="hidden sm:inline">{{ $weekdays[$date->dayOfWeekIso - 1] }}</span>
                </div>
                @if($dayEvents->isNotEmpty())
                    <div class="flex flex-wrap gap-0.5 mt-1 sm:hidden">
                        @foreach($dayEvents as $event)
                            <span class="w-2 h-2 rounded-full bg-green-600 inline-block"></span>
                        @endforeach
                    </div>
                    <div class="hidden sm:block text-xs mt-1 space-y-0.5 overflow-hidden">
                        @foreach($dayEvents as $i => $event)
                            <div class="truncate {{ $i % 2 === 0 ? 'text-green-600' : 'text-blue-500' }}">
                                {{ $event->title }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </a>
        @endfor
    </div>
</div>
</x-app-layout>
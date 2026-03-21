@php
    use Carbon\Carbon;
    $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
    $firstDay = Carbon::create($year, $month, 1)->dayOfWeekIso;
    $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
@endphp

<x-app-layout>
    <div class="bg-white overflow-hidden shadow-xl rounded-lg p-4 sm:p-8">
        <h1 class="my-heading text-2xl mb-4">{{ __('Events calendar') }}</h1>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form method="GET" action="{{ route('calendar.index') }}">
                <div class="flex justify-between items-end">
                    <div class="flex gap-4">
                        <div>
                            <x-input-label :value="__('Year')" />
                            <x-select-input name="year" :options="collect(range(now()->year - 3, now()->year + 1))->mapWithKeys(fn($y) => [$y => $y])->toArray()" :selected="$year" :searchable="false" 
                                class="mt-1 block w-[160px] text-sm" />
                        </div>
                        <div>
                            <x-input-label :value="__('Month')" />
                            <x-select-input name="month" :options="collect(range(1, 12))->mapWithKeys(fn($m) => [$m => Carbon::create($year, $m, 1)->format('F')])->toArray()" :selected="$month" :searchable="false" 
                                class="mt-1 block w-[160px] text-sm" />
                        </div>
                    </div>
                    <x-primary-button type="submit">{{ __('Show') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-7 gap-2">
            @for($i = 1; $i < $firstDay; $i++)
                <div></div>
            @endfor
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $date = Carbon::create($year, $month, $d);
                    $dayEvents = $events->where('start_date', '>=', $date->startOfDay())
                        ->where('start_date', '<', $date->endOfDay());
                    $isWeekend = in_array($date->dayOfWeekIso, [6, 7]); // Saturday=6, Sunday=7
                @endphp
                <a href="{{ route('calendar.day', [$year, $month, $d]) }}" 
                    class="border rounded-lg p-2 h-[120px] flex flex-col justify-between 
                    {{ $isWeekend ? 'bg-purple-200 hover:bg-blue-200' : 'bg-neutral-100 hover:bg-blue-100' }} 
                    {{ $date->isToday() ? 'ring-2 ring-red-500' : '' }}">
                    <div>
                        <div class="text-xs font-bold">{{ $d }} {{ $weekdays[$date->dayOfWeekIso - 1] }}</div>
                    </div>
                    <div class="text-xs mt-2">
                        @foreach($dayEvents as $i => $event)
                            <div class="{{ $i % 2 === 0 ? 'text-green-600' : 'text-yellow-500' }}">
                                {{ $event->title }}
                            </div>
                        @endforeach
                    </div>
                </a>
            @endfor
        </div>
    </div>
</x-app-layout>

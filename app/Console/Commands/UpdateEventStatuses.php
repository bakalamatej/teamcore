<?php

namespace App\Console\Commands;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Console\Command;

class UpdateEventStatuses extends Command
{
    protected $signature = 'events:update-statuses';

    protected $description = 'Automatically update event statuses based on current time';

    public function handle(): int
    {
        $now = now();

        $scheduledToOngoing = Event::query()
            ->where('status', EventStatus::SCHEDULED->value)
            ->where('start_date', '<=', $now)
            ->update(['status' => EventStatus::ONGOING->value]);

        $ongoingToFinished = Event::query()
            ->where('status', EventStatus::ONGOING->value)
            ->where('end_date', '<', $now)
            ->update(['status' => EventStatus::FINISHED->value]);

        $this->info("Updated statuses: {$scheduledToOngoing} scheduled->ongoing, {$ongoingToFinished} ongoing->finished.");

        return self::SUCCESS;
    }
}
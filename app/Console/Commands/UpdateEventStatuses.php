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

        // SCHEDULED → ONGOING (events that have started but not finished)
        $scheduledToOngoing = Event::query()
            ->where('status', EventStatus::SCHEDULED->value)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>', $now)
            ->update(['status' => EventStatus::ONGOING->value]);

        // ONGOING → FINISHED (events that have ended)
        $ongoingToFinished = Event::query()
            ->where('status', EventStatus::ONGOING->value)
            ->where('end_date', '<=', $now)
            ->update(['status' => EventStatus::FINISHED->value]);

        // SCHEDULED → FINISHED (events that started and ended without being marked ongoing)
        $scheduledToFinished = Event::query()
            ->where('status', EventStatus::SCHEDULED->value)
            ->where('end_date', '<=', $now)
            ->update(['status' => EventStatus::FINISHED->value]);

        $totalUpdated = $scheduledToOngoing + $ongoingToFinished + $scheduledToFinished;

        $this->info("Updated statuses:");
        $this->info("  SCHEDULED → ONGOING: {$scheduledToOngoing}");
        $this->info("  ONGOING → FINISHED: {$ongoingToFinished}");
        $this->info("  SCHEDULED → FINISHED: {$scheduledToFinished}");
        $this->info("Total: {$totalUpdated} events updated");

        return self::SUCCESS;
    }
}
<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to all event attendees that the event starts soon.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
		$events = Event::with('attendee.user')
			->whereBetween('start_time', [now(), now()->addDay()])
			->get();
		
		$eventCount = $events->count();
		$eventLabel = Str::plural('event', $eventCount);
		
		$this->info("Found {$eventCount} and {$eventLabel}");
		
		$events->each(
			fn ($event) => $event->attendee->each(
				fn ($attendee) => $this->info("Notifying the user {$attendee->user->id}")
			)
		);
		
        $this->info('Reminder notifications sent successfully!');
    }
}

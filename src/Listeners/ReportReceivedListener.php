<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ReportReceived;
use Appleton\Threads\Notifications\ReportReceived as ReportReceivedNotification;
use Illuminate\Database\Eloquent\Model;

class ReportReceivedListener
{
    public function handle(ReportReceived $event): void
    {
        $moderatorEmails = config('threads.moderator_emails');

        /** @var Model $userModel */
        $userModel = config('threads.user_model');

        $users = $userModel::query()->whereIn('email', $moderatorEmails)->get();

        $users->each(function ($user) use ($event) {
            $user->notify(new ReportReceivedNotification($event->getReport()));
        });
    }
}

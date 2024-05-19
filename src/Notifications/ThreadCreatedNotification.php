<?php

declare(strict_types=1);

namespace Appleton\Threads\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ThreadCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        // Will be used for database
        return [];
    }

    public function toArray($notifiable): array
    {
        // Will be used for front end
        return [];
    }
}

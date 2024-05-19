<?php

declare(strict_types=1);

namespace Appleton\Threads\Notifications;

use Appleton\Threads\Models\BlockedCommenter;
use Appleton\Threads\Notifications\Concerns\HasConfig;
use Appleton\Threads\Notifications\Concerns\HasUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class CommenterBlocked extends Notification implements ShouldQueue
{
    use HasConfig;
    use HasUser;
    use Queueable;

    protected ?Model $user;

    public function __construct(private readonly BlockedCommenter $blockedCommenter)
    {
        $this->user = $this->blockedCommenter->blockerUser;
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     */
    public function toVonage(object $notifiable): VonageMessage
    {
        return (new VonageMessage)
            ->content($this->getMessage('sms', $this->getNameField()));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->getMessage('database', $this->getNameField()),
            'user_name' => $this->getUserName(),
            'avatar' => $this->getUserAvatar(),
        ];
    }

    /**
     * Get the push representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->getMessage('push', $this->getNameField()),
            'user_name' => $this->getUserName(),
            'avatar' => $this->getUserAvatar(),
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getEmailSubject())
            ->line($this->getMessage('email', $this->getNameField()));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->getMessage('database', $this->getNameField()),
            'user_name' => $this->getUserName(),
            'avatar' => $this->getUserAvatar(),
        ];
    }
}

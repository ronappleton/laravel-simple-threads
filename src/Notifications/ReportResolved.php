<?php

declare(strict_types=1);

namespace Appleton\Threads\Notifications;

use Appleton\Threads\Models\ThreadReport;
use Appleton\Threads\Notifications\Concerns\HasConfig;
use Appleton\Threads\Notifications\Concerns\HasUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class ReportResolved extends Notification implements ShouldQueue
{
    use HasConfig;
    use HasUser;
    use Queueable;

    private string $url;

    public function __construct(private readonly ThreadReport $report)
    {
        $threadId = $report->comment_id ? $report->comment->thread_id : $report->thread_id;

        $this->url = $this->getThreadShowUrl($threadId);
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     */
    public function toVonage(object $notifiable): VonageMessage
    {
        return (new VonageMessage)
            ->content(
                sprintf(
                    '%s - %s',
                    $this->getMessage('sms', $this->getNameField(), $this->getType()),
                    $this->url
                )
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->getMessage('database', $this->getNameField(), $this->getType()),
            'url' => $this->url,
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
            'message' => $this->getMessage('push', $this->getNameField(), $this->getType()),
            'url' => $this->url,
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
            ->line($this->getMessage('email', $this->getNameField(), $this->getType()))
            ->action('View Thread', $this->url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->getMessage('database', $this->getNameField(), $this->getType()),
            'url' => $this->url,
            'user_name' => $this->getUserName(),
            'avatar' => $this->getUserAvatar(),
        ];
    }

    public function getType(): string
    {
        return $this->report->comment_id ? 'comment' : 'thread';
    }
}

<?php

declare(strict_types=1);

namespace Appleton\Threads\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class CommentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $config;

    public function __construct()
    {
        /** @var array<string, array<string, mixed>> $config */
        $config = config()->array('threads.notifications.comment_created');

        $this->config = $config;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if ($this->config['sms']['enabled'] ?? false) {
            $channels[] = 'database';
        }

        if ($this->config['database']['enabled'] ?? false) {
            $channels[] = 'database';
        }

        if ($this->config['email']['enabled'] ?? false) {
            $channels[] = 'mail';
        }

        if ($this->config['push']['enabled'] ?? false) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     */
    public function toVonage(object $notifiable): VonageMessage
    {
        /** @var string $message */
        $message = $this->config['sms']['message'] ?? '';

        return (new VonageMessage)->content($message);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        /** @var string $message */
        $message = $this->config['database']['message'] ?? '';

        return [
            'message' => $message,
        ];
    }

    /**
     * Get the push representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        /** @var string $message */
        $message = $this->config['push']['message'] ?? '';

        return new BroadcastMessage([
            'message' => $message,
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        /** @var string $subject */
        $subject = $this->config['email']['subject'] ?? '';

        /** @var string $message */
        $message = $this->config['email']['message'] ?? '';

        return (new MailMessage)->subject($subject)->line($message)->action('View Thread', url('/'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        /** @var string $message */
        $message = $this->config['database']['message'] ?? '';

        return [
            'message' => $message,
        ];
    }
}

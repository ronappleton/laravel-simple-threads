<?php

declare(strict_types=1);

namespace Appleton\Threads\Notifications\Concerns;

use Illuminate\Support\Str;

trait HasConfig
{
    public function getSnakeName(): string
    {
        return Str::snake(static::class);
    }

    public function smsEnabled(): bool
    {
        return config()->bool("threads.notifications.{$this->getSnakeName()}.sms_enabled", false);
    }

    public function databaseEnabled(): bool
    {
        return config()->bool("threads.notifications.{$this->getSnakeName()}.database_enabled", false);
    }

    public function emailEnabled(): bool
    {
        return config()->bool("threads.notifications.{$this->getSnakeName()}.email_enabled", false);
    }

    public function pushEnabled(): bool
    {
        return config()->bool("threads.notifications.{$this->getSnakeName()}.push_enabled", false);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if ($this->smsEnabled()) {
            $channels[] = 'database';
        }

        if ($this->databaseEnabled()) {
            $channels[] = 'database';
        }

        if ($this->emailEnabled()) {
            $channels[] = 'mail';
        }

        if ($this->pushEnabled()) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    public function getEmailSubject(): string
    {
        return config()
            ->string("threads.notifications.{$this->getSnakeName()}.email_subject", 'System Notification');
    }

    public function getNameField(): ?string
    {
        return config()
            ->string("threads.notifications.{$this->getSnakeName()}.name_field");
    }
}

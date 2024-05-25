<?php

declare(strict_types=1);

namespace Appleton\Threads\Notifications\Concerns;

use Illuminate\Support\Str;

trait HasConfig
{
    use HasClass;

    public function smsEnabled(): bool
    {
        return config()->boolean("threads.notifications.{$this->getSnakeName(static::class)}.sms_enabled", false);
    }

    public function databaseEnabled(): bool
    {
        return config()->boolean("threads.notifications.{$this->getSnakeName(static::class)}.database_enabled", false);
    }

    public function emailEnabled(): bool
    {
        return config()->boolean("threads.notifications.{$this->getSnakeName(static::class)}.email_enabled", false);
    }

    public function pushEnabled(): bool
    {
        return config()->boolean("threads.notifications.{$this->getSnakeName(static::class)}.push_enabled", false);
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
            $channels[] = 'sms';
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
            ->string("threads.notifications.{$this->getSnakeName(static::class)}.email_subject", 'System Notification');
    }

    public function getNameField(): ?string
    {
        return config()
            ->string("threads.notifications.{$this->getSnakeName(static::class)}.name_field");
    }

    public function getThreadShowUrl(string $threadId): string
    {
        return sprintf('%s?thread=%s', config()->string('threads.thread_show_url'), $threadId);
    }

    public function getReportUrl(string $reportId): string
    {
        return sprintf('%s?report=%s', config()->string('threads.report_url'), $reportId);
    }
}

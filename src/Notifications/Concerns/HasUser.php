<?php

declare(strict_types=1);

namespace Appleton\Threads\Notifications\Concerns;

trait HasUser
{
    use HasClass;

    public function getUserName(): string
    {
        return $this->user->{$this->getUserNameField()} ?? 'System Robot';
    }

    public function getUserAvatar(): string
    {
        return $this->user->{$this->getUserAvatarField()} ?? 'System Robot';
    }

    public function getMessage(string $channel, ?string $nameField = null, ?string $replaceOverride = null): string
    {
        $message = config()->string(sprintf('threads.notifications.%s.%s.message', $this->getSnakeName(static::class), $channel));

        if (is_null($nameField)) {
            return $message;
        }

        if (! is_null($replaceOverride)) {
            return str_replace(":$nameField", $replaceOverride, $message);
        }

        if (is_null($this->user)) {
            return str_replace(":$nameField", 'System Robot', $message);
        }

        return str_replace(":$nameField", $this->getUserName(), $message);
    }

    public function getUserNameField(): string
    {
        return config()->string('threads.user_name_field', 'display_name');
    }

    public function getUserAvatarField(): string
    {
        return config()->string('threads.user_avatar_field', 'avatar');
    }
}

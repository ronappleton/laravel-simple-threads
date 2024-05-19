<?php

declare(strict_types=1);

namespace Appleton\Threads\Notifications\Concerns;

trait HasUser
{
    public function getUserName(): string
    {
        if (is_null($this->user)) {
            return 'System Robot';
        }

        if (property_exists($this->user, $this->getUserNameField())) {
            $field = $this->getUserNameField();

            return $this->user->$field;
        }

        return 'System Robot';
    }

    public function getUserAvatar(): string
    {
        if (is_null($this->user)) {
            return 'System Robot';
        }

        if (property_exists($this->user, $this->getUserAvatarField())) {
            $field = $this->getUserAvatarField();

            return $this->user->$field;
        }

        return 'System Robot';
    }

    public function getMessage(string $channel, ?string $nameField = null, ?string $replaceOverride = null): string
    {
        $message = config()->string("threads.notifications.$channel.message", '');

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

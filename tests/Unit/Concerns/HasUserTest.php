<?php

declare(strict_types=1);

namespace Tests\Unit\Concerns;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Notifications\Concerns\HasUser;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(HasUser::class)]
class HasUserTest extends TestCase
{
    use HasUser;

    protected ?UserUuid $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new UserUuid();
        $this->user->setAttribute('display_name', 'Test User');
        $this->user->setAttribute('avatar', 'Test Avatar');
    }

    public function testUserNameReturnsSystemRobotWhenUserIsNull(): void
    {
        $this->user = null;
        $this->assertEquals('System Robot', $this->getUserName());
    }

    public function testUserNameReturnsUserNameWhenUserIsNotNull(): void
    {
        $this->assertEquals('Test User', $this->getUserName());
    }

    public function testUserAvatarReturnsSystemRobotWhenUserIsNull(): void
    {
        $this->user = null;
        $this->assertEquals('System Robot', $this->getUserAvatar());
    }

    public function testUserAvatarReturnsUserAvatarWhenUserIsNotNull(): void
    {
        $this->assertEquals('Test Avatar', $this->getUserAvatar());
    }

    public function testMessageReturnsConfigMessageWhenNameFieldIsNull(): void
    {
        Config::set('threads.notifications.has_user_test.sms.message', 'Test Message');

        $this->assertEquals('Test Message', $this->getMessage('sms'));
    }

    public function testMessageReturnsReplacedMessageWhenReplaceOverrideIsNotNull(): void
    {
        Config::set('threads.notifications.has_user_test.sms.message', 'Hello :name');

        $this->assertEquals('Hello Test', $this->getMessage('sms', 'name', 'Test'));
    }

    public function testMessageReturnsReplacedMessageWithUserNameWhenUserIsNotNull(): void
    {
        Config::set('threads.notifications.has_user_test.sms.message', 'Hello :name');

        $this->assertEquals('Hello Test User', $this->getMessage('sms', 'name'));
    }

    public function testMessageReturnsReplacedMessageWithSystemRobotWhenUserIsNull(): void
    {
        Config::set('threads.notifications.has_user_test.sms.message', 'Hello :name');

        $this->user = null;

        $this->assertEquals('Hello System Robot', $this->getMessage('sms', 'name'));
    }
}
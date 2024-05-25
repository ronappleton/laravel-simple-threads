<?php

declare(strict_types=1);

namespace Tests\Unit\Concerns;

use Appleton\Threads\Notifications\Concerns\HasConfig;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class HasConfigTest extends TestCase
{
    use HasConfig;

    public function testSnakeNameReturnsCorrectValue(): void
    {
        $this->assertEquals('has_config_test', $this->getSnakeName(static::class));
    }

    public function testSmsEnabledReturnsConfigValue(): void
    {
        Config::set('threads.notifications.has_config_test.sms_enabled', true);
        $this->assertTrue($this->smsEnabled());
    }

    public function testDatabaseEnabledReturnsConfigValue(): void
    {
        Config::set('threads.notifications.has_config_test.database_enabled', true);
        $this->assertTrue($this->databaseEnabled());
    }

    public function testEmailEnabledReturnsConfigValue(): void
    {
        Config::set('threads.notifications.has_config_test.email_enabled', true);
        $this->assertTrue($this->emailEnabled());
    }

    public function testPushEnabledReturnsConfigValue(): void
    {
        Config::set('threads.notifications.has_config_test.push_enabled', true);
        $this->assertTrue($this->pushEnabled());
    }

    public function testViaReturnsCorrectChannels(): void
    {
        Config::set('threads.notifications.has_config_test.sms_enabled', true);
        Config::set('threads.notifications.has_config_test.database_enabled', true);
        Config::set('threads.notifications.has_config_test.email_enabled', true);
        Config::set('threads.notifications.has_config_test.push_enabled', true);

        $this->assertEquals(['sms', 'database', 'mail', 'broadcast'], $this->via(new \stdClass()));
    }

    public function testEmailSubjectReturnsConfigValue(): void
    {
        Config::set('threads.notifications.has_config_test.email_subject', 'Test Subject');
        $this->assertEquals('Test Subject', $this->getEmailSubject());
    }

    public function testNameFieldReturnsConfigValue(): void
    {
        Config::set('threads.notifications.has_config_test.name_field', 'Test Name Field');
        $this->assertEquals('Test Name Field', $this->getNameField());
    }

    public function testThreadShowUrlReturnsCorrectValue(): void
    {
        Config::set('threads.thread_show_url', 'http://example.com/thread');
        $this->assertEquals('http://example.com/thread?thread=test', $this->getThreadShowUrl('test'));
    }
}
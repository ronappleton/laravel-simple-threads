<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndex(): void
    {
        $response = $this->json('get', route('threads.index'));

        $response->assertStatus(200);
    }
}
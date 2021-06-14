<?php

namespace CodeOfDigital\LaravelUrlShortener\Tests\Unit;

use PHPUnit\Framework\TestCase as PHPTestCase;

abstract class TestCase extends PHPTestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = new MockClient();
    }

    protected function tearDown(): void
    {
        if ($this->client->hasQueuedMessages())
            $this->fail(sprintf('HTTP Client contains %d unused message(s)', $this->client->getQueueSize()));
    }
}

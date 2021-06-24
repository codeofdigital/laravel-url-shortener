<?php

namespace CodeOfDigital\LaravelUrlShortener\Tests\Unit;

use CodeOfDigital\LaravelUrlShortener\Drivers\OuoIoDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidApiTokenException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;

class OuoIoShortenerTest extends TestCase
{
    protected $shortener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shortener = new OuoIoDriverShortener($this->client, 'API_TOKEN');
    }

    /**
     * Test the URL Shortening through Ouo.io
     *
     * @throws ShortUrlException
     */
    public function testShortening()
    {
        $this->client->queue(require __DIR__ . '/../Responses/ouo_io/http-200.php');

        $shortUrl = $this->shortener->shorten('https://laravel.com');
        $request = $this->client->getRequest(0);

        $this->assertNotNull($request);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('ouo.io', $request->getUri()->getHost());
        $this->assertEquals('/api/API_TOKEN?s=https%253A%252F%252Flaravel.com', $request->getRequestTarget());

        $this->assertEquals('https://ouo.io/5FhZHP', $shortUrl);
    }

    /**
     * Test failure if there is invalid syntax in request
     *
     * @throws ShortUrlException
     */
    public function testUnauthorized()
    {
        $this->client->queue(require __DIR__ . '/../Responses/ouo_io/http-302.php');
        $this->expectException(InvalidApiTokenException::class);
        $this->shortener->shorten('https://laravel.com');
    }

    /**
     * Test failure if parsed URL is invalid or incorrect format
     *
     * @throws ShortUrlException
     */
    public function testInvalidUrl()
    {
        $this->expectException(ShortUrlException::class);
        $this->shortener->shorten('some-string.com');
    }
}

<?php

namespace CodeOfDigital\LaravelUrlShortener\Tests\Unit;

use CodeOfDigital\LaravelUrlShortener\Drivers\HideUriDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Exceptions\BadRequestException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;

class HideUriShortenerTest extends TestCase
{
    protected $shortener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shortener = new HideUriDriverShortener($this->client);
    }

    /**
     * Test the URL Shortening through Hide.uri
     *
     * @throws ShortUrlException
     */
    public function testUrlShortening()
    {
        $this->client->queue(require __DIR__ . '/../Responses/hide_uri/http-200.php');

        $shortUrl = $this->shortener->shorten('https://laravel.com');
        $request = $this->client->getRequest(0);

        $this->assertNotNull($request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('hideuri.com', $request->getUri()->getHost());
        $this->assertEquals('/api/v1/shorten', $request->getRequestTarget());

        $this->assertEquals('https://hideuri.com/Oky9AE', $shortUrl);
    }

    /**
     * Test failure if there is invalid syntax in request
     *
     * @throws ShortUrlException
     */
    public function testFailure()
    {
        $this->client->queue(require __DIR__ . '/../Responses/hide_uri/http-400.php');
        $this->expectException(BadRequestException::class);
        $this->shortener->shorten('https://laravel,com');
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

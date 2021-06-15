<?php

namespace CodeOfDigital\LaravelUrlShortener\Tests\Unit;

use CodeOfDigital\LaravelUrlShortener\Drivers\TinyUrlDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidApiTokenException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;

class TinyUrlShortenerTest extends TestCase
{
    protected $shortener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shortener = new TinyUrlDriverShortener($this->client, 'API_TOKEN', false);
    }

    /**
     * Test the URL Shortening through Tinyurl
     *
     * @throws ShortUrlException
     */
    public function testUrlShortening()
    {
        $this->client->queue(require __DIR__ . '/../Responses/tiny_url/http-200.php');

        $shortUrl = $this->shortener->shorten('https://laravel.com');
        $request = $this->client->getRequest(0);

        $this->assertNotNull($request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('api.tinyurl.com', $request->getUri()->getHost());
        $this->assertEquals('/create', $request->getRequestTarget());
        $this->assertEquals('Bearer API_TOKEN', $request->getHeader('Authorization')[0]);
        $this->assertEquals('application/json', $request->getHeader('Content-Type')[0]);

        $this->assertEquals('https://tinyurl.com/3ffwsk3p', $shortUrl);
    }

    /**
     * Test failure to authenticate with Tinyurl
     *
     * @throws ShortUrlException
     */
    public function testUnauthorized()
    {
        $this->client->queue(require __DIR__ . '/../Responses/tiny_url/http-401.php');
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

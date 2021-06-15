<?php

namespace CodeOfDigital\LaravelUrlShortener\Tests\Unit;

use CodeOfDigital\LaravelUrlShortener\Drivers\ShorteStDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidApiTokenException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;

class ShorteStShortenerTest extends TestCase
{
    protected $shortener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shortener = new ShorteStDriverShortener($this->client, 'API_TOKEN');
    }

    /**
     * Test the URL Shortening through Shorte.st
     *
     * @throws ShortUrlException
     */
    public function testShortening()
    {
        $this->client->queue(require __DIR__ . '/../Responses/shorte_st/http-200.php');

        $shortUrl = $this->shortener->shorten('https://laravel.com');
        $request = $this->client->getRequest(0);

        $this->assertNotNull($request);
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('api.shorte.st', $request->getUri()->getHost());
        $this->assertEquals('/v1/data/url', $request->getRequestTarget());
        $this->assertEquals('API_TOKEN', $request->getHeader('Public-API-Token')[0]);
        $this->assertEquals('application/json', $request->getHeader('Content-Type')[0]);

        $this->assertEquals('http://gestyy.com/ei4Z9l', $shortUrl);
    }

    /**
     * Test failure to authenticate with Shorte.st
     *
     * @throws ShortUrlException
     */
    public function testUnauthorized()
    {
        $this->client->queue(require __DIR__ . '/../Responses/shorte_st/http-302.php');
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

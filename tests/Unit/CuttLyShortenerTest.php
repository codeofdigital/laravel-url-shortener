<?php

namespace CodeOfDigital\LaravelUrlShortener\Tests\Unit;

use CodeOfDigital\LaravelUrlShortener\Drivers\CuttLyDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidApiTokenException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;

class CuttLyShortenerTest extends TestCase
{
    protected $shortener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shortener = new CuttLyDriverShortener($this->client, 'API_TOKEN');
    }

    /**
     * Test the URL Shortening through Cutt.ly
     *
     * @throws ShortUrlException
     */
    public function testUrlShortening()
    {
        $this->client->queue(require __DIR__ . '/../Responses/cutt_ly/http-200.php');

        $shortUrl = $this->shortener->shorten('https://laravel.com');
        $request = $this->client->getRequest(0);

        $this->assertNotNull($request);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('cutt.ly', $request->getUri()->getHost());
        $this->assertEquals('/api/api.php?key=API_TOKEN&short=https%3A%2F%2Flaravel.com', $request->getRequestTarget());

        $this->assertEquals('https://cutt.ly/lnHBR1m', $shortUrl);
    }

    /**
     * Test failure to authenticate with Cutt.ly
     *
     * @throws ShortUrlException
     */
    public function testUnauthorized()
    {
        $this->client->queue(require __DIR__ . '/../Responses/cutt_ly/http-401.php');
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

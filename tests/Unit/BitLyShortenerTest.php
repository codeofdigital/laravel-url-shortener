<?php

namespace CodeOfDigital\LaravelUrlShortener\Tests\Unit;

use CodeOfDigital\LaravelUrlShortener\Drivers\BitLyDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidApiTokenException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;

class BitLyShortenerTest extends TestCase
{
    protected $shortener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shortener = new BitLyDriverShortener($this->client, 'API_TOKEN', false);
    }

    /**
     * Test the URL Shortening through Bit.ly
     *
     * @throws \CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException
     */
    public function testUrlShortening()
    {
        $this->client->queue(require __DIR__ . '/../Responses/bit_ly/http-200.php');

        $shortUrl = $this->shortener->shorten('https://laravel.com');
        $request = $this->client->getRequest(0);

        $this->assertNotNull($request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('api-ssl.bitly.com', $request->getUri()->getHost());
        $this->assertEquals('/v4/shorten', $request->getRequestTarget());
        $this->assertEquals('Bearer API_TOKEN', $request->getHeader('Authorization')[0]);
        $this->assertEquals('application/json', $request->getHeader('Content-Type')[0]);

        $this->assertEquals('https://bit.ly/3iSAOvF', $shortUrl);
    }

    /**
     * Test failure to authenticate with Bit.ly
     *
     * @throws \CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException
     */
    public function testUnauthorized()
    {
        $this->client->queue(require __DIR__ . '/../Responses/bit_ly/http-403.php');
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

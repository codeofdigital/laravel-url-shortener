<?php

namespace CodeOfDigital\LaravelUrlShortener\Tests\Unit;

use CodeOfDigital\LaravelUrlShortener\Drivers\IsGdDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Exceptions\BadRequestException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;

class IsGdShortenerTest extends TestCase
{
    protected $shortener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shortener = new IsGdDriverShortener($this->client, false);
    }

    /**
     * Test the URL Shortening through Is.gd
     *
     * @throws ShortUrlException
     */
    public function testShortening()
    {
        $this->client->queue(require __DIR__ . '/../Responses/is_gd/http-200.php');

        $shortUrl = $this->shortener->shorten('https://laravel.com');
        $request = $this->client->getRequest(0);

        $this->assertNotNull($request);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('is.gd', $request->getUri()->getHost());
        $this->assertEquals('/create.php?format=simple&logstats=0&url=https%3A%2F%2Flaravel.com', $request->getRequestTarget());

        $this->assertEquals('https://is.gd/Qn8dwg', $shortUrl);
    }

    /**
     * Test failure if there is invalid syntax in request
     *
     * @throws ShortUrlException
     */
    public function testFailure()
    {
        $this->client->queue(require __DIR__ . '/../Responses/is_gd/http-400.php');
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

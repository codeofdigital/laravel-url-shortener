<?php

namespace CodeOfDigital\LaravelUrlShortener\Tests\Unit;

use CodeOfDigital\LaravelUrlShortener\Drivers\FirebaseDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Exceptions\BadRequestException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidApiTokenException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;

class FirebaseShortenerTest extends TestCase
{
    protected $shortener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shortener = new FirebaseDriverShortener($this->client, 'API_TOKEN', 'URI_PREFIX', 'UNGUESSABLE');
    }

    /**
     * Test the shortening of URLs through Firebase
     *
     * @throws ShortUrlException
     */
    public function testUrlShortening()
    {
        $this->client->queue(require __DIR__ . '/../Responses/firebase/http-200.php');

        $shortUrl = $this->shortener->shorten('https://laravel.com');
        $request = $this->client->getRequest(0);

        $this->assertNotNull($request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('firebasedynamiclinks.googleapis.com', $request->getUri()->getHost());
        $this->assertEquals('/v1/shortLinks?key=API_TOKEN', $request->getRequestTarget());
        $this->assertEquals('application/json', $request->getHeader('Content-Type')[0]);
        $this->assertEquals('application/json', $request->getHeader('Accept')[0]);

        $expected = '{"dynamicLinkInfo":{"domainUriPrefix":"URI_PREFIX","link":"https:\/\/laravel.com"},"suffix":{"option":"UNGUESSABLE"}}';
        $this->assertJsonStringEqualsJsonString($expected, $request->getBody()->getContents());

        $this->assertEquals('https://codeofdigital.page.link/CBNYHKkLc6FZmuYU9', $shortUrl);
    }

    /**
     * Test failure to authenticate with Firebase
     *
     * @throws ShortUrlException
     */
    public function testUnauthorized()
    {
        $this->client->queue(require __DIR__ . '/../Responses/firebase/http-400.php');
        $this->expectException(BadRequestException::class);
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

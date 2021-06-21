<?php

namespace CodeOfDigital\LaravelUrlShortener\Drivers;

use CodeOfDigital\LaravelUrlShortener\Exceptions\BadRequestException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidResponseException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\TooManyRequestException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class HideUriDriverShortener extends DriverShortener
{
    protected $client;
    protected $object;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->object = [
            'allow_redirects' => false,
            'base_uri' => 'https://hideuri.com',
        ];
    }

    /**
     * @inheritDoc
     */
    public function shortenAsync($url, array $options = [])
    {
        if (!Str::startsWith($url, ['http://', 'https://']))
            throw new ShortUrlException('The given URL must begin with http/https');

        $options = array_merge_recursive(Arr::add($this->object, 'json.url', $url), $options);
        $request = new Request('POST', '/api/v1/shorten');

        return $this->client->sendAsync($request, $options)->then(
            function (ResponseInterface $response) {
                return json_decode($response->getBody()->getContents())->result_url;
            },
            function (RequestException $e) {
                $this->getErrorMessage($e->getCode(), json_decode($e->getResponse()->getBody()->getContents())->error);
            }
        );
    }

    protected function getErrorMessage($code, $message = null)
    {
        switch ($code) {
            case 400: throw new BadRequestException($message);
            case 429: throw new TooManyRequestException($message);
            default: throw new InvalidResponseException($message);
        }
    }
}

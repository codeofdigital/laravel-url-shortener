<?php

namespace CodeOfDigital\LaravelUrlShortener\Drivers;

use CodeOfDigital\LaravelUrlShortener\Exceptions\BadRequestException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidApiTokenException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidResponseException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class OuoIoDriverShortener extends DriverShortener
{
    protected $client;
    protected $object;
    protected $token;

    public function __construct(ClientInterface $client, $token)
    {
        $this->client = $client;
        $this->token = $token;
        $this->object = [
            'allow_redirects' => false,
            'base_uri' => 'https://ouo.io',
        ];
    }

    /**
     * @inheritDoc
     */
    public function shortenAsync($url, array $options = [])
    {
        if (!Str::startsWith($url, ['http://', 'https://']))
            throw new ShortUrlException('The given URL must begin with http/https');

        $options = array_merge_recursive(Arr::add($this->object, 'query.s', urlencode($url)), ['query' => $options]);
        $request = new Request('GET', "/api/{$this->token}");

        return $this->client->sendAsync($request, $options)->then(
            function (ResponseInterface $response) {
                if ($response->getStatusCode() == 302)
                    $this->getErrorMessage($response->getStatusCode(), "Your API Token is invalid. Please try a new API Token.");

                return $response->getBody()->getContents();
            },
            function (RequestException $e) {
                $this->getErrorMessage($e->getCode(), $e->getResponse()->getBody()->getContents());
            }
        );
    }

    protected function getErrorMessage($code, $message = null)
    {
        switch ($code) {
            case 302: throw new InvalidApiTokenException($message);
            case 400: throw new BadRequestException($message);
            default: throw new InvalidResponseException($message);
        }
    }
}

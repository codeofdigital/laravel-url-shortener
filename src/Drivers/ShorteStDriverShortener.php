<?php

namespace CodeOfDigital\LaravelUrlShortener\Drivers;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;

class ShorteStDriverShortener extends DriverShortener
{
    protected $client;
    protected $object;

    public function __construct(ClientInterface $client, $token)
    {
        $this->client = $client;
        $this->object = [
            'allow_redirects' => false,
            'verify' => false,
            'base_uri' => 'https://api.shorte.st',
            'headers' => [
                'Accept' => 'application/json',
                'Public-API-Token' => $token,
                'Content-Type' => 'application/json'
            ],
            'json' => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function shortenAsync($url, array $options = [])
    {
        $options = array_merge_recursive(Arr::add($this->object, 'json.urlToShorten', $url), ['json' => $options]);
        $request = new Request('PUT', '/v1/data/url');

        return $this->client->sendAsync($request, $options)->then(function (ResponseInterface $response) {
            return json_decode($response->getBody()->getContents())->shortenedUrl;
        });
    }
}

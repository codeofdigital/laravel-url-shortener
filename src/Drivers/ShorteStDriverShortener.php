<?php

namespace CodeOfDigital\LaravelUrlShortener\Drivers;

use CodeOfDigital\LaravelUrlShortener\Exceptions\BadRequestException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidApiTokenException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidResponseException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
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

        return $this->client->sendAsync($request, $options)->then(
            function (ResponseInterface $response) {
                if ($response->getStatusCode() == 302)
                    $this->getErrorMessage($response->getStatusCode(), "Your API Token is invalid. Please try a new API Token.");

                return json_decode($response->getBody()->getContents())->shortenedUrl;
            },
            function (RequestException $e) {
                $this->getErrorMessage($e->getCode(), $e->getMessage());
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

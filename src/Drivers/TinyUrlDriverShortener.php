<?php

namespace CodeOfDigital\LaravelUrlShortener\Drivers;

use CodeOfDigital\LaravelUrlShortener\Exceptions\BadRequestException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidApiTokenException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidDataException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidResponseException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\MethodNotAllowedException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class TinyUrlDriverShortener extends DriverShortener
{
    protected $client;
    protected $object;

    public function __construct(ClientInterface $client, $token, $domain)
    {
        $this->client = $client;
        $this->object = [
            'allow_redirects' => false,
            'base_uri' => 'https://api.tinyurl.com',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'domain' => $domain
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function shortenAsync($url, array $options = [])
    {
        if (!Str::startsWith($url, ['http://', 'https://']))
            throw new ShortUrlException('The given URL must begin with http/https');

        $options = array_merge_recursive(Arr::add($this->object, 'json.url', $url), ['json' => $options]);
        $request = new Request('POST', '/create');

        return $this->client->sendAsync($request, $options)->then(
            function (ResponseInterface $response) {
                return str_replace('http://', 'https://', json_decode($response->getBody()->getContents())->data->tiny_url);
            },
            function (RequestException $e) {
                $contents = json_decode($e->getResponse()->getBody()->getContents());
                $errorMessage = implode(' ', $contents->errors);
                $this->getErrorMessage($e->getCode(), $errorMessage);
            }
        );
    }

    protected function getErrorMessage($code, $message = null)
    {
        switch ($code) {
            case 400: throw new BadRequestException($message);
            case 401: throw new InvalidApiTokenException($message);
            case 405: throw new MethodNotAllowedException($message);
            case 422: throw new InvalidDataException($message);
            default: throw new InvalidResponseException($message);
        }
    }
}

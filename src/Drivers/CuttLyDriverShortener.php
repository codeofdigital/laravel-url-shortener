<?php

namespace CodeOfDigital\LaravelUrlShortener\Drivers;

use CodeOfDigital\LaravelUrlShortener\Exceptions\BadRequestException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidApiTokenException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidDataException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidResponseException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class CuttLyDriverShortener extends DriverShortener
{
    protected $client;
    protected $object;

    public function __construct(ClientInterface $client, $token)
    {
        $this->client = $client;
        $this->object = [
            'allow_redirects' => false,
            'base_uri' => 'https://cutt.ly',
            'query' => [
                'key' => $token
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function shortenAsync($url, array $options = [])
    {
        if (!Str::startsWith($url, ['http://', 'https://']))
            throw new ShortUrlException('The given URL must begin with http:// or https://');

        $options = array_merge_recursive(Arr::add($this->object, 'query.short', urlencode($url)), ['query' => $options]);
        $request = new Request('GET', '/api/api.php');

        return $this->client->sendAsync($request, $options)->then(
            function (ResponseInterface $response) {
                $statusCode = json_decode($response->getBody()->getContents())->url->status;
                if ($statusCode != 7) $this->getErrorMessage($statusCode);
                return str_replace('http://', 'https://', json_decode($response->getBody()->getContents())->url->shortLink);
            },
            function (RequestException $e) {
                $this->getErrorMessage($e->getCode(), $e->getMessage());
            }
        );
    }

    protected function getErrorMessage($code, $message = null)
    {
        switch ($code) {
            case 1:
                throw new BadRequestException("The link has already been shortened.");
            case 2:
                throw new InvalidDataException("The entered link is not a link.");
            case 3:
                throw new InvalidDataException("The preferred link name is already taken.");
            case 4:
            case 401:
                throw new InvalidApiTokenException("Your API Key is invalid and incorrect.");
            case 5:
                throw new InvalidDataException("The link has not passed validated. There is invalid characters.");
            case 6:
                throw new InvalidDataException("The link provided is from a blocked domain.");
            default:
                throw new InvalidResponseException($message);
        }
    }
}

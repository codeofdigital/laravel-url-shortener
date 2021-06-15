<?php

namespace CodeOfDigital\LaravelUrlShortener\Drivers;

use CodeOfDigital\LaravelUrlShortener\Exceptions\BadRequestException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidDataException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\InvalidResponseException;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class IsGdDriverShortener extends DriverShortener
{
    protected $client;
    protected $object;

    public function __construct(ClientInterface $client, $statistic)
    {
        $this->client = $client;
        $this->object = [
            'allow_redirects' => false,
            'base_uri' => 'https://is.gd',
            'query' => [
                'format' => 'simple',
                'logstats' => intval($statistic)
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

        $options = array_merge_recursive(Arr::add($this->object, 'query.url', $url), ['query' => $options]);
        $request = new Request('GET', '/create.php');

        return $this->client->sendAsync($request, $options)->then(
            function (ResponseInterface $response) {
                return str_replace('http://', 'https://', $response->getBody()->getContents());
            },
            function (RequestException $e) {
                $this->getErrorMessage($e->getCode(), $e->getMessage());
            }
        );
    }

    protected function getErrorMessage($code, $message = null)
    {
        switch ($code) {
            case 400: throw new BadRequestException($message);
            case 406: throw new InvalidDataException($message);
            default: throw new InvalidResponseException($message);
        }
    }
}

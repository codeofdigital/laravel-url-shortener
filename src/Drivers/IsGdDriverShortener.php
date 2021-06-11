<?php

namespace CodeOfDigital\LaravelUrlShortener\Drivers;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
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
                'format' => 'json',
                'logstats' => intval($statistic)
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function shortenAsync($url, array $options = [])
    {
        $options = array_merge_recursive(Arr::add($this->object, 'query.url', $url), ['query' => $options]);
        $request = new Request('GET', '/create.php');

        return $this->client->sendAsync($request, $options)->then(function (ResponseInterface $response) {
            return str_replace('http://', 'https://', json_decode($response->getBody()->getContents())->shorturl);
        });
    }
}

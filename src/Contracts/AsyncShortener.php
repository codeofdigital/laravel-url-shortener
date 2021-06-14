<?php

namespace CodeOfDigital\LaravelUrlShortener\Contracts;

use GuzzleHttp\Promise\PromiseInterface;

interface AsyncShortener extends Shortener
{
    /**
     * Shorten the given URL asynchronously
     *
     * @param $url
     * @param array $options
     * @return PromiseInterface
     */
    public function shortenAsync($url, array $options = []);
}

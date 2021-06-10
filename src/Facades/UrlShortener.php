<?php

namespace CodeOfDigital\LaravelUrlShortener\Facades;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string shorten($url, array $options = [])
 * @method static PromiseInterface shortenAsync($url, array $options = [])
 *
 * @see \CodeOfDigital\LaravelUrlShortener\UrlShortener
 */
class UrlShortener extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'url.shortener';
    }
}

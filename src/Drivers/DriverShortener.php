<?php

namespace CodeOfDigital\LaravelUrlShortener\Drivers;

use CodeOfDigital\LaravelUrlShortener\Contracts\AsyncShortener;
use CodeOfDigital\LaravelUrlShortener\Exceptions\ShortUrlException;
use Illuminate\Support\Str;

abstract class DriverShortener implements AsyncShortener
{
    abstract protected function getErrorMessage($code, $message = null);

    public function shorten($url, array $options = [])
    {
        if (!Str::startsWith($url, ['http://', 'https://']))
            throw new ShortUrlException('The given URL must begin with http:// or https://');

        return $this->shortenAsync($url, $options)->wait();
    }
}

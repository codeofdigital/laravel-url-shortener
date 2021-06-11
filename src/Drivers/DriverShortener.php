<?php

namespace CodeOfDigital\LaravelUrlShortener\Drivers;

use CodeOfDigital\LaravelUrlShortener\Contracts\AsyncShortener;

abstract class DriverShortener implements AsyncShortener
{
    abstract protected function getErrorMessage($code, $message = null);

    public function shorten($url, array $options = [])
    {
        return $this->shortenAsync($url, $options)->wait();
    }
}

<?php

namespace CodeOfDigital\LaravelUrlShortener\Contracts;

interface Shortener
{
    /**
     * Shorten the given URL
     *
     * @param string $url
     * @param array $options
     * @return string
     */
    public function shorten($url, array $options = []);
}

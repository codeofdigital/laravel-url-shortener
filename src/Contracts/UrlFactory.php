<?php

namespace CodeOfDigital\LaravelUrlShortener\Contracts;

interface UrlFactory
{
    public function shortener($name = null);
}

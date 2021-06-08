<?php

namespace CodeOfDigital\LaravelUrlShortener;

use CodeOfDigital\LaravelUrlShortener\Contracts\UrlFactory;
use http\Exception\InvalidArgumentException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

class UrlShortenerInstance implements UrlFactory
{
    protected $app;
    protected $shorteners;

    /**
     * Create a new URL Shortener instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->shorteners = [];
    }

    /**
     * Get the default URL shortener driver
     *
     * @return string
     */
    public function getUrlDefaultDriver(): string
    {
        return $this->app['config']['url-shortener.default'];
    }

    /**
     * Set the default URL shortener driver
     *
     * @return $this
     */
    public function setUrlDefaultDriver($name): UrlShortenerInstance
    {
        $this->app['config']['url-shortener.default'] = $name;
        return $this;
    }

    public function getUrlShortenerConfig($name)
    {
        return config("url-shortener.shorteners.{$name}");
    }

    public function resolveUrlDriver($name)
    {
        $config = $this->getUrlShortenerConfig($name);

        if (is_null($config) || !array_key_exists('driver', $config))
            throw new InvalidArgumentException("URL shortener driver [{$name}] is not defined in the configuration.");

        $driverMethodName = 'create'.Str::studly($config['driver']).'Driver';

        if (method_exists($this, $driverMethodName))
            return $this->{$driverMethodName}($config);

        throw new InvalidArgumentException("URL shortener driver [{$config['driver']}] is not supported in this package.");
    }

    public function shortener($name = null)
    {
        $name = $name ?: $this->getUrlDefaultDriver();

        if (array_key_exists($name, $this->shorteners))
            return $this->shorteners[$name];

        return $this->shorteners[$name] = $this->resolveUrlDriver($name);
    }
}

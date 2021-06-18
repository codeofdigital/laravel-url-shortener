<?php

namespace CodeOfDigital\LaravelUrlShortener;

use CodeOfDigital\LaravelUrlShortener\Contracts\UrlFactory;
use CodeOfDigital\LaravelUrlShortener\Drivers\BitLyDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Drivers\CuttLyDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Drivers\FirebaseDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Drivers\IsGdDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Drivers\ShorteStDriverShortener;
use CodeOfDigital\LaravelUrlShortener\Drivers\TinyUrlDriverShortener;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use http\Exception\InvalidArgumentException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @method string shorten($url, array $options = [])
 * @method PromiseInterface shortenAsync($url, array $options = [])
 */
class UrlShortener implements UrlFactory
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
     * Dynamically call the default driver instance
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }

    /**
     * Create an instance of Bit.ly driver
     *
     * @param array $config
     * @return BitLyDriverShortener
     */
    protected function createBitLyDriver(array $config)
    {
        return new BitLyDriverShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'token'),
            Arr::get($config, 'domain', 'bit.ly')
        );
    }

    /**
     * Create an instance of TinyURL driver
     *
     * @param array $config
     * @return TinyUrlDriverShortener
     */
    protected function createTinyUrlDriver(array $config)
    {
        return new TinyUrlDriverShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'token'),
            Arr::get($config, 'domain', 'tinyurl.com')
        );
    }

    /**
     * Create an instance of Shorte.st driver
     *
     * @param array $config
     * @return ShorteStDriverShortener
     */
    protected function createShorteStDriver(array $config)
    {
        return new ShorteStDriverShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'token')
        );
    }

    /**
     * Create an instance of Is.gd driver
     *
     * @param array $config
     * @return IsGdDriverShortener
     */
    protected function createIsGdDriver(array $config)
    {
        return new IsGdDriverShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'statistic')
        );
    }

    /**
     * Create an instance of Cutt.ly driver
     *
     * @param array $config
     * @return CuttLyDriverShortener
     */
    protected function createCuttLyDriver(array $config)
    {
        return new CuttLyDriverShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'token')
        );
    }

    /**
     * Create an instance of Firebase driver
     *
     * @param array $config
     * @return FirebaseDriverShortener
     */
    protected function createFirebaseDriver(array $config)
    {
        return new FirebaseDriverShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'token'),
            Arr::get($config, 'domain'),
            Arr::get($config, 'suffix')
        );
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
    public function setUrlDefaultDriver($name): UrlShortener
    {
        $this->app['config']['url-shortener.default'] = $name;
        return $this;
    }

    /**
     * Get the URL shortener configuration in array form
     *
     * @param $name
     * @return mixed
     */
    protected function getUrlShortenerConfig($name)
    {
        return $this->app['config']["url-shortener.shorteners.{$name}"];
    }

    /**
     * Get a URL shortener driver instance
     *
     * @param null $name
     * @return mixed
     */
    public function driver($name = null)
    {
        return $this->shortener($name);
    }

    /**
     * Resolve the URL driver by creating an instance of the given URL shortener
     *
     * @param $name
     * @return mixed
     */
    protected function resolveUrlDriver($name)
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

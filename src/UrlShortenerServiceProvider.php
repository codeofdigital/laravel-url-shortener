<?php

namespace CodeOfDigital\LaravelUrlShortener;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class UrlShortenerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/url-shortener.php', 'url-shortener');
        $this->publishAssets();
        $this->registerMacros();
    }

    protected function publishAssets()
    {
        if (!$this->app->runningInConsole() || Str::contains($this->app->version(), 'lumen'))
            return;

        $this->publishes([__DIR__.'/../config/url-shortener.php' => config_path('url-shortener.php')]);
    }

    protected function registerMacros()
    {
        if (!class_exists(UrlGenerator::class) || !method_exists(UrlGenerator::class, 'macro'))
            return;

        UrlGenerator::macro('shorten', function (...$parameters) {
            return app('url.shortener')->shorten(...$parameters);
        });

        UrlGenerator::macro('shortenAsync', function (...$parameters) {
            return app('url.shortener')->shortenAsync(...$parameters);
        });

        UrlGenerator::macro('shortener', function () {
            return app('url.shortener');
        });
    }

    public function register()
    {
        $this->app->alias('url.shortener', UrlShortener::class);
        $this->app->bindIf(ClientInterface::class, Client::class);
        $this->app->singleton('url.shortener', function ($app) {
            return new UrlShortener($app);
        });
    }
}
